<?php

use yii\db\Schema;
use yii\db\Migration;

class m150328_121954_create_usm_textblocks_table extends Migration {

    public function up() {
        $this->createTable('usm_textblocks', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(100) NOT NULL',
            'text' => Schema::TYPE_TEXT,
            'description' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL'
        ]);

        $this->createIndex('usm_textblocks_name', 'usm_textblocks', 'name', true);
    }

    public function down() {
        $this->dropTable('usm_textblocks');
    }

}
