<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use maerduq\usm\models\Textblock;
use Yii;

class TextblocksController extends UsmController {

    public function actionAdmin() {
        $model = new Textblock();

        return $this->render('admin', [
                'model' => $model
        ]);
    }

    public function actionCreate() {
        $model = new Textblock();

        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['Textblock'];
            if ($model->save()) {
                if (isset($_POST['translations'])) {
                    $model->saveTranslations($_POST['translations']);
                }
                Yii::$app->session->setFlash('success', 'Textblock has been added');
                $this->redirect(['update', 'id' => $model->id]);
                return;
            }
        }

        return $this->render('create', [
                'model' => $model
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Textblock::findOne($id);
        if ($model == null) {
            $this->redirect(['admin']);
            return;
        }

        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['Textblock'];
            if ($model->save()) {
                if (isset($_POST['translations'])) {
                    $model->saveTranslations($_POST['translations']);
                }
                Yii::$app->session->setFlash('success', 'Textblock has been edited');
                $this->redirect(['update', 'id' => $model->id]);
                return;
            }
        }

        return $this->render('update', [
                'model' => $model
        ]);
    }

    public function actionDelete($id = null) {
        $model = Textblock::findOne($id);
        if ($model == null) {
            throw new \yii\web\HttpException(404, 'Textblock not found');
        }
        $model->delete();

        Yii::$app->session->setFlash('success', 'Textblock has been deleted');
        $this->redirect(['admin']);
        return;
    }

}
