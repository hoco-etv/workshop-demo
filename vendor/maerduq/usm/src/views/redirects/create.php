<?php

$this->params['pageHeader'] = "New URL";
$this->params['breadcrumbs'] = array(
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'URLs', 'url' => ['admin']],
    'New URL'
);

echo $this->render('_form', ['model' => $model]);

