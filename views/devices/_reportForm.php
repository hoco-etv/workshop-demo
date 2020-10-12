<?php

use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'device-report',
    'layout' => 'horizontal',
    'method' => 'post',
    'action' => ['report_submitted'],
]);
?>


<?= $form->field($device, 'id')->textInput(['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'cursor:not-allowed;border-color:#ccc;-webkit-box-shadow:unset'])->hint('Device ID') ?>
<?= $form->field($device, 'brand')->textInput(['class' => 'form-control', 'disabled' => true])->hint('Device brand or manufacturer') ?>
<?= $form->field($device, 'name')->textInput(['class' => 'form-control', 'disabled' => true])->hint('Device name') ?>
<?= $form->field($device, 'type')->textInput(['class' => 'form-control', 'disabled' => true])->hint('Device series number') ?>
<?= $form->field($device, 'userReport')->textArea(['rows' => 8])->hint('Please describe the issue as clear as possible') ?>



<div style="text-align:center">
    <input type="submit" class="btn btn-success" value="Submit">
    <a class="btn btn-link" href='/devices'>Back</span></a>
</div>


<?php
ActiveForm::end();
?>