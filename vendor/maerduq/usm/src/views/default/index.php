<?php

use yii\helpers\Url;

$this->params['pageHeader'] = "<h1>" . \Yii::$app->name . " <br /><small>Admin panel</small></h1>";
$this->params['documentation'] = "<p>This is your admin panel! In here you can edit the content of your website.</p>";
$this->params['breadcrumbs'] = [
    "Admin Panel",
];

$buttons = [
    ['Menu admin', 'glyphicon-tasks', ['/usm/menu/admin']],
    ['Pages', 'glyphicon-edit', ['/usm/pages/admin']],
    ['Textblocks', 'glyphicon-text-width', ['/usm/textblocks/admin']],
    ['URLs', 'glyphicon-transfer', ['/usm/redirects/index']],
]
?>

<div class="row buttons">
    <?php foreach ($buttons as $button): ?>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="<?= Url::to($button[2]) ?>" class="thumbnail">
                <div>
                    <div>
                        <div>
                            <span class="glyphicon <?= $button[1] ?>"></span>
                            <?= $button[0] ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<style type="text/css">
    .buttons .thumbnail {
        width: 100%;
        text-align: center;
        padding-top: 90%;
        font-size: 20pt;
    }

    .thumbnail > div {
        position: absolute;
        top: 0;
        bottom: 20px;
        left: 15px;
        right: 15px;
    }

    .thumbnail > div > div {
        display: table;
        width: 100%;
        height: 100%;
    }

    .thumbnail > div > div > div {
        display: table-cell;
        width: 100%;
        vertical-align: middle;
    }

    .buttons a:hover {
        text-decoration: none;
        background: #337ab7;
        color: #fff;
    }

    .buttons .glyphicon {
        font-size: 80px;
        display: block;
        margin-bottom: 15px;
    }
</style>