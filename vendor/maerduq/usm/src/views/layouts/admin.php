<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use maerduq\usm\assets\AdminAsset;

$asset = AdminAsset::register($this);
$usmModule = Yii::$app->getModule('usm');

$this->registerJs("$('[data-toggle=\"tooltip\"]').tooltip();$('[data-toggle=\"popover\"]').tooltip()", \yii\web\View::POS_READY);

if (!isset($this->params['pageHeader'])) {
    $this->params['pageHeader'] = $this->title;
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="language" content="en">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <link rel="SHORTCUT ICON" href="<?= $asset->baseUrl ?>/img/application_view_tile.png">

        <?= Html::csrfMetaTags() ?>
        <title><?= strip_tags($this->params['pageHeader']) . ' | Admin Panel | ' . Yii::$app->name; ?></title>
        <?php $this->head() ?>
    </head>

    <body>
        <?php $this->beginBody() ?>
        <main>
            <?php
            NavBar::begin([
                'brandLabel' => 'Admin Panel',
                'brandUrl' => ['/usm'],
                'options' => [
                    'class' => 'navbar-default',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'encodeLabels' => false,
                'items' => Yii::$app->getModule('usm')->getMenu(),
            ]);

            $itemsRight = [
                ['label' => 'Go to site', 'url' => (Url::base() == '') ? '/' : Url::base()],
                ['label' => 'Log out', 'url' => ['/usm/global/logout']]
            ];
            if (count($usmModule->languages) > 1) {
                $langChooserItems = [];
                foreach ($usmModule->languages AS $lang) {
                    if ($lang == Yii::$app->language) {
                        continue;
                    }

                    $langChooserItems[] = [
                        'label' => $lang,
                        'url' => Url::current(['lang' => $lang])
                    ];
                }
                $itemsRight = array_merge([[
                    'label' => 'Lang: ' . Yii::$app->language,
                    'items' => $langChooserItems,
                    ]], $itemsRight);
            }

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $itemsRight
            ]);
            NavBar::end();
            ?>
            <div class="container">
                <?php if (isset($this->params['pageHeader'])): ?>
                    <div class="page-header" style="margin-top:0"><?= (substr($this->params['pageHeader'], 0, 1) != "<") ? "<h1>{$this->params['pageHeader']}</h1>" : $this->params['pageHeader'] ?></div>
                <?php endif; ?>
            </div>
            <section class="grey">
                <div class="container">
                    <?=
                    Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'homeLink' => false
                    ])
                    ?>
                    <?php if (isset($this->params['documentation'])): ?>
                        <div class="documentation">
                            <?= $this->params['documentation']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <div class="container">
                <?php
                foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                    if ($key == 'error') {
                        $key = 'danger';
                    }
                    echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
                }
                ?>
                <?= $content; ?>
            </div>
            <div class='push'></div>
        </main>
        <footer>
            <div class='container' style="text-align:center;line-height:100px;color:#aaa;">
                Ultimate Site Management is a product of <a href="http://www.depaul.nl" target="_blank">dePaul Programming</a> | &copy 2013 - <?= date('Y'); ?>
            </div>
        </footer>
        <script>
            $(document).ready(function () {
                $('.dropdown-toggle').dropdown()
            });
        </script>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>