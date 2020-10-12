<?php

namespace maerduq\usm\assets;

use yii\web\AssetBundle;

class CodemirrorAsset extends AssetBundle {

    public $sourcePath = '@npm/codemirror';
    public $js = [
        'lib/codemirror.js',
        'mode/xml/xml.js',
        'mode/javascript/javascript.js',
        'mode/css/css.js'
    ];
    public $css = [
        'lib/codemirror.css'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];

}
