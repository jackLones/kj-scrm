<?php

	return [
		'class'       => 'yii\db\Connection',

		// 主库的配置
		'dsn'         => 'mysql:host=10.10.10.237;dbname=lyx_kj_pscrm',
		'username'    => 'root',
		'password'    => '123',
		'charset'     => 'utf8',
		'tablePrefix' => 'pig_',

		// 从库的通用配置
		'slaveConfig' => [
			'username'    => 'root',
			'password'    => '123',
			'charset'     => 'utf8',
			'tablePrefix' => 'pig_',
			'attributes'  => [
				// 使用一个更小的连接超时
				\PDO::ATTR_TIMEOUT => 10,
			],
		],
		// 从库的配置列表
		'slaves'      => [
			//['dsn' => 'mysql:host=MYSQLHOST;dbname=crm'],
		],
	];
