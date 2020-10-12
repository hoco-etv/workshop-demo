<?php

use yii\db\Schema;
use yii\db\Migration;

class m150328_121926_create_usm_menu_items_table extends Migration {

    public function up() {
        $this->createTable('usm_menu_items', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER,
            'position' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'title' => Schema::TYPE_STRING . '(100) NOT NULL',
            'alias' => Schema::TYPE_STRING . '(100) NOT NULL',
            'type' => 'ENUM("empty", "cms", "php", "link") NOT NULL',
            'page_id' => Schema::TYPE_INTEGER,
            'url' => Schema::TYPE_STRING . '(200)',
            'visible' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'access' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL'
        ]);

        $this->addForeignKey('fk_usm_menu_items_parent_id', 'usm_menu_items', 'parent_id', 'usm_menu_items', 'id');

        $this->addForeignKey('fk_usm_menu_items_page_id', 'usm_menu_items', 'page_id', 'usm_pages', 'id');

        $this->insert('usm_menu_items', [
            'parent_id' => NULL,
            'position' => 1,
            'title' => 'Home',
            'alias' => 'home',
            'type' => 'cms',
            'page_id' => 1,
            'url' => '-',
            'visible' => 1,
            'access' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function down() {
        $this->dropForeignKey('fk_usm_menu_items_parent_id', 'usm_menu_items');
        $this->dropForeignKey('fk_usm_menu_items_page_id', 'usm_menu_items');
        $this->dropTable('usm_menu_items');
    }

}
