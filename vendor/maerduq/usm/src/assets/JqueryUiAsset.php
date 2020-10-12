<?php

namespace maerduq\usm\assets;

use yii\web\AssetBundle;

class JqueryUiAsset extends AssetBundle {

    public $sourcePath = '@usm/style/jquery-ui-1.11.4.custom';
    public $css = [
        'jquery-ui.min.css',
        'jquery-ui.theme.min.css'
    ];
    public $js = [
        'jquery-ui.min.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

}
