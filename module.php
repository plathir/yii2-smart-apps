<?php

namespace plathir\apps;

use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'plathir\apps\controllers';
    public $defaultRoute = 'dashboard';
    public $appsPath = '';
    
    public function init() {
        parent::init();
    }
}
