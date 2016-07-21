<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'New Микоян',
        'theme'=>'masonry',
        'sourceLanguage'=>'en_US',
        'language'=>'ru',
        'charset'=>'utf-8',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
            'application.widgets.*',
            'application.modules.*'
	),

	'modules'=>array(
                'adm'=>array(
                    'class' => 'application.modules.adm.AdmModule',
                ),            
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1901539semen',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			// 'ipFilters'=>array('127.0.0.1','::1'),
      'ipFilters'=>require(dirname(__FILE__).'/conf_ipFilters.php'),
		),
	),

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
                    'loginUrl' => array('adminka/login'),
		),
            
    'authManager'=>array(
        'class'=>'PhpAuthManager',
        'defaultRoles'=>array('guest'),
    ),            
            
    'urlManager'=>require(dirname(__FILE__).'/conf_URLmanager.php'),
            
		'db'=>array(
      'connectionString' => 'mysql:host=u32727.mysql.masterhost.ru;dbname=u32727',
			'emulatePrepare' => true,
			'username' => 'u32727',
			'password' => 'coma5_IcsIO_o6',
			'charset' => 'utf8',
		),            

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
                'adminEmail'=>'styleroom@yandex.ru',
                'good_ip'=>require(dirname(__FILE__).'/conf_ipFilters.php'),
                'bad_ip'=>require(dirname(__FILE__).'/conf_badIP.php'),
	),
);
