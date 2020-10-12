<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use maerduq\usm\models\MenuItem;
use maerduq\usm\components\Usm;
use yii\helpers\Url;

$form = ActiveForm::begin(['layout' => 'horizontal', 'enableClientValidation' => false]);
?>

<?= $form->errorSummary($model); ?>
<?= $form->field($model, 'visible')->checkbox()->hint('Whether this menu item should be visible'); ?>

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
    {hint}'])->hint('The visible title of the menu item'); ?>

<?php
$translations = $model->titleTranslationsByKey;
foreach ($languages as $l) :
    ?>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="input-group">
                <span class="input-group-addon"><?= $l ?></span>
                <input type="text" class="form-control" name="translations[title][<?= $l ?>]" value="<?= isset($translations[$l]) ? $translations[$l]->value : '' ?>" />
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $form->field($model, 'alias')->hint('The name for this menu item in the address bar (should only contain lower case letters or dashes). If this is left blank, an url name will be generated'); ?>
<hr />
<?= $form->field($model, 'type')->listBox(MenuItem::$typeOptions)->hint('What item should be linked to this menu item?'); ?>
<?= $form->field($model, 'page_id')->dropDownList($this->params['cms_options'], ['prompt' => '- Make new page (name will be equal to the title of the menu item) -'])->hint(($model->isNewRecord) ? 'This menu item will inherit access rights from the page.' : '<button onclick="window.location=\'' . Url::to(['/usm/pages/update', 'id' => 'XXX']) . '\'.replace(\'XXX\',$(\'#menuitem-page_id\').val())" type="button" class="btn btn-default">Edit page</button> This menu item will inherit access rights from the page.'); ?>

<?= $form->field($model, 'url')->hint('<p>For links: just copy the url WITH http://</p><p>For controllers, example: /model/update?id=1</p>'); ?>
<hr />
<?= $form->field($model, 'access')->listBox(["0" => "All users", "1" => "Logged in users", "2" => "Admins"], ['size' => 3])->hint('What kind of users should be able to see and visit this menu item?'); ?>
<div class="form-group">
    <div class="col-sm-offset-3 col-sm-6">
        <?= Html::submitButton((($model->isNewRecord) ? 'Create' : 'Update') . ' menu item', ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Back', Usm::returnUrl($return, ['admin']), ['class' => 'btn btn-link']); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(checkType);
    $("#menuitem-type").bind('change', checkType);

    function checkType() {
        switch ($("#menuitem-type").val()) {
            case 'cms':
                $("#menuitem-url").closest('.form-group').hide();
                $("#menuitem-access").closest('.form-group').hide();
                $("#menuitem-page_id").closest('.form-group').show();
                break;
            case 'php':
            case 'link':
                $("#menuitem-url").closest('.form-group').show();
                $("#menuitem-access").closest('.form-group').show();
                $("#menuitem-page_id").closest('.form-group').hide();
                break;
            case 'empty':
                $("#menuitem-url").closest('.form-group').hide();
                $("#menuitem-access").closest('.form-group').show();
                $("#menuitem-page_id").closest('.form-group').hide();
                break;
        }
    }
</script>