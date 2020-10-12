<?php

use yii\helpers\Url;

$url = Url::to(['/admin/projects?ProjectSearch%5Bid%5D=' . $model->id], true);
?>


Ha beunhazen!

Het project '<?= $model->title ?>' is zojuist bevestigd.
Stel een reviewer in check het project via deze link: <?= $url ?>


Klusjes en een dikke lebber van de site!