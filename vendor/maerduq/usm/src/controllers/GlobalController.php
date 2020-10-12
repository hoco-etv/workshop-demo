<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use maerduq\usm\components\Usm;
use Yii;

class GlobalController extends UsmController {

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true
                    ]
                ]
            ]
        ];
    }

    public function actionSitemap() {
        $items = Usm::getMenu(0, true);

        foreach ($this->module->sitemaps as $sitemapAction) {
            $controller = Yii::$app->createController($sitemapAction);
            if ($controller == null) {
                continue;
            }

            $result = $controller[0]->sitemap();
            $items = array_merge($items, $result);
        }

        return $this->renderPartial('sitemap', [
                'items' => $items
        ]);
    }

    public function actionLogin() {
        if ($this->module->access_type != 'usm' || Yii::$app->session->get('usm_loggedin')) {
            return $this->redirect(['default/index']);
        }

        if (Yii::$app->request->isPost) {
            if ($_POST['LoginForm']['password'] == Yii::$app->getModule('usm')->access_password) {
                Yii::$app->session->set('usm_loggedin', true);
                $url = Yii::$app->session->get('usm_login_return');
                if ($url == $this->action->uniqueId) {
                    $url = 'usm/default/index';
                }
                return $this->redirect(['/' . $url]);
            } else {
                Yii::$app->session->setFlash('error', 'Password incorrect');
            }
        }

        $model = new \maerduq\usm\models\LoginForm();

        return $this->renderPartial('login', [
                'model' => $model
        ]);
    }

    public function actionLogout() {
        switch ($this->module->access_type) {
            case 'yii':
                Yii::$app->user->logout();
                break;
            case 'usm':
                Yii::$app->session->set('usm_loggedin', false);
                Yii::$app->session->setFlash('success', 'Successfully logged out');
                break;
            default:
                throw new \yii\web\HttpException(500, 'USM Access rules have not been set properly');
        }
        return $this->redirect(['/usm']);
    }

}
