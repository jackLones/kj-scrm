<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Matrix\Exception;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%fans_time_line}}".
	 *
	 * @property int    $id
	 * @property int    $fans_id     粉丝ID
	 * @property string $openid      用户的标识，对当前公众号唯一
	 * @property string $event       行为
	 * @property string $event_time  行为发生时间
	 * @property string $create_time 创建时间
	 * @property int    $scene_id    参数二维码id
	 * @property int    $source      来源
	 * @property int    $remark      备注
	 *
	 * @property Fans   $fans
	 */
	class FansTimeLine extends \yii\db\ActiveRecord
	{
		const SUBSCRIBE_EVENT = 'subscribe';
		const UNSUBSCRIBE_EVENT = 'unsubscribe';
		const SCAN_EVENT = 'scan';
		const CLICK_EVENT = 'click';
		const VIEW_EVENT = 'view';
		const VIEW_MINIPROGRAM = 'view_miniprogram';
		const ADD_REMARK_EVENT = 'add_remark';
		const MODIFY_REMARK_EVENT = 'modify_remark';
		const REMOVE_REMARK_EVENT = 'remove_remark';

		const SEND_TEXT = 'text';
		const SEND_IMAGE = 'image';
		const SEND_VOICE = 'voice';
		const SEND_VIDEO = 'video';
		const SEND_SHORTVIDEO = 'shortVideo';
		const SEND_LOCATION = 'location';
		const SEND_LINK = 'link';

		const MODIFY_FIELD_EVENT = 'modify_field';//修改粉丝自定义属性
		const FOLLOW_EVENT = 'follow';//粉丝跟进

		//event 新增的事件 group 群发 news 图文  give_tag 打标签  remove_tag 移除标签  add_remark 添加备注  modify_remark 修改备注 kefu 客服消息 template 模板

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans_time_line}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['fans_id'], 'integer'],
				[['event'], 'string'],
				[['event_time', 'create_time'], 'safe'],
				[['openid'], 'string', 'max' => 80],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'fans_id'     => Yii::t('app', '粉丝ID'),
				'openid'      => Yii::t('app', '用户的标识，对当前公众号唯一'),
				'event'       => Yii::t('app', '行为'),
				'event_time'  => Yii::t('app', '行为发生时间'),
				'create_time' => Yii::t('app', '创建时间'),
				'scene_id'    => Yii::t('app', '参数二维码id'),
				'source'      => Yii::t('app', '来源'),
				'remark'      => Yii::t('app', '备注'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}

		/**
		 * 创建时间线
		 *
		 * @param int               $fansId
		 * @param string            $event
		 * @param int|string        $time
		 * @param int               $sceneId
		 * @param int               $source 4修改粉丝自定义属性
		 * @param null|string|array $remark
		 * @param int               $type
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($fansId, $event, $time, $sceneId = 0, $source = 0, $remark = NULL, $type = 0)
		{
			$timeLine = static::findOne(['fans_id' => $fansId, 'event' => $event, 'event_time' => DateUtil::getFormattedTime($time)]);
			//if (empty($timeLine)) {
			$timeLine              = new FansTimeLine();
			$timeLine->create_time = DateUtil::getCurrentTime();
			//}
			$fansInfo             = Fans::findOne($fansId);
			$timeLine->fans_id    = $fansId;
			$timeLine->openid     = $fansInfo->openid;
			$timeLine->event      = $event;
			$timeLine->event_time = DateUtil::getFormattedTime($time);
			$timeLine->scene_id   = $sceneId;
			$timeLine->source     = $source;
			$timeLine->remark     = is_array($remark) ? Json::encode($remark, JSON_UNESCAPED_UNICODE) : $remark;
			//同步粉丝时 只有当粉丝活跃时间为空时再更新
			if ($type == 1 && !empty($fansInfo->last_time)) {
				return false;
			}
//			if ($source == 0 && $event != 'group' && $event != 'news' && $event != 'give_tag' && $event != 'remove_tag' && $event!='remove_remark' && $event != 'add_remark' && $event != 'modify_remark') {
//				// 更行粉丝的最后活跃时间
//				$fansInfo->last_time = strtotime($timeLine->event_time);
//			}
			if ($source == 0 && ($event == 'text' || $event == 'image' || $event == 'view_miniprogram' || $event == 'voice' || $event == 'video' || $event == 'location' || $event == 'shortVideo' || $event == 'link' || $event == 'subscribe' || $event == 'unsubscribe' || $event == 'scan' || $event == 'view' || $event == 'click')) {
				// 更行粉丝的最后活跃时间
				$fansInfo->last_time = strtotime($timeLine->event_time);
			}
			$fansInfo->save();

			if (!$timeLine->validate() || !$timeLine->save()) {
				throw new InvalidDataException(SUtils::modelError($timeLine));
			}

			//判断当前粉丝是否是客户
			try {
				\Yii::error($event, '$event');
				if ($event == static::SUBSCRIBE_EVENT) {
					if(!empty($fansInfo->unionid)){
						$externalContact = WorkExternalContact::findOne(['unionid' => $fansInfo->unionid]);
						if (!empty($externalContact)) {
							$fansInfo->external_userid = $externalContact->id;
							$fansInfo->save();
							$relation = UserAuthorRelation::findOne(['author_id' => $fansInfo->author_id]);
							//记录客户轨迹
							$subscribeScene = $fansInfo->subscribe_scene ? Fans::getSubscribeScene($fansInfo->subscribe_scene) : "其他";
							$remark         = '客户通过【' . $subscribeScene . '】来源关注公众号【' . $fansInfo->author->wxAuthorizeInfo->nick_name . '】';
							\Yii::error($remark, '$remark');
							ExternalTimeLine::addExternalTimeLine(['uid' => $relation->uid, 'external_id' => $externalContact->id, 'event' => static::SUBSCRIBE_EVENT, 'remark' => $remark]);
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'message');
			}



			return true;
		}
	}
