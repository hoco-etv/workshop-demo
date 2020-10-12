<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;

$this->title = 'Klushok';
?>
<div class="Admin-devices">
    <?php
    $id = $_GET['id'];
    $img = $_GET['img'];
    ActiveForm::begin([
        'id' => 'imageSelect',
        'method' => 'post',
        'action' => 'select_image'
    ]);
    ?>
    <p>These are images uploaded to the 'devices' category using the <a href='/usm/files/index'>files</a> option. Click on an image to select it. Submit to confirm your choice or press 'Back' to cancel any changes.</p>
    <p>Alternatively you can click the wrench icon next to a project to add refference to an external image.</p>
    <div style='text-align:center'>
        <?php foreach ($imageNames as $imageName) : ?>
            <?= Html::img('/file/devices/' . $imageName, [
                'style' => [
                    'max-width' => '200px',
                    'max-height' => '200px',
                    'margin' => '0 10px',
                ],
                'class' => 'device-img-listed',
                'id'    =>  $imageName
            ]) ?>
        <?php endforeach; ?>
    </div>

    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" id="selectedImage" name="imageName" value="">

    <div style="text-align:center;margin-top:30px">
        <input type="submit" class="btn btn-success" value="Submit">
        <button class="btn btn-link" data-dismiss="modal">Back</span></button>
    </div>

    <?php ActiveForm::end(); ?>
    <span id='oldImgName' style="display: none"><?= $img ?></span>
</div>

<script>
    if (document.readyState == 'loading') {
        document.addEventListener('DOMContentLoaded', ready)
    } else {
        ready()
    }

    function ready() {
        // get images
        var images = document.getElementsByClassName("device-img-listed");
        var returnField = document.getElementById('selectedImage');

        // get current image and border style
        const oldImgName = document.getElementById('oldImgName').innerText;
        const borderLayout = '4px solid #17d425';

        // add event listeners and define initial selected image
        for (var i = 0; i < images.length; i++) {
            var image = images[i];
            image.addEventListener('click', function(event) {
                for (var j = 0; j < images.length; j++) {
                    images[j].style.border = 'none';
                }
                event.target.style.border = borderLayout;
                returnField.value = event.target.id;
                console.log(event.target.id);
            })

            // define and highlight initial selected image
            if (oldImgName != null && image.id == oldImgName) {
                image.style.border = borderLayout;
                returnField.value = oldImgName;
            }
        }
    }
</script>