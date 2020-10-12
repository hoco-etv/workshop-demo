<?php

namespace app\models;

use Yii;
use yii\base\Model;

use app\models\Order;
use yii\data\ActiveDataProvider;

class OrderSearch extends Order
{
    //https://www.yiiframework.com/wiki/653/displaying-sorting-and-filtering-model-relations-on-a-gridview
    public $customer;
    public $order_details;
    // public $date;

    public function rules()
    {
        return [
            [['customer', 'order_details'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Order::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);



        // $dataProvider->sort->attributes['customer'] = [
        //     'asc' => ['customers.id' => SORT_ASC],
        //     'desc' => ['customers.id' => SORT_DESC],
        // ];

        // $dataProvider->sort->attributes['order_details'] = [
        //     'asc' => ['order_details.order_id' => SORT_ASC],
        //     'desc' => ['order_details.order_id' => SORT_DESC],
        // ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            // ->andFilterWhere(['like', 'id', $this->id])

            // Here we search the attributes of our relations using our previously configured
            // ones the models
            ->andFilterWhere(['user_id'=> $this->customer])
            // ->andFilterWhere(['like', 'order_details.order_id', $this->order_details])
            ;


        return $dataProvider;
    }
}
