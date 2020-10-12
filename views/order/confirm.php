<?php

/* @var $this yii\web\View */

use app\models\Order_details;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

$this->title = 'confirm order';
?>

<div class="order-confirm">
    <div style="text-align:center">

        <h1>Confirm your order</h1>
        <p>Check your order! If everything is ok, confirm the order below</p>
    </div>
    <?php
    if (sizeof($orders) === 1) {
        $dataProvider = new ActiveDataProvider([
            'query' => Order_details::find()
                ->where(['order_id' => $orders[0]->id]),
        ]);
    } elseif (sizeof($orders) === 2) {
        $dataProvider = new ActiveDataProvider([
            'query' => Order_details::find()
                ->where(['order_id' => $orders[0]->id])
                ->orWhere(['order_id' => $orders[1]->id])
                ->orderBy('store'),
        ]);
    }

    $gridTitle = 'Order of ' . $orders[0]->customer->name;

    // Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [            
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'header'=>'Store',
                'value'=>'store',
                'group' => true,  // enable grouping,
                'groupedRow' => true,                    // move grouped column to a single grouped row
                'groupOddCssClass' => 'kv-grouped-row',  // configure odd group cell css class
                'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class
            ],
            [
                'header'=>'Part number',
                'value' => 'part_no',
            ],
            [
                'header'=>'Description',
                'value'=>'description',
            ],
            [
                'header'=>'Quantity',
                'value'=>'quantity',
            ],
    ],
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'export' => false,
        'panel' => [
            'type' => 'primary', 
            'heading' => $gridTitle
        ],
        'toolbar'=>false,
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'summary'=>'Order received at ' . $orders[0]->date,
        'pjaxSettings' => [
            'neverTimeout' => true,
            // 'beforeGrid'=>'My fancy content before.',
            // 'afterGrid'=>'My fancy content after.',
        ],
    ]);

    // Pjax::end();
    ?>



    <div style="text-align:center">
        <?php
        $form = ActiveForm::begin([
            'id' => 'confirm-form',
            'layout' => 'horizontal',
            // 'action' => '/site/admin',
            // 'method' => 'get',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],

            ],
        ]);

        ?>

        <?= $form->field($model, 'consent')->checkbox([
            'label' => 'I understand that by confirming this order I accept the financial responsibillity for it.',
            //'template' => "<div class=\"col-lg-offset-3 col-lg-4\">{input} {label}</div>\n<div class=\"col-lg-4\">{error}</div>",
        ]) ?>
        <div class="form-group">
            <?= Html::submitButton('Confirm order', ['class' => 'btn btn-primary', 'name' => 'confirm-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>