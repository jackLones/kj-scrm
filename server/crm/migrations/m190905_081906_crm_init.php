<?php

use yii\db\Migration;

/**
 * Class m190905_081906_crm_init
 */
class m190905_081906_crm_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$sql = <<<SQL
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pig_wx_authorize_config
-- ----------------------------
DROP TABLE IF EXISTS {{%wx_authorize_config}};
CREATE TABLE {{%wx_authorize_config}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appid` char(64) DEFAULT NULL COMMENT '第三方开放平台应用APPID',
  `appSecret` char(64) DEFAULT NULL COMMENT '第三方开放平台应用APPSECRET',
  `token` varchar(255) DEFAULT NULL COMMENT '第三方开放平台应用对接TOKEN',
  `encode_aes_key` varchar(255) DEFAULT NULL COMMENT '第三方开放平台应用对接ENCODE_AES_KEY',
  `component_verify_ticket` varchar(255) DEFAULT NULL COMMENT '第三方平台安全TICKET（每十分钟更新一次）',
  `component_access_token` varchar(255) DEFAULT NULL COMMENT '第三方平台接口调用凭据',
  `component_access_token_expires` char(16) DEFAULT NULL COMMENT '第三方平台接口调用凭据失效时间',
  `pre_auth_code` varchar(255) DEFAULT NULL COMMENT '第三方平台授权流程准备的预授权码',
  `pre_auth_code_expires` char(16) DEFAULT NULL COMMENT '第三方平台授权流程准备的预授权码失效时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '0：关闭、1：开启',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '第三方平台安全TICKET更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='开放平台配置表';

-- ----------------------------
-- Table structure for pig_wx_authorize
-- ----------------------------
DROP TABLE IF EXISTS {{%wx_authorize}};
CREATE TABLE {{%wx_authorize}} (
  `author_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(11) unsigned NOT NULL COMMENT '授权配置ID',
  `authorizer_appid` char(64) DEFAULT NULL COMMENT '授权方APPID',
  `authorizer_access_token` varchar(255) DEFAULT NULL COMMENT '授权方接口调用凭据',
  `authorizer_access_token_expires` char(16) DEFAULT NULL COMMENT '授权方接口调用凭据有效期',
  `authorizer_refresh_token` varchar(255) DEFAULT NULL COMMENT '接口调用凭据刷新令牌',
  `func_info` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '公众号授权给开发者的权限集列表，ID为1到50',
  `auth_type` tinyint(2) DEFAULT '0' COMMENT '0：公众号；1：小程序',
  `auth_mini_type` tinyint(2) unsigned DEFAULT '0' COMMENT '0：普通；1：快速创建',
  `authorizer_type` char(16) DEFAULT NULL COMMENT '授权状态 unauthorized是取消授权，updateauthorized是更新授权，authorized是授权成功通知',
  `authorizer_code` varchar(255) DEFAULT NULL COMMENT '授权码，可用于换取公众号的接口调用凭据',
  `authorizer_code_expires` char(16) DEFAULT NULL COMMENT '授权码有效期',
  `pre_auth_code` varchar(255) DEFAULT NULL COMMENT '预授权码',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  PRIMARY KEY (`author_id`),
  KEY `KEY_WX_AUTHORIZE_CONFIG_ID` (`config_id`),
  KEY `KEY_WX_AUHORIZE_APPID` (`authorizer_appid`),
  CONSTRAINT `KEY_WX_AUTHORIZER_CONFIG_ID` FOREIGN KEY (`config_id`) REFERENCES {{%wx_authorize_config}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号、小程序基础信息表';

-- ----------------------------
-- Table structure for pig_wx_authorize_info
-- ----------------------------
DROP TABLE IF EXISTS {{%wx_authorize_info}};
CREATE TABLE {{%wx_authorize_info}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL COMMENT '授权信息ID',
  `nick_name` char(64) DEFAULT NULL COMMENT '授权方昵称',
  `head_img` text COMMENT '授权方头像',
  `service_type_info` tinyint(1) DEFAULT NULL COMMENT '授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号',
  `verify_type_info` tinyint(1) DEFAULT NULL COMMENT '授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证',
  `user_name` char(64) DEFAULT NULL COMMENT '授权方公众号的原始ID',
  `signature` varchar(255) DEFAULT NULL COMMENT '帐号介绍',
  `principal_name` char(64) DEFAULT NULL COMMENT '小程序的主体名称',
  `alias` char(64) DEFAULT NULL COMMENT '授权方公众号所设置的微信号，可能为空',
  `open_store` tinyint(1) DEFAULT NULL COMMENT '是否开通微信门店功能',
  `open_scan` tinyint(1) DEFAULT NULL COMMENT '是否开通微信扫商品功能',
  `open_pay` tinyint(1) DEFAULT NULL COMMENT '是否开通微信支付功能',
  `open_card` tinyint(1) DEFAULT NULL COMMENT '是否开通微信卡券功能',
  `open_shake` tinyint(1) DEFAULT NULL COMMENT '是否开通微信摇一摇功能',
  `qrcode_url` text COMMENT '二维码图片的URL',
  `qrcode_img` text COMMENT '二维码base64数据',
  `authorizer_appid` char(64) DEFAULT NULL COMMENT '授权方appid',
  `func_info` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '公众号授权给开发者的权限集列表，ID为1到50',
  `miniprograminfo` longtext COMMENT '可根据这个字段判断是否为小程序类型授权',
  `auth_type` tinyint(2) DEFAULT '0' COMMENT '0：公众号；1：小程序',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WX_AUTHORIZER_INFO_AUTHORID` (`author_id`),
  KEY `KEY_WX_AUTHORIZER_INFO_AUTHORIZERAPPID` (`authorizer_appid`),
  CONSTRAINT `KEY_WX_AUTHORIZER_INFO_AUTORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号、小程序详细信息表';

-- ----------------------------
-- Table structure for pig_user
-- ----------------------------
DROP TABLE IF EXISTS {{%user}};
CREATE TABLE {{%user}} (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` char(64) NOT NULL COMMENT '账户名',
  `password` varchar(255) NOT NULL COMMENT '加密后的密码',
  `salt` char(6) NOT NULL COMMENT '加密校验码',
  `access_token` varchar(255) DEFAULT NULL COMMENT '对接验证字符串',
  `access_token_expire` timestamp NULL DEFAULT NULL COMMENT '对接验证字符串失效时间戳',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`uid`),
  KEY `KEY_USER_ACCOUNT` (`account`),
  KEY `KEY_USER_PASSWORD` (`password`(16)),
  KEY `KEY_USER_SALT` (`salt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Table structure for pig_material
-- ----------------------------
DROP TABLE IF EXISTS {{%material}};
CREATE TABLE {{%material}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '素材ID',
  `media_id` char(64) DEFAULT NULL COMMENT '新增素材的media_id',
  `expire` char(16) DEFAULT NULL COMMENT '临时素材失效时间',
  `type` tinyint(1) NOT NULL COMMENT '素材有效期类型：0、临时素材；1、永久素材',
  `material_type` tinyint(1) NOT NULL COMMENT '素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、缩略图（thumb)、6：参数二维码（scene）',
  `article_sort` char(16) DEFAULT NULL COMMENT '图文素材的排序，多图文时用逗号分割',
  `file_name` char(16) DEFAULT NULL COMMENT '素材名称',
  `file_length` char(8) DEFAULT NULL COMMENT '素材大小',
  `content_type` char(16) DEFAULT NULL COMMENT '素材类型',
  `title` char(64) DEFAULT NULL COMMENT '视频素材的标题',
  `introduction` varchar(128) DEFAULT NULL COMMENT '视频素材的描述',
  `local_path` text COMMENT '素材本地地址',
  `yun_url` text COMMENT '素材云端地址',
  `wx_url` text COMMENT '素材微信地址',
  `created_at` char(16) DEFAULT NULL COMMENT '媒体文件上传时间戳',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='素材表';

-- ----------------------------
-- Table structure for pig_scene
-- ----------------------------
DROP TABLE IF EXISTS {{%scene}};
CREATE TABLE {{%scene}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）',
  `action_name` char(32) NOT NULL COMMENT '二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值',
  `scene_str` char(64) DEFAULT NULL COMMENT '场景值ID（字符串形式的ID），字符串类型，长度限制为1到64',
  `scene_expire` char(16) DEFAULT NULL COMMENT '该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。',
  `ticket` varchar(128) DEFAULT NULL COMMENT '获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。',
  `url` text COMMENT '二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_SCENE_SCENESTR` (`scene_str`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='参数二维码表';

-- ----------------------------
-- Table structure for pig_fans
-- ----------------------------
DROP TABLE IF EXISTS {{%fans}};
CREATE TABLE {{%fans}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL COMMENT '公众号ID',
  `subscribe` tinyint(1) unsigned NOT NULL COMMENT '用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。',
  `openid` char(80) DEFAULT NULL COMMENT '用户的标识，对当前公众号唯一',
  `nickname` varchar(128) DEFAULT NULL COMMENT '用户的昵称',
  `sex` tinyint(1) DEFAULT NULL COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `city` char(32) DEFAULT NULL COMMENT '用户所在城市',
  `country` char(32) DEFAULT NULL COMMENT '用户所在国家',
  `province` char(32) DEFAULT NULL COMMENT '用户所在省份',
  `language` char(16) DEFAULT NULL COMMENT '用户的语言，简体中文为zh_CN',
  `headerimg` varchar(255) DEFAULT NULL COMMENT '用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。',
  `subscribe_time` char(16) DEFAULT NULL COMMENT '用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间',
  `unionid` char(80) DEFAULT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。',
  `remark` varchar(255) DEFAULT NULL COMMENT '公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注',
  `groupid` char(80) DEFAULT NULL COMMENT '用户所在的分组ID（兼容旧的用户分组接口）',
  `targid_list` char(80) DEFAULT NULL COMMENT '用户被打上的标签ID列表',
  `subscribe_scene` char(64) DEFAULT NULL COMMENT '返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENEPROFILE LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他',
  `qr_scene` int(11) unsigned DEFAULT NULL COMMENT '二维码扫码场景（开发者自定义）',
  `qr_scene_str` varchar(255) DEFAULT NULL COMMENT '二维码扫码场景描述（开发者自定义）',
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_FANS_AUTHORID` (`author_id`),
  KEY `KEY_FANS_OPENID` (`openid`),
  KEY `KEY_FANS_SCENEID` (`qr_scene`),
  CONSTRAINT `KEY_FANS_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_FANS_SCENEID` FOREIGN KEY (`qr_scene`) REFERENCES {{%scene}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='粉丝表';

-- ----------------------------
-- Table structure for pig_kf_user
-- ----------------------------
DROP TABLE IF EXISTS {{%kf_user}};
CREATE TABLE {{%kf_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `kf_id` int(11) unsigned DEFAULT NULL COMMENT '客服工号',
  `kf_account` char(80) DEFAULT NULL COMMENT '完整客服账号，格式为：账号前缀@公众号微信号',
  `kf_nick` char(32) DEFAULT NULL COMMENT '客服昵称',
  `kf_wx` char(16) DEFAULT NULL COMMENT '客服微信号',
  `kf_headimgurl` text COMMENT '客服头像',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_KF_USER_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_KF_USER_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客服表';

-- ----------------------------
-- Table structure for pig_area
-- ----------------------------
DROP TABLE IF EXISTS {{%area}};
CREATE TABLE {{%area}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL COMMENT '父级ID',
  `sid` int(10) unsigned NOT NULL COMMENT '原始的ID',
  `name` char(32) DEFAULT NULL COMMENT '城市名称',
  `full_name` char(64) DEFAULT NULL COMMENT '城市全称',
  `pinyin` char(64) DEFAULT NULL COMMENT '城市名称拼音',
  `lng` decimal(10,6) NOT NULL COMMENT '经度',
  `lat` decimal(10,6) NOT NULL COMMENT '纬度',
  `level` tinyint(1) unsigned NOT NULL COMMENT '级别',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_AREA_PARENTID` (`parent_id`),
  KEY `KEY_AREA_SID` (`sid`),
  KEY `KEY_AREA_PINYIN` (`pinyin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='区域表';

-- ----------------------------
-- Table structure for pig_article
-- ----------------------------
DROP TABLE IF EXISTS {{%article}};
CREATE TABLE {{%article}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `material_id` int(11) unsigned DEFAULT NULL COMMENT '素材ID',
  `title` char(64) DEFAULT NULL COMMENT '标题',
  `thumb_media_id` int(11) unsigned DEFAULT NULL COMMENT '图文消息的封面图片素材id（必须是永久mediaID）',
  `author` char(16) DEFAULT NULL COMMENT '作者',
  `digest` varchar(255) DEFAULT NULL COMMENT '图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前64个字。',
  `show_cover_pic` tinyint(1) unsigned DEFAULT '0' COMMENT '是否显示封面，0为false，即不显示，1为true，即显示',
  `content` text NOT NULL COMMENT '图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源 "上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。',
  `wx_content` text COMMENT '图文消息的具体内容（微信）',
  `content_source_url` varchar(255) DEFAULT NULL COMMENT '图文消息的原文地址，即点击“阅读原文”后的URL',
  `need_open_comment` tinyint(1) unsigned DEFAULT '1' COMMENT 'Uint32 是否打开评论，0不打开，1打开',
  `only_fans_can_comment` tinyint(1) unsigned DEFAULT '0' COMMENT 'Uint32 是否粉丝才可评论，0所有人可评论，1粉丝才可评论',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_ARTICLE_MATERIALID` (`material_id`),
  KEY `KEY_ARTICLE_THUMBMEDIAID` (`thumb_media_id`),
  CONSTRAINT `KEY_ARTICLE_MATERIALID` FOREIGN KEY (`material_id`) REFERENCES {{%material}} (`id`),
  CONSTRAINT `KEY_ARTICLE_THUMBMEDIAID` FOREIGN KEY (`thumb_media_id`) REFERENCES {{%material}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图文消息表';

-- ----------------------------
-- Table structure for pig_attachment
-- ----------------------------
DROP TABLE IF EXISTS {{%attachment}};
CREATE TABLE {{%attachment}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `file_type` tinyint(1) DEFAULT NULL COMMENT '附件类型，1：图片、2：音频、3：视频',
  `file_name` char(64) DEFAULT NULL COMMENT '附件名称',
  `file_content_type` char(16) DEFAULT NULL COMMENT '附件类型',
  `file_length` int(11) DEFAULT NULL COMMENT '附件大小',
  `file_width` decimal(11,3) unsigned DEFAULT NULL COMMENT '图片附件宽度',
  `file_height` decimal(11,3) unsigned DEFAULT NULL COMMENT '图片附件高度',
  `local_path` text COMMENT '附件本地地址',
  `yun_url` text COMMENT '附件云端地址',
  `wx_url` text COMMENT '附件微信地址',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_ATTACNMENT_UID` (`uid`),
  CONSTRAINT `KEY_ATTACNMENT_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户附件表';

-- ----------------------------
-- Table structure for pig_auto_reply
-- ----------------------------
DROP TABLE IF EXISTS {{%auto_reply}};
CREATE TABLE {{%auto_reply}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `replay_type` tinyint(1) unsigned DEFAULT NULL COMMENT '自动回复分类，1：关注后自动回复、2：消息自动回复',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '是否开启，0代表未开启，1代表开启',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_REPLY_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_REPLY_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='自动回复表';

-- ----------------------------
-- Table structure for pig_blacklist
-- ----------------------------
DROP TABLE IF EXISTS {{%blacklist}};
CREATE TABLE {{%blacklist}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL COMMENT '公众号ID',
  `openid` char(80) NOT NULL COMMENT '用户的标识，对当前公众号唯',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_BLACKLIST_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_BLACKLIST_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='黑名单表';

-- ----------------------------
-- Table structure for pig_fans_msg
-- ----------------------------
DROP TABLE IF EXISTS {{%fans_msg}};
CREATE TABLE {{%fans_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fans_id` int(11) unsigned DEFAULT NULL COMMENT '粉丝ID',
  `kf_id` int(11) unsigned DEFAULT NULL COMMENT '客服ID',
  `from` tinyint(1) DEFAULT NULL COMMENT '发送方，1：粉丝、2：用户、3：客服',
  `to` tinyint(1) DEFAULT NULL COMMENT '接收方，1：粉丝、2：用户、3：客服',
  `content` text COMMENT '消息内容',
  `content_type` tinyint(1) unsigned DEFAULT NULL COMMENT '消息类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）',
  `material_id` int(11) unsigned DEFAULT NULL COMMENT '素材ID',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_FANS_MSG_FANSID` (`fans_id`),
  KEY `KEY_FANS_MSG_KFID` (`kf_id`),
  CONSTRAINT `KEY_FANS_MSG_FANSID` FOREIGN KEY (`fans_id`) REFERENCES {{%fans}} (`id`),
  CONSTRAINT `KEY_FANS_MSG_KFID` FOREIGN KEY (`kf_id`) REFERENCES {{%kf_user}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='粉丝留言信息表';

-- ----------------------------
-- Table structure for pig_fans_time_line
-- ----------------------------
DROP TABLE IF EXISTS {{%fans_time_line}};
CREATE TABLE {{%fans_time_line}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fans_id` int(11) unsigned DEFAULT NULL COMMENT '粉丝ID',
  `openid` char(80) DEFAULT NULL COMMENT '用户的标识，对当前公众号唯一',
  `event` text COMMENT '行为',
  `event_time` timestamp NULL DEFAULT NULL COMMENT '行为发生时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_FANS_TIME_LINE_FANSID` (`fans_id`),
  CONSTRAINT `KEY_FANS_TIME_LINE_FANSID` FOREIGN KEY (`fans_id`) REFERENCES {{%fans}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='粉丝行为轨迹表';

-- ----------------------------
-- Table structure for pig_high_level_push_msg
-- ----------------------------
DROP TABLE IF EXISTS {{%high_level_push_msg}};
CREATE TABLE {{%high_level_push_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `msg_title` char(32) DEFAULT NULL COMMENT '消息名称',
  `msg_type` tinyint(1) unsigned DEFAULT NULL COMMENT '类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）',
  `content` text COMMENT '文本消息内容',
  `material_id` int(11) unsigned DEFAULT NULL COMMENT '素材ID',
  `to_wx` tinyint(1) DEFAULT NULL COMMENT '出现在微信后台已群发消息，0：不出现、1：出现',
  `push_type` tinyint(1) unsigned DEFAULT NULL COMMENT '发送类别：1：全部粉丝、2：标签、3：性别、4：自定义',
  `push_rule` text COMMENT '发送条件（json格式）',
  `push_time` timestamp NULL DEFAULT NULL COMMENT '发送时间',
  `notice_phone` char(32) DEFAULT NULL COMMENT '发送失败的通知手机号',
  `continue` tinyint(1) unsigned DEFAULT NULL COMMENT '若有文章判定为转载，继续群发若有文章判定为转载，继续群发，0：不继续、1：继续',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_HIGH_LEVEL_PUSH_MSG_AUTHORID` (`author_id`),
  KEY `KEY_HIGH_LEVEL_PUSH_MSG_MATERIALID` (`material_id`),
  CONSTRAINT `KEY_HIGH_LEVEL_PUSH_MSG_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_HIGH_LEVEL_PUSH_MSG_MATERIALID` FOREIGN KEY (`material_id`) REFERENCES {{%material}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='高级群发推送表';

-- ----------------------------
-- Table structure for pig_keyword
-- ----------------------------
DROP TABLE IF EXISTS {{%keyword}};
CREATE TABLE {{%keyword}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `rule_name` char(64) DEFAULT NULL COMMENT '规则名称',
  `reply_mode` tinyint(1) DEFAULT NULL COMMENT '回复模式，1：reply_all代表全部回复，2：random_one代表随机回复其中一条',
  `keyword` char(32) DEFAULT NULL COMMENT '关键词',
  `match_mode` tinyint(1) DEFAULT NULL COMMENT '匹配模式，1：contain代表消息中含有该关键词即可，2：equal表示消息内容必须和关键词严格相同',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '是否开启，0代表未开启，1代表开启',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_KEYWORD_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_KEYWORD_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='关键词表';

-- ----------------------------
-- Table structure for pig_kf_push_msg
-- ----------------------------
DROP TABLE IF EXISTS {{%kf_push_msg}};
CREATE TABLE {{%kf_push_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `msg_title` char(32) DEFAULT NULL COMMENT '消息名称',
  `msg_type` tinyint(1) unsigned DEFAULT NULL COMMENT '类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）',
  `content` text COMMENT '文本回复的内容',
  `material_id` int(11) unsigned DEFAULT NULL COMMENT '素材ID',
  `title` char(64) DEFAULT NULL COMMENT '图文消息的标题',
  `digest` varchar(255) DEFAULT NULL COMMENT '图文消息的摘要',
  `cover_url` text COMMENT '封面图片的URL',
  `content_url` text COMMENT '正文的URL',
  `push_type` tinyint(1) unsigned DEFAULT NULL COMMENT '发送类别：1：全部粉丝、2：标签、3：性别、4：自定义',
  `push_rule` text COMMENT '发送条件（json格式）',
  `push_time` timestamp NULL DEFAULT NULL COMMENT '发送时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_KF_PUSH_MSG_AUTHORID` (`author_id`),
  KEY `KEY_KF_PUSH_MSG_MATERIALID` (`material_id`),
  CONSTRAINT `KEY_KF_PUSH_MSG_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_KF_PUSH_MSG_MATERIALID` FOREIGN KEY (`material_id`) REFERENCES {{%material}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客服消息推送表';

-- ----------------------------
-- Table structure for pig_location
-- ----------------------------
DROP TABLE IF EXISTS {{%location}};
CREATE TABLE {{%location}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fans_id` int(11) unsigned NOT NULL COMMENT '粉丝ID',
  `lng` decimal(10,6) NOT NULL COMMENT '经度',
  `lat` decimal(10,6) NOT NULL COMMENT '纬度',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_LOCATION_FANSID` (`fans_id`),
  CONSTRAINT `KEY_LOCATION_FANSID` FOREIGN KEY (`fans_id`) REFERENCES {{%fans}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户地理位置表';

-- ----------------------------
-- Table structure for pig_quick_msg
-- ----------------------------
DROP TABLE IF EXISTS {{%quick_msg}};
CREATE TABLE {{%quick_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `content` text COMMENT '快捷回复内容',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_QUICK_MSG_UID` (`uid`),
  KEY `KEY_QUICK_MSG_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_QUICK_MSG_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_QUICK_MSG_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快捷回复表';

-- ----------------------------
-- Table structure for pig_reply_info
-- ----------------------------
DROP TABLE IF EXISTS {{%reply_info}};
CREATE TABLE {{%reply_info}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kw_id` int(11) unsigned DEFAULT NULL COMMENT '关键词ID',
  `rp_id` int(11) unsigned DEFAULT NULL COMMENT '自动回复ID',
  `scene_id` int(11) unsigned DEFAULT NULL COMMENT '参数二维码ID',
  `type` tinyint(1) NOT NULL COMMENT '回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）',
  `content` text COMMENT '对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID',
  `material_id` int(11) unsigned DEFAULT NULL COMMENT '素材库ID',
  `title` char(64) DEFAULT NULL COMMENT '图文消息的标题',
  `digest` varchar(255) DEFAULT NULL COMMENT '图文消息的摘要',
  `author` char(16) DEFAULT NULL COMMENT '图文消息的作者',
  `show_cover` tinyint(1) DEFAULT NULL COMMENT '是否显示封面，0为不显示，1为显示',
  `cover_url` text COMMENT '封面图片的URL',
  `content_url` text COMMENT '正文的URL',
  `source_url` text COMMENT '原文的URL，若置空则无查看原文入口',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '是否开启，0代表未开启，1代表开启',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_REPLY_INFO_KWID` (`kw_id`),
  KEY `KEY_REPLY_INFO_RPID` (`rp_id`),
  KEY `KEY_REPLY_INFO_SCENEID` (`scene_id`),
  KEY `KEY_REPLY_INFO_MATERIALID` (`material_id`),
  CONSTRAINT `KEY_REPLY_INFO_KWID` FOREIGN KEY (`kw_id`) REFERENCES {{%keyword}} (`id`),
  CONSTRAINT `KEY_REPLY_INFO_MATERIALID` FOREIGN KEY (`material_id`) REFERENCES {{%material}} (`id`),
  CONSTRAINT `KEY_REPLY_INFO_RPID` FOREIGN KEY (`rp_id`) REFERENCES {{%auto_reply}} (`id`),
  CONSTRAINT `KEY_REPLY_INFO_SCENEID` FOREIGN KEY (`scene_id`) REFERENCES {{%scene}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='回复详细表';

-- ----------------------------
-- Table structure for pig_sub_user
-- ----------------------------
DROP TABLE IF EXISTS {{%sub_user}};
CREATE TABLE {{%sub_user}} (
  `sub_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
  `account` char(64) NOT NULL COMMENT '子账户名',
  `password` varchar(255) NOT NULL COMMENT '加密后的密码',
  `salt` char(6) NOT NULL COMMENT '加密校验码',
  `access_token` varchar(255) DEFAULT NULL COMMENT '对接验证字符串',
  `access_token_expire` timestamp NULL DEFAULT NULL COMMENT '对接验证字符串失效时间戳',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`sub_id`),
  KEY `KEY_SUB_USER_UID` (`uid`),
  KEY `KEY_SUB_USER_ACCOUNT` (`account`),
  KEY `KEY_SUB_USER_PASSWORD` (`password`(16)),
  KEY `KEY_SUBUSER_SALT` (`salt`),
  CONSTRAINT `KEY_SUB_USER_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='子账户表';

-- ----------------------------
-- Table structure for pig_tags
-- ----------------------------
DROP TABLE IF EXISTS {{%tags}};
CREATE TABLE {{%tags}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `tag_id` int(111) unsigned DEFAULT NULL COMMENT '标签ID',
  `name` char(64) DEFAULT NULL COMMENT '标签名',
  `count` int(11) unsigned DEFAULT NULL COMMENT '此标签下粉丝数',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_TAGS_AUTHORID` (`author_id`),
  KEY `KEY_TAGS_NAME` (`name`),
  CONSTRAINT `KEY_TAGS_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签表';

-- ----------------------------
-- Table structure for pig_template
-- ----------------------------
DROP TABLE IF EXISTS {{%template}};
CREATE TABLE {{%template}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `template_id_short` char(32) DEFAULT NULL COMMENT '模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式',
  `template_id` char(64) DEFAULT NULL COMMENT '模板ID',
  `title` char(64) DEFAULT NULL COMMENT '模板标题',
  `primary_industry` char(64) DEFAULT NULL COMMENT '模板所属行业的一级行业',
  `deputy_industry` char(64) DEFAULT NULL COMMENT '模板所属行业的二级行业',
  `content` text COMMENT '模板内容',
  `example` text COMMENT '模板示例',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_TEMPLATE_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_TEMPLATE_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模板库';

-- ----------------------------
-- Table structure for pig_template_push_msg
-- ----------------------------
DROP TABLE IF EXISTS {{%template_push_msg}};
CREATE TABLE {{%template_push_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '公众号ID',
  `msg_title` char(32) DEFAULT NULL COMMENT '消息名称',
  `template_id` int(11) unsigned DEFAULT NULL COMMENT '模板消息ID',
  `template_data` text COMMENT '模板消息的data（json格式）',
  `redirect_type` tinyint(1) unsigned DEFAULT NULL COMMENT '跳转类型，1：链接、2：小程序',
  `url` text COMMENT '模板跳转链接（海外帐号没有跳转能力）',
  `appid` char(64) DEFAULT NULL COMMENT '所需跳转到的小程序appid（该小程序appid必须与发模板消息的公众号是绑定关联关系，暂不支持小游戏）',
  `pagepath` varchar(255) DEFAULT NULL COMMENT '所需跳转到小程序的具体页面路径，支持带参数,（示例index?foo=bar），要求该小程序已发布，暂不支持小游戏',
  `push_type` tinyint(1) unsigned DEFAULT NULL COMMENT '发送类别：1：全部粉丝、2：标签、3：性别、4：自定义',
  `push_rule` text COMMENT '发送条件（json格式）',
  `push_time` timestamp NULL DEFAULT NULL COMMENT '发送时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_TEMPLATE_PUSH_MSG_AUTHORID` (`author_id`),
  KEY `KEY_TEMPLATE_PUSH_MSG_TEMPLATEID` (`template_id`),
  CONSTRAINT `KEY_TEMPLATE_PUSH_MSG_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_TEMPLATE_PUSH_MSG_TEMPLATEID` FOREIGN KEY (`template_id`) REFERENCES {{%template}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模板消息群发推送表';

-- ----------------------------
-- Table structure for pig_user_author_relation
-- ----------------------------
DROP TABLE IF EXISTS {{%user_author_relation}};
CREATE TABLE {{%user_author_relation}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '授权ID',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_USER_AUTHOR_RELATION_UID` (`uid`),
  KEY `KEY_USER_AUTHOR_RELATION_AUTHORID` (`author_id`),
  CONSTRAINT `KEY_USER_AUTHOR_RELATION_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_USER_AUTHOR_RELATION_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户公众号、小程序关系表';

-- ----------------------------
-- Table structure for pig_user_profile
-- ----------------------------
DROP TABLE IF EXISTS {{%user_profile}};
CREATE TABLE {{%user_profile}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
  `nick_name` char(64) DEFAULT NULL COMMENT '昵称',
  `avatar` blob COMMENT '头像',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_USER_PROFILE_UID` (`uid`),
  CONSTRAINT `KEU_USER_PROFILE_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户资料表';

SET FOREIGN_KEY_CHECKS = 1;
SQL;

    	$this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190905_081906_crm_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190905_081906_crm_init cannot be reverted.\n";

        return false;
    }
    */
}
