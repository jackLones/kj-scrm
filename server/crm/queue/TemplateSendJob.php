<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/14
	 * Time: 16:43
	 */

	namespace app\queue;

	use app\models\FansTimeLine;
	use app\models\TemplatePushInfo;
	use app\models\TemplatePushMsg;
	use app\models\WxAuthorize;
	use app\util\DateUtil;
	use app\util\SUtils;
	use callmez\wechat\sdk\Wechat;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class TemplateSendJob extends BaseObject implements JobInterface
	{
		public $pushInfoId;
		public $appid;
		public $fans;
		public $templateId;
		public $templateData;
		public $url;
		public $miniprogram;
		public $templatePushMsgId;
		public $title;
		public $end = false;

		public function execute ($queue)
		{
			$templatePushMsg = TemplatePushMsg::findOne($this->templatePushMsgId);
			if (empty($templatePushMsg)) {
				return false;
			}

			try {
				if (!$this->end) {
					if (empty($this->pushInfoId)) {
						return false;
					}

					$templatePushInfo = TemplatePushInfo::findOne($this->pushInfoId);
					if (empty($templatePushInfo)) {
						return false;
					}

					$templatePushInfo->status    = TemplatePushInfo::SENDING;
					$templatePushInfo->send_time = DateUtil::getCurrentTime();
					$templatePushInfo->update();

					$wxAuthorize = WxAuthorize::getTokenInfo($this->appid, false, true);
					if (empty($wxAuthorize)) {
						return false;
					}

					/** @var Wechat $wechat */
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $this->appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					$result = $wechat->sendTemplateMessage($this->fans['openid'], $this->templateId, $this->templateData, $this->url, $this->miniprogram, $templatePushMsg->redirect_type);
					\Yii::error($result, __CLASS__ . '-' . __FUNCTION__);

					if ($result['errcode'] == 0) {
						//插入粉丝轨迹
						FansTimeLine::create($this->fans['id'], 'template', time(), 0, 0, $this->title);

						$templatePushInfo->message_id   = $result['msgid'];
						$templatePushInfo->status       = TemplatePushInfo::SEND_SUCCESS;
						$templatePushInfo->queue_id     = 0;
						$templatePushInfo->success_time = DateUtil::getCurrentTime();

						if (!$templatePushInfo->validate() || !$templatePushInfo->save()) {
							\Yii::error(SUtils::modelError($templatePushInfo), __CLASS__ . '-' . __FUNCTION__ . 'model');
						}
					} else {
						if (empty($templatePushMsg->msg_id)) {
							$templatePushInfo->status    = TemplatePushInfo::SEND_FAILED;
							$templatePushInfo->queue_id  = 0;
							$templatePushInfo->errcode   = $result['errcode'];
							$templatePushInfo->errmsg    = $result['errmsg'];
							$templatePushInfo->send_time = DateUtil::getCurrentTime();

							if (!$templatePushInfo->validate() || !$templatePushInfo->save()) {
								\Yii::error(SUtils::modelError($templatePushInfo), __CLASS__ . '-' . __FUNCTION__ . 'model');
							}
						}
					}
				} else {
					$fansNum = TemplatePushInfo::find()->where(['template_id' => $templatePushMsg->id, 'status' => [TemplatePushInfo::SEND_SUCCESS, TemplatePushInfo::SENDING]])->count();

					if ($fansNum > 0) {
						$templatePushMsg->status     = 1;
						$templatePushMsg->error_code = '';
						$templatePushMsg->error_msg  = '';
					} else {
						$templatePushMsg->status = 2;
					}

					$templatePushMsg->queue_id = 0;
					$templatePushMsg->save();
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'SendJob-error');
			}
		}
	}