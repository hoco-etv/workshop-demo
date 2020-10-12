<?php

namespace maerduq\usm\components;

use yii\web\Controller;
use Yii;

class UsmController extends Controller {

    public $layout = '@usm/views/layouts/admin';

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => Yii::$app->getModule('usm')->getAccessRules(),
                'denyCallback' => function($rule, $action) {
                    switch (Yii::$app->getModule('usm')->access_type) {
                        case 'yii':
                            $user = Yii::$app->user;
                            if ($user->isGuest) {
                                $user->loginRequired();
                            } else {
                                throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                            }
                            break;
                        case 'usm':
                            Yii::$app->session->set('usm_login_return', $action->uniqueId);
                            $this->redirect(['/usm/global/login']);
                            break;
                    }
                }
            ]
        ];
    }

    public function getOptions($model, $column, $where = null, $withFirst = true) {
        $options_model = new $model();

        if ($where == null) {
            $options_temp = $options_model::find()->orderBy($column . ' ASC')->all();
        } elseif (is_array($where) && !isset($where['condition'])) {
            $cond = [];
            foreach ($where as $key => $item) {
                $cond[] = "`{$key}` = :{$key}";
            }
            $cond = implode(' AND ', $cond);
            $options_temp = $options_model::find()->where($cond, $where)->orderBy($column . ' ASC')->all();
        } elseif (is_array($where) && isset($where['condition'])) {
            $options_temp = $options_model::find()->where($where['condition'], $where['params'])->orderBy($column . ' ASC')->all();
        } else {
            $options_temp = $options_model::find()->where($where)->orderBy($column . ' ASC')->all();
        }
        $options = [];
        if ($withFirst) {
            $options[null] = 'Select option';
        }
        foreach ($options_temp as $temp) {
            $options[$temp->id] = $temp->$column;
        }
        return $options;
    }

    public function menuItems() {
        return [];
    }

}
