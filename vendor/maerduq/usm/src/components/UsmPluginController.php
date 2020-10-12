<?php

namespace maerduq\usm\components;

/**
 * Description of UsmPluginController
 *
 * @author Paul Marcelis
 */
class UsmPluginController extends UsmController {

    public function beforeAction() {
        $this->layout = "usm.views.layouts." . Yii::app()->getModule('usm')->adminlayoutfile;
        return true;
    }

}
