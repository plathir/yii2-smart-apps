<?php
namespace plathir\apps\backend;

use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'plathir\apps\backend\controllers';
    public $defaultRoute = 'dashboard';
    public $appsExtractPath = '';
    public $uploadZipPath = '';

    public function init() {
        parent::init();
        $this->appsExtractPath = Yii::getAlias('@apps');
        $this->uploadZipPath = Yii::getAlias('@apps') . '/uploads';
    }

}
