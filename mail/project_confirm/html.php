<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/projects/confirm','h' => $project->hash],true);

?>

<p>Dear <?= $project->author ?>,</p>

<p>This email has been send to you to confirm your project "<?=$project->title?>" at <?=$_SERVER['HTTP_HOST'] ?>.</p>

<!-- insert tabel met order details -->
<p>To view and confirm your project please visit <?=Html::a($url,$url)?></p>

<p>If you did not create this project, please ignore this email.</p>