<?php
$config = [
    'bootstrap' => [
        [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => 'json',
                'application/xml' => 'xml',
            ],
            'languages' => [
                'zh-CN',
                'en',
            ],
        ]
    ],
    'modules'=>[
        'route'=>'deepziyu\yii\rest\module\RouteModule',
    ],
    'components' => [
        'errorHandler' => [
            'class'=>'deepziyu\yii\rest\ErrorHandler'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                //模块化的路径
                "GET,POST,PUT,DELETE <module>/<controller:[\w-]+>/<action:[\w-]+>" => "<module>/<controller>/<action>",
                //基本路径
                "GET,POST,PUT,DELETE <controller:[\w-]+>/<action:[\w-]+>" => "<controller>/<action>",
            ],
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            //返回异常统一处理
            'on beforeSend' => function ($event) {
                //$event->sender->format = 'json';

            },
        ],
    ],
];

return $config;
