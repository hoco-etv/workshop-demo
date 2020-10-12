<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->params['pageHeader'] = 'Textblocks';
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    'Textblocks'
];
$this->params['documentation'] = $this->render('_docs');
?>

<?= Html::a('New Textblock', ['create'], ['class' => 'btn btn-primary']); ?>
<hr />
<?=
GridView::widget([
    'id' => 'textblock-grid',
    'dataProvider' => $model->search(),
    'emptyText' => 'No textblocks defined yet.',
    'columns' => [
        'name',
        [
            'attribute' => 'text',
            'format' => 'html',
            'value' => function ($data) {
                return strip_tags($data->text);
            },
            'contentOptions' => [
                'class' => 'td-text'
            ]
        ],
        [
            'attribute' => 'description',
            'value' => function($data) {
                return ($data->description == '') ? "No description" : $data->description;
            },
            'contentOptions' => [
                'style' => 'min-width:120px'
            ]
        ],
        [
            'value' => function($data) {
                return "<div class='btn-group'>" .
                    Html::a('<span class="glyphicon glyphicon-edit"></span> Edit', ['update', 'id' => $data->id], ['class' => 'btn btn-xs btn-primary']) .
                    Html::a('<span class="glyphicon glyphicon-remove"></span> Delete', ['delete', 'id' => $data->id], ['class' => 'btn btn-danger btn-xs button-delete']) .
                    "</div>";
            },
                'format' => 'html',
                'contentOptions' => [
                    'style' => 'width:140px;text-align:right'
                ]
            ]
        ],
    ]);
    ?>
<script type="text/javascript">
    $(".button-delete").click(function () {
        return confirm('Sure to delete this?');
    });

    $("#textblock-grid .td-text a").each(function () {
        $(this).removeAttr('href');
    });
</script>