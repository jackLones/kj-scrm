<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_category}}".
	 *
	 * @property int                          $id
	 * @property string                       $category_type 类别标识
	 * @property string                       $category_name 类别名称
	 * @property int                          $status        状态：0、关闭；1、开启
	 * @property string                       $create_time   创建时间
	 *
	 * @property WorkMsgAuditNoticeRuleInfo[] $workMsgAuditNoticeRuleInfos
	 */
	class WorkMsgAuditCategory extends \yii\db\ActiveRecord
	{
		const OPEN_CATEGORY = 1;
		const CLOSE_CATEGORY = 0;

		const TEXT_CATEGORY = "text";
		const IMAGE_CATEGORY = "image";
		const REVOKE_CATEGORY = "revoke";
		const AGREE_CATEGORY = "agree";
		const DISAGREE_CATEGORY = "disagree";
		const VOICE_CATEGORY = "voice";
		const VIDEO_CATEGORY = "video";
		const CARD_CATEGORY = "card";
		const LOCATION_CATEGORY = "location";
		const EMOTION_CATEGORY = "emotion";
		const FILE_CATEGORY = "file";
		const LINK_CATEGORY = "link";
		const WEAPP_CATEGORY = "weapp";
		const CHATRECORD_CATEGORY = "chatrecord";
		const TODO_CATEGORY = "todo";
		const VOTE_CATEGORY = "vote";
		const COLLECT_CATEGORY = "collect";
		const REDPACKET_CATEGORY = "redpacket";
		const MEETING_CATEGORY = "meeting";
		const DOCMSG_CATEGORY = "docmsg";
		const MARKDOWN_CATEGORY = "markdown";
		const NEWS_CATEGORY = "news";
		const CALENDAR_CATEGORY = "calendar";
		const MIXED_CATEGORY = "mixed";
		const MEETING_VOICE_CALL_CATEGORY = "meeting_voice_call";
		const VOIP_DOC_SHARE_CATEGORY = "voip_doc_share";
		const EXTERNAL_REDPACKET_CATEGORY = "external_redpacket";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_category}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['status'], 'integer'],
				[['create_time'], 'safe'],
				[['category_type', 'category_name'], 'string', 'max' => 64],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'category_type' => Yii::t('app', '类别标识'),
				'category_name' => Yii::t('app', '类别名称'),
				'status'        => Yii::t('app', '状态：0、关闭；1、开启'),
				'create_time'   => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditNoticeRuleInfos ()
		{
			return $this->hasMany(WorkMsgAuditNoticeRuleInfo::className(), ['category_id' => 'id']);
		}

		/**
		 * @return array
		 */
		public static function getCategory ()
		{
			$category     = [];
			$categoryData = self::findall(['status' => self::OPEN_CATEGORY]);
			if (!empty($categoryData)) {
				foreach ($categoryData as $categoryInfo) {
					$category[$categoryInfo->id] = $categoryInfo->category_name;
				}
			}

			return $category;
		}
	}
