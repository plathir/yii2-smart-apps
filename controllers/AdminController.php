<?php

namespace plathir\apps\controllers;

use Yii;
use plathir\apps\models\Apps;
use plathir\apps\models\AppsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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
                //    'uninstall' => ['post'],
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

    public function actionInstall() {

        $model = new Apps();
        $model->Destination = $this->module->appsExtractPath;

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->file->saveAs($this->module->uploadZipPath . '/' . $model->file->name);
            $model->FileName = $this->module->uploadZipPath . '/' . $model->file->name;
            $this->ExtractFile($model->FileName, $model->Destination);

            $model = $this->FillModelValuesFromAppFiles($model);
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Application installed ' . $model->FileName);
                $this->deleteZip($model->FileName);
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('danger', 'Application cannot install!');
                $this->DeleteAppFiles($model->Destination);
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('install', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUninstall($id) {
        $model = $this->findModel($id);

        return $this->render('uninstall');
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
        // parse xml config files and fill $model data to store in Apps table 
        //....
        $model->name = 'test name';
        $model->descr = 'test descr';
        $model->type = 'test type';
        $model->alias = 'test alias';
        $model->key = 'test key';
        $model->vendor = 'test vendor';
        $model->vendor_email = 'test vendor email';
        $model->version = 'test version';
        return $model;
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

}
