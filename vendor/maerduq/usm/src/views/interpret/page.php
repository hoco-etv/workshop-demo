<?php

use maerduq\usm\components\Usm;
use yii\helpers\Html;

$this->params['pageHeader'] = $page->title;
$this->title = $page->title;
$this->params['breadcrumbs'] = array(
    $page->title
);
?>

<?php if ($this->context->module->isUserAdmin()): ?>
    <div style="position:absolute;right:0px;top:0px;z-index:100" class="btn-group">
        <?php
        if ($page->wysiwyg) {
            echo Html::a('Edit page content', ['/usm/pages/editpage', 'id' => $page->id, 'lang' => Yii::$app->language, 'return' => Usm::returnUrl()], ['class' => 'btn btn-default btn-xs']);
            echo Html::a('Edit page properties', ['/usm/pages/update', 'id' => $page->id, 'return' => Usm::returnUrl()], ['class' => 'btn btn-default btn-xs']);
        } else {
            echo Html::a('Edit page', ['/usm/pages/update', 'id' => $page->id, 'return' => Usm::returnUrl()], ['class' => 'btn btn-default btn-xs']);
        }
        ?>
    </div>

<?php endif; ?>

<?= $page->content; ?>
