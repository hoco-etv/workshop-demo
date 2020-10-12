<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;

class DefaultController extends UsmController {

    public function actionIndex() {
        return $this->render('index');
    }

}
