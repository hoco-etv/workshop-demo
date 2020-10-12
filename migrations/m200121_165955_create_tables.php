<?php

use yii\db\Migration;

/**
 * Class m200121_165955_create_tables
 */
class m200121_165955_create_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customers', [
            'id' =>         $this->primaryKey(),
            'email' =>      $this->text()->notNull(),
            'name' =>       $this->text()->notNull(),
            'student_no' => $this->integer()->unsigned()->defaultValue(null),
        ]);

        $this->createTable('inventory', [
            'id' =>         $this->primaryKey(),
            'category' =>   $this->text()->notNull(),
            'name' =>       $this->text()->notNull(),
            'info' =>       $this->text()->defaultValue(null),
            'price' =>      $this->float()->notNull(),
            'stock' =>      $this->integer()->defaultValue(-1),
        ]);

        $this->createTable('orders', [
            'id' =>         $this->primaryKey(),
            'user_id' =>    $this->integer()->unsigned()->notNull(),
            'date' =>       $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'price' =>      $this->float()->defaultValue(null),
            'confirmed' =>  $this->tinyInteger(1)->defaultValue(0),
            'ordered' =>    $this->tinyInteger(1)->defaultValue(0),
            'paid' =>       $this->tinyInteger(1)->defaultValue(0),
            'retrieved' =>  $this->tinyInteger(1)->defaultValue(0),
            'hash' =>        $this->text()->defaultValue(null),
        ]);

        $this->createTable('order_details', [
            'id' =>         $this->primaryKey(),
            'order_id' =>       $this->integer()->unsigned()->notNull(),
            'store' =>          "ENUM('RS','Farnell') NOT NULL",
            'part_no' =>        $this->integer()->unsigned()->notNull(),
            'description' =>    $this->text(),
            'quantity' =>       $this->integer()->unsigned()->notNull(),

        ]);


        $this->createTable('devices', [
            'id'                =>  $this->primaryKey(),
            'brand'             =>  $this->string(100)->defaultValue(null),
            'name'              =>  $this->string(100)->notNull(),
            'type'              =>  $this->string(100)->defaultValue(null),
            'status'            =>  $this->integer()->defaultValue(0), // Model contains method getStatus returning an associative array with string interpretations
            'last_updated_at'   =>  $this->dateTime()->defaultValue(null),
            'description'       =>  $this->text()->defaultValue(null),
            'repair_notes'      =>  $this->text()->defaultValue(null),
            'image'                =>    $this->string(350)->defaultValue(null),
            'manual'            =>    $this->string(350)->defaultValue(null)
        ]);

        $this->createTable('projects', [
            'id'            =>  $this->primaryKey(),
            'title'         =>  $this->string(40)->notNull(),
            'subtitle'      =>  $this->string(140)->defaultValue(null),
            'author'        =>  $this->string(40)->notNull(),
            'email'         =>  $this->text()->notNull(),
            'created_at'    =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'confirmed'     =>  $this->boolean()->defaultValue(false),
            'approved'      =>  $this->boolean()->defaultValue(false),
            'hash'          =>  $this->string(60)->notNull(),
            'cover_type'    =>  $this->string(40)->notNull(),
            'cover_ext'     =>  $this->string(20)->notNull(),
            'file1_type'    =>  $this->string(40)->defaultValue(null),
            'file1_ext'     =>  $this->string(20)->defaultValue(null),
            'file1_name'    =>  $this->string(40)->defaultValue(null),
            'file2_type'    =>  $this->string(40)->defaultValue(null),
            'file2_ext'     =>  $this->string(20)->defaultValue(null),
            'file2_name'    =>  $this->string(40)->defaultValue(null),
            'file3_type'    =>  $this->string(40)->defaultValue(null),
            'file3_ext'     =>  $this->string(20)->defaultValue(null),
            'file3_name'    =>  $this->string(40)->defaultValue(null),
            'file4_type'    =>  $this->string(40)->defaultValue(null),
            'file4_ext'     =>  $this->string(20)->defaultValue(null),
            'file4_name'    =>  $this->string(40)->defaultValue(null),
            'file5_type'    =>  $this->string(40)->defaultValue(null),
            'file5_ext'     =>  $this->string(20)->defaultValue(null),
            'file5_name'    =>  $this->string(40)->defaultValue(null),
            'content'       =>  $this->text()->notNull(),
            'reviewer'      =>  $this->string(40)->defaultValue(null),
            'approved_at'   =>  $this->dateTime()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customers');
        $this->dropTable('inventory');
        $this->dropTable('orders');
        $this->dropTable('order_details');
        $this->dropTable('devices');
        $this->dropTable('projects');
    }
}
