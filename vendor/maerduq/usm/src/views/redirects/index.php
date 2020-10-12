<?php

use yii\helpers\Html;
use yii\grid\GridView;
use maerduq\usm\components\Usm;

$this->params['pageHeader'] = 'URLS';
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    'URLs'
];
?>

<?= Html::a('New URL', ['create'], ['class' => 'btn btn-primary']); ?>
<hr />
<?=
GridView::widget([
    'id' => 'banned-grid',
    'dataProvider' => $model->search(),
    'columns' => [
        [
            'header' => 'Active',
            'attribute' => 'active',
            'format' => 'html',
            'value' => function($data) {
                return ($data->active) ? "<span class='label label-success'>Active</span>" : "<span class='label label-default'>Inactive</span>";
            },
            'contentOptions' => [
                'style' => 'width:80px'
            ],
        ],
        [
            'header' => 'Redirect',
            'attribute' => 'forward',
            'format' => 'html',
            'value' => function($data) {
                return ($data->forward) ? "<span class='label label-warning'>Forward</span>" : "<span class='label label-success'>Endpoint</span>";
            },
            'contentOptions' => [
                'style' => 'width:80px'
            ],
        ],
        [
            'header' => 'Url',
            'format' => 'html',
            'value' => function($data) {
                return Html::a('/' . $data->url, '@web/' . $data->url);
            }
        ],
        [
            'header' => 'Destination',
            'attribute' => 'type',
            'format' => 'html',
            'value' => function($data) {
                switch ($data->type) {
                    case "cms":
                        return "Page " . Html::a($data->page->title, ['pages/update', 'id' => $data->destination, 'return' => Usm::returnUrl()]);
                    case "php":
                        return "Controller " . $data->destination;
                    case "menu_item":
                        if ($data->menuItem == null) {
                            return "Wrong menu item!";
                        }
                        return "Menu item " . Html::a($data->menuItem->title, ['menu/update', 'id' => $data->menu_item_id, 'return' => Usm::returnUrl()]);
                    case 'link':
                        return "Link: " . Html::a($data->destination, $data->destination, ['title' => $data->destination]);
                }
            }
                ],
                [
                    'header' => 'Actions',
                    'format' => 'html',
                    'headerOptions' => [
                        'style' => 'text-align:right'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align:right;width:140px'
                    ],
                    'value' => function($data) {
                if (!$data->generated) {
                    return "<div class='btn-group'>" .
                            Html::a('<span class="glyphicon glyphicon-edit"></span> Edit', ['update', 'id' => $data->id], ['class' => 'btn btn-xs btn-primary']) .
                            Html::a('<span class="glyphicon glyphicon-remove"></span> Delete', ['delete', 'id' => $data->id], ['class' => 'btn btn-xs btn-danger button-delete']) .
                            "</div>";
                } else {
                    return "<i>Generated</i>";
                }
            }
                ],
            ],
        ]);
        ?>
<script type="text/javascript">
    $(".button-delete").click(function () {
        return confirm('Sure to delete this?');
    });
</script>