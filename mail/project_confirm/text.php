<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/projects/confirm','h' => $project->hash],true);

?>

Dear <?= $project->author ?>,

This email has been send to you to confirm your project "<?=$project->title?>" at <?= $_SERVER['HTTP_HOST']?>.

To view and confirm your project please visit <?=$url?>

If you did not create this project, please ignore this email.