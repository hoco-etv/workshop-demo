<?php

namespace app\modules\maillist\controllers;

// use app\modules\quotes\models\Quote;
// use app\modules\quotes\models\QuoteSearch;

use app\models\User;
use app\modules\maillist\models\Maillist;
use app\modules\maillist\models\Maillist_member;
use maerduq\usm\components\UsmController;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AdminController implements the CRUD actions for Quote model.
 */
class AdminController extends UsmController
{
    // public function behaviors() {
    //     // return parent::behaviors() + [
    //     //     'verbs' => [
    //     //         'class' => VerbFilter::className(),
    //     //         'actions' => [
    //     //             'delete' => ['post'],
    //     //         ],
    //     //     ],
    //     // ];
    // }

    /**
     * Lists all Maillist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $users = User::findUserIdEmail();
        $maillists = Maillist::$maillists;

        return $this->render('index', [
            'users' => $users,
            'maillists' => $maillists,
        ]);
    }

    public function actionUpdate_members()
    {
        $post = Yii::$app->request->post();
        if (key_exists('list_id', $post) && key_exists('members', $post)) {
            $list_id =  $post['list_id'];   // id of the list being edited
            $new_members = $post['members']; // array of id's of members of the list

            // detele current members
            Maillist_member::deleteAll(['list_id' => $list_id]);

            // add new members
            $succes = true;
            foreach ($new_members as $nm) {
                $model = new Maillist_member();
                $model->user_id = $nm;
                $model->list_id = $list_id;
                $succes = $succes && $model->save(false);
            }

            if ($succes) {
                Yii::$app->session->setFlash('success', sizeof($new_members) . " users have been added to '" . Maillist::$maillists[$list_id]['name'] . "'");
            } else {
                Yii::$app->session->setFlash('danger', 'Could not update mail list');
            }
        } else if (key_exists('list_id', $post)) {
            $list_id =  $post['list_id'];   // id of the list being edited
            $delRows = Maillist_member::deleteAll(['list_id' => $list_id]);
            Yii::$app->session->setFlash('success', 'Removed ' . $delRows . ' user(s) from ' . Maillist::$maillists[$list_id]['name']);
        }

        return $this->redirect('index');
    }


    public function menuItems()
    {
        return [
            [
                'label' => '<i class="glyphicon glyphicon-envelope"></i> ' . 'Mail list',
                'url' => ['/maillist/admin/index']
            ]
        ];
    }
}
