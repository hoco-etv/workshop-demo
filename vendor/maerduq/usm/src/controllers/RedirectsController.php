<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use maerduq\usm\models\Redirect;
use maerduq\usm\models\Page;
use Yii;

class RedirectsController extends UsmController {

    public function actionIndex() {
        $model = new Redirect();

        return $this->render('index', [
                'model' => $model,
        ]);
    }

    public function actionCreate() {
        $model = new Redirect();

        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['Redirect'];
            switch ($model->type) {
                case 'cms':
                    $model->destination = (isset($_POST['cms_page'])) ? $_POST['cms_page'] : null;
                    break;
                case 'menu_item':
                    $model->destination = '';
                    break;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'URL has been added');
                $this->redirect(['update', 'id' => $model->id]);
                return;
            }
        }

        $cms = Page::find()->orderBy('title')->all();
        $this->view->params['cms_options'] = [];
        foreach ($cms as $item) {
            $this->view->params['cms_options'][$item->id] = $item->title;
        }

        return $this->render('create', [
                'model' => $model
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Redirect::findOne($id);
        if ($model == null || $model->generated) {
            $this->redirect(['index']);
            return;
        }

        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['Redirect'];
            switch ($model->type) {
                case 'cms':
                    $model->destination = (isset($_POST['cms_page'])) ? $_POST['cms_page'] : null;
                    break;
                case 'menu_item':
                    $model->destination = '';
                    break;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'URL has been edited');
                $this->redirect(['index']);
                return;
            }
        }

        $cms = Page::find()->orderBy('title')->all();
        $this->view->params['cms_options'] = [];
        foreach ($cms as $item) {
            $this->view->params['cms_options'][$item->id] = $item->title;
        }

        return $this->render('update', [
                'model' => $model
        ]);
    }

    public function actionDelete($id = null) {
        $model = Redirect::findOne($id);
        if ($model == null) {
            throw new \yii\web\HttpException(404, 'URL not found');
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'URL has been deleted');
        $this->redirect(['index']);
        return;
    }

}
