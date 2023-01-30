<?php

	namespace app\models;

	use app\queue\SyncWorkAddTagJob;
	use Yii;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use app\components\InvalidDataException;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;

	/**
	 * This is the model class for table "{{%awards_join}}".
	 *
	 * @property int            $id
	 * @property int            $award_id    抽奖活动id
	 * @property int            $openid      参与者身份openid
	 * @property int            $external_id 外部联系人id
	 * @property string         $config_id   联系方式的配置id
	 * @property string         $nick_name   昵称
	 * @property string         $avatar      头像
	 * @property string         $qr_code     联系二维码的URL
	 * @property string         $state       企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
	 * @property int            $num         获得的抽奖次数
	 * @property string         $last_time   最后一次抽奖时间
	 * @property string         $create_time 参与时间
	 * @property string         $tags        活动标签
	 *
	 * @property AwardsActivity $award
	 */
	class AwardsJoin extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%awards_join}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['award_id', 'external_id', 'num'], 'integer'],
				[['last_time', 'create_time'], 'safe'],
				[['config_id', 'nick_name', 'state'], 'string', 'max' => 64],
				[['qr_code', 'avatar', 'tags'], 'string', 'max' => 255],
				[['award_id'], 'exist', 'skipOnError' => true, 'targetClass' => AwardsActivity::className(), 'targetAttribute' => ['award_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'award_id'    => '抽奖活动id',
				'openid'      => '参与者身份openid',
				'external_id' => '外部联系人id',
				'config_id'   => '联系方式的配置id',
				'nick_name'   => '昵称',
				'avatar'      => '头像',
				'qr_code'     => '联系二维码的URL',
				'state'       => '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值',
				'num'         => '获得的抽奖次数',
				'last_time'   => '最后一次抽奖时间',
				'create_time' => '参与时间',
				'tags'        => '活动标签',
			];
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAward ()
		{
			return $this->hasOne(AwardsActivity::className(), ['id' => 'award_id']);
		}

		/**
		 * @param $externalUserId
		 * @param $follow_user_id
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function setJoinData ($externalUserId, $stateArr = [])
		{
			$awardId    = isset($stateArr[1]) ? intval($stateArr[1]) : 0;
			$externalId = isset($stateArr[2]) ? intval($stateArr[2]) : 0;//上级id 来源work_external_contact的id
			$awardInfo  = AwardsActivity::findOne($awardId);
			if (empty($awardInfo) || empty($externalUserId) || $awardInfo->status != 1) {
				return false;
			}
			$contact = WorkExternalContact::findOne($externalUserId);
			//是否有性别的限制
			$is_limit = 0;
			if ($awardInfo->sex_type != 1) {
				$is_limit = RedPack::checkSex($externalUserId, $awardInfo->sex_type);
			}
			if (empty($is_limit)) {
				$joinId = static::add($awardId, $externalUserId, $externalId, $contact, $awardInfo->init_num, $awardInfo->share_setting, 1, 1);
			}

			return !empty($joinId) ? $joinId : 0;

		}

		/**
		 * @param int                 $awardId        活动id
		 * @param int|String          $externalUserId 当前参与者id
		 * @param int|String          $externalId     上级id
		 * @param WorkExternalContact $contact
		 * @param int|String          $init_num       初始次数
		 * @param string              $share_setting
		 * @param int                 $is_auto        是否自动助力 0 否 1是
		 * @param int                 $isTag          是否打标签 0打 1不打
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function add ($awardId, $externalUserId, $externalId, $contact, $init_num, $share_setting, $is_auto = 0, $isTag = 0)
		{
			\Yii::error($externalUserId, '$externalUserId-1');
			/**@var WorkExternalContact $contact * */
			//设置参与者
			$joinId        = 0;
			$awardActivity = AwardsActivity::findOne($awardId);
			$awardJoin     = AwardsJoin::findOne(['award_id' => $awardId, 'external_id' => $externalUserId]);
			if (empty($awardJoin) && $awardActivity->status == 1) {
				$new_apply_setting = json_decode($awardActivity->apply_setting, true);
				if (empty($new_apply_setting['limit'])) {
					$initNum = $awardActivity->init_num;
				} else {
					if ($new_apply_setting['total_num'] >= $awardActivity->init_num) {
						$initNum = $awardActivity->init_num;
					} else {
						$initNum = $new_apply_setting['total_num'];
					}
				}
				$awardJoin              = new AwardsJoin();
				$awardJoin->award_id    = $awardId;
				$awardJoin->external_id = $externalUserId;
				$awardJoin->openid      = $contact->openid;
				$awardJoin->nick_name   = $contact->name;
				$awardJoin->avatar      = $contact->avatar;
				$awardJoin->num         = $initNum;
				$awardJoin->create_time = DateUtil::getCurrentTime();
				if (!$awardJoin->validate() || !$awardJoin->save()) {
					throw new InvalidDataException(SUtils::modelError($awardJoin));
				}
				if (!empty($awardActivity->tag_ids)) {
					if(empty($awardJoin->tags)){
						$awardJoin->tags = $awardActivity->tag_ids;
						$awardJoin->save();
					}
				}
				//打标签
				if (!empty($awardActivity->tag_ids) && $isTag == 0) {
					$userKeys = !empty($awardActivity->user_key) ? json_decode($awardActivity->user_key, true) : [];
					if (!empty($userKeys)) {
						$users = [];
						foreach ($userKeys as $val) {
							array_push($users, $val['id']);
						}
						$followId   = [];
						$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $externalUserId, 'user_id' => $users])->asArray()->all();
						if (!empty($followUser)) {
							foreach ($followUser as $user) {
								array_push($followId, $user['id']);
							}
						}
						$tag_ids = explode(',', $awardActivity->tag_ids);
						\Yii::$app->queue->push(new SyncWorkAddTagJob([
							'type'      => 2,
							'user_ids'  => $followId,
							'tag_ids'   => $tag_ids,
							'otherData' => ['type' => 'fission', 'msg' => '抽奖引流【' . $awardActivity->title . '】'],
						]));
					}

				}
			}
			if (!empty($is_auto) && !empty($externalId) && $externalUserId != $externalId && !empty($awardActivity->is_share_open) && $awardActivity->status == 1) {
				//来源于上级
				$parentJoin = AwardsJoin::findOne(['award_id' => $awardId, 'external_id' => $externalId]);
				if (!empty($parentJoin)) {
					$detail = AwardsJoinDetail::findOne(['awards_join_id' => $parentJoin->id, 'external_id' => $externalUserId]);
					if (!empty($parentJoin) && empty($detail)) {
						//给上级增加抽奖次数
						$shareSetting = json_decode($share_setting, true);

						$awardShare              = new AwardsShare();
						$awardShare->join_id     = $parentJoin->id;
						$awardShare->num         = $shareSetting[0]['total_num'];
						$awardShare->create_time = DateUtil::getCurrentTime();
						$awardShare->save();

						$shareNum = $shareSetting[0]['total_num']; //分享一次增加的抽奖次数
						$dayNum   = $shareSetting[1]['day_num']; //日分享获得最大抽奖次数
						$limit    = $shareSetting[1]['limit']; //0代表 日分享获得最大抽奖次数不限
						if (empty($limit)) {
							$parentJoin->num = $parentJoin->num + $shareNum;
						} else {
							$dayStart = date('Y-m-d') . ' 00:00:00';
							$dayEnd   = date('Y-m-d') . ' 23:59:59';
							$num      = 0;
							$share    = AwardsShare::find()->where(['join_id' => $parentJoin->id])->andFilterWhere(['between', 'create_time', $dayStart, $dayEnd])->asArray()->all();
							foreach ($share as $sh) {
								$num += $sh['num'];
							}
							\Yii::error($num, '$num');//4   2
							\Yii::error($dayNum, '$dayNum');//1  1
							\Yii::error($shareNum, '$shareNum');//2  2
							if ($dayNum <= $shareNum) {
//							if ($num > $dayNum) {
//								//return false;
//							}else{
//								$parentJoin->num = $parentJoin->num + $dayNum;
//							}
								if (count($share) <= 1) {
									$parentJoin->num = $parentJoin->num + $dayNum;
								}
							} else {
								if ($num > $dayNum) {
									if (($num - $dayNum) >= $shareNum) {
										//return false;
									} else {
										$parentJoin->num = $parentJoin->num + ($shareNum - ($num - $dayNum));
									}
								} else {
									$parentJoin->num = $parentJoin->num + $shareNum;
								}
							}
						}
						$parentJoin->save();

						$joinDetail                 = new AwardsJoinDetail();
						$joinDetail->awards_join_id = $parentJoin->id;
						$joinDetail->external_id    = $externalUserId;
						$joinDetail->create_time    = DateUtil::getCurrentTime();
						$joinDetail->save();

						//打标签
						if (!empty($awardActivity->tag_ids) && $isTag == 0) {
							$userKeys = !empty($awardActivity->user_key) ? json_decode($awardActivity->user_key, true) : [];
							if (!empty($userKeys)) {
								$users = [];
								foreach ($userKeys as $val) {
									array_push($users, $val['id']);
								}
								$followId   = [];
								$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $externalUserId, 'user_id' => $users])->asArray()->all();
								if (!empty($followUser)) {
									foreach ($followUser as $user) {
										array_push($followId, $user['id']);
									}
								}
								$tag_ids = explode(',', $awardActivity->tag_ids);
								\Yii::$app->queue->delay(1)->push(new SyncWorkAddTagJob([
									'type'      => 2,
									'user_ids'  => $followId,
									'tag_ids'   => $tag_ids,
									'otherData' => ['type' => 'fission', 'msg' => '抽奖引流【' . $awardActivity->title . '】'],
								]));

							}
						}
					}
				}
			}
			if (!empty($awardJoin)) {
				$joinId = $awardJoin->id;
			}

			return $joinId;
		}

		/**
		 * @param $joinId
		 *
		 * @return mixed|string
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function changeConfig ($joinId)
		{
			$awardJoin = static::findOne($joinId);
			if (empty($awardJoin->qr_code)) {
				$state          = AwardsActivity::AWARD_HEAD . '_' . $awardJoin->award_id . '_' . $awardJoin->external_id;
				$awardActivity  = AwardsActivity::findOne($awardJoin->award_id);
				$contactWayInfo = [
					'type'        => 2,
					'scene'       => 2,
					'style'       => 1,
					'remark'      => '',
					'skip_verify' => true,
					'state'       => $state,
					'user'        => json_decode($awardActivity->user, 1),
					'party'       => [],
				];
				$workApi        = WorkUtils::getWorkApi($awardActivity->corp_id, WorkUtils::EXTERNAL_API);
				try {
					if (!empty($workApi)) {
						$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
						$wayResult = $workApi->ECAddContactWay($sendData);
						\Yii::error($wayResult, 'changeConfig_1');
						if ($wayResult['errcode'] != 0) {
							throw new InvalidDataException($wayResult['errmsg']);
						}
						$wayInfo              = $workApi->ECGetContactWay($wayResult['config_id']);
						$wayInfo              = SUtils::Object2Array($wayInfo);
						$contactWayInfo       = $wayInfo['contact_way'];
						$awardJoin->config_id = $contactWayInfo['config_id'];
						$awardJoin->qr_code   = $contactWayInfo['qr_code'];
						$awardJoin->save();
					}
				} catch (\Exception $e) {
					$message = $e->getMessage();
					if (strpos($message, '84074') !== false) {
						$message = '没有外部联系人权限';
					}
					throw new InvalidDataException($message);
				}
			}

			return $awardJoin->qr_code;
		}

	}
