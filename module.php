<?php

namespace plathir\apps;

use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'plathir\user\controllers';
    
    public function init() {

        parent::init();
    }
}
