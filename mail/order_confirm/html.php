<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/order/confirm', 'h' => $hash], true);
?>

<style>
    #order {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #order td,
    #order th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #order tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #order tr:hover {
        background-color: #ddd;
    }

    #order th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        /* background-color: #4CAF50; */
        background-color: #B41F21;
        color: white;
    }
    
    .description{
        word-break: break-all;
    }
</style>

<p>Dear <?= $customer->name ?>,</p>

<p>This email has been send to you to confirm your order at <?= $_SERVER['HTTP_HOST'] ?>.</p>

<!-- insert tabel met order details -->
<p>To confirm your order and check its status or future action points please visit <?= Html::a($url, $url) ?></p>

<table id="order">
    <tr>
        <th>Store</th>
        <th>Part number</th>
        <th>Quantity</th>
        <th>Description</th>
    </tr>
    <?php
    foreach ($customer->order as $order) :  // returns all orders placed by this customer
        if ($order['hash'] === $hash) :
            foreach ($order->order_details as $detail) :
    ?>
                <tr>
                    <td><?= $detail->store ?></td>
                    <td><?= $detail->part_no ?></td>
                    <td><?= $detail->quantity ?></td>
                    <td class="description"><?= $detail->description ?></td>
                </tr>
    <?php
            endforeach;
        endif;
    endforeach; ?>

</table>

<p>If you did not place this order, please ignore this email.</p>

<p>Regards,<br>
The klushok committee</p>


