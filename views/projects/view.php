<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\Project;
use kartik\markdown\Markdown;


$this->title = 'Klushok';
$this->registerCssFile('@web/css/project_view.css');


?>
<div id="project-view">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) :
        if ($key == 'error') {
            $key = 'danger';
        } ?>
        <?= '<div class="alert alert-' . $key . '">' . $message . "</div>\n" ?>
    <?php endforeach; ?>

    <div class="title">
        <h1><?= $model->title ?></h1>
        <h4><?= $model->subtitle ?></h4>
        <?= $model->show_cover($isAuthor) ?><br>
        <h5>Created by: <?= $model->author ?>, <?= substr($model->created_at, 0, 10) ?></h5><br><br>
    </div>
    <div class="content">

        <?= Markdown::convert($model->show_content($isAuthor)) ?>

    </div>
</div>
<div id=modal-root>
    <div id="myModal" class="modal_img">
        <span class="close_modal">&times;</span>
        <img class="modal-content" id="img01">
    </div>
</div>

<script>
    if (document.readyState == 'loading') {
        document.addEventListener('DOMContentLoaded', ready)
    } else {
        ready()
    }

    function ready() {
        // get modal and content
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");

        // get the span to close the modal
        var span = document.getElementsByClassName("close_modal")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // add onclick attributes to all images
        var imgs = document.getElementsByClassName('image');
        for (var i = 0; i < imgs.length; i++) {
            var img = imgs[i];
            img.onclick = function() { // set onclick element of image
                modal.style.display = "block";
                modalImg.src = this.src;
            }
        }

        // press escape to close the modal
        document.addEventListener("keydown", function(event) {
            if (event.keyCode == 27) {
                modal.style.display = "none";
            }
        })

        // close modal when clicking outside of the image
        var modalRoot = document.getElementById('modal-root');
        modalRoot.addEventListener('click', function(event) {
            if (event.target.id !== modalImg.id) {
                modal.style.display = 'none';
            }
        })
    }
</script>