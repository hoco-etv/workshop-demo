<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/admin/projects?ProjectSearch%5Bid%5D=' . $model->id], true);
?>


<p>Ha beunhazen!</p>

<p>Het project '<?= $model->title ?>' is zojuist bevestigd.</p>
<p>Stel een reviewer in check het project via deze link: <?= Html::a($url, $url) ?></p>


<p>Klusjes en een dikke lebber van de site!</p>