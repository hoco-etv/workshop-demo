<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use maerduq\usm\components\Usm;
use maerduq\usm\models\Page;
use maerduq\usm\models\Translation;
use Yii;

class PagesController extends UsmController {

    public $defaultAction = 'admin';

    public function actionAdmin() {
        $model = new Page();

        return $this->render('admin', [
                'model' => $model
        ]);
    }

    public function actionEditpage($id, $return = null, $edit_lang = null) {
        $languages = $this->module->languages;
        $baseLanguage = $languages[0];
        if ($edit_lang == null || !in_array($edit_lang, $languages)) {
            $edit_lang = $baseLanguage;
        }

        $model = Page::find()
            ->select(['p.*', 'IFNULL(in.value, p.title) AS title', 'IFNULL(in2.value, p.content) AS content'])
            ->from(Page::tableName() . ' AS p')
            ->leftJoin('usm_translations AS in', 'in.item_type = "page" AND in.item_id = p.id AND in.key = "title" AND in.lang = :lang', ['lang' => $edit_lang])
            ->leftJoin('usm_translations AS in2', 'in.item_type = "page" AND in2.item_id = p.id AND in2.key = "content" AND in2.lang = :lang', ['lang' => $edit_lang])
            ->where('p.id = :id', ['id' => $id])
            ->one();
        if ($model == null) {
            throw new \yii\web\HttpException(404, 'Page not found');
        }

        if (!$model->wysiwyg) {
            $this->redirect(['update', 'id' => $id]);
            return;
        }

        if (Yii::$app->request->isPost && isset($_POST['thecontent'])) {
            $done = false;
            if ($edit_lang == $baseLanguage) {
                $model->content = $_POST['thecontent'];
                $done = $model->save();
            } else {
                $translationRecord = Translation::find()
                    ->where(['=', 'item_type', $model->translationSettings()['item_type']])
                    ->andWhere(['=', 'item_id', $id])
                    ->andWhere(['=', 'lang', $edit_lang])
                    ->andWhere(['=', 'key', 'content'])
                    ->one();
                if ($translationRecord == null) {
                    $translationRecord = new Translation();
                    $translationRecord->item_type = $model->TranslationSettings()['item_type'];
                    $translationRecord->item_id = $id;
                    $translationRecord->lang = $edit_lang;
                    $translationRecord->key = 'content';
                }
                $translationRecord->value = $_POST['thecontent'];
                $done = $translationRecord->save();
            }

            if ($done) {
                Yii::$app->session->setFlash('success', "Page <b>{$model->title}</b> has been edited! " . \yii\helpers\Html::a('View page', ['interpret/page', 'id' => $model->id], ['class' => 'alert-link']));
                $this->redirect(Usm::returnUrl($return, ['editpage', 'id' => $id, 'edit_lang' => $edit_lang]));
                return;
            }
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

        return $this->render('edit/page', [
                'page' => $model,
                'return' => $return,
                'edit_lang' => $edit_lang,
                'languages' => $languages
        ]);
    }

    public function actionCreate($return = null) {
        $model = new Page();
        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['Page'];
            if ($model->save()) {
                if (isset($_POST['translations'])) {
                    $model->saveTranslations($_POST['translations']);
                }
                Yii::$app->session->setFlash('success', "Page <b>{$model->title}</b> has been created! " . \yii\helpers\Html::a('View page', ['interpret/page', 'page' => $model->id], ['class' => 'alert-link']));
                $this->redirect(['update', 'id' => $model->id]);
                return;
            }
        }

        return $this->render('create', [
                'model' => $model,
                'return' => $return
        ]);
    }

    public function actionUpdate($id = null, $return = null) {
        $model = Page::findOne($id);
        if ($model == null) {
            throw new \yii\web\HttpException(404, 'Page not found');
        }

        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['Page'];
            if ($model->save()) {
                if (isset($_POST['translations'])) {
                    $model->saveTranslations($_POST['translations']);
                }
                Yii::$app->session->setFlash('success', "Page <b>{$model->title}</b> has been edited! " . \yii\helpers\Html::a('View page', ['interpret/page', 'id' => $model->id]));
                if ($return == null) {
                    $this->refresh();
                    return;
                } else {
                    $this->redirect(Usm::returnUrl($return, ['update', 'id' => $id]));
                    return;
                }
            }
        }

        return $this->render('update', [
                'model' => $model,
                'return' => $return
        ]);
    }

    public function actionDelete($id = null) {
        $model = Page::findOne($id);
        if ($model == null) {
            throw new \yii\helpers\HttpException(404, 'Page not found');
        }

        $model->delete();
        $this->redirect(['admin']);
        return;
    }

}
