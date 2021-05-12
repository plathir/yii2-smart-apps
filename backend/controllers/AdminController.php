<?php

namespace plathir\apps\backend\controllers;

use Yii;
use plathir\apps\backend\models\Apps;
use plathir\apps\backend\models\AppsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\base\UserException;
use \plathir\apps\components\migration\AppMigrationHelper;

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
                    'activate' => ['post'],
                    'uninstall' => ['post'],
                    'reloadxml' => ['post'],
                    'reloadpermissions' => ['post'],
                    'reloadsettings' => ['post'],
                    'reloadtables' => ['post'],
                    'buildtheme'
                ],
            ],
        ];
    }

    /**
     * Lists all Apps models.
     * @return mixed
     */
    public function actionIndex() {
        if (\yii::$app->user->can('AppsIndex')) {
            $searchModel = new AppsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Apps Admin'));
        }
    }

    /**
     * Displays a single Apps model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        if (\yii::$app->user->can('AppsView')) {
            return $this->render('view', [
                        'model' => $this->findModel($id),
            ]);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Apps View'));
        }
    }

    /**
     * 
     * @return type
     */
    public function actionInstall() {
        if (\yii::$app->user->can('AppsInstall')) {
            $model = new Apps();
            $model->Destination = $this->module->appsExtractPath;

            $model->active = 0;

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
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Install Apps '));
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
            $this->BuildViews($model->name, 'smart');
            $this->BuildAssets($model->name, 'smart');
            $this->installApp($model);
        } else {
            Yii::$app->getSession()->setFlash('danger', 'Application cannot extract !');
            $this->deleteZip($model->FileName);
            return $this->redirect(['index']);
        }
    }

    public function actionBuildtheme($appname, $theme) {
        $this->BuildViews($appname, $theme);
        $this->BuildAssets($appname, $theme);

        return $this->redirect(['index']);
    }

    /**
     * 
     * @param type $model
     * @return type
     */
    public function installApp($model) {
        if ($model != null && ($model->save())) {
            $classname = 'apps\\' . $model->name . '\backend\helper\Permissions';
            if (class_exists($classname)) {
                $permissions = new $classname;
                $permissions->create();
            }

            $classname = 'apps\\' . $model->name . '\backend\helper\Settings';
            if (class_exists($classname)) {
                $settings = new $classname;
                $settings->create();
            }

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
            $h_model = Apps::find()->where(['name' => $model->name])->One();
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
        if (\yii::$app->user->can('AppsUninstall')) {
            $this->UninstallApp($id);
            return $this->redirect(['index']);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Uninstall Apps '));
        }
    }

    /**
     * 
     * @param type $id
     */
    public function UninstallApp($id) {

        $appName = $this->findModel($id)->name;
        $destination = $this->module->appsExtractPath;
        if ($this->findModel($id)->delete()) {
            $classname = 'apps\\' . $appName . '\backend\helper\Permissions';
            if (class_exists($classname)) {
                $permissions = new $classname;
                $permissions->remove();
            }
            $classname = 'apps\\' . $appName . '\backend\helper\Settings';
            if (class_exists($classname)) {
                $settings = new $classname;
                $settings->remove();
            }
            $this->MigrateDown($appName);
            $this->DeleteAppFiles($destination . '/' . $appName);
            $this->RemoveBuildedTheme($appName, 'smart');
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
        $model = Apps::findOne($id);
        if ($model != null) {
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
            $model->app_key = (string) $xml->key;
            $model->vendor = (string) $xml->vendor;
            $model->vendor_email = (string) $xml->vendor_email;
            $model->version = (string) $xml->version;
            $model->app_icon = (string) $xml->app_icon;
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
     * @param type $appName
     */
    public function MigrateUp($appName) {
        $classname = 'apps\\' . $appName . '\migrations\AppMigration';
        if (class_exists($classname)) {
            $migration = new $classname;
            $migration->up();
        }
    }

    /**
     * 
     * @param type $appName
     */
    public function MigrateDown($appName) {
        $classname = 'apps\\' . $appName . '\migrations\AppMigration';
        if (class_exists($classname)) {
            $migration = new $classname;
            $migration->down();
        }
    }

    /**
     * Activate Deactivate toggle Apps
     * @param type $id
     * @return type
     * @throws yii\web\NotFoundHttpException
     */
    public function actionActivate($id) {

        if (\yii::$app->user->can('AppsActivate')) {
            if ($module = $this->findModel($id)) {
                echo $module->active . '<br>';
                //   die();
                if ($module->active == true) {
                    $module->active = 0;
                    $module->update();
                    return $this->redirect(Yii::$app->request->referrer ?: $this->redirect(['index']));
                } else {
                    $module->active = true;
                    $module->update();
                    //return $this->redirect(['index']);
                    return $this->redirect(Yii::$app->request->referrer ?: $this->redirect(['index']));
                }
            } else {
                throw new yii\web\NotFoundHttpException('');
            }
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Activate/Deactivate Apps '));
        }
    }

    public function BuildViews($appname, $theme) {

        $apps = [
            "$appname" => ['path' => Yii::getalias("@apps") . DIRECTORY_SEPARATOR . $appname],
        ];

        $results = [];
        foreach ($apps as $appkey => $app) {
            if ($this->BuildModuleViews($appkey, $app['path'])) {
                $results[$appkey] = 'Views Builded !';
            } else {
                $results[$appkey] = 'Views Cannot Build !';
            }
        }
        return $results;
    }

    public function BuildAssets($appname, $theme) {

        $apps = [
            "$appname" => ['path' => Yii::getalias("@apps") . DIRECTORY_SEPARATOR . $appname],
        ];

        $results = [];
        foreach ($apps as $appkey => $app) {
            if ($this->BuildModuleAssets($appkey, $app['path'])) {
                $results[$appkey] = 'Assets Builded !';
            } else {
                $results[$appkey] = 'Assets Cannot Build !';
            }
        }
        return $results;
    }

    public function BuildModuleViews($appName, $appPath) {
        try {
            $this->BuildViewsFiles('backend', 'admin', $appName, $appPath);
            $this->BuildWidgetsViewsFiles('backend', 'admin', $appName, $appPath);


            $this->BuildViewsFiles('frontend', 'site', $appName, $appPath);
            $this->BuildWidgetsViewsFiles('frontend', 'site', $appName, $appPath);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function BuildModuleAssets($appName, $appPath) {
        try {
            $this->BuildAssetsFiles('backend', 'admin', $appName, $appPath);
            $this->BuildAssetsFiles('frontend', 'site', $appName, $appPath);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function BuildViewsFiles($app, $env, $appName, $appPath) {

        $sourceViewsPath = $appPath . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'smart';
        $targetViewsPath = Yii::getalias("@themes") . DIRECTORY_SEPARATOR . $env . DIRECTORY_SEPARATOR . 'smart' . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . $appName;

        if (file_exists($sourceViewsPath)) {
            if (!file_exists($targetViewsPath)) {
                @mkdir($targetViewsPath);
            }
            $this->recursive_copy($sourceViewsPath, $targetViewsPath);
        }
    }

    public function BuildAssetsFiles($app, $env, $appName, $appPath) {

        $sourceAssetsPath = $appPath . DIRECTORY_SEPARATOR .
                $app . DIRECTORY_SEPARATOR .
                'assets' . DIRECTORY_SEPARATOR .
                'themes' . DIRECTORY_SEPARATOR .
                'smart';
        $targetAssetsPath = Yii::getalias("@realAppPath") . DIRECTORY_SEPARATOR .
                'www' . DIRECTORY_SEPARATOR .
                $env . DIRECTORY_SEPARATOR .
                'themes' . DIRECTORY_SEPARATOR .
                'smart' . DIRECTORY_SEPARATOR .
                'apps' . DIRECTORY_SEPARATOR .
                $appName;

        if (file_exists($sourceAssetsPath)) {
            if (!file_exists($targetAssetsPath)) {
                @mkdir($targetViewsPath);
            }
            $this->recursive_copy($sourceAssetsPath, $targetAssetsPath);
        }
    }

    public function BuildWidgetsViewsFiles($app, $env, $appName, $appPath) {

        $sourceViewsPath = $appPath . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'smart';
        $targetViewsPath = Yii::getalias("@themes") . DIRECTORY_SEPARATOR . $env . DIRECTORY_SEPARATOR . 'smart' . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR . 'widgets';
        if (file_exists($sourceViewsPath)) {
            if (!file_exists($targetViewsPath)) {
                @mkdir($targetViewsPath);
            }
            $this->recursive_copy($sourceViewsPath, $targetViewsPath);
        }
    }

    public function recursive_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    $this->recursive_copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                } else {
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }

    public function RemoveBuildedTheme($appName, $theme) {

        $backendTheme = Yii::getalias("@themes") . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . $appName;
        $frontendTheme = Yii::getalias("@themes") . DIRECTORY_SEPARATOR . 'site' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . $appName;

        $backendThemeAssets = Yii::getalias("@RootPath") . DIRECTORY_SEPARATOR .
                'admin' . DIRECTORY_SEPARATOR .
                'themes' . DIRECTORY_SEPARATOR .
                $theme . DIRECTORY_SEPARATOR .
                'apps' . DIRECTORY_SEPARATOR .
                $appName;
        $frontendThemeAssets = Yii::getalias("@RootPath") . DIRECTORY_SEPARATOR . 
                'site' . DIRECTORY_SEPARATOR . 
                'themes' . DIRECTORY_SEPARATOR .
                $theme . DIRECTORY_SEPARATOR . 
                'apps' . DIRECTORY_SEPARATOR . 
                $appName;


        $this->DeleteAppFiles($backendTheme);
        $this->DeleteAppFiles($frontendTheme);
        $this->DeleteAppFiles($backendThemeAssets);
        $this->DeleteAppFiles($frontendThemeAssets);
    }

    public function actionReloadxml($appName) {

        if (\yii::$app->user->can('AppsReloadXML')) {
            if ($this->ReloadAppXML($appName)) {
                Yii::$app->getSession()->setFlash('success', ' Reload App Defaults Successfully !');
            } else {
                Yii::$app->getSession()->setFlash('danger', 'Cannot Reload App Defaults !');
            }
            return $this->redirect(['index']);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Reload App Defaults '));
        }
    }

    public function ReloadAppXML($appName) {


        $appsMigr = new AppMigrationHelper();

        $data = $appsMigr->getXMLData($appName);

        if ($data) {
            $layouts = array_key_exists("Layouts", $data) ? $data["Layouts"] : '';
            $widget_types = array_key_exists("WidgetTypes", $data) ? $data["WidgetTypes"] : '';
            $widgets = array_key_exists("Widgets", $data) ? $data["Widgets"] : '';
            $positions = array_key_exists("Positions", $data) ? $data["Positions"] : '';
            $menu = array_key_exists("Menu", $data) ? $data["Menu"] : '';

            $appsMigr->deleteExistValues($appName);

            $appsMigr->CreateAppWidgetTypes($widget_types);

            $appsMigr->CreateAppPositions($positions);

            $appsMigr->CreateAppWidgets($widgets);

            $appsMigr->CreateAppMenu($menu);

            $appsMigr->CreateAppLayouts($layouts);

            return true;
        } else
            return false;
    }

    public function actionReloadpermissions($appName) {

        if (\yii::$app->user->can('AppsReloadPermissions')) {
            if ($this->ReloadAppPermissions($appName)) {
                Yii::$app->getSession()->setFlash('success', ' Reload Permissions Successfully !');
            } else {
                Yii::$app->getSession()->setFlash('danger', 'Cannot Reload Permissions !');
            }

            return $this->redirect(['index']);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Reload App Default Permissions '));
        }
    }

    public function ReloadAppPermissions($appName) {

        $classname = 'apps\\' . $appName . '\backend\helper\Permissions';
        if (class_exists($classname)) {
            $permissions = new $classname;
            $permissions->create();
            return true;
        } else
            return false;
    }

    public function actionReloadsettings($appName) {

        if (\yii::$app->user->can('AppsReloadSettings')) {
            if ($this->ReloadAppSettings($appName)) {
                Yii::$app->getSession()->setFlash('success', ' Reload Settings Successfully !');
            } else {
                Yii::$app->getSession()->setFlash('danger', 'Cannot Reload Settings !');
            }
            return $this->redirect(['index']);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Reload App Defaults '));
        }
    }

    public function ReloadAppSettings($appName) {

        $classname = 'apps\\' . $appName . '\backend\helper\Settings';
        if (class_exists($classname)) {
            $settings = new $classname;
            $settings->create();
            return true;
        } else
            return false;
    }

    public function actionReloadtables($appName) {

        if (\yii::$app->user->can('AppsReloadTables')) {
            if ($this->ReloadAppTables($appName)) {
                Yii::$app->getSession()->setFlash('success', ' Reload Tables Successfully !');
            } else {
                Yii::$app->getSession()->setFlash('danger', 'Cannot Reload Tables !');
            }
            return $this->redirect(['index']);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Reload App Tables '));
        }
    }

    public function ReloadAppTables($appName) {

        $classname = 'apps\\' . $appName . '\migrations\AppMigration';
        if (class_exists($classname)) {
            $migr = new $classname;
            $migr->CreateAppTables();
            $this->ReloadAppXML($appName);
            return true;
        } else
            return false;
    }

}
