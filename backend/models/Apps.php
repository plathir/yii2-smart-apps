<?php

namespace plathir\apps\backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use plathir\apps\backend\models\AppsMenu;

/**
 * This is the model class for table "apps".
 *
 * @property integer $id
 * @property string $name
 * @property string $descr
 * @property string $type
 * @property string $alias
 * @property string $key
 * @property string $vendor
 * @property string $vendor_email
 * @property string $version
 * @property string $app_icon
 * @property integer $created_at
 * @property integer $updated_at
 */
class Apps extends \yii\db\ActiveRecord {

    public $file;
    public $FileName;
    public $Destination;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%apps}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'descr', 'type', 'alias', 'app_key', 'vendor', 'vendor_email', 'version'], 'required'],
            [['name'], 'unique'],
            [['descr'], 'string'],
            [['file'], 'file'],
            [['active'], 'integer'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'type', 'alias', 'app_key', 'vendor', 'vendor_email', 'version', 'app_icon'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('apps', 'ID'),
            'name' => Yii::t('apps', 'Name'),
            'descr' => Yii::t('apps', 'Descr'),
            'file' => Yii::t('apps', 'File'),
            'type' => Yii::t('apps', 'Type'),
            'alias' => Yii::t('apps', 'Alias'),
            'app_key' => Yii::t('apps', 'App Key'),
            'vendor' => Yii::t('apps', 'Vendor'),
            'vendor_email' => Yii::t('apps', 'Vendor Email'),
            'version' => Yii::t('apps', 'Version'),
            'active' => Yii::t('apps', 'Active'),
            'app_icon' => Yii::t('apps', 'App Icon'),
            'created_at' => Yii::t('apps', 'Created At'),
            'updated_at' => Yii::t('apps', 'Updated At'),
            
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getMenu() {
       // return 100;
        return $this->hasOne(AppsMenu::className(), ['app_name' => 'name']);
    }

}
