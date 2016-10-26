<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'posttest' => ['post'],
                    'url' => ['get', 'post']
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($name = '')
    {
        return $this->render('index', ['name' => $name]);
    }

    public function actionTime() {
        $datetime = date('r');
        return $this->render('time', ['datetime' => $datetime]);
    }

    public function actionLink() {
        if(!Yii::$app->request->isPost) {
          return $this->render('linkform');
        }

        return $this->render('link', ['link' => Yii::$app->request->post('link')]);
    }

    public function actionPosttest() {
        die(Yii::$app->request->post('name'));
    }
}
