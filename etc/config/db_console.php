<?php

/**
 * This is a template file for the db connection. Replace
 * HOST, DB, USER and PASSWORD with the correct values for
 * your own system configuration.
 */
return [
	'class' => 'yii\db\Connection',
	'dsn' => 'mysql:host=workshop_mysql:3306;dbname=workshop',
	'username' => 'root',
	'password' => 'verysecret',
	'charset' => 'utf8',
	'enableSchemaCache' => true,
	'schemaCacheDuration' => 3600,
	'schemaCache' => 'cache'
];
