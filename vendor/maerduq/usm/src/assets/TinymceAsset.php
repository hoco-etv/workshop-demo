<?php

namespace maerduq\usm\assets;

use yii\web\AssetBundle;

class TinymceAsset extends AssetBundle {

    public $sourcePath = '@vendor/tinymce/tinymce';
    public $js = [
        'tinymce.min.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

}
