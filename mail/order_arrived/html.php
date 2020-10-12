<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/order/confirm','h' => $model->hash],true);
$imgUrl = Url::to(['/img/order/arrived.gif'], true);
?>

<p>Dear <?= $model->customer->name ?>,</p>

<p>Your order at <?=$_SERVER['HTTP_HOST'] ?> has arrived!</p>
<img src='<?= $imgUrl ?>'>
<!-- insert tabel met order details -->
<p>To check the status of your order, please visit <?=Html::a($url,$url)?></p>

<p>If you did not place this order, please ignore this email.</p>