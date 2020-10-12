<?php

use maerduq\usm\components\Usm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => [
                'enctype' => "multipart/form-data"
            ]
        ]);

$sizeLimit = ini_get('upload_max_filesize');

echo $form->field($model, 'file')->fileInput()->hint("File size limit: {$sizeLimit}. " . (($model->isNewRecord) ? '' : 'Upload a file if you want to change the file. ' . Html::a('Download current file', ['download', 'id' => $model->id]) . "."));
echo $form->field($model, 'name')->hint('For if you want to name the uploaded file different than the original file.');
echo $form->field($model, 'category')->hint('By adding a file to a category, it\'ll be more manageble. A category is not required.');
echo $form->field($model, 'access')->dropDownList(Usm::$accessOptions);
?>

<div class="col-sm-offset-3 col-sm-6">
    <input type="submit" value="<?= ($model->isNewRecord) ? 'New file' : 'Save file' ?>" class="btn btn-primary"/>
    <?= Html::a('Back', ['index'], ['class' => 'btn btn-link']); ?>
</div>

<?php
ActiveForm::end();
