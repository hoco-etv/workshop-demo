<?php

namespace maerduq\usm\assets;

use yii\web\AssetBundle;

class NotifyJsAsset extends AssetBundle {

    public $sourcePath = '@npm/notifyjs-browser/dist/';
    public $js = [
        'notify.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

}
