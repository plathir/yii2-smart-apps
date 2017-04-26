<?php

namespace plathir\apps\models;

use Yii;

/**
 * This is the model class for table "apps_menu".
 *
 * @property string $app_name
 * @property integer $menu_id
 */
class AppsMenu extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'apps_menu';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['app_name'], 'unique'],
            [['menu_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'app_name' => Yii::t('app', 'App Name'),
            'menu_id' => Yii::t('app', 'Menu ID'),
        ];
    }

}
