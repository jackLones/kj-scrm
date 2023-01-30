<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\DateUtil;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%red_pack_chat_send_rule}}".
 *
 * @property int $id
 * @property int $corp_id 企业微信ID
 * @property int $user_id 员工ID
 * @property int $chat_id 群ID
 * @property int $type 单个红包金额类型：1、固定金额，2、随机金额
 * @property string $redpacket_amount 红包金额
 * @property int $redpacket_num 红包个数
 * @property string $get_amount 领取金额
 * @property int $get_num 领取个数
 * @property string $amount_allot 红包分配
 * @property string $remark 红包备注
 * @property string $des 描述（祝福语）
 * @property string $create_time 创建时间
 * @property string $update_time 修改时间
 *
 * @property WorkCorp $corp
 * @property WorkUser $user
 * @property WorkChat $chat
 */
class RedPackChatSendRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%red_pack_chat_send_rule}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'chat_id', 'type', 'redpacket_num', 'get_num'], 'integer'],
            [['redpacket_amount', 'get_amount'], 'number'],
            [['amount_allot'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['des'], 'string', 'max' => 500],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '企业微信ID'),
            'user_id' => Yii::t('app', '员工ID'),
            'chat_id' => Yii::t('app', '群ID'),
            'type' => Yii::t('app', '单个红包金额类型：1、固定金额，2、随机金额'),
            'redpacket_amount' => Yii::t('app', '红包金额'),
            'redpacket_num' => Yii::t('app', '红包个数'),
            'get_amount' => Yii::t('app', '领取金额'),
            'get_num' => Yii::t('app', '领取个数'),
            'amount_allot' => Yii::t('app', '红包分配'),
            'remark' => Yii::t('app', '红包备注'),
            'des' => Yii::t('app', '描述（祝福语）'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
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
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
    }

	/**
	 * @param $data
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 */
	public static function setData ($data)
	{
		$sendRule                   = new RedPackChatSendRule();
		$sendRule->corp_id          = $data['corp_id'];
		$sendRule->user_id          = $data['user_id'];
		$sendRule->chat_id          = $data['chat_id'];
		$sendRule->type             = $data['type'];
		$sendRule->redpacket_amount = $data['redpacket_amount'];
		$sendRule->redpacket_num    = $data['redpacket_num'];
		$sendRule->remark           = $data['remark'];
		$sendRule->des              = $data['des'];
		$sendRule->create_time      = DateUtil::getCurrentTime();

		$amount_allot = static::randBonus($data['redpacket_amount'], $data['redpacket_num'], $data['type']);
		if (empty($amount_allot)) {
			throw new InvalidDataException('红包金额设置错误');
		}
		$sendRule->amount_allot = !empty($amount_allot) ? json_encode($amount_allot) : '';

		if (!$sendRule->validate() || !$sendRule->save()) {
			throw new InvalidDataException(SUtils::modelError($sendRule));
		}

		//群轨迹
		$workUser = WorkUser::findOne($sendRule->user_id);
		$remark   = '群主【' . $workUser->name . '】向群里发送' . $data['redpacket_num'] . '份';
		$remark   .= $data['type'] == 1 ? '固定金额红包' : '拼手气红包';
		$remark   .= '，共' . $data['redpacket_amount'] . '元';
		ExternalTimeLine::addExternalTimeLine(['uid' => $data['uid'], 'external_id' => 0, 'user_id' => $sendRule->user_id, 'event' => 'chat_track', 'event_id' => 10, 'related_id' => $sendRule->chat_id, 'remark' => $remark]);

		return $sendRule->id;
	}

	/**
	 * 群红包金额分配
	 * @param $money_total  红包总金额
	 * @param $personal_num  红包个数
	 * @param $bonus_type  红包类型1普通2随机
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 */
	public static function randBonus ($money_total, $personal_num, $bonus_type)
	{
		$min_money   = 0.3;
		$money_right = $money_total;
		$money_avg   = number_format($money_total / $personal_num, 2); // 平均每个红包多少钱

		$randMoney = [];
		for ($i = 1; $i <= $personal_num; $i++) {
			if ($i == $personal_num) {
				$money = $money_right;
			} else {
				if ($bonus_type == 2) {
					$max   = $money_right * 100 - ($personal_num - $i) * $min_money * 100;
					$money = rand($min_money * 100, $max) / 100;
					$money = sprintf("%.2f", $money);
					//防止出现低于0.3的金额
					$money = $money > 0.3 ? $money : 0.3;
					if ($money_right < $money && $money_right < 0.3){
						break;
					}
				} else {
					$money = sprintf("%.2f", $money_avg);
				}
			}
			$randMoney[] = $money;
			$money_right = $money_right - $money;
			$money_right = sprintf("%.2f", $money_right);
		}
		shuffle($randMoney);

		return $randMoney;
	}
}
