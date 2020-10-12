<?php

use yii\db\Migration;

/**
 * Handles the creation of table `maillist_member`.
 */
class m200314_210954_create_maillist_members_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('maillist_members', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'list_id' => $this->integer()->unsigned()->notNull(),
            'added_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('maillist_members');
    }
}
