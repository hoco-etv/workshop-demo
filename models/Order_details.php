<?php

namespace app\models;

use Yii;
use yii\base\Model;
use kartik\builder\TabularForm;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

class Order_details extends \yii\db\ActiveRecord
{   
    public static $stores = [ // Be aware!! this system is a dubplicate of the enum on the database. It is added to allow orderController to return store names and future use
        0 => 'RS',
        1 => 'Farnell'
    ];

    public function rules()
    {
        return [
            [['store', 'part_no', 'quantity'], 'required'],
            ['description', 'string', 'max' => 100],
            [['part_no', 'quantity'], 'integer', 'min' => 1],
            [['part_no', 'quantity'], 'integer', 'max' => 4294967295]
        ];
    }

    public static function tableName()
    {
        /*
        [id]            [int 11]            contains detail id
        [order_id]      [int 11]            contains order id
        [store]         [enum RS, Farnell]  contains the available stores
        [part_no]       [int 11]            part number at selected store
        [description]   [text]              user entered description of the product
        [quantity]      [smallint 5]        desired quantity
        
        */
        return 'order_details';
    }

    public function getOrders()
    {
        return $this->hasOne(Order::className(), ['order_id' => 'id']);
    }

    public function getOrderDetailsByStore($store)
    {
        $details = Order_details::find()
            ->where(['store' => $store])
            //->orderby(['order_id'=>SORT_ASC])
            ->all();

        $arrayOut = array();
        foreach ($details as $object) {
            $arrayTemp = array();
            $arrayTemp['id'] = $object->id;
            $arrayTemp['order_id'] = $object->order_id;
            $arrayTemp['store'] = $object->store;
            $arrayTemp['part_no'] = $object->part_no;
            $arrayTemp['description'] = $object->description;
            $arrayTemp['quantity'] = $object->quantity;

            array_push($arrayOut, $arrayTemp);
        }
        return $arrayOut;
    }
}
