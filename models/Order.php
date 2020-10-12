<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Order extends \yii\db\ActiveRecord
{
    // public $name;
    // public $email;

    // public function rules()
    // {
    //     return [
    //         [['name', 'email'], 'required'],
    //         ['email', 'email'],
    //     ];
    // }

    public static function tableName()
    {
        /*
        [id]        [int]   contains order id, 1 order containing multiple stores will get 2 id's
        [user_id]   [int]        user who ordered the components
        [date]      [datetime]      time and date of order placement on klushok site
        [price]     [float]         field where the order price can be added by the committe member or board member responsible
        [confirmed] [tinyint 1]     bool email confirmation
        [ordered]   [tinyint 1]     bool ordered by committee/board
        [paid]      [tinyint 1]     bool paid for the order at etv desk
        [retrieved] [tinyint 1]     bool order retrieved from ETV desk - order is finalized
        */
        return 'orders';
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'user_id']);
    }
    public function getOrder_details()
    {
        return $this->hasMany(Order_details::className(), ['order_id' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'price' => 'Price',
            'confirmed' => 'Confirmed',
            'ordered' => 'Ordered',
            'paid' => 'Paid',
            'retrieved' => 'Retrieved',
        ];
    }
}
