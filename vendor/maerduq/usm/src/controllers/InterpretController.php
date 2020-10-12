<?php

namespace maerduq\usm\controllers;

use Yii;
use maerduq\usm\components\UsmController;
use maerduq\usm\models\Page;
use maerduq\usm\components\Usm;

/**
 * Description of InterpretController
 *
 * @author Paul Marcelis
 */
class InterpretController extends UsmController {

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

    public function actionLink($href = null) {
        header('location: ' . $href);
        die();
    }

    public function actionPage($id = null) {
        if ($this->module->isUserAdmin()) {
            $access = 2;
        } elseif (Yii::$app->user->isGuest) {
            $access = 0;
        } else {
            $access = 1;
        }

        $lang = Yii::$app->language;
        if (!in_array($lang, Yii::$app->getModule('usm')->languages)) {
            $lang = $usm->languages[0];
        }

        $model = Page::find()
            ->select(['p.*', 'IFNULL(in.value, p.title) AS title', 'IFNULL(in2.value, p.content) AS content'])
            ->from(Page::tableName() . ' AS p')
            ->leftJoin('usm_translations AS in', 'in.item_type = "page" AND in.item_id = p.id AND in.key = "title" AND in.lang = :lang', ['lang' => $lang])
            ->leftJoin('usm_translations AS in2', 'in.item_type = "page" AND in2.item_id = p.id AND in2.key = "content" AND in2.lang = :lang', ['lang' => $lang])
            ->where('p.id = :id', ['id' => $id])->andWhere('access <= :access', ['access' => $access])
            ->one();
        if ($model == null) {
            throw new \yii\web\HttpException(404, 'Page not found');
        }

        switch ($model->style) {
            case 'plain':
                $this->layout = $this->module->layout_plain;
                break;
            case 'empty':
                $this->layout = $this->module->layout_empty;
                break;
            default:
                $this->layout = $this->module->layout_container;
                break;
        }

        $model->content = Usm::evalContent($model->content);

        return $this->render('page', array(
                'pageTitle' => $model->title . ' | ' . Yii::$app->name,
                'page' => $model
        ));
    }

}
