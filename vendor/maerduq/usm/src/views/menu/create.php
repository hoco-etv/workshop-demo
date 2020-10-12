<?php

$this->params['pageHeader'] = "New menu item";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'Menu', 'url' => ['admin']],
    'New menu item'
];
$this->params['documentation'] = "<p>Here you can add a new menu item to the menu.</p>";

echo $this->render('_form', ['model' => $model, 'return' => $return]);
