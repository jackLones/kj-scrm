<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/2/29
	 * Time: 15:20
	 */

	namespace app\util;

	class WorkConstUtil
	{
		/**
		 * 注册完成回调register_corp
		 */
		const REGISTER_CORP = 'register_corp';

		/**
		 * 第三方推送suite_ticket
		 */
		const SUITE_TICKET = 'suite_ticket';

		/**
		 * 成员变更
		 */
		const CHANGE_CONTACT = 'change_contact';
		/**
		 * 异步任务完成
		 */
		const BATCH_JOB_RESULT = 'batch_job_result';
		/**
		 * 外部联系人管理
		 */
		const CHANGE_EXTERNAL_CONTACT = 'change_external_contact';
		/**
		 * 客户群变更
		 */
		const CHANGE_EXTERNAL_CHAT = 'change_external_chat';

		/**
		 * 新增成员
		 */
		const CREATE_USER = 'create_user';
		/**
		 * 更新成员
		 */
		const UPDATE_USER = 'update_user';
		/**
		 * 删除成员
		 */
		const DELETE_USER = 'delete_user';
		/**
		 * 新增部门
		 */
		const CREATE_PARTY = 'create_party';
		/**
		 * 更新部门
		 */
		const UPDATE_PARTY = 'update_party';
		/**
		 * 删除部门
		 */
		const DELETE_PARTY = 'delete_party';
		/**
		 * 标签成员变更
		 */
		const UPDATE_TAG = 'update_tag';
		/**
		 * 添加企业客户
		 */
		const ADD_EXTERNAL_CONTACT = 'add_external_contact';
		/**
		 * 外部联系人免验证添加成员
		 */
		const ADD_HALF_EXTERNAL_CONTACT = 'add_half_external_contact';
		/**
		 * 删除企业客户
		 */
		const DEL_EXTERNAL_CONTACT = 'del_external_contact';
		/**
		 * 删除跟进成员
		 */
		const DEL_FOLLOW_USER = 'del_follow_user';
		/**
		 * 外部联系人的备注、手机号或标签
		 */
		const EDIT_EXTERNAL_CONTACT = 'edit_external_contact';
		/**
		 * 客户接替失败事件
		 */
		const TRANSFER_FAIL = 'transfer_fail';
		/**
		 * 文本消息
		 */
		const AGENT_MSG_TEXT_TYPE = 'text';
		/**
		 * 图片消息
		 */
		const AGENT_MSG_IMAGE_TYPE = 'image';
		/**
		 * 语音消息
		 */
		const AGENT_MSG_VOICE_TYPE = 'voice';
		/**
		 * 视频消息
		 */
		const AGENT_MSG_VIDEO_TYPE = 'video';
		/**
		 * 地理位置消息
		 */
		const AGENT_MSG_LOCATION_TYPE = 'location';
		/**
		 * 链接消息
		 */
		const AGENT_MSG_LINK_TYPE = 'link';
		/**
		 * 事件推送
		 */
		const AGENT_MSG_EVENT_TYPE = 'event';
		/**
		 * 事件类型
		 */
		const EVENT_SUBSCRIBE = 'subscribe';//成员关注
		const EVENT_UNSUBSCRIBE = 'unsubscribe';//成员取消关注

		const MSG_AUDIT_NOT_FINISH = 0;
		const MSG_AUDIT_IS_FINISH = 1;
	}