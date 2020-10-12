<?php

$this->params['pageHeader'] = "<h1>Edit page <small>{$model->title}</small></h1>";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'Pages', 'url' => ['admin']],
    $model->title
];

echo $this->render('_form', ['model' => $model, 'return' => $return]);
