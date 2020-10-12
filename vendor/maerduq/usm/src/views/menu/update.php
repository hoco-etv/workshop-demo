<?php

$this->params['pageHeader'] = "Menu item <small>{$model->title}</small>";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'Menu', 'url' => ['admin']],
    "Edit menu item " . $model->title
];

echo $this->render('_form', array('model' => $model, 'return' => $return));
