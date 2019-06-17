<?php
namespace plathir\apps\backend;

use Yii;
use common\helpers\ThemesHelper;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'plathir\apps\backend\controllers';
    public $defaultRoute = 'dashboard';
    public $appsExtractPath = '';
    public $uploadZipPath = '';
    public $Theme = 'smart';

    public function init() {
        parent::init();
        $this->appsExtractPath = Yii::getAlias('@apps');
        $this->uploadZipPath = Yii::getAlias('@apps') . '/uploads';
        
        $helper = new ThemesHelper();
        $path = $helper->ModuleThemePath('apps', 'backend', dirname(__FILE__) . "/themes/$this->Theme");
        $path = $path . '/views';
        $this->setViewPath($path);

    }

}
