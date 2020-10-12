<?php

use yii\db\Schema;
use yii\db\Migration;

class m150406_233701_create_usm_files_table extends Migration {

    public function up() {
        $this->createTable('usm_files', [
            'id' => Schema::TYPE_PK,
            'category' => Schema::TYPE_STRING . '(60) NOT NULL',
            'name' => Schema::TYPE_STRING . '(60) NOT NULL',
            'file_name' => Schema::TYPE_STRING . '(120) NOT NULL',
            'file_type' => Schema::TYPE_STRING . '(60) NOT NULL',
            'file_size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'file_ext' => Schema::TYPE_STRING . '(10) NOT NULL',
            'last_accessed_at' => Schema::TYPE_DATETIME,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL'
        ]);

        $this->createIndex('usm_files_file_name_unique', 'usm_files', 'file_name', true);
        $this->createIndex('usm_files_file_unique', 'usm_files', ['category', 'name', 'file_ext']);
    }

    public function down() {
        $this->dropTable('usm_files');
    }

}
