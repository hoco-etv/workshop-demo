<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use app\models\Device;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

$this->title = 'klushok | Devices';
$this->registerCssFile('css/devices.css');

?>
<div class="site-devices">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        if ($key == 'error') {
            $key = 'danger';
        }
        echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
    }
    ?>



    <h1>Devices</h1>

    <?php
    $model = new Device();
    if (isset(Yii::$app->request->queryParams['Device'])) {
        $model->brand = $_GET['Device']['brand'];
        $model->name = $_GET['Device']['name'];
        $model->type = $_GET['Device']['type'];
    }

    $form = ActiveForm::begin([
        'id' => 'Device-broken-form',
        'layout' => 'horizontal',
        'method' => 'get',
        'action' => ['/devices'],

    ]);
    ?>

    <?= $form->field($model, 'brand')->dropDownList(
        array_combine(Device::getDistict('brand'), Device::getDistict('brand')),
        [
            'prompt' => Device::getDistict('brand') ? 'Select...' : 'No options available',
        ]
    )->hint('Brand or manufacturer of the device (e.g. \'RS PRO\').') ?>
    <?= $form->field($model, 'name')->hint('Name of the device (e.g. \'Soldering station\'), leave empty if unknown.') ?>
    <?= $form->field($model, 'type')->hint('Type or series number of the device, leave empty if unknown.') ?>

    <div style="text-align:center">
        <input type="submit" class="btn btn-success" value="Search">
        <a class="btn btn-link" href='devices'>Clear</span></a>
    </div>
    <?php ActiveForm::end(); ?>
    <div class='devices-container'>
        <div class='search-filters'>

            <?php
            $modelName = 'Device';
            $viewName = 'devices';
            if (isset(Yii::$app->request->queryParams[$modelName])) {
                $params = Yii::$app->request->queryParams[$modelName];

                $filterTagsHtml = '';
                foreach ($params as $key => $value) {

                    if ($value) {
                        $paramLink = $params;
                        $paramLink[$key] = '';
                        $filterTagsHtml .=
                            "<div class='label label-info' style='font-size:small; margin-right:10px'>" .
                            "<span>" . ucfirst($key) . ": $value</span>" .
                            "<a href='" . $viewName . "?" .
                            http_build_query([$modelName => $paramLink])
                            . "' style='color:#fff; margin-left:15px; text-decoration:none'>x</a></div>";
                    }
                }
                if (!empty($filterTagsHtml)) {
                    echo '<h4>Filters:</h4>';
                }
                echo $filterTagsHtml;
            }
            ?>
        </div>
        <br>
        <h3>search results:</h3><br>

        <?php foreach ($devices as $device) : ?>
            <?= $device->getHTML() . "<br>" ?>
        <?php endforeach; ?>
    </div>

    <div style="text-align:center">
        <?= LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>
    </div>
</div>