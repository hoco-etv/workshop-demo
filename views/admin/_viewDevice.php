<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\markdown\Markdown;

$this->title = 'Klushok';
$this->registerCssFile('css/devices.css');

?>
<div class="device-view">
    <?= $model->getHtml() ?>

    <?php if(!empty($model->repair_notes)){?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span style="font-size: 20pt">Repair notes</span>
        </div>
        <div class="panel-body">
            <?= Markdown::convert($model->repair_notes) ?>
        </div>
    </div>
    <?php } else {
        echo 'No repair notes or user reports';
    }
    ?>
</div>