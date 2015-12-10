<?php

namespace plathir\apps\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 * @property integer $created_at
 * @property integer $updated_at
 */
class Apps extends \yii\db\ActiveRecord
{
    public $file;
    public $FileName;
    public $Destination;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'apps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'descr', 'type', 'alias', 'key', 'vendor', 'vendor_email', 'version'], 'required'],
            [['name'], 'unique'],
            [['descr'], 'string'],
            [['file'], 'file'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'type', 'alias', 'key', 'vendor', 'vendor_email', 'version'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'descr' => Yii::t('app', 'Descr'),
            'type' => Yii::t('app', 'Type'),
            'alias' => Yii::t('app', 'Alias'),
            'key' => Yii::t('app', 'Key'),
            'vendor' => Yii::t('app', 'Vendor'),
            'vendor_email' => Yii::t('app', 'Vendor Email'),
            'version' => Yii::t('app', 'Version'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

        public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    }
