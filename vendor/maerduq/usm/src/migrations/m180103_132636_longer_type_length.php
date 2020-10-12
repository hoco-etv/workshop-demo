<?php

use yii\db\Migration;

class m180103_132636_longer_type_length extends Migration {

    public function up() {
        $this->alterColumn('usm_files', 'file_type', $this->string());
    }

    public function down() {
        $this->alterColumn('usm_files', 'file_type', $this->string(60));
    }

}
