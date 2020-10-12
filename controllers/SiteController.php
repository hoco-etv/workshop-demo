<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\controllers\InventoryController;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Inventory;
use app\models\Project;
use app\models\ProjectSearch;
use yii\data\Pagination;
use app\models\Device;
use yii\helpers\Html;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
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
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionProjects()
    {
        $query = Project::find()
            ->where(['approved' => true])
            ->orderBy('created_at');

        $searchModel = new ProjectSearch();

        // configuring pagination
        $dataProvider = $searchModel->search(
            Yii::$app->Request->queryParams, // queryparams is get request
            $query
        );
        $dataProvider->pagination->route = '/projects';
        $dataProvider->pagination->defaultPageSize = 12;

        return $this->render('projects', [
            'searchModel' => $searchModel,
            'projects' => $dataProvider->models,
            'pages' => $dataProvider->pagination
        ]);
    }

    public function actionOrder()
    {
        return $this->render('order',['shoppingCart'=>$this->renderAjax('/order/shoppingcart')]);
    }

    public function actionCommittee()
    {
        return "<script>window.open('https://etv.tudelft.nl/members/committee/view?id=14', '_blank')</script>";
    }

    public function actionInventory()
    {
        // $inventoryController = new InventoryController('inventory_controller', new Inventory);
        // $pricelist = $inventoryController->getPricelist(true);

        $pricelist = (new Inventory)->getPricelist(true);
        return $this->render('inventory', ['pricelist' => $pricelist]);
    }

    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/usm/default');
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/usm/default');
        }
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionDevices()
    {

        $searchModel = new Device();
        $searchModel->scenario = 'search';

        $dataProvider = $searchModel->search(
            Yii::$app->Request->queryParams
        );
        $dataProvider->pagination->route = '/devices';
        $dataProvider->pagination->defaultPageSize = 10;

        return $this->render('devices', [
            'searchModel' => $searchModel,
            'devices'   => $dataProvider->models,
            'pages' => $dataProvider->pagination
        ]);
    }
}
