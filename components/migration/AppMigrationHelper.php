<?php

namespace plathir\apps\components\migration;

use yii\db\Migration;
use Yii;
use \plathir\apps\components\migration\ReadDataXML;

class AppMigrationHelper extends Migration {

    public $appname = '';

    public function getXMLData($appname) {
        $filename = Yii::getAlias('@apps/' . $appname . '/migrations/Data.xml');
        $xml = file_get_contents($filename);

        $reader = new ReadDataXML();
        $data = $reader->readxml($xml);

        return $data;
    }

    public function deleteExistValues($appname) {
        $this->delete('{{%menu}}', ['app' => $appname]);
        $this->delete('{{%apps_menu}}', ['app_name' => $appname]);
        $this->delete('{{%widgets_positions}}', ['module_name' => 'backend-' . $appname]);
        $this->delete('{{%widgets_positions}}', ['module_name' => 'frontend-' . $appname]);
        $this->delete('{{%widgets_layouts}}', ['module_name' => 'backend-' . $appname]);
        $this->delete('{{%widgets_layouts}}', ['module_name' => 'frontend-' . $appname]);
        $this->delete('{{%widgets_types}}', ['module_name' => 'backend-' . $appname]);
        $this->delete('{{%widgets_types}}', ['module_name' => 'frontend-' . $appname]);
    }

    public Function CreateAppWidgetTypes($widget_types) {
        if ($widget_types) {
            foreach ($widget_types as $widget_type) {

                $this->insert('{{%widgets_types}}', [
                    'tech_name' => $widget_type["tech_name"],
                    'module_name' => $widget_type["module_name"],
                    'widget_name' => $widget_type["widget_name"],
                    'widget_class' => $widget_type["widget_class"],
                    'description' => $widget_type["description"],
                ]);
            }
        }
    }

    public function CreateAppPositions($positions) {
        if ($positions) {
            foreach ($positions as $position) {

                $this->insert('{{%widgets_positions}}', [
                    'tech_name' => $position["tech_name"],
                    'name' => $position["name"],
                    'publish' => $position["publish"],
                    'module_name' => $position["module_name"],
                ]);
            }
        }
    }

    public Function CreateAppWidgets($widgets) {
        if ($widgets) {
            foreach ($widgets as $widget) {

                $this->insert('{{%widgets}}', [
                    'widget_type' => $widget["widget_type"],
                    'name' => $widget["name"],
                    'description' => $widget["description"],
                    'position' => $widget["position"],
                    'publish' => $widget["publish"],
                    'config' => $widget["config"],
                    'rules' => $widget["rules"],
                    'created_at' => $widget["created_at"],
                    'updated_at' => $widget["updated_at"],
                ]);

                $id = Yii::$app->db->getLastInsertID();
                $posSortOrder[$widget["position"]][] = $id;
            }

            foreach ($posSortOrder as $position => $positiondata) {
                $this->insert('{{%widgets_positions_sorder}}', [
                    'position_tech_name' => $position,
                    'widget_sort_order' => implode(',', $positiondata)
                ]);
            }
        }
    }

    public Function CreateAppLayouts($layouts) {
        if ($layouts) {
            foreach ($layouts as $layout) {

                $this->insert('{{%widgets_layouts}}', [
                    'tech_name' => $layout["tech_name"],
                    'name' => $layout["name"],
                    'path' => $layout["path"],
                    'html_layout' => $layout["html_layout"],
                    'publish' => $layout["publish"],
                    'module_name' => $layout["module_name"],
                ]);
            }
        }
    }

    public function CreateAppMenu($menu) {

        $apps_menu = '';

        foreach ($menu as $item) {
            $this->insert('{{%menu}}', [
                'name' => $item["name"],
                'route' => $item["route"],
                'order' => $item["id"],
                'data' => $item["data"],
                'app' => $item["app"],
            ]);
            $key_parent = Yii::$app->db->getLastInsertID();

            $inserted_items[] = [
                'db_id' => $key_parent,
                'id' => $item["id"],
                'name' => $item["name"],
                'parent' => $item["parent_id"],
                'route' => $item["route"],
                'order' => $item["id"],
                'data' => $item["data"],
                'app' => $item["app"],
            ];

            if (!$apps_menu) {
                $this->insert('{{%apps_menu}}', [
                    'app_name' => $item["app"],
                    'menu_id' => $key_parent,
                ]);
                $apps_menu = true;
            }
        }
        foreach ($inserted_items as $ins_item) {
            if ($ins_item["parent"] != null) {
                $key = array_search($ins_item["parent"], array_column($inserted_items, 'id'));
                $this->update('{{%menu}}', ['parent' => $inserted_items[$key]['db_id']], ['id' => $ins_item["db_id"]]);
            }
        }
    }

    public function dropIfExist($tableName) {
        if (in_array($this->db->tablePrefix . $tableName, $this->getDb()->schema->tableNames)) {
            $this->dropTable($this->db->tablePrefix . $tableName);
        }
    }

    public function createFolderIfNotExist($folder) {
        $path = Yii::getAlias($folder);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

}
