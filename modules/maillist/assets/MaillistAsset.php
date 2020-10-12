<?php

namespace app\modules\maillist\assets;

use yii\web\AssetBundle;

class MaillistAsset extends AssetBundle {

    public $sourcePath = '@app/modules/maillist/assets';
    public $css = [
        'css/adminIndex.css'
    ];
    public $depends = [
        'app\assets\AppAsset'
    ];

}
