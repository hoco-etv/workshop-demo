<?php
$this->params['pageHeader'] = 'Edit file';
$this->params['breadcrumbs'] = [
	['label' => 'USM Admin', 'url' => ['/usm']],
	['label' => 'Files', 'url' => ['index']],
	'Edit file'
];

echo $this->render('_form', ['model' => $model]);