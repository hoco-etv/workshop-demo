<?php

use yii\helpers\Html;
use yii\grid\GridView;
use maerduq\usm\components\Usm;

$this->params['pageHeader'] = "Pages";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    'Pages'
];

$this->params['documentation'] = $this->render('_docs');
?>

<?= Html::a('New Page', ['create'], ['class' => 'btn btn-primary']); ?>
<hr />

<?=
GridView::widget([
    'dataProvider' => $model->search(),
    'columns' => [
        'title',
        [
            'header' => 'Access',
            'attribute' => 'access',
            'sortLinkOptions' => [
                'url' => 'sort=access'
            ],
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
            }
        ],
        [
            'header' => 'In menu',
            'value' => function($data) {
                if ($data->menuItems == null) {
                    return Html::a("Make menu item", ["menu/create", "page" => $data->id, 'return' => Usm::returnUrl()], ['class' => 'btn btn-default btn-xs']);
                } else {
                    $r = [];
                    foreach ($data->menuItems as $m) {
                        $r[] = Html::a("<i data-toggle='tooltip' data-placement='top' title='/{$m->redirect->url}'>" . $m->title . "</i>", ["menu/update", "id" => $m->id, 'return' => Usm::returnUrl()]);
                    }
                    return "<span class='label label-info'>" . count($data->menuItems) . "&times;</span> " . implode(", ", $r);
                }
            },
            'format' => 'html'
        ],
        [
            'header' => 'URLs',
            'value' => function($data) {
                if ($data->redirects == null) {
                    return "<i>None</i>";
                } else {
                    $r = [];
                    foreach ($data->redirects as $m) {
                        $r[] = Html::a("<i>/" . $m->url . "</i>", ["redirects/update", "id" => $m->id, 'return' => Usm::returnUrl()]);
                    }
                    return "<span class='label label-info'>" . count($data->redirects) . "&times;</span> " . implode(", ", $r);
                }
            },
            'format' => 'html'
        ],
        [
            'header' => 'Actions',
            'value' => function($data) {
                return "<div class='btn-group'>" .
                        Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', ['interpret/page', 'id' => $data->id], ['class' => 'btn btn-xs btn-default']) .
                        Html::a('<span class="glyphicon glyphicon-edit"></span> Edit', ['update', 'id' => $data->id], ['class' => 'btn btn-xs btn-primary']) .
                        Html::a('<span class="glyphicon glyphicon-remove"></span> Delete', ['delete', 'id' => $data->id], ['class' => 'btn btn-danger btn-xs button-delete']) .
                        "</div>";
            },
            'format' => 'html',
            'headerOptions' => [
                'style' => 'width:200px;text-align:right'
            ],
            'contentOptions' => [
                'style' => 'width:200px;text-align:right'
            ]
        ]
    ]
]);
?>

<script type="text/javascript">
    $(".button-delete").click(function () {
        return confirm('Are you sure to delete this page?');
    });
</script>