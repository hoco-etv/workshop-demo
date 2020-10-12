<?php

/* @var $this yii\web\View */

use app\models\Customer;
use app\models\OrderSearch;
use app\models\Order_details;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
// use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Klushok';
$this->params['documentation'] = $this->render('_projects_docs');


?>
<div class="Admin-projects">


<?php

Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'responsive' => false,
        'panel' => ['type' => 'primary', 'heading' => 'Klushok Projects'],
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