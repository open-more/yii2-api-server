<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [   //  v1 版本接口
            'class' => \api\modules\v1\Module::class,
        ],
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
//            'identityClass' => \common\activeRecords\User::class,
            'enableAutoLogin' => false, //  无状态 api 不使用 session
            'enableSession' => false,
        ],
//        引入日志配置文件
        'log' => require 'log.php',
        'urlManager' => [
            'enablePrettyUrl' => true, //  美化 url
            'enableStrictParsing' => true, //  严格解析, 路由必须经过 route.php 文件定义, 不使用隐式路由
            'showScriptName' => false, //  不显示 index.php
            'rules' => require 'route.php', //  引入路由配置
        ],
    'params' => $params,
];
