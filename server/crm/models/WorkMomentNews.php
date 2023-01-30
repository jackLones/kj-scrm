<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_moment_news}}".
	 *
	 * @property int         $id
	 * @property int         $moment_id   朋友圈ID
	 * @property int         $att         内容引擎id
	 * @property string      $title       图文消息标题
	 * @property string      $description 图文消息描述
	 * @property string      $url         图文消息点击跳转地址
	 * @property string      $pic_path    图文消息配图的地址
	 * @property string      $create_time 创建时间
	 *
	 * @property WorkMoments $moment
	 * @property Attachment  $att0
	 */
	class WorkMomentNews extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moment_news}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['moment_id', 'att'], 'integer'],
				[['url', 'pic_path'], 'string'],
				[['create_time'], 'safe'],
				[['title'], 'string', 'max' => 64],
				[['description'], 'string', 'max' => 255],
				[['moment_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMoments::className(), 'targetAttribute' => ['moment_id' => 'id']],
				[['att'], 'exist', 'skipOnError' => true, 'targetClass' => Attachment::className(), 'targetAttribute' => ['att' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'moment_id'   => Yii::t('app', '朋友圈ID'),
				'title'       => Yii::t('app', '图文消息标题'),
				'description' => Yii::t('app', '图文消息描述'),
				'url'         => Yii::t('app', '图文消息点击跳转地址'),
				'pic_path'    => Yii::t('app', '图文消息配图的地址'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMoment ()
		{
			return $this->hasOne(WorkMoments::className(), ['id' => 'moment_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAtt0 ()
		{
			return $this->hasOne(Attachment::className(), ['id' => 'att']);
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			return [
				'moment_id'   => $this->moment_id,
				'title'       => $this->title,
				'description' => $this->description,
				'url'         => $this->url,
				'pic_path'    => $this->pic_path,
				'create_time' => $this->create_time,
			];
		}

		/**
		 * @param int   $momentId
		 * @param array $newsData
		 *
		 * @return WorkMomentNews|null
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($momentId, $newsData)
		{
			$momentNews = self::findOne(['moment_id' => $momentId]);

			if (empty($momentNews)) {
				$momentNews->moment_id   = $momentId;
				$momentNews->create_time = DateUtil::getCurrentTime();
			}

			$momentNews->title       = $newsData['title'];
			$momentNews->description = $newsData['description'];
			$momentNews->url         = $newsData['url'];
			$momentNews->pic_path    = $newsData['pic_path'];

			if ($momentNews->dirtyAttributes) {
				if (!$momentNews->validate() || !$momentNews->save()) {
					throw new InvalidDataException(SUtils::modelError($momentNews));
				}
			}

			return $momentNews;
		}

		/**
		 * @param $id
		 * @param $info
		 *
		 */
		public static function getAttachmentInfo ($id, &$info)
		{
			$att = Attachment::findOne($id);

			if (!empty($att)) {
				$info['title']       = $att->file_name;
				$info['description'] = $att->content;
				$info['pic_url']     = empty($att->s_local_path) ? $att->local_path : $att->s_local_path;
				$info['url']         = $att->jump_url;
				$info['att']         = $att->id;
			} else {
				$info = [
					"title"       => isset($info['title']) ? $info['title'] : '',
					"description" => isset($info['description']) ? $info['description'] : '',
					"pic_url"     => isset($info['pic_url']) ? $info['pic_url'] : '',
					"att"         => isset($info['att']) ? $info['att'] : '',
				];
			}
		}

		/**
		 * @param $infos
		 * @param $momentId
		 *
		 * @throws InvalidDataException
		 */
		public static function createData ($infos, $momentId)
		{
			foreach ($infos as $info) {
				$new = new self();
				if (isset($info["attachment"]) && !empty($info["attachment"])) {
					self::getAttachmentInfo($info["attachment"], $info);
					$new->att = $info['att'];
				}
				if(empty($info['url'])){
					throw new InvalidDataException("图文参数不完整！");
				}
				$new->moment_id   = $momentId;
				$new->title       = isset($info["title"]) ? $info["title"] : '';
				$new->description = isset($info["title"]) ? mb_substr($info['description'], 0, 254) : '';
				$new->url         = isset($info['url']) ? $info['url'] : '';
				$new->pic_path    = isset($info['pic_url']) ? $info['pic_url'] : '';
				if (!$new->validate() || !$new->save()) {
					throw new InvalidDataException(SUtils::modelError($new));
				}
			}
		}
	}
