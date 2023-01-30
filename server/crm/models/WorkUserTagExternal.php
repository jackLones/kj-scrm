<?php

	namespace app\models;

	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_user_tag_external}}".
	 *
	 * @property int                 $id
	 * @property int                 $corp_id                    企业微信id
	 * @property int                 $user_id                    授权的企业的成员ID
	 * @property int                 $external_id                外部联系人ID
	 * @property int                 $tag_id                     标签id
	 * @property int                 $follow_user_id             外部联系人对应的ID
	 * @property int                 $keyword                    关键词
	 * @property int                 $audit_info_id              会话内容ID
	 * @property int                 $status                     0删除、1可用
	 * @property int                 $add_time                   打标签时间
	 *
	 * @property WorkCorp            $corp
	 * @property WorkExternalContact $external
	 * @property WorkTag             $tag
	 * @property WorkUser            $user
	 */
	class WorkUserTagExternal extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_tag_external}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'user_id', 'external_id', 'tag_id'], 'required'],
				[['corp_id', 'user_id', 'external_id', 'tag_id', 'status'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'corp_id'        => Yii::t('app', '企业微信id'),
				'user_id'        => Yii::t('app', '授权的企业的成员ID'),
				'external_id'    => Yii::t('app', '外部联系人ID'),
				'tag_id'         => Yii::t('app', '标签id'),
				'follow_user_id' => Yii::t('app', '外部联系人对应的ID'),
				'keyword'        => Yii::t('app', '关键词'),
				'audit_info_id'  => Yii::t('app', '会话内容ID'),
				'status'         => Yii::t('app', '0删除、1可用'),
				'add_time'       => Yii::t('app', '打标签时间'),
			];
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
		public function getExternal ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_id']);
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
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/*
		 * 记录客户打标签
		 *
		 * $auditInfo 会话消息数据
		 * $content  发送内容
		 */
		public static function setData ($auditInfo, $content)
		{
			try {
				/**@var WorkMsgAuditInfo $auditInfo * */
				$corpId = $auditInfo->audit->corp_id;

				$fromType = $auditInfo->from_type;
				$toType   = $auditInfo->to_type;
				//不是客户对员工的不做通知
				if (!($fromType == SUtils::IS_EXTERNAL_USER && $toType == SUtils::IS_WORK_USER)) {
					return '不是客户对员工的不做通知';
				}
				$externalId = $auditInfo->external_id;
				$userId     = $auditInfo->to_user_id;
				//成员信息
				$workUser = WorkUser::findOne(['corp_id' => $corpId, 'id' => $userId]);
				if (empty($workUser)) {
					return '未找到员工信息';
				}
				if (empty($externalId) || empty($userId)) {
					return '不能同时为空';
				}

				$followUser = WorkExternalContactFollowUser::findOne(['external_userid' => $externalId, 'user_id' => $userId]);
				if (empty($followUser)) {
					return '未找到外部联系人';
				}
				$followUserId = $followUser->id;

				//查询是否有符合的规则
				$userTagRule = WorkUserTagRule::findOne(['corp_id' => $corpId, 'user_id' => $userId, 'status' => 2]);
				if (empty($userTagRule) || empty($userTagRule->tags_id)) {
					return '没有符合的规则';
				}

				//是否有符合的关键词和标签
				$tagWordData = $tagIdData = $wordData = [];
				$tagIds      = explode(',', $userTagRule->tags_id);
				$workTag     = WorkTag::find()->where(['id' => $tagIds, 'is_del' => 0, 'type' => 0])->all();

				/**@var WorkTag $tag * */
				foreach ($workTag as $tag) {
					$tag_id      = $tag->id;
					$keyWordRule = WorkTagKeywordRule::find()->where(['corp_id' => $corpId, 'status' => 2])->andWhere("find_in_set ($tag_id,tags_id)")->one();
					if (!empty($keyWordRule)) {
						//查询此标签是否已打过
						$tagFollow = WorkTagFollowUser::findOne(['corp_id' => $corpId, 'tag_id' => $tag_id, 'follow_user_id' => $followUserId, 'status' => 1]);
						if (empty($tagFollow)) {
							$keyWordData = json_decode($keyWordRule->keyword, 1);
							foreach ($keyWordData as $word) {
								if (strpos($content, $word) !== false) {
									$tagWordData[] = ['tag_id' => $tag_id, 'keyword' => $word];
									array_push($tagIdData, $tag_id);
									array_push($wordData, '【' . $word . '】');
									break;
								}
							}
						}
					}
				}

				\Yii::error($tagWordData, 'tagWordData');
				\Yii::error($tagIdData, 'tagIdData');
				\Yii::error($wordData, 'wordData');
				//无符合的关键词
				if (empty($tagWordData)) {
					return '无符合的关键词';
				}

				//打标签
				$wordData = array_unique($wordData);
				$wordStr   = implode('、', $wordData);
				$otherData = ['type' => 'chat_tag', 'msg' => '与成员【' . $workUser->name . '】聊天中触发' . $wordStr . '关键词，自动给该客户打上'];
				WorkTag::addUserTag(2, [$followUserId], $tagIdData, $otherData);

				//添加打标签数据
				foreach ($tagWordData as $tagWord) {
					$tagFollow = WorkTagFollowUser::findOne(['corp_id' => $corpId, 'tag_id' => $tagWord['tag_id'], 'follow_user_id' => $followUserId]);
					if (!empty($tagFollow)) {
						$tagExternal = WorkUserTagExternal::findOne(['corp_id' => $corpId, 'user_id' => $userId, 'external_id' => $externalId, 'tag_id' => $tagWord['tag_id'], 'follow_user_id' => $followUserId]);
						if (empty($tagExternal)) {
							$tagExternal                 = new WorkUserTagExternal();
							$tagExternal->corp_id        = $corpId;
							$tagExternal->user_id        = $userId;
							$tagExternal->external_id    = $externalId;
							$tagExternal->tag_id         = $tagWord['tag_id'];
							$tagExternal->follow_user_id = $followUserId;
						}
						$tagExternal->keyword       = $tagWord['keyword'];
						$tagExternal->audit_info_id = $auditInfo->id;
						$tagExternal->status        = 1;
						$tagExternal->add_time      = time();
						if (!$tagExternal->validate() || !$tagExternal->save()) {

						}
					}
				}

				return '完成';
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'addTag');
			}
		}
	}
