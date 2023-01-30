<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_user_tag_rule}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id     企业微信id
	 * @property int      $user_id     授权的企业的成员ID
	 * @property string   $tags_id     标签id
	 * @property int      $status      0删除、1关闭、2开启
	 * @property string   $update_time 更新时间
	 * @property string   $add_time    创建时间
	 *
	 * @property WorkCorp $corp
	 * @property WorkUser $user
	 */
	class WorkUserTagRule extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_tag_rule}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'user_id'], 'required'],
				[['corp_id', 'user_id', 'status'], 'integer'],
				[['tags_id'], 'string'],
				[['update_time', 'add_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '企业微信id'),
				'user_id'     => Yii::t('app', '授权的企业的成员ID'),
				'tags_id'     => Yii::t('app', '标签id'),
				'status'      => Yii::t('app', '0删除、1关闭、2开启'),
				'update_time' => Yii::t('app', '更新时间'),
				'add_time'    => Yii::t('app', '创建时间'),
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
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		public function dumpData ()
		{
			$result = [
				'id'        => $this->id,
				'key'       => (string) $this->id,
				'user_id'   => $this->user_id,
				'status'    => $this->status,
				'avatar'    => '',
				'user_name' => '',
			];
			//成员
			$workUser = WorkUser::findOne($this->user_id);
			if (!empty($workUser)) {
				$result['avatar']    = $workUser->avatar;
				$result['user_name'] = $workUser->name;
			}

			//标签
			$tagData = $tagIds = [];
			if (!empty($this->tags_id)) {
				$workTag = WorkTag::find()->where(['corp_id' => $this->corp_id, 'is_del' => 0])->andWhere('id in(' . $this->tags_id . ')')->all();
				if (!empty($workTag)) {
					/**@var WorkTag $tag * */
					foreach ($workTag as $tag) {
						$tag_id = $tag->id;
						$keyWordRule = WorkTagKeywordRule::find()->where(['corp_id' => $this->corp_id,'status' => 2])->andWhere("find_in_set ($tag_id,tags_id)")->one();
						if(!empty($keyWordRule)){
							$tagTitle = $tag->tagname;
							array_push($tagIds, $tag_id);
							$tagData[] = ['id' => (string) $tag_id, 'title' => $tagTitle, 'keyword' => json_decode($keyWordRule->keyword, 1)];
						}
					}
				}
			}

			$result['tagData'] = $tagData;

			//客户数量
			$tagExternal = WorkUserTagExternal::find()->alias('ute');
			$tagExternal = $tagExternal->leftJoin('{{%work_tag_follow_user}} tfu', 'ute.tag_id = tfu.tag_id and ute.follow_user_id = tfu.follow_user_id');
			$tagExternal = $tagExternal->where(['ute.user_id' => $this->user_id, 'ute.tag_id' => $tagIds, 'ute.status' => 1, 'tfu.status' => 1]);
			$tagExternal = $tagExternal->groupBy('ute.external_id');

			$externalNum            = $tagExternal->count();
			$result['external_num'] = $externalNum;

			return $result;
		}

		public static function setData ($data)
		{
			$corp_id = !empty($data['corp_id']) ? $data['corp_id'] : 0;
			$userIds = !empty($data['user_ids']) ? $data['user_ids'] : [];
			$tagIds  = !empty($data['tag_ids']) ? $data['tag_ids'] : [];
			$status  = !empty($data['status']) ? $data['status'] : 2;
			if (empty($corp_id)) {
				throw new InvalidDataException('参数不正确');
			}
			if (empty($userIds)) {
				throw new InvalidDataException('请选择成员');
			}
			if (empty($tagIds)) {
				throw new InvalidDataException('请选择标签');
			}
			$tagStr      = implode(',', $tagIds);
			$userTagInfo = static::find()->where(['user_id' => $userIds, 'status' => [1, 2]])->all();
			if (!empty($userTagInfo)) {
				throw new InvalidDataException('成员已经存在，请更换');
			}
			$addTime     = DateUtil::getCurrentTime();
			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				foreach ($userIds as $user_id) {
					$userTag           = new WorkUserTagRule();
					$userTag->corp_id  = $corp_id;
					$userTag->user_id  = $user_id;
					$userTag->tags_id  = $tagStr;
					$userTag->status   = $status;
					$userTag->add_time = $addTime;
					if (!$userTag->validate() || !$userTag->save()) {
						throw new InvalidDataException(SUtils::modelError($userTag));
					}
				}
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		//根据标签状态修改规则状态
		public static function updateStatus ($userTagRule)
		{
			/**@var WorkUserTagRule $userTagRule * */
			if (!empty($userTagRule->tags_id)) {
				$workTag = WorkTag::find()->where(['corp_id' => $userTagRule->corp_id, 'is_del' => 0])->andWhere('id in(' . $userTagRule->tags_id . ')')->all();
				if (empty($workTag)) {
					$userTagRule->tags_id = '';
					$userTagRule->status  = 1;
					$userTagRule->update();
				} else {
					$tagIds = [];
					/**@var WorkTag $tag * */
					foreach ($workTag as $tag) {
						$tag_id   = $tag->id;
						$ruleInfo = WorkTagKeywordRule::find()->where(['corp_id' => $userTagRule->corp_id, 'status' => 2])->andWhere("find_in_set ($tag_id,tags_id)")->one();
						if (!empty($ruleInfo)) {
							array_push($tagIds, $tag_id);
						}
					}
					if (!empty($tagIds)) {
						$userTagRule->tags_id = implode(',', $tagIds);
						$userTagRule->update();
					} else {
						$userTagRule->tags_id = '';
						$userTagRule->status  = 1;
						$userTagRule->update();
					}
				}
			}
		}
	}
