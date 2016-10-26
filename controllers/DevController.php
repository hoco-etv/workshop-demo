<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class DevController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
