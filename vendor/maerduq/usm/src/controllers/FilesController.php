<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use maerduq\usm\components\Usm;
use maerduq\usm\models\File;
use yii\helpers\Html;
use Yii;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class FilesController
 *
 * @property \maerduq\usm\UsmModule $module
 */
class FilesController extends UsmController {

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['download'],
                        'allow' => true
                    ],
                    [
                        'allow' => Usm::isUserAdmin()
                    ]
                ]
            ]
        ];
    }

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!is_writable(File::fileDir())) {
            throw new HttpException(404, "The file directory should be writable! " . File::fileDir());
        }

        return true;
    }

    public function actionIndex() {

        $model = new File(['scenario' => 'search']);

        if (isset($_GET['File'])) {
            $model->attributes = $_GET['File'];
        } elseif (Yii::$app->request->isPost) {
            return $this->actionNew();
        }

        return $this->render('index', [
                'model' => $model
        ]);
    }

    public function actionNew() {
        if (Yii::$app->request->isPost && isset($_POST['File'])) {
            $model = new File(['scenario' => 'new']);
            $model->attributes = $_POST['File'];
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'File successfully added');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', 'Something went with saving the file.');
                return $this->render('new', ['model' => $model]);
            }
        } else {
            Yii::$app->session->setFlash('danger', 'Something went wrong.');
            return $this->render('new', ['model' => $model]);
        }
    }

    public function actionUpdate($id = null) {
        $model = File::findOne($id);
        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['File'];
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'File successfully edited');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', 'Something went wrong. ' . Html::errorSummary($model));
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDownload($id = null, $view = null) {
        if ($this->module->isUserAdmin()) {
            $access = 2;
        } elseif (!Yii::$app->user->isGuest) {
            $access = 1;
        } else {
            $access = 0;
        }

        $model = null;

        $path = explode('/', $id);
        if (count($path) == 1) { //only name
            $model = File::findOne(['category' => '', 'name' => $path[0]]);
            if ($model === null) {
                $model = File::findOne($id);
            }
        } elseif (count($path) == 2) { //catergory and name
            $model = File::findOne(['category' => $path[0], 'name' => $path[1]]);
        }

        if ($model == null || $model->access > $access) {
            throw new HttpException(404, 'File not found');
        }

        $file = File::fileDir() . $model->file_name;

        $model->last_accessed_at = Usm::datetime();
        $model->save();

        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_RAW;

        $response->headers->set("Pragma", "public"); // required
        $response->headers->set("Expires", "0");
        $response->headers->set("Cache-Control", "max-age=172800, public, must-revalidate");
        $response->headers->set("Content-Type", $model->file_type);

        $response->headers->set("Content-Disposition", ($view !== null ? "inline" : "attachment") .
            "; filename=\"{$model->name}.{$model->file_ext}\";");
        $response->headers->set("Content-Transfer-Encoding", "binary");
        $response->headers->set("Content-Length", filesize($file));

        if (!is_resource($response->stream = fopen($file, 'r'))) {
            throw new ServerErrorHttpException('file access failed: permission deny');
        }

        $response->send();
    }

    public function actionDelete($id = null) {
        $model = File::findOne($id);
        if ($model == null) {
            throw new HttpException(404, 'File not found');
        }

        $deleted = false;
        try {
            $deleted = $model->delete();
        } catch (\yii\db\IntegrityException $e) {
            Yii::$app->session->setFlash('danger', 'This file is still in use by an other object in the database. Therefor it cannot be deleted!');
            $this->redirect(['index']);
            return;
        }

        if ($deleted) {
            Yii::$app->session->setFlash('success', 'File successfully removed');
        } else {
            Yii::$app->session->setFlash('danger', 'Something went wrong. ' . Html::errorSummary($model));
        }

        $this->redirect(['index']);
    }

}
