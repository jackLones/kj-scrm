<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncRedPackJob;
	use app\queue\SyncWorkAddTagJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%red_pack_join}}".
	 *
	 * @property int                 $id
	 * @property int                 $uid                   账户ID
	 * @property int                 $rid                   裂变任务id
	 * @property int                 $external_id           外部联系人id
	 * @property int                 $openid                外部联系人openid
	 * @property string              $config_id             联系方式的配置id
	 * @property string              $qr_code               联系二维码的URL
	 * @property string              $state                 企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
	 * @property int                 $help_num              有效助力数
	 * @property int                 $invite_amount         裂变人数数量
	 * @property int                 $redpack_price         裂变红包金额
	 * @property string              $first_amount          首拆金额
	 * @property string              $rest_amount           裂变成功剩余金额
	 * @property string              $friend_amount         好友拆红包金额
	 * @property int                 $status                任务状态：0未完成、1进行中、2已完成
	 * @property int                 $first_send_status     首拆发放状态：0未发放、1已发放
	 * @property int                 $first_send_type       首拆发放类型：1零钱发放、2标记发放
	 * @property int                 $send_status           剩余发放状态：0未发放、1已发放
	 * @property int                 $send_type             剩余发放类型：1零钱发放、2标记发放
	 * @property string              $join_time             参与时间
	 * @property string              $complete_time         完成时间
	 * @property string              $complete_second       完成耗时
	 * @property string              $is_remind             是否需要提醒：0否、1是
	 * @property string              $tags                  活动标签
	 *
	 * @property RedPackHelpDetail[] $redPackHelpDetails
	 * @property RedPack             $r
	 * @property User                $u
	 */
	class RedPackJoin extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%red_pack_join}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'rid', 'external_id', 'help_num', 'invite_amount', 'status', 'send_status'], 'integer'],
				[['first_amount', 'friend_amount'], 'number'],
				[['join_time', 'complete_time'], 'safe'],
				[['config_id', 'state'], 'string', 'max' => 64],
				[['qr_code', 'tags'], 'string', 'max' => 255],
				[['rid'], 'exist', 'skipOnError' => true, 'targetClass' => RedPack::className(), 'targetAttribute' => ['rid' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                => Yii::t('app', 'ID'),
				'uid'               => Yii::t('app', '账户ID'),
				'rid'               => Yii::t('app', '裂变任务id'),
				'external_id'       => Yii::t('app', '外部联系人id'),
				'openid'            => Yii::t('app', '外部联系人openid'),
				'config_id'         => Yii::t('app', '联系方式的配置id'),
				'qr_code'           => Yii::t('app', '联系二维码的URL'),
				'state'             => Yii::t('app', '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'help_num'          => Yii::t('app', '有效助力数'),
				'invite_amount'     => Yii::t('app', '裂变人数数量'),
				'redpack_price'     => Yii::t('app', '裂变红包金额'),
				'first_amount'      => Yii::t('app', '首拆金额'),
				'rest_amount'       => Yii::t('app', '裂变成功剩余金额'),
				'friend_amount'     => Yii::t('app', '好友拆红包金额'),
				'status'            => Yii::t('app', '任务状态：0未完成、1进行中、2已完成'),
				'first_send_status' => Yii::t('app', '首拆发放状态：0未发放、1已发放'),
				'first_send_type'   => Yii::t('app', '首拆发放类型：1零钱发放、2标记发放'),
				'send_status'       => Yii::t('app', '剩余发放状态：0未发放、1已发放'),
				'send_type'         => Yii::t('app', '剩余发放类型：1零钱发放、2标记发放'),
				'join_time'         => Yii::t('app', '参与时间'),
				'complete_time'     => Yii::t('app', '完成时间'),
				'complete_second'   => Yii::t('app', '完成耗时'),
				'is_remind'         => Yii::t('app', '是否需要提醒：0否、1是'),
				'tags'              => Yii::t('app', '活动标签'),
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
		public function getRedPackHelpDetails ()
		{
			return $this->hasMany(RedPackHelpDetail::className(), ['jid' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getR ()
		{
			return $this->hasOne(RedPack::className(), ['id' => 'rid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		//获取首拆金额
		public static function getFirstAmount ($redPack)
		{
			$first_amount = '';
			/** @var RedPack $redPack * */
			if ($redPack->first_detach_type == 1) {
				$min_amount   = $redPack->min_random_amount * 100;
				$max_amount   = $redPack->max_random_amount * 100;
				$first_amount = rand($min_amount, $max_amount);
				$first_amount = $first_amount / 100;
			} elseif ($redPack->first_detach_type == 2) {
				$first_amount = $redPack->fixed_amount;
			} elseif ($redPack->first_detach_type == 3) {
				$min_amount   = $redPack->redpack_price * $redPack->min_random_amount_per;
				$max_amount   = $redPack->redpack_price * $redPack->max_random_amount_per;
				$first_amount = rand($min_amount, $max_amount);
				$first_amount = $first_amount / 100;
			}

			return $first_amount;
		}

		//获取好友拆领金额
		public static function getFriendAmount ($redPack)
		{
			$friend_amount = '';
			/** @var RedPack $redPack * */
			if ($redPack->friend_detach_type == 1) {
				$min_amount    = $redPack->min_friend_random_amount * 100;
				$max_amount    = $redPack->max_friend_random_amount * 100;
				$friend_amount = rand($min_amount, $max_amount);
				$friend_amount = $friend_amount / 100;
			} elseif ($redPack->friend_detach_type == 2) {
				$friend_amount = $redPack->fixed_friend_amount;
			}

			return $friend_amount;
		}

		//添加成员后数据处理
		public static function dealData ($external_id, $stateArr)
		{
			$rid     = isset($stateArr[1]) ? intval($stateArr[1]) : 0;
			$redPack = RedPack::findOne($rid);
			if (empty($external_id) || empty($redPack) || $redPack->status != 2) {
				return false;
			}

			//是否有性别的限制
			$is_limit = 0;
			if ($redPack->sex_type != 1) {
				$is_limit = RedPack::checkSex($external_id, $redPack->sex_type);
			}

			if (empty($is_limit)) {
				if (empty($stateArr[2])) {//添加参与者
					static::setJoin($redPack, [RedPack::RED_HEAD, $redPack->id, $external_id], 1, 1);
				} else {//添加助力者
					static::setHelpDetail($redPack, [RedPack::RED_HEAD, $redPack->id, $stateArr[2], $external_id], 1, 1);
				}
			}
		}

		/*
		 * 设置裂变参与
		 * $redPack 裂变任务数据
		 * $stateArr 0：标记，1：任务id，2、外部联系人id
		 * $is_remind 是否需要提醒：0否、1是
		 * $isTag 是否打标签 0打 1不打
		 */
		public static function setJoin ($redPack, $stateArr, $is_remind = 0, $isTag = 0)
		{
			/** @var RedPack $redPack * */
			if ($redPack->status == 1) {
				throw new InvalidDataException('活动还未发布');
			} elseif (in_array($redPack->status, [0, 3, 4, 5])) {
				throw new InvalidDataException('活动已结束');
			}

			$joinCount = RedPackJoin::find()->where(['rid' => $redPack->id])->count();
			if ($joinCount >= $redPack->redpack_num) {
				throw new InvalidDataException('抱歉，当前裂变红包个数已抢完');
			}

			$external_id = intval($stateArr[2]);
			$redPackJoin = static::findOne(['uid' => $redPack->uid, 'rid' => $redPack->id, 'external_id' => $external_id]);
			$state       = implode('_', $stateArr);
			if (empty($redPackJoin)) {
				$contactInfo = WorkExternalContact::findOne($external_id);
				//添加
				$redPackJoin                = new RedPackJoin();
				$redPackJoin->uid           = $redPack->uid;
				$redPackJoin->rid           = $redPack->id;
				$redPackJoin->external_id   = $external_id;
				$redPackJoin->openid        = $contactInfo->openid;
				$redPackJoin->state         = $state;
				$redPackJoin->invite_amount = $redPack->invite_amount;
				$redPackJoin->redpack_price = $redPack->redpack_price;
				$first_amount               = static::getFirstAmount($redPack);
				$redPackJoin->first_amount  = $first_amount;
				$redPackJoin->rest_amount   = bcsub($redPack->redpack_price, $first_amount, 2);
				$redPackJoin->join_time     = DateUtil::getCurrentTime();
				$redPackJoin->is_remind     = $is_remind;

				$transaction = \Yii::$app->mdb->beginTransaction();
				try {
					$configArr              = RedPack::addConfigId($redPack, $state);
					$redPackJoin->config_id = $configArr['config_id'];
					$redPackJoin->qr_code   = $configArr['qr_code'];
					if (!$redPackJoin->validate() || !$redPackJoin->save()) {
						throw new InvalidDataException(SUtils::modelError($redPackJoin));
					}
					$transaction->commit();

					//发首拆红包
					$remark   = '还有' . $redPackJoin->rest_amount . '元正在路上，快召唤' . $redPackJoin->invite_amount . '位好友一起拆红包，TA有，你也有~~';
					$joinData = [
						'uid'               => $redPack->uid,
						'corp_id'           => $redPack->corp_id,
						'rid'               => $redPack->id,
						'jid'               => $redPackJoin->id,
						'external_id'       => $redPackJoin->external_id,
						'openid'            => $redPackJoin->openid,
						'amount'            => $first_amount,
						'remark'            => $remark,
						'send_type'         => 1,
						'first_send_status' => 1,
						'first_send_type'   => 1,
					];
					\Yii::$app->queue->push(new SyncRedPackJob([
						'red_pack_id' => $redPack->id,
						'red_status'  => -1,
						'sendData'    => $joinData,

					]));
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}
				if (!empty($redPack->tag_ids) && empty($redPackJoin->tags)) {
					$redPackJoin->tags = $redPack->tag_ids;
					$redPackJoin->save();
				}
				//打标签
				if (!empty($redPack->tag_ids) && $isTag == 0) {
					$userKeys = !empty($redPack->user_key) ? json_decode($redPack->user_key, true) : [];
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
						$tag_ids = explode(',', $redPack->tag_ids);
						\Yii::$app->queue->push(new SyncWorkAddTagJob([
							'type'      => 2,
							'user_ids'  => $followId,
							'tag_ids'   => $tag_ids,
							'otherData' => ['type' => 'fission', 'msg' => '红包裂变【' . $redPack->title . '】'],
						]));
					}

				}

				$picRule  = json_decode($redPack->pic_rule, 1);
				$assist   = RedPack::RED_HEAD . '_' . $redPack->id . '_' . $external_id;
				$name     = !empty($contactInfo->name) ? rawurldecode($contactInfo->name) : $contactInfo->name_convert;
				$head_url = $contactInfo->avatar;
				if (!empty($head_url) && empty($is_remind)) {
					//获取远程文件所采用的方法
					$ch      = curl_init();
					$timeout = 300;
					curl_setopt($ch, CURLOPT_URL, $head_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					$img = curl_exec($ch);
					curl_close($ch);
					$base64Data = 'data:image/png;base64,' . base64_encode($img);
				} else {//默认头像
					$site_url   = \Yii::$app->params['site_url'];
					$base64Data = $head_url = $site_url . '/static/image/default-avatar.png';
				}

				return ['amount' => $first_amount, 'rest_amount' => $redPackJoin->rest_amount, 'invite_amount' => $redPackJoin->invite_amount, 'assist' => $assist, 'picRule' => $picRule, 'nick_name' => $name, 'base64Data' => $base64Data];
			} else {
				throw new InvalidDataException('您已拆过，无需再拆');
			}
		}

		/*
		 * 设置助力记录
		 * $redPack 裂变任务数据
		 * $stateArr 0：标记，1：任务id，2、参与者外部联系人id，2、助力者外部联系人id
		 * $is_remind 是否需要提醒：0否、1是
		 * $isTag 是否打标签 0打 1不打
		 */
		public static function setHelpDetail ($redPack, $stateArr, $is_remind = 0, $isTag = 0)
		{
			/** @var RedPack $redPack * */
			if ($redPack->status == 1) {
				throw new InvalidDataException('活动还未发布');
			} elseif (in_array($redPack->status, [0, 3, 4, 5])) {
				throw new InvalidDataException('活动已结束');
			}

			$external_id = intval($stateArr[3]);
			try {
				//外部联系人信息
				$contactInfo = WorkExternalContact::findOne($external_id);
				//参与者信息
				$joinInfo = RedPackJoin::findOne(['uid' => $redPack->uid, 'rid' => $redPack->id, 'external_id' => $stateArr[2]]);
				if (empty($joinInfo)) {
					throw new InvalidDataException('无此参与者');
				}
				if ($joinInfo->external_id == $external_id) {
					throw new InvalidDataException('不能为自己帮拆');
				}
				$is_del = 0;
				//助力者信息
				$helpDetail = RedPackHelpDetail::findOne(['jid' => $joinInfo->id, 'external_id' => $external_id]);
				if (empty($helpDetail)) {
					//助力次数限制
					if (!empty($redPack->help_limit)) {
						$helpCount = RedPackHelpDetail::find()->where(['rid' => $redPack->id, 'external_id' => $external_id])->count();
						if ($helpCount >= $redPack->help_limit) {
							throw new InvalidDataException('助力次数已达限制，不能再助力');
						}
					}

					//如果助力者人数够了，就不给助力了
					if ($joinInfo->help_num >= $joinInfo->invite_amount) {
						throw new InvalidDataException('帮拆人数已达到，不能再拆');
					}
					//好友拆领金额
					$amount = static::getFriendAmount($redPack);

					//助力记录
					$helpDetail              = new RedPackHelpDetail();
					$helpDetail->rid         = $redPack->id;
					$helpDetail->jid         = $joinInfo->id;
					$helpDetail->external_id = $external_id;
					$helpDetail->openid      = $contactInfo->openid;
					$helpDetail->amount      = $amount;
					$helpDetail->status      = 1;
					$helpDetail->help_time   = DateUtil::getCurrentTime();
					$helpDetail->is_remind   = $is_remind;
					if (!$helpDetail->validate() || !$helpDetail->save()) {
						throw new InvalidDataException(SUtils::modelError($helpDetail));
					}

					//上级外部联系人信息
					$parentContactInfo = WorkExternalContact::findOne($joinInfo->external_id);

					$name        = !empty($contactInfo->name) ? rawurldecode($contactInfo->name) : $contactInfo->name_convert;
					$parent_name = !empty($parentContactInfo->name) ? rawurldecode($parentContactInfo->name) : $parentContactInfo->name_convert;

					//发助力红包
					$remark   = '恭喜您，你帮“' . $parent_name . '”拆红包，获得' . $helpDetail->amount . '元红包';
					$helpData = [
						'uid'         => $redPack->uid,
						'corp_id'     => $redPack->corp_id,
						'rid'         => $redPack->id,
						'jid'         => $joinInfo->id,
						'hid'         => $helpDetail->id,
						'external_id' => $helpDetail->external_id,
						'openid'      => $helpDetail->openid,
						'amount'      => $helpDetail->amount,
						'remark'      => $remark,
						'send_type'   => 3,
					];
					\Yii::$app->queue->push(new SyncRedPackJob([
						'red_pack_id' => $redPack->id,
						'red_status'  => -1,
						'sendData'    => $helpData,
					]));

					//修改参与者数据
					$joinInfo->help_num      += 1;
					$joinInfo->friend_amount += $amount;
					$oldStatus               = $joinInfo->status;
					$is_complete             = 0;
					if ($joinInfo->help_num >= $joinInfo->invite_amount) {
						if ($oldStatus != 2) {
							$joinInfo->status        = 2;
							$joinInfo->is_remind     = 0;
							$joinInfo->complete_time = DateUtil::getCurrentTime();
							$is_complete             = 1;
							//计算完成耗时
							$join_time                 = strtotime($joinInfo->join_time);
							$time                      = time();
							$joinInfo->complete_second = $time - $join_time;
						}
					}
					if (!$joinInfo->validate() || !$joinInfo->save()) {
						throw new InvalidDataException(SUtils::modelError($joinInfo));
					}

					//修改裂变完成数量
					if (!empty($is_complete)) {
						if ($redPack->complete_num < $redPack->redpack_num) {
							$redPack->complete_num += 1;
							$is_update             = 1;
						}
						//没库存时结束此活动
						if ($redPack->complete_num >= $redPack->redpack_num) {
							$redPack->status = 4;
							$is_update       = 1;
							$is_del          = 1;
						}
						if (!empty($is_update)) {
							$redPack->update();
						}
						if (!empty($is_del)) {
							\Yii::$app->queue->push(new SyncRedPackJob([
								'red_pack_id' => $redPack->id,
								'red_status'  => 4
							]));
						} else {//裂变完成 发放红包
							if ($redPack->send_type == 1) {
								$remark    = '';
								$send_type = $send_amount = 0;
								$joinData  = [
									'uid'         => $redPack->uid,
									'corp_id'     => $redPack->corp_id,
									'rid'         => $redPack->id,
									'jid'         => $joinInfo->id,
									'external_id' => $joinInfo->external_id,
									'openid'      => $joinInfo->openid,
								];
								if ($joinInfo->first_send_status == 0 && ($joinInfo->status == 2 && $joinInfo->send_status == 0)) {
									$remark                        = $joinInfo->invite_amount . '位好友已全部拆完，' . $joinInfo->redpack_price . '元红包拿走，不谢~~~';
									$send_amount                   = $joinInfo->redpack_price;
									$send_type                     = 4;
									$joinData['first_send_status'] = 1;
									$joinData['first_send_type']   = 1;
									$joinData['send_status']       = 1;
									$joinData['send_type']         = 1;
								} elseif ($joinInfo->first_send_status == 0) {
									$remark                        = '还有' . $joinInfo->rest_amount . '元正在路上，快召唤' . $joinInfo->invite_amount . '位好友一起拆红包，TA有，你也有~~';
									$send_amount                   = $joinInfo->first_amount;
									$send_type                     = 1;
									$joinData['first_send_status'] = 1;
									$joinData['first_send_type']   = 1;
								} elseif ($joinInfo->status == 2 && $joinInfo->send_status == 0) {
									$remark                  = $joinInfo->invite_amount . '位好友已全部拆完，剩下的' . $joinInfo->rest_amount . '元红包拿走，不谢~~~';
									$send_amount             = $joinInfo->rest_amount;
									$send_type               = 2;
									$joinData['send_status'] = 1;
									$joinData['send_type']   = 1;
								}
								if (!empty($send_type)) {
									$joinData['amount']    = $send_amount;
									$joinData['remark']    = $remark;
									$joinData['send_type'] = $send_type;

									\Yii::$app->queue->push(new SyncRedPackJob([
										'red_pack_id' => $redPack->id,
										'red_status'  => -1,
										'sendData'    => $joinData,
									]));
								}
							}
						}
						$userKeys = !empty($redPack->user_key) ? json_decode($redPack->user_key, true) : [];
						if (!empty($userKeys)) {
							$tag_ids = empty($redPack->success_tags) ? [] : explode(",",$redPack->success_tags);
							if(!empty($tag_ids)){
								$users      = array_column($userKeys, "id");
								$followId   = [];
								$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $joinInfo->external_id, 'user_id' => $users])->asArray()->all();
								if (!empty($followUser)) {
									$followId = array_column($followUser, "id");
								}
								if (!empty($joinInfo->tags)) {
									$joinInfoTags = explode(",", $joinInfo->tags);
									$tag_ids      = array_diff($tag_ids, $joinInfoTags);
								}
								if (!empty($tag_ids)) {
									$TEMP           = explode(",", $joinInfo->tags);
									$tempTags       = array_merge($tag_ids, $TEMP);
									$joinInfo->tags = implode(",", $tempTags);
								} else {
									$joinInfo->tags = is_array($tag_ids) ?  implode(",", $tag_ids) : NULL;
								}
								$joinInfo->save();
								\Yii::$app->queue->push(new SyncWorkAddTagJob([
									'type'      => 2,
									'user_ids'  => $followId,
									'tag_ids'   => $tag_ids,
									'otherData' => ['type' => 'fission', 'msg' => '红包裂变【' . $redPack->title . '】已完成'],
								]));
							}
						}
					}

					//打标签
					if (!empty($redPack->tag_ids) && $isTag == 0) {
						$userKeys = !empty($redPack->user_key) ? json_decode($redPack->user_key, true) : [];
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
							$tag_ids = explode(',', $redPack->tag_ids);
							\Yii::$app->queue->push(new SyncWorkAddTagJob([
								'type'      => 2,
								'user_ids'  => $followId,
								'tag_ids'   => $tag_ids,
								'otherData' => ['type' => 'fission', 'msg' => '红包裂变【' . $redPack->title . '】'],
							]));
						}

					}

					//判断下参与者数量是否达到红包数，是否要显示我要参与按钮
					$joinCount = RedPackJoin::find()->where(['rid' => $redPack->id])->count();
					if ($joinCount >= $redPack->redpack_num) {
						$assist = '';
					} else {
						$assist = RedPack::RED_HEAD . '_' . $redPack->id . '_0';
					}

					return ['avatar' => $contactInfo->avatar, 'name' => $name, 'parent_name' => $parent_name, 'amount' => $amount, 'assist' => $assist];
				} else {
					throw new InvalidDataException('您已帮拆过，不能再拆');
				}
			} catch (InvalidDataException $e) {
				throw new InvalidDataException($e->getMessage());
			}
		}

		/*
		 * 裂变排行榜
		 * $rid 裂变任务id
		 * @var $joinInfo 参与者
		 * @var $external_id 外部联系人
		 */
		public static function rankList ($rid, $joinInfo = [], $external_id = '')
		{
			/** @var RedPackJoin $joinInfo * */
			$redPackJoin = static::find()->alias('rj');
			$redPackJoin = $redPackJoin->leftJoin('{{%work_external_contact}} wec', '`rj`.`external_id` = `wec`.`id`');
			$redPackJoin = $redPackJoin->where(['rj.rid' => $rid, 'rj.status' => 2]);
			$redPackJoin = $redPackJoin->select('wec.name,wec.avatar,rj.*');
			$count       = $redPackJoin->count();
			$redPackJoin = $redPackJoin->limit(100)->orderBy('rj.complete_second asc')->asArray()->all();
			$joinData    = [];
			foreach ($redPackJoin as $key => $join) {
				$joinData[$key]['key']             = $join['id'];
				$joinData[$key]['id']              = $join['id'];
				$joinData[$key]['name']            = urldecode($join['name']);
				$joinData[$key]['avatar']          = $join['avatar'];
				$joinData[$key]['amount']          = $join['redpack_price'];
				$joinData[$key]['complete_second'] = static::sec2Time($join['complete_second']);
			}
			$info = [];
			if (!empty($joinInfo) && ($joinInfo->external_id == $external_id) && ($joinInfo->status == 2)) {
				$contactInfo = WorkExternalContact::findOne($joinInfo->external_id);
				$joinList    = RedPackJoin::find()->where(['rid' => $rid, 'status' => 2])->andWhere(['<=', 'complete_second', $joinInfo->complete_second])->select('id')->orderBy('complete_second asc')->all();
				$ranking     = 1;
				if (!empty($joinList)) {
					foreach ($joinList as $join) {
						if ($join->id == $joinInfo->id) {
							break;
						} else {
							$ranking += 1;
						}
					}
					$info = ['avatar' => $contactInfo->avatar, 'name' => urldecode($contactInfo->name), 'amount' => $joinInfo->redpack_price, 'complete_second' => static::sec2Time($joinInfo->complete_second), 'ranking' => $ranking];
				}
			}
			$tips = !empty($count) ? '已有' . $count . '人获得裂变红包' : '';

			return [
				'count' => $count,
				'tips'  => $tips,
				'info'  => $info,
				'join'  => $joinData,
			];
		}

		//好友助力
		public static function friendList ($joinInfo)
		{
			/** @var RedPackJoin $joinInfo * */
			if (empty($joinInfo)) {
				return [];
			}
			$helpList = [];
			$helpData = RedPackHelpDetail::find()->alias('rhd');
			$helpData = $helpData->leftJoin('{{%work_external_contact}} wec', '`rhd`.`external_id` = `wec`.`id`');
			$helpData = $helpData->where(['rhd.jid' => $joinInfo->id]);
			$helpData = $helpData->select('wec.name,wec.avatar,rhd.*');
			$count    = $helpData->count();
			$helpData = $helpData->orderBy('rhd.id desc')->asArray()->all();
			foreach ($helpData as $key => $help) {
				$helpList[$key]['key']       = $help['id'];
				$helpList[$key]['id']        = $help['id'];
				$helpList[$key]['name']      = urldecode($help['name']);
				$helpList[$key]['avatar']    = $help['avatar'];
				$helpList[$key]['amount']    = $help['amount'];
				$helpList[$key]['help_time'] = substr($help['help_time'], 0, 16);
			}
			if ($joinInfo->invite_amount <= $count) {
				$tips = '已有' . $count . '位好友帮拆，裂变成功';
			} else {
				$diff = $joinInfo->invite_amount - $count;
				$tips = !empty($count) ? '已有' . $count . '位好友帮拆，还差' . $diff . '位' : '';
			}

			return [
				'count' => $count,
				'tips'  => $tips,
				'info'  => [],
				'join'  => $helpList,
			];
		}

		//将秒数转换为时间
		public static function sec2Time ($time)
		{
			if (is_numeric($time)) {
				$str = '';
				if ($time >= 31556926) {
					$years = floor($time / 31556926);
					$str   .= $years . '年';
					$time  = ($time % 31556926);
				}
				if ($time >= 86400) {
					$days = floor($time / 86400);
					$str  .= $days . '天';
					$time = ($time % 86400);
				}
				if ($time >= 3600) {
					$hours = floor($time / 3600);
					$str   .= $hours . '时';
					$time  = ($time % 3600);
				}
				if ($time >= 60) {
					$minutes = floor($time / 60);
					$str     .= $minutes . '分';
					$time    = ($time % 60);
				}
				$seconds = floor($time);
				if (!empty($seconds)) {
					$str .= $seconds . '秒';
				}

				return $str;
			} else {
				return '';
			}
		}
	}
