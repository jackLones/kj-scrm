<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_moment_media}}".
	 *
	 * @property int         $id
	 * @property int         $moment_id    朋友圈ID
	 * @property int         $att          内容引擎id
	 * @property int         $sort         排序
	 * @property string      $local_path   媒体本地位置
	 * @property string      $s_local_path 缩略图地址
	 * @property string      $create_time  创建时间
	 *
	 * @property WorkMoments $moment
	 * @property Attachment  $att0
	 */
	class WorkMomentMedia extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moment_media}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['moment_id', 'att', 'sort'], 'integer'],
				[['local_path', 's_local_path'], 'string'],
				[['create_time'], 'safe'],
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
				'id'           => Yii::t('app', 'ID'),
				'moment_id'    => Yii::t('app', '朋友圈ID'),
				'att'          => Yii::t('app', '内容引擎id'),
				'sort'         => Yii::t('app', '排序'),
				'local_path'   => Yii::t('app', '媒体本地位置'),
				's_local_path' => Yii::t('app', '缩略图地址'),
				'create_time'  => Yii::t('app', '创建时间'),
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
				'sort'        => $this->sort,
				'local_path'  => $this->local_path,
				'create_time' => $this->create_time,
			];
		}

		/**
		 * @param $id
		 * @param $info
		 */
		public static function getAttachmentInfo ($id, &$info)
		{

			$att = Attachment::findOne($id);
			if (!empty($att)) {
				$info['local_path']   = $att->local_path;
				$info['s_local_path'] = $att->s_local_path;
				$info['att']          = $att->id;
			} else {
				$info = [
					'local_path'   => isset($info['local_path']) ? $info['local_path'] : '',
					's_local_path' => isset($info['s_local_path']) ? $info['s_local_path'] : '',
					'att'          => isset($info['att']) ? $info['att'] : '',
				];
			}
		}

		/**
		 * @param $info
		 * @param $momentId
		 */
		public static function createData ($info, $momentId)
		{
			$Media = new self();
			if (isset($info["attachment"]) && !empty($info["attachment"])) {
				self::getAttachmentInfo($info["attachment"], $info);
				$Media->att = isset($info['att']) ? $info['att'] : NULL;
			}
			if(empty($info['local_path'])){
				throw new InvalidDataException("媒体资源参数不完整！");
			}
			$Media->moment_id    = $momentId;
			$Media->local_path   = isset($info['local_path']) ? $info['local_path'] : '';
			$Media->s_local_path = isset($info['s_local_path']) ? $info['s_local_path'] : NULL;
			$Media->sort         = isset($info['sort']) ? $info['sort'] : 1;
			if (!$Media->validate() || !$Media->save()) {
				Yii::error(SUtils::modelError($Media), "save_error");
				throw new InvalidDataException(SUtils::modelError($Media));
			}
		}
	}
