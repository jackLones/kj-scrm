<?php

namespace app\models;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\util\DateUtil;
use app\util\SUtils;
use app\util\WxPay\RedPacketPay;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%work_contact_way_redpacket_send}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $way_id 红包活动活码ID
 * @property int $rule_id 红包规则ID
 * @property int $rule_type 初始单个红包金额类型：1、固定金额，2、随机金额
 * @property int $user_id 成员ID
 * @property int $external_userid 外部联系人ID
 * @property string $send_money 发放金额
 * @property int $is_send 是否发送红包（图文）1是0否
 * @property int $status 领取状态0待领取1已领取2已过期3已领完（活动金额）4发放失败
 * @property string $msg 发放失败描述
 * @property int $create_time 创建时间
 * @property int $send_time 领取时间
 * @property int $update_time 更新时间
 *
 * @property WorkExternalContact $externalUser
 * @property WorkUser $user
 * @property WorkCorp $corp
 * @property WorkContactWayRedpacket $way
 */
class WorkContactWayRedpacketSend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket_send}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'way_id', 'rule_id', 'rule_type', 'user_id', 'external_userid', 'is_send', 'status', 'create_time', 'send_time', 'update_time'], 'integer'],
            [['send_money'], 'number'],
            [['msg'], 'string', 'max' => 255],
            [['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayRedpacket::className(), 'targetAttribute' => ['way_id' => 'id']],
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
			'way_id'          => Yii::t('app', '红包活动活码ID'),
			'rule_id'         => Yii::t('app', '红包规则ID'),
			'rule_type'       => Yii::t('app', '初始单个红包金额类型：1、固定金额，2、随机金额'),
			'user_id'         => Yii::t('app', '成员ID'),
			'external_userid' => Yii::t('app', '外部联系人ID'),
			'send_money'      => Yii::t('app', '发放金额'),
			'is_send'         => Yii::t('app', '是否发送红包（图文）1是0否'),
			'status'          => Yii::t('app', '领取状态0待领取1已领取2已过期3已领完（活动金额）4发放失败'),
			'msg'             => Yii::t('app', '发放失败描述'),
			'create_time'     => Yii::t('app', '创建时间'),
			'send_time'       => Yii::t('app', '领取时间'),
			'update_time'     => Yii::t('app', '更新时间'),
		];
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
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWay()
    {
        return $this->hasOne(WorkContactWayRedpacket::className(), ['id' => 'way_id']);
    }

	/**
	 * 添加红包活动发放表
	 */
    public static function setData($way_redpack_id, $user_id, $external_id)
    {
	    $resData = [];

	    $wayRedpacket = WorkContactWayRedpacket::findOne($way_redpack_id);

        $redpacketSend = WorkContactWayRedpacketSend::findOne(['corp_id' => $wayRedpacket->corp_id, 'way_id' => $wayRedpacket->id, 'user_id' => $user_id, 'external_userid' => $external_id, 'is_send' => 1]);
        if ($redpacketSend){
            return $resData;
        }

	    if ($wayRedpacket->redpacket_status == WorkContactWayRedpacket::RED_WAY_ISSUE && ($wayRedpacket->redpacket_amount - $wayRedpacket->out_amount) >= 0.3 && ($wayRedpacket->time_type == 1 || ($wayRedpacket->time_type == 2 && strtotime($wayRedpacket->end_time) > time()))) {
		    if ($wayRedpacket->rule_id > 0) {
			    $redRule = RedPackRule::find()->andWhere(['id' => $wayRedpacket->rule_id])->asArray()->one();
		    } else {
			    $redRule = Json::decode($wayRedpacket->rule_text);
		    }

		    if (($redRule['type'] == 1 && ($wayRedpacket->redpacket_amount - $wayRedpacket->out_amount) >= $redRule['fixed_amount']) || ($redRule['type'] == 2 && ($wayRedpacket->redpacket_amount - $wayRedpacket->out_amount) >= $redRule['min_random_amount'])) {
			    if ($redRule['type'] == 2) {
				    $left       = $wayRedpacket->redpacket_amount - $wayRedpacket->out_amount;
				    $min        = $redRule['min_random_amount'];
				    $max        = $redRule['max_random_amount'] < $left ? $redRule['max_random_amount'] : $left;
				    $send_money = sprintf("%.2f", ($min + lcg_value() * (abs($max - $min))));
				    $send_money = $send_money > $left ? $left : $send_money;
			    } else {
				    $send_money = $redRule['fixed_amount'];
			    }

			    $redpacketSend                  = new WorkContactWayRedpacketSend();
			    $redpacketSend->corp_id         = $wayRedpacket->corp_id;
			    $redpacketSend->way_id          = $wayRedpacket->id;
			    $redpacketSend->rule_id         = $wayRedpacket->rule_id;
			    $redpacketSend->rule_type       = $redRule['type'];
			    $redpacketSend->user_id         = $user_id;
			    $redpacketSend->external_userid = $external_id;
			    $redpacketSend->send_money      = $send_money;
			    $redpacketSend->is_send         = 0;
			    $redpacketSend->status          = 0;
			    $redpacketSend->create_time     = time();

			    if (!$redpacketSend->validate() || !$redpacketSend->save()) {
				    throw new InvalidDataException(SUtils::modelError($redpacketSend));
			    }

			    $resData['send_id'] = $redpacketSend->id;
			    $resData['red_rule'] = $redRule;
		    }
	    }

	    return $resData;
    }

	/**
	 * 验证红包是否可发放
	 */
	public static function verifyRedpacketSend($way_redpack_id, $send_id, $external_id)
	{
		$resData = [];

		$wayRedpacket = WorkContactWayRedpacket::findOne($way_redpack_id);
		if (empty($wayRedpacket)) {
			throw new InvalidParameterException('活动参数不正确！');
		}
		$redpacketSend = WorkContactWayRedpacketSend::findOne($send_id);
		if (empty($redpacketSend)) {
			throw new InvalidParameterException('发放参数不正确！');
		}
		if ($redpacketSend->external_userid != $external_id) {
			throw new InvalidParameterException('客户参数不正确！');
		}
		if ($wayRedpacket->rule_id > 0) {
			$redRule = RedPackRule::find()->andWhere(['id' => $wayRedpacket->rule_id])->asArray()->one();
		} else {
			$redRule = Json::decode($wayRedpacket->rule_text);
		}

		$resData['canOpen']       = 1;
		$resData['isOpen']        = 0;
		$resData['msg']           = '';
		$resData['redRule']       = $redRule;
		$resData['redpacketSend'] = $redpacketSend;

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
		} elseif ($redpacketSend->status == 3) {
			$resData['canOpen'] = 0;
			$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

			return $resData;
		}
		//红包过期
		if (time() - $redpacketSend->create_time > 86400) {
			$redpacketSend->status      = 2;
			$redpacketSend->is_send     = 1;
			$redpacketSend->update_time = time();
			$redpacketSend->save();

			$resData['canOpen'] = 0;
			$resData['msg']     = '红包已超过24小时，无法领取！';

			return $resData;
		}
		//红包配额已用完
		if ($redRule['type'] == 1 && ($wayRedpacket->redpacket_amount - $wayRedpacket->send_amount) < $redpacketSend->send_money) {
			$redpacketSend->status      = 3;
			$redpacketSend->is_send     = 1;
			$redpacketSend->update_time = time();
			$redpacketSend->save();

			$resData['canOpen'] = 0;
			$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

			return $resData;
		}
		if ($redRule['type'] == 2 && ($wayRedpacket->redpacket_amount - $wayRedpacket->send_amount) < $redpacketSend->send_money) {
			if ($redpacketSend->send_money > $redRule['min_random_amount'] && ($wayRedpacket->redpacket_amount - $wayRedpacket->send_amount) < $redRule['min_random_amount']) {
				$redpacketSend->status      = 3;
				$redpacketSend->is_send     = 1;
				$redpacketSend->update_time = time();
				$redpacketSend->save();

				$resData['canOpen'] = 0;
				$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

				return $resData;
			}
		}

		//红包活动已失效红包仍可领

		return $resData;
	}

	/**
	 * 红包发送
	 */
	public static function redpacketSend ($send_id, $remark)
	{
		$redpacketSend = WorkContactWayRedpacketSend::findOne($send_id);
		$corp_id       = $redpacketSend->corp_id;
		$remark        = !empty($remark) ? $remark : '恭喜发财，大吉大利';
		$amount        = $redpacketSend->send_money;
		$time          = time();

		$wayRedpacket = WorkContactWayRedpacket::findOne($redpacketSend->way_id);
		if ($wayRedpacket->rule_id > 0) {
			$redRule = RedPackRule::find()->andWhere(['id' => $wayRedpacket->rule_id])->asArray()->one();
		} else {
			$redRule = Json::decode($wayRedpacket->rule_text);
		}
		if ($redRule['type'] == 2) {
			//随机金额不足情况处理
			$left = $wayRedpacket->redpacket_amount - $wayRedpacket->send_amount;
			if ($left >= $redRule['min_random_amount'] && $left < $amount) {
				$amount = $left;
			}
		}
		//客户信息
		$externalInfo = WorkExternalContact::findOne($redpacketSend->external_userid);
		if (empty($externalInfo) || empty($externalInfo->openid)) {
			throw new InvalidParameterException('客户参数缺失！');
		}

		$order_id                     = '44' . date('YmdHis') . mt_rand(111111, 999999) . mt_rand(11, 99);
		$sendData                     = [];
		$sendData['partner_trade_no'] = $order_id;
		$sendData['openid']           = $externalInfo->openid;
		$sendData['amount']           = $amount * 100;
		$sendData['desc']             = $remark;

		try {
			$redPacketPay = new RedPacketPay();
			$resData      = $redPacketPay->RedPacketSend($corp_id, $sendData);
			\Yii::error($sendData, 'RedPacketSendData');
			\Yii::error($resData, 'RedPacketResData');
			$redpacketSend->is_send     = 1;
			$redpacketSend->update_time = $time;
			if ($resData['return_code'] == 'SUCCESS' && $resData['result_code'] == 'SUCCESS') {
				$redpacketSend->status     = 1;
				$redpacketSend->send_money = $amount;
				$redpacketSend->send_time  = $time;

				if (!$redpacketSend->validate() || !$redpacketSend->save()) {
					throw new InvalidDataException(SUtils::modelError($redpacketSend));
				}

				$wayRedpacket->send_amount += $amount;
				$wayRedpacket->save();

				$user_corp                = UserCorpRelation::findOne(['corp_id' => $corp_id]);
				$redOrder                 = new RedPackOrder();
				$redOrder->uid            = $user_corp->uid;
				$redOrder->type           = 4;
				$redOrder->corp_id        = $corp_id;
				$redOrder->rid            = $wayRedpacket->id;
				$redOrder->jid            = $redpacketSend->id;
				$redOrder->external_id    = $redpacketSend->external_userid;
				$redOrder->openid         = $externalInfo->openid;
				$redOrder->amount         = $amount;
				$redOrder->order_id       = $order_id;
				$redOrder->ispay          = 1;
				$redOrder->pay_time       = $resData['payment_time'];
				$redOrder->transaction_id = $resData['payment_no'];
				$redOrder->remark         = $remark;
				$redOrder->send_time      = time();

				if (!$redOrder->validate() || !$redOrder->save()) {
					throw new InvalidDataException(SUtils::modelError($redOrder));
				}

				//记录客户轨迹
				ExternalTimeLine::addExternalTimeLine(['uid' => $user_corp->uid, 'external_id' => $redpacketSend->external_userid, 'user_id' => $redpacketSend->user_id, 'event' => 'red_way', 'event_id' => $redpacketSend->id, 'related_id' => $redpacketSend->way_id, 'remark' => $redpacketSend->send_money]);

				return true;
			} else {
				$msg = isset($resData['err_code_des']) && !empty($resData['err_code_des']) ? $resData['err_code_des'] : '';
				$msg = empty($msg) && isset($resData['return_msg']) ? $resData['return_msg'] : $msg;

				$redpacketSend->status      = 4;
				$redpacketSend->msg         = $msg;
				$redpacketSend->update_time = $time;
				$redpacketSend->save();

				$msg = $msg == '余额不足' ? '抱歉，领取失败，商户账户余额不足' : $msg;
				$msg = $msg == '该用户今日付款次数超过限制，如有需要请进入微信支付商户平台-产品中心-企业付款到零钱-产品设置进行修改' ? '您今日领取次数已达上限' : $msg;
				$msg = $msg == '证书已过期' ? '很抱歉，活动过于火爆，请刷新页面后尝试' : $msg;

				throw new InvalidDataException($msg);
			}
		} catch (\Exception $e) {
			throw new InvalidDataException($e->getMessage());
		}
	}

	/**
	 * 红包拉新统计
	 */
	public static function getRedpacketSendStatistic ($corp_id, $way_id, $type, $s_date, $e_date, $s_week)
	{
		$xData           = [];//X轴
		$new_member      = [];//拉新人数
		$receive_num     = [];//领取笔数
		$not_receive_num = [];//待领取笔数
		//$expired_num     = [];//已过期笔数
		$receive_sum     = [];//领取金额
		$not_receive_sum = [];//待领取金额
		//$expired_sum     = [];//已过期金额
		$result        = [];
		$newMember     = 0;
		$receiveNum    = 0;
		$notReceiveNum = 0;
		$receiveSum    = 0;
		$notReceiveSum = 0;
		switch ($type) {
			case 1:
				$data = DateUtil::getDateFromRange($s_date, $e_date);
				foreach ($data as $k => $v) {
					$statistic = static::redpacketSendStatisticTime($corp_id, $way_id, $v, $v);

					$result[$k]['new_member']      = $statistic['newMember'];
					$result[$k]['receive_num']     = $statistic['receiveNum'];
					$result[$k]['not_receive_num'] = $statistic['notReceiveNum'];
					//$result[$k]['expired_num']     = $statistic['expiredNum'];
					$result[$k]['receive_sum']     = $statistic['receiveSum'];
					$result[$k]['not_receive_sum'] = $statistic['notReceiveSum'];
					//$result[$k]['expired_sum']     = $statistic['expiredSum'];
					$result[$k]['time']            = $v;
					array_push($new_member, $statistic['newMember']);
					array_push($receive_num, $statistic['receiveNum']);
					array_push($not_receive_num, $statistic['notReceiveNum']);
					//array_push($expired_num, $statistic['expiredNum']);
					array_push($receive_sum, $statistic['receiveSum']);
					array_push($not_receive_sum, $statistic['notReceiveSum']);
					//array_push($expired_sum, $statistic['expiredSum']);

					$newMember     += $statistic['newMember'];
					$receiveNum    += $statistic['receiveNum'];
					$notReceiveNum += $statistic['notReceiveNum'];
					$receiveSum    += $statistic['receiveSum'];
					$notReceiveSum += $statistic['notReceiveSum'];
				}
				$xData = $data;
				break;
			case 2:
				//按周
				$data    = DateUtil::getDateFromRange($s_date, $e_date);
				$data    = DateUtil::getWeekFromRange($data);
				$s_date1 = $data['s_date'];
				$e_date1 = $data['e_date'];
				foreach ($s_date1 as $k => $v) {
					foreach ($e_date1 as $kk => $vv) {
						if ($k == $kk) {
							if ($s_week == 53) {
								$s_week = 1;
							}

							$statistic = static::redpacketSendStatisticTime($corp_id, $way_id, $v, $vv);

							$result[$k]['new_member']      = $statistic['newMember'];
							$result[$k]['receive_num']     = $statistic['receiveNum'];
							$result[$k]['not_receive_num'] = $statistic['notReceiveNum'];
							//$result[$k]['expired_num']     = $statistic['expiredNum'];
							$result[$k]['receive_sum']     = $statistic['receiveSum'];
							$result[$k]['not_receive_sum'] = $statistic['notReceiveSum'];
							//$result[$k]['expired_sum']     = $statistic['expiredSum'];
							$result[$k]['time']            = $v . '~' . $vv . '(' . $s_week . '周)';
							array_push($new_member, $statistic['newMember']);
							array_push($receive_num, $statistic['receiveNum']);
							array_push($not_receive_num, $statistic['notReceiveNum']);
							//array_push($expired_num, $statistic['expiredNum']);
							array_push($receive_sum, $statistic['receiveSum']);
							array_push($not_receive_sum, $statistic['notReceiveSum']);
							//array_push($expired_sum, $statistic['expiredSum']);
							array_push($xData, $result[$k]['time']);

							$newMember     += $statistic['newMember'];
							$receiveNum    += $statistic['receiveNum'];
							$notReceiveNum += $statistic['notReceiveNum'];
							$receiveSum    += $statistic['receiveSum'];
							$notReceiveSum += $statistic['notReceiveSum'];

							$s_week++;
						}
					}
				}
				break;
			case 3:
				//按月
				$date = DateUtil::getLastMonth();
				foreach ($date as $k => $v) {
					$statistic = static::redpacketSendStatisticTime($corp_id, $way_id, $v['firstday'], $v['lastday']);

					$result[$k]['new_member']      = $statistic['newMember'];
					$result[$k]['receive_num']     = $statistic['receiveNum'];
					$result[$k]['not_receive_num'] = $statistic['notReceiveNum'];
					//$result[$k]['expired_num']     = $statistic['expiredNum'];
					$result[$k]['receive_sum']     = $statistic['receiveSum'];
					$result[$k]['not_receive_sum'] = $statistic['notReceiveSum'];
					//$result[$k]['expired_sum']     = $statistic['expiredSum'];
					$result[$k]['time']    = $v['time'];
					array_push($new_member, $statistic['newMember']);
					array_push($receive_num, $statistic['receiveNum']);
					array_push($not_receive_num, $statistic['notReceiveNum']);
					//array_push($expired_num, $statistic['expiredNum']);
					array_push($receive_sum, $statistic['receiveSum']);
					array_push($not_receive_sum, $statistic['notReceiveSum']);
					//array_push($expired_sum, $statistic['expiredSum']);
					array_push($xData, $result[$k]['time']);

					$newMember     += $statistic['newMember'];
					$receiveNum    += $statistic['receiveNum'];
					$notReceiveNum += $statistic['notReceiveNum'];
					$receiveSum    += $statistic['receiveSum'];
					$notReceiveSum += $statistic['notReceiveSum'];
				}

				break;
		}
		$seriesData = [
			[
				'name'   => '拉新人数',
				'type'   => 'line',
				'smooth' => true,
				'data'   => $new_member,
			],
			[
				'name'   => '领取金额',
				'type'   => 'line',
				'smooth' => true,
				'data'   => $receive_sum,
			],
			[
				'name'   => '领取人数',
				'type'   => 'line',
				'smooth' => true,
				'data'   => $receive_num,
			],
			[
				'name'   => '未领取金额',
				'type'   => 'line',
				'smooth' => true,
				'data'   => $not_receive_sum,
			],
			[
				'name'   => '未领取人数',
				'type'   => 'line',
				'smooth' => true,
				'data'   => $not_receive_num,
			],
		];
		$legData    = ['拉新人数', '领取金额', '领取人数', '未领取金额', '未领取人数'];
		$info = [
			'data'          => $result,
			'legData'       => $legData,
			'xData'         => $xData,
			'seriesData'    => $seriesData,
			'newMember'     => $newMember,
			'receiveSum'    => sprintf('%.2f', $receiveSum),
			'receiveNum'    => $receiveNum,
			'notReceiveSum' => sprintf('%.2f', $notReceiveSum),
			'notReceiveNum' => $notReceiveNum,
		];

		return $info;
	}

	//红包拉新单位时间内数据
	private function redpacketSendStatisticTime ($corp_id, $way_id, $stime, $etime)
	{
		$stime  = strtotime($stime);
		$etime  = strtotime($etime . ' 23:59:59');
		$field  = 'sum(send_money) send_sum, count(id) send_num';
		$result = [];

		//拉新人数
		$result['newMember'] = WorkExternalContactFollowUser::find()->where(['way_redpack_id' => $way_id])->andFilterWhere(['between', 'createtime', $stime, $etime])->count();
		//领取
		$receiveData          = WorkContactWayRedpacketSend::find()->andWhere(['corp_id' => $corp_id, 'way_id' => $way_id, 'status' => 1])->andFilterWhere(['between', 'send_time', $stime, $etime]);
		$receiveData          = $receiveData->select($field)->groupBy('way_id')->asArray()->all();
		$result['receiveSum'] = !empty($receiveData[0]['send_sum']) ? $receiveData[0]['send_sum'] : '0.00';
		$result['receiveNum'] = !empty($receiveData[0]['send_num']) ? $receiveData[0]['send_num'] : 0;
		//待领取
		$notReceiveData          = WorkContactWayRedpacketSend::find()->andWhere(['corp_id' => $corp_id, 'way_id' => $way_id, 'is_send' => 1, 'status' => [0, 2, 4]])->andFilterWhere(['between', 'create_time', $stime, $etime]);
		$notReceiveData          = $notReceiveData->select($field)->groupBy('way_id')->asArray()->all();
		$result['notReceiveSum'] = !empty($notReceiveData[0]['send_sum']) ? $notReceiveData[0]['send_sum'] : '0.00';
		$result['notReceiveNum'] = !empty($notReceiveData[0]['send_num']) ? $notReceiveData[0]['send_num'] : 0;
		//已过期
		/*$expiredData          = WorkContactWayRedpacketSend::find()->andWhere(['corp_id' => $corp_id, 'way_id' => $way_id, 'status' => 2])->andFilterWhere(['between', 'update_time', $stime, $etime]);
		$expiredData          = $expiredData->select($field)->groupBy('way_id')->asArray()->all();
		$result['expiredSum'] = !empty($expiredData[0]['send_sum']) ? $expiredData[0]['send_sum'] : '0.00';
		$result['expiredNum'] = !empty($expiredData[0]['send_num']) ? $expiredData[0]['send_num'] : 0;*/

		return $result;
	}
}
