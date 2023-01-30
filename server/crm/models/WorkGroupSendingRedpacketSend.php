<?php

namespace app\models;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\util\SUtils;
use app\util\WorkUtils;
use app\util\WxPay\RedPacketPay;
use Yii;

/**
 * This is the model class for table "{{%work_group_sending_redpacket_send}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $send_id 群发活动ID
 * @property int $group_send_id 群发明细ID(work_tag_group_statistic)
 * @property int $rule_id 红包规则ID
 * @property int $rule_type 初始单个红包金额类型：1、固定金额，2、随机金额
 * @property int $user_id 成员ID
 * @property int $external_userid 外部联系人ID
 * @property int $is_chat 是否群红包1是0否
 * @property string $send_money 发放金额
 * @property string $get_money 群红包领取金额（is_chat=1时）
 * @property int $get_num 群红包领取人数（is_chat=1时）
 * @property int $is_send 是否发送红包（图文）1是0否
 * @property int $status 领取状态0待领取1已领取2已过期3已领完（活动金额）4发放失败
 * @property string $msg 发放失败描述
 * @property int $create_time 创建时间
 * @property int $send_time 领取时间
 * @property int $update_time 更新时间
 *
 * @property WorkCorp $corp
 * @property WorkExternalContact $externalUser
 * @property WorkUser $user
 * @property WorkGroupSending $send
 * @property WorkTagGroupStatistic $groupSend
 */
class WorkGroupSendingRedpacketSend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_group_sending_redpacket_send}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'send_id', 'group_send_id', 'rule_id', 'rule_type', 'user_id', 'external_userid', 'is_send', 'status', 'create_time', 'send_time', 'update_time', 'is_chat', 'get_num'], 'integer'],
            [['send_money', 'get_money'], 'number'],
            [['msg'], 'string', 'max' => 255],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['send_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupSending::className(), 'targetAttribute' => ['send_id' => 'id']],
            [['group_send_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTagGroupStatistic::className(), 'targetAttribute' => ['group_send_id' => 'id']],
        ];
    }

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels ()
	{
		return [
			'id'              => Yii::t('app', 'ID'),
			'corp_id'         => Yii::t('app', '授权的企业ID'),
			'send_id'         => Yii::t('app', '群发活动ID'),
			'group_send_id'   => Yii::t('app', '群发明细ID(work_tag_group_statistic)'),
			'rule_id'         => Yii::t('app', '红包规则ID'),
			'rule_type'       => Yii::t('app', '初始单个红包金额类型：1、固定金额，2、随机金额'),
			'user_id'         => Yii::t('app', '成员ID'),
			'external_userid' => Yii::t('app', '外部联系人ID'),
			'is_chat'         => Yii::t('app', '是否群红包1是0否'),
			'send_money'      => Yii::t('app', '发放金额'),
			'get_money'       => Yii::t('app', '群红包领取金额（is_chat=1时）'),
			'get_num'         => Yii::t('app', '群红包领取人数（is_chat=1时）'),
			'is_send'         => Yii::t('app', '是否发送红包（图文）1是0否'),
			'status'          => Yii::t('app', '领取状态0待领取1已领取2已过期3已领完（活动金额）4发放失败'),
			'msg'             => Yii::t('app', '发放失败描述'),
			'create_time'     => Yii::t('app', '创建时间'),
			'send_time'       => Yii::t('app', '领取时间'),
			'update_time'     => Yii::t('app', '更新时间'),
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
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExternalUser()
    {
        return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSend()
    {
        return $this->hasOne(WorkGroupSending::className(), ['id' => 'send_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupSend()
    {
        return $this->hasOne(WorkTagGroupStatistic::className(), ['id' => 'group_send_id']);
    }

	/**
	 * 添加群发红包活动发放表
	 */
	public static function setData ($sendData, $redRule)
	{
		$redpacketSend = static::findOne(['group_send_id' => $sendData['group_send_id'], 'user_id' => $sendData['user_id'], 'external_userid' => $sendData['external_userid'], 'is_chat' => $sendData['is_chat']]);

		if (empty($redpacketSend)) {
			$redpacketSend                  = new WorkGroupSendingRedpacketSend();
			$redpacketSend->corp_id         = $sendData['corp_id'];
			$redpacketSend->send_id         = $sendData['send_id'];
			$redpacketSend->group_send_id   = $sendData['group_send_id'];
			$redpacketSend->rule_id         = $sendData['rule_id'];
			$redpacketSend->rule_type       = $redRule['type'];
			$redpacketSend->user_id         = $sendData['user_id'];
			$redpacketSend->external_userid = $sendData['external_userid'];
			$redpacketSend->is_chat         = $sendData['is_chat'];

			if ($sendData['is_chat'] == 1){
				$send_money = $sendData['send_money'];
			}else{
				if ($redRule['type'] == 2) {
					$min        = $redRule['min_random_amount'];
					$max        = $redRule['max_random_amount'] < $sendData['send_money'] ? $redRule['max_random_amount'] : $sendData['send_money'];
					$send_money = sprintf("%.2f", ($min + lcg_value() * (abs($max - $min))));
				} else {
					$send_money = $redRule['fixed_amount'];
				}
			}
			$redpacketSend->send_money  = $send_money;
			$redpacketSend->is_send     = 0;
			$redpacketSend->status      = 0;
			$redpacketSend->create_time = 0;
		}

		if (!$redpacketSend->validate() || !$redpacketSend->save()) {
			throw new InvalidDataException(SUtils::modelError($redpacketSend));
		}

		return true;
	}

	/**
	 * 获取群红包发放状态
	 *
	 * @param          $send_id
	 * @param          $user_id
	 *
	 * @return array
	 *
	 * @throws InvalidParameterException
	 */
	public static function refreshChatSendResult ($send_id, $user_id)
	{
		$chatSend = static::findOne(['send_id' => $send_id, 'user_id' => $user_id, 'is_chat' => 1, 'is_send' => 1]);//有已记录的群红包记录
		if (!empty($chatSend)) {
			return true;
		}

		$group  = WorkGroupSending::findOne($send_id);
		$msgIds = $group->success_list;
		if (!empty($msgIds)) {
			$msg = explode(',', $msgIds);
			if (!empty($msg)) {
				foreach ($msg as $val) {
					try {
						$workApi = WorkUtils::getWorkApi($group->corp_id, WorkUtils::EXTERNAL_API);
						$result  = $workApi->ECGetGroupMsgResult($val);
						$status  = 0;
						if ($result['errcode'] == 0) {
							$detail_list = $result['detail_list'];
							if (!empty($detail_list)) {
								$userId = [];
								foreach ($detail_list as $key => $listVal) {
									if (!empty($listVal['userid'])) {
										array_push($userId, $listVal['userid']);
									}
								}
								$userId   = array_unique($userId);
								$statData = [];
								if (!empty($userId)) {
									foreach ($userId as $k => $uData) {
										$statData[$k]['userid'] = $uData;
										$user                   = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $uData]);
										$tag                    = WorkTagGroupStatistic::findOne(['send_id' => $send_id, 'user_id' => $user->id, 'push_type' => 1]);
										$flag                   = 0;
										if (!empty($tag)) {
											$flag = 1;
										}
										$statData[$k]['flag'] = $flag;
									}
								}

								foreach ($detail_list as $list) {
									foreach ($statData as $userVal) {
										if ($list['userid'] == $userVal['userid']) {
											if (empty($userVal['flag'])) {
												$chat     = WorkChat::findOne(['corp_id' => $group->corp_id, 'chat_id' => $list['chat_id']]);
												$workUser = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $list['userid']]);
												if (!empty($chat) && !empty($workUser)) {
													$statistic = WorkTagGroupStatistic::findOne(['send_id' => $send_id, 'chat_id' => $chat->id, 'user_id' => $workUser->id]);
													if (!empty($statistic)) {
														if (isset($list['send_time']) && !empty($list['send_time'])) {
															$statistic->push_time = date('Y-m-d H:i:s', $list['send_time']);
														}
														$statistic->send = $list['status'];
														if (!$statistic->validate() || !$statistic->save()) {
															\Yii::error(SUtils::modelError($statistic), 'GetGroupMsgResultJob-error');
														}
														if ($list['status'] == 1) {
															$stat           = WorkTagGroupUserStatistic::findOne(['send_id' => $send_id, 'user_id' => $workUser->id]);
															$realNum        = intval($stat->real_num);
															$realNum        = $realNum + 1;
															$stat->real_num = $realNum;
															$stat->save();

															if ($group->is_redpacket == 1) {
																$create_time = isset($list['send_time']) && !empty($list['send_time']) ? $list['send_time'] : time();
																WorkGroupSendingRedpacketSend::updateAll(['create_time' => $create_time, 'is_send' => 1], ['send_id' => $send_id, 'user_id' => $workUser->id, 'external_userid' => $chat->id, 'is_chat' => 1, 'create_time' => 0]);
															}
														}

														if ($list['status'] != 0) {
															WorkTagGroupStatistic::updateAll(['push_type' => 1], ['send_id' => $send_id, 'user_id' => $workUser->id]);

															$userSta = WorkTagGroupUserStatistic::findOne(['send_id' => $send_id, 'user_id' => $workUser->id]);
															if (!empty($userSta)) {
																$userSta->status = 1;
																if (isset($list['send_time']) && !empty($list['send_time'])) {
																	$userSta->push_time = $list['send_time'];
																}
																$userSta->save();
															}
															$status = 1;
														}
													}
												}
											}
										}
									}

								}

							}
						}
						if ($status != 0) {
							$group->status = $status;
						}
						$group->queue_id = 0;
						$group->save();
					} catch (\Exception $e) {
						$group->queue_id = 0;
						$group->save();
						\Yii::error($e->getMessage(), 'GetGroupChatMsgResultJob');
					}
				}
			}
		}

		return true;
	}

	/**
	 * 验证群发红包是否可领取
	 *
	 * @param WorkCorp $corpInfo
	 * @param          $send_id
	 * @param          $external_id
	 * @param          $user_id
	 * @param          $toChat
	 * @param          $chatId
	 *
	 * @return array
	 *
	 * @throws InvalidParameterException
	 */
	public static function verifyRedpacketOpen ($corpInfo, $send_id, $external_id, $user_id, $toChat, $chatId = 0)
	{
		$resData            = [];
		$resData['canOpen'] = 1;
		$resData['isOpen']  = 0;
		$resData['msg']     = '';
		$time               = time();

		$groupSend = WorkGroupSending::findOne($send_id);
		if (empty($groupSend)) {
			throw new InvalidParameterException('活动参数不正确！');
		}
		if ($groupSend->rule_id > 0) {
			$redRule = RedPackRule::find()->andWhere(['id' => $groupSend->rule_id])->asArray()->one();
		} else {
			$redRule = json_decode($groupSend->rule_text, true);
		}
		$resData['des'] = $redRule['thanking'] ? $redRule['thanking'] : MoneyOrder::REDPACKET_THANKING;

		if (empty($external_id)){
			$resData['canOpen'] = 0;
			$resData['msg']     = '您当前为个人微信客户，没有添加过企业成员，无法领取红包！';

			return $resData;
		}

		if ($toChat == 0) {
			$redpacketSend = static::findOne(['send_id' => $send_id, 'user_id' => $user_id, 'external_userid' => $external_id, 'is_chat' => 0]);
			if (empty($redpacketSend)) {
				throw new InvalidParameterException('发放参数不正确！');
			}

			//更新发放时间
			if (empty($redpacketSend->create_time)) {
				static::updateAll(['create_time' => $time], ['send_id' => $send_id, 'user_id' => $user_id, 'create_time' => 0]);
				$redpacketSend->create_time = $time;
			}

			//再次进入
			if ($redpacketSend->status == 1) {
				$resData['canOpen'] = 0;
				$resData['isOpen']  = 1;
				$resData['msg']     = '红包已领取！';

				return $resData;
			} elseif ($redpacketSend->status == 2) {
				$resData['canOpen'] = 0;
				$resData['msg']     = '红包已超过24小时，无法领取！';

				return $resData;
			}

			//红包过期
			if ($time - $redpacketSend->create_time > 86400) {
				$redpacketSend->status      = 2;
				$redpacketSend->is_send     = 1;
				$redpacketSend->update_time = $time;
				$redpacketSend->save();

				$resData['canOpen'] = 0;
				$resData['msg']     = '红包已超过24小时，无法领取！';

				return $resData;
			}

			//红包配额已用完
			if ($redRule['type'] == 1 && ($groupSend->redpacket_amount - $groupSend->send_amount) < $redpacketSend->send_money) {
				$redpacketSend->status      = 3;
				$redpacketSend->is_send     = 1;
				$redpacketSend->update_time = $time;
				$redpacketSend->save();

				$resData['canOpen'] = 0;
				$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

				return $resData;
			}
			if ($redRule['type'] == 2 && ($groupSend->redpacket_amount - $groupSend->send_amount) < $redpacketSend->send_money) {
				if ($redpacketSend->send_money > $redRule['min_random_amount'] && ($groupSend->redpacket_amount - $groupSend->send_amount) < $redRule['min_random_amount']) {
					$redpacketSend->status      = 3;
					$redpacketSend->is_send     = 1;
					$redpacketSend->update_time = time();
					$redpacketSend->save();

					$resData['canOpen'] = 0;
					$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

					return $resData;
				}else{
					$redpacketSend->send_money = $groupSend->redpacket_amount - $groupSend->send_amount;
					$redpacketSend->save();
				}
			}
		} elseif ($toChat == 1) {
			$redpacketSend = static::findOne(['send_id' => $send_id, 'user_id' => $user_id, 'external_userid' => $chatId, 'is_chat' => 1]);//群红包记录
			if (empty($redpacketSend)) {
				throw new InvalidParameterException('发放参数不正确！');
			}

			/*//更新发放时间
			if (empty($redpacketSend->create_time)) {
				static::updateAll(['create_time' => $time], ['send_id' => $send_id, 'user_id' => $user_id, 'create_time' => 0]);
				$redpacketSend->create_time = $time;
			}*/

			//再次进入
			$sendExternal = static::findOne(['group_send_id' => $redpacketSend->group_send_id, 'user_id' => $user_id, 'external_userid' => $external_id, 'is_chat' => 0]);//群个人红包记录
			if (!empty($sendExternal) && $sendExternal->status == 1) {
				$resData['canOpen'] = 0;
				$resData['isOpen']  = 1;
				$resData['msg']     = '红包已领取！';

				return $resData;
			}

			//红包过期
			if ($time - $redpacketSend->create_time > 86400) {
				/*$redpacketSend->status = 2;
				$redpacketSend->is_send     = 1;
				$redpacketSend->update_time = $time;
				$redpacketSend->save();*/

				$resData['canOpen'] = 0;
				$resData['msg']     = '红包已超过24小时，无法领取！';

				return $resData;
			}

			//红包配额已用完
			if ($redRule['type'] == 1 && ($redpacketSend->send_money - $redpacketSend->get_money) < $redRule['fixed_amount']) {
				/*$redpacketSend->status      = 3;
				$redpacketSend->is_send     = 1;
				$redpacketSend->update_time = $time;
				$redpacketSend->save();*/

				$resData['canOpen'] = 0;
				$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

				return $resData;
			}
			if ($redRule['type'] == 2 && ($redpacketSend->send_money - $redpacketSend->get_money) < $redRule['min_random_amount']) {
				/*$redpacketSend->status      = 3;
				$redpacketSend->is_send     = 1;
				$redpacketSend->update_time = time();
				$redpacketSend->save();*/

				$resData['canOpen'] = 0;
				$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

				return $resData;
			}

			$chatInfo = WorkChatInfo::findOne(['chat_id' => $chatId, 'type' => 2, 'external_id' => $external_id]);
			if (empty($chatInfo)) {
				throw new InvalidParameterException('只有群客户才能领取红包！');
			}
			/*$followUser = WorkExternalContactFollowUser::findOne(['user_id' => $user_id, 'external_userid' => $external_id, 'del_type' => 0]);
			if (empty($followUser)) {
				$resData['canOpen'] = 0;
				$resData['msg']     = '您当前尚未成为企业成员的客户，无法领取红包！';

				return $resData;
			}*/
		}

		return $resData;
	}

	/**
	 * 群发红包领取
	 *
	 * @param          $send_id
	 * @param          $external_id
	 * @param          $user_id
	 * @param          $toChat
	 * @param          $chatId
	 * @param          $remark
	 *
	 * @return int
	 *
	 * @throws InvalidParameterException
	 */
	public static function redpacketOpen ($send_id, $external_id, $user_id, $toChat, $chatId, $remark)
	{
		$time      = time();
		$groupSend = WorkGroupSending::findOne($send_id);
		//客户信息
		$externalInfo = WorkExternalContact::findOne($external_id);
		if (empty($externalInfo) || empty($externalInfo->openid)) {
			throw new InvalidParameterException('客户参数缺失！');
		}
		if ($toChat == 1) {
			$chatSend = static::findOne(['send_id' => $send_id, 'user_id' => $user_id, 'external_userid' => $chatId, 'is_chat' => 1]);//群红包记录
			if (empty($chatSend)) {
				throw new InvalidParameterException('发放参数不正确！');
			}

			$redpacketSend = static::findOne(['group_send_id' => $chatSend->group_send_id, 'user_id' => $user_id, 'external_userid' => $external_id, 'is_chat' => 0]);//群个人红包记录
			if (!empty($redpacketSend)) {
				if (in_array($redpacketSend->status, [1, 2, 3])){
					throw new InvalidParameterException('已存在群红包领取记录！');
				}
			}else{
				$redpacketSend = new WorkGroupSendingRedpacketSend();
			}

			$corp_id = $chatSend->corp_id;
			//领取金额
			if ($groupSend->rule_id > 0) {
				$redRule = RedPackRule::find()->andWhere(['id' => $groupSend->rule_id])->asArray()->one();
			} else {
				$redRule = json_decode($groupSend->rule_text, true);
			}
			if ($redRule['type'] == 2) {
				$min = $redRule['min_random_amount'];
				$max = $redRule['max_random_amount'];
				if ($chatSend->send_money - $chatSend->get_money < $redRule['max_random_amount']) {
					$max = $chatSend->send_money - $chatSend->get_money;
				}
				$send_money = sprintf("%.2f", ($min + lcg_value() * (abs($max - $min))));
			} else {
				$send_money = $redRule['fixed_amount'];
			}

			$redpacketSend->corp_id         = $corp_id;
			$redpacketSend->send_id         = $send_id;
			$redpacketSend->group_send_id   = $chatSend->group_send_id;
			$redpacketSend->rule_id         = $groupSend->rule_id;
			$redpacketSend->rule_type       = $redRule['type'];
			$redpacketSend->user_id         = $user_id;
			$redpacketSend->external_userid = $external_id;
			$redpacketSend->is_chat         = 0;
			$redpacketSend->send_money      = $send_money;
			$redpacketSend->is_send         = 1;
			$redpacketSend->status          = 0;
			$redpacketSend->create_time     = $chatSend->create_time;

			if (!$redpacketSend->validate() || !$redpacketSend->save()) {
				throw new InvalidParameterException(SUtils::modelError($redpacketSend));
			}
		} else {
			$redpacketSend = static::findOne(['send_id' => $send_id, 'user_id' => $user_id, 'external_userid' => $external_id, 'is_chat' => 0]);
			if (empty($redpacketSend)) {
				throw new InvalidParameterException('发放参数不正确！');
			}
			$corp_id = $redpacketSend->corp_id;
		}

		$sendData                     = [];
		$order_id                     = '44' . date('YmdHis') . $user_id . mt_rand(111111, 999999);
		$sendData['partner_trade_no'] = $order_id;
		$sendData['openid']           = $externalInfo->openid;
		$sendData['amount']           = $redpacketSend->send_money * 100;
		$sendData['desc']             = $remark;

		try {
			$redPacketPay = new RedPacketPay();
			$resData      = $redPacketPay->RedPacketSend($corp_id, $sendData);
			\Yii::error($sendData, 'GroupRedPacketOpenData');
			\Yii::error($resData, 'GroupRedPacketOpenResData');
			if ($resData['return_code'] == 'SUCCESS' && $resData['result_code'] == 'SUCCESS') {
				$redpacketSend->is_send     = 1;
				$redpacketSend->status      = 1;
				$redpacketSend->send_time   = strtotime($resData['payment_time']);
				$redpacketSend->update_time = $time;

				if (!$redpacketSend->validate() || !$redpacketSend->save()) {
					throw new InvalidParameterException(SUtils::modelError($redpacketSend));
				}

				//群领取金额
				if ($toChat == 1) {
					$chatSend->get_money += $redpacketSend->send_money;
					$chatSend->get_num   += 1;
					$chatSend->is_send   = 1;
					$chatSend->save();
				}

				//总领取金额
				$groupSend->send_amount += $redpacketSend->send_money;
				$groupSend->send_num    += 1;
				$groupSend->save();

				$user_corp                = UserCorpRelation::findOne(['corp_id' => $corp_id]);
				$redOrder                 = new RedPackOrder();
				$redOrder->uid            = $user_corp->uid;
				$redOrder->type           = 5;
				$redOrder->corp_id        = $corp_id;
				$redOrder->rid            = $send_id;
				$redOrder->jid            = $redpacketSend->id;
				$redOrder->external_id    = $redpacketSend->external_userid;
				$redOrder->openid         = $externalInfo->openid;
				$redOrder->amount         = $redpacketSend->send_money;
				$redOrder->order_id       = $order_id;
				$redOrder->ispay          = 1;
				$redOrder->pay_time       = $resData['payment_time'];
				$redOrder->transaction_id = $resData['payment_no'];
				$redOrder->remark         = $remark;
				$redOrder->send_time      = $time;

				if (!$redOrder->validate() || !$redOrder->save()) {
					throw new InvalidDataException(SUtils::modelError($redOrder));
				}

				//记录客户轨迹
				if ($toChat == 0){
					ExternalTimeLine::addExternalTimeLine(['uid' => $user_corp->uid, 'external_id' => $redpacketSend->external_userid, 'user_id' => $redpacketSend->user_id, 'event' => 'group_send_money', 'event_id' => $redpacketSend->id, 'related_id' => $redpacketSend->send_id, 'remark' => $redpacketSend->send_money]);
				} else {
					ExternalTimeLine::addExternalTimeLine(['uid' => $user_corp->uid, 'external_id' => $redpacketSend->external_userid, 'user_id' => $redpacketSend->user_id, 'event' => 'group_send_chat_money', 'event_id' => $redpacketSend->group_send_id, 'related_id' => $redpacketSend->send_id, 'remark' => $redpacketSend->send_money]);
				}
				//群客户轨迹
				if ($toChat == 1) {
					$remark = '客户【' . $externalInfo->name_convert . '】通过群发红包-' . $groupSend->title . '，领取【' . $redpacketSend->send_money . '元】';
					$remark .= $redRule['type'] == 1 ? '固定金额红包' : '拼手气红包';
					ExternalTimeLine::addExternalTimeLine(['uid' => $user_corp->uid, 'external_id' => $external_id, 'user_id' => $redpacketSend->user_id, 'event' => 'chat_track_money', 'event_id' => 11, 'related_id' => $chatId, 'remark' => $remark]);
				}

				return true;
			} else {
				$msg = isset($resData['err_code_des']) && !empty($resData['err_code_des']) ? $resData['err_code_des'] : '';
				$msg = empty($msg) && isset($resData['return_msg']) ? $resData['return_msg'] : $msg;

				$redpacketSend->status = 4;
				$redpacketSend->msg    = $msg;
				$redpacketSend->save();

				$msg = $msg == '余额不足' ? '抱歉，领取失败，商户账户余额不足' : $msg;
				$msg = $msg == '该用户今日付款次数超过限制，如有需要请进入微信支付商户平台-产品中心-企业付款到零钱-产品设置进行修改' ? '您今日领取次数已达上限' : $msg;

				throw new InvalidParameterException($msg);
			}
		} catch (\Exception $e) {
			throw new InvalidParameterException($e->getMessage());
		}
	}

}
