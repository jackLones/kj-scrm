<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_chat}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id     授权的企业ID
	 * @property int      $tag_id      授权的企业的标签ID
	 * @property int      $chat_id     群ID
	 * @property int      $status      0不显示1显示
	 * @property string   $update_time 更新时间
	 * @property string   $add_time    创建时间
	 *
	 * @property WorkCorp $corp
	 * @property WorkTag  $tag
	 * @property WorkChat $chat
	 */
	class WorkTagChat extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_chat}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'tag_id', 'chat_id', 'status'], 'integer'],
				[['update_time', 'add_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
				[['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '企业微信ID'),
				'tag_id'      => Yii::t('app', '标签ID'),
				'chat_id'     => Yii::t('app', '群ID'),
				'status'      => Yii::t('app', '0不显示1显示'),
				'update_time' => Yii::t('app', '更新时间'),
				'add_time'    => Yii::t('app', '创建时间'),
			];
		}

		/**
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
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTag ()
		{
			return $this->hasOne(WorkTag::className(), ['id' => 'tag_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getChat ()
		{
			return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
		}

		public static function addChatTag ($corp_id, $chat_id, array $tag_ids)
		{
			foreach ($tag_ids as $tag_id) {
				$tagChat = static::findOne(['tag_id' => $tag_id, 'chat_id' => $chat_id]);
				if (empty($tagChat)) {
					$tagChat           = new WorkTagChat();
					$tagChat->add_time = DateUtil::getCurrentTime();
				}
				$tagChat->corp_id = $corp_id;
				$tagChat->tag_id  = $tag_id;
				$tagChat->chat_id = $chat_id;
				$tagChat->status  = 1;
				if (!$tagChat->validate() || !$tagChat->save()) {
					throw new InvalidDataException(SUtils::modelError($tagChat));
				}
			}
		}

		public static function removeChatTag ($chat_id, array $tag_ids)
		{
			foreach ($tag_ids as $tag_id) {
				$tagChat = static::findOne(['tag_id' => $tag_id, 'chat_id' => $chat_id]);
				if (!empty($tagChat)) {
					$tagChat->status  = 0;
					$tagChat->update();
				}
			}
		}

		/**
		 * 新建群标签（客户标签作为群标签使用的，创建新的群标签）
		 */
		public static function copyChatTag ()
		{
			try {
				$tagChat = static::find()->where(['status' => 1])->groupBy('tag_id')->all();

				foreach ($tagChat as $v) {
					$workTag = WorkTag::findOne(['id' => $v->tag_id, 'type' => 0, 'is_del' => 0]);
					if (!empty($workTag)) {
						$workTagGroup = WorkTagGroup::findOne(['id' => $workTag->group_id]);
						if (!empty($workTagGroup)) {
							$newTagGroup = WorkTagGroup::findOne(['corp_id' => $workTagGroup->corp_id, 'group_name' => $workTagGroup->group_name, 'type' => 2]);
							if (empty($newTagGroup)) {
								$newTagGroup             = new WorkTagGroup();
								$newTagGroup->corp_id    = $workTagGroup->corp_id;
								$newTagGroup->group_name = $workTagGroup->group_name;
								$newTagGroup->type       = 2;
								$newTagGroup->sort       = $workTagGroup->sort;

								if (!$newTagGroup->validate() || !$newTagGroup->save()) {
									throw new InvalidDataException(SUtils::modelError($newTagGroup));
								}
							}

							$newWorkTag = WorkTag::findOne(['corp_id' => $workTag->corp_id, 'tagname' => $workTag->tagname, 'type' => 2]);
							if (empty($newWorkTag)) {
								$newWorkTag           = new WorkTag();
								$newWorkTag->corp_id  = $workTag->corp_id;
								$newWorkTag->tagname  = $workTag->tagname;
								$newWorkTag->type     = 2;
								$newWorkTag->group_id = $newTagGroup->id;

								if (!$newWorkTag->validate() || !$newWorkTag->save()) {
									throw new InvalidDataException(SUtils::modelError($newWorkTag));
								}
							}

							static::updateAll(['tag_id' => $newWorkTag->id], ['tag_id' => $v->tag_id]);
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'copyChatTag-getMessage');
			}

			return true;
		}
	}
