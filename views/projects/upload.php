<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\Project;

$this->title = 'Klushok';
?>
<div class="project-upload">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) :
        if ($key == 'error') {
            $key = 'danger';
        }
    ?>
        <?= '<div class="alert alert-' . $key . '">' . $message . "</div>\n" ?>
    <?php endforeach; ?>


    <!-- <a class="btn btn-primary" onclick="$(this).remove();$('#new-file').slideDown()">New file</a> -->
    <div class="panel panel-default" id="new-file" style="display:visible">
        <div class="panel-heading">
            <h4 class="panel-title">Create new project</h4>
        </div>
        <div class="panel-body">

            <?= $this->render('_uploadForm', ['model' => new Project]); ?>
        </div>
    </div>
    <hr />

</div>