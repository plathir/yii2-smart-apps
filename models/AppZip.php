?<?php

namespace plathir\apps\models;

use yii\web\Model;

class AppZip extends \yii\web\model {



    public function ExtractFile() {
        $zip = new ZipArchive;
        if ($zip->open($this->FileName) === TRUE) {
            $zip->extractTo($this->Destination);
            $zip->close();
            return true;
        } else {
            echo false;
        }
    }
   
    public function UploadZip() {
        
    }
    
}
