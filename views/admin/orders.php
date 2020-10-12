<?php

/* @var $this yii\web\View */

use app\models\Customer;
use app\models\Order_details;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Klushok';
$this->params['documentation'] = $this->render('_orders_docs');
?>
<div class="Admin-orders">
    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'panel' => ['type' => 'primary', 'heading' => 'Klushok Orders'],
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
    ]);
    Pjax::end();
    ?>
</div>

<script>
    const toCopy = [];
    <?php $model = new Order_details;?>
    toCopy['RS'] = <?php echo json_encode($model->getOrderDetailsByStore('RS')); ?>;
    toCopy['Farnell'] = <?php echo json_encode($model->getOrderDetailsByStore('Farnell')); ?>;

    const url = {
        'RS': 'https://nl.rs-online.com/web/ca/overzichtwinkelwagen/',
        'Farnell': 'https://nl.farnell.com/quick-order?isQuickPaste=true&quickPaste=',
    };

    function getIndex(order_id, store) {
        var indices = [];
        for (var i = 0; i < toCopy[store].length; i++) {
            if (toCopy[store][i]['order_id'] == order_id) {
                indices.push(i);
            }

        }
        return indices;
    }

    function getString(indices, store) {
        var string = '';
        for (var i = 0; i < indices.length; i++) {
            string += toCopy[store][indices[i]]['part_no'];
            string += ', ';
            string += toCopy[store][indices[i]]['quantity'];
            string += "\n";
        }
        return string;
    }

    function copyToClip(order_id, store) {
        var copyEl = document.createElement('textarea');

        var value = getString(getIndex(order_id, store), store);
        copyEl.value = value;
        copyEl.setAttribute('readonly', '');

        document.body.appendChild(copyEl);
        copyEl.select();
        document.execCommand('copy');
        document.body.removeChild(copyEl);

        if (store == 'Farnell') {
            window.open(url[store] + encodeURI(value));
        } else {
            window.open(url[store]);
        }
    }
</script>