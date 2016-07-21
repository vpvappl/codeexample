<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'name' => 'Я русский',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'homeUrl' => '/',   
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@webroot/cache',
            'cacheFileSuffix' => '.html',
        ],        
        'assetManager' => [
            'bundles' => [                
                'yii\web\JqueryAsset' => ['js'=>[]],
                'yii\bootstrap\BootstrapPluginAsset' => ['js'=>[]],
                'yii\bootstrap\BootstrapAsset' => ['css' => [],],
            ],     
        ],        
        'wp' => [
            'class' => 'frontend\components\WpComponent',
        ],        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'suffix' => '/',
            'rules' => [
                
                'theme1/<action>' => 'theme1/<action>',
                
                'forum'=>'redirect/forum',

                '/'=>'site/index',        
                'feed'=>'content/feed',
                
                'search/<query:[a-zA-Zа-яА-Я0-9\_\- ]+>' => 'search/index', 
                'search' => 'search/index',
                
                'archive/<year:\d{1,4}>/<month:\d{1,2}>/page/<page:\d+>' => 'content/archive',
                'archive/<year:\d{1,4}>/<month:\d{1,2}>' => 'content/archive',
                'archive/<year:\d{1,4}>' => 'content/archive',
                'archive' => 'content/archive',
                
                'author/<link:[a-zA-Zа-яА-Я0-9\_\-]+>/page/<page:\d+>' => 'content/author',
                'author/<link:[a-zA-Zа-яА-Я0-9\_\-]+>' => 'content/author',
                
                'category/<link:[a-zA-Zа-яА-Я0-9\_\-]+>/page/<page:\d+>' => 'content/category',                
                'category/<link:[a-zA-Zа-яА-Я0-9\_\-]+>' => 'content/category',
                'category/page/<page:\d+>' => 'content/category',
                'category' => 'content/category',
                
                'tag/<link:[a-zA-Zа-яА-Я0-9\_\-]+>/page/<page:\d+>' => 'content/tag',
                'tag/<link:[a-zA-Zа-яА-Я0-9\_\-]+>' => 'content/tag',
                'tag/page/<page:\d+>' => 'content/tag',
                'tag' => 'content/tag',
                
                '<first:[a-zA-Zа-яА-Я0-9\_\-]+>/<second:[a-zA-Zа-яА-Я0-9\_\-]+>' => 'content/postpage',
                '<first:[a-zA-Zа-яА-Я0-9\_\-]+>' => 'content/postpage', 
                
            ]
        ],        
        'request' => [
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
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
    ],
    'params' => $params,
];
