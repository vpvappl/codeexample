<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-rest',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'rest\controllers',
    'bootstrap' => ['log'],
    'name' => 'API iamruss.ru',
    'modules' => [],
    'homeUrl' => '/rest/',
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'rest\modules\v1\Module'   // here is our v1 modules
        ]
    ],     
    'components' => [
        'request' => [
            'baseUrl' => '/rest',
        ],
        'user' => [
            'identityClass' => 'rest\models\User',
            'enableAutoLogin' => true,
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'enableStrictParsing' => false,
            'suffix' => '/',
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/articles',   // our country api rule,
                    'except' => ['delete', 'create', 'update'], // запрет на удаление
                    'tokens' => [
                        '{id}' => '<id:\\w+>',
                        '{count}' => '<count:\\w+>',
                    ],                    
                    'extraPatterns' => [                        
                        'GET novelty' => 'novelty',
                        'GET novelty/{count}' => 'novelty',
                        'GET last' => 'last',
                        'GET last/{count}' => 'last',                         
                        'GET lastanons' => 'lastanons',
                        'GET lastanons/{count}' => 'lastanons',
                        'GET one' => 'one',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
