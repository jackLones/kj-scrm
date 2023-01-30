<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_material}}".
	 *
	 * @property int           $id
	 * @property int           $corp_id        授权的企业ID
	 * @property string        $media_id       新增素材的media_id
	 * @property string        $expire         临时素材失效时间
	 * @property int           $type           素材有效期类型：0、临时素材；1、永久素材
	 * @property int           $material_type  素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、文件（file)、6：文本（text）、7：小程序（miniprogram）
	 * @property text          $content        对于文本类型，content是文本内容，对于图文类型，content是图文描述，，对于小程序类型，content是图片的pic_media_id
	 * @property string        $file_name      素材名称
	 * @property string        $media_width    素材宽度
	 * @property string        $media_height   素材高度
	 * @property string        $media_duration 素材时长秒
	 * @property int           $file_length    素材大小
	 * @property string        $content_type   素材类型
	 * @property string        $appId          小程序appid
	 * @property string        $appPath        小程序page路径
	 * @property string        $local_path     素材本地地址
	 * @property string        $yun_url        素材云端地址
	 * @property string        $wx_url         素材微信地址
	 * @property string        $jump_url       图文的跳转地址
	 * @property int           $created_at     媒体文件上传时间戳
	 * @property int           $status         1可用 0不可用
	 * @property string        $update_time    修改时间
	 * @property string        $create_time    创建时间
	 * @property string        $attachment_id  附件id
	 *
	 * @property WorkArticle[] $workArticles
	 * @property WorkCorp      $corp
	 */
	class WorkMaterial extends \yii\db\ActiveRecord
	{
		const SHORT_TIME_MATERIAL = 0;
		const FOREVER_MATERIAL = 1;

		const IMG_MATERIAL = 2;
		const VOICE_MATERIAL = 3;
		const VIDEO_MATERIAL = 4;
		const FILE_MATERIAL = 5;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_material}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'type', 'material_type', 'file_length', 'created_at', 'status'], 'integer'],
				[['local_path', 'yun_url', 'wx_url', 'jump_url'], 'string'],
				[['update_time', 'create_time'], 'safe'],
				[['media_id', 'file_name'], 'string', 'max' => 128],
				[['expire', 'content_type'], 'string', 'max' => 128],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'corp_id'        => Yii::t('app', '授权的企业ID'),
				'media_id'       => Yii::t('app', '新增素材的media_id'),
				'expire'         => Yii::t('app', '临时素材失效时间'),
				'type'           => Yii::t('app', '素材有效期类型：0、临时素材；1、永久素材'),
				'material_type'  => Yii::t('app', '素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、文件（file)、6：文本（text）、7：小程序（miniprogram）'),
				'content'        => Yii::t('app', '对于文本类型，content是文本内容，对于图文类型，content是图文描述，，对于小程序类型，content是图片的pic_media_id'),
				'file_name'      => Yii::t('app', '素材名称'),
				'media_width'    => Yii::t('app', '素材宽度'),
				'media_height'   => Yii::t('app', '素材高度'),
				'media_duration' => Yii::t('app', '素材时长秒'),
				'file_length'    => Yii::t('app', '素材大小'),
				'content_type'   => Yii::t('app', '素材类型'),
				'appId'          => Yii::t('app', '小程序appid'),
				'appPath'        => Yii::t('app', '小程序page路径'),
				'local_path'     => Yii::t('app', '素材本地地址'),
				'yun_url'        => Yii::t('app', '素材云端地址'),
				'wx_url'         => Yii::t('app', '素材微信地址'),
				'jump_url'       => Yii::t('app', '图文的跳转地址'),
				'created_at'     => Yii::t('app', '媒体文件上传时间戳'),
				'status'         => Yii::t('app', '1可用 0不可用'),
				'update_time'    => Yii::t('app', '修改时间'),
				'create_time'    => Yii::t('app', '创建时间'),
				'attachment_id'  => Yii::t('app', '附件id'),
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

		//单独图片上传到微信素材库
		public static function getWorkMaterialByPic ($corp_id, $local_path)
		{
			$workMaterial = WorkMaterial::findOne(['corp_id' => $corp_id, 'local_path' => $local_path]);
			if (!empty($workMaterial)) {
				return ['id' => $workMaterial->id];
			}
			$transaction = \Yii::$app->db->beginTransaction();
			try {
				//上传临时素材
				$serviceWork = WorkUtils::getWorkApi($corp_id);
				//上传企业微信素材
				$filePath  = \Yii::getAlias('@app') . $local_path;
				$imgData   = getimagesize($filePath);
				$imgArr    = explode('/', $local_path);
				$file_name = $imgArr[count($imgArr) - 1];
				$media_id  = $serviceWork->MediaUpload($filePath, 'image');
				$time      = time();
				//添加
				$workMaterial = new WorkMaterial();

				$workMaterial->corp_id        = $corp_id;
				$workMaterial->media_id       = $media_id;
				$expire                       = $time + 259200;//三天后时间戳
				$workMaterial->expire         = (string) $expire;
				$workMaterial->type           = 0;
				$workMaterial->material_type  = 2;
				$workMaterial->file_name      = $file_name;
				$workMaterial->file_length    = filesize($filePath);
				$workMaterial->content_type   = $imgData['mime'];
				$workMaterial->local_path     = $local_path;
				$workMaterial->media_width    = $imgData[0];
				$workMaterial->media_height   = $imgData[1];
				$workMaterial->media_duration = '';
				$workMaterial->created_at     = $time;
				$workMaterial->create_time    = DateUtil::getCurrentTime();
				if (!$workMaterial->validate() || !$workMaterial->save()) {
					throw new InvalidDataException(SUtils::modelError($workMaterial));
				}

				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return ['id' => $workMaterial->id];
		}

		//根据附件来上传微信素材库
		public static function uploadMedia ($attachment_id, $corp_id)
		{
			$attachment = Attachment::findOne($attachment_id);
			if (empty($attachment)) {
				throw new InvalidDataException('无此附件，请检查！');
			}
			$appPath           = \Yii::getAlias('@app');
			$filePath          = $appPath . $attachment->local_path;
			$file_content_type = $attachment->file_content_type;
			$local_path        = $attachment->local_path;
			if ($attachment->file_type == 1) {//图片
				$material_type = 2;
				$fileType      = 'image';
			} elseif ($attachment->file_type == 2) {//音频
				if ($attachment->file_duration > '00:01:00') {
					throw new InvalidDataException('音频的播放长度不能超过60s');
				}
				$material_type = 3;
				$fileType      = 'voice';
			} elseif ($attachment->file_type == 3) {//视频
				$material_type = 4;
				$fileType      = 'video';
			} elseif ($attachment->file_type == 5) {//文件
				$material_type = 5;
				$fileType      = 'file';
			}
			$transaction = \Yii::$app->db->beginTransaction();
			try {
				//上传临时素材
				$serviceWork = WorkUtils::getWorkApi($corp_id);

				//上传企业微信素材
				$media_id = $serviceWork->MediaUpload($filePath, $fileType, ['file_name' => $attachment->file_name]);
				$time     = time();
				//添加
				$workMaterial                 = new WorkMaterial();
				$workMaterial->corp_id        = $corp_id;
				$workMaterial->media_id       = $media_id;
				$expire                       = $time + 259200;//三天后时间戳
				$workMaterial->expire         = (string) $expire;
				$workMaterial->type           = 0;
				$workMaterial->material_type  = $material_type;
				$workMaterial->file_name      = $attachment->file_name;
				$workMaterial->file_length    = $attachment->file_length;
				$workMaterial->content_type   = $file_content_type;
				$workMaterial->local_path     = $local_path;
				$workMaterial->media_width    = $attachment->file_width;
				$workMaterial->media_height   = $attachment->file_height;
				$workMaterial->media_duration = $attachment->file_duration;
				$workMaterial->created_at     = $time;
				$workMaterial->create_time    = DateUtil::getCurrentTime();
				$workMaterial->attachment_id  = $attachment_id;
				if (!$workMaterial->validate() || !$workMaterial->save()) {
					throw new InvalidDataException(SUtils::modelError($workMaterial));
				}

				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return ['id' => $attachment->id, 'file_name' => $attachment->file_name, 'local_path' => $attachment->local_path, 'media_id' => $media_id];
		}

	}
