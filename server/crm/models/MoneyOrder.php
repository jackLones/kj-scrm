<?php

	namespace app\models;

	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WxPay\RedPacketPay;
	use Yii;

	/**
	 * This is the model class for table "{{%money_order}}".
	 *
	 * @property int    $id
	 * @property string $order_id       支付订单号
	 * @property int    $uid            商户id
	 * @property int    $corp_id        企业微信id
	 * @property int    $user_id        成员ID
	 * @property int    $external_id    外部联系人ID
	 * @property string $goods_type     商品类型 sendMoney企业付款到零钱 redPacket发红包
	 * @property int    $goods_id       订单关联
	 * @property string $money          金额
	 * @property int    $send_time      发送时间
	 * @property int    $ispay          是否支付1是0否
	 * @property int    $pay_time       支付时间
	 * @property int    $status         领取状态0待领取1已领取2已过期3已领完4发放失败
	 * @property int    $chat_send_id   群红包发放表id
	 * @property string $openid         外部联系人openid（非企微客户存储）
	 * @property string $remark         红包备注
	 * @property string $message        留言
	 * @property string $transaction_id 支付订单号
	 * @property string $third_id       第三方订单号
	 * @property string $shop           第三方店铺
	 * @property string $account        购物账号
	 * @property string $extrainfo      额外信息
	 */
	class MoneyOrder extends \yii\db\ActiveRecord
	{
		const DAY_SUM_MONEY = '100000.00';//默认单日红包总额度
		const DAY_EXTERNAL_NUM = 10;//客户单日红包次数
		const DAY_EXTERNAL_MONEY = '5000.00';//客户单日红包额度
		const DAY_USER_NUM = '999999';//员工单日红包次数
		const DAY_USER_MONEY = '100.00';//员工单日红包额度

		const REDPACKET_SEND = "RedpacketSend";//发放单人红包
		const REDPACKET_CHAT_SEND = "RedpacketChatSend";//发放群红包
		const H5_URL = '/h5/pages/redpacketSend/index';
		const REDPACKET_THANKING = '恭喜发财，大吉大利';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%money_order}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'user_id', 'external_id', 'send_time', 'goods_id', 'ispay', 'pay_time', 'status', 'chat_send_id'], 'integer'],
				[['external_id', 'goods_type'], 'required'],
				[['money'], 'number'],
				[['order_id', 'goods_type'], 'string', 'max' => 50],
				[['openid'], 'string', 'max' => 64],
				[['remark', 'message', 'shop', 'account', 'extrainfo'], 'string', 'max' => 255],
				[['transaction_id', 'third_id'], 'string', 'max' => 100],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'order_id'       => Yii::t('app', '支付订单号'),
				'uid'            => Yii::t('app', '商户id'),
				'corp_id'        => Yii::t('app', '企业微信id'),
				'user_id'        => Yii::t('app', '成员ID'),
				'external_id'    => Yii::t('app', '外部联系人ID'),
				'goods_type'     => Yii::t('app', '商品类型 sendMoney企业付款到零钱 redPacket发红包'),
				'goods_id'       => Yii::t('app', '订单关联'),
				'money'          => Yii::t('app', '金额'),
				'send_time'      => Yii::t('app', '发送时间'),
				'ispay'          => Yii::t('app', '是否支付1是0否'),
				'pay_time'       => Yii::t('app', '支付时间'),
				'status'         => Yii::t('app', '领取状态0待领取1已领取2已过期3已领完4发放失败'),
				'chat_send_id'   => Yii::t('app', '群红包发放表id'),
				'openid'         => Yii::t('app', '外部联系人openid（非企微客户存储）'),
				'remark'         => Yii::t('app', '红包备注'),
				'message'        => Yii::t('app', '留言'),
				'transaction_id' => Yii::t('app', '支付订单号'),
				'third_id'       => Yii::t('app', '第三方订单号'),
				'shop'           => Yii::t('app', '第三方店铺'),
				'account'        => Yii::t('app', '购物账号'),
				'extrainfo'      => Yii::t('app', '额外信息'),
			];
		}

		/**
		 * 当日发送红包数据查询
		 *
		 * @param WorkCorp            $corpInfo
		 * @param WorkUser            $userInfo
		 * @param WorkExternalContact $externalUserData
		 * @param int                 $money
		 *
		 * @return array
		 *
		 */
		public static function sendMoneyInfo ($corpInfo, $userInfo, $externalUserData, $money = 0)
		{
			$result             = [];
			$can_send           = 1;
			$send_msg           = '';
			$day_sum_money      = $corpInfo->day_sum_money > 0 ? $corpInfo->day_sum_money : MoneyOrder::DAY_SUM_MONEY;//单日红包总额度
			$day_user_num       = $userInfo->day_user_num > 0 ? $userInfo->day_user_num : MoneyOrder::DAY_USER_NUM;//员工单日红包次数
			$day_user_money     = $userInfo->day_user_money > 0 ? $userInfo->day_user_money : MoneyOrder::DAY_USER_MONEY;//员工单日红包额度
			$day_external_num   = $corpInfo->day_external_num > 0 ? $corpInfo->day_external_num : MoneyOrder::DAY_EXTERNAL_NUM;//客户单日红包次数
			$day_external_money = $corpInfo->day_external_money > 0 ? $corpInfo->day_external_money : MoneyOrder::DAY_EXTERNAL_MONEY;//客户单日红包额度

			$moneyData  = MoneyOrder::find()->andWhere(['corp_id' => $corpInfo->id, 'goods_type' => 'redPacket', 'ispay' => 1]);
			$moneyData  = $moneyData->andFilterWhere(['between', 'send_time', strtotime(date('Y-m-d')), time()]);
			$moneyDataE = clone $moneyData;
			//企业微信当日已发红包
			$allMoneyData = $moneyData->select('SUM(money) smoney')->asArray()->all();
			$allMoney     = !empty($allMoneyData[0]['smoney']) ? $allMoneyData[0]['smoney'] : 0;
			//员工已发红包
			$moneyData     = MoneyOrder::find()->andWhere(['corp_id' => $corpInfo->id, 'goods_type' => 'redPacket', 'chat_send_id' => 0]);
			$moneyData     = $moneyData->andFilterWhere(['between', 'send_time', strtotime(date('Y-m-d')), time()]);
			$userMoneyData = $moneyData->andWhere(['user_id' => $userInfo->id])->select('SUM(money) smoney, COUNT(id) snum')->asArray()->all();
			$user_money1   = !empty($userMoneyData[0]['smoney']) ? $userMoneyData[0]['smoney'] : 0;
			$user_num1     = !empty($userMoneyData[0]['snum']) ? $userMoneyData[0]['snum'] : 0;

			$moneyData       = RedPackChatSendRule::find()->andWhere(['corp_id' => $corpInfo->id, 'user_id' => $userInfo->id]);
			$moneyData       = $moneyData->andFilterWhere(['between', 'create_time', date('Y-m-d') . ' 00:00:00', date('Y-m-d H:i:s')]);
			$selectUserMoney = $moneyData->select('SUM(redpacket_amount) smoney, COUNT(id) snum')->groupBy('user_id')->asArray()->all();
			$user_money2     = !empty($selectUserMoney[0]['smoney']) ? $selectUserMoney[0]['smoney'] : 0;
			$user_num2       = !empty($selectUserMoney[0]['snum']) ? $selectUserMoney[0]['snum'] : 0;

			$user_money = $user_money1 + $user_money2;
			$user_num   = $user_num1 + $user_num2;

			//客户已收红包
			if (!empty($externalUserData)) {
				$externalMoneyData = $moneyDataE->andWhere(['external_id' => $externalUserData->id])->select('SUM(money) smoney, COUNT(id) snum')->asArray()->all();
				$external_money    = !empty($externalMoneyData[0]['smoney']) ? $externalMoneyData[0]['smoney'] : 0;
				$external_num      = !empty($externalMoneyData[0]['snum']) ? $externalMoneyData[0]['snum'] : 0;

				$external_hmoney = $day_external_money > $external_money ? sprintf('%.2f', $day_external_money - $external_money) : '0.00';
				$external_hnum   = $day_external_num > $external_num ? ($day_external_num - $external_num) : 0;
			}

			if ($allMoney >= $day_sum_money) {
				$can_send = 0;
				$send_msg = '今日，商户号发放零钱金额已达上限，无法发放。';
			}
			if ($user_money >= $day_user_money) {
				$can_send = 0;
				$send_msg = '今日，您付款给客户的总金额' . $day_user_money . '元，已全部用完，无法再进行发放。';
			}
			if ($user_num >= $day_user_num) {
				$can_send = 0;
				$send_msg = '今日，您付款给客户的总次数' . $day_user_num . '次，已全部用完，无法再进行发放。';
			}
			if (!empty($externalUserData)){
				if ($external_money >= $day_external_money) {
					$can_send = 0;
					$send_msg = '今日，该客户收款额度已达上限，无法再对其进行发放。';
				}
				if ($external_num >= $day_external_num) {
					$can_send = 0;
					$send_msg = '今日，该客户收款次数已达上限，无法再对其进行发放。';
				}
			}

			if ($money > 0) {
				if ($allMoney + $money > $day_sum_money) {
					$can_send = 0;
					$send_msg = '超过今日商户号发放零钱金额上限，无法发放。';
				}
				if ($user_money + $money > $day_user_money) {
					$can_send = 0;
					$send_msg = '超过今日您付款给客户的总金额' . $day_user_money . '元，无法进行发放。';
				}
				if (!empty($externalUserData)){
					if ($external_money + $money > $day_external_money) {
						$can_send = 0;
						$send_msg = '超过今日该客户收款额度上限，无法对其进行发放。';
					}
				}
			}

			if (!empty($externalUserData)) {
				$externalFollowUser = WorkExternalContactFollowUser::findOne(['external_userid' => $externalUserData->id, 'user_id' => $userInfo->id]);
				if (empty($externalFollowUser) && !empty($externalUserData->openid)) {
					$newExternalUser = WorkExternalContact::find()->where(['and', ['openid' => $externalUserData->openid], ['not', ['id' => $externalUserData->id]]])->one();
					if (!empty($newExternalUser)) {
						$externalFollowUser = WorkExternalContactFollowUser::findOne(['external_userid' => $newExternalUser->id, 'user_id' => $userInfo->id]);
					}
				}

				if (!empty($externalFollowUser)) {
					if ($externalFollowUser->del_type == WorkExternalContactFollowUser::WORK_DEL_EX) {
						$can_send = 0;
						$send_msg = '无法发放，您已将该客户删除。';
					} elseif ($externalFollowUser->del_type == WorkExternalContactFollowUser::EX_DEL_WORK) {
						$can_send = 0;
						$send_msg = '无法发放，客户已将您删除。';
					}
				} else {
					$can_send = 0;
					$send_msg = '无法发放，已不存在客户关系。';
				}
			}

			if ($userInfo->can_send_money == 0){
				$can_send = 0;
				$send_msg = '发红包权限已关闭。';
			}

			$user_hmoney = $day_user_money > $user_money ? sprintf('%.2f', $day_user_money - $user_money) : '0.00';
			$user_hnum   = $day_user_num > $user_num ? ($day_user_num - $user_num) : 0;

			$result['can_send']        = $can_send;
			$result['send_msg']        = $send_msg;
			$result['user_hmoney']     = $user_hmoney;
			$result['user_hnum']       = $user_hnum;
			$result['external_hmoney'] = isset($external_hmoney) ? $external_hmoney : '0.00';
			$result['external_hnum']   = isset($external_hnum) ? $external_hnum : 0;

			return $result;
		}

		/**
		 * 验证红包是否可领取
		 *
		 * @param WorkCorp $corpInfo
		 * @param          $type
		 * @param          $send_id
		 * @param          $external_id
		 * @param          $openid
		 *
		 * @return array
		 *
		 * @throws InvalidParameterException
		 */
		public static function verifyRedpacketOpen ($corpInfo, $type, $send_id, $external_id, $openid = '')
		{
			$resData            = [];
			$resData['canOpen'] = 1;
			$resData['isOpen']  = 0;
			$resData['msg']     = '';

			$day_external_num   = $corpInfo->day_external_num > 0 ? $corpInfo->day_external_num : MoneyOrder::DAY_EXTERNAL_NUM;//客户单日红包次数
			$day_external_money = $corpInfo->day_external_money > 0 ? $corpInfo->day_external_money : MoneyOrder::DAY_EXTERNAL_MONEY;//客户单日红包额度

			$moneyData = MoneyOrder::find()->andWhere(['corp_id' => $corpInfo->id, 'ispay' => 1])->andWhere(['goods_type' => ['sendMoney', 'redPacket']]);
			$moneyData = $moneyData->andFilterWhere(['between', 'pay_time', strtotime(date('Y-m-d')), time()]);
			if ($external_id) {
				$moneyData = $moneyData->andWhere(['external_id' => $external_id]);
			} else {
				$moneyData = $moneyData->andWhere(['openid' => $openid]);
			}
			$externalMoneyData = $moneyData->select('SUM(money) smoney, COUNT(id) snum')->asArray()->all();
			$external_money    = !empty($externalMoneyData[0]['smoney']) ? $externalMoneyData[0]['smoney'] : 0;
			$external_num      = !empty($externalMoneyData[0]['snum']) ? $externalMoneyData[0]['snum'] : 0;

			if ($type == MoneyOrder::REDPACKET_SEND) {
				$moneyOrder         = static::findOne($send_id);
				$resData['user_id'] = $moneyOrder->user_id;
				$resData['des']     = $moneyOrder->message ? $moneyOrder->message : static::REDPACKET_THANKING;

				if (empty($moneyOrder)) {
					throw new InvalidParameterException('发放参数不正确！');
				}
				if ($moneyOrder->external_id != $external_id) {
					throw new InvalidParameterException('客户参数不正确！');
				}
				//再次进入
				if ($moneyOrder->status == 1) {
					$resData['canOpen'] = 0;
					$resData['isOpen']  = 1;
					$resData['msg']     = '红包已领取！';

					return $resData;
				} elseif ($moneyOrder->status == 2) {
					$resData['canOpen'] = 0;
					$resData['msg']     = '红包已超过24小时，无法领取！';

					return $resData;
				}
				//红包过期
				if (time() - $moneyOrder->send_time > 86400) {
					$moneyOrder->status = 2;
					$moneyOrder->save();

					$resData['canOpen'] = 0;
					$resData['msg']     = '红包已超过24小时，无法领取！';

					return $resData;
				}
				if ($external_money + $moneyOrder->money > $day_external_money) {
					$resData['canOpen'] = 0;
					$resData['msg']     = '今日您的收款额度已不足，无法领取！';

					return $resData;
				}
			} elseif ($type == MoneyOrder::REDPACKET_CHAT_SEND) {
				$chatSendRule       = RedPackChatSendRule::findOne($send_id);
				$resData['user_id'] = $chatSendRule->user_id;
				$resData['des']     = $chatSendRule->des ? $chatSendRule->des : static::REDPACKET_THANKING;

				if (empty($chatSendRule)) {
					throw new InvalidParameterException('发放参数不正确！');
				}

				//再次进入
				if ($external_id){
					$moneyOrder = static::findOne(['user_id' => $chatSendRule->user_id, 'external_id' => $external_id, 'chat_send_id' => $chatSendRule->id]);
				}else{
					$moneyOrder = static::findOne(['user_id' => $chatSendRule->user_id, 'openid' => $openid, 'chat_send_id' => $chatSendRule->id]);
				}
				if (!empty($moneyOrder) && $moneyOrder->status == 1){
					$resData['canOpen'] = 0;
					$resData['isOpen']  = 1;
					$resData['msg']     = '红包已领取！';

					return $resData;
				}

				if ($chatSendRule->get_num >= $chatSendRule->redpacket_num) {
					$resData['canOpen'] = 0;
					$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

					return $resData;
				}
				if (time() - strtotime($chatSendRule->create_time) > 86400) {
					$resData['canOpen'] = 0;
					$resData['msg']     = '红包已超过24小时，无法领取！';

					return $resData;
				}
				$amount_allot = !empty($chatSendRule->amount_allot) ? json_decode($chatSendRule->amount_allot, true) : [];
				if (empty($amount_allot)) {
					$resData['canOpen'] = 0;
					$resData['msg']     = '抱歉，您来晚了，红包已全部抢光了！';

					return $resData;
				}
				$allot_money = $amount_allot[0];
				if ($external_money + $allot_money > $day_external_money) {
					if ($chatSendRule->type == 1){
						$resData['canOpen'] = 0;
						$resData['msg']     = '今日您的收款额度已不足，无法领取！';

						return $resData;
					}else{
						if ($external_money + 0.3 > $day_external_money){
							$resData['canOpen'] = 0;
							$resData['msg']     = '今日您的收款额度已不足，无法领取！';

							return $resData;
						}else{
							$amount_allot[0]            = $day_external_money - $external_money;
							$chatSendRule->amount_allot = !empty($amount_allot) ? json_encode($amount_allot) : '';
							$chatSendRule->save();
						}
					}

				}

				/*$chatInfo = WorkChatInfo::findOne(['chat_id' => $chatSendRule->chat_id, 'type' => 2, 'external_id' => $external_id]);
				if (empty($chatInfo)) {
					throw new InvalidParameterException('群客户数据错误！');
				}
				$followUser = WorkExternalContactFollowUser::findOne(['user_id' => $chatSendRule->user_id, 'external_userid' => $external_id, 'del_type' => 0]);
				if (empty($followUser)) {
					$resData['canOpen'] = 0;
					$resData['msg']     = '您当前尚未成为企业成员的客户，无法领取红包！';

					return $resData;
				}*/
			}

			if ($external_money >= $day_external_money) {
				$resData['canOpen'] = 0;
				$resData['msg']     = '今日您的收款额度已达上限，无法领取！';

				return $resData;
			}
			if ($external_num >= $day_external_num) {
				$resData['canOpen'] = 0;
				$resData['msg']     = '今日您的收款次数已达上限，无法领取！';

				return $resData;
			}

			return $resData;
		}

		/**
		 * 红包领取
		 *
		 * @param          $type
		 * @param          $send_id
		 * @param          $external_id
		 * @param          $remark
		 *
		 * @return int
		 *
		 * @throws InvalidParameterException
		 */
		public static function redpacketOpen ($type, $send_id, $external_id, $remark, $openid = '')
		{
			$sendData = [];
			$time     = time();
			//客户信息
			if ($external_id) {
				$externalInfo = WorkExternalContact::findOne($external_id);
				if (empty($externalInfo) || empty($externalInfo->openid)) {
					throw new InvalidParameterException('客户参数缺失！');
				}
				$openid = $externalInfo->openid;
			}

			if ($type == MoneyOrder::REDPACKET_CHAT_SEND) {
				$chatSendRule = RedPackChatSendRule::findOne($send_id);
				if (empty($chatSendRule)) {
					throw new InvalidParameterException('发放参数不正确！');
				}
				$corp_id  = $chatSendRule->corp_id;
				$userCorp = UserCorpRelation::findOne(['corp_id' => $corp_id]);

				$amount_allot = !empty($chatSendRule->amount_allot) ? json_decode($chatSendRule->amount_allot, true) : [];
				$allot_money  = array_shift($amount_allot);

				$moneyOrder = static::findOne(['corp_id' => $corp_id, 'chat_send_id' => $chatSendRule->id, 'external_id' => $external_id]);
				if (!empty($moneyOrder)) {
					if (in_array($moneyOrder->status, [1, 2, 3])) {
						throw new InvalidParameterException('已存在群红包领取记录！');
					}
				} else {
					$moneyOrder           = new MoneyOrder();
					$moneyOrder->uid      = $userCorp->uid;
					$moneyOrder->order_id = '33' . date('YmdHis') . $chatSendRule->user_id . mt_rand(1111, 9999);
					$moneyOrder->corp_id  = $corp_id;
					$moneyOrder->user_id  = $chatSendRule->user_id;
				}
				$moneyOrder->external_id  = $external_id;
				$moneyOrder->goods_type   = 'redPacket';
				$moneyOrder->goods_id     = 0;
				$moneyOrder->money        = $allot_money;
				$moneyOrder->send_time    = $time;
				$moneyOrder->remark       = $chatSendRule->remark;
				$moneyOrder->message      = $remark;
				$moneyOrder->ispay        = 0;
				$moneyOrder->chat_send_id = $chatSendRule->id;
				$moneyOrder->openid       = $openid;
				if (!$moneyOrder->validate() || !$moneyOrder->save()) {
					throw new InvalidParameterException(SUtils::modelError($moneyOrder));
				}
			} else {
				$moneyOrder = static::findOne($send_id);
				if (empty($moneyOrder)) {
					throw new InvalidParameterException('发放参数不正确！');
				}
				$corp_id = $moneyOrder->corp_id;
			}

			$sendData['partner_trade_no'] = $moneyOrder->order_id;
			$sendData['openid']           = $openid;
			$sendData['amount']           = $moneyOrder->money * 100;
			$sendData['desc']             = $remark;

			try {
				$redPacketPay = new RedPacketPay();
				$resData      = $redPacketPay->RedPacketSend($corp_id, $sendData);
				\Yii::error($sendData, 'RedPacketOpenData');
				\Yii::error($resData, 'RedPacketOpenResData');
				if ($resData['return_code'] == 'SUCCESS' && $resData['result_code'] == 'SUCCESS') {
					$moneyOrder->ispay          = 1;
					$moneyOrder->pay_time       = strtotime($resData['payment_time']);
					$moneyOrder->transaction_id = $resData['payment_no'];
					$moneyOrder->status         = 1;

					if (!$moneyOrder->validate() || !$moneyOrder->save()) {
						throw new InvalidParameterException(SUtils::modelError($moneyOrder));
					}

					if ($type == MoneyOrder::REDPACKET_CHAT_SEND) {
						$chatSendRule->get_amount   += $allot_money;
						$chatSendRule->get_num      += 1;
						$chatSendRule->amount_allot = !empty($amount_allot) ? json_encode($amount_allot) : '';
						$chatSendRule->update_time  = DateUtil::getCurrentTime();
						$chatSendRule->save();
					}

					//记录客户轨迹
					if ($external_id){
						if ($type == MoneyOrder::REDPACKET_SEND){
							ExternalTimeLine::addExternalTimeLine(['uid' => $moneyOrder->uid, 'external_id' => $external_id, 'user_id' => $moneyOrder->user_id, 'event' => 'send_money', 'related_id' => $moneyOrder->id, 'remark' => strval($moneyOrder->money)]);
						} else {
							ExternalTimeLine::addExternalTimeLine(['uid' => $moneyOrder->uid, 'external_id' => $external_id, 'user_id' => $moneyOrder->user_id, 'event' => 'send_chat_money', 'event_id' => $chatSendRule->chat_id, 'related_id' => $moneyOrder->id, 'remark' => strval($moneyOrder->money)]);
						}
					}
					//群客户轨迹
					if ($type == MoneyOrder::REDPACKET_CHAT_SEND) {
						$workUser = WorkUser::findOne($moneyOrder->user_id);
						$remark   = !empty($external_id) ? '客户【' . $externalInfo->name_convert . '】' : '未知客户';
						$remark   .= '领取群主【' . $workUser->name . '】发送的【' . $moneyOrder->money . '元】';
						$remark   .= $chatSendRule->type == 1 ? '固定金额红包' : '拼手气红包';
						ExternalTimeLine::addExternalTimeLine(['uid' => $moneyOrder->uid, 'external_id' => $external_id, 'user_id' => $moneyOrder->user_id, 'event' => 'chat_track_money', 'event_id' => 11, 'related_id' => $chatSendRule->chat_id, 'remark' => $remark]);
					}

					return true;
				} else {
					$msg = isset($resData['err_code_des']) && !empty($resData['err_code_des']) ? $resData['err_code_des'] : '';
					$msg = empty($msg) && isset($resData['return_msg']) ? $resData['return_msg'] : $msg;

					$moneyOrder->status    = 4;
					$moneyOrder->extrainfo = $msg;
					$moneyOrder->save();

					$msg = $msg == '余额不足' ? '抱歉，领取失败，商户账户余额不足' : $msg;
					$msg = $msg == '该用户今日付款次数超过限制，如有需要请进入微信支付商户平台-产品中心-企业付款到零钱-产品设置进行修改' ? '您今日领取次数已达上限' : $msg;

					throw new InvalidParameterException($msg);
				}
			} catch (\Exception $e) {
				throw new InvalidParameterException($e->getMessage());
			}
		}


	}
