<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\controllers\AdminController;
use maerduq\usm\components\Usm;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href='/img/favicon-144x144.png'>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
        NavBar::begin([
            'brandLabel' => Html::img('@web/img/etv-logo-flag.png', ['alt' => Yii::$app->name, 'class' => "logo_flag"]),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'id' => 'klushok-navbar'
            ],
        ]);

        if (Yii::$app->user->isGuest) {
            $isGuest = false;
            $username = '';
        } else {
            $isGuest = true;
            $username = Yii::$app->user->identity->username;
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => Usm::getMenu()
        ]);
        NavBar::end();
        ?>

        <div class="klushok-logo">
            <a href="/">
                <img src="/img/klushok-logo.png" alt="supermooi">
            </a>
        </div>


        <div class="container">

            <?= $content ?>
        </div>

    </div>




    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Klushok <?= date('Y') ?></p>

            <!-- <p class="pull-right"><?= Yii::powered() ?></p> -->
            <p class="pull-right"><a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">Goed gebeund is niet lelijk</a></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
$('a[href*="http"]').attr('target','_blank'); // change external links to open in new tab
</script>

</html>
<?php $this->endPage() ?>