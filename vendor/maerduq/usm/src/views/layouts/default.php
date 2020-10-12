<?php

use maerduq\usm\components\Usm;
use maerduq\usm\assets\AdminAsset;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!doctype HTML>
<html>
    <head>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head() ?>
    </head>
    <body style="margin-top:70px">
        <?php $this->beginBody() ?>
        <?php
        NavBar::begin([
            'brandUrl' => null,
            'options' => [
                'class' => 'navbar-fixed-top navbar-inverse'
            ]
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => Usm::getMenu(),
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <h1><?= Yii::$app->name; ?></h1>
            <?=
            Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ])
            ?>

            <hr />
            <?php
            foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                if ($key == 'error') {
                    $key = 'danger';
                }
                echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
            }
            ?>
            <h2><?= isset($this->params['pageHeader']) ? $this->params['pageHeader'] : "" ?></h2>
            <?= $content ?>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>