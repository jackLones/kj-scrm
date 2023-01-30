<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use app\util\WxPay\RedPacketPay;
	use Yii;

	/**
	 * This is the model class for table "{{%red_pack_help_detail}}".
	 *
	 * @property int         $id
	 * @property int         $rid            裂变任务id
	 * @property int         $jid            参与表id
	 * @property int         $external_id    外部联系人id
	 * @property int         $openid         外部联系人openid
	 * @property string      $amount         红包金额
	 * @property int         $status         有效状态：0无效、1有效
	 * @property int         $send_status    发放状态：0未发放、1已发放
	 * @property int         $send_type      发放类型：1零钱发放、2标记发放
	 * @property string      $help_time      助力时间
	 * @property string      $is_remind      是否需要提醒：0否、1是
	 *
	 * @property RedPackJoin $j
	 * @property RedPack     $r
	 */
	class RedPackHelpDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%red_pack_help_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['rid', 'jid', 'external_id', 'status', 'send_status'], 'integer'],
				[['amount'], 'number'],
				[['help_time'], 'safe'],
				[['jid'], 'exist', 'skipOnError' => true, 'targetClass' => RedPackJoin::className(), 'targetAttribute' => ['jid' => 'id']],
				[['rid'], 'exist', 'skipOnError' => true, 'targetClass' => RedPack::className(), 'targetAttribute' => ['rid' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'rid'         => Yii::t('app', '裂变任务id'),
				'jid'         => Yii::t('app', '参与表id'),
				'external_id' => Yii::t('app', '外部联系人id'),
				'openid'      => Yii::t('app', '外部联系人openid'),
				'amount'      => Yii::t('app', '红包金额'),
				'status'      => Yii::t('app', '有效状态：0无效、1有效'),
				'send_status' => Yii::t('app', '发放状态：0未发放、1已发放'),
				'send_type'   => Yii::t('app', '发放类型：1零钱发放、2标记发放'),
				'help_time'   => Yii::t('app', '助力时间'),
				'is_remind'   => Yii::t('app', '是否需要提醒：0否、1是'),
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
		public function getJ ()
		{
			return $this->hasOne(RedPackJoin::className(), ['id' => 'jid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getR ()
		{
			return $this->hasOne(RedPack::className(), ['id' => 'rid']);
		}

		//更改助力者有效状态
		public static function updateStatus ($redPack)
		{
			/** @var RedPack $redPack * */
			if (empty($redPack)) {
				return '';
			}
			try {
				$user     = json_decode($redPack->user, 1);
				$helpData = static::find()->alias('rhd');
				$helpData = $helpData->leftJoin('{{%work_external_contact_follow_user}} wec', '`rhd`.`external_id` = `wec`.`external_userid`');
				$helpData = $helpData->where(['rhd.rid' => $redPack->id, 'rhd.send_status' => 0, 'wec.del_type' => [1, 2], 'wec.userid' => $user]);
				$helpData = $helpData->select('rhd.*')->groupBy('rhd.id')->all();
				if (!empty($helpData)) {
					foreach ($helpData as $help) {
						/** @var RedPackHelpDetail $help * */
						$help->status = 0;
						$help->update();
					}
				}
			} catch (\Exception $e) {

			}
		}
	}
