<?php

	return [
		'adminEmail'          => 'admin@example.com',
		'senderEmail'         => 'noreply@example.com',
		'senderName'          => 'Example.com mailer',
		'site_url'            => 'https://pscrm-adm.51lick.com',
		'web_url'             => 'http://pscrm-mob.51lick.com',
		'scrm_url'            => 'https://pscrm.51lick.com',
		'license'             => 'xBWGExczU4endkT1RoZU1pWXNJUU1qaVV4by81allRSkRLSF1',    // 授权码
		'tx_key'              => 'S6PBZ-D7BRQ-BNB5S-G2LBZ-PYAIO-DJF4K',    //腾讯地图key
		'dialout_url'         => 'https://tx.fastwhale.com.cn/',    //外呼平台url
        'sms_provider'=>'Xuntao', //短信平台： Xuntao | sms_center
        'sms_items'=>array(
            'Xuntao'=>array(
                'account'=>'305618', 
                'password'=>'99oanlpz',
                'apiUrl'=>'http://223.223.187.13:8080/eums/sms/utf8/',
            ),
            'sms_center'=>array(
                'url'=>'http://10.10.10.175:8905',
            ),
        ),
        "ucpass"=>array(//云之讯短信接口
            "appID"=>"828e508c2efc480dacaec6b277f71e97",
            "accountSid"=>"0d889f936d5b46581daf2db0fc8a7e4e",
            "token"=>"6cea74ff0229d5a237f43be6904fe53e",
            "displayNum"=>"057188195693",
        ),
		'weixin'              => [
			'appid'     => '',
			'appSecret' => '',
			'mch_id'    => '',
			'key'       => '',
			'payee'     => '日思夜想SCRM',
		],
		'webhook'             => [
			'register' => []
		],
		'work_agent'          => [
			'must_begin_with' => '日思夜想',
		],
		"default_corp_num"    => 1,
		"default_author_num"  => 5,
		"cashier_url"         => "https://k.pigcms.com.cn",
		"qxy_url"             => "https://h5.qingxiaoyun.cn",
		"qxy_site_url"        => "https://www.qingxiaoyun.com",
		"check_server_corpid" => [
			"wx8807973e8bfcca69"
		],
		"is_prod"             => false,//是否是本系统
		"default_pro"         => 1, // 默认企业微信服务商ID
		"default_auth"        => 1, // 默认公众号第三方平台ID
		"safe_from"           => [],  // 安全来源
		"has_qxy"             => false,
		'hide_str'            => false,
		'call'                => [
			'circuit' => '7moor'
		]
	];
