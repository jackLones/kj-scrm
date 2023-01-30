<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_news}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $title         图文消息标题
	 * @property string                           $description   图文消息描述
	 * @property string                           $url           图文消息点击跳转地址
	 * @property string                           $picurl        图文消息配图的url
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfo                 $auditInfo
	 */
	class WorkMsgAuditInfoNews extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = "news";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_news}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['url', 'picurl'], 'string'],
				[['title'], 'string', 'max' => 64],
				[['description'], 'string', 'max' => 255],
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
				'title'         => Yii::t('app', '图文消息标题'),
				'description'   => Yii::t('app', '图文消息描述'),
				'url'           => Yii::t('app', '图文消息点击跳转地址'),
				'picurl'        => Yii::t('app', '图文消息配图的url'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['news_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['news_id' => 'id']);
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
				'title'       => $this->title,
				'description' => $this->description,
				'url'         => $this->url,
				'picurl'      => $this->picurl
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
			$newsInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($newsInfo) || $needCreate) {
				if (!empty($info['item'])) {
					foreach ($info['item'] as $item) {
						$newsInfo                = new self();
						$newsInfo->audit_info_id = $infoId;
						$newsInfo->title         = $item['title'];
						$newsInfo->description   = $item['description'];
						$newsInfo->url           = $item['url'];
						$newsInfo->picurl        = $item['picurl'];

						if (!$newsInfo->validate() || !$newsInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($newsInfo));
						}
					}
				}
			}

			return $newsInfo->id;
		}
	}
