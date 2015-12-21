<?php

namespace plathir\apps\helpers;

class AppsHelper {

    public function getAppsList() {
        $searchModel = new AppsSearch();
        $appsList =  $searchModel->find()->where(['name' => 'apptest1', 'active' => true])->all();
        return $appsList;
    }
    
    
    public function getAppMenu($id) {
        // get menu items for application
        
        return $appMenu;
    }
    
    
}


