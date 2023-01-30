<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_chat_remind}}".
	 *
	 * @property int                  $id
	 * @property int                  $corp_id      企业微信id
	 * @property int                  $agentid      应用id
	 * @property string               $title        规则名称
	 * @property string               $chat_ids     适用群id集合
	 * @property string               $remind_user  接收成员
	 * @property int                  $is_image     是否图片提醒1是0否
	 * @property int                  $is_link      是否链接提醒1是0否
	 * @property int                  $is_weapp     是否小程序提醒1是0否
	 * @property int                  $is_card      是否名片提醒1是0否
	 * @property int                  $is_voice     是否音频提醒1是0否
	 * @property int                  $is_video     是否视频提醒1是0否
	 * @property int                  $is_redpacket 是否红包提醒1是0否
	 * @property int                  $is_text      是否文本关键词提醒1是0否
	 * @property string               $keyword      关键词集合
	 * @property int                  $status       是否有效1是0否
	 * @property int                  $add_time     创建时间
	 * @property int                  $upt_time     更新时间
	 *
	 * @property WorkCorpAgent        $agent
	 * @property WorkCorp             $corp
	 */
	class WorkChatRemind extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_remind}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id'], 'required'],
				[['corp_id', 'agentid', 'is_image', 'is_link', 'is_weapp', 'is_card', 'is_voice', 'is_video', 'is_redpacket', 'is_text', 'status', 'add_time', 'upt_time'], 'integer'],
				[['title', 'keyword'], 'string', 'max' => 255],
				[['chat_ids'], 'string', 'max' => 1000],
				[['remind_user'], 'string', 'max' => 2000],
				[['agentid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agentid' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'corp_id'      => Yii::t('app', '企业微信id'),
				'agentid'      => Yii::t('app', '应用id'),
				'title'        => Yii::t('app', '规则名称'),
				'chat_ids'     => Yii::t('app', '适用群id集合'),
				'remind_user'  => Yii::t('app', '接收成员'),
				'is_image'     => Yii::t('app', '是否图片提醒1是0否'),
				'is_link'      => Yii::t('app', '是否链接提醒1是0否'),
				'is_weapp'     => Yii::t('app', '是否小程序提醒1是0否'),
				'is_card'      => Yii::t('app', '是否名片提醒1是0否'),
				'is_voice'     => Yii::t('app', '是否音频提醒1是0否'),
				'is_video'     => Yii::t('app', '是否视频提醒1是0否'),
				'is_redpacket' => Yii::t('app', '是否红包提醒1是0否'),
				'is_text'      => Yii::t('app', '是否文本关键词提醒1是0否'),
				'keyword'      => Yii::t('app', '关键词集合'),
				'status'       => Yii::t('app', '是否有效1是0否'),
				'add_time'     => Yii::t('app', '创建时间'),
				'upt_time'     => Yii::t('app', '更新时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agentid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @param $data
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException]
		 */
		public static function creat ($data)
		{
			$hasRemind = WorkChatRemind::find()->andWhere(['corp_id' => $data['corp_id'], 'status' => 1, 'title' => $data['title']]);
			if (!empty($data['remind_id'])) {
				$hasRemind = $hasRemind->andWhere(['!=', 'id', $data['remind_id']]);
			}
			$hasRemind = $hasRemind->one();
			if (!empty($hasRemind)) {
				throw new InvalidDataException('规则名称已存在');
			}

			foreach ($data['chat_ids'] as $chat_id) {
				$chatRemind = WorkChatRemind::find()->andWhere(['corp_id' => $data['corp_id'], 'status' => 1]);
				$chatRemind = $chatRemind->andWhere(['like', 'chat_ids', '"' . $chat_id . '"']);
				if (!empty($data['remind_id'])) {
					$chatRemind = $chatRemind->andWhere(['!=', 'id', $data['remind_id']]);
				}
				$chatRemind = $chatRemind->one();

				if (!empty($chatRemind)) {
					$workChat = WorkChat::findOne($chat_id);
					throw new InvalidDataException('群聊【' . $workChat->name . '】已设置过群提醒，不能重复设置');
				}
			}

			if (!empty($data['remind_id'])) {
				$chatRemind = static::findOne($data['remind_id']);
				if (empty($chatRemind)) {
					throw new InvalidDataException('群提醒数据错误');
				}
				$chatRemind->upt_time = time();
			} else {
				$chatRemind           = new WorkChatRemind();
				$chatRemind->add_time = time();
			}

			$chatRemind->corp_id      = $data['corp_id'];
			$chatRemind->agentid      = $data['agentid'];
			$chatRemind->title        = $data['title'];
			$chatRemind->chat_ids     = json_encode($data['chat_ids']);
			$chatRemind->remind_user  = json_encode($data['remind_user']);
			$chatRemind->is_image     = $data['is_image'];
			$chatRemind->is_link      = $data['is_link'];
			$chatRemind->is_weapp     = $data['is_weapp'];
			$chatRemind->is_card      = $data['is_card'];
			$chatRemind->is_voice     = $data['is_voice'];
			$chatRemind->is_video     = $data['is_video'];
			$chatRemind->is_redpacket = $data['is_redpacket'];
			$chatRemind->is_text      = $data['is_text'];
			$chatRemind->keyword      = json_encode($data['keyword']);
			$chatRemind->status       = 1;

			if (!$chatRemind->validate() || !$chatRemind->save()) {
				throw new InvalidDataException(SUtils::modelError($chatRemind));
			}

			//同步以前的消息敏感词监控
			if (!empty($data['is_text']) && !empty($data['keyword'])) {
				foreach ($data['chat_ids'] as $chat_id) {
					LimitWordMsg::pushJob($data['keyword'], ['corp_id' => $data['corp_id'], 'chat_id' => $chat_id, 'audit_id' => $data['audit_id'], 'uid' => $data['uid']]);
				}
			}

			return true;
		}
	}
