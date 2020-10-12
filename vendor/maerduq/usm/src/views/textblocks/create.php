<?php

$this->params['pageHeader'] = "New textblock";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'Textblocks', 'url' => ['admin']],
    'New Textblock'
];
$this->params['documentation'] = $this->render('_docs');

echo $this->render('_form', ['model' => $model]);
