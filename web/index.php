<?php

/*
 * Determine the environment and debug setting using HTTP_HOST.
 * - if tudelft.nl is not in host name, you are local
 * - else, you are on live
 */
if (strpos($_SERVER['HTTP_HOST'], 'tudelft.nl') === false) {
	defined('YII_DEBUG') or define('YII_DEBUG', true);
	defined('YII_ENV') or define('YII_ENV', 'local');
	defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
} else {
	defined('YII_DEBUG') or define('YII_DEBUG', false);
	defined('YII_ENV') or define('YII_ENV', 'live');
	defined('YII_ENV_DEV') or define('YII_ENV_DEV', false);
}



// echo "index.php from web folder";

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
