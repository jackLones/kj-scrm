<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_vote}}".
	 *
	 * @property int                     $id
	 * @property int                     $audit_info_id 会话内容ID
	 * @property string                  $votetitle     投票主题
	 * @property string                  $voteitem      投票选项，可能多个内容
	 * @property int                     $votetype      投票类型.101发起投票、102参与投票
	 * @property string                  $voteid        投票id，方便将参与投票消息与发起投票消息进行前后对照
	 *
	 * @property WorkMsgAuditInfoMixed[] $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfo        $auditInfo
	 */
	class WorkMsgAuditInfoVote extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'vote';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_vote}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['voteitem'], 'string'],
				[['votetitle', 'voteid'], 'string', 'max' => 64],
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
				'votetitle'     => Yii::t('app', '投票主题'),
				'voteitem'      => Yii::t('app', '投票选项，可能多个内容'),
				'votetype'      => Yii::t('app', '投票类型.101发起投票、102参与投票'),
				'voteid'        => Yii::t('app', '投票id，方便将参与投票消息与发起投票消息进行前后对照'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['vote_id' => 'id']);
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
				'votetitle' => $this->votetitle,
				'voteitem'  => json_decode($this->voteitem),
				'votetype'  => $this->votetype,
				'voteid'    => $this->voteid,
			];
		}

		/**
		 * @param      $infoId
		 * @param      $info
		 * @param bool $needCreate
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($infoId, $info, $needCreate = false)
		{
			$voteInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($voteInfo) || $needCreate) {
				$voteInfo                = new self();
				$voteInfo->audit_info_id = $infoId;
				$voteInfo->votetitle     = $info['votetitle'];
				$voteInfo->voteitem      = json_encode($info['voteitem'], JSON_UNESCAPED_UNICODE);
				$voteInfo->votetype      = $info['votetype'];
				$voteInfo->voteid        = $info['voteid'];

				if (!$voteInfo->validate() || !$voteInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($voteInfo));
				}
			}

			return $voteInfo->id;
		}
	}
