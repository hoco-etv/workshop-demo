<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\controllers\InventoryController;
use kartik\form\activeForm;
use app\models\Inventory;
use yii\helpers\Console;
use maerduq\usm\models\Textblock;

$this->title = 'Klushok';

// TODO:
// - print optie voor de pricelist toevoegen
// - pricelist omschrijven naar table layout

?>



<div>

    <?= Textblock::read('pricelist-heading') ?>
   
</div>
<section id="pricelist-container">
    <h1><span>pricelist</span></h1>
    <?=$pricelist?>
    <div style="clear:both"></div> <!-- removes the fload property of the colums so the border wraps around them -->
</section>
</div>