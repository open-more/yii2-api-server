<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'auth' => [
            'class' => \common\components\JwtAuth::class,
            'key' => 'openmore.org',    // JWT加密key
            'expire' => 14 * 24 * 3600, // token失效时间, 默认2周
            'refresh' => 7200,          // token需要刷新时间,默认2小时,设置为0,表示只到expire时间,不需要刷新
            'max_request_count' => 60,  // 每分钟最大请求数,默认为60次,设置为0,表示没有限制
            'enableKickOff' => false,
        ],
    ],
];
