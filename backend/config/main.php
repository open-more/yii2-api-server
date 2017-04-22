<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['gii','log'],
    'modules' => [
        'v1' => [
            'class' => 'backend\modules\v1\Module',
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
        ]
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ],
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'utf-8',
        ],
        'user' => [
            'identityClass' => \common\activeRecords\Administrator::class,
//            无状态 api 不使用 session
            'enableAutoLogin' => false,
            'enableSession' => false,
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
        // 添加rbac权限控制
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
//            引入路由配置
            'rules' => require 'route.php',
        ],
    ],
    'params' => $params,
];
