<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/order/confirm','h' => $hash],true);

?>

Dear <?= $customer->name ?>,

This email has been send to you to confirm your order at <?= $_SERVER['HTTP_HOST']?>.

<!-- insert tabel met order details -->
To confirm your order and check its status or future action points please visit <?=$url?>

If you did not place this order, please ignore this email.