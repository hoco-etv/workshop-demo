<?php

use app\models\Order;
use yii\db\Migration;

/**
 * Class m200312_192255_add_arrived_bool
 */
class m200312_192255_add_arrived_bool extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders','arrived',  $this->tinyInteger(1)->defaultValue(0)->after('paid'));
        
        // set arrived to true for retrieved values
        $orders = Order::find()->where(['retrieved'=>1])->all();
        foreach($orders as $order){
            $order->arrived = true;
            $order->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('orders','arrived');
    }
}
