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
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view_order' => ['post'],
                    // 'update_order' => ['get', 'post'],
                    'orders' => ['get', 'post'],
                    'inventory' => ['post', 'get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        //'actions' => ['all'],
                        'roles' => ['@'],
                    ],
                ],
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
                //'class' => 'yii\web\ErrorAction',
            ],
        ];
        // return $this->redirect('/site/admin');
    }

    public function actionOrders()
    {
        $post = Yii::$app->request->post();
        $action = Yii::$app->request->post('action');
        $id = Yii::$app->request->post('id');
        $orderModel = Order::find()
            ->where(['id' => $id])
            ->one();

        if ($action === 'edit_order') {
            if (array_key_exists('price', $post)) {
                $orderModel->price = $post['price'];
            }

            $orderModel->confirmed = array_key_exists('confirmed', $post);
            $orderModel->ordered = array_key_exists('ordered', $post);
            $orderModel->paid = array_key_exists('paid', $post);
            $orderModel->retrieved = array_key_exists('retrieved', $post);
            $oldArrived = $orderModel->arrived;
            $orderModel->arrived = array_key_exists('arrived', $post);

            if(!$oldArrived && $orderModel->arrived){ // order arrived (and had not arrived earlier)
                if($this->sendOrderArrivedMail($orderModel)){
                    Yii::$app->session->setFlash('success', 'An email confirming the arrival has been send to'.$orderModel->customer->email);
                }
            }

            $orderModel->save();
        } else if ($action === 'delete') {
            $orderModel->delete();
        }

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->Request->queryParams);
        $dataProvider->pagination->pageSize = 20;

        $columns = [
            [
                'attribute' => 'id',
                'mergeHeader' => true
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'header' => '',
                'width' => '30px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('/admin/view_order', ['model' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true,
            ],
            [
                'attribute' => 'customer',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->customer->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Customer::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Customer'],
                // 'group' => true,  // enable grouping,
                // 'groupedRow' => true,                    // move grouped column to a single grouped row
                'groupOddCssClass' => 'kv-grouped-row', // configure odd group cell css class
                'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '200px',
            ],
            [
                'attribute' => 'store',
                'width' => '80px',
                'value' => function ($model, $key, $index, $widget) {
                    if (key_exists(0, $model->order_details)) {
                        return $model->order_details[0]->store;
                    }
                    return;
                },
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'mergeHeader' => true,
            ],

            [
                'header' => 'Price',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->price == null ? $model->price : Yii::$app->formatter->asCurrency($model->price, "EUR");
                },
                // 'class' => 'kartik\grid\EditableColumn',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '150px',
                'mergeHeader' => true,
            ],
            [
                'attribute' => 'date',
                'width' => '170px',
                'mergeHeader' => true,
                'value' => function ($model, $key, $index, $widget) {
                    return $model->date;
                },
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'confirmed',
                'value' => 'confirmed',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'showNullAsFalse' => true,
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
                'mergeHeader' => true,
            ],
            [
                'attribute' => 'paid',
                'value' => 'paid',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
                'mergeHeader' => true,

            ],
            [
                'attribute' => 'ordered',
                'value' => 'ordered',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
                'mergeHeader' => true,

            ],
            [
                'attribute' => 'arrived',
                'value' => 'arrived',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
                'mergeHeader' => true,
            ],
            [
                'attribute' => 'retrieved',
                'value' => 'retrieved',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'yes',
                'falseLabel' => 'no',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '100px',
                'mergeHeader' => true,
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => '',
                'width' => '120px',
                'template' => '{copy} {view_status} {mail_customer} {delete}',
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                // 'headerOptions'=>['style'=>'vertical-align: middle',],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        if ($model->paid) {
                            return '<span class="glyphicon glyphicon-trash"></span>';
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', [''], [
                            'title' => Yii::t('app', 'delete'),
                            'data-confirm' => Yii::t('app', "Are you sure you want to delete this order? \n This action cannot be undone!"),
                            'data-pjax' => true,
                            'data-method' => 'post',
                            'data-params' => [
                                'action' => 'delete',
                                'id' => $model->id,
                            ],
                            'title' => 'Delete order',
                            'data-toggle' => 'tooltip'
                        ]);
                    },
                    'copy' => function ($url, $model) {
                        if (key_exists(0, $model->order_details)) {
                            return Html::a('<span class="glyphicon glyphicon-shopping-cart"></span>', [''], [
                                'onclick' => 'copyToClip(' . $model->id . ', "' . $model->order_details[0]->store . '")',
                                'title' => 'copy order details to clipboard and open store in new tab',
                                'data-toggle' => 'tooltip'
                            ]);
                        }
                        return '<span class="glyphicon glyphicon-shopping-cart"></span>';
                    },
                    'view_status' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/order/confirm?h=' . $model->hash],
                            [
                                'target' => '_blank',
                                'data-pjax' => "0",
                                'title' => 'View order progress as shown to the customer',
                                'data-toggle' => 'tooltip'
                            ]
                        );
                    },
                    'mail_customer' => function ($url, $model) {
                        return Html::mailto(
                            '<span class="glyphicon glyphicon-envelope"></span>',
                            $model->customer->email .
                                '?subject=Your klushok order (order no: ' . $model->id . ')' .
                                '&body=Dear ' . $model->customer->name . ",\n \n",
                            [
                                'title' => 'Open a new email to the customer regarding their order',
                                'data-toggle' => 'tooltip'
                            ]
                        );
                    }
                ],
            ],
        ];

        return $this->render('orders', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'columns' => $columns
        ]);
    }

    public function sendOrderArrivedMail($model)
    {
        return Yii::$app->mailer->compose(
            [
                'html' => 'order_arrived/html',
                'text' => 'order_arrived/text',
            ],
            [
                'model' => $model,
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($model->customer->email)
            ->setSubject('Your order has arrived!')
            ->send();
    }


    public function actionView_order()
    {
        return $this->render('view_order');
    }

    public function actionInventory()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Inventory::find(),
            // ->where(['order_id' => $model->id]),
        ]);

        $columns = [
            ['class' => 'kartik\grid\SerialColumn'],
            ['attribute' => 'category'],
            ['attribute' => 'name'],
            ['attribute' => 'info'],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'price',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency($model->price, "EUR");
                },
                'editableOptions' => function ($model) {
                    return [
                        'name' => 'price',
                        //'value' => Yii::$app->formatter->asCurrency($model->price, "EUR"),
                        'asPopover' => true,
                        'header' => 'Price',
                        'inputType' => Editable::INPUT_TEXT,
                        //'data' => [0 => 'Available', -1 => 'Unknown', 1 => 'Limited', 99 => 'Sold out'],
                        'options' => [
                            'class' => 'form-control',
                            'type' => 'number',
                            'step' => 0.01,
                            'min' => 0,

                        ],

                    ];
                },
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                //https://webtips.krajee.com/wp-content/cache/page_enhanced/webtips.krajee.com/setup-editable-column-grid-view-manipulate-records/_index.html_gzip
                'attribute' => 'stock',
                'value' => function ($model) {
                    $stock = $model->getStockStatus($model->stock);
                    return $stock['status'] . " (" . $model->stock . ")";
                },
                'editableOptions' => function ($model) {
                    return [
                        'name' => 'stock',
                        // 'value' => function ($model) {
                        //     $stock = Inventory::getStockStatus($model->stock);
                        //     return $stock['status'] . " (" . $model->stock . ")";
                        // },
                        'asPopover' => true,
                        'header' => 'Stock',
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'data' => [0 => 'Available', -1 => 'Unknown', 1 => 'Limited', 99 => 'Sold out'],
                        'options' => ['class' => 'form-control'],
                        'displayValueConfig' => [
                            '0' => $model->getStockStatus(0)['status'] . " (0)",
                            '-1' => $model->getStockStatus(-1)['status'] . " (-1)",
                            '1' => $model->getStockStatus(1)['status'] . " (1)",
                            '99' => $model->getStockStatus(99)['status'] . " (99)",
                        ],
                    ];
                },
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['inventory'], [
                            'title' => Yii::t('app', 'delete'),
                            'data-confirm' => Yii::t('app', "Are you sure you want to delete this component? \n This action cannot be undone!"),
                            'data-pjax' => true,
                            'data-method' => 'post',
                            'data-params' => [
                                'action' => 'delete',
                                'id' => $model->id,
                            ],
                        ]);
                    },
                ],
            ],
        ];

        $model = new Inventory();
        $model->scenario = 'new';

        $post = Yii::$app->request->post();
        $isInDb = null;

        if (!empty($post) && array_key_exists('Inventory', $post)) {
            if (array_key_exists('editableAttribute', $post)) // edit a value from column
            {
                $message = '';
                $output = '';
                $id = $post['editableKey'];
                $value = $post['Inventory'][$post['editableIndex']][$post['editableAttribute']];

                if ($post['editableAttribute'] === 'stock') { // update stock
                    $model->setStock($id, $value);
                } elseif ($post['editableAttribute'] === 'price') { // update price
                    if (fmod(100 * $value, 1) === (float) 0) { // check number of decimal points
                        $model->setPrice($id, $value);
                        $output = Yii::$app->formatter->asCurrency($value, "EUR");
                    } else {
                        $message = 'Please enter a multiple of 0.01';
                    }
                }
                echo Json::encode(['output' => $output, 'message' => $message]);
                return;
            } elseif ($post['Inventory']['action'] === 'add') { // add item to inventory
                $isInDb = $model->isInDb(
                    $post['Inventory']['category'],
                    $post['Inventory']['name'],
                    $post['Inventory']['info']
                );

                if (!$isInDb) {
                    $model->addComponent(
                        $post['Inventory']['category'],
                        $post['Inventory']['name'],
                        $post['Inventory']['info'],
                        $post['Inventory']['price']
                    );

                    Yii::$app->session->setFlash('success', 'The new component was added to the inventory');
                    return $this->refresh();
                } else if ($isInDb) {
                    Yii::$app->session->setFlash('error', 'This component could not be added since it is already part of the inventory');
                }
            }
        } else if (!empty($post) && $post['action'] == 'delete') {
            $deleteReturn = $model->deleteComponentById($post['id']);
        }

        return $this->render('inventory', [
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'model' => $model,
        ]);

        // return $this->redirect(['inventory'] , 302 , false);
    }

    public function actionCustomers()
    {

        $action = Yii::$app->request->post('action');
        $id = Yii::$app->request->post('id');
        if ($action === 'delete' && $id != null) {
            $customer = Customer::find()->where(['id' => $id])->one();
            $customer->delete();
        }

        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->Request->queryParams);
        $columns = [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'header' => '',
                'width' => '30px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('/admin/view_customer', ['customer' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true,
            ],
            [
                'attribute' => 'name',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Customer::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Name'],
                'width' => '150px',
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'email',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Customer::find()->orderBy('name')->asArray()->all(), 'id', 'email'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Email'],
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'student_no',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Customer::find()->orderBy('name')->asArray()->all(), 'id', 'student_no'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Student no'],
                'width' => '150px',
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'header' => '# orders',
                'value' => function ($model) {
                    return sizeof($model->order);
                },
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'mergeHeader' => true,
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => '',
                'width' => '60px',
                'template' => '{delete}',
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                // 'headerOptions'=>['style'=>'vertical-align: middle',],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        if (!empty($model->order)) {
                            return '<span class="glyphicon glyphicon-trash"></span>';
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', [''], [
                            'title' => Yii::t('app', 'delete'),
                            'data-confirm' => Yii::t('app', "Are you sure you want to delete this order? \n This action cannot be undone!"),
                            'data-pjax' => true,
                            'data-method' => 'post',
                            'data-params' => [
                                'action' => 'delete',
                                'id' => $model->id,
                            ],
                        ]);
                    },
                ],
            ],
        ];

        return $this->render('customers', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $columns,
        ]);
    }

    public function actionView_customer()
    {
        return $this->renderPartial('view_customer');
    }

    public function actionProjects()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->Request->queryParams);

        $columns = [
            [
                'attribute' => 'id',
                'mergeHeader' => true,
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '40px',
            ],
            [
                'attribute' => 'title',
                'filterInputOptions' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                    'placeholder' => 'Search...'
                ],
                // 'width'=>'40ch',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'author',
                'filterInputOptions' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                    'placeholder' => 'Search...'
                ],
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'created_at',
                'filterInputOptions' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                    'placeholder' => 'Search...'
                ],
                'value' => function ($model) {
                    return substr($model->created_at, 0, 10);
                },
                'width' => '130px',
                'vAlign' => 'middle',

            ],
            [
                'attribute' => 'reviewer',
                'class' =>  'kartik\grid\EditableColumn',
                'mergeHeader' => true,
            ],
            [

                'class' => 'kartik\grid\EditableColumn',
                'mergeHeader' => true,
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '80px',
                'attribute' => 'confirmed',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->confirmed
                        ? '<span class="glyphicon glyphicon-ok text-success"></span>'
                        : '<span class="glyphicon glyphicon-remove text-danger"></span>';
                },
                'editableOptions' => function ($model) {
                    return [
                        'name' => 'confirmed',
                        'asPopover' => true,
                        'header' => 'Confirm',
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'data' => [false => 'No', true => 'Yes'],
                        'options' => ['class' => 'form-control'],
                    ];
                },

            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'mergeHeader' => true,
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'width' => '80px',
                'attribute' => 'approved',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->approved
                        ? '<span class="glyphicon glyphicon-ok text-success" style="border-bottom:none"></span>'
                        : '<span class="glyphicon glyphicon-remove text-danger"></span>';
                },
                'editableOptions' => function ($model) {
                    return [
                        // 'containerOptions' => [
                        //     'data-toggle' => 'tooltip',
                        //     'title' => $model->reviewer
                        //         ? ''
                        //         : 'This attribute can only be changed when a reviewer is set',
                        // ],
                        'contentOptions' => [
                            'data-toggle' => 'tooltip',
                            'title' => 'WARNING: Approving a project will notify the author via email',
                        ],
                        // 'disabled' => $model->reviewer ? false : true,
                        'name' => 'approved',
                        'asPopover' => true,
                        'header' => 'Approve',
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'data' => [false => 'No', true => 'Yes'],
                        'options' => ['class' => 'form-control'],
                    ];
                },

            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'dropdown' => false,
                'dropdownOptions' => ['class' => 'float-right'],
                'header' => 'Actions',
                'width' => '110px',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'template' => '{view}{email}{hash}{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash" style="margin-left:5px"></span>',
                            ['delete_project'],
                            [
                                'title' => Yii::t('app', 'Delete project'),
                                'data-toggle' => 'tooltip',
                                'data-confirm' => Yii::t('app', "Are you sure you want to delete this order? \n This action cannot be undone!"),
                                'data-pjax' => true,
                                'data-method' => 'post',
                                'data-params' => [
                                    'action' => 'delete',
                                    'id' => $model->id,
                                ],
                            ]
                        );
                    },
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open" style="margin-right:5px"></span>',
                            ['/projects/view?id=' . $model->id],
                            [
                                'title' => Yii::t('app', 'View project'),
                                'data-toggle' => 'tooltip',
                            ]
                        );
                    },
                    'email' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-envelope" style="margin:0px 5px"></span>',
                            Url::to(
                                'mailto:' . $model->email .
                                    '?subject=Klushok project: ' . $model->title .
                                    '&body=Dear ' . $model->author . ','
                            ),
                            [
                                'title' => Yii::t('app', 'Email author'),
                                'data-toggle' => 'tooltip',
                            ]
                        );
                    },
                    'hash' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-send" style="margin:0px 5px"></span>',
                            ['send_hashmail?id=' . $model->id],
                            [
                                'title' => Yii::t('app', 'Send new unique access code to author'),
                                'data-toggle' => 'tooltip',
                            ]
                        );
                    },
                ],
            ],
        ];


        if (Yii::$app->request->isPost && isset($_POST['hasEditable'])) // an attribute is being changed
        {

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $index = $_POST['editableIndex'];
            $attribute = $_POST['editableAttribute'];
            $id = $_POST['editableKey'];
            $value = $_POST['Project'][$index][$attribute];

            $project = Project::findOne($id);
            $isNewlyApproved = false;

            // return var_dump($project->reviewer, Yii::$app->user->identity->first_name);
            switch ($attribute) {
                case 'approved':
                    if ($project->reviewer == null) {
                        return [
                            'output' => '',
                            'message' => 'A reviewer must be set before this project can be approved or disapproved'
                        ];
                    } elseif (stripos($project->reviewer, Yii::$app->user->identity->first_name) === false) {  // === false -> stripos() documentation
                        return [
                            'output' => '',
                            'message' => 'You are not the reviewer of this project (ID mismatch)'
                        ];
                    } elseif ($value) {
                        $project->approved_at = date("Y-m-d H:i:s");
                    }

                    if (!$project->approved && $value) {
                        $isNewlyApproved = true;
                    }

                    $project->$attribute = $value; // TODO
                    $output = $project->$attribute
                        ? '<span class="glyphicon glyphicon-ok text-success"></span>'
                        : '<span class="glyphicon glyphicon-remove text-danger"></span>';

                    break;

                case 'confirmed':
                    $project->$attribute = $value;
                    $output = $project->$attribute
                        ? '<span class="glyphicon glyphicon-ok text-success"></span>'
                        : '<span class="glyphicon glyphicon-remove text-danger"></span>';

                    break;

                case 'reviewer':
                    $project->$attribute = $value;
                    $output = $value;
                    break;

                default:
                    return [
                        'output' => '',
                        'message' => 'Invalid attribute'
                    ];
            }

            if ($project->save()) {
                if ($isNewlyApproved) {       // if the project has been approved, send mail  
                    $project->send_project_approved_mail();
                }
                return ['output' => $output, 'message' => ''];
            }

            return ['output' => '', 'message' => 'Something went wrong, value = ' . $project->subtitle];
        }

        return $this->render('projects', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $columns,
        ]);
    }

    public function actionDelete_project()
    {
        if (Yii::$app->request->isPost && $_POST['action'] === 'delete') {
            $project = Project::findOne($_POST['id']);

            $path = Yii::$app->basePath . "/files/projects/" . $project->id . "/";
            FileHelper::removeDirectory($path);
            if (!file_exists(Yii::$app->basePath . "/files/projects/" . $project->id . "/")) {
                $project->delete();
                Yii::$app->session->setFlash('success', 'The project was deleted.');
            } else {
                Yii::$app->session->setFlash('danger', 'Project not deleted: Could not delete project file directory.');
            }
        } else {
            Yii::$app->session->setFlash('danger', 'Could not delete project due to an invalid request.');
        }
        // Yii::$app->session->setFlash('info', 'test');
        $this->redirect('projects');
    }

    public function actionSend_hashmail()
    {
        // flowchart: 
        // - get project id from post/get
        // - get project model from db (findOne($id))
        // - start transaction
        // - reset hash in model
        // - create email
        // - commit transaction if email succesfully send

        if (!Yii::$app->request->isGet || !isset($_GET['id'])) {
            Yii::$app->session->setFlash('danger', '<span class="glyphicon glyphicon-exclamation-sign
            " style="margin-right:10px;font-size:20px"></span> Request not valid.');
            return $this->redirect('projects');
        }

        $id = $_GET['id'];
        $project = Project::findOne($id);

        if (!$project) {  // project not found
            Yii::$app->session->setFlash('danger', '<span class="glyphicon glyphicon-remove" style="margin-right:10px"></span> The email could not be send: project id invalid.');
            return $this->redirect('projects');
        }


        $transaction = Project::getDb()->beginTransaction();    // begin transaction
        $project->createUniqueHash();                           // create new hash

        // save hash, if failed: throw warning and rollback transaction
        if (!$project->save()) {
            Yii::$app->session->setFlash('danger', 'Could not save new hash to project');
            $transaction->rollBack();           // rollback transaction
            return $this->redirect('projects');
        }

        if (Yii::$app->mailer->compose(
            [
                'html' => 'project_resend_hash/html',
                'text' => 'project_resend_hash/text',
            ],
            [
                'project' => $project,
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($project->email)
            ->setSubject('Klushok unique acces link to your project')
            ->send()
        ) {
            //mail has been send, commit transaction
            $transaction->commit();
            Yii::$app->session->setFlash('success', '<span class="glyphicon glyphicon-ok" style="margin-right:10px"></span>  An email with a new hash link has been send');
        } else {
            // mail not send, rollback transaction
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', 'Could not mail new hash to author');
        }


        // Yii::$app->session->setFlash('success', '<span class="glyphicon glyphicon-ok" style="margin-right:10px"></span>  An email with a new hash link has been send');
        return $this->redirect('projects');
    }

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
                'label' => '<span class="glyphicon glyphicon-shopping-cart"></span> Orders',
                'url' => ['/admin/orders'],
            ],
            [
                'label' => '<span class="glyphicon glyphicon-oil"></span> Inventory',
                'url' => ['/admin/inventory'],
            ],
            [
                'label' => '<span class="glyphicon glyphicon-user"></span> Customers',
                'url' => ['/admin/customers'],
            ],
            [
                'label' => '<span class="glyphicon glyphicon-pencil"></span> Projects',
                'url' => ['/admin/projects'],
            ],
            [
                'label' => '<span class="glyphicon glyphicon-scale"></span> Devices',
                'url' => ['/admin/devices']
            ]
        ];
    }
}
