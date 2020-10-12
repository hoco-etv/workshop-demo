<?php

use yii\db\Schema;
use yii\db\Migration;

class m150328_121937_create_usm_redirects_table extends Migration {

    public function up() {
        $this->createTable('usm_redirects', [
            'id' => Schema::TYPE_PK,
            'active' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'url' => Schema::TYPE_STRING . '(200) NOT NULL DEFAULT ""',
            'type' => 'ENUM("cms", "php", "link", "menu_item") NOT NULL',
            'menu_item_id' => Schema::TYPE_INTEGER,
            'destination' => Schema::TYPE_STRING . '(200)',
            'forward' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'generated' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL'
        ]);

        $this->addForeignKey('fk_usm_redirects_menu_item_id', 'usm_redirects', 'menu_item_id', 'usm_menu_items', 'id');

        $this->insert('usm_redirects', [
            'active' => 1,
            'url' => 'home',
            'type' => 'menu_item',
            'menu_item_id' => 1,
            'destination' => null,
            'forward' => 0,
            'generated' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->insert('usm_redirects', [
            'active' => 1,
            'url' => '',
            'type' => 'menu_item',
            'menu_item_id' => 1,
            'destination' => null,
            'forward' => 1,
            'generated' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function down() {
        $this->dropForeignKey('fk_usm_redirects_menu_item_id', 'usm_redirects');
        $this->dropTable('usm_redirects');
    }

}
