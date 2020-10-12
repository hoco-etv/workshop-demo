<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/projects/view', 'id' => $project->id], true);
$imgUrl = Url::to(["/img/project/approved.gif"], true);

?>

<p>Dear <?= $project->author ?>,</p>

<h3>Congratulations!</h3>
<img src=<?= $imgUrl ?>>
<p> your Project at <?= $_SERVER['HTTP_HOST'] ?> has been approved!.</p>

<!-- insert tabel met order details -->
<p>Your project is now publicly available at <?= Html::a($url, $url) ?></p>