<?php

namespace plathir\apps\controllers;

use Yii;
use yii\web\Controller;
use plathir\apps\models\AppsSearch;

class DashboardController extends Controller {

    public function actionIndex() {
        if (\yii::$app->user->can('AppsDashboard')) {
            $searchModel = new AppsSearch();
            $applications = $searchModel->find()->where(['active' => true])->all();
            return $this->render('index', [
                        'applications' => $applications,
            ]);
        } else {
            throw new \yii\web\NotAcceptableHttpException(Yii::t('app', 'No Permission to Apps index'));
        }
    }

}
