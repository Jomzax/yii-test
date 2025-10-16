<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();

function d($data, $isVarDump = false)
{
  echo "<pre>";

  if ($isVarDump) {
    var_dump($data);
  } else {
    print_r($data);
  }

  echo "</pre>";
}

function dd($data, $isVarDump = false)
{
  d($data, $isVarDump);
  exit;
}
