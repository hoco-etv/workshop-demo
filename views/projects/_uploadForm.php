<?php

// use maerduq\usm\components\Usm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;
use kartik\markdown\MarkdownEditor;


$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'options' => [
        'enctype' => "multipart/form-data",
    ],
    // 'enableAjaxValidation'=>true,
]);
$model->scenario = 'new';
?>
<?= $form->field($model, 'title')->hint("Title of your project") ?>
<?= $form->field($model, 'subtitle')->hint("A short introduction to you project, max 140 characters") ?>
<?= $form->field($model, 'author')->hint("The author of the project") ?>
<?= $form->field($model, 'email')->hint("Your email is used to verify the post or contact you if changes are required. Only tudelft.nl email adresses are allowed") ?>
<?= $form->field($model, 'cover')->fileInput()->hint("The Cover image of your project, the advised aspect ratio is 4:3") ?>
<?= $form->field($model, 'file1')->fileInput()->hint("Optional extra file/image") ?>
<?= $form->field($model, 'file2')->fileInput()->hint("Optional extra file/image") ?>
<?= $form->field($model, 'file3')->fileInput()->hint("Optional extra file/image") ?>
<?= $form->field($model, 'file4')->fileInput()->hint("Optional extra file/image") ?>
<?= $form->field($model, 'file5')->fileInput()->hint("Optional extra file/image") ?>

<?php // TODO: pre processor toevoegen door de web config url aan te passen, afbeelding in te voegen met juiste breedte en vervolgens een preview te renderen door een redirect naar 'markdown/parse/preview te maken 
?>

<?= $form->field($model, 'content')->widget(
    MarkdownEditor::classname(),
    [
        // 'height'=>300,
        'encodeLabels' => false,
    ]
)->hint("The contents of your project! Please describe how and why this project was made, your relevant choices and your results. You can type everything you'd like to say about your project here. <br><br>
    To enter your uploaded files into the text, refer to them as {{file*}} where * corresponds to the number of one of the uploaded files.") ?>


<div class="col-sm-offset-3 col-sm-6">
    <input type="submit" value="<?= 'Save file' //($model->isNewRecord) ? 'New file' : 'Save file' 
                                ?>" class="btn btn-primary" />
    <?= Html::a('Back', ['upload'], ['class' => 'btn btn-link']); ?>
</div>

<?php
ActiveForm::end();
