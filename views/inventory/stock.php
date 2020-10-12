<?php

/* @var $this yii\web\View */

use app\models\Inventory;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Klushok';

?>
<div class="inventory-stock">

    <?php
    Pjax::begin();

    foreach (Yii::$app->session->getAllFlashes() as $key => $message) :
        if ($key == 'error') {
            $key = 'danger';
        }
    ?>
        <?= '<div class="alert alert-' . $key . '">' . $message . "</div>\n" ?>
    <?php endforeach; ?>

    <?php
    $form = ActiveForm::begin([
        'id' => 'stock-form',
        'layout' => 'horizontal',
        'action' => ['stock'],
        'method' => 'post',
        'options' => [
            'data-pjax' => true,
        ]
    ]); ?>

    <?= $form->field($model, 'category')->dropDownList(
        array_combine(Inventory::getCategories(), Inventory::getCategories()),
        [
            'onChange' => '$("#stock-form").attr("action", "stock").submit()',
            'prompt' => 'Select category',
        ]
    )->hint('Component category as seen on the pricelist') ?>

    <?= $form->field($model, 'name')->dropDownList(
        array_combine(Inventory::getNames($model->category), Inventory::getNames($model->category)),
        [
            'onChange' => '$("#stock-form").attr("action", "stock").submit()',
            'prompt' => 'Select name',
            'disabled' => $model->category ? false : true
        ]
    )->hint('Component name as seen on the pricelist') ?>

    <?php
    // TODO if inventory::getInfo returns only a single value, auto select this value; could even be upgraded to say 'no info' if this is the case 
    ?>

    <?= $form->field($model, 'info')->dropDownList(
        array_combine(
            Inventory::getInfo($model->category, $model->name),
            Inventory::getInfo($model->category, $model->name)
        ),
        [
            'prompt' => 'Select info',
            'disabled' => $model->name ? false : true
        ]
    )->hint('Component information as seen on the pricelist. If no info is mentioned, choose \'Select info\'') ?>

    <?= $form->field($model, 'additionalNotes')->textarea(['rows' => 6])
        ->hint('Please note down anything that might be important when re-ordering this component. <br>In case of a resistor this could be its resistance, in case of a capacitor: its capacitance and voltage rating') ?>

    <?php
    ActiveForm::end();
    Pjax::end();
    ?>

    <div class="col-sm-offset-3 col-sm-6">
        <button id='userSubmitBtn' class="btn btn-primary">Submit</button>
    </div>


    <?php

    $this->registerJs("$(function () { 
        $('#userSubmitBtn').click(function (e) {
            e.preventDefault();
            $('#stock-form')
                .attr('action', 'stock_user_submit')
                .submit();
            console.log('test');
        });
    });")
    ?>

</div>