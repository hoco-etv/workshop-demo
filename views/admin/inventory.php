<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use app\models\Inventory;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\typeahead\Typeahead;

$this->title = 'Klushok';
$this->params['documentation'] = $this->render('_inventory_docs');
?>

<div class="inventory-panel">

  <?php

  Pjax::begin([]);
  
  $form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'method' => 'post',
    'action' => ['inventory'],
    'formConfig' => [
      'labelSpan' => 4,
      'deviceSize' => ActiveForm::SIZE_TINY
    ],
    'fieldConfig' => [
      'options' => [
        'class' => 'form-group mr-1',
        // 'data-pjax' => true,
      ],
    ]
  ]);
  ?>

  <div class="form-group row mb-1">
    <div class="col-sm-4">
      <?= $form->field($model, 'category')
        ->widget(
          Typeahead::classname(),
          [
            'dataset' => [
              ['local' => Inventory::getCategories()],
            ],
            'options' => ['placeholder' => 'Categories', 'autocomplete' => 'off'],
            'pluginOptions' => ['highlights' => true],
          ]
        )
      ?>
    </div>
    <!-- <div class="form-group row mb-0"> -->
    <div class="col-sm-2">
      <?= $form->field($model, 'name')->textInput(['type' => 'text', 'placeholder' => 'Name', 'autocomplete' => 'off']) ?>
    </div>
    <div class="col-sm-2">
      <?= $form->field($model, 'info')->textInput(['type' => 'text', 'placeholder' => 'Info']) ?>
    </div>
    <div class="col-sm-2">
      <?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => 0.01, 'min'=>0, 'placeholder' => 'Price']) ?>
    </div>
    <div class="col-sm-1">
      <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    </div>
  </div>
  <?= $form->field($model, 'action')->hiddenInput(['value'=> 'add'])->label(false)?>
  <?php
  ActiveForm::end();
  ?>




  <?php
  echo GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => $columns,
    'pjax' => true,
    'striped' => true,
    'hover' => true,
    'panel' => ['type' => 'primary', 'heading' => 'Klushok inventory'],
    'toggleDataContainer' => ['class' => 'btn-group mr-2'],
    'pjaxSettings' => [
      'neverTimeout' => true,
    ]
  ]);
  Pjax::end();

  ?>


</div>