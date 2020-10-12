<?php

use maerduq\usm\assets\TinymceAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

TinymceAsset::register($this);

$form = ActiveForm::begin(['layout' => 'horizontal']);
?>
<hr />
<?= $form->field($model, 'name') ?>


<?php
$languages = $this->context->module->languages;
$baseLanguage = array_shift($languages);
?>


<?= $form->field($model, 'text', ['template' => '
   {label}
   <div class="col-sm-6">
          <b>' . $baseLanguage . '</b><br />
          {input}
       {error}
    </div>
    {hint}'])->textarea(['class' => 'tinymce', 'style' => 'height:300px']) ?>

<?php
$translations = $model->textTranslationsByKey;
foreach ($languages as $l) :
    ?>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <b><?= $l ?></b></br>
            <textarea class="form-control tinymce" style='height:300px' name="translations[text][<?= $l ?>]"><?= isset($translations[$l]) ? $translations[$l]->value : '' ?></textarea>
        </div>
    </div>
<?php endforeach; ?>

<?= $form->field($model, 'description')->textarea() ?>

<div class="row">
    <div class="col-sm-offset-3 col-sm-6">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Back', ['admin'], ['class' => 'btn btn-link']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        tinymce.init({
            selector: "textarea.tinymce",
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
            relative_urls: false,
            convert_urls: false
        });
    });
</script>