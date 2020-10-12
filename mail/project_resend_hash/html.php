<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/projects/confirm','h' => $project->hash],true);

?>

<p>Dear <?= $project->author ?>,</p>

<p>This email contains a new unique access link for your project: "<?=$project->title?>" at <?=$_SERVER['HTTP_HOST'] ?>.</p>

<!-- insert tabel met order details -->
<p>To view your project please visit <?=Html::a($url,$url)?></p>

<p>If you did not request this link, please ignore this email.</p>