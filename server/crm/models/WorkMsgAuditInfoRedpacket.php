<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_redpacket}}".
	 *
	 * @property int              $id
	 * @property int              $audit_info_id 会话内容ID
	 * @property int              $type          红包消息类型。1 普通红包、2 拼手气群红包、3 激励群红包
	 * @property string           $wish          红包祝福语
	 * @property int              $totalcnt      红包总个数
	 * @property int              $totalamount   红包总金额。单位为分
	 *
	 * @property WorkMsgAuditInfo $auditInfo
	 */
	class WorkMsgAuditInfoRedpacket extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'redpacket';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_redpacket}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['wish'], 'string', 'max' => 64],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'audit_info_id' => Yii::t('app', '会话内容ID'),
				'type'          => Yii::t('app', '红包消息类型。1 普通红包、2 拼手气群红包、3 激励群红包'),
				'wish'          => Yii::t('app', '红包祝福语'),
				'totalcnt'      => Yii::t('app', '红包总个数'),
				'totalamount'   => Yii::t('app', '红包总金额。单位为分'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		public function dumpData ()
		{
			return [
				'type'        => $this->type,
				'wish'        => $this->wish,
				'totalcnt'    => $this->totalcnt,
				'totalamount' => $this->totalamount,
			];
		}

		/**
		 * @param $infoId
		 * @param $info
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($infoId, $info)
		{
			$redpacketInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($redpacketInfo)) {
				$redpacketInfo                = new self();
				$redpacketInfo->audit_info_id = $infoId;
				$redpacketInfo->type          = $info['type'];
				$redpacketInfo->wish          = $info['wish'];
				$redpacketInfo->totalcnt      = $info['totalcnt'];
				$redpacketInfo->totalamount   = $info['totalamount'];

				if (!$redpacketInfo->validate() || !$redpacketInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($redpacketInfo));
				}

				//发送提醒
				$auditInfo = WorkMsgAuditInfo::findOne($infoId);
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $redpacketInfo->id;
		}
	}
