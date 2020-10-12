<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/order/confirm','h' => $model->hash],true);

?>

Dear <?= $model->customer->name ?>,


Your order at <?=$_SERVER['HTTP_HOST'] ?> has arrived!
<img src="/img/order/arrived.gif">
<!-- insert tabel met order details -->
To check the status of your order, please visit <?=$url?>

If you did not place this order, please ignore this email.