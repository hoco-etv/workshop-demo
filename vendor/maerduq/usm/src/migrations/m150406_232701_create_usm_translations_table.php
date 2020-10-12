<?php

use yii\db\Schema;
use yii\db\Migration;

class m150406_232701_create_usm_translations_table extends Migration {

    public function up() {
        $this->createTable('usm_translations', [
            'id' => Schema::TYPE_PK,
            'item_type' => Schema::TYPE_STRING . '(50) NOT NULL',
            'item_id' => Schema::TYPE_INTEGER,
            'lang' => Schema::TYPE_STRING . '(10) NOT NULL',
            'key' => Schema::TYPE_STRING . '(20) NOT NULL',
            'value' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL'
        ]);

        $this->createIndex('usm_translations_item_id', 'usm_translations', 'item_id');
        $this->createIndex('usm_translations_foreign_key', 'usm_translations', ['item_type', 'item_id', 'lang', 'key']);
    }

    public function down() {
        $this->dropTable('usm_translations');
    }

}
