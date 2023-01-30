<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%mini_user}}".
	 *
	 * @property int               $id
	 * @property int               $author_id 小程序ID
	 * @property string            $openid    用户的标识，对当前小程序唯一
	 * @property string            $remark    小程序用户备注
	 * @property string            $unionid   只有在用户将小程序绑定到微信开放平台帐号后，才会出现该字段。
	 * @property int               $fans_id   绑定的公众号的粉丝ID
	 * @property string            $last_time 最后活跃时间
	 * @property string            $create_time
	 *
	 * @property MiniMsg[]         $miniMsgs
	 * @property MiniMsgMaterial[] $miniMsgMaterials
	 * @property WxAuthorize       $author
	 * @property Fans              $fans
	 */
	class MiniUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%mini_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id'], 'required'],
				[['author_id', 'fans_id'], 'integer'],
				[['create_time'], 'safe'],
				[['openid', 'unionid'], 'string', 'max' => 80],
				[['remark'], 'string', 'max' => 255],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'author_id'   => Yii::t('app', '小程序ID'),
				'openid'      => Yii::t('app', '用户的标识，对当前小程序唯一'),
				'remark'      => Yii::t('app', '小程序用户备注'),
				'unionid'     => Yii::t('app', '只有在用户将小程序绑定到微信开放平台帐号后，才会出现该字段。'),
				'fans_id'     => Yii::t('app', '绑定的公众号的粉丝ID'),
				'last_time'   => Yii::t('app', '最后活跃时间'),
				'create_time' => Yii::t('app', 'Create Time'),
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
		public function getMiniMsgs ()
		{
			return $this->hasMany(MiniMsg::className(), ['mini_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMiniMsgMaterials ()
		{
			return $this->hasMany(MiniMsgMaterial::className(), ['mini_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}

		/**
		 * @param bool $withFansInfo
		 *
		 * @return array
		 */
		public function dumpData ($withFansInfo = false)
		{
			$data = [
				'mini_id'   => $this->id,
				'openid'    => $this->openid,
				'remark'    => $this->remark,
				'head_img'  => !empty($this->fans) ? $this->fans->headerimg : SUtils::makeGravatar($this->openid),
				'unionid'   => $this->unionid,
				'last_time' => $this->last_time,
			];

			if ($withFansInfo) {
				$data['fans_info'] = $this->fans->dumpData();
			}

			return $data;
		}

		/**
		 * @return array
		 */
		public function dumpMinData ()
		{
			return [
				'mini_id'  => $this->id,
				'openid'   => $this->openid,
				'remark'   => $this->remark,
				'head_img' => !empty($this->fans) ? $this->fans->headerimg : SUtils::makeGravatar($this->openid),
			];
		}

		/**
		 * @param        $authorId
		 * @param        $openid
		 * @param string $unionid
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($authorId, $openid, $unionid = '')
		{
			$miniId = 0;
			if (!empty($authorId) && !empty($openid)) {
				$miniAuthor = WxAuthorize::findOne($authorId);
				if (empty($miniAuthor)) {
					throw new InvalidDataException('参数不正确');
				}

				$miniInfo = static::findOne(['author_id' => $authorId, 'openid' => $openid]);

				if (empty($miniInfo)) {
					$miniInfo              = new MiniUser();
					$miniInfo->author_id   = $authorId;
					$miniInfo->openid      = $openid;
					$miniInfo->remark      = '';
					$miniInfo->create_time = DateUtil::getCurrentTime();

					if (!empty($unionid)) {
						$miniInfo->unionid = $unionid;

						$fansInfo = Fans::findOne(['unionid' => $unionid]);

						if (!empty($fansInfo) && $fansInfo->author->userAuthorRelations[0]->uid == $miniAuthor->userAuthorRelations[0]->uid) {
							$miniInfo->fans_id = $fansInfo->id;
						}
					}

					if (!$miniInfo->validate() || !$miniInfo->save()) {
						throw new InvalidDataException(SUtils::modelError($miniInfo));
					}
				}

				$miniId = $miniInfo->id;
			}

			return $miniId;
		}

		public static function modifyFansRemark ($miniId, $remark)
		{
			if (!empty($miniId)) {
				$miniInfo = static::findOne(['id' => $miniId]);
				if (!empty($miniInfo)) {
					$miniInfo->remark = $remark;
					if (!$miniInfo->validate() || !$miniInfo->save()) {
						throw new InvalidDataException(SUtils::modelError($miniInfo));
					}
				}
			}

			return true;
		}

		/**
		 * 获取活跃的小程序列表（48小时内有互动）
		 *
		 * @param $authId
		 *
		 * @return array
		 */
		public static function getActiveUsers ($authId)
		{
			$fansList = [];

			$msgPreTime = 48 * 60 * 60;

			$miniData = static::find()->alias('mini');
			$miniData = $miniData->select(['mini.*', 'max(miniM.create_time) as msg_time']);
			$miniData = $miniData->rightJoin('{{%mini_msg}} miniM', '`miniM`.`mini_id` = `mini`.`id`');
			$miniData = $miniData->where(['mini.author_id' => $authId]);
			$miniData = $miniData->andWhere(['>=', 'miniM.create_time', DateUtil::getPreviousSecondsTime($msgPreTime)]);
			$miniData = $miniData->groupBy('mini.id')->orderBy(['msg_time' => SORT_DESC])->all();

			if (!empty($miniData)) {
				/** @var MiniUser $miniInfo */
				foreach ($miniData as $miniInfo) {
					$miniListInfo                 = $miniInfo->dumpData();
					$miniListInfo['last_content'] = MiniMsg::getMsgList($miniInfo->id, 0, 1, true);
					array_push($fansList, $miniListInfo);
				}
			}

			return $fansList;
		}
	}
