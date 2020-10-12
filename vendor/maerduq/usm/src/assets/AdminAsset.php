<?php

namespace maerduq\usm\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle {

    public $sourcePath = '@usm/style/usm';
    public $css = [
        'css/usm.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'maerduq\usm\assets\BootboxJsAsset',
        'maerduq\usm\assets\NotifyJsAsset',
        'maerduq\usm\assets\JqueryUiAsset'
    ];

}
