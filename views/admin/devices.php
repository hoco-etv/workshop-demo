<?php

/* @var $this yii\web\View */

use app\models\Device;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use kartik\typeahead\Typeahead;

$this->title = 'Klushok';
$this->registerCssFile('@web/css/devices.css');
$this->params['documentation'] = $this->render('_devices_docs');

?>
<div class="Admin-devices">
    <?php

    $device = new Device();
    $device->scenario = 'new';

    $form = ActiveForm::begin([
        'id' => 'newDevice',
        'method' => 'post',
        'action' => 'new_device',
        'layout' => 'horizontal'
    ]);
    ?>
    <div style="margin-bottom:20px">
        <button id='new-btn' class="btn btn-success" onclick="$(this).hide();$('#new-device').slideDown()">Add device</button>
        <div id="new-device" class="panel panel-default" style="display: none">
            <div class="panel-heading">Add device</div>
            <div class="panel-body">
                <?= $form->field($device, 'brand')->widget(
        Typeahead::classname(),
        [
            'dataset' => [
                ['local' => $device->getDistict('brand')],
            ],
            'options' => ['placeholder' => 'Brand', 'autocomplete' => 'off'],
            'pluginOptions' => ['highlights' => true],
        ]
    )
        ->hint('e.g. \'AOYUE\'') ?>
                <?= $form->field($device, 'name')->hint('e.g. \'Soldering station\'') ?>
                <?= $form->field($device, 'type')->hint('e.g. INT2930') ?>
                <?= $form->field($device, 'image')->hint('Directory to file uploaded using \'Files\' under the category \'devices\'  (e.g. file/devices/fileName)') ?>
                <?= $form->field($device, 'manual')->hint('Directory or URL to device manual') ?>

                <div style="text-align:center">
                    <input type="submit" class="btn btn-success" value="Add">
                    <a class="btn btn-link" onclick="$('#new-device').slideUp();$('#new-btn').show()">Back</span></a>
                </div>
            </div>
        </div>
    </div>
    <?php

    ActiveForm::end();




    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'responsive' => false,
        'panel' => ['type' => 'primary', 'heading' => 'Klushok Devices'],
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'pjaxSettings' => [
            'neverTimeout' => true,

        ],
    ]);

    Modal::begin([
        'id' => 'modal',
        'header' => '<h3>Device image</h3>'
    ]);
    echo "<div id=modalContent></div>";
    Modal::end();

    Pjax::end();

    $this->registerJs("$(function () { 
        $('.update-modal-link').click(function (e) {
            e.preventDefault();
            $('#modal')
                .modal('show')
                .find('#modalContent')
                .load($(this).attr('href'));
        });
    });")

    ?>