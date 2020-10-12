<?php

use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'Klushok';
$this->registerCssFile('@web/css/order_status.css');




if ($isRetrieved && $isPaid) { // order retrieved 
    $lastAction = 'been retrieved';
    $todo = 'Your order has been completed!';
} elseif($isArrived && $isPaid){    // order arrived at ETV desk
    $lastAction = 'arrived';
    $todo = 'Your order has arrived at the ETV desk, come by to retrieve it!';
} elseif ($isOrdered && $isPaid) {
    $lastAction = 'been ordered at the supplier(s)';
    $todo = 'Your order is on its way to the ETV!';
} elseif ($isPaid) {
    $lastAction = 'been paid';
    $todo = 'Your order will soon be placed at the suppliers, check this page to stay up to date!';
} else {
    $lastAction = 'been confirmed';
    $todo = 'Please pay for your order at the ETV desk.';
}

?>

<div class="order-confirm" style="text-align: center">
    <h1>Your order has <?= $lastAction ?>!</h1>
    <h4><?= $todo ?></h4>
    <br><br>
    <p style="margin-bottom:50px"><?= Html::img('/img/order/order.gif', ['class' => 'order-img']) ?></p>
    <div class='step-container'>
        <ul class='progressbar'>
            <li <?= $isConfirmed ? "class='active'" : '' ?>>Order confirmed</li>
            <li <?= $isPaid ? "class='active'" : '' ?>>Order paid</li>
            <li <?= $isOrdered ? "class='active'" : '' ?>>Ordered at supplier</li>
            <li <?= $isArrived ? "class='active'" : '' ?>> Order arrived </li>
            <li <?= $isRetrieved ? "class='active'" : '' ?>>Order retrieved</li>
        </ul>
    </div>
</div>