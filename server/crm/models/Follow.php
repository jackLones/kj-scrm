<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%follow}}".
	 *
	 * @property int                                  $id
	 * @property int                                  $uid         用户ID
	 * @property string                               $title       名称
	 * @property string                               $describe    阶段描述
	 * @property int                                  $status      1可用 0删除
	 * @property int                                  $sort        排序
	 * @property int                                  $is_change   是否完成待办改变跟进状态0否1是
	 * @property string                               $way         1至少完成几项2所选的多选必须全部完成可共存
	 * @property int                                  $type        进入到下一阶段类型 1所有项目完成 2非所有
	 * @property int                                  $num         至少完成几项
	 * @property string                               $project_two 所选的多选必须全部包含
	 * @property string                               $project_one 含有至少完某几项的多选
	 * @property string                               $update_time 修改时间
	 * @property string                               $create_time 添加时间
	 * @property int                                  $lose_one    是否输单0否1是
	 *
	 * @property User   $u
	 */
	class Follow extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%follow}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'status', 'sort', 'is_change', 'type', 'num', 'lose_one'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['title'], 'string', 'max' => 16],
				[['describe'], 'string', 'max' => 50],
				[['way'], 'string', 'max' => 32],
				[['project_two', 'project_one'], 'string', 'max' => 255],
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
				'uid'         => Yii::t('app', '用户ID'),
				'title'       => Yii::t('app', '名称'),
				'describe'    => Yii::t('app', '阶段描述'),
				'status'      => Yii::t('app', '1可用 0删除'),
				'sort'        => Yii::t('app', '排序'),
				'is_change'   => Yii::t('app', '是否完成待办改变跟进状态0否1是'),
				'way'         => Yii::t('app', '1至少完成几项2所选的多选必须全部完成可共存'),
				'type'        => Yii::t('app', '进入到下一阶段类型 1所有项目完成 2非所有'),
				'num'         => Yii::t('app', '至少完成几项'),
				'project_two' => Yii::t('app', '所选的多选必须全部包含'),
				'project_one' => Yii::t('app', '含有至少完某几项的多选'),
				'update_time' => Yii::t('app', '修改时间'),
				'create_time' => Yii::t('app', '添加时间'),
				'lose_one'    => Yii::t('app', '是否输单0否1是'),
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

		//添加默认跟进状态
		public static function setDefaultData ($uid)
		{
			$follow = static::findOne(['uid' => $uid]);
			if (empty($follow)) {
				$transaction = \Yii::$app->db->beginTransaction();
				try {
					//未跟进
					$follow              = new Follow();
					$follow->uid         = $uid;
					$follow->title       = '未跟进';
					$follow->create_time = DateUtil::getCurrentTime();
					if (!$follow->validate() || !$follow->save()) {
						throw new InvalidDataException(SUtils::modelError($follow));
					}
					//跟进中
					$follow              = new Follow();
					$follow->uid         = $uid;
					$follow->title       = '跟进中';
					$follow->create_time = DateUtil::getCurrentTime();
					if (!$follow->validate() || !$follow->save()) {
						throw new InvalidDataException(SUtils::modelError($follow));
					}
					//已拒绝
					$follow              = new Follow();
					$follow->uid         = $uid;
					$follow->title       = '已拒绝';
					$follow->create_time = DateUtil::getCurrentTime();
					if (!$follow->validate() || !$follow->save()) {
						throw new InvalidDataException(SUtils::modelError($follow));
					}
					//已成交
					$follow              = new Follow();
					$follow->uid         = $uid;
					$follow->title       = '已成交';
					$follow->create_time = DateUtil::getCurrentTime();
					if (!$follow->validate() || !$follow->save()) {
						throw new InvalidDataException(SUtils::modelError($follow));
					}

					$transaction->commit();
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}
			}

			return true;
		}

		//更新粉丝和客户状态
		public static function updateFollow ($type = 0)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$userList = User::find()->all();
			try {
				foreach ($userList as $user) {
					$uid = $user->uid;
					static::setDefaultData($uid);
					//未跟进
					$follow1 = Follow::findOne(['uid' => $uid, 'title' => '未跟进']);
					//跟进中
					$follow2 = Follow::findOne(['uid' => $uid, 'title' => '跟进中']);
					//已拒绝
					$follow3 = Follow::findOne(['uid' => $uid, 'title' => '已拒绝']);
					//已成交
					$follow4 = Follow::findOne(['uid' => $uid, 'title' => '已成交']);

					if ($type == 0) {//粉丝
						$authorList = UserAuthorRelation::find()->where(['uid' => $uid])->all();
						foreach ($authorList as $author) {
							$fansList = Fans::find()->where(['author_id' => $author->author_id, 'follow_id' => NULL])->select('id,author_id,subscribe,follow_status')->all();
							foreach ($fansList as $fans) {
								$follow_status = $fans->follow_status;
								if ($follow_status == 0) {
									$fans->follow_id = $follow1->id;
								} elseif ($follow_status == 1) {
									$fans->follow_id = $follow2->id;
								} elseif ($follow_status == 2) {
									$fans->follow_id = $follow3->id;
								} elseif ($follow_status == 3) {
									$fans->follow_id = $follow4->id;
								}
								$fans->update();
							}
						}
					} else {//客户
						$corpList = UserCorpRelation::find()->where(['uid' => $uid])->all();
						foreach ($corpList as $corp) {
							$contactList = WorkExternalContact::find()->where(['corp_id' => $corp->corp_id, 'follow_id' => NULL])->select('id,follow_status')->all();
							foreach ($contactList as $contact) {
								$follow_status = $contact->follow_status;
								if ($follow_status == 0) {
									$contact->follow_id = $follow1->id;
								} elseif ($follow_status == 1) {
									$contact->follow_id = $follow2->id;
								} elseif ($follow_status == 2) {
									$contact->follow_id = $follow3->id;
								} elseif ($follow_status == 3) {
									$contact->follow_id = $follow4->id;
								}
								$contact->update();
							}
						}
					}
				}
			} catch (\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}
		}

		//根据corpId获取默认跟进状态id
		public static function getFollowIdByCorpId ($corpId)
		{
			$corpInfo = UserCorpRelation::findOne(['corp_id' => $corpId]);
			if (!empty($corpInfo)) {
				$uid        = $corpInfo->uid;
				//$followInfo = static::findOne(['uid' => $uid, 'status' => 1]);
				$followInfo = static::find()->where(['uid' => $uid, 'status' => 1])->orderBy(['status' => SORT_DESC, 'sort' => SORT_ASC, 'id' => SORT_ASC])->one();
				if (!empty($followInfo)) {
					return $followInfo->id;
				}
			}

			return '';
		}

		//根据corpId获取默认跟进状态id
		public static function getFollowIdByAuthorId ($authorId)
		{
			$authorInfo = UserAuthorRelation::findOne(['author_id' => $authorId]);
			if (!empty($authorInfo)) {
				$uid        = $authorInfo->uid;
				$followInfo = static::findOne(['uid' => $uid, 'status' => 1]);
				if (!empty($followInfo)) {
					return $followInfo->id;
				}
			}

			return '';
		}

		//更新目前follow_id为空的数据
		public static function updateFollowContact ($type = 0)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			if ($type == 0) {//粉丝
				$fansList = Fans::find()->where(['follow_id' => NULL])->select('id,author_id,subscribe,follow_status')->all();
				/** @var Fans $fans */
				foreach ($fansList as $fans) {
					$follow_id = static::getFollowIdByAuthorId($fans->author_id);
					if (!empty($follow_id)) {
						$fans->follow_id = $follow_id;
						$fans->update();
					}
				}
			} else {
				$contactList = WorkExternalContact::find()->where(['follow_id' => NULL])->select('id,corp_id')->all();
				/** @var WorkExternalContact $contact */
				foreach ($contactList as $contact) {
					$follow_id = static::getFollowIdByCorpId($contact->corp_id);
					if (!empty($follow_id)) {
						$contact->follow_id = $follow_id;
						$contact->update();
					}
				}
			}
		}

		/**
		 * 近3、7、15等时间数据
		 *
		 * @param $type
		 *
		 * @return false|string
		 *
		 */
		public static function getTime ($type)
		{
			$date = 0;
			switch ($type) {
				case 1:
					$time = time() - 3 * 24 * 3600;
					break;
				case 2:
					$time = time() - 7 * 24 * 3600;
					break;
				case 3:
					$time = time() - 15 * 24 * 3600;
					break;
				case 4:
					$time = time() - 30 * 24 * 3600;
					break;
				case 5:
					$time = time() - 90 * 24 * 3600;
					break;
				case 6:
					$time = time() - 180 * 24 * 3600;
					break;
				case 7:
				case 8:
					$time = time() - 365 * 24 * 3600;
					break;
			}
			if (!empty($time)) {
				$date = $time;
			}

			return $date;
		}

		//获取账户的默认跟进状态
		public static function getFollowIdByUid ($uid)
		{
			$followInfo = Follow::find()->where(['uid' => $uid, 'status' => 1])->orderBy(['status' => SORT_DESC, 'sort' => SORT_ASC, 'id' => SORT_ASC])->one();
			if (!empty($followInfo)) {
				return $followInfo->id;
			}

			return 0;
		}
	}
