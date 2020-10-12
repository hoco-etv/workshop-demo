<?php

$this->params['pageHeader'] = "<h1>New page</h1>";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'Pages', 'url' => ['admin']],
    'New page'
];

echo $this->render('_form', ['model' => $model, 'return' => $return]);
