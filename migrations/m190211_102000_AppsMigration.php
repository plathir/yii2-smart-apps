<?php

use yii\db\Migration;

/**
 * Class m190210_141936_BaseMigration
 */
class m190211_102000_AppsMigration extends Migration {

    public function up() {

        $this->CreateAppsTable();
        $this->CreateAppsMenuTable();
    }

    public function down() {

        $this->dropIfExist('apps');
        $this->dropIfExist('apps_menu');
    }

    public function CreateAppsTable() {
        $this->dropIfExist('apps');

        $this->createTable('apps', [
            'id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'descr' => $this->string()->notNull(),
            'type' => $this->string(255)->notNull(),
            'alias' => $this->string(255)->notNull(),
            'app_key' => $this->string(255)->notNull(),
            'vendor' => $this->string(255)->notNull(),
            'vendor_email' => $this->string(255)->notNull(),
            'version' => $this->string(255)->notNull(),
            'active' => $this->integer(1)->notNull(),
            'app_icon' => $this->string(255)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ]);

        $this->addPrimaryKey('pk_id', 'apps', ['id']);
    }

        public function CreateAppsMenuTable() {
        $this->dropIfExist('apps_menu');

        $this->createTable('apps_menu', [
            'app_name' => $this->string(50)->notNull(),
            'menu_id' => $this->integer(11)->notNull(),
        ]);

        $this->addPrimaryKey('pk_id', 'apps_menu', ['app_name']);
    }
    
    
    
    public function dropIfExist($tableName) {
        if (in_array($tableName, $this->getDb()->schema->tableNames)) {
            $this->dropTable($tableName);
        }
    }

}
