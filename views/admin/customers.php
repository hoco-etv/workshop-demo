<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Klushok';
$this->params['documentation'] = 'View all customers and their orders (click <span class="glyphicon glyphicon-expand"></span> to show the orders). Customers can only be deleted if they have no associated orders.'
?>
<div class="Admin-customers">


<?php

Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'panel' => ['type' => 'primary', 'heading' => 'Klushok Customers'],
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'pjaxSettings' => [
            'neverTimeout' => true,
            // 'beforeGrid'=>'My fancy content before.',
            // 'afterGrid'=>'My fancy content after.',
        ],
    ]);

    Pjax::end();
    ?>

</div>