<?php

use app\models\Device;
use kartik\markdown\MarkdownEditor;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */

$this->title = 'Klushok';
// $this->registerCssFile('css/devices.css');

?>
<div class="device-repair">
    <h1 style="text-align: center;margin-bottom:50px"><?= $device->brand . ' ' . $device->name . ' ' . $device->type ?></h1>

    <div class="container" style='margin-bottom:20px'>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <img src='<?= $device->image ?>' style='max-width:100%'>
            </div>
            <div class="col"></div>
        </div>
    </div>

    <?php
    $form = ActiveForm::begin([
        'id' => 'device-report',
        'layout' => 'horizontal',
        'method' => 'post',
        'action' => ['repair_device?id=' . $device->id],
    ]);
    ?>

    <?= $form->field($device, 'id')->textInput(['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'cursor:not-allowed;border-color:#ccc;-webkit-box-shadow:unset'])->hint('Device ID')?>
    <?= $form->field($device, 'brand')->hint('Device brand or manufacturer')?>
    <?= $form->field($device, 'name')->hint('Device name')?>
    <?= $form->field($device, 'type')->hint('Device series number')?>
    <?= $form->field($device, 'status')->dropDownList(array_column(Device::$statusArray, 'message'))?>
    <?= $form->field($device, 'image')->hint('Path to image uploaded using \'Files\'')?>
    <?= $form->field($device, 'manual')->hint('URL or path to manual (can be uploaded using \'Files\')')?>
    <?= $form->field($device, 'description')->textArea(['rows' => 8])->hint('Please describe the device as clear as possible in max 500 words')?>
    <?= $form->field($device, 'repair_notes')->widget(
    MarkdownEditor::classname(),
    [
    // 'height'=>300,
    'encodeLabels' => false,
    ]
    )->hint('Note down any repairs or view the issues reported by users. <br><b>This field uses markdown, enable \'preview\' to ease reading repair notes</b>')?>

    <div style="text-align:center">
        <input type="submit" class="btn btn-success" value="Submit">
        <a class="btn btn-link" href='/admin/devices'>Back</span></a>
    </div>


    <?php
    ActiveForm::end();
    ?>
</div>