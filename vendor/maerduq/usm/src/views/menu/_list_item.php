<?php

use yii\helpers\Html;
use maerduq\usm\components\Usm;
use yii\helpers\Url;
?>
<div class="media">
    <div class="pull-right">
        <?php
        echo ($data->visible == 1) ? "<span class='label label-success lbl-visible'>Visible</span>" : "<span class='label label-default lbl-visible'>Hidden</span>";

        if ($data->type == 'cms' && $data->page != null) {
            $access = $data->page->access;
        } else {
            $access = $data->access;
        }

        switch ($access) {
            case 0:
                echo "<span class='label label-success lbl-access'>All visitors</span>";
                break;
            case 1:
                echo "<span class='label label-warning lbl-access'>Logged in users</span>";
                break;
            case 2:
                echo "<span class='label label-danger lbl-access'>Administrators</span>";
                break;
        }

        switch ($data->type) {
            case "cms":
                if ($data->page != null) {
                    echo '<span class="label label-success" data-toggle="tooltip" data-placement="top" title="' . $data->page->title . '">Page</span>';
                }
                break;
            case "php":
                echo '<span class="label label-warning" data-toggle="tooltip" data-placement="top" title="' . $data->url . '">Module</span>';
                break;
            case 'link':
                echo Html::a('<span class="label label-info" data-toggle="tooltip" data-placement="top" title="' . $data->url . '">Link</span>', $data->url, ['target' => '_blank']);
                break;
            case "empty":
                echo "<span class='label label-default'>Empty</span>";
                break;
        }
        ?>
        <div style="width:190px;text-align:right;display:inline-block">
            <div class="btn-group">
                <?php
                switch ($data->type) {
                    case "cms":
                        if ($data->page != null) {
                            echo Html::a("<span class='glyphicon glyphicon-edit'></span> Edit page", [($data->page->wysiwyg) ? 'pages/editpage' : 'pages/update', 'id' => $data->page_id, 'return' => Usm::returnUrl()], array('class' => 'btn btn-xs btn-default'));
                        }
                        break;
                }
                ?>
                <a href="<?= Url::to(['update', 'id' => $data->id]) ?>" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                <a onclick="return confirm('Sure to delete this menu item?')" href="<?= Url::to(['delete', 'id' => $data->id]) ?>" class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a>
            </div>
            <?php
            if ($sudo != null) {
                echo "(" . $data->position;
            }
            ?>

        </div>
    </div>

    <?php if ($data->redirect != null): ?>
        <a href="<?= Url::base() . "/" . $data->redirect->url ?>" target="_blank" class='btn btn-xs btn-default'><span class='glyphicon glyphicon-eye-open'></span> View</a>&nbsp;&nbsp;
        <h4 data-toggle="tooltip" data-placement="top" title="<?= '/'.$data->redirect->url ?>"><?= $data->title ?></h4>
    <?php else: ?>
        <h4><?= $data->title ?></h4>
    <?php endif; ?>
</div>