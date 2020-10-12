<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;
use maerduq\usm\assets\AdminAsset;
use yii\bootstrap\ActiveForm;

AdminAsset::register($this);

$this->params['pageHeader'] = 'Login';
$this->params['breadcrumbs'] = array(
    'Admin Panel',
    'Login'
);
$this->params['documentation'] = 'Please log in to start managing your site';

$this->registerJs("$('[data-toggle=\"tooltip\"]').tooltip();$('[data-toggle=\"popover\"]').tooltip()", \yii\web\View::POS_READY);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="language" content="en">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css' />
        <link rel="SHORTCUT ICON" href="<?= Url::to('@app/modules/usm') ?>/img/application_view_tile.png" />

        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title); ?></title>
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
                    'class' => 'navbar-default navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Go to site', 'url' => (Url::base() == '') ? '/' : Url::base()]
                ]
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
                $form = ActiveForm::begin(['layout' => 'horizontal'])
                ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class='push'></div>
        </main>
        <footer>
            <div class='container' style="text-align:center;line-height:100px;color:#aaa;">
                Ultimate Site Management is a product of <a href="http://www.depaul.nl" target="_blank">dePaul Programming</a> | &copy 2013
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>