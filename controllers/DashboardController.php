<?php

namespace plathir\apps\controllers;

use Yii;
use yii\web\Controller;
use plathir\apps\models\AppsSearch;

class DashboardController extends Controller {

    public function actionIndex() {
        $searchModel = new AppsSearch();
        $applications = $searchModel->find()->where(['active' => true])->all();
        return $this->render('index', [
                    'applications' => $applications,
        ]);
    }

}
