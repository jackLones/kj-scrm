<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_keyword_rule}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id     企业微信id
	 * @property string   $tags_id     标签id
	 * @property string   $keyword     关键词
	 * @property int      $status      0删除、1关闭、2开启
	 * @property string   $update_time 更新时间
	 * @property string   $add_time    创建时间
	 *
	 * @property WorkCorp $corp
	 */
	class WorkTagKeywordRule extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_keyword_rule}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'keyword'], 'required'],
				[['corp_id', 'status'], 'integer'],
				[['tags_id', 'keyword'], 'string'],
				[['update_time', 'add_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
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
				'tags_id'     => Yii::t('app', '标签id'),
				'keyword'     => Yii::t('app', '关键词'),
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

		public function dumpData ()
		{
			$result = [
				'id'      => $this->id,
				'key'     => (string) $this->id,
				'corp_id' => $this->corp_id,
				'keyword' => json_decode($this->keyword, 1),
				'status'  => $this->status,
			];
			//标签
			$userNum = $externalNum = 0;
			$tagData = $tagIdArr = [];

			$tagWhere = '';
			if (!empty($this->tags_id)) {
				$workTag = WorkTag::find()->where(['corp_id' => $this->corp_id, 'is_del' => 0])->andWhere('id in(' . $this->tags_id . ')')->all();
				if (!empty($workTag)) {
					/**@var WorkTag $tag * */
					foreach ($workTag as $tag) {
						$tagTitle = $tag->tagname;
						$tag_id   = $tag->id;
						array_push($tagIdArr, $tag_id);
						$tagData[] = ['id' => (string) $tag_id, 'title' => $tagTitle];
						$tagWhere  .= " find_in_set ($tag_id,tags_id) or";
					}
				}
			}

			//获取生效员工个数
			$tagWhere = trim($tagWhere, ' or');
			if (!empty($tagWhere)) {
				$userNum = WorkUserTagRule::find()->where(['corp_id' => $this->corp_id, 'status' => [1, 2]])->andWhere($tagWhere)->count();
			}

			$result['tagData']  = $tagData;
			$result['user_num'] = $userNum;

//			//已打客户数
//			if (!empty($tagIdArr)) {
//				$tagExternal = WorkUserTagExternal::find()->alias('ute');
//				$tagExternal = $tagExternal->leftJoin('{{%work_tag_follow_user}} tfu', 'ute.tag_id = tfu.tag_id  and ute.follow_user_id = tfu.follow_user_id');
//				$tagExternal = $tagExternal->where(['ute.tag_id' => $tagIdArr, 'ute.status' => 1, 'tfu.status' => 1]);
//				$tagExternal = $tagExternal->groupBy('ute.external_id');
//				$externalNum = $tagExternal->count();
//			}
//
//			$result['external_num'] = $externalNum;

			return $result;
		}

		//添加数据
		public static function setData ($data)
		{
			$corp_id  = !empty($data['corp_id']) ? $data['corp_id'] : 0;
			$tagRules = !empty($data['tag_rules']) ? $data['tag_rules'] : [];
			if (empty($corp_id)) {
				throw new InvalidDataException('参数不正确！');
			}

			if (empty($tagRules)) {
				throw new InvalidDataException('请添加标签规则！');
			}

			//检查是否有重复标签
			$tagIdArr = [];
			$tagWhere = '';
			foreach ($tagRules as $rule) {
				if (empty($rule['tags'])) {
					throw new InvalidDataException('请检查标签！');
				}
				foreach ($rule['tags'] as $tag_id) {
					if (in_array($tag_id, $tagIdArr)) {
						throw new InvalidDataException('有重复标签，请检查！');
					} else {
						array_push($tagIdArr, $tag_id);
						$tagWhere .= " find_in_set ($tag_id,tags_id) or";
					}
				}
			}
			$tagWhere = trim($tagWhere, ' or');
			if (!empty($tagWhere)) {
				$ruleInfo = static::find()->where(['corp_id' => $corp_id, 'status' => [1, 2]])->andWhere($tagWhere)->one();
				if (!empty($ruleInfo)) {
					throw new InvalidDataException('有标签已经添加过，请检查！');
				}
			}

			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				foreach ($tagRules as $rule) {
					$words = $rule['words'];
					$tags  = $rule['tags'];
					if (empty($words) || empty($tags)) {
						throw new InvalidDataException('请添加标签规则');
					}
					$words = preg_replace("/\s(?=\s)/", "\\1", $words);
					$words = trim($words);
					if (empty($words)) {
						throw new InvalidDataException('关键词不能为空');
					}
					$keyword               = explode(" ", $words);
					$keyword               = array_unique($keyword);
					$keyWordRule           = new WorkTagKeywordRule();
					$keyWordRule->corp_id  = $corp_id;
					$keyWordRule->tags_id  = implode(',', $tags);
					$keyWordRule->keyword  = json_encode($keyword, JSON_UNESCAPED_UNICODE);
					$keyWordRule->add_time = DateUtil::getCurrentTime();
					if (!$keyWordRule->validate() || !$keyWordRule->save()) {
						throw new InvalidDataException(SUtils::modelError($keyWordRule));
					}
				}
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		//修改数据
		public static function updateData ($data)
		{
			$id    = !empty($data['id']) ? $data['id'] : 0;
			$tags  = !empty($data['tags']) ? $data['tags'] : [];
			$words = !empty($data['words']) ? $data['words'] : '';
			if (empty($id) || empty($tags) || empty($words)) {
				throw new InvalidDataException('参数不正确！');
			}
			//标签
			$workTag = WorkTag::find()->where(['id' => $tags])->all();
			if (empty($workTag)) {
				throw new InvalidDataException('请选择可用标签');
			}
			$tagIds = array_column($workTag, 'id');

			//关键词
			$words = preg_replace("/\s(?=\s)/", "\\1", $words);
			$words = trim($words);
			if (empty($words)) {
				throw new InvalidDataException('关键词不能为空');
			}

			$keyword     = explode(" ", $words);
			$keyword     = array_unique($keyword);
			$keyWordRule = static::findOne($id);
			if (empty($keyWordRule)) {
				throw new InvalidDataException('参数不正确！');
			}

			$keyWordRule->tags_id = implode(',', $tagIds);
			$keyWordRule->keyword = json_encode($keyword, JSON_UNESCAPED_UNICODE);
			if (!$keyWordRule->validate() || !$keyWordRule->save()) {
				throw new InvalidDataException(SUtils::modelError($keyWordRule));
			}
		}

		//根据标签状态修改规则状态
		public static function updateStatus ($keyWordRule)
		{
			/**@var WorkTagKeywordRule $keyWordRule * */
			if (!empty($keyWordRule->tags_id)) {
				$workTag = WorkTag::find()->where(['corp_id' => $keyWordRule->corp_id, 'is_del' => 0])->andWhere('id in(' . $keyWordRule->tags_id . ')')->all();
				if (empty($workTag)) {
					$keyWordRule->tags_id = '';
					$keyWordRule->update();
				}
			}
		}
	}
