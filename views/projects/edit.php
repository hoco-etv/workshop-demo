<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\Project;
use kartik\markdown\MarkdownEditor;
use yii\bootstrap\ActiveForm;


$this->title = 'Klushok';

?>

<div class="project-edit">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        if ($key == 'error') {
            $key = 'danger';
        }
        echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
    }

    $form = ActiveForm::begin([
        //'layout' => 'horizontal',
        'options' => [
            'enctype' => "multipart/form-data",
        ],
        // 'enableAjaxValidation'=>true,
    ]);
    ?>

    <?= $form->field($model, 'cover')->fileInput()->hint("Replace cover image, leave blank to keep the current cover") ?>

    <?= $form->field($model, 'title') ?>
    <?= $form->field($model, 'subtitle') ?>

    <?php
    $files = ['file1', 'file2', 'file3', 'file4', 'file5'];
    foreach ($files as $index => $file) : ?>
        <div class="form-group row sm-2">
            <?= Html::activeLabel($model, $file, ['label' => 'File ' . ($index + 1), 'class' => 'col-sm-2 col-form-label']) ?>
            <div class="col-sm-5">
                <?= $form->field($model, $file . '_name')->textInput(['placeholder' => 'file not uploaded yet', 'disabled' => !$model->{$file . '_type'}])->label(false)->hint('Change the name of the currently uploaded file') ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, $file)->fileInput()->label(false)->hint("(re)upload file/image, click 'save file' to confirm") ?>
            </div>
            <div class="col-sm-1 col-sm-offset-1">
                <a href=<?= "/projects/imgdelete?h=" . $hash . "&file=" . $index ?> class="glyphicon glyphicon-trash delete-img" style="color:#737373;font-size:18px" data-confirm="Are you sure to delete this item?"></a>
            </div>
        </div>
    <?php endforeach; ?>

    <?= $form->field($model, 'content')->widget(
        MarkdownEditor::classname(),
        [
            'height' => 600,
            'encodeLabels' => false,
            //'value' => $model->content,
        ]
    ) ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'hash')->hiddenInput()->label(false) ?>

    <br>
    <div class="col-sm-6">
        <input type="submit" value="Save project" class="btn btn-primary" />
        <?= Html::a('Back', ['view?id=' . $model->id . '&h=' . $model->hash], ['class' => 'btn btn-link', 'data-confirm'=>"Any unsaved changes will be lost, are you sure you want to go back?"]); ?>
    </div>

    <?php
    ActiveForm::end();
    ?>
</div>