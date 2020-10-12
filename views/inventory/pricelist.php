<?php

/* @var $this yii\web\View */

use app\controllers\InventoryController;


$this->title = 'Klushok';
?>
<style>
    /* @font-face {
    font-family: 'BEBAS';
    src: url('@web/fonts/BEBAS.ttf') format('truetype');
    font-weight: normal;
    font-weight: normal;
} */

    #pricelist-container h1 {

        font-family: 'BEBAS';
        /* src: url('../fonts/BEBAS.ttf') format("truetype"); */
        width: 100%;
        text-align: center;
        font-size: 60px;
        /* font-weight: normal; */
        color: #000;
        border-bottom: 10px solid #000000;
        /* height: 0.65em; */
        line-height: 0;
        margin: 50px auto 20px;
    }

    #pricelist-container h1 span {
        background: #fff;
        padding: 0 12px;
    }

    #pricelist-container .title {
        text-align: center;
    }

    #pricelist-container .column {
        float: left;
        padding: 10px;
        font-family: Arial, Helvetica, sans-serif;
    }

    #pricelist-container .left {
        width: 44%;
        margin-right: 3%;
    }

    #pricelist-container .right {
        width: 44%;
        margin-left: 3%;
    }

    #pricelist-container .header {
        display: table;
        font-weight: bold;
        margin-top: 1.5em;
        width: 100%;
    }

    #pricelist-container .item-container {
        display: table-row;
    }

    #pricelist-container .item {
        font-weight: normal;
        display: table-cell;
        width: 50%;
        white-space: nowrap;
    }

    #pricelist-container .dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 5px;
        position: relative;
    }
</style>



<!-- TODO: omschrijven om table te gebruiken, 1 table per category. -->
<section id="pricelist-container">
    <h1><span>PRICELIST</span></h1>
    <?php 
    //echo (InventoryController::getPricelist(false));  //temporary disabled
    ?>
    <div style="clear:both"></div> <!-- removes the fload property of the colums so the border wraps around them -->
</section>

<!-- <button onclick="window.print(this);window.close()"> get state </button>

<script>
console.log(document.readyState)
window.print();
if (document.readyState == 'loading') {
    document.addEventListener('DOMContentLoaded', ready)
    console.log('if loading')
} else {
    ready()
}

function ready()
{   
    console.log('printen maar')
    HTMLDocument.print();
}

function getState()
{
    console.log(document.readyState)
}

</script> -->