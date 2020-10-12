<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/projects/view','id' => $project->id],true);

?>

Dear <?= $project->author ?>,

Congratulations!
<img src="/img/project/approved.gif">
your Project at <?=$_SERVER['HTTP_HOST'] ?> has been approved!.

<!-- insert tabel met order details -->
Your project is now publicly available at <?=Html::a($url,$url)?>