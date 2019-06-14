<?php
namespace plathir\apps\helpers;

use plathir\apps\backend\models\AppsSearch;

class AppsHelper {

    public function getAppsList($active = true) {
        $searchModel = new AppsSearch();
        if ($active == true) {
            $appsList = $searchModel->find()->where(['active' => $active])->all();
        } else {
            $appsList = $searchModel->find()->all();
        }
        return $appsList;
    }

    public function getAppMenuBackend($id) {
        // get menu items for application

        return $appMenu;
    }

}
