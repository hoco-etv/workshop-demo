<?php

/* @var $this yii\web\View */


use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;
use app\models\Order;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;



$this->title = 'Klushok';
?>
<div class="admin_view_customer">

    <h4>Orders from <?= $customer->name ?></h4>

    <?php
    $dataProvider = new ActiveDataProvider([
        'query' => Order::find()
            ->where(['user_id' => $customer->id])
            ->orderBy(['date' => SORT_ASC]),
    ]);

    echo GridView::widget([
        'summary' => '',
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => 'Date',
                'value' => 'date',
                // 'group' => true,
            ],
            [
                'header' => 'Price',
                'value' => 'price',
            ],
            [
                'header' => 'Store',
                'value' => function ($model) {
                    if (!empty($model->order_details)) {
                        return($model->order_details[0]->store);
                    }
                },
            ],
            [
                'header' => 'Confirmed',
                'value' => 'confirmed',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'showNullAsFalse' => true,
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
            ],
            [
                'header' => 'Paid',
                'value' => 'paid',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
            ],
            [
                'header' => 'Ordered',
                'value' => 'ordered',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
            ],
            [
                'header' => 'Retrieved',
                'value' => 'retrieved',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
            ],
        ],
    ]);

    ?>

</div>