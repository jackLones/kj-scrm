<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%limit_word}}".
	 *
	 * @property int    $id
	 * @property int    $uid          账户id
	 * @property int    $group_id     分组id
	 * @property string $title        名称
	 * @property int    $status       0不可用，1可用
	 * @property string $add_time     创建时间
	 * @property string $update_time  修改时间
	 *
	 * @property User   $u
	 */
	class LimitWord extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%limit_word}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'status'], 'integer'],
				[['title'], 'string', 'max' => 255],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'uid'         => Yii::t('app', '账户id'),
				'group_id'    => Yii::t('app', '分组id'),
				'title'       => Yii::t('app', '名称'),
				'status'      => Yii::t('app', '状态：0不可用，1可用'),
				'update_time' => Yii::t('app', '修改时间'),
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
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/*
		 * 获取敏感词列表
		 *
		 * $uid 账户id
		 * $ids 敏感词id
		 * $is_close 是否显示已关闭的敏感词
		 */
		public static function getList ($uid = '', $ids = [], $is_close = 0)
		{
			$limitWord = static::find();
			if (!empty($is_close)) {
				$limitWord = $limitWord->where(['status' => [1, 2]]);
			} else {
				$limitWord = $limitWord->where(['status' => 1]);
			}
			if (!empty($ids)) {
				$limitWord = $limitWord->andWhere(['id' => $ids]);
			} elseif (!empty($uid)) {
				$limitWord = $limitWord->andWhere(['uid' => $uid]);
			} else {
				return [];
			}
			$limitWord = $limitWord->all();

			return $limitWord;
		}

		//设置敏感词
		public static function setName ($data)
		{
			$id       = !empty($data['id']) ? $data['id'] : 0;
			$uid      = !empty($data['uid']) ? $data['uid'] : NULL;
			$titleArr = !empty($data['title']) ? $data['title'] : [];
			$group_id = !empty($data['group_id']) ? $data['group_id'] : 0;

			if (empty($titleArr)) {
				throw new InvalidDataException('请填写敏感词');
			}
			if (!is_array($titleArr)) {
				$titleArr = [$titleArr];
			}
			if (empty($group_id)) {
				throw new InvalidDataException('请选择分组');
			}

			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				foreach ($titleArr as $title) {
					if (empty($title)) {
						throw new InvalidDataException('请填写敏感词');
					} elseif (mb_strlen($title, 'utf-8') > 6) {
						throw new InvalidDataException('敏感词不能超过6个字符');
					}
					$wordInfo = static::find()->where(['title' => $title, 'status' => [1, 2]]);
					if (empty($uid)) {
						$wordInfo = $wordInfo->andWhere(['uid' => NULL]);
					} else {
						$wordInfo = $wordInfo->andWhere(['or', ['uid' => NULL], ['uid' => $uid]]);
					}
					if (!empty($id)) {
						$wordInfo  = $wordInfo->andWhere(['<>', 'id', $id]);
						$limitWord = static::findOne($id);
						if (empty($limitWord->uid) && !empty($uid)) {
							throw new InvalidDataException('系统敏感词不能修改');
						}
					} else {
						$limitWord           = new LimitWord();
						$limitWord->uid      = $uid;
						$limitWord->add_time = DateUtil::getCurrentTime();
					}

					//查询标识是否重复
					$wordInfo = $wordInfo->one();
					if (!empty($wordInfo)) {
						throw new InvalidDataException('敏感词已经存在，请更换');
					}

					$limitWord->title    = $title;
					$limitWord->group_id = $group_id;
					if (!$limitWord->validate() || !$limitWord->save()) {
						throw new InvalidDataException(SUtils::modelError($limitWord));
					}
				}
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		//查询是否有敏感词
		public static function checkWord ($data)
		{
			$uid       = isset($data['uid']) ? $data['uid'] : '';
			$content   = isset($data['content']) ? $data['content'] : '';
			$ids       = isset($data['ids']) ? $data['ids'] : [];
			$is_system = isset($data['is_system']) ? $data['is_system'] : 0;
			$wordList  = static::getList($uid, $ids);
			$idData    = [];
			$titleData = [];
			/**@var LimitWord $word * */
			foreach ($wordList as $word) {
				if (strpos($content, $word->title) !== false) {
					array_push($idData, $word->id);
					array_push($titleData, $word->title);
				}
			}

			return ['idData' => $idData, 'titleData' => $titleData];
		}
	}
