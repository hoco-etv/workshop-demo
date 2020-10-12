<?php

/**
 * @var File $model
 */

use maerduq\usm\models\File;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['pageHeader'] = 'Files';
$this->params['breadcrumbs'] = [
    ['label' => 'USM Admin', 'url' => ['/usm']],
    'Files'
];
$this->params['documentation'] = $this->render('admin_docs');
?>

<script type="text/javascript">
    var what = "new";
</script>



<a class="btn btn-primary" onclick="$(this).remove();$('#new-file').slideDown()">New file</a>
<div class="panel panel-default" id="new-file" style="display:none">
    <div class="panel-heading">
        <h4 class="panel-title">Upload new file</h4>
    </div>
    <div class="panel-body">
        <?= $this->render('_form', ['model' => new File(['scenario' => 'new'])]); ?>
    </div>
</div>
<hr />

<div class="table-responsive">
    <?=
    GridView::widget([
        'dataProvider' => $model->search(),
        'columns' => [
            [
                'format' => 'html',
                'value' => function($data) {
                    return Html::a('<span class="glyphicon glyphicon-download"></span>', ['download', 'id' => $data->id], ['class' => 'btn btn-xs btn-default']);
                }
            ],
            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function($data) {
                    return Html::a($data->id, ['download', 'category' => $data->category, 'name' => $data->name], ['class' => 'button-link']);
                }
            ],
            [
                'attribute' => 'access',
                'format' => 'html',
                'contentOptions' => [
                    'style' => 'width:100px;'
                ],
                'value' => function($model) {
                    switch ($model->access) {
                        case 0:
                            return "<span class='label label-success'>All visitors</span>";
                        case 1:
                            return "<span class='label label-warning'>Logged in users</span>";
                        case 2:
                            return "<span class='label label-danger'>Administrators</span>";
                    }
                    return '';
                }
            ],
            'category',
            [
                'attribute' => 'name',
                'value' => function($data) {
                    $name = $data->name . '.' . $data->file_ext;
                    if (in_array(strtolower($data->file_ext), ['jpg', 'jpeg', 'gif', 'bmp', 'png', 'svg'])) {
                        return Html::a($name, ['download', 'id' => $data->id], ['class' => 'img-link']);
                    } else {
                        return $name;
                    }
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'file_size',
                'value' => function ($data) {
                    return $data->file_size_readable;
                },
                'filter' => false
            ],
            'last_accessed_at:datetime',
            'updated_at:datetime',
            [
                'header' => 'Actions',
                'value' => function($data) {
                    return "<div class='btn-group'>" .
                            Html::a('<span class="glyphicon glyphicon-edit"></span> Edit', ['update', 'id' => $data->id], ['class' => 'btn btn-xs btn-primary']) .
                            Html::a('<span class="glyphicon glyphicon-remove"></span> Delete', ['delete', 'id' => $data->id], ['class' => 'btn btn-danger btn-xs button-delete']) .
                    "</div>";
        },
                'format' => 'html',
                'options' => [
                    'style' => 'width:130px;text-align:right'
                ]
            ]
        ]
    ]);
    ?>
</div>

<script type="text/javascript">
    $(".button-delete").click(function () {
        return confirm('Are you sure to delete this file?');
    });

    $(document).ready(function () {
        $(".button-link").each(function () {
            var val = $(this).text();
            var val2 = $(this).attr('href').replace('<?= Url::to('@web') ?>', '');
            $(this).attr('href', '#');
            $(this).popover({
                title: 'File link',
                container: 'body',
                html: true,
                position: 'top',
                content: "\
                <h5>On category and name</h5>\
                In pages: <pre>{{baseUrl}}" + val2 + "</pre>\n\
                In views: <pre>&lt;?= Url::to('@web') ?>" + val2 + "</pre>\n\
                External link: <pre><?= $_SERVER['SERVER_NAME'] . Url::to('@web') ?>" + val2 + "</pre>\
                <h5>On file id</h5>\
                In pages: <pre>{{baseUrl}}/file/" + val + "</pre>\n\
                In views: <pre>&lt;?= Url::to('@web') ?>/file/" + val + "</pre>\n\
                External link: <pre><?= $_SERVER['SERVER_NAME'] . Url::to('@web') ?>/file/" + val + "</pre>"
            });
        });
    });

    $(".img-link").click(function () {
        event.preventDefault();
        var random = Math.random().toString(36).substring(7);
        bootbox.dialog({
            title: $(this).text(),
            message: "<img src='" + $(this).attr('href') + "?" + random + "' style='max-width:100%' />"
        });
    });
</script>
