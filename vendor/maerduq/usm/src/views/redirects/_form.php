<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use maerduq\usm\models\MenuItem;
use maerduq\usm\models\Redirect;

$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?= $form->field($model, 'active')->checkbox()->hint('Whether the URL should be active') ?>
<?= $form->field($model, 'forward')->listbox(Redirect::$forwardOptions, ['size' => 2])->hint('Whether the URL should be visitable, or a forward. (A link will always be a forwarder)') ?>
<?= $form->field($model, 'url', ['template' => '
   {label}
   <div class="col-sm-6">
       <div class="input-group">
          <span class="input-group-addon">' . $_SERVER['SERVER_NAME'] . Url::base() . '/</span>
          {input}
       </div>
       {error}
    </div>
    {hint}'])->hint('The URL which should be redirected.') ?>
<?= $form->field($model, 'type')->listBox(Redirect::$typeOptions, ['size' => 4])->hint('What item should be linked to this URL?') ?>
<?= $form->field($model, 'menu_item_id')->dropDownList($this->context->getOptions(MenuItem::className(), 'title', [], false)) ?>

<div class="form-group">
    <label class="control-label col-sm-3">Page <span class="required">*</span></label>
    <div class="col-sm-6">
        <?= Html::dropDownList('cms_page', $model->destination, $this->params['cms_options'], ['class' => 'form-control', 'id' => 'cms_page']) ?>
    </div>
</div>
<?= $form->field($model, 'destination')->hint('<p>For links: just copy the url WITH http://</p><p>For controllers, example: /model/update?id=1</p>') ?>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-6">
        <?= Html::submitButton((($model->isNewRecord) ? 'Create' : 'Update') . ' URL', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-link']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
<script type="text/javascript">
    $(document).ready(checkType);
    $("#redirect-type").bind('change', checkType);

    function checkType() {
        switch ($("#redirect-type").val()) {
            case 'cms':
                $("#redirect-destination").closest('.form-group').hide();
                $("#cms_page").closest('.form-group').show();
                $("#redirect-menu_item_id").closest('.form-group').hide();
                break;
            case 'php':
                $("#redirect-destination").closest('.form-group').show();
                $("#cms_page").closest('.form-group').hide();
                $("#redirect-menu_item_id").closest('.form-group').hide();
                break;
            case 'menu_item':
                $("#redirect-destination").closest('.form-group').hide();
                $("#cms_page").closest('.form-group').hide();
                $("#redirect-menu_item_id").closest('.form-group').show();
                break;
            case 'link':
                $("#redirect-destination").closest('.form-group').show();
                $("#cms_page").closest('.form-group').hide();
                $("#redirect-menu_item_id").closest('.form-group').hide();
                break;
        }
    }
</script>