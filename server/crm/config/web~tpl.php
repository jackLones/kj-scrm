<?php

	$params = require __DIR__ . '/params.php';
	$db     = require __DIR__ . '/db.php';
	$mdb    = require __DIR__ . '/mdb.php';

	$config = [
		'id'         => 'crm',
		'basePath'   => dirname(__DIR__),
		'bootstrap'  => [
			'log',
			'websocket',
			'queue',
			'pq',
			'p1',
			'p2',
			'p3',
			'work',
			'template',
			'msg',
			'sq',
			'msgmedia',
		],
		'aliases'    => [
			'@bower'  => '@vendor/bower-asset',
			'@npm'    => '@vendor/npm-asset',
			'@upload' => '@app/upload',
			'@static' => '@app/static',
			'@pem'    => '@app/pem',
		],
		'components' => [
			'websocket'         => [
				'class'    => '\yiiplus\websocket\swoole\WebSocket',
				'host'     => '127.0.0.1',
				'port'     => 7099,
				'channels' => [
					'server-message' => 'app\channels\ServerMessageChannel',
					'push-message'   => 'app\channels\PushMessageChannel',
					'bind'           => 'app\channels\BindChannel',
					'unbind'         => 'app\channels\UnbindChannel',
					'heart'          => 'app\channels\HeartChannel',
					'web-message'    => 'app\channels\WebMessageChannel',
					'pull'           => 'app\channels\PullChannel',
				],
			],
			'queue'             => [
				'class' => '\yii\queue\redis\Queue',
				'redis' => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'pq'                => [
				'class'     => '\yii\queue\db\Queue',
				'db'        => $mdb, // DB connection component or its config
				'tableName' => '{{%queue}}', // Table name
				'channel'   => 'default', // Queue channel key
				'mutex'     => '\yii\mutex\MysqlMutex', // Mutex used to sync queries
			],
			'p1'                => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'pq_one',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'p2'                => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'pq_two',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'p3'                => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'pq_three',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'work'              => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'work',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'template'          => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'template',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'msg'               => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'msg',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'sq'              => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'shop',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'msgmedia'          => [
				'class'   => '\yii\queue\redis\Queue',
				'channel' => 'msg_media',
				'redis'   => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 4
				]
			],
			'websocketRedis'    => [
				'class'    => 'yii\redis\Connection',
				'hostname' => 'REDISHOST',
				'port'     => 6379,
				'password' => 'REDISPASSWORD',
				'database' => 2
			],
			'redis'             => [
				'class'    => 'yii\redis\Connection',
				'hostname' => 'REDISHOST',
				'port'     => 6379,
				'password' => 'REDISPASSWORD',
				'database' => 0
			],
			'request'           => [
				// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
				'cookieValidationKey' => '0Oh6j_0eKyKJ0d7bBsebS2QDPmsgZojk',
			],
			'cache'             => [
				'class' => 'yii\redis\Cache',
			],
			'authCache'         => [
				'class' => 'yii\redis\Cache',
				'redis' => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 3
				]
			],
			'user'              => [
				'identityClass'   => 'app\models\User',
				'enableAutoLogin' => true,
			],
			'subUser'           => [
				'class'           => 'yii\web\User',
				'identityClass'   => 'app\models\SubUser',
				'enableAutoLogin' => true,
			],
			'adminUser'         => [
				'class'           => 'yii\web\User',
				'identityClass'   => 'app\models\AdminUser',
				'identityCookie'  => ['name' => '_identity_admin', 'path' => '/admin'],
				'idParam'         => '_admin_id',
				'enableAutoLogin' => true,
			],
			'adminUserEmployee' => [
				'class'           => 'yii\web\User',
				'identityClass'   => 'app\models\AdminUserEmployee',
				'identityCookie'  => ['name' => '_identity_admin_employee', 'path' => '/admin'],
				'idParam'         => '_admin_id_employee',
				'enableAutoLogin' => true,
			],
			'errorHandler'      => [
				'errorAction' => 'site/error',
			],
			'mailer'            => [
				'class'            => 'yii\swiftmailer\Mailer',
				// send all mails to a file by default. You have to set
				// 'useFileTransport' to false and configure a transport
				// for the mailer to send real emails.
				'useFileTransport' => true,
			],
			'log'               => [
				'traceLevel' => YII_DEBUG ? 3 : 0,
				'targets'    => [
					[
						'class'   => 'yii\log\FileTarget',
						'levels'  => ['error'],
						'logFile' => '@runtime/logs/' . date('Y-m-d') . '/app.log',
					],
				],
			],
			'db'                => $db,
			'mdb'               => $mdb,
			'urlManager'        => [
				'enablePrettyUrl'     => true,
				'enableStrictParsing' => false,
				'showScriptName'      => false,
				'rules'               => [
					''                                                   => 'site/index',
					'upload/<fileName:[\W|\w]+>'                         => 'upload/index',
					'static/<fileName:[\W|\w]+>'                         => 'upload/static',
					'pem/<fileName:[\W|\w]+>'                            => 'upload/pem',
                    'shop/<key:[\w]+>'                                   => 'api/shop/index',
					'admin'                                              => 'admin/index/login',
					'admin/<_c:[\w|\-]+>'                                => 'admin/<_c>/login',
					'<_c:[\w|\-]+>'                                      => '<_c>/index',
					'<_c:[\w|\-]+>/<id:\d+>'                             => '<_c>/index',
					'<_c:[\w|\-]+>/<_a:[\w|\-]+>'                        => '<_c>/<_a>',
					'<_c:[\w|\-]+>/<_a:[\w|\-]+>/<id:\d+>'               => '<_c>/<_a>',
					'<_m:[\w|\-]+>/<_c:[\w|\-]+>/<_a:[\w|\-]+>'          => '<_m>/<_c>/<_a>',
					'<_m:[\w|\-]+>/<_c:[\w|\-]+>/<_a:[\w|\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
				],
			],
			'ihuyi'             => [
				'class' => 'dovechen\yii2\ihuyi\Ihuyi',
				'msms'  => [
					'appid'  => '',
					'apikey' => '',
				],
			],
			'aes'               => [
				'class' => 'dovechen\yii2\aes\Aes',
				'key'   => 'OallWRnpXVVJMY1h', // The encrypt & decrypt key.
				'iv'    => 'SVVF5OVVNbmQ1Z4R', // A non-NULL Initialization Vector, default: 397e2eb61307109f.
			]
		],
		'modules'    => [
			'api'    => [
				'class' => 'app\modules\api\Module',
			],
			'wechat' => [
				'class' => 'app\modules\wechat\Module',
			],
			'admin'  => [
				'class'  => 'app\modules\admin\Module',
				'layout' => 'main',
			],
			'work'   => [
				'class' => 'app\modules\work\Module',
			],
			'msg'    => [
				'class' => 'app\modules\msg\Module',
			],
		],
		'params'     => $params,
	];

	if (YII_ENV_DEV) {
		// configuration adjustments for 'dev' environment
		$config['bootstrap'][]      = 'debug';
		$config['modules']['debug'] = [
			'class' => 'yii\debug\Module',
			// uncomment the following to add your IP if you are not connecting from localhost.
			//'allowedIPs' => ['127.0.0.1', '::1'],
		];

		$config['bootstrap'][]    = 'gii';
		$config['modules']['gii'] = [
			'class' => 'yii\gii\Module',
			// uncomment the following to add your IP if you are not connecting from localhost.
			//'allowedIPs' => ['127.0.0.1', '::1'],
		];
	}

	return $config;
