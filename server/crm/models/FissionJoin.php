<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncWorkAddTagJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\queue\SyncFissionJob;
	use Yii;

	/**
	 * This is the model class for table "{{%fission_join}}".
	 *
	 * @property int                 $id
	 * @property int                 $uid           用户ID
	 * @property int                 $fid           裂变任务id
	 * @property int                 $parent_id     上级外部联系人id
	 * @property int                 $external_id   外部联系人id
	 * @property string              $config_id     联系方式的配置id
	 * @property string              $qr_code       联系二维码的URL
	 * @property string              $state         企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
	 * @property int                 $help_num      有效助力数
	 * @property int                 $fission_num   裂变人数
	 * @property string              $amount        红包金额
	 * @property int                 $status        任务状态0未完成、1进行中、2已完成
	 * @property int                 $prize_status  任务状态0未处理、1已处理、2无法处理
	 * @property int                 $is_black      是否黑名单0否、1是
	 * @property string              $update_time   更新时间
	 * @property string              $join_time     参与时间
	 * @property string              $complete_time 完成时间
	 * @property string              $black_time    拉入黑名单时间
	 * @property string              $is_remind     是否需要提醒：0否、1是
	 * @property string              $config_status 活码状态：0删除、1可用、2活动结束
	 * @property string              $expire_time   活码过期时间
	 *
	 * @property FissionHelpDetail[] $fissionHelpDetails
	 * @property Fission             $f
	 * @property User                $u
	 */
	class FissionJoin extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fission_join}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'fid', 'help_num', 'fission_num', 'status', 'prize_status', 'is_black'], 'integer'],
				[['update_time', 'join_time', 'complete_time', 'black_time'], 'safe'],
				[['config_id'], 'string', 'max' => 64],
				[['fid'], 'exist', 'skipOnError' => true, 'targetClass' => Fission::className(), 'targetAttribute' => ['fid' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'uid'           => Yii::t('app', '用户ID'),
				'fid'           => Yii::t('app', '裂变任务id'),
				'parent_id'     => Yii::t('app', '上级外部联系人id'),
				'external_id'   => Yii::t('app', '外部联系人id'),
				'config_id'     => Yii::t('app', '联系方式的配置id'),
				'qr_code'       => Yii::t('app', '联系二维码的URL'),
				'state'         => Yii::t('app', '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'help_num'      => Yii::t('app', '有效助力数'),
				'fission_num'   => Yii::t('app', '裂变人数'),
				'amount'        => Yii::t('app', '红包金额'),
				'status'        => Yii::t('app', '任务状态0未完成、1进行中、2已完成'),
				'prize_status'  => Yii::t('app', '奖品状态0未处理、1已处理、2无法处理'),
				'is_black'      => Yii::t('app', '是否黑名单0否、1是'),
				'update_time'   => Yii::t('app', '修改时间'),
				'join_time'     => Yii::t('app', '参与时间'),
				'complete_time' => Yii::t('app', '完成时间'),
				'black_time'    => Yii::t('app', '拉入黑名单时间'),
				'is_remind'     => Yii::t('app', '是否需要提醒：0否、1是'),
				'config_status' => Yii::t('app', '活码状态：0删除、1可用、2活动结束'),
				'expire_time'   => Yii::t('app', '活码过期时间'),
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
		public function getFissionHelpDetails ()
		{
			return $this->hasMany(FissionHelpDetail::className(), ['jid' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getF ()
		{
			return $this->hasOne(Fission::className(), ['id' => 'fid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * 设置裂变参与
		 * @param     $fissionInfo
		 * @param     $stateArr
		 * @param int $parent_id
		 * @param int $isTag 是否打标签 0打 1不打
		 *
		 * @return FissionJoin|null
		 *
		 * @throws InvalidDataException
		 */
		public static function setJoin ($fissionInfo, $stateArr, $parent_id = 0, $isTag = 0)
		{
			/**@var Fission $fissionInfo **/
			$external_id   = intval($stateArr[2]);
			$fissionJoin   = FissionJoin::findOne(['uid' => $fissionInfo->uid, 'fid' => $fissionInfo->id, 'external_id' => $external_id]);
			$state         = implode('_', $stateArr);
			$expire_second = 3600;//过期时间
			if (empty($fissionJoin) && ($fissionInfo->status == 2)) {
				$fissionJoin                = new FissionJoin();
				$fissionJoin->uid           = $fissionInfo->uid;
				$fissionJoin->fid           = $fissionInfo->id;
				$fissionJoin->parent_id     = $parent_id;
				$fissionJoin->external_id   = $external_id;
				$prizeRule                  = json_decode($fissionInfo->prize_rule, 1);
				$fission_num                = $prizeRule[0]['fission_num'];
				$fissionJoin->fission_num   = $fission_num;
				if($fissionInfo->prize_type == 1 && !empty($prizeRule[0]['amount'])){
					$fissionJoin->amount = $prizeRule[0]['amount'];
				}
				$fissionJoin->state         = $state;
				$fissionJoin->join_time     = DateUtil::getCurrentTime();
				$fissionJoin->config_status = 1;
				$fissionJoin->expire_time   = date('Y-m-d H:i:s', time() + $expire_second);

				if (!$fissionJoin->validate() || !$fissionJoin->save()) {
					throw new InvalidDataException(SUtils::modelError($fissionJoin));
				}

				try {
					$configArr              = Fission::addConfigId($fissionInfo, $state);
					$fissionJoin->config_id = $configArr['config_id'];
					$fissionJoin->qr_code   = $configArr['qr_code'];
					if (!$fissionJoin->validate() || !$fissionJoin->save()) {
						throw new InvalidDataException(SUtils::modelError($fissionJoin));
					}
				} catch (InvalidDataException $e) {
					throw new InvalidDataException($e->getMessage());
				}
				//打标签
				if (!empty($fissionInfo->tag_ids) && $isTag == 0) {
					$userKeys = !empty($fissionInfo->user_key) ? json_decode($fissionInfo->user_key, true) : [];
					if (!empty($userKeys)) {
						$users = [];
						foreach ($userKeys as $val) {
							array_push($users, $val['id']);
						}
						$followId   = [];
						$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $external_id, 'user_id' => $users])->asArray()->all();
						if (!empty($followUser)) {
							foreach ($followUser as $user) {
								array_push($followId, $user['id']);
							}
						}
						$tag_ids = explode(',', $fissionInfo->tag_ids);
						\Yii::$app->queue->push(new SyncWorkAddTagJob([
							'type'      => 2,
							'user_ids'  => $followId,
							'tag_ids'   => $tag_ids,
							'otherData' => ['type' => 'fission', 'msg' => '裂变引流【' . $fissionInfo->title . '】'],
						]));
					}

				}
			} elseif (!empty($fissionJoin) && ($fissionJoin->config_status == 0 || empty($fissionJoin->qr_code)) && ($fissionInfo->status == 2)) {
				try {
					$configArr                  = Fission::addConfigId($fissionInfo, $fissionJoin->state);
					$fissionJoin->config_id     = $configArr['config_id'];
					$fissionJoin->qr_code       = $configArr['qr_code'];
					$fissionJoin->config_status = 1;
					$fissionJoin->expire_time   = date('Y-m-d H:i:s', time() + $expire_second);
					if (!$fissionJoin->validate() || !$fissionJoin->save()) {
						throw new InvalidDataException(SUtils::modelError($fissionJoin));
					}
				} catch (InvalidDataException $e) {
					throw new InvalidDataException($e->getMessage());
				}
			}

			return $fissionJoin;
		}

		//设置裂变参与
		public static function setJoinData ($externalUserId, $stateArr = [])
		{
			try {
				$fission_id  = isset($stateArr[1]) ? intval($stateArr[1]) : 0;
				$external_id = $externalUserId;
				$fissionInfo = Fission::findOne($fission_id);
				if (empty($fissionInfo) || $fissionInfo->status != 2) {
					return false;
				}
				//是否有性别的限制
				$is_limit = 0;
				if ($fissionInfo->sex_type != 1) {
					$is_limit = RedPack::checkSex($external_id, $fissionInfo->sex_type);
				}
				if (empty($is_limit)) {
					//设置参与者
					static::setJoin($fissionInfo, [Fission::FISSION_HEAD, $fissionInfo->id, $external_id], $stateArr[2],1);
					//助力记录
					if (!empty($stateArr[2])) {
						$stateArr = [Fission::FISSION_HEAD, $fissionInfo->id, $stateArr[2], $external_id];
						static::setHelpDetail($fissionInfo, $stateArr, 1,1);
					}
				}
			} catch (InvalidDataException $e) {
				\Yii::error($e->getMessage(), 'setJoinData');
			}
		}

		/**
		 * 助力修改记录
		 * @param     $fissionInfo
		 * @param     $stateArr
		 * @param int $is_remind
		 * @param int $isTag 是否打标签 0打 1不打
		 *
		 * @return int
		 * @throws InvalidDataException
		 * @throws \Throwable
		 * @throws \yii\db\StaleObjectException
		 */
		public static function setHelpDetail ($fissionInfo, $stateArr, $is_remind = 0, $isTag = 0)
		{
			/**@var Fission $fissionInfo**/
			$external_id = intval($stateArr[3]);
			//裂变要求
//			if ($fissionInfo->is_friend == 0) {//新好友助力
//				$count = WorkExternalContactFollowUser::find()->where(['external_userid' => $external_id])->count();
//				if ($count > 1) {
//					return 0;
//				}
//			} else {//全部好友
//				//参与记录
//				$joinInfo = static::findOne(['fid' => $fissionInfo->id, 'external_id' => $external_id]);
//				if (!empty($joinInfo)) {
//					return 0;
//				}
//				//助力记录
//				$detailInfo = FissionHelpDetail::findOne(['fid' => $fissionInfo->id, 'external_id' => $external_id]);
//				if (!empty($detailInfo)) {
//					return 0;
//				}
//			}
			//防刷检测
//				if (empty($joinInfo->is_black) && !empty($fissionInfo->is_brush)) {
//					$brushRule = json_decode($fissionInfo->brush_rule, 1);
//					$brushTime = $brushRule['brush_time'];
//					$brushNum  = $brushRule['brush_num'];
//					$helpTime  = date('Y-m-d H:i:s', time() - $brushTime);
//					$count     = FissionHelpDetail::find()->where(['jid' => $joinInfo->id, 'status' => 1])->andWhere(['>', 'help_time', $helpTime])->count();
//					if ($count > $brushNum) {
//						$joinInfo->is_black   = 1;
//						$joinInfo->black_time = DateUtil::getCurrentTime();
//						if (!$joinInfo->validate() || !$joinInfo->save()) {
//							throw new InvalidDataException(SUtils::modelError($joinInfo));
//						}
//					}
//				}

			try {
				$joinInfo = static::findOne(['uid' => $fissionInfo->uid, 'fid' => $fissionInfo->id, 'external_id' => $stateArr[2]]);

				$is_del = 0;
				//添加助力记录
				$helpDetail = FissionHelpDetail::findOne(['jid' => $joinInfo->id, 'external_id' => $external_id]);
				if (empty($helpDetail)) {
					//助力次数限制
					if (!empty($fissionInfo->help_limit)) {
						$helpCount = FissionHelpDetail::find()->where(['fid' => $fissionInfo->id, 'external_id' => $external_id])->count();
						if ($helpCount >= $fissionInfo->help_limit) {
							return 3;
						}
					}

					//如果助力者人数够了，就不给助力了
					if ($joinInfo->help_num >= $joinInfo->fission_num) {
						return 2;
					}

					$joinInfo->help_num += 1;
					$oldStatus          = $joinInfo->status;
					$is_complete        = 0;
					if ($joinInfo->help_num >= $joinInfo->fission_num) {
						if ($joinInfo->is_black == 0 && $oldStatus != 2) {
							$joinInfo->status        = 2;
							$joinInfo->is_remind     = 1;
							$joinInfo->complete_time = DateUtil::getCurrentTime();
							$is_complete             = 1;
						}
					} else {
						$joinInfo->status    = 1;
						$joinInfo->is_remind = 0;
					}
					if (!$joinInfo->validate() || !$joinInfo->save()) {
						throw new InvalidDataException(SUtils::modelError($joinInfo));
					}
					$helpDetail              = new FissionHelpDetail();
					$helpDetail->fid         = $fissionInfo->id;
					$helpDetail->jid         = $joinInfo->id;
					$helpDetail->external_id = $external_id;
					$helpDetail->help_time   = DateUtil::getCurrentTime();
					$helpDetail->is_remind   = $is_remind;
					if (!$helpDetail->validate() || !$helpDetail->save()) {
						throw new InvalidDataException(SUtils::modelError($helpDetail));
					}

					if ($joinInfo->is_black == 0 && !empty($is_complete)) {
						$prizeRule = json_decode($fissionInfo->prize_rule, 1);
						$prize_num = $prizeRule[0]['prize_num'];
						$is_update = 0;
						if ($fissionInfo->complete_num < $prize_num) {
							$fissionInfo->complete_num += 1;
							$is_update                 = 1;
						}
						//没库存时结束此活动
						if (($fissionInfo->complete_num >= $prize_num) && !empty($fissionInfo->is_end) && $fissionInfo->status == 2) {
							$fissionInfo->status = 4;
							$is_update           = 1;
							$is_del              = 1;
						}
						if (!empty($is_update)) {
							$fissionInfo->update();
						}
						if (!empty($is_del)) {
							\Yii::$app->queue->push(new SyncFissionJob([
								'fission_id'     => $fissionInfo->id,
								'fission_status' => 4
							]));
						} else {//裂变完成 发放红包
							if($fissionInfo->prize_type == 1 && $fissionInfo->prize_send_type == 1){
								$remark      = '裂变成功，' . $joinInfo->amount . '元红包拿走，不谢~~~';
								$contactInfo = WorkExternalContact::findOne($joinInfo->external_id);
								$joinData    = [
									'uid'         => $fissionInfo->uid,
									'corp_id'     => $fissionInfo->corp_id,
									'rid'         => $fissionInfo->id,
									'jid'         => $joinInfo->id,
									'external_id' => $joinInfo->external_id,
									'openid'      => $contactInfo->openid,
									'amount'      => $joinInfo->amount,
									'remark'      => $remark,
								];
								\Yii::$app->queue->push(new SyncFissionJob([
									'fission_id'     => $fissionInfo->id,
									'fission_status' => -1,
									'sendData'       => $joinData,
								]));
							}
						}
					}

					//打标签
					if (!empty($fissionInfo->tag_ids) && $isTag == 0) {
						$userKeys = !empty($fissionInfo->user_key) ? json_decode($fissionInfo->user_key, true) : [];
						if (!empty($userKeys)) {
							$users = [];
							foreach ($userKeys as $val) {
								array_push($users, $val['id']);
							}
							$followId   = [];
							$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $external_id, 'user_id' => $users])->asArray()->all();
							if (!empty($followUser)) {
								foreach ($followUser as $user) {
									array_push($followId, $user['id']);
								}
							}
							$tag_ids = explode(',', $fissionInfo->tag_ids);
							\Yii::$app->queue->delay(1)->push(new SyncWorkAddTagJob([
								'type'      => 2,
								'user_ids'  => $followId,
								'tag_ids'   => $tag_ids,
								'otherData' => ['type' => 'fission', 'msg' => '裂变引流【' . $fissionInfo->title . '】'],
							]));
						}

					}

					return 1;
				} elseif (!empty($helpDetail->is_remind) && empty($is_remind)) {
					$helpDetail->is_remind = 0;
					if (!$helpDetail->validate() || !$helpDetail->save()) {
						throw new InvalidDataException(SUtils::modelError($helpDetail));
					}

					return 1;
				}
			} catch (InvalidDataException $e) {
				throw new InvalidDataException($e->getMessage());
			}

			return 0;
		}

	}
