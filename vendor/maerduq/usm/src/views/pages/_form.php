<?php

use maerduq\usm\assets\TinymceAsset;
use maerduq\usm\assets\CodemirrorAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use maerduq\usm\components\Usm;
use maerduq\usm\models\Page;

TinymceAsset::register($this);
CodemirrorAsset::register($this);

$form = ActiveForm::begin(['layout' => 'horizontal']);
?>
<div class="btn-toolbar">
    <?= Html::submitButton((($model->isNewRecord) ? 'Create' : 'Update') . ' page', ['class' => 'btn btn-primary']) ?>
    <?php
    if (!$model->isNewRecord && $model->wysiwyg) {
        echo Html::a('Edit page content', ['pages/editpage', 'id' => $model->id, 'return' => $return], ['class' => 'btn btn-link']);
    }
    ?>
    <?= Html::a('Back', Usm::returnUrl($return, ['admin']), ['class' => 'btn btn-link']) ?>
</div>

<hr />
<?php
$languages = $this->context->module->languages;
$baseLanguage = array_shift($languages);
?>

<?= $form->field($model, 'title', ['template' => '
   {label}
   <div class="col-sm-6">
       <div class="input-group">
          <span class="input-group-addon">' . $baseLanguage . '</span>
          {input}
       </div>
       {error}
    </div>
    {hint}']); ?>

<?php
$translations = $model->titleTranslationsByKey;
foreach ($languages as $l) :
    ?>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="input-group">
                <span class="input-group-addon"><?= $l ?></span>
                <input type="text" class="form-control" name="translations[title][<?= $l ?>]" value="<?= isset($translations[$l]) ? $translations[$l]->value : $model->title ?>" />
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $form->field($model, 'access')->dropDownList(Usm::$accessOptions) ?>
<div class="panel panel-default">
    <div class="panel-heading" onclick=""> 
        <h4 class="panel-title">
            <a data-toggle="collapse" href="#collapseOne">Advanced settings</a>
        </h4>
    </div>
    <div class="panel-collapse collapse" id="collapseOne">
        <div class="panel-body">
            <?= $form->field($model, 'style')->dropDownList(Page::$styleOptions) ?>
            <?= $form->field($model, 'wysiwyg')->checkbox() ?>
        </div>
    </div>
</div>
<hr />
<?php
if ($model->isNewRecord) {
    echo "<p>The content of the page can be edited after you saved the properties first</p>";
} elseif ($model->wysiwyg) {
    echo "<p>You can edit the content of the page via " . Html::a('this link', ['pages/editpage', 'id' => $model->id]) . "</p>";
} else {
    echo "<b>$baseLanguage</b>";
    echo Html::textarea('Page[content]', $model->content, ['class' => 'codemirror']);

    $contentTranslations = $model->contentTranslationsByKey;
    foreach ($languages as $l) :
        ?>
        <b><?= $l ?></b></br>
        <textarea class="form-control codemirror" style='height:300px' name="translations[content][<?= $l ?>]"><?= isset($contentTranslations[$l]) ? $contentTranslations[$l]->value : '' ?></textarea>
        <?php
    endforeach;
}
?>
<hr />
<div class="btn-toolbar">
    <?= Html::submitButton((($model->isNewRecord) ? 'Create' : 'Update') . ' page', ['class' => 'btn btn-primary']) ?>
    <?php
    if (!$model->isNewRecord && $model->wysiwyg) {
        echo Html::a('Edit page content', ['pages/editpage', 'id' => $model->id], ['class' => 'btn btn-link']);
    }
    ?>
    <?= Html::a('Back', Usm::returnUrl($return, ['admin']), ['class' => 'btn btn-link']) ?>
</div>


<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(function () {
<?php if (!$model->wysiwyg): ?>
            $("textarea.codemirror").each(function () {
                var myCodeMirror = CodeMirror.fromTextArea($(this).get(0), {
                    mode: "text/html",
                    lineNumbers: true,
                    lineWrapping: true,
                });
            });
<?php endif; ?>
    });
</script>
<style type="text/css">
    .CodeMirror {
        border: 1px solid #ddd;
        height: auto;
    }
</style>