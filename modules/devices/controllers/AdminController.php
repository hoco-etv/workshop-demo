<?php

namespace app\controllers;

use app\models\Customer;
use app\models\CustomerSearch;
use app\models\Device;
use app\models\Inventory;
use app\models\Order;
use app\models\Project;
use app\models\ProjectSearch;
use kartik\editable\Editable;
use kartik\grid\GridView;
use maerduq\usm\components\UsmController;
use maerduq\usm\models\File;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\OrderSearch;

class AdminController extends UsmController
{

    public function actionDevices()
    {

        $searchModel = new Device();
        $searchModel->scenario = 'search';
        $dataProvider = $searchModel
            ->search(Yii::$app->Request->queryParams);

        $columns =
            [
                [
                    'attribute' => 'id',
                    'mergeHeader' => true,
                ],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'width' => '40px',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    // uncomment below and comment detail if you need to render via ajax
                    // 'detailUrl'=>Url::to(['/site/book-details']),
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial(
                            '_viewDevice',
                            [
                                'model' => $model,
                            ]
                        );
                    },
                    'header' => '',
                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                    'expandOneOnly' => true
                ],
                [
                    'attribute' => 'brand',
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'type',
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->status . ' (' . $model->getStatus()['message'] . ')';
                    }
                ],
                [
                    'attribute' => 'last_updated_at',
                ],
                [
                    'class' => '\kartik\grid\ActionColumn',
                    'dropdown' => false,
                    'dropdownOptions' => ['class' => 'float-right'],
                    'header' => 'Actions',
                    'width' => '90px',
                    'vAlign' => 'middle',
                    'hAlign' => 'center',
                    'template' => '{edit}{picture}{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-trash" style="margin:0 3px"></span>',
                                ['delete_device'],
                                [
                                    'title' => Yii::t('app', 'Delete device'),
                                    'data-toggle' => 'tooltip',
                                    'data-confirm' => Yii::t('app', "Are you sure you want to delete this device? \n This action cannot be undone!"),
                                    'data-pjax' => true,
                                    'data-method' => 'post',
                                    'data-params' => [
                                        'action' => 'delete',
                                        'id' => $model->id,
                                    ],
                                ]
                            );
                        },
                        'picture' => function ($url, $model) {
                            $imageNameParts = explode('/', $model->image);
                            $currentImageName = end($imageNameParts);
                            return Html::a(
                                '<span class="glyphicon glyphicon-picture" style="margin:0 3px"></span>',
                                ['select_image?id=' . $model->id . '&img=' . $currentImageName],
                                [
                                    // 'id' => 'popupModal',
                                    'class' => 'update-modal-link',
                                    'title' => Yii::t('app', 'Change device image'),
                                    'data-toggle' => 'tooltip',
                                ]
                            );
                        },
                        'edit' => function ($url, $model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-wrench" style="margin:0 3px"></span>',
                                ['repair_device?id=' . $model->id],
                                [
                                    'title' => 'Repair device',
                                    'data-toggle' => 'tooltip'
                                ]
                            );
                        }
                    ],
                ],
            ];



        return $this->render(
            'devices',
            [
                'imageNames' => $this->getImageNames(),
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'columns' => $columns
            ]
        );
    }

    public function actionRepair_device()
    {
        // if post request -> 
        // save new data
        // redirect to /admin/devices



        $id = Yii::$app->request->get('id', null);
        $device = Device::findOne($id);

        if (!$id || !$device) {
            throw new HttpException(404, 'Device not found');
        }

        if (Yii::$app->request->isPost) {
            if (isset($_POST['Device'])) {
                $device->load(Yii::$app->request->post());

                if ($device->save()) {
                    Yii::$app->session->setFlash('success', 'Device updated');
                    return $this->redirect('/admin/devices');
                } else {
                    Yii::$app->session->setFlash('danger', 'Could not update Device');
                }
            }
        }

        return $this->render('repair_device', ['device' => $device]);
    }

    private function getImageNames()
    {
        $images = File::findAll(['category' => 'devices']);
        $imageNames = array();
        foreach ($images as $image) {
            array_push($imageNames, $image->name);
        }
        return $imageNames;
    }

    public function actionSelect_image()
    {
        if (Yii::$app->request->isPost && isset($_POST['id']) && isset($_POST['imageName'])) {
            $model = Device::findOne($_POST['id']);
            $model->image = '/file/devices/' . $_POST['imageName'];
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Device image updated');
            } else {
                Yii::$app->session->setFlash('danger', 'Something went wrong, the image could not be changed');
            }
            return $this->redirect('/admin/devices');
            // return;
        }

        return $this->renderAjax('_imageSelect', ['imageNames' => $this->getImageNames()]);
    }

    public function actionNew_device()
    {
        if (Yii::$app->request->isPost && isset($_POST['Device'])) {
            $model = new Device();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Device added!');
            } else {
                Yii::$app->session->setFlash('danger', 'Could not add device, please try again later');
            }
        }
        return $this->redirect('/admin/devices');
    }

    public function actionDelete_device()
    {
        if (Yii::$app->request->isPost && isset($_POST['id']) && isset($_POST['action'])) {
            $model = Device::findOne($_POST['id']);
            if ($_POST['action'] === 'delete' && $model->delete()) {
                Yii::$app->session->setFlash('success', 'Device has been deleted');
            } else {
                Yii::$app->session->setFlash('danger', 'Device could not be deleted');
            }
        } else {
            Yii::$app->session->setFlash('danger', 'Invalid request');
        }
        return $this->redirect('/admin/devices');
    }

    public function menuItems()
    {
        return [

            [
                'label' => '<span class="glyphicon glyphicon-scale"></span> Devices',
                'url' => ['/admin/devices']
            ]

        ];
    }
}
