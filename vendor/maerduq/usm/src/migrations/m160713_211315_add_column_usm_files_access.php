<?php

use yii\db\Migration;

class m160713_211315_add_column_usm_files_access extends Migration {

    public function up() {
        $this->addColumn('usm_files', 'access', 'SMALLINT(1) NOT NULL DEFAULT 0 AFTER name');
    }

    public function down() {
        $this->dropColumn('usm_files', 'access');
    }

}
