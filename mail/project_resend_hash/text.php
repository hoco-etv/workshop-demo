<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/projects/confirm','h' => $project->hash],true);

?>

Dear <?= $project->author ?>,

This email contains a new unique access link for your project: "<?=$project->title?>" at <?= $_SERVER['HTTP_HOST']?>.

To view your project please visit <?=$url?>

If you did not create this link, please ignore this email.