<?php

namespace plathir\smartapps\controllers;

use yii\web\Controller;

class AppsController extends Controller {

    public function actionIndex() {
        return $this->render('index');
    }
}
