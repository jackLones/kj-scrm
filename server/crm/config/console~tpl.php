<?php

	$params = require __DIR__ . '/params.php';
	$db     = require __DIR__ . '/db.php';
	$mdb    = require __DIR__ . '/mdb.php';

	$config = [
		'id'                  => 'basic-console',
		'basePath'            => dirname(__DIR__),
		'bootstrap'           => [
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
		'controllerNamespace' => 'app\commands',
		'aliases'             => [
			'@bower'  => '@vendor/bower-asset',
			'@npm'    => '@vendor/npm-asset',
			'@tests'  => '@app/tests',
			'@upload' => '@app/upload',
			'@static' => '@app/static',
			'@pem'    => '@app/pem',
		],
		'components'          => [
			'websocket'      => [
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
			'queue'          => [
				'class' => '\yii\queue\redis\Queue',
				'redis' => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 1
				]
			],
			'pq'             => [
				'class'     => '\yii\queue\db\Queue',
				'db'        => $mdb, // DB connection component or its config
				'tableName' => '{{%queue}}', // Table name
				'channel'   => 'default', // Queue channel key
				'mutex'     => '\yii\mutex\MysqlMutex', // Mutex used to sync queries
			],
			'p1'             => [
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
			'p2'             => [
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
			'p3'             => [
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
			'work'           => [
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
			'template'       => [
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
			'msg'            => [
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
			'sq'           => [
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
			'msgmedia'       => [
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
			'websocketRedis' => [
				'class'    => 'yii\redis\Connection',
				'hostname' => 'REDISHOST',
				'port'     => 6379,
				'password' => 'REDISPASSWORD',
				'database' => 2
			],
			'redis'          => [
				'class'    => 'yii\redis\Connection',
				'hostname' => 'REDISHOST',
				'port'     => 6379,
				'password' => 'REDISPASSWORD',
				'database' => 0
			],
			'cache'          => [
				'class' => 'yii\redis\Cache',
			],
			'authCache'      => [
				'class' => 'yii\redis\Cache',
				'redis' => [
					'class'    => '\yii\redis\Connection',
					'hostname' => 'REDISHOST',
					'port'     => 6379,
					'password' => 'REDISPASSWORD',
					'database' => 3
				]
			],
			'log'            => [
				'targets' => [
					[
						'class'   => 'yii\log\FileTarget',
						'levels'  => ['error'],
						'logFile' => '@runtime/consoleLogs/' . date('Y-m-d') . '/app.log',
					],
				],
			],
			'db'             => $db,
			'mdb'            => $mdb,
			'ihuyi'          => [
				'class' => 'dovechen\yii2\ihuyi\Ihuyi',
				'msms'  => [
					'appid'  => '',
					'apikey' => '',
				],
			],
			'aes'            => [
				'class' => 'dovechen\yii2\aes\Aes',
				'key'   => 'OallWRnpXVVJMY1h', // The encrypt & decrypt key.
				'iv'    => 'SVVF5OVVNbmQ1Z4R', // A non-NULL Initialization Vector, default: 397e2eb61307109f.
			]
		],
		'params'              => $params,
		/*
		'controllerMap' => [
			'fixture' => [ // Fixture generation command line.
				'class' => 'yii\faker\FixtureController',
			],
		],
		*/
	];

	if (YII_ENV_DEV) {
		// configuration adjustments for 'dev' environment
		$config['bootstrap'][]    = 'gii';
		$config['modules']['gii'] = [
			'class' => 'yii\gii\Module',
		];
	}

	return $config;
