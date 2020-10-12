<?php

/* @var $this yii\web\View */

use app\models\Customer;
use app\models\Order_details;
use maerduq\usm\models\Textblock;
use yii\bootstrap\ActiveForm;

$this->title = 'Klushok';

// TODO
// - add small shopping cart icon to the menu or add badge to 'Order'


?>
<script src="js/shoppingcart.js"></script>
<div class="site-order">
  <h1>Order Components</h1>
  <div class="order-text">
    <?= Textblock::read('Order_heading') ?>
  </div>

  <div class="add-item">
    <ul class="nav nav-tabs" style="border-bottom: none">
      <li role="presentation" class="">
        <a href="#bulk" onclick="$(this).tab('show');event.preventDefault();">Bulk add</a>
      </li>
      <li role="presentation" class="active">
        <a href="#individual" onclick="$(this).tab('show');event.preventDefault();">Add individually</a>
      </li>
    </ul>

    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="individual">
        <div class="panel panel-default">
          <div class="panel-body">
            <?php
            $form = ActiveForm::begin([
              'id' => 'add-component-form',
              'layout' => 'horizontal',
            ]);

            $model = new Order_details();
            ?>
            <?= $form->field($model, 'store')->dropDownList(
              [
                0 => 'RS-Components',
                1 => 'Farnell'
              ],
              [
                'prompt' =>
                [
                  'text' => 'Select store',
                  'options' => [
                    'selected' => true,
                    'disabled' => true
                  ]
                ]
              ]
            )->hint('please select the store you\'d like to order the component from') ?>
            <?= $form->field($model, 'part_no')->textInput(['type' => 'number'])->hint('Part number as can be found on the respecitve store\'s website. In case of Farnell this is the <b>Ordercode</b>, in case of RS this is the <b>RS-stocknr</b>. Please leave out any dashes or spaces') ?>
            <?= $form->field($model, 'description')->hint('(Optional) A max 100 character description of the component to help identify it quickly') ?>
            <?= $form->field($model, 'quantity')->textInput(['type' => 'number'])->hint('The quantity of the components you\'d like to order. Please enter a multiple of the minimum order amount as found on the store\'s site. ') ?>
            <?php ActiveForm::end() ?>

            <div class="row">
              <div class="col-sm-offset-3 col-sm-6">
                <div class="cart_input_error"></div>
                <button class="btn btn-primary add-item-button" type="button" style="margin:10px;">Add to cart</button>
              </div>
              <div class="col-sm-3 component-added">
                <div class="fadeOut" style="display:none">
                  <span style="font-size:14px;margin-right:30px">Item added to cart</span>
                  <span style="font-size:33pt;" class="glyphicon glyphicon-ok"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="bulk">
        <div class="panel panel-default" style="border-top-left-radius: 0px">
          <div class="panel-body">
            Enter the products you'd like to add to your shopping cart in the following format: <br>
            <b>Store, part number, quantity, description</b><br>
            The current options for stores are 'rs' or 'farnell'. If an item is already present in your cart, the quantity will be increased.<br>
            If a line contains too few arguments, it will be skipped.
            <div class="bulkInputField">
              <textarea rows="4" cols="50" name="comment" form="usrform" class="form-control" placeholder="store, part number, quantity, description"></textarea>
            </div>
            <div class="bulk_error_field"></div>
            <button class="btn btn-primary bulk-add-item-button" type="button" style="margin:10px;" onclick="bulkAddClicked()">Add to cart</button>
            <div class="col-sm-3 component-added">
                <div class="fadeOut" style="display:none">
                  <span style="font-size:14px;margin-right:30px">Item added to cart</span>
                  <span style="font-size:33pt;" class="glyphicon glyphicon-ok"></span>
                </div>
              </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="shoppingcart" class="cart-container panel panel-default">
    <div class="panel-heading">
      <div class="cart-title">
        SHOPPING CART
      </div>
    </div>
    <div class="panel-body">
      <div class="cart-items">
      </div>
      <div class="row">
        <div class="btn-next">
          <button class="btn btn-primary" type="button" onclick="
          $('#customer-information').slideDown();
          $('html, body').animate({scrollTop: $('#customer-information').offset().top }, 800);">Next</button>
        </div>
      </div>
    </div>
  </div>

  <div id="customer-information" class="panel panel-default" style="display: none">
    <div class="row">
      <div class="col-sm-offset-1 col-sm-10" style="margin-bottom: 40px">
        Please verify the products in your shopping cart. If correct, please insert your email, name and optionally your student number below and press 'order'.
      </div>
    </div>

    <?php
    $form = ActiveForm::begin([
      'id' => 'customer-info-form',
      'layout' => 'horizontal',
    ]);

    $model = new Customer;
    ?>

    <?= $form->field($model, 'email')->label('Your TU delft email address')->hint('Your email address will be used to confirm your order and contact you in case of any questions') ?>
    <?= $form->field($model, 'name')->hint('We\'d like to know who placed the order :) ') ?>
    <?= $form->field($model, 'student_no')->label('Student number (optional)')->textInput(['type' => 'number']) ?>

    <?php ActiveForm::end(); ?>

    <div id="cart_response"></div>
    <div class="row">
      <div class="col-sm-offset-3 col-sm-6">
        <button class="btn btn-primary btn-purchase" type="button">ORDER</button>
        <button class="btn btn-link" type="button" onclick="
          $('.order-error-field').empty();
          $('#customer-information').slideUp();
          $('.btn-next :first').css('display','unset');
          $('.item-quantity-container > a').css('display','unset');
          $('.item-delete').css('display','unset');
          ">Back
        </button>
      </div>
      <div class="col-sm-offset-1 col-sm-10 order-error-field">
      </div>
    </div>
  </div>
</div>