<?php

namespace maerduq\usm\assets;

use yii\web\AssetBundle;

class BootboxJsAsset extends AssetBundle {

    public $sourcePath = '@npm/bootbox/';
    public $js = [
        'bootbox.min.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];

}
