<?php
	/**
	 * Created by PhpStorm.
	 * User: Dove
	 * Date: 2019-09-10
	 * Time: 13:22
	 */

	namespace app\util;

	class WxConstUtil
	{
		/**
		 * 文本消息
		 */
		const WX_MSG_TEXT_TYPE = 'text';
		/**
		 * 图片消息
		 */
		const WX_MSG_IMAGE_TYPE = 'image';
		/**
		 * 语音消息
		 */
		const WX_MSG_VOICE_TYPE = 'voice';
		/**
		 * 视频消息
		 */
		const WX_MSG_VIDEO_TYPE = 'video';
		/**
		 * 小视频消息
		 */
		const WX_MSG_SHORT_VIDEO_TYPE = 'shortvideo';
		/**
		 * 地理位置消息
		 */
		const WX_MSG_LOCATION_TYPE = 'location';
		/**
		 * 链接消息
		 */
		const WX_MSG_LINK_TYPE = 'link';
		/**
		 * 小程序卡片消息
		 */
		const MINI_MSG_MINIPROGRAMPAGE_TYPE = 'miniprogrampage';

		/**
		 * 事件推送
		 */
		const WX_MSG_EVENT_TYPE = 'event';

		/**
		 * 用户关注事件
		 */
		const WX_SUBSCRIBE_EVENT = 'subscribe';
		/**
		 * 用户关注事件
		 */
		const WX_VIEW_MINIPROGRAM = 'view_miniprogram';
		/**
		 * 用户取消关注
		 */
		const WX_UN_SUBSCRIBE_EVENT = 'unsubscribe';
		/**
		 * 扫描带参数二维码事件
		 * 用户已关注时的事件推送
		 */
		const WX_SCAN_EVENT = 'scan';
		/**
		 * 上报地理位置事件
		 */
		const WX_LOCATION_EVENT = 'location';

		/**
		 * 进入会话事件
		 */
		const MINI_USER_ENTER_TEMPSESSION = 'user_enter_tempsession';

		/**
		 * 自定义菜单事件推送
		 *
		 * 点击菜单拉取消息时的事件推送
		 */
		const WX_CLICK_EVENT = 'click';
		/**
		 * 点击菜单跳转链接时的事件推送
		 */
		const WX_VIEW_EVENT = 'view';
		/**
		 * 扫码推事件的事件推送
		 */
		const WX_SCAN_CODE_PUSH_EVENT = 'scancode_push';
		/**
		 * 扫码推事件且弹出“消息接收中”提示框的事件推送
		 */
		const WX_SCAN_CODE_WAIT_MSG_EVENT = 'scancode_waitmsg';
		/**
		 * 弹出系统拍照发图的事件推送
		 */
		const WX_PIC_SYS_PHOTO_EVENT = 'pic_sysphoto';
		/**
		 * 弹出拍照或者相册发图的事件推送
		 */
		const WX_PIC_PHOTO_OR_ALBUM_EVENT = 'pic_photo_or_album';
		/**
		 * 弹出微信相册发图器的事件推送
		 */
		const WX_PIC_WEIXIN_EVENT = 'pic_weixin';
		/**
		 * 弹出地理位置选择器的事件推送
		 */
		const WX_LOCATION_SELECT_EVENT = 'location_select';

		/**
		 * 微信认证事件推送
		 *
		 * 资质认证成功（此时立即获得接口权限）
		 */
		const WX_QUALIFICATION_VERIFY_SUCCESS_EVENT = 'qualification_verify_success';
		/**
		 * 资质认证失败
		 */
		const WX_QUALIFICATION_VERIFY_FAIL_EVENT = 'qualification_verify_fail';
		/**
		 * 名称认证成功（即命名成功）
		 */
		const WX_NAMING_VERIFY_SUCCESS_EVENT = 'naming_verify_success';
		/**
		 * 名称认证失败（这时虽然客户端不打勾，但仍有接口权限）
		 */
		const WX_NAMING_VERIFY_FAIL_EVENT = 'naming_verify_fail';
		/**
		 * 年审通知
		 */
		const WX_ANNUAL_RENEW_EVENT = 'annual_renew';
		/**
		 * 年审通知
		 */
		const WX_VERIFY_EXPIRED_EVENT = 'verify_expired';

		/**
		 * 卡券事件推送
		 *
		 * 审核事件推送(卡券通过审核)
		 */
		const WX_CARD_PASS_CHECK_EVENT = 'card_pass_check';
		/**
		 * 审核事件推送(卡券未通过审核)
		 */
		const WX_CARD_NOT_PASS_CHECK_EVENT = 'card_not_pass_check';
		/**
		 * 领取事件推送
		 */
		const WX_USER_GET_CARD_EVENT = 'user_get_card';
		/**
		 * 转赠事件推送
		 */
		const WX_USER_GIFTING_CARD_EVENT = 'user_gifting_card';
		/**
		 * 删除事件推送
		 */
		const WX_USER_DEL_CARD_EVENT = 'user_del_card';
		/**
		 * 核销事件推送
		 */
		const WX_USER_CONSUME_CARD_EVENT = 'user_consume_card';
		/**
		 * 买单事件推送
		 */
		const WX_USER_PAY_FROM_PAY_CELL_EVENT = 'user_pay_from_pay_cell';
		/**
		 * 进入会员卡事件推送
		 */
		const WX_USER_VIEW_CARD_EVENT = 'user_view_card';
		/**
		 * 从卡券进入公众号会话事件推送
		 */
		const WX_ENTER_SESSION_FROM_CARD_EVENT = 'user_enter_session_from_card';
		/**
		 * 会员卡内容更新事件
		 */
		const WX_UPDATE_MEMBER_CARD_EVENT = 'update_member_card';
		/**
		 * 库存报警事件
		 */
		const WX_CARD_SKU_REMIND_EVENT = 'card_sku_remind';
		/**
		 * 券点流水详情事件
		 */
		const WX_CARD_PAY_ORDER_EVENT = 'card_pay_order';
		/**
		 * 会员卡激活事件推送
		 */
		const WX_SUBMIT_MEMBERCARD_USER_INFO_EVENT = 'submit_membercard_user_info';

		/**
		 * 审核事件推送
		 */
		const WX_POI_CHECK_NOTIFY_EVENT = 'poi_check_notify';

		/**
		 * 摇一摇事件通知
		 */
		const WX_SHAKEAR_ROUND_USER_SHAKE_EVENT = 'shakearoundusershake';

		/**
		 * 模板消息发送成功通知
		 */
		const WX_TEMPLATESENDJOBFINISH = 'templatesendjobfinish';

		/**
		 * 群发信息发送成功通知
		 */
		const WX_MASSSENDJOBFINISH = 'masssendjobfinish';

		/**
		 * 小程序门店审核
		 */
		const WX_ADD_STORE_AUDIT_INFO_EVENT = 'add_store_audit_info';

		/**
		 * 小程序审核成功
		 */
		const WX_WEAPP_AUDIT_SUCCESS = 'weapp_audit_success';

		/**
		 * 小程序审核失败
		 */
		const WX_WEAPP_AUDIT_FAIL = 'weapp_audit_fail';

		/**
		 * 小程序名称设置及改名
		 */
		const WX_WXA_NICKNAME_AUDIT = 'wxa_nickname_audit';

		/**
		 * 回复文本消息
		 */
		const WX_REPLY_TEXT = 'text';
		/**
		 * 回复图片消息
		 */
		const WX_REPLY_IMAGE = 'image';
		/**
		 * 回复语音消息
		 */
		const WX_REPLY_VOICE = 'voice';
		/**
		 * 回复视频消息
		 */
		const WX_REPLY_VIDEO = 'video';
		/**
		 * 回复音乐消息
		 */
		const WX_REPLY_MUSIC = 'music';
		/**
		 * 回复图文消息
		 */
		const WX_REPLY_NEWS = 'news';

		/**
		 * 消息转发到客服
		 */
		const WX_TRANSFER_CUSTOMER_SERVICE = 'transfer_customer_service';

		/**
		 * 临时二维码
		 */
		const WX_QR_SCENE = 'QR_SCENE';
		/**
		 * 永久二维码
		 */
		const WX_QR_LIMIT_SCENE = 'QR_LIMIT_SCENE';
		/**
		 * 字符串形式的永久二维码
		 */
		const WX_QR_LIMIT_STR_SCENE = 'QR_LIMIT_STR_SCENE';
	}