<?php

// use maerduq\usm\components\Usm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;
use kartik\markdown\MarkdownEditor;
?>


<div style="text-align:center">
    <h3>Please confirm you project</h3>
    <br>
    <div style='text-align:left'>
        We ask you to confirm your project so we know the email address you provided is a valid one.
        This email address will be used to update you on the status of this project and contact you in case of any questions.<br><br>
        <span style="font-size:large"><span class="glyphicon glyphicon-warning-sign"></span><b> WARNING</b></span><br>
        <b>The link you received in your email functions as a unique access code to this project. </b><br>
        Until it has been approved by our admins, only someone with this unique access code is able to view the project.<br>
        Althoug it might sound tempting to share this link with your friends, please be carefull when doing so.

        <b>Anyone with this link can edit your project!</b><br><br>
        Please contact <a href="mailto:klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a> if you've lost your access code.

    </div>
    <?php
    $form = ActiveForm::begin([
        'options' => [
            'enctype' => "multipart/form-data",
        ],
    ]);
    ?>

    <?= $form->field($model, 'confirmed')->hiddenInput()->label(false) ?>

    <input type="submit" value="Confirm Project" class="btn btn-primary" />
    <?php ActiveForm::end(); ?>
    <br>
</div>