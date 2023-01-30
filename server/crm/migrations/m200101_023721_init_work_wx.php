<?php

	use yii\db\Migration;

	/**
	 * Class m200101_023721_init_work_wx
	 */
	class m200101_023721_init_work_wx extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<SQL
DROP TABLE IF EXISTS {{%work_provider_config}};
CREATE TABLE {{%work_provider_config}}  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider_corpid` char(64) NOT NULL COMMENT '每个服务商同时也是一个企业微信的企业，都有唯一的corpid。',
  `provider_secret` char(64) NOT NULL COMMENT '作为服务商身份的调用凭证，应妥善保管好该密钥，务必不能泄漏。',
  `token` varchar(255) NOT NULL COMMENT 'Token用于计算签名',
  `encode_aes_key` varchar(255) NOT NULL COMMENT 'EncodingAESKey用于消息内容加密',
  `provider_access_token` varchar(255) NULL COMMENT '服务商的access_token',
  `provider_access_token_expires` char(16) NULL COMMENT 'provider_access_token有效期',
  `register_code` varchar(255) NULL COMMENT '注册码，只能消费一次。在访问注册链接时消费。',
  `register_code_expires` char(16) NULL COMMENT 'register_code有效期，生成链接需要在有效期内点击跳转',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0：关闭、1：开启',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`)
) COMMENT = '企业微信服务商配置';

DROP TABLE IF EXISTS {{%work_suite_config}};
CREATE TABLE {{%work_suite_config}}  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) UNSIGNED NOT NULL COMMENT '服务商ID',
  `suite_id` char(64) NOT NULL COMMENT 'suiteid为应用的唯一身份标识',
  `suite_secret` char(64) NOT NULL COMMENT 'suite_secret为对应的调用身份密钥',
  `token` varchar(255) NOT NULL COMMENT 'Token用于计算签名',
  `encode_aes_key` varchar(255) NOT NULL COMMENT 'EncodingAESKey用于消息内容加密',
  `suite_ticket` varchar(255) NULL COMMENT 'suite_ticket与suite_secret配套使用，用于获取suite_access_token。企业微信后台向登记的应用指令回调地址定期推送（10分钟）',
  `suite_access_token` varchar(255) NULL COMMENT '授权方（企业）access_token',
  `suite_access_token_expires` char(16) NULL COMMENT '授权方（企业）access_token超时时间',
  `pre_auth_code` varchar(255) NULL COMMENT '预授权码',
  `pre_auth_code_expires` char(16) NULL COMMENT '预授权码有效期',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '0：关闭、1：开启',
  `update_time` timestamp(0) NULL ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_WORK_SUITE_PROVIDER_ID` FOREIGN KEY (`provider_id`) REFERENCES {{%work_provider_config}} (`id`)
) COMMENT = '企业微信应用配置';

DROP TABLE IF EXISTS {{%work_corp}};
CREATE TABLE {{%work_corp}}  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `suite_id` int(11) UNSIGNED NOT NULL COMMENT '应用ID',
  `access_token` varchar(255) NULL COMMENT '授权方（企业）access_token\n',
  `access_token_expires` char(16) NULL COMMENT '授权方（企业）access_token超时时间',
  `permanent_code` varchar(255) NULL COMMENT '企业微信永久授权码',
  `corpid` char(64) NULL COMMENT '授权方企业微信id',
  `corp_name` char(64) NULL COMMENT '授权方企业名称，即企业简称',
  `corp_type` char(16) NULL COMMENT '授权方企业类型，认证号：verified, 注册号：unverified',
  `corp_square_logo_url` text NULL COMMENT '授权方企业方形头像',
  `corp_user_max` int(11) NULL COMMENT '授权方企业用户规模',
  `corp_agent_max` int(11) NULL COMMENT '授权方企业应用数上限',
  `corp_full_name` char(64) NULL COMMENT '授权方企业的主体名称(仅认证或验证过的企业有)，即企业全称。',
  `verified_end_time` char(16) NULL COMMENT '企业类型，1. 企业; 2. 政府以及事业单位; 3. 其他组织, 4.团队号',
  `subject_type` tinyint(2) NULL COMMENT '认证到期时间',
  `corp_wxqrcode` varchar(255) NULL COMMENT '授权企业在微工作台（原企业号）的二维码，可用于关注微工作台',
  `corp_scale` varchar(255) NULL COMMENT '企业规模。当企业未设置该属性时，值为空',
  `corp_industry` varchar(255) NULL COMMENT '企业所属行业。当企业未设置该属性时，值为空',
  `corp_sub_industry` varchar(255) NULL COMMENT '企业所属子行业。当企业未设置该属性时，值为空',
  `location` varchar(255) NULL COMMENT '企业所在地信息, 为空时表示未知',
  `auth_user_info` varchar(255) NULL COMMENT '授权管理员的信息',
  `dealer_corp_info` varchar(255) NULL COMMENT '代理服务商企业信息',
  `auth_type` char(16) NULL COMMENT '授权状态 cancel_auth是取消授权，change_auth是更新授权，create_auth是授权成功通知',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_WORK_CORP_SUITE_ID` FOREIGN KEY (`suite_id`) REFERENCES {{%work_suite_config}} (`id`)
) COMMENT = '企业微信授权方信息';

DROP TABLE IF EXISTS {{%work_corp_agent}};
CREATE TABLE {{%work_corp_agent}}  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) UNSIGNED NOT NULL COMMENT '授权方ID',
  `agentid` int(11) UNSIGNED NULL COMMENT '授权方应用id',
  `name` char(64) NULL COMMENT '授权方应用名字',
  `round_logo_url` varchar(255) NULL COMMENT '授权方应用方形头像',
  `square_logo_url` varchar(255) NULL COMMENT '授权方应用圆形头像',
  `appid` int(11) UNSIGNED NULL COMMENT '旧的多应用套件中的对应应用id，新开发者请忽略',
  `level` tinyint(5) NULL COMMENT '权限等级。1:通讯录基本信息只读；2:通讯录全部信息只读；3:通讯录全部信息读写；4:单个基本信息只读；5:通讯录全部信息只写',
  `allow_party` varchar(255) NULL COMMENT '应用可见范围（部门）',
  `allow_user` varchar(255) NULL COMMENT '应用可见范围（成员）',
  `allow_tag` varchar(255) NULL COMMENT '应用可见范围（标签）',
  `extra_party` varchar(255) NULL COMMENT '额外通讯录（部门）',
  `extra_user` varchar(255) NULL COMMENT '额外通讯录（成员）',
  `extra_tag` varchar(255) NULL COMMENT '额外通讯录（标签）',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_WORK_CORP_AGENT_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) COMMENT = '授权的应用信息';
SQL;

			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200101_023721_init_work_wx cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200101_023721_init_work_wx cannot be reverted.\n";

			return false;
		}
		*/
	}
