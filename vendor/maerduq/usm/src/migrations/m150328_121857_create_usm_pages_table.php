<?php

use yii\db\Schema;
use yii\db\Migration;

class m150328_121857_create_usm_pages_table extends Migration {

    public function up() {
        $this->createTable('usm_pages', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . '(100) NOT NULL',
            'content' => Schema::TYPE_TEXT . ' NOT NULL',
            'access' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'wysiwyg' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
            'style' => 'ENUM("container", "plain", "empty") NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL'
        ]);

        $this->insert('usm_pages', [
            'title' => 'Home',
            'content' => '<p>This is the home page',
            'access' => 0,
            'wysiwyg' => 1,
            'style' => 'container',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function down() {
        $this->dropTable('usm_pages');
    }

}
