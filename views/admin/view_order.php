<?php

/* @var $this yii\web\View */


use yii\helpers\Html;
// use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
// use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Order_details;
use app\models\Order;

// use kartik\grid\SerialColumn;
// use yii\grid\Column;
// use kartik\builder\TabularForm;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;


$this->title = 'Klushok';
?>
<div class="view_order">
  <?php

  $dataProvider = new ActiveDataProvider([
    'query' => Order_details::find()
      ->where(['order_id' => $model->id]),
  ]);

  echo GridView::widget([
    'summary' => '',
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      ['header' => 'Store', 'value' => 'store',],
      ['header' => 'Part number', 'value' => 'part_no',],
      ['header' => 'Description', 'value' => 'description',],
      ['header' => 'Quantity', 'value' => 'quantity',],
    ],
  ]);

  ?>

  <?= Html::beginForm(['/admin/orders'], 'post', ['data-pjax' => true, 'class' => 'form-inline']); ?>
  <?= Html::hiddenInput('action', 'edit_order') ?>
  <?= Html::hiddenInput('id', $model->id) ?>
  Price: <?= Html::input('number', 'price', $model->price, [
            'label' => 'Price',
            'class' => 'form-control',
            'step' => 0.01,
            'disabled' => $model->paid ? true : false,
          ]) ?>
  <?= Html::checkbox('confirmed', $model->confirmed, [
    'label' => 'Confirmed',
    'class' => 'form-control',
    'style' => 'margin-left:5em',
  ]) ?>
  <?= Html::checkbox('paid', $model->paid, [
    'label' => 'Paid', 'class' => 'form-control',
    'style' => 'margin-left:5em',
  ]) ?>
  <?= Html::checkbox('ordered', $model->ordered, [
    'label' => 'Ordered',
    'class' => 'form-control',
    'style' => 'margin-left:5em',
  ]) ?>
  <?= Html::checkbox('arrived', $model->arrived, [
    'label' => 'arrived',
    'class' => 'form-control',
    'style' => 'margin-left:5em',
  ]) ?>
  <?= Html::checkbox('retrieved', $model->retrieved, [
    'label' => 'Retrieved',
    'class' => 'form-control',
    'style' => 'margin-left:5em',
  ]) ?>
  <br>
  <?= Html::submitButton('Submit', [
    'class' => 'btn btn-primary',
    'name' => 'submit',
  ]) ?>
  <?= Html::endForm() ?>
</div>