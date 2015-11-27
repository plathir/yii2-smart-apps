<?php

namespace plathir\apps\controllers;

use Yii;
use yii\web\Controller;
use plathir\apps\models\AppsSearch;

class DashboardController extends Controller {

    public function actionIndex() {
        $searchModel = new AppsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
}
