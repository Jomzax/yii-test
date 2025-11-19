<?php

use yii\web\Request;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$baseUrl = str_replace('/web', '', (new Request())->getBaseUrl());
$trustedHosts = [];
$ipHeaders = ['X-Forwarded-For', 'X-Real-IP'];
$sessionMinutes = 30;

$config = [
  'id' => 'basic',
  'basePath' => dirname(__DIR__),
  'bootstrap' => ['log'],
  'aliases' => [
    '@bower' => '@vendor/bower-asset',
    '@npm'   => '@vendor/npm-asset',
  ],
  'components' => [
    'request' => [
      // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
      'cookieValidationKey' => '',
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache',
    ],
    'user' => [
      'identityClass' => 'app\models\User',
      'enableAutoLogin' => true,
    ],
    'errorHandler' => [
      'errorAction' => 'site/error',
    ],
    'mailer' => [
      'class' => \yii\symfonymailer\Mailer::class,
      'viewPath' => '@app/mail',
      // send all mails to a file by default.
      'useFileTransport' => true,
    ],
    'log' => [
      'traceLevel' => YII_DEBUG ? 3 : 0,
      'targets' => [
        [
          'class' => 'yii\log\FileTarget',
          'levels' => ['error', 'warning'],
        ],
      ],
    ],

    'mongodb' => [
      'class' => '\yii\mongodb\Connection',
      'dsn' => 'mongodb://admin:P%40ssw0rd@10.243.91.72:27017/?authSource=admin',
      'defaultDatabaseName' => 'db',
    ],

    'request' => [
      // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
      'cookieValidationKey' => 'qccb8NEkn7eVp_JjcY_Na4gu9xKVNqx4',
      'csrfParam' => '_csrf-smic2-app',
      'baseUrl' => $baseUrl,
      'trustedHosts' => $trustedHosts,
      'ipHeaders' => $ipHeaders,
      'parsers' => [
        'application/json' => 'yii\web\JsonParser',
      ]
    ],

    'urlManager' => [
      'class' => 'yii\web\UrlManager',
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
        '' => 'site/index',
        'login' => 'site/login',
        'logout' => 'site/logout',
        'signup' => 'site/signup',
        'contact' => 'site/contact',
        '/unit/create/<parentId>' => 'unit/create',
        'file/download/<filename>' => 'file/download',
        'file/download/<path:.+>/<filename>' => 'file/download',
        '<controller:\w+>' => '<controller>/index',
        '<controller:\w+>/<action>/<_id>' => '<controller>/<action>',
        '<controller:\w+>/<action>' => '<controller>/<action>',
        'module/<module:\w+>/<controller:\w+>/<action>' => '<module>/<controller>/<action>',
      ],
    ],
  ],
  'params' => $params,
];

if (YII_ENV_DEV) {
  // // configuration adjustments for 'dev' environment
  // $config['bootstrap'][] = 'debug';
  // $config['modules']['debug'] = [
  //   'class' => 'yii\debug\Module',
  //   // uncomment the following to add your IP if you are not connecting from localhost.
  //   //'allowedIPs' => ['127.0.0.1', '::1'],
  // ];

  $config['bootstrap'][] = 'gii';
  $config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'generators' => [
      'mongoDbModel' => [
        'class' => 'yii\mongodb\gii\model\Generator'
      ]
    ],
    // uncomment the following to add your IP if you are not connecting from localhost.
    'allowedIPs' => ['*'],
  ];
}

return $config;
