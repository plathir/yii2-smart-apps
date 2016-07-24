<?php
namespace plathir\apps\helpers;

use plathir\apps\models\AppsSearch;

class AppsHelper {

    public function getAppsList() {
        $searchModel = new AppsSearch();
        $appsList =  $searchModel->find()->where(['active' => true])->all();
        return $appsList;
    }
    
    
    public function getAppMenuBackend($id) {
        // get menu items for application
        
        return $appMenu;
    }
    
    
}


