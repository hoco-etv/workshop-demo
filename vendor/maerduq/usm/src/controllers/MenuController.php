<?php

namespace maerduq\usm\controllers;

use maerduq\usm\components\UsmController;
use maerduq\usm\components\Usm;
use maerduq\usm\models\MenuItem;
use maerduq\usm\models\Page;
use maerduq\usm\models\Redirect;
use Yii;

class MenuController extends UsmController {

    public $options = [];

    public function actionAdmin($sudo = null) {
        $items = MenuItem::find()
            ->from(MenuItem::tableName() . ' AS t')
            ->joinWith(['parent' => function ($query) {
                    $query->from(MenuItem::tableName() . ' AS parent');
                }])
            ->orderBy('IF(parent.position is null,`t`.`position`*100, (parent.position*100)+t.position) ASC')
            ->with(['redirect', 'page'])
            ->all();

        return $this->render('admin', [
                'items' => $items,
                'sudo' => $sudo
        ]);
    }

    public function actionAjaxposition() {
        if (!isset($_POST['newspot'])) {
            die('0');
        }

        $i = $_POST['newspot'];
        if ($i['parent'] == 'NaN') {
            $i['parent'] = null;
        }
        if ($i['oldParent'] == 'NaN') {
            $i['oldParent'] = null;
        }

        $item = MenuItem::findOne($i['id']);
        $item->parent_id = $i['parent'];
        $item->position = $i['order'];
        $item->save();
        if ($i['parent'] == $i['oldParent']) {
            if ($i['order'] > $i['oldOrder']) {
                $sql = "UPDATE " . MenuItem::tableName() . " SET position = position-1 ";
                $sql .= ($i['parent'] == null) ?
                    "WHERE parent_id IS NULL && position >= :oldOrder && position <= :order && id != :id" :
                    "WHERE parent_id = :parent && position >= :oldOrder && position <= :order && id != :id";

                $params = ['oldOrder' => $i['oldOrder'], 'order' => $i['order'], 'id' => $i['id']];
                if ($i['parent'] !== null) {
                    $params['parent'] = $i['parent'];
                }

                Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
            } else {
                $sql = "UPDATE " . MenuItem::tableName() . " SET position = position+1 ";
                $sql .= ($i['parent'] == null) ?
                    "WHERE parent_id IS NULL && position >= :order && position <= :oldOrder && id != :id" :
                    "WHERE parent_id = :parent && position >= :order && position <= :oldOrder && id != :id";

                $params = ['oldOrder' => $i['oldOrder'], 'order' => $i['order'], 'id' => $i['id']];
                if ($i['parent'] !== null) {
                    $params['parent'] = $i['parent'];
                }

                Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
            }
        } else {
            $sql = ($i['oldParent'] == null) ?
                "UPDATE " . MenuItem::tableName() . " SET position = position-1 WHERE parent_id IS NULL && position >= :oldOrder && id != :id" :
                "UPDATE " . MenuItem::tableName() . " SET position = position-1 WHERE parent_id = :oldParent && position >= :oldOrder && id != :id";
            $params = ($i['oldParent'] == null) ?
                ['oldOrder' => $i['oldOrder'], 'id' => $i['id']] :
                ['oldParent' => $i['oldParent'], 'oldOrder' => $i['oldOrder'], 'id' => $i['id']];
            Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

            $sql = ($i['parent'] == null) ?
                "UPDATE " . MenuItem::tableName() . " SET position = position+1 WHERE parent_id IS NULL && position >= :order && id != :id" :
                "UPDATE " . MenuItem::tableName() . " SET position = position+1 WHERE parent_id = :parent && position >= :order && id != :id";
            $params = ($i['parent'] == null) ?
                ['order' => $i['order'], 'id' => $i['id']] :
                ['parent' => $i['parent'], 'order' => $i['order'], 'id' => $i['id']];
            Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }
    }

    public function actionAjaxvisibility() {
        if (!isset($_POST['item']) || !isset($_POST['state'])) {
            die('0');
        }

        $item = MenuItem::findOne($_POST['item']);
        $item->visible = ($_POST['state'] == true) ? 1 : 0;
        if ($item->save()) {
            die('1');
        } else {
            die('0');
        }
    }

    public function actionAjaxaccess() {
        if (!isset($_POST['item']) || !isset($_POST['state'])) {
            die('0');
        }

        $item = MenuItem::findOne($_POST['item']);
        if ($item->type == 'cms') {
            $item->page->access = ($_POST['state'] <= 2 && $_POST['state'] >= 0) ? $_POST['state'] : 0;
            die($item->page->save() ? '1' : '0');
        } else {
            $item->access = ($_POST['state'] <= 2 && $_POST['state'] >= 0) ? $_POST['state'] : 0;
            die($item->save() ? '1' : '0');
        }
    }

    public function actionCreate($page = null, $return = null) {
        $model = new MenuItem();
        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['MenuItem'];
            if ($model->save()) {
                if (isset($_POST['translations'])) {
                    $model->saveTranslations($_POST['translations']);
                }
                Yii::$app->session->setFlash('success', 'Menu item has been added');
                $this->redirect(Usm::returnUrl($return, ['admin']));
                return;
            }
        } elseif ($page != null) {
            $page = Page::findOne($page);
            if ($page != null) {
                $model->visible = 1;
                $model->type = 'cms';
                $model->page_id = $page->id;
                $model->title = $page->title;
            }
        }

        $this->view->params['cms_options'] = $this->getOptions(Page::className(), 'title', [], false);

        return $this->render('create', [
                'model' => $model,
                'return' => $return
        ]);
    }

    public function actionUpdate($id = null, $return = null) {
        $model = MenuItem::findOne($id);
        if ($model == null) {
            throw new \yii\web\HttpException(404, 'Menu item not found');
        }

        if (Yii::$app->request->isPost) {
            $model->attributes = $_POST['MenuItem'];

            if ($model->save()) {
                if (isset($_POST['translations'])) {
                    $model->saveTranslations($_POST['translations']);
                }

                Yii::$app->session->setFlash('success', 'Menu item has been edited!');
                $this->refresh();
                return;
            }
        }

        $this->view->params['cms_options'] = $this->getOptions(Page::className(), 'title', [], false);

        return $this->render('update', [
                'model' => $model,
                'return' => $return
        ]);
    }

    public function actionDelete($id = null) {
        $model = MenuItem::findOne($id);
        if ($model != null) {
            Redirect::deleteAll('menu_item_id = :id', ['id' => $id]);
            $model->delete();
            $sql = ($model->parent_id == null) ?
                "UPDATE " . MenuItem::tableName() . " SET position = position-1 WHERE parent_id IS NULL && position >= :order" :
                "UPDATE " . MenuItem::tableName() . " SET position = position-1 WHERE parent_id = :parent && position >= :order";
            $params = ($model->parent_id == null) ?
                ['order' => $model->position] :
                ['parent' => $model->parent_id, 'order' => $model->position];
            Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }
        $this->redirect(['admin']);
        return;
    }

}
