<?php

namespace app\models;

use Yii;
use yii\base\Model;

use app\models\Order;
use yii\data\ActiveDataProvider;

class CustomerSearch extends Customer
{
    //https://www.yiiframework.com/wiki/653/displaying-sorting-and-filtering-model-relations-on-a-gridview
    public $name;
    public $student_no;
    public $email;
    // public $date;

    public function rules()
    {
        return[
            [['name','student_no', 'email'],'safe'],
        ];
    }

    public function search($params)
    {
        $query = Customer::find();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
        ->andFilterWhere(['like', 'id', $this->name])
        ->andFilterWhere(['like', 'id', $this->email])
        ->andFilterWhere(['like', 'id', $this->student_no])
        ;     

        return $dataProvider;

    }

}