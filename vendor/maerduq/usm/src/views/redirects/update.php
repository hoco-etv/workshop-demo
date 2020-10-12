<?php

$this->params['pageHeader'] = "Edit URL";
$this->params['breadcrumbs'] = array(
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'URLs', 'url' => ['admin']],
    'Edit URL'
);

echo $this->render('_form', ['model' => $model]);

