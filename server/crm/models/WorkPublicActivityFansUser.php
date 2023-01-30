<?php

	namespace app\models;

	use app\components\ForbiddenException;
	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\components\NotAllowException;
	use app\util\MsgUtil;
	use app\util\ShortUrlUtil;
	use app\util\SUtils;
	use app\util\WorkPublicPoster;
	use app\util\WorkUtils;
	use app\util\WxPay\RedPacketPay;
	use app\util\WxPay\WxPayException;
	use callmez\wechat\sdk\Wechat;
	use Matrix\Exception;
	use Throwable;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\db\StaleObjectException;
	use yii\helpers\Url;

	/**
	 * This is the model class for table "{{%work_public_activity_fans_user}}".
	 *
	 * @property int                $id
	 * @property int                $corp_id         企业id
	 * @property int                $public_id       公众号id
	 * @property int                $activity_id     活动id
	 * @property int                $activity_num    活动完成人数
	 * @property int                $tier            层级明细
	 * @property int                $level           所在层级
	 * @property string             $parent_id       上级id
	 * @property int                $external_userid 外部联系人id
	 * @property int                $user_id         企业成员id
	 * @property int                $fans_id         粉丝id
	 * @property int                $is_tags         是否打标签
	 * @property string             $tags            标签
	 * @property int                $prize           领奖id
	 * @property int                $is_form         领奖id
	 * @property string             $poster_path     生成海报素材地址
	 * @property int                $success_time    完成时间
	 * @property int                $create_time     创建时间
	 * @property int                $update_time     修改时间
	 * @property int                $qc_url          渠道活码url
	 * @property int                $config_id       渠道活码config
	 *
	 * @property WorkPublicActivity $activity
	 * @property WorkCorp           $corp
	 * @property WorkUser           $user
	 */
	class WorkPublicActivityFansUser extends \yii\db\ActiveRecord
	{
		const ACTIVITY_NUM = 1;
		const TALL = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity_fans_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'tier', 'is_form', 'public_id', 'activity_id', 'activity_num', 'level', 'external_userid', 'user_id', 'fans_id', 'is_tags', 'prize', 'success_time', 'create_time', 'update_time'], 'integer'],
				[['tags', 'poster_path', 'qc_url', 'config_id'], 'string', 'max' => 255],
				[['parent_id'], 'string'],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'corp_id'         => Yii::t('app', '企业id'),
				'public_id'       => Yii::t('app', '公众号id'),
				'activity_id'     => Yii::t('app', '活动id'),
				'activity_num'    => Yii::t('app', '活动完成人数'),
				'tier'            => Yii::t('app', '层级明细'),
				'level'           => Yii::t('app', '是否发送完成海报'),
				'parent_id'       => Yii::t('app', '上级id'),
				'external_userid' => Yii::t('app', '外部联系人id'),
				'user_id'         => Yii::t('app', '企业成员id'),
				'fans_id'         => Yii::t('app', '粉丝id'),
				'is_tags'         => Yii::t('app', '是否打标签'),
				'tags'            => Yii::t('app', '标签'),
				'prize'           => Yii::t('app', '领奖id'),
				'is_form'         => Yii::t('app', '是否填写表单'),
				'poster_path'     => Yii::t('app', '生成海报素材地址'),
				'success_time'    => Yii::t('app', '完成时间'),
				'create_time'     => Yii::t('app', '创建时间'),
				'update_time'     => Yii::t('app', '修改时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getActivity ()
		{
			return $this->hasOne(WorkPublicActivity::className(), ['id' => 'activity_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @param $openid
		 * @param $id
		 * 人数自增+1
		 * @param $activity_id
		 * @param $public_id
		 * @param $name
		 *
		 */
		public static function setActivityNumIncr ($openid, $id, $activity_id, $public_id, $name)
		{
			$user               = self::findOne($id);
			$user->activity_num = $user->activity_num + self::ACTIVITY_NUM;
			if ($user->dirtyAttributes) {
				if (!$user->validate() || !$user->save()) {
					Yii::error(SUtils::modelError($user), 'saveError');
				}
			}
			Yii::error($user, '$user');
			$wechat   = WorkPublicPoster::getWxObject($public_id);
			$wxConfig = WxAuthorize::find()->where(['author_id' => $public_id])->select("authorizer_appid")->asArray()->one();
			/** @var WorkPublicActivityConfigLevel $level * */
			$level = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activity_id, "is_open" => 1])->andWhere("$user->activity_num >= number ")->orderBy("level desc")->one();
			Yii::error($level, '$level');
			$activity = WorkPublicActivity::findOne($activity_id);
			if (!empty($level)) {
				if ($level->level == 1) {
					//记录完成时间
					$user->success_time = time();
					$user->save();
					/** @var WorkPublicActivityConfigCall $successImg * */
					$successImg = WorkPublicActivityConfigCall::find()->where(["activity_id" => $activity_id, "type" => WorkPublicActivityConfigCall::TYPE_FOUR, "is_open" => 1])->one();
					if (!empty($successImg) && !empty($successImg->img_url) && $level->level == 1 && empty($user->level)) {
						$user->level = 1;
						$user->save();
						$result = $wechat->uploadMedia(Yii::$app->basePath . $successImg->img_url, "image");
						MsgUtil::send($wxConfig["authorizer_appid"], $openid, 2, ["media_id" => $result["media_id"]], true);
					}
				}
				if (!empty($user->prize)) {
					return;
				}
				//流程是直接给奖品，那么生成对应奖品id,但是没有地址，需用户填写
				if ($activity->flow == 1 && empty($user->prize) && $level->type == 1) {
					self::getPrize($user, $level, $activity);
				}
//				if ($user->activity_num > $level["number"]) {
//					return;
//				}
				switch ((int) $level->level) {
					case 1:
						$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $activity_id, "type" => WorkPublicActivityConfigCall::TYPE_THREE, "is_open" => 1])->one()->toArray();
						break;
					case 2:
						$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $activity_id, "type" => WorkPublicActivityConfigCall::TYPE_FIVE, "is_open" => 1])->one()->toArray();
						break;
					case 3:
						$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $activity_id, "type" => WorkPublicActivityConfigCall::TYPE_SEVEN, "is_open" => 1])->one()->toArray();
						break;
				}
				if ($level->type == 2 && empty($user->prize) && $activity->flow == 1) {
					self::SendRedBook($wxConfig["authorizer_appid"], $level->id, $activity, $user, $name, $call);

					return;
				}
				if (!empty($call)) {
					if ($call['is_template']) {
						//模板发送
						$url      = self::getTemplateUrl($call["is_url"], $activity->id, $activity->public_id, $user->id);
						$callText = self::replaceLevelAll($name, $call['template_context'], $activity_id, $user->id, true);
						$wechat->sendTemplateMessage($openid, $call["template_id"], json_decode($callText, true), $url, NULL, 1);
					} else {
						$callText = WorkPublicActivityFansUser::replaceLevelAll($name, $call["context"], $activity_id, $user->id);
						MsgUtil::send($wxConfig["authorizer_appid"], $openid, 1, ["text" => $callText]);
					}
				}
			}
		}

		/**
		 * @var self                          $fans
		 * @var WorkPublicActivityConfigLevel $levelModel
		 * @var WorkPublicActivity            $activity
		 */
		public static function getPrize ($fans, $levelModel, $activity)
		{
			$prize              = new WorkPublicActivityPrizeUser();
			$prize->level       = $levelModel->level;
			$prize->level_id    = $levelModel->id;
			$prize->public_id   = $activity->public_id;
			$prize->activity_id = $fans->activity_id;
			$prize->create_time = time();
			$prize->save();
			$fans->prize = $prize->id;
			$fans->save();
			if (!empty($levelModel->num_old)) {
				$levelModel->num = $levelModel->num - 1;
			}
			$levelModel->save();
			$levelEnd = json_decode($activity->level_end, 1);

			if (!empty($levelEnd)) {
				$level = array_pop($levelEnd);
				if ($level == $levelModel->level && ($levelModel->money_count == 0 || $levelModel->num == 0)) {
					self::DelActivityConfig($activity);
					$activity->is_over = WorkPublicActivity::IS_OVER_THREE;
					$activity->save();
				}
			}
			if ($activity->type != 1) {
				self::activityTags($fans->external_userid, $activity, $fans, 2, true);
			} else {
				$fansInfo = Fans::findOne($fans->fans_id);
				if (!empty($fansInfo)) {
					self::SetWechatTags($activity, $fansInfo->openid, $fans, true);
				}
			}
		}

		public static function DelActivityConfig ($activity)
		{
			try {
				if ($activity->type != 1) {
					try {
						$workApi = WorkUtils::getWorkApi($activity->corp_id, WorkUtils::EXTERNAL_API);
						$config  = json_decode($activity->config_id, true);
						foreach ($config as $value) {
							$workApi->ECDelContactWay($value);
						}
						$delData = WorkPublicActivityFansUser::find()->where(["corp_id" => $activity->corp_id, "activity_id" => $activity->id])->andWhere(["!=", "config_id", ''])->all();
						if (!empty($delData)) {
							foreach ($delData as $del) {
								$workApi->ECDelContactWay($del->config_id);
								$del->qc_url    = NULL;
								$del->config_id = NULL;
								$del->save();
							}
						}
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), "DelActivityConfig");
					}
					$activity->config_del = 1;
					$activity->save();
				}
			} catch (\Exception $e) {
				Yii::error($e->getMessage(), "DelActivityConfig");
			}

		}

		/**
		 * @param                          $appId
		 * @param                          $levelId
		 * @param WorkPublicActivity       $activity
		 * @param self                     $fans
		 * @param string                   $name
		 * @param array                    $call
		 *完成发红包
		 *
		 * @throws Throwable
		 * @throws ForbiddenException
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws NotAllowException
		 * @throws WxPayException
		 * @throws InvalidConfigException
		 * @throws StaleObjectException
		 */
		public static function SendRedBook ($appId, $levelId, $activity, $fans, $name = '', $call = [])
		{
			if (Yii::$app->cache->exists("activity-" . $fans->id)) {
				return;
			}
			if (!is_null($fans->prize)) {
				return;
			}
			$prize      = new WorkPublicActivityPrizeUser();
			$levelModel = WorkPublicActivityConfigLevel::findOne($levelId);
			$remark     = '已邀请' . $fans->activity_num . '人，完成任务，获得' . $levelModel->money_amount . '元红包。';
			$appid      = '';
			if ($activity->type == 2) {
				$contactInfo = WorkExternalContact::findOne($fans->external_userid);
				$openid      = $contactInfo->openid;
			} else {
				$wxinfo   = WxAuthorize::findOne(["author_id" => $activity->public_id]);
				$appid    = $wxinfo->authorizer_appid;
				$fansInfo = Fans::findOne($fans->fans_id);
				$openid   = $fansInfo->openid;
			}
			$sendData                     = [];
			$sendData['partner_trade_no'] = '44' . date('YmdHis') . mt_rand(111111, 999999) . mt_rand(11, 99);
			$sendData['openid']           = $openid;
			$sendData['amount']           = $levelModel->money_amount * 100;
			$sendData['desc']             = $remark;
			$redPacketPay                 = new RedPacketPay();
			try {
				$resData = $redPacketPay->RedPacketSend($activity->corp_id, $sendData, $appid);
				$cache   = $activity->end_time - time();
				Yii::$app->cache->set("activity-" . $fans->id, 1, $cache);
			} catch (\Exception $e) {
				Yii::error($e->getLine(), __CLASS__ . __FUNCTION__ . "sendRedBook");
			}
			$prize->order_sn    = $sendData['partner_trade_no'];
			$prize->status      = 0;
			$prize->price       = $sendData['amount'];
			$prize->type        = 2;
			$prize->level       = $levelModel->level;
			$prize->level_id    = $levelModel->id;
			$prize->public_id   = $activity->public_id;
			$prize->activity_id = $activity->id;
			$prize->create_time = time();
			if (!empty($levelModel->money_count_old)) {
				WorkPublicActivityConfigLevel::updateAllCounters(["money_count" => -1], ["id" => $levelId]);
//				$levelModel->money_count = $levelModel->money_count - 1;
			}
			$msg = "您好！您的好友 {nickname} 支持你了！\r\n您已获得 {success} 个好友支持\r\n恭喜你完成任务，获得" . $levelModel->money_amount . "元，已自动发放至您的微信零钱里。若没有收到，请联系商家";
			if (isset($resData['return_code']) && $resData['return_code'] == 'SUCCESS' && $resData['result_code'] == 'SUCCESS') {
				$prize->status = 1;
			}
			$prize->save();
//			$levelModel->save();
			$fans->prize = $prize->id;
			$fans->save();
			$levelEnd = json_decode($activity->level_end, 1);
			if (!empty($levelEnd)) {
				$level = array_pop($levelEnd);
				if ($level == $levelModel->level && ($levelModel->money_count == 0 || $levelModel->num == 0)) {
					self::DelActivityConfig($activity);
					$activity->is_over = WorkPublicActivity::IS_OVER_THREE;
					$activity->save();
				}
			}
			if ($activity->type != WorkPublicActivity::ACTIVITY_TYPE_TWO && empty($call["is_template"])) {
				$msg = self::replaceFansName($msg, $name);
				$msg = self::replaceSuccess($msg, $activity->id, $fans->id);
				MsgUtil::send($appId, $openid, 1, ["text" => $msg], true);
			}
			if (!empty($call["is_template"]) && $activity->type != WorkPublicActivity::ACTIVITY_TYPE_TWO) {
				$context = '{"first":{"value":"您好，您的好友 {nickname} 支持你了！","color":"#000"},"keyword1":{"value":"{activityName}","color":"#000"},"keyword2":{"value":"{success}","color":"#000"},"remark":{"value":"恭喜你完成任务，获得' . $levelModel->money_amount . '元，已自动发放至您的微信零钱里。若没有收到，请联系商家","color":"#000"}}';
//				$url      = self::getTemplateUrl($call["is_url"], $activity->id, $activity->public_id, $fans->id);
				$callText = self::replaceLevelAll($name, $context, $activity->id, $fans->id, true);
				$wechat   = WorkPublicPoster::getWxObject($activity->public_id);
				$wechat->sendTemplateMessage($openid, $call["template_id"], json_decode($callText, true), NULL, NULL, 2);
			}
			if ($activity->type != 1) {
				self::activityTags($fans->external_userid, $activity, $fans, 2, true);
			} else {
				self::SetWechatTags($activity, $openid, $fans, true);
			}
		}

		/**
		 * Title: SetWechatTags
		 * User: sym
		 * Date: 2020/12/25
		 * Time: 9:56
		 *
		 * @param WorkPublicActivity|array $activity
		 * @param string                   $openid
		 * @param self                     $fans
		 * @param bool                     $success
		 *
		 * @remark
		 */
		public static function SetWechatTags ($activity, $openid, $fans, $success = false)
		{
			$temp         = is_array($activity) ? true : false;
			$success_tags = $temp ? $activity["success_tags"] : $activity->success_tags;
			$label_id     = $temp ? $activity["label_id"] : $activity->label_id;
			$public_id    = $temp ? $activity["public_id"] : $activity->public_id;
			if ($success) {
				$labelId = json_decode($success_tags, true);
				if (!empty($fans->is_tags)) {
					$tags = explode(",", $fans->tags);
					$tags = array_diff($labelId, $tags);
					if (!empty($tags)) {
						$fans->tags .= "," . implode(",", $tags);
					}
				} else {
					$fans->tags = implode(",", $labelId);
				}
			} else {
				$labelId       = json_decode($label_id, true);
				$fans->is_tags = 1;
				$fans->tags    = implode(",", $labelId);
			}
			$fans->save();
			if (!empty($labelId)) {
				$tags     = Tags::find()->where(["author_id" => $public_id])->andWhere(["in", "tag_id", $labelId])->select("id")->asArray()->all();
				$tags     = array_column($tags, "id");
				$wxConfig = WxAuthorize::find()->where(['author_id' => $public_id])->select("authorizer_appid")->asArray()->one();
				try {
					Tags::giveUserTags($wxConfig['authorizer_appid'], $public_id, $tags, [$openid], 0, 1);
				} catch (\Exception $e) {
					Yii::error($e->getMessage(), "tags-message");
				}
			}
		}

		/**
		 * @param $id
		 * @param $activity_id
		 * 人数自减+1
		 */
		public static function setActivityNumDecr ($id, $activity_id)
		{
			$user = self::findOne($id);
			if (empty($user)) {
				return;
			}
			$user->activity_num = $user->activity_num - self::ACTIVITY_NUM;
			$activity           = WorkPublicActivity::findOne($activity_id);
			$level              = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activity_id, "is_open" => 1])->andWhere("$user->activity_num >= number ")->orderBy("level asc")->asArray()->one();
			if (empty($level) && !empty($user->success_time) && $activity->flow != 1) {
				$user->success_time = NULL;
			}
			$user->save();
		}

		/**
		 * @param        $data
		 *设置发送明细并返回当前对象
		 *
		 * @param string $Activity
		 *
		 * @return WorkPublicActivityFansUser
		 * @throws InvalidDataException
		 */
		public static function setData ($data)
		{
			$row = new self();
			$row->setAttributes($data);
			$row->save();

			return $row;
		}

		/**
		 * @param $context
		 * @param $nickname
		 *替换名称
		 *
		 * @return string|string[]
		 */
		public static function replaceFansName ($context, $nickname)
		{
			if (strpos($context, '{nickname}') !== false) {
				$context = str_replace("{nickname}", "@" . $nickname, $context);
			}

			return $context;
		}

		/**
		 * @param $context
		 * @param $activity
		 * @param $fans
		 *兑奖连接
		 *
		 * @return string|string[]
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws NotAllowException
		 * @throws InvalidConfigException
		 */
		public static function replaceFansUrl ($context, $activity)
		{
			if (strpos($context, '{url}') !== false) {
				$WorkPublicActivity = WorkPublicActivity::findOne($activity);
				if ($WorkPublicActivity->action_type == 2) {
					$context = preg_replace("/<\w\s\w+='{url}'.+[<\/a>]$/", '', $context);
				} else {
					$wxInfo      = WxAuthorizeInfo::findOne(["author_id" => $WorkPublicActivity->public_id]);
					$wxAuthorize = WxAuthorize::getTokenInfo($wxInfo->authorizer_appid, false, true);
					$wechat      = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $wxInfo->authorizer_appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);
					$redirectUrl = Yii::$app->params["site_url"] . '/api/chat-message/activity-status-success?activity_id=' . $activity . '&show=1';

					$url     = $wechat->getOauth2AuthorizeUrl($redirectUrl);
					$context = str_replace("{url}", $url, $context);
				}
			}

			return $context;
		}

		public static function getSuccessUrl ($public_id, $activity)
		{
			$wxInfo      = WxAuthorizeInfo::findOne(["author_id" => $public_id]);
			$wxAuthorize = WxAuthorize::getTokenInfo($wxInfo->authorizer_appid, false, true);
			$wechat      = \Yii::createObject([
				'class'          => Wechat::className(),
				'appId'          => $wxInfo->authorizer_appid,
				'appSecret'      => $wxAuthorize['config']->appSecret,
				'token'          => $wxAuthorize['config']->token,
				'componentAppId' => $wxAuthorize['config']->appid,
			]);
			$redirectUrl = Yii::$app->params["site_url"] . '/api/chat-message/activity-status-success?activity_id=' . $activity . '&show=1';

			return $wechat->getOauth2AuthorizeUrl($redirectUrl);
		}

		/**
		 * @param $context
		 * @param $activity_id
		 * @param $fans_id
		 *距离成功
		 *
		 * @return string|string[]
		 */
		public static function replaceSuccess ($context, $activity_id, $fans_id)
		{
			if (strpos($context, '{success}') !== false) {
				$parent = self::findOne($fans_id);
				$num    = 0;
				if (!empty($parent)) {
					$num = $parent->activity_num;
				}
				$context = str_replace("{success}", $num, $context);
			}

			return $context;
		}

		/**
		 * @param $context
		 * @param $activityId
		 * @param $fans_id
		 *排行旁
		 *
		 * @return string|string[]
		 */
		public static function replaceRanking ($context, $activityId, $fans_id)
		{
			if (strpos($context, '{ranking}') !== false) {
				$webUrl  = Yii::$app->params["web_url"];
				$params  = WorkPublicActivity::RANKING . "?activity_id=" . $activityId . "&fans_id=" . $fans_id;
				$params  = ShortUrlUtil::setShortUrl($params);
				$context = str_replace("{ranking}", $webUrl . "/h5/I/" . $params, $context);
			}

			return $context;
		}

		/**
		 * @param $context
		 * @param $activityId
		 *活动名称
		 *
		 * @return string|string[]
		 */
		public static function replaceActivity ($context, $activityId)
		{
			if (strpos($context, '{activityName}') !== false) {
				$Activity = WorkPublicActivity::findOne($activityId);
				Yii::error($activityId, '$Activity');
				Yii::error($Activity, '$Activity');
				$context = str_replace("{activityName}", $Activity->activity_name, $context);
			}

			return $context;
		}

		/**
		 * @param $context
		 * @param $activity_id
		 * @param $fans_id
		 *距离下一阶段还差
		 *
		 * @return string|string[]
		 */
		public static function replaceError ($context, $activity_id, $fans_id)
		{
			if (strpos($context, '{error}') !== false) {
				$parent = self::findOne($fans_id);
				$level  = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activity_id, "is_open" => 1])->andWhere("number > $parent->activity_num")->asArray()->orderBy("id asc")->one();
				if (!empty($level)) {
					$errorNum = ($level['number'] - $parent->activity_num) >= 0 ? $level['number'] - $parent->activity_num : 0;
					$context  = str_replace("{error}", $errorNum, $context);
				} else {
					$context = str_replace("{error}", 0, $context);
				}
			}

			return $context;
		}

		/**
		 * @param array $levels
		 * @param       $context
		 * @param       $activity_id
		 *每一阶段库存
		 *
		 * @return string|string[]
		 */
		public static function replaceLevelNum ($context, $activity_id, $levels = [])
		{
			if (empty($levels)) {
				$levels = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activity_id, "is_open" => 1])->asArray()->all();
			}
			foreach ($levels as $key => $level) {
				if ($level['type'] == 2) {
					$level["prize_name"] = "红包";
					$level["num"]        = $level["money_count"];
				}
				if (strpos($context, '{oneLevel}') !== false && $level['level'] == 1) {
					$context = str_replace("{oneLevel}", $level["prize_name"] . $level["num"], $context);
					unset($levels[$key]);
					self::replaceLevelNum($context, $activity_id, $levels);
				}
				if (strpos($context, '{twoLevel}') !== false && $level['level'] == 2) {
					$context = str_replace("{twoLevel}", $level["prize_name"] . $level["num"], $context);
					unset($levels[$key]);
					self::replaceLevelNum($context, $activity_id, $levels);
				}
				if (strpos($context, '{threeLevel}') !== false && $level['level'] == 3) {
					unset($levels[$key]);
					$context = str_replace("{threeLevel}", $level["prize_name"] . $level["num"], $context);
					self::replaceLevelNum($context, $activity_id, $levels);
				}
			}

			return $context;
		}

		//全部替换
		public static function replaceLevelAll ($nickname, $context, $activity_id, $fans_id, $template = false)
		{
			$context = self::replaceFansName($context, $nickname);
			$context = self::replaceError($context, $activity_id, $fans_id);
			$context = self::replaceActivity($context, $activity_id);
			$context = self::replaceFansUrl($context, $activity_id);
			$context = self::replaceLevelNum($context, $activity_id, []);
			$context = self::replaceSuccess($context, $activity_id, $fans_id);
			if (!$template) {
				$context = self::replaceRanking($context, $activity_id, $fans_id);
			}

			return $context;
		}

		/**
		 * @param $corp_id            //企业微信id
		 * @param $user_Id            //删除成员id
		 * @param $workExternalUserId //外部联系人id
		 *
		 */
		public static function corpPublicExternalDel ($corp_id, $user_Id, $workExternalUserId)
		{
			try {
				$exUser = Yii::$app->db->createCommand(
					"SELECT
							d.prize,
							d.activity_id,
							c.public_id,
							d.parent_id,
							d.poster_path,
							d.id,
							b.`name` as name_convert,
							d.fans_id,
							c.type,
							c.flow,
							c.action_type,
							e.openid, 
							e.subscribe, 
							e.nickname, 
							c.not_attention 
						FROM
							{{%work_external_contact_follow_user}} AS a
							LEFT JOIN {{%work_external_contact}} AS b ON a.external_userid = b.id
							LEFT JOIN {{%work_public_activity}} AS c ON a.activity_id = c.id
							LEFT JOIN {{%work_public_activity_fans_user}} AS d ON (a.activity_id = d.activity_id 
							AND a.external_userid = d.external_userid )
							LEFT JOIN {{%fans}} as e on e.id = d.fans_id
						WHERE
							a.user_id = $user_Id 
							AND a.external_userid = $workExternalUserId
							AND b.corp_id = $corp_id
							AND a.activity_id IS NOT NULL
							AND a.del_type = 2"
				)->queryOne();
				Yii::error($exUser, "sym333333");
				if (!empty($exUser)) {
					$fans      = self::findOne($exUser["id"]);
					$parentIds = explode(",", $exUser["parent_id"]);
					//仅企业微信
					if ($exUser['type'] == WorkPublicActivity::ACTIVITY_TYPE_TWO && $exUser['not_attention'] == WorkPublicActivity::NOT_ATT_FOUR) {
						foreach ($parentIds as $parentId) {
							//助力数据改为取消关注
							/** @var WorkPublicActivityFansUserDetail $fansUserDetail * */
							$fansUserDetail = WorkPublicActivityFansUserDetail::find()
								->where(["public_parent_id" => $parentId, "user_id" => $user_Id, "public_user_id" => $exUser["id"], "activity_id" => $exUser['activity_id']])
								->andWhere("type is null ")
								->one();
							if (!empty($fansUserDetail)) {
								$fansUserDetail->type = 4;
								$fansUserDetail->save();
								//取消上级助力次数
								WorkPublicActivityFansUser::setActivityNumDecr($parentId, $exUser["activity_id"]);
							}
						}
						$fans->parent_id = NULL;
						$fans->save();

						return false;
					}
					//企业微信+公众号
					if ($exUser['type'] == WorkPublicActivity::ACTIVITY_TYPE_THREE && ($exUser['not_attention'] == WorkPublicActivity::NOT_ATT_FOUR || $exUser['subscribe'] == 0)) {
						$publicDel = false;
						if ($exUser['subscribe'] == Fans::USER_UNSUBSCRIBE) {
							$publicDel = true;
						}
						$wxConfig = WxAuthorize::find()->where(['author_id' => $exUser['public_id']])->select("authorizer_appid")->asArray()->one();
						//获取上级公众号用户
						foreach ($parentIds as $parentId) {
							//取消上级助力次数
							WorkPublicActivityFansUser::setActivityNumDecr($parentId, $exUser["activity_id"]);
							//助力数据改为取消关注
							/** @var WorkPublicActivityFansUserDetail $fansUserDetail * */
							$fansUserDetail = WorkPublicActivityFansUserDetail::find()
								->where(["public_parent_id" => $parentId, "user_id" => $user_Id, "public_user_id" => $exUser["id"], "activity_id" => $exUser['activity_id']])
								->andWhere("type is null ")
								->one();
							if (!empty($fansUserDetail)) {
								if ($publicDel) {
									$fansUserDetail->type = 3;
								} else {
									$fansUserDetail->type = 4;
								}
								$fansUserDetail->save();
							}
							$parentFans = WorkPublicActivityFansUser::find()->alias("a")
								->leftJoin("{{%fans}} as b", "a.fans_id = b.id")
								->where(["a.id" => $parentId])
								->select("b.openid,b.nickname,a.id,a.prize")
								->asArray()->one();
							//取消关注提醒
							$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $exUser['activity_id'], "is_open" => 1, "type" => WorkPublicActivityConfigCall::TYPE_TWO])->one();
							$call = !empty($call) ? $call->toArray() : [];
							if ($exUser["flow"] == 1 && !empty($parentFans["prize"])) {
								$str = "您的好友 {nickname} 放弃为你助力！\r\n人气值-1，但不影响您已获得的奖品。\r\n";
								if ($exUser["action_type"] == 1) {
									$str .= "<a href='{url}'>点此领取奖品吧~</a>";
								} else {
									$str .= "快去联系客服领取奖品吧~";
								}
								$str = self::replaceLevelAll($exUser['nickname'], $str, $exUser['activity_id'], $parentId);
								MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => "任务活动--" . $exUser["activity_name"] . "\r\n" . $str]);
								FansMsg::create($parentFans['id'], 1, $str, 2, FansMsg::TO_FANS);
								continue;
							}
							if (!empty($parentFans) && !empty($call)) {
								if (!empty($call['is_template'])) {
									//模板发送
									$url      = self::getTemplateUrl($call["is_url"], $exUser['activity_id'], $exUser['public_id'], $parentFans['id']);
									$wechat   = WorkPublicPoster::getWxObject($exUser['public_id']);
									$callText = WorkPublicActivityFansUser::replaceLevelAll($exUser['nickname'], $call['template_context'], $exUser['activity_id'], $parentFans['id'], true);
									$wechat->sendTemplateMessage($parentFans['openid'], $call["template_id"], json_decode($callText, true), $url, NULL, 1);
								} else {
									$callText = WorkPublicActivityFansUser::replaceLevelAll($exUser['nickname'], $call["context"], $exUser['activity_id'], $parentFans['id']);
									MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => $callText]);
								}
							}
						}
						$fans->parent_id = NULL;
						$fans->save();
					}
				}
			} catch (\Exception $e) {
				Yii::error($e->getLine(), "sym333333");
				Yii::error($e->getMessage(), "sym333333");

			}
		}

		/**
		 * @param int|string $corp_id            企业id
		 * @param int|string $user_Id            企业成员id
		 * @param int|string $workExternalUserId 外部联系人id
		 *                                       增加
		 *
		 * @return array|bool
		 */
		public static function corpPublicWelcomeSend ($corp_id, $user_Id, $workExternalUserId, $state)
		{
			try {
				if (Yii::$app->cache->get($corp_id . "-" . $user_Id . "-" . $workExternalUserId)) {
					return false;
				}
				Yii::$app->cache->set($corp_id . "-" . $user_Id . "-" . $workExternalUserId, 1, 5);
				if (empty($state)) {
					return false;
				}
				$stateData = explode("_", $state);
				if ($stateData[0] != WorkPublicActivity::STATE_NAME) {
					return false;
				}
				Yii::error($stateData, '$stateData');

				$activity = WorkPublicActivity::findOne($stateData[1]);
				Yii::error($activity, '$activity');

				$channel_user_id = explode(",", $activity->channel_user_id);
				if (!in_array($user_Id, $channel_user_id)) {
					return false;
				}
				$extU = WorkExternalContactFollowUser::find()->alias("a")
					->leftJoin("{{%work_external_contact}} as b", "a.external_userid = b.id")
					->where(["a.user_id" => $user_Id, "a.external_userid" => $workExternalUserId, "b.corp_id" => $corp_id, "a.activity_id" => $stateData[1]])
					->select("a.id,b.name")
					->one();
				Yii::error($extU, '$extU');
				//查看自己是否参与活动
				$oldSelFans = self::findOne(["external_userid" => $workExternalUserId, "activity_id" => $stateData[1]]);
				//获取微信配置
				$wxConfig = WxAuthorize::find()->where(['author_id' => $activity->public_id])->select("authorizer_appid")->asArray()->one();
				Yii::error($oldSelFans, '$oldSelFans');
				if (!empty($oldSelFans)) {
					$detail = WorkPublicActivityFansUserDetail::findOne(["external_userid" => $workExternalUserId, "type" => NULL, "public_user_id" => $oldSelFans->id, "public_parent_id" => $stateData[2], "user_id" => $user_Id, "activity_id" => $stateData[1]]);
					if (!empty($detail)) {
						return false;
					}
				}
				if ($activity->type == WorkPublicActivity::ACTIVITY_TYPE_TWO) {
					if (!empty($extU) && empty($oldSelFans)) {
						$fansData = [
							"corp_id"         => $activity->corp_id,
							"public_id"       => $activity->public_id,
							"external_userid" => $workExternalUserId,
							"user_id"         => $user_Id,
							"parent_id"       => $stateData[2],
							"activity_id"     => $activity->id,
							"create_time"     => time(),
						];

						//判断人数限制
						if (WorkPublicActivity::checkJoinNumIsMax($activity->id)) {
							WorkPublicActivity::setActivityOver($activity->id);
							$str             = "很抱歉，当前活动过于火爆暂时无法参与";
							$content['text'] = ['content' => $str];

							return $content;
						}

						$fans = self::setData($fansData);
						//自己外部联系人
						self::activityTags($extU['id'], $activity, $fans);
						$WorkExternalContact = WorkExternalContact::findOne($workExternalUserId);
						$tier                = self::setParentLeveL($stateData, $fans);
						//存在上级
						if (!empty($stateData[2])) {
							//增加上级助力人数
							$parentFans               = self::findOne($stateData[2]);
							$parentFans->activity_num = $parentFans->activity_num + self::ACTIVITY_NUM;
							$parentFans->save();
							/** @var WorkPublicActivityConfigLevel $level * */
							$level = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activity->id, "is_open" => 1])->andWhere("$parentFans->activity_num >= number ")->orderBy("level desc")->one();
							if (!empty($level)) {
								if ($level->level == 1) {
									//记录完成时间
									$parentFans->success_time = time();
									$parentFans->save();
									if ($level->type == 2 && empty($parentFans->prize)) {
										WorkPublicActivityFansUser::SendRedBook('', $level["id"], $activity, $parentFans);
									} elseif ($level->type == 1 && empty($parentFans->prize)) {
										WorkPublicActivityFansUser::getPrize($parentFans, $level, $activity);
									}
								}
							}
							self::setRecord([
								"user_id"          => $user_Id,
								"external_userid"  => $workExternalUserId,
								"activity_id"      => $activity->id,
								"public_parent_id" => $stateData[2],
								"public_user_id"   => $fans->id,
								"is_remind"        => 0,
							]);
							$fans->save();
						}

						$corpAgent = WorkCorpAgent::findOne($activity->corp_agent);
						$workCorp  = WorkCorp::findOne($activity->corp_id);
						$state     = WorkPublicActivity::STATE_NAME . '_' . $activity->id . '_' . $fans->id . "_" . $tier->id;
						if ($corpAgent->agent_type == WorkCorpAgent::AUTH_AGENT) {
							$url = \Yii::$app->params['web_url'] . WorkPublicActivity::H5_URL . '?suite_id=' . $corpAgent->suite->suite_id . '&corp_id=' . $corpAgent->corp_id . '&corpid=' . $workCorp->corpid . '&agent_id=' . $activity->corp_agent . '&assist=' . $state;
						} else {
							$url = \Yii::$app->params['web_url'] . WorkPublicActivity::H5_URL . '?corp_id=' . $corpAgent->corp_id . '&corpid=' . $workCorp->corpid . '&agent_id=' . $activity->corp_agent . '&assist=' . $state;
						}
						if (!empty($stateData[2])) {
							$str = self::replaceFansName($activity->welcome_help, $WorkExternalContact->name);
						} else {
							$str = self::replaceFansName($activity->welcome, $WorkExternalContact->name);
						}
						$str = self::replaceRanking($str, $activity->id, $fans->id);
						self::activityTags($extU['id'], $activity, $fans);
						$content         = [];
						$content['text'] = ['content' => $str];
						$content['link'] = [
							'title'  => $activity->welcome_title,
							'picurl' => \Yii::$app->params['site_url'] . $activity->welcome_url,
							'desc'   => $activity->welcome_describe,
							'url'    => $url,
						];
						Yii::error($content, '$content');
						Yii::$app->websocket->send([
							'channel' => 'web-message',
							'to'      => $WorkExternalContact->openid,
							'type'    => WorkPublicActivity::STATE_NAME,
							'info'    => [
								'mission_id' => $workExternalUserId,
								'type'       => WorkPublicActivity::STATE_NAME,
								'has_chat'   => true,
							]
						]);

						return $content;
					}

					return false;
				} else {
					$fansInfo = WorkExternalContact::find()->alias("a")
						->leftJoin("{{%fans}} as b", "a.unionid = b.unionid")
						->leftJoin("{{%work_contact_way_line}} as c", "c.external_userid = a.id")
						->where(["a.id" => $workExternalUserId, "a.corp_id" => $corp_id])
						->andWhere("a.unionid is not null")
						->select("b.openid,b.id,b.subscribe, b.headerimg, c.gender, b.nickname")->asArray()->one();
					Yii::error($fansInfo, '$fansInfo');
					if (empty($fansInfo)) {
						return false;
					}
					if (!empty($extU) && empty($oldSelFans)) {
						//性别限定
						if ($fansInfo["gender"] == 0) {
							$fansInfo["gender"] = 3;
						}
						if ($activity->sex_type != 4 && $activity->sex_type != $fansInfo["gender"] && $activity->type == 3) {
							$sexMsg = "当前【" . $activity->activity_name . "】";
							if ($activity->sex_type == 1) {
								$sexMsg .= "仅限男性参加";
							} else if ($activity->sex_type == 2) {
								$sexMsg .= "仅限女性参加";
							} else {
								$sexMsg .= "仅限未知参加";
							}
							MsgUtil::send($wxConfig["authorizer_appid"], $fansInfo["openid"], 1, ["text" => $sexMsg]);
							FansMsg::create($fansInfo["id"], 1, $sexMsg, 2, FansMsg::TO_FANS);

							return false;
						}
						//判断人数限制
						if (WorkPublicActivity::checkJoinNumIsMax($activity->id)) {
							WorkPublicActivity::setActivityOver($activity->id);
							$str             = "很抱歉，当前活动过于火爆暂时无法参与";
							$content['text'] = ['content' => $str];

							return $content;
						}

						//本身
						$fansData = [
							"corp_id"         => $activity->corp_id,
							"public_id"       => $activity->public_id,
							"external_userid" => $workExternalUserId,
							"user_id"         => $user_Id,
							"fans_id"         => $fansInfo["id"],
							"parent_id"       => $stateData[2],
							"activity_id"     => $activity->id,
							"create_time"     => time(),
						];
						Yii::error($fansData, '$fansData');
						$fans = self::setData($fansData);
					}
					if (!isset($fans)) {
						$fans = $oldSelFans;
					}
					$tier = self::setParentLeveL($stateData, $fans);
					Yii::error($tier, '$tier');
					if (!empty($stateData[2])) {
						//获取上级公众号用户
						$parentFans = WorkPublicActivityFansUser::find()->alias("f")
							->leftJoin("{{%fans}} as s", "f.fans_id = s.id")
							->select("s.openid,f.parent_id,f.fans_id,f.external_userid,f.user_id,s.nickname,f.id,f.prize")
							->where(["f.id" => $stateData[2]])->asArray()->one();
						Yii::error($parentFans, '$parentFans');
						if (empty($parentFans["fans_id"])) {
							$publicFans = Fans::findOne(["external_userid" => $parentFans["external_userid"]]);
							if (!empty($publicFans)) {
								WorkPublicActivityFansUser::updateAll(["fans_id" => $publicFans->id], ["id" => $parentFans["id"]]);
							}
						}
						//当天提示超过3次免打扰
						$disturbing = WorkPublicActivityFansUserDetail::find()
							->where(["activity_id" => $activity->id, "public_parent_id" => $stateData[2]])
							->andWhere("type is null")
							->andFilterWhere(["between", "create_time", strtotime(date("Y-m-d")), strtotime(date("Y-m-d")) + 86400])
							->count();
						$remind     = true;
						if ($disturbing >= 3) {
							WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $activity->id, $parentFans['nickname'], $stateData[2], $parentFans['openid'], WorkPublicActivityConfigCall::TYPE_ELEVEN, $stateData[2]);
							$remind = false;
						}
						WorkPublicActivityFansUser::setActivityNumIncr($parentFans['openid'], $stateData[2], $activity->id, $activity->public_id, $fansInfo['nickname']);
						$parent_id = explode(",", $fans->parent_id);
						if (empty($parent_id)) {
							$fans->parent_id = $stateData[2];
						}
						if (!in_array($stateData[2], $parent_id)) {
							$fans->parent_id = $fans->parent_id . "," . $stateData[2];
						}
						$fans->save();
						$array  = [
							"activity_id"      => $activity->id,
							"public_parent_id" => $stateData[2],
							"fans_id"          => $fansInfo["id"],
						];
						$record = WorkPublicActivityFansUserDetail::findOne($array);
						Yii::error($record, '$record');
						if (!empty($record)) {
							$record->public_user_id  = $fans->id;
							$record->user_id         = $user_Id;
							$record->external_userid = $workExternalUserId;
							$record->is_remind       = 1;
							$record->type            = NULL;
							$record->create_time     = time();
							$record->save();
						} else {
							$array["public_user_id"]  = $fans->id;
							$array["create_time"]     = time();
							$array["external_userid"] = $workExternalUserId;
							$array["user_id"]         = $user_Id;
							WorkPublicActivityFansUserDetail::setData($array);
						}
						WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $activity->id, $fansInfo['nickname'], $fans->id, $fansInfo['openid'], WorkPublicActivityConfigCall::TYPE_EIGHT, $fansInfo["id"]);
						$prize = WorkPublicActivityFansUser::find()->alias("a")
							->leftJoin("{{%work_public_activity_prize_user}} as c", "a.prize = c.id")
							->select("a.prize,c.order_sn")
							->where(["a.id" => $parentFans["id"]])->asArray()->one();
						if (!empty($parentFans["prize"])) {
							WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $activity->id, $fansInfo["nickname"], $parentFans['id'], $parentFans['openid'], WorkPublicActivityConfigCall::TYPE_THIRTEEN, $parentFans['id']);
						}
						if ($remind && empty($prize["prize"])) {
							//新助力上级提醒
							$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $activity->id, "type" => WorkPublicActivityConfigCall::TYPE_ONE, "is_open" => 1])->one();
							if (!empty($call)) {
								$call = $call->toArray();
								if ($call['is_template']) {
									//模板发送
									$url      = self::getTemplateUrl($call["is_url"], $activity->id, $activity->public_id, $stateData[2]);
									$wechat   = WorkPublicPoster::getWxObject($activity->public_id);
									$callText = WorkPublicActivityFansUser::replaceLevelAll($fansInfo['nickname'], $call['template_context'], $activity->id, $stateData[2], true);
									$wechat->sendTemplateMessage($parentFans['openid'], $call["template_id"], json_decode($callText, true), $url, NULL, 1);
								} else {
									$callText = WorkPublicActivityFansUser::replaceLevelAll($fansInfo['nickname'], $call["context"], $activity->id, $stateData[2]);
									MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => $callText]);
								}
							}
						}
					}
					self::activityTags($extU['id'], $activity, $fans);
					//获取海报配置
					$posterConfig              = WorkPublicActivityPosterConfig::getPosterConfig($activity->id);
					$posterConfig["heard_url"] = $fansInfo['headerimg'];
					$posterConfig["userName"]  = $fansInfo['nickname'];
					$fans->tier                = $tier->id;
					$fans->save();
					$media_id = WorkPublicPoster::getPoster($activity, $fans, $posterConfig, $tier->id, true);
					if (!empty($stateData[2])) {
						$welcome = $activity->welcome_help;
					} else {
						$welcome = $activity->welcome;
					}
					$context = self::replaceRanking($welcome, $activity->id, $fans->id);
					$context = self::replaceFansName($context, $fansInfo["nickname"]);

					return [
						"image" => [
							"media_id" => $media_id
						],
						"text"  => [
							"content" => $context
						],
					];
				}
			} catch (\Exception $e) {
				Yii::error($e->getLine(), 'welcome-sym');
				Yii::error($e->getFile(), 'welcome-sym');
				Yii::error($e->getMessage(), 'welcome-sym');
			}

		}

		/**
		 * @param $array
		 * @param $user_id
		 * 助力记录，存在更新，不存在新增
		 */
		public static function setRecord ($array, $user_id = NULL)
		{
			$record = WorkPublicActivityFansUserDetail::findOne($array);
			Yii::error($record, '$record');
			if (!empty($record)) {
				if ($record->type == 5) {
					$record->user_id = $user_id;
				}
				if (!isset($array["type"])) {
					$record->type = NULL;
				}
				$record->create_time = time();
				$record->save();
			} else {
				$array["create_time"] = time();
				WorkPublicActivityFansUserDetail::setData($array);
			}
		}

		/**
		 * @param                    $workExternalUserId
		 * @param WorkPublicActivity $activity
		 * @param self               $fans
		 * @param int                $type
		 * @param bool               $success
		 *
		 */
		public static function activityTags ($workExternalUserId, $activity, $fans, $type = 2, $success = false)
		{
			try {
				if ($success) {
					$labelId = json_decode($activity->success_tags, true);
				} else {
					$labelId = json_decode($activity->label_id, true);
				}
				if (empty($labelId)) {
					return;
				}
				$labelIdTemp = [];
				foreach ($labelId as $item) {
					if (!empty($item)) {
						array_push($labelIdTemp, $item);
					}
				}
				$fail = WorkTag::addUserTag($type, [$workExternalUserId], $labelIdTemp, ["type" => "activity", "msg" => $activity->activity_name]);
				if ($fail >= 0) {
					$fans->is_tags = 1;
					if ($success) {
						if (!empty($fans->tags)) {
							$tags       = explode(",", $fans->tags);
							$tags       = array_diff($labelId, $tags);
							$fans->tags .= "," . implode(",", $tags);
						} else {
							$fans->tags = implode(",", $labelId);
						}
					} else {
						$fans->tags = implode(",", $labelId);
					}
					$fans->save();
				}
			} catch (\Exception $e) {
				Yii::error($e->getFile(), "corp_tags");
				Yii::error($e->getLine(), "corp_tags");
				Yii::error($e->getMessage(), "corp_tags");
			}
		}

		/**
		 * @param $stateData
		 * @param $fans
		 *设置层级
		 *
		 * @return WorkPublicActivityTier
		 */
		public static function setParentLeveL ($stateData, $fans)
		{
			//层级明细
			if ($stateData[2] != 0 && $stateData[3] != 0) {
				$parentTier = WorkPublicActivityTier::findOne($stateData[3]);
				$tier       = WorkPublicActivityTier::findOne(["fans_id" => $fans->id, "parent_id" => $stateData[3], "activity_id" => $stateData[1], "parent" => $stateData[2]]);
				if (!empty($tier)) {
					return $tier;
				}
				$tier = new WorkPublicActivityTier();
				if (empty($parentTier->parent_fans)) {
					$tier->parent_fans = $parentTier->fans_id . "," . $fans->id;
					$tier->level       = '2';
				} else {
					$tier->parent_fans = $parentTier->parent_fans . "," . $fans->id;
					$levelIds          = explode(",", $parentTier->level);
					Yii::error($levelIds, '$levelIds');
					foreach ($levelIds as &$levelId) {
						$levelId += 1;
					}
					array_push($levelIds, 2);
					$tier->level = implode(",", $levelIds);
				}
				$tier->parent_id   = $stateData[3];
				$tier->parent      = $stateData[2];
				$tier->fans_id     = $fans->id;
				$tier->activity_id = $stateData[1];
				$tier->create_time = time();
				$tier->save();
			} else {
				$tier = WorkPublicActivityTier::findOne(["fans_id" => $fans->id]);
				if (!empty($tier)) {
					return $tier;
				}
				$tier              = new WorkPublicActivityTier();
				$tier->fans_id     = $fans->id;
				$tier->activity_id = $stateData[1];
				$tier->level       = '1';
				$tier->create_time = time();
				$tier->save();
			}

			return $tier;
		}

		/**
		 * @param                                  $hasSendRanking
		 * @param                                  $describe
		 * @param                                  $publicActivity
		 * @param                                  $wxConfig
		 * @param                                  $fromUserName
		 * @param Fans                             $fans
		 * @param                                  $posterDescribe
		 * @param WorkPublicActivityFansUser|array $workPublicActivityFansUser
		 *
		 * @throws ForbiddenException
		 * @throws InvalidConfigException
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws NotAllowException
		 * @throws StaleObjectException
		 * @throws Throwable
		 */
		public static function sendRanking (&$hasSendRanking, $describe, $publicActivity, $wxConfig, $fromUserName, $fans, $posterDescribe, $workPublicActivityFansUser = [])
		{
			$hasSendRanking = true;
			if (!empty($publicActivity['describe'])) {
				$describe = self::replaceRanking($describe, $publicActivity["id"], empty($workPublicActivityFansUser) ? 0 : $workPublicActivityFansUser->id);
				MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $describe]);
				FansMsg::create($fans->id, 1, $describe, 2, FansMsg::TO_FANS);
			}
			//发送海报话术
			if ($publicActivity['poster_open'] == 1 && !empty($publicActivity['poster_describe'])) {
				$posterDescribe = self::replaceRanking($posterDescribe, $publicActivity["id"], empty($workPublicActivityFansUser) ? 0 : $workPublicActivityFansUser->id);
				MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $posterDescribe]);
				FansMsg::create($fans->id, 1, $posterDescribe, 2, FansMsg::TO_FANS);
			}
		}

		/**
		 * 公众号|| 公众号+企业微信
		 *
		 * @param $fromUserName
		 * @param $PublicActivity
		 * @param $activity
		 * @param $subscribe
		 *
		 * @return bool
		 *
		 * @throws Throwable
		 */
		public static function wechatPublicScanSendActivity ($fromUserName, $PublicActivity, $activity, $subscribe)
		{
			Yii::error($PublicActivity, '$PublicActivity');
			Yii::error($subscribe, '$subscribe');
			//获取粉丝
			try {
				if (Yii::$app->cache->exists($fromUserName . $PublicActivity["id"])) {
					return false;
				}
				Yii::$app->cache->set($fromUserName . $PublicActivity["id"], 1, 10);
				Yii::error($activity, '$activity');
				$fansInfo = Fans::findOne(["openid" => $fromUserName]);
				//添加活动粉丝列表
				$fans        = WorkPublicActivityFansUser::findOne(["activity_id" => $PublicActivity['id'], "fans_id" => $fansInfo->id]);
				$activity[5] = 0;
				//获取微信对象
				$wechat = WorkPublicPoster::getWxObject($PublicActivity['public_id']);
				//获取微信配置
				$wxConfig = WxAuthorize::find()->where(['author_id' => $PublicActivity["public_id"]])->select("authorizer_appid")->asArray()->one();
				//获取上级公众号用户
				$parentFans = WorkPublicActivityFansUser::find()->alias("f")
					->leftJoin("{{%fans}} as s", "f.fans_id = s.id")
					->select("s.openid,f.parent_id,f.tier,f.user_id,s.nickname,f.prize,f.id")
					->where(["f.id" => $activity[3]])->asArray()->one();
				if (!empty($parentFans)) {
					$activity[5] = empty($parentFans["tier"]) ? 0 : $parentFans["tier"];
				}
				if ($PublicActivity["start_time"] > time()) {
					MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => "活动未开始"]);
					FansMsg::create($fansInfo->id, 1, "活动未开始", 2, FansMsg::TO_FANS);

					return false;
				}
				//h活动结束
				if ($PublicActivity["end_time"] < time() || in_array($PublicActivity["is_over"], [WorkPublicActivity::IS_OVER_THREE, WorkPublicActivity::IS_OVER_FOUR, WorkPublicActivity::IS_OVER_TWO])) {
					WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $fansInfo->nickname, 0, $fromUserName, WorkPublicActivityConfigCall::TYPE_TWELVE, $fansInfo->id);

					return false;
				}
				if (!empty($fans) && $activity[3] == 0 && $subscribe == Fans::USER_SUBSCRIBE) {
					WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $fansInfo->nickname, $fans->id, $fromUserName, WorkPublicActivityConfigCall::TYPE_SIX, $fansInfo->id);

//					return false;
				}

				//判断人数限制
				if ($PublicActivity['type'] == WorkPublicActivity::ACTIVITY_TYPE_THREE && WorkPublicActivity::checkJoinNumIsMax($PublicActivity['id'])) {
					WorkPublicActivity::setActivityOver($PublicActivity['id']);
					$str = "很抱歉，当前活动过于火爆暂时无法参与";
					MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $str]);

					return false;
				}

				$channel_user_id = empty($PublicActivity["channel_user_id"]) ? 0 : $PublicActivity["channel_user_id"];
				if (!empty($fansInfo->unionid)) {
					$extUser = WorkExternalContactFollowUser::find()->alias("a")
						->leftJoin("{{%work_external_contact}} as b", "a.external_userid = b.id")
						->leftJoin("{{%work_contact_way_line}} as c", "c.external_userid = b.id")
						->where(["b.unionid" => $fansInfo->unionid])
						->andWhere(["in", "a.user_id", explode(",", $channel_user_id)])
						->select("a.id,c.gender,a.external_userid")
						->asArray()->one();
				} else {
					$extUser = [];
				}

				Yii::error($extUser, '$extUser');

				$describe        = WorkPublicActivityFansUser::replaceFansName($PublicActivity["describe"], $fansInfo->nickname);
				$poster_describe = WorkPublicActivityFansUser::replaceFansName($PublicActivity["poster_describe"], $fansInfo->nickname);
				// 是否已经范松Ranking文案
				$hasSendRanking = false;
				if ($subscribe == Fans::USER_UNSUBSCRIBE) {
					self::sendRanking($hasSendRanking, $describe, $PublicActivity, $wxConfig, $fromUserName, $fansInfo, $poster_describe, $fans);
				}

				if ($PublicActivity["type"] == 3 && !empty($extUser)) {
					$fansInfo->sex = $extUser["gender"];
				}
				//性别限定
				if ($fansInfo->sex == 0) {
					$fansInfo->sex = 3;
				}
				if (empty($fans) && $PublicActivity['sex_type'] != 4 && $PublicActivity['sex_type'] != $fansInfo->sex) {
					$sexMsg = "当前【" . $PublicActivity["activity_name"] . "】";
					if ($PublicActivity['sex_type'] == 1) {
						$sexMsg .= "仅限男性参加";
					} else if ($PublicActivity['sex_type'] == 2) {
						$sexMsg .= "仅限女性参加";
					} else {
						$sexMsg .= "仅限未知参加";
					}
					MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $sexMsg]);
					FansMsg::create($fansInfo->id, 1, $sexMsg, 2, FansMsg::TO_FANS);

					return false;
				}
				$level = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $PublicActivity['id'], "is_open" => 1])->asArray()->all();
				//奖品不足
				if (!empty($level)) {
					$sum      = 0;
					$infinite = true;
					foreach ($level as $item) {
						if (empty($item["num_old"]) || empty($item["money_count_old"])) {
							$infinite = false;
						}
						if ($item["type"] == 1 && !empty($item["num_old"])) {
							$sum += $item["num"];
						} else if ($item["type"] == 2 && !empty($item["money_count_old"])) {
							$sum += $item["money_count"];
						}
					}
					if ($sum == 0 && $infinite) {
						$call = WorkPublicActivityConfigCall::findOne(["type" => WorkPublicActivityConfigCall::TYPE_TEN, "activity_id" => $PublicActivity["id"], "is_open" => 1]);
						$call = self::replaceFansName($call->context, $fansInfo->nickname);
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $call]);

						return false;
					}
				}
				if (empty($fansInfo->activity_id) && strtotime($fansInfo->create_time) >= $PublicActivity["start_time"] && strtotime($fansInfo->create_time) < $PublicActivity["end_time"]) {
					$fansInfo->activity_id = $PublicActivity["id"];
					$fansInfo->save();
				}
				if (!empty($fans) && !empty($activity[3])) {
					//不允许自己给自己助力
					if ($activity[3] == $fans->id) {
						WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $fansInfo->nickname, $fans->id, $fromUserName, WorkPublicActivityConfigCall::TYPE_SIX, $fansInfo->id);
					}
					//不允许互助
					$mutualData = WorkPublicActivityFansUserDetail::find()
						->where(["activity_id" => $activity[1], "public_parent_id" => $fans->id, "public_user_id" => $activity[3]])
						->count();
					if ($mutualData > 0) {
						WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $parentFans["nickname"], $fans->id, $fromUserName, WorkPublicActivityConfigCall::TYPE_NINE, $fansInfo->id);

						return false;
					}
					//对用户重复助力
					$repetition = WorkPublicActivityFansUserDetail::find()
						->where(["activity_id" => $activity[1], "public_parent_id" => $activity[3], "public_user_id" => $fans->id])
						->andWhere("type is null")
						->count();
					if ($repetition >= 1) {
						$str = "抱歉，助力无效，您帮好友@" . $parentFans["nickname"] . "重复助力";
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $str]);
						FansMsg::create($fansInfo->id, 1, $str, 2, FansMsg::TO_FANS);

						return false;
					}
					if ($PublicActivity["type"] == WorkPublicActivity::ACTIVITY_TYPE_ONE) {
						//是否限定帮助次数
						$number = WorkPublicActivityFansUserDetail::find()
							->where(["activity_id" => $activity[1], "public_user_id" => $fans->id])
							->andWhere("(level_time is null or type = 1)")->count();
						if ($PublicActivity['number'] != 0 && $number >= $PublicActivity['number']) {
							WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $parentFans["nickname"], $fans->id, $fromUserName, WorkPublicActivityConfigCall::TYPE_FIFTEEN, $fansInfo->id);

							return false;
						}
					}

				}
				//仅公众号 不存在参与者，和公众号加+企业微信老用户不存在参与者
				if (($PublicActivity['type'] == WorkPublicActivity::ACTIVITY_TYPE_ONE && empty($fans)) || ($PublicActivity['type'] == WorkPublicActivity::ACTIVITY_TYPE_THREE && !empty($extUser) && empty($fans))) {
					//创建参与者或助力者
					$fansData = [
						"corp_id"     => $PublicActivity['corp_id'],
						"public_id"   => $PublicActivity['public_id'],
						"parent_id"   => ($activity[3] == 0) ? NULL : $activity[3],
						"activity_id" => $PublicActivity['id'],
						"fans_id"     => $fansInfo->id,
						"create_time" => time(),
					];
					if (!empty($extUser)) {
						$fansData['external_userid'] = $extUser['external_userid'];
					}

					//判断人数限制
					if (WorkPublicActivity::checkJoinNumIsMax($PublicActivity['id']) && $PublicActivity['type'] == WorkPublicActivity::ACTIVITY_TYPE_ONE) {
						WorkPublicActivity::setActivityOver($PublicActivity['id']);
						$str = "很抱歉，当前活动过于火爆暂时无法参与";
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $str]);

						return false;
					}

					$fans = WorkPublicActivityFansUser::setData($fansData);

					if (!$hasSendRanking) {
						self::sendRanking($hasSendRanking, $describe, $PublicActivity, $wxConfig, $fromUserName, $fansInfo, $poster_describe, $fans);
					}
				}
				//打标签
				if ($PublicActivity['type'] == WorkPublicActivity::ACTIVITY_TYPE_ONE && empty($fans->is_tags)) {
					$tags = json_decode($PublicActivity["label_id"], true);
					Yii::error($tags, '$tags');
					if (!empty($tags)) {
						if (in_array($PublicActivity['type'], [1, 2])) {
							self::SetWechatTags($PublicActivity, $fansInfo->openid, $fans, false);
						}
					}
				}
				//获取海报配置
				$posterConfig              = WorkPublicActivityPosterConfig::getPosterConfig($PublicActivity['id']);
				$posterConfig["code_url"]  = $PublicActivity['code_url'];
				$posterConfig["heard_url"] = $fansInfo['headerimg'];
				$posterConfig["userName"]  = $fansInfo->nickname;
				//仅公众号
				if ($PublicActivity["type"] == WorkPublicActivity::ACTIVITY_TYPE_ONE) {
					//层级
					$tier = self::setParentLeveL([0, $activity[1], $activity[3], $activity[5]], $fans);
					//生成海报
					$media_id = WorkPublicPoster::getPoster($PublicActivity, $fans, $posterConfig, $tier->id);
					//发送海报
					MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 2, ["media_id" => $media_id], true);
				} else {
					//公众号+企业微信
					if ($PublicActivity["type"] == WorkPublicActivity::ACTIVITY_TYPE_THREE && ((!empty($extUser) || empty($extUser)) && !empty($fans))) {
						//层级
						$tier = self::setParentLeveL([0, $activity[1], $activity[3], $activity[5]], $fans);
						//老用户
						//生成海报
						try {
							$media_id = WorkPublicPoster::getPoster($PublicActivity, $fans, $posterConfig, $tier->id);
						} catch (\Exception $E) {
							MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => "海报生成失败！可以回复关键词参加活动{" . $PublicActivity['keyword'] . "}快去回复吧"]);
							FansMsg::create($fansInfo->id, 1, "海报生成失败！可以回复关键词参加活动{" . $PublicActivity['keyword'] . "}快去回复吧", 2, FansMsg::TO_FANS);

							return false;
						}
						$user_id    = explode(",", $PublicActivity["channel_user_id"]);
						$follwoUser = WorkExternalContactFollowUser::find()->where(["external_userid" => $extUser['external_userid']])->andWhere(["in", "user_id", $user_id])->one();
						if ($follwoUser || (!empty($fans) && empty($extUser))) {
							if ($activity[3] != $fans->id) {
								if ($activity[3] != 0) {
									MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => "您已是老用户啦，无法助力了哦，可以转发下方专属海报参与活动哦"]);
								}
								self::setRecord([
									"activity_id"      => $activity[1],
									"public_parent_id" => $activity[3],
									"type"             => 1,
									"is_remind"        => 1,
									"public_user_id"   => $fans->id,
									"fans_id"          => $fansInfo->id,
								]);
								$str = self::replaceFansName("【{nickname}】已是老用户啦，无法助力了哦~", $fansInfo->nickname);
								MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => $str]);
								FansMsg::create($parentFans['id'], 1, $str, 2, FansMsg::TO_FANS);
							}
						}
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 2, ["media_id" => $media_id], true);
					} else {
						if (!$hasSendRanking) {
							self::sendRanking($hasSendRanking, $describe, $PublicActivity, $wxConfig, $fromUserName, $fansInfo, $poster_describe, $fans);
						}
						$workApi      = WorkUtils::getWorkApi($PublicActivity['corp_id'], WorkUtils::EXTERNAL_API);
						$channel_user = explode(",", $PublicActivity["channel_user_id"]);
						$path         = WorkPublicActivity::CheckCorpUser($workApi, $PublicActivity["id"], $channel_user, $activity[3], $activity[5], true, 2);
						$array        = [
							"activity_id"      => $PublicActivity["id"],
							"public_parent_id" => $activity[3],
							"type"             => 5,
							"fans_id"          => $fansInfo->id,
						];
						$record       = WorkPublicActivityFansUserDetail::findOne($array);
						if (!empty($record)) {
							$record->type        = 5;
							$record->create_time = time();
							$record->save();
						} else {
							$array["create_time"] = time();
							WorkPublicActivityFansUserDetail::setData($array);
						}
						$result = $wechat->uploadMedia(Yii::$app->basePath . $path, "image");
						unlink(Yii::$app->basePath . $path);
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 2, ["media_id" => $result["media_id"]], true);
						if ($activity[3] != 0) {
							WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $fansInfo->nickname, 0, $parentFans['openid'], WorkPublicActivityConfigCall::TYPE_FOURTEEN, $parentFans['id']);
						}
					}

					return false;
				}
				if ($activity[3] == 0) {
					return false;
				}
				if ($activity[3] == $fans->id) {
					return false;
				}

				//免打扰
				$disturbing = WorkPublicActivityFansUserDetail::find()
					->where(["activity_id" => $activity[1], "public_parent_id" => $activity[3]])
					->andFilterWhere(["between", "create_time", strtotime(date("Y-m-d")), strtotime(date("Y-m-d")) + 86399])
					->count();
				$remind     = true;
				if ($disturbing > 3) {
					$remind = false;
				}
				if ($disturbing == 3) {
					WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $fansInfo->nickname, $parentFans['id'], $parentFans['openid'], WorkPublicActivityConfigCall::TYPE_ELEVEN, $parentFans['id']);
					$remind = false;
				}
				//查询明细
				$userDetail = WorkPublicActivityFansUserDetail::find()
					->where(["activity_id" => $activity[1], "public_parent_id" => $activity[3], "public_user_id" => $fans->id])
					->andWhere("type is null")
					->count();
				//记录助力数据
				if (empty($userDetail) || $PublicActivity['number'] == 0) {
					//增加上级助力人数
					WorkPublicActivityFansUser::setActivityNumIncr($parentFans['openid'], $activity[3], $activity[1], $PublicActivity['public_id'], $fansInfo->nickname);
					//添加|更新助力记录
					self::setRecord([
						"activity_id"      => $activity[1],
						"public_parent_id" => $activity[3],
						"public_user_id"   => $fans->id,
						"is_remind"        => 1,
						"fans_id"          => $fansInfo->id,
					]);
					//增加当前参与者上级明细
					$parentIds = explode(",", $fans->parent_id);
					if (!in_array($activity[3], $parentIds)) {
						if (empty($fans->parent_id)) {
							$fans->parent_id = $activity[3];
						} else {
							$fans->parent_id = $fans->parent_id . "," . $activity[3];
						}
						$fans->save();
					}
					//帮好友助力成功
					WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $parentFans['nickname'], $fans->id, $fromUserName, WorkPublicActivityConfigCall::TYPE_EIGHT, $fansInfo->id);
					if (!empty($parentFans["prize"])) {
						WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $PublicActivity['id'], $fansInfo->nickname, $parentFans['id'], $parentFans['openid'], WorkPublicActivityConfigCall::TYPE_THIRTEEN, $parentFans['id']);

						return false;
					}
					$prize = WorkPublicActivityFansUser::find()->alias("a")
						->leftJoin("{{%work_public_activity_prize_user}} as c", "a.prize = c.id")
						->select("a.prize,c.order_sn")
						->where(["a.id" => $parentFans["id"]])->asArray()->one();
					Yii::error($prize, '$prize');
					if (!empty($prize["prize"]) || !empty($prize["order_sn"])) {
						return false;
					}
					if ($remind) {
						//新助力提醒父级
						$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $PublicActivity['id'], "type" => WorkPublicActivityConfigCall::TYPE_ONE, "is_open" => 1])->one();
						if (!empty($call)) {
							$call = $call->toArray();
							if ($call['is_template']) {
								//模板发送
								$url      = self::getTemplateUrl($call["is_url"], $PublicActivity["id"], $PublicActivity['public_id'], $activity[3]);
								$callText = WorkPublicActivityFansUser::replaceLevelAll($fansInfo->nickname, $call["template_context"], $PublicActivity['id'], $activity[3], true);
								$wechat->sendTemplateMessage($parentFans['openid'], $call["template_id"], json_decode($callText, true), $url, NULL, 1);
							} else {
								$callText = WorkPublicActivityFansUser::replaceLevelAll($fansInfo->nickname, $call["context"], $PublicActivity['id'], $activity[3]);
								MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => $callText]);
								FansMsg::create($parentFans['id'], 1, $callText, 2, FansMsg::TO_FANS);
							}
						}
					}
				}

			} catch (\Exception $e) {
				\Yii::error($e->getFile(), "sym-wx-scan");
				\Yii::error($e->getLine(), "sym-wx-scan");
				\Yii::error($e->getCode(), "sym-wx-scan");
				\Yii::error($e->getMessage(), "sym-wx-scan");
			}
		}

		/**
		 * @param $fans_id
		 * @param $author_id
		 *扣除人数
		 */
		public static function ActivityDnfollowDel ($fans_id, $author_id)
		{
			//取消关注扣除活动人数
			$publicFansId = WorkPublicActivityFansUser::find()->alias("au")
				->leftJoin("{{%fans}} as f", "au.fans_id = f.id")
				->leftJoin("{{%work_public_activity}} as  wpa", "wpa.id = au.activity_id")
				->where(["au.fans_id" => $fans_id, "wpa.is_over" => 1, "au.public_id" => $author_id])
				->andWhere("wpa.end_time > UNIX_TIMESTAMP() and au.parent_id is not null and au.parent_id !=0")
				->select("wpa.activity_name,wpa.action_type,wpa.flow,au.prize,au.corp_id,f.subscribe,au.user_id,au.parent_id,f.nickname,au.activity_id,wpa.type,au.id,wpa.not_attention,wpa.public_id,f.unionid")->asArray()->all();
			\Yii::error($publicFansId, '$publicFansId');
			if (!empty($publicFansId)) {
				foreach ($publicFansId as $item) {
					$fans          = self::findOne($item["id"]);
					$fans->is_tags = NULL;
					$fans->tags    = NULL;
					$fans->save();
					if ($item['not_attention'] == 1) {
						continue;
					}
					$parentIds = explode(",", $item["parent_id"]);
					if (empty($parentIds)) {
						continue;
					}
					$wxConfig = WxAuthorize::find()->where(['author_id' => $item["public_id"]])->select("authorizer_appid")->asArray()->one();
					//取关
					if ($item['not_attention'] == WorkPublicActivity::NOT_ATT_TWO && ($item["type"] == 1 || $item["type"] == 3)) {
						self::CorpDelActivitySendMessage($wxConfig, $parentIds, $item, $fans, 2);
					}
					//取关+删除员工
					if ($item['not_attention'] == WorkPublicActivity::NOT_ATT_THREE && $item["type"] == 3) {
						$extId = WorkExternalContact::find()->where(["unionid" => $item["unionid"], "corp_id" => $item["corp_id"]])->select("id")->asArray()->one();
						if (!empty($item["user_id"])) {
							$followUser = WorkExternalContactFollowUser::find()->where(["user_id" => $item["user_id"], "activity_id" => $item["activity_id"], "external_userid" => $extId["id"], "del_type" => 2])->exists();
							\Yii::error($followUser, '$followUser');
							if ($followUser) {
								self::CorpDelActivitySendMessage($wxConfig, $parentIds, $item, $fans, 3);
							}
						}
					}
				}
			}
		}

		public static function CorpDelActivitySendMessage ($wxConfig, $parentIds, $item, $fans, $type)
		{
			$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $item['activity_id'], "type" => WorkPublicActivityConfigCall::TYPE_TWO, "is_open" => 1])->one()->toArray();
			Yii::error($call, '$call');
			foreach ($parentIds as $key => $parentId) {
				/** @var WorkPublicActivityFansUserDetail $fansUserDetail * */
				$fansUserDetail = WorkPublicActivityFansUserDetail::find()
					->where(["public_parent_id" => $parentId, "public_user_id" => $item["id"], "activity_id" => $item['activity_id']])
					->andWhere("level_time is null")
					->one();
				if (!empty($fansUserDetail)) {
					$fansUserDetail->type       = $type;
					$fansUserDetail->level_time = time();
					$fansUserDetail->save();
					WorkPublicActivityFansUser::setActivityNumDecr($parentId, $item['activity_id']);
					$fans->parent_id = NULL;
					$fans->save();
					$parentFans = WorkPublicActivityFansUser::find()->alias("f")
						->leftJoin("{{%fans}} as s", "f.fans_id = s.id")
						->select("s.openid,f.prize,s.id")
						->where(["f.id" => $parentId])->asArray()->one();
					if (!empty($parentFans) && !empty($call)) {
						if ($item["flow"] == 1 && !empty($parentFans["prize"])) {
							$str = "您的好友 {nickname} 放弃为你助力！\r\n人气值-1，但不影响您已获得的奖品。\r\n";
							if ($item["action_type"] == 1) {
								$str .= "<a href='{url}'>点此领取奖品吧~</a>";
							} else {
								$str .= "快去联系客服领取奖品吧~";
							}
							$str = self::replaceLevelAll($item['nickname'], $str, $item['activity_id'], $parentId);
							MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => "任务活动--" . $item["activity_name"] . "\r\n" . $str]);
							FansMsg::create($parentFans['id'], 1, $str, 2, FansMsg::TO_FANS);
							continue;
						}
						if (!empty($call['is_template'])) {
							//模板发送
							$wechat   = WorkPublicPoster::getWxObject($item['public_id']);
							$url      = self::getTemplateUrl($call["is_url"], $item["activity_id"], $item['public_id'], $parentId);
							$callText = WorkPublicActivityFansUser::replaceLevelAll($item['nickname'], $call["template_context"], $item['activity_id'], $parentId, true);
							$wechat->sendTemplateMessage($parentFans['openid'], $call["template_id"], json_decode($callText, true), $url, NULL, 1);
						} else {
							$callText = WorkPublicActivityFansUser::replaceLevelAll($item['nickname'], $call["context"], $item['activity_id'], $parentId);
							MsgUtil::send($wxConfig["authorizer_appid"], $parentFans['openid'], 1, ["text" => "任务活动--" . $item["activity_name"] . "\r\n" . $callText]);
							FansMsg::create($parentFans['id'], 1, $callText, 2, FansMsg::TO_FANS);
						}
					}
				}
			}
		}

		public static function getTemplateUrl ($is_url, $activity_Id, $public, $fans_id)
		{
			if ($is_url == 1) {
				$url = Yii::$app->params["web_url"] . WorkPublicActivity::RANKING . "?activity_id=" . $activity_Id . "&fans_id=" . $fans_id;
			} else if ($is_url == 2) {
				$url = self::getSuccessUrl($public, $activity_Id);
			} else {
				$url = NULL;
			}
			Yii::error($url, '$url');

			return $url;
		}

		/**
		 * @param $fromUserName
		 * @param $activity
		 * @param $subscribe
		 *微信公众号和公众号+企业微信文本回复
		 *
		 * @throws Throwable
		 */
		public static function WechatActivityTextMsg ($fromUserName, $activity, $subscribe)
		{
			try {
				if (!empty($activity)) {
					if (Yii::$app->cache->exists($fromUserName . $activity["id"])) {
						return false;
					}
					Yii::$app->cache->set($fromUserName . $activity["id"], 1, 5);
					$wxConfig = WxAuthorize::find()->where(['author_id' => $activity["public_id"]])->select("authorizer_appid")->asArray()->one();
					$fansInfo = Fans::findOne(['openid' => $fromUserName]);
					Yii::error($fansInfo, '$fansInfo');
					if ($activity["start_time"] > time()) {
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => "活动未开始"]);
						FansMsg::create($fansInfo->id, 1, "活动未开始", 2, FansMsg::TO_FANS);

						return false;
					}

					if ($activity["is_over"] != 1) {
						$call = WorkPublicActivityConfigCall::find()->where(["activity_id" => $activity["id"], "type" => WorkPublicActivityConfigCall::TYPE_TWELVE, "is_open" => 1])->one();
						if (!empty($call)) {
							$call = $call->toArray();
							$call = WorkPublicActivityFansUser::replaceFansName($call["context"], $fansInfo->nickname);
							MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $call]);
							FansMsg::create($fansInfo->id, 1, $call, 2, FansMsg::TO_FANS);

						}

						return false;
					}
					if (!empty($fansInfo['unionid'])) {
						$extUser = WorkExternalContactFollowUser::find()->alias("a")
							->leftJoin("{{%work_external_contact}} as b", "a.external_userid = b.id")
							->leftJoin("{{%work_contact_way_line}} as c", "c.external_userid = b.id")
							->where(["b.unionid" => $fansInfo['unionid']])
							->andWhere(["in", "a.user_id", explode(",", $activity["channel_user_id"])])
							->select("a.id,c.gender,a.external_userid")
							->asArray()->one();
					} else {
						$extUser = [];
					}

					if ($activity["type"] == 3 && !empty($extUser)) {
						$fansInfo->sex = $extUser["gender"];
					}
					//性别限定
					if ($fansInfo->sex == 0) {
						$fansInfo->sex = 3;
					}
					/** @var WorkPublicActivityFansUser $fans * */
					$fans = WorkPublicActivityFansUser::find()->where(["activity_id" => $activity['id'], "fans_id" => $fansInfo->id])->one();
					if (!empty($fans)) {
						WorkPublicActivityConfigCall::sendCallText($wxConfig["authorizer_appid"], $activity['id'], $fansInfo->nickname, $fans->id, $fromUserName, WorkPublicActivityConfigCall::TYPE_SIX, $fansInfo->id);
					}
					if (empty($fans) && $activity['sex_type'] != 4 && $activity['sex_type'] != $fansInfo->sex) {
						$sexMsg = "当前【" . $activity["activity_name"] . "】";
						if ($activity['sex_type'] == 1) {
							$sexMsg .= "仅限男性参加";
						} else if ($activity['sex_type'] == 2) {
							$sexMsg .= "仅限女性参加";
						} else {
							$sexMsg .= "仅限未知参加";
						}
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $sexMsg]);
						FansMsg::create($fansInfo->id, 1, $sexMsg, 2, FansMsg::TO_FANS);

						return false;
					}
					$level = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activity['id'], "is_open" => 1])->asArray()->all();
					//奖品不足
					if (!empty($level)) {
						$sum      = 0;
						$infinite = true;
						foreach ($level as $item) {
							if (empty($item["num_old"]) || empty($item["money_count_old"])) {
								$infinite = false;
							}
							if ($item["type"] == 1 && !empty($item["num_old"])) {
								$sum += $item["num"];
							} else if ($item["type"] == 2 && !empty($item["money_count_old"])) {
								$sum += $item["money_count"];
							}
						}
						if ($sum == 0 && $infinite) {
							$call = WorkPublicActivityConfigCall::findOne(["type" => WorkPublicActivityConfigCall::TYPE_TEN, "activity_id" => $activity["id"], "is_open" => 1]);
							$call = self::replaceFansName($call->context, $fansInfo->nickname);
							MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 1, ["text" => $call]);

							return false;
						}
					}
					if (empty($fansInfo->activity_id) && strtotime($fansInfo->create_time) >= $activity["start_time"] && strtotime($fansInfo->create_time) < $activity["end_time"]) {
						$fansInfo->activity_id = $activity["id"];
						$fansInfo->save();
					}
					$describe        = WorkPublicActivityFansUser::replaceFansName($activity['describe'], $fansInfo->nickname);
					$poster_describe = WorkPublicActivityFansUser::replaceFansName($activity['poster_describe'], $fansInfo->nickname);
					$hasSendRanking  = false;
					//企业微信+公众号新用户
					if (empty($extUser) && $activity['type'] == 3 && empty($fans) && empty($activity[3])) {
						self::sendRanking($hasSendRanking, $describe, $activity, $wxConfig, $fromUserName, $fansInfo, $poster_describe, $fans);
						//新用户
						$wechat = WorkPublicPoster::getWxObject($activity['public_id']);
						$result = $wechat->uploadMedia(\Yii::$app->basePath . $activity['qc_url'], "image");
						Yii::error($result, '$result');
						MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 2, ["media_id" => $result["media_id"]], true);

						return false;
					}
					if (($activity['type'] == 1 && empty($fans)) || ($activity['type'] == 3 && !empty($extUser) && empty($fans))) {
						//添加活动粉丝列表
						$fans = new WorkPublicActivityFansUser();
						$fans->setAttributes([
							"corp_id"     => $activity['corp_id'],
							"public_id"   => $activity['public_id'],
							"activity_id" => $activity['id'],
							"fans_id"     => $fansInfo->id,
							"create_time" => time(),
						]);
						if (!empty($extUser)) {
							$fans->external_userid = $extUser["external_userid"];
						}
						$fans->save();
						self::sendRanking($hasSendRanking, $describe, $activity, $wxConfig, $fromUserName, $fansInfo, $poster_describe, $fans);
					}

					$tags = json_decode($activity["label_id"], true);
					if (!empty($tags) && !empty($fans) && $activity["type"] == 1) {
						if (empty($fans->is_tags) && $activity['type'] != 2) {
							$tags     = Tags::find()->where(["author_id" => $activity["public_id"]])->andWhere(["in", "tag_id", $tags])->select("id")->asArray()->all();
							$tags     = array_column($tags, "id");
							$wxConfig = WxAuthorize::find()->where(['author_id' => $activity["public_id"]])->select("authorizer_appid")->asArray()->one();
							try {
								Tags::giveUserTags($wxConfig['authorizer_appid'], $activity["public_id"], $tags, [$fansInfo->openid], 0, 1);
							} catch (\Exception $e) {
								Yii::error($e->getMessage(), "wechat-text-err-message");
							}
							$fans->is_tags = 1;
							$fans->tags    = $activity["label_id"];
							$fans->save();
						}
					}
					$tier = WorkPublicActivityTier::findOne(["activity_id" => $activity["id"], "fans_id" => $fans->id, "level" => 1]);
					if (empty($tier)) {
						$tier              = new WorkPublicActivityTier();
						$tier->fans_id     = $fans->id;
						$tier->activity_id = $activity["id"];
						$tier->level       = '1';
						$tier->create_time = time();
						$tier->save();
					}
					if ((empty($tier) && $activity['type'] == 1) || (!empty($extUser) && $activity['type'] == 3) && empty($tier)) {
						$tier              = new WorkPublicActivityTier();
						$tier->fans_id     = $fans->id;
						$tier->activity_id = $activity['id'];
						$tier->level       = '1';
						$tier->create_time = time();
						$tier->save();
					}

					//获取海报配置
					$posterConfig              = WorkPublicActivityPosterConfig::find()->where(["activity_id" => $activity['id']])->asArray()->one();
					$posterConfig["code_url"]  = $activity['code_url'];
					$posterConfig["heard_url"] = $fansInfo['headerimg'];
					$posterConfig["userName"]  = $fansInfo['headerimg'];
					$posterConfig["userName"]  = $fansInfo['nickname'];
					//生成海报
					$media_id = WorkPublicPoster::getPoster($activity, $fans, $posterConfig, $tier->id);
					MsgUtil::send($wxConfig["authorizer_appid"], $fromUserName, 2, ["media_id" => $media_id], true);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getFile(), "sym-wx-error");
				\Yii::error($e->getLine(), "sym-wx-error");
				\Yii::error($e->getMessage(), "sym-wx-error");
			}
		}

	}
