<?php

$this->params['pageHeader'] = "Edit textblock";
$this->params['breadcrumbs'] = [
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    ['label' => 'Textblocks', 'url' => ['admin']],
    'Edit Textblock'
];
$this->params['documentation'] = $this->render('_docs');

echo $this->render('_form', ['model' => $model]);
