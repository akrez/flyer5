<?php

function dd(...$input)
{
    foreach ($input as $i) {
        var_dump($i);
    }
    die;
}

function jd(...$input)
{
    die(json_encode($input));
}

function ed($input)
{
    var_export($input);
    die;
}

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

$config = [
    'id' => 'flyer5',
    'name' => 'راپکو',
    'language' => 'fa-IR',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'vendorPath' => dirname(__DIR__) . '../../vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'eZmKmrB7TIBhGNCWrF1OeqrKAxWCDlzC',
            'baseUrl' => '/projects/flyer5',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => 'flyer5-identity-app', 'httpOnly' => true],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=flyer5',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'schemaCacheDuration' => 60,
            'schemaCache' => 'cache',
            'enableSchemaCache' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
                '<controller:[\w\-]+>/' => '<controller>/index',
                '' => 'site/index',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'formatter' => [
            'class' => 'app\components\Formatter',
        ],
        'storage' => [
            'class' => 'app\components\Storage',
        ],
    ],
    'params' => [
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

(new yii\web\Application($config))->run();
