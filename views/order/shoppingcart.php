<?php

/* @var $this yii\web\View */
// $this->title = 'Shoppingcart';
// $this->registerCssFile('css/cart.css');
?>

<div class="cart-container panel panel-default">
    <div class="panel-heading">
        <div class="cart-title">
            SHOPPING CART
        </div>
    </div>
    <div class="panel-body">
        <div class="cart-items">
            <div class="cart-item">
                <div class="item-image">
                    <img src="/img/stores/RS-logo.png">
                    <!-- afbeelding van de store -->
                </div>
                <div class="item-info">
                    <p>Product Number</p>
                    <p>Optional Description</p>
                    <!-- product number and description -->
                </div>
                <div class="item-actions">
                    <!-- <div class="item-delete">
                        <a class="glyphicon glyphicon-trash"></a>
                    </div> -->
                    <div class="item-quantity-container">
                        <a class="glyphicon glyphicon-plus-sign"></a>
                        <span class="item-quantity">
                            <!-- quantity -->
                            7
                        </span>
                        <a class="glyphicon glyphicon-minus-sign"></a>
                    </div>
                </div>
                <div class="item-delete">
                    <a data-toggle="tooltip" title="Delete" class="glyphicon glyphicon-trash"></a>
                </div>
            </div>

            <div class="cart-item">
                <div class="item-image">
                    <img src="/img/stores/farnell-logo.png">
                    <!-- afbeelding van de store -->
                </div>
                <div class="item-info">
                    <p>Product Number</p>
                    <p>Optional Description</p>
                    <!-- product number and description -->
                </div>
                <div class="item-actions">
                    <!-- <div class="item-delete">
                        <a class="glyphicon glyphicon-trash"></a>
                    </div> -->
                    <div class="item-quantity-container">
                        <a class="glyphicon glyphicon-plus-sign"></a>
                        <span class="item-quantity">
                            <!-- quantity -->
                            7
                        </span>
                        <a class="glyphicon glyphicon-minus-sign"></a>
                    </div>
                </div>
                <div class="item-delete">
                    <a data-toggle="tooltip" title="Delete" class="glyphicon glyphicon-trash"></a>
                </div>
            </div>
        </div>
    </div>
</div>