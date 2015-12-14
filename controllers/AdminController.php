<?php

namespace plathir\apps\controllers;

use Yii;
use plathir\apps\models\Apps;
use plathir\apps\models\AppsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\base\UserException;

/**
 * AppsController implements the CRUD actions for Apps model.
 * @property \plathir\apps\Module $module
 */
class AdminController extends Controller {

    public function __construct($id, $module) {
        parent::__construct($id, $module);
    }

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'uninstall' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Apps models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AppsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Apps model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Apps model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Apps();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Apps model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Apps model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 
     * @return type
     */
    public function actionInstall() {

        $model = new Apps();
        $model->Destination = $this->module->appsExtractPath;

        if ($model->load(Yii::$app->request->post())) {
            if (($model->file = UploadedFile::getInstance($model, 'file')) &&
                    ( $model->file->saveAs($this->module->uploadZipPath . '/' . $model->file->name) ) && ($model->FileName = $this->module->uploadZipPath . '/' . $model->file->name )) {
                $this->ExtractAndInstallApp($model);
            } else {
                Yii::$app->getSession()->setFlash('danger', 'Application cannot upload !');
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('install', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * 
     * @param type $model
     * @return type
     */
    public function ExtractAndInstallApp($model) {

        if ($this->ExtractFile($model->FileName, $model->Destination)) {
            $model = $this->FillModelValuesFromAppFiles($model);
            $this->MigrateUp($model->name);
            $this->installApp($model);
        } else {
            Yii::$app->getSession()->setFlash('danger', 'Application cannot extract !');
            $this->deleteZip($model->FileName);
            return $this->redirect(['index']);
        }
    }

    /**
     * 
     * @param type $model
     * @return type
     */
    public function installApp($model) {
        if ($model != null && ($model->save())) {
            Yii::$app->getSession()->setFlash('success', 'Application installed ' . $model->FileName);
            $this->deleteZip($model->FileName);
            return $this->redirect(['index']);
        } else {
            $errors = $model->errors;
            foreach ($errors as $error) {
                Yii::$app->getSession()->setFlash('danger', $error[0] . '<br> Installation aborted !');
            }
            $this->deleteZip($model->FileName);
            // search if application with same name exists
            $h_model = \plathir\apps\models\Apps::find()->where(['name' => $model->name]);
            if ($h_model == null) {
                // Delete application files only if another application exist         
                $this->DeleteAppFiles($model->Destination . '/' . \basename($model->file->name, ".zip"));
            }
            return $this->redirect(['index']);
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionUninstall($id) {
        $this->UninstallApp($id);
        return $this->redirect(['index']);
    }

    /**
     * 
     * @param type $id
     */
    public function UninstallApp($id) {

        $appName = $this->findModel($id)->name;
        $destination = $this->module->appsExtractPath;
        if ($this->findModel($id)->delete()) {
            $this->MigrateDown($appName);
            $this->DeleteAppFiles($destination . '/' . $appName);
            Yii::$app->getSession()->setFlash('success', 'Uninstall Application : ' . $appName . ' succesfull !');
        } else {
            Yii::$app->getSession()->setFlash('danger' . 'Application : ' . $appName . ' cannot uninstall !');
        }
    }

    /**
     * Finds the Apps model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Apps the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Apps::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @param type $filename
     * @param type $destination
     * @return boolean
     */
    public function ExtractFile($filename, $destination) {
        $zip = new \ZipArchive();
        $basename = \basename($filename, ".zip");

        if ($zip->open($filename) === TRUE) {
            $zip->extractTo($destination . '/' . $basename);
            $zip->close();
            return true;
        } else {
            echo false;
        }
    }

    /**
     * 
     * @param type $model
     * @return boolean
     */
    public function FillModelValuesFromAppFiles($model) {

        $model->name = \basename($model->FileName, ".zip");
        $appDir = $model->Destination . '/' . $model->name;
        $xmlFile = $appDir . '/' . $model->name . '.xml';
        $xml = $this->LoadInitXML($xmlFile);

        if ($xml === false) {
            $message = "Failed loading XML: ";
            foreach (libxml_get_errors() as $error) {
                echo $message .= "<br>", $error->message;
            }
            throw new UserException($message);
        } else {
            $model->descr = (string) $xml->description;
            $model->type = (string) $xml->type;
            $model->alias = (string) $xml->alias;
            $model->key = (string) $xml->key;
            $model->vendor = (string) $xml->vendor;
            $model->vendor_email = (string) $xml->vendor_email;
            $model->version = (string) $xml->version;
            return $model;
        }
    }

    /**
     * 
     * @param string $dirPath
     * @throws InvalidArgumentException
     */
    public function DeleteAppFiles($dirPath) {

        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::DeleteAppFiles($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    /**
     * 
     * @param type $filename
     * @return type
     */
    public function deleteZip($filename) {
        if (file_exists($filename)) {
            return unlink($filename);
        }
    }

    /**
     * 
     * @param type $xmlFile
     * @return type
     * @throws UserException
     */
    public function LoadInitXML($xmlFile) {
        if (!\file_exists($xmlFile)) {
            throw new UserException("xml config file not found !");
        }
        return \simplexml_load_file($xmlFile);
    }

    /**
     * 
     * @param type $appname
     * @return boolean
     */
    public function validateAppFiles($appname) {


        return true;
    }

    /**
     * 
     * @param type $appName
     */
    public function MigrateUp($appName) {
        if (\file_exists(Yii::getAlias('@apps') . '/migrations')) {
            $oldApp = \Yii::$app;
            new \yii\console\Application([
                'id' => 'Command runner',
                'basePath' => '@app',
                'components' => [
                    'db' => $oldApp->db,
                ],
            ]);
            \Yii::$app->runAction('migrate/up', ['migrationPath' => '@apps/' . $appName . '/migrations/', 'interactive' => false]);
            \Yii::$app = $oldApp;
        }
    }

    /**
     * 
     * @param type $appName
     */
    public function MigrateDown($appName) {
        if (\file_exists(Yii::getAlias('@apps') . '/migrations')) {
            $oldApp = \Yii::$app;
            new \yii\console\Application([
                'id' => 'Command runner',
                'basePath' => '@app',
                'components' => [
                    'db' => $oldApp->db,
                ],
            ]);
            \Yii::$app->runAction('migrate/down', ['migrationPath' => '@apps/' . $appName . '/migrations/', 'interactive' => false]);
            \Yii::$app = $oldApp;
        }
    }

}
