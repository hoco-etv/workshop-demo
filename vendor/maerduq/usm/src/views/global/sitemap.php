<?php

use yii\helpers\Url;
use yii\web\Response;

Yii::$app->response->format = Response::FORMAT_RAW;
$headers = Yii::$app->response->headers;
$headers->add('Content-Type', 'text/xml');

$content = $this->render('sitemap_xml', [
    'items' => $items
]);

echo trim(preg_replace('/(  |\t|\n)+/', '', $content));
