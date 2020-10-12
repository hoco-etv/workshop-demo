<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use Yii;

class SessionController extends UsmController {

    public function actionLogout() {
        Yii::$app->user->logout();
        $this->redirect(['/usm']);
        return;
    }

}
