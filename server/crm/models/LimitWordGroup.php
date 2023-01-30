<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%limit_word_group}}".
	 *
	 * @property int    $id
	 * @property string $uid              账户id
	 * @property string $title            分组名称
	 * @property int    $status           状态：1可用 0不可用
	 * @property int    $is_not_group     0已分组、1未分组
	 * @property string $update_time      修改时间
	 * @property string $add_time         创建时间
	 */
	class LimitWordGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%limit_word_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['status', 'is_not_group'], 'integer'],
				[['title'], 'string', 'max' => 32],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'uid'          => Yii::t('app', '账户id'),
				'title'        => Yii::t('app', '名称'),
				'is_not_group' => Yii::t('app', 'ID'),
				'status'       => Yii::t('app', '状态：0不可用，1可用'),
				'update_time'  => Yii::t('app', '修改时间'),
				'add_time'     => Yii::t('app', '创建时间'),
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

		//未分组
		public static function defaultGroup ()
		{
			$groupInfo = static::findOne(['is_not_group' => 1]);
			if (empty($groupInfo)) {
				$groupInfo               = new self();
				$groupInfo->title        = '未分组';
				$groupInfo->is_not_group = 1;
				$groupInfo->add_time     = DateUtil::getCurrentTime();
				if (!$groupInfo->validate() || !$groupInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($groupInfo));
				}
			}

			return $groupInfo->id;
		}

		//获取敏感词列表
		public static function getList ($uid = '', $ids = [])
		{
			$limitGroup = static::find()->where(['status' => 1]);
			if (!empty($uid)) {
				$limitGroup = $limitGroup->andWhere(['or', ['uid' => NULL], ['uid' => $uid]]);
			} else {
				$limitGroup = $limitGroup->andWhere(['uid' => NULL]);
			}
			if (!empty($ids)) {
				$limitGroup = $limitGroup->andWhere(['id' => $ids]);
			}
			$limitGroup = $limitGroup->all();

			return $limitGroup;
		}

		//设置分组
		public static function setGroup ($data)
		{
			$id    = !empty($data['id']) ? $data['id'] : 0;
			$title = !empty($data['title']) ? $data['title'] : '';
			$uid   = !empty($data['uid']) ? $data['uid'] : NULL;
			if (empty($title)) {
				throw new InvalidDataException('请填写分组名称');
			} elseif (mb_strlen($title, 'utf-8') > 15) {
				throw new InvalidDataException('分组名称不能超过15个字符');
			}
			$groupInfo = static::find()->where(['title' => $title, 'status' => 1]);
			if (empty($uid)) {
				$groupInfo = $groupInfo->andWhere(['uid' => NULL]);
			} else {
				$groupInfo = $groupInfo->andWhere(['or', ['uid' => NULL], ['uid' => $uid]]);
			}
			if (!empty($id)) {
				$groupInfo = $groupInfo->andWhere(['<>', 'id', $id]);
				$group     = static::findOne($id);
			} else {
				$group           = new LimitWordGroup();
				$group->add_time = DateUtil::getCurrentTime();
			}
			//查询标识是否重复
			$groupInfo = $groupInfo->one();
			if (!empty($groupInfo)) {
				throw new InvalidDataException('分组名已经存在，请更换');
			}
			$group->uid   = $uid;
			$group->title = $title;
			if (!$group->validate() || !$group->save()) {
				throw new InvalidDataException(SUtils::modelError($group));
			}
		}

		/*
		 * 获取分组敏感词数据
		 *
		 * $uid 账户id
		 * $is_close 是否显示已关闭的敏感词
		 */
		public static function groupWordData ($uid, $is_close = 0)
		{
			if (empty($uid)) {
				return [];
			}
			$groupData  = [];
			$limitGroup = static::find()->where(['status' => 1]);
			$wordList   = LimitWord::find()->where(['uid' => $uid]);
			if (!empty($is_close)) {
				$wordList = $wordList->andWhere(['status' => [1, 2]]);
			} else {
				$wordList = $wordList->andWhere(['status' => 1]);
			}
			$limitGroup = $limitGroup->andWhere(['or', ['uid' => NULL], ['uid' => $uid]]);
			$limitGroup = $limitGroup->all();
			$wordList   = $wordList->all();
			/**@var LimitWordGroup $group * */
			foreach ($limitGroup as $group) {
				$is_forbid             = !empty($group->uid) ? 0 : 1;
				$group_id              = (string) $group->id;
				$groupData[$group->id] = ['key' => 'group_' . $group_id, 'id' => $group_id, 'title' => $group->title, 'is_forbid' => $is_forbid, 'children' => []];
			}
			/**@var LimitWord $word * */
			foreach ($wordList as $word) {
				if (!empty($word->group_id)) {
					$word_id = (string) $word->id;
					if ($word->status == 2) {
						$title = $word->title . '（已关闭）';
					} else {
						$title = $word->title;
					}
					$groupData[$word->group_id]['children'][] = ['key' => 'word_' . $word_id, 'id' => $word_id, 'title' => $title, 'status' => $word->status];
				}
			}

			return array_values($groupData);
		}

	}
