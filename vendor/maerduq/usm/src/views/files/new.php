<?php
$this->params['pageHeader'] = 'New file';
$this->params['breadcrumbs'] = [
	['label' => 'USM Admin', 'url' => ['/usm']],
	['label' => 'Files', 'url' => ['index']],
	'New file'
];

echo $this->render('_form', ['model' => $model]);