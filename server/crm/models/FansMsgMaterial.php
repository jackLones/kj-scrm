<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\MaterialDownload;
	use app\util\SUtils;
	use callmez\wechat\sdk\Wechat;
	use Yii;

	/**
	 * This is the model class for table "{{%fans_msg_material}}".
	 *
	 * @property int         $id
	 * @property int         $author_id       公众号ID
	 * @property int         $fans_id         粉丝ID
	 * @property int         $msg_id          粉丝消息ID
	 * @property string      $media_id        粉丝发送的media_id
	 * @property int         $material_type   素材类型：2、图片（image）；3、语音（voice）；4、视频（video）；6：音乐素材（music）
	 * @property string      $file_name       素材名称
	 * @property string      $media_width     素材宽度
	 * @property string      $media_height    素材高度
	 * @property string      $media_duration  多媒体素材时长
	 * @property string      $file_length     素材大小
	 * @property string      $content_type    素材类型
	 * @property string      $local_path      素材本地地址
	 * @property string      $yun_url         素材云端地址
	 * @property string      $wx_url          素材微信地址
	 * @property string      $create_time     创建时间
	 *
	 * @property WxAuthorize $author
	 * @property Fans        $fans
	 * @property FansMsg     $msg
	 */
	class FansMsgMaterial extends \yii\db\ActiveRecord
	{
		const IMAGE_TYPE = 2;
		const VOICE_TYPE = 3;
		const VIDEO_TYPE = 4;
		const SHORT_VIDEO_TYPE = 5;
		const MUSIC_TYPE = 6;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans_msg_material}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'fans_id', 'msg_id', 'material_type'], 'integer'],
				[['media_id', 'material_type'], 'required'],
				[['local_path', 'yun_url', 'wx_url'], 'string'],
				[['create_time'], 'safe'],
				[['media_id'], 'string', 'max' => 64],
				[['file_name'], 'string', 'max' => 32],
				[['media_duration', 'content_type'], 'string', 'max' => 16],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
				[['msg_id'], 'exist', 'skipOnError' => true, 'targetClass' => FansMsg::className(), 'targetAttribute' => ['msg_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'author_id'      => Yii::t('app', '公众号ID'),
				'fans_id'        => Yii::t('app', '粉丝ID'),
				'msg_id'         => Yii::t('app', '粉丝消息ID'),
				'media_id'       => Yii::t('app', '粉丝发送的media_id'),
				'material_type'  => Yii::t('app', '素材类型：2、图片（image）；3、语音（voice）；4、视频（video）；5、小视频（short video）；6：音乐素材（music）'),
				'file_name'      => Yii::t('app', '素材名称'),
				'media_width'    => Yii::t('app', '素材宽度'),
				'media_height'   => Yii::t('app', '素材高度'),
				'media_duration' => Yii::t('app', '多媒体素材时长'),
				'file_length'    => Yii::t('app', '素材大小'),
				'content_type'   => Yii::t('app', '素材类型'),
				'local_path'     => Yii::t('app', '素材本地地址'),
				'yun_url'        => Yii::t('app', '素材云端地址'),
				'wx_url'         => Yii::t('app', '素材微信地址'),
				'create_time'    => Yii::t('app', '创建时间'),
			];
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getMsg ()
		{
			return $this->hasOne(FansMsg::className(), ['id' => 'msg_id']);
		}

		private function getDurationData ()
		{
			$durationData = [];
			if (!empty($this->media_duration)) {
				$durationData = explode(":", $this->media_duration);
				if (!empty($durationData) && count($durationData) == 3) {
					$tempData = explode('.', $durationData[2]);

					if (!empty($tempData) && count($tempData) == 2) {
						$durationData[2] = $tempData[0];
						$durationData[3] = $tempData[1];
					}
				}
			}

			return $durationData;
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			$data = [
				'media_id'       => $this->media_id,
				'material_type'  => $this->material_type,
				'file_name'      => $this->file_name,
				'media_width'    => $this->media_width,
				'media_height'   => $this->media_height,
				'media_duration' => $this->getDurationData(),
				'file_length'    => $this->file_length,
				'content_type'   => $this->content_type,
				'local_path'     => $this->local_path,
				'local_url'      => Yii::$app->params['site_url'] . $this->local_path,
				'yun_url'        => $this->yun_url,
				'wx_url'         => $this->wx_url,
				'create_time'    => $this->create_time,
			];

			return $data;
		}

		/**
		 * @return array
		 */
		public function dumpMiniData ()
		{
			$data = [
				'file_name'      => $this->file_name,
				'file_length'    => $this->file_length,
				'media_width'    => $this->media_width,
				'media_height'   => $this->media_height,
				'media_duration' => $this->getDurationData(),
				'content_type'   => $this->content_type,
				'local_path'     => $this->local_path,
				'local_url'      => Yii::$app->params['site_url'] . $this->local_path,
				'yun_url'        => $this->yun_url,
				'wx_url'         => $this->wx_url,
			];

			return $data;
		}

		/**
		 * 保存粉丝消息素材
		 *
		 * @param       $authorId
		 * @param       $fansId
		 * @param       $msgId
		 * @param array $mediaInfo
		 * @param int   $mediaType
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \Exception
		 */
		public static function create ($authorId, $fansId, $msgId, $mediaInfo, $mediaType = self::IMAGE_TYPE)
		{
			$fansMsgMaterialId = 0;

			$mediaId = $mediaInfo['media_id'];

			$fansMsgMaterialInfo = static::findOne(['media_id' => $mediaId]);

			$wxAuthor = WxAuthorize::findOne(['author_id' => $authorId]);

			if (!empty($wxAuthor)) {
				$appid = $wxAuthor->authorizer_appid;

				$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

				if (empty($fansMsgMaterialInfo)) {
					$fansMsgMaterialInfo            = new FansMsgMaterial();
					$fansMsgMaterialInfo->author_id = $authorId;
					$fansMsgMaterialInfo->fans_id   = $fansId;
					$fansMsgMaterialInfo->msg_id    = $msgId;
					$fansMsgMaterialInfo->media_id  = $mediaId;

					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					$downloadUrl = $wechat::WECHAT_BASE_URL . $wechat::WECHAT_MEDIA_URL . 'access_token=' . $wechat->getAccessToken() . '&media_id=' . $mediaId;

					switch ($mediaType) {
						case static::IMAGE_TYPE:
							$materialData = MaterialDownload::download($downloadUrl);
							if ($materialData['error'] == 0) {
								$fansMsgMaterialInfo->material_type = static::IMAGE_TYPE;
								$fansMsgMaterialInfo->file_name     = $materialData['file_name'];
								$fansMsgMaterialInfo->file_length   = $materialData['file_length'];
								$fansMsgMaterialInfo->media_width   = $materialData['local_data']['width'];
								$fansMsgMaterialInfo->media_height  = $materialData['local_data']['height'];
								$fansMsgMaterialInfo->content_type  = $materialData['local_data']['mime'];
								$fansMsgMaterialInfo->local_path    = $materialData['local_path'];
								$fansMsgMaterialInfo->wx_url        = $mediaInfo['wx_url'];
							}

							break;
						case static::VOICE_TYPE:
							$materialData = MaterialDownload::download($downloadUrl, MaterialDownload::VOICE_TYPE, MaterialDownload::VOICE_PATH);
							if ($materialData['error'] == 0) {
								$fansMsgMaterialInfo->material_type  = static::VOICE_TYPE;
								$fansMsgMaterialInfo->file_name      = $materialData['file_name'];
								$fansMsgMaterialInfo->file_length    = $materialData['file_length'];
								$fansMsgMaterialInfo->media_duration = $materialData['local_data']['media_duration'];
								$fansMsgMaterialInfo->content_type   = $materialData['local_data']['content_type'];
								$fansMsgMaterialInfo->local_path     = $materialData['local_path'];
							}

							break;
						case static::VIDEO_TYPE:
							$materialData = MaterialDownload::download($downloadUrl, MaterialDownload::VIDEO_TYPE, MaterialDownload::VIDEO_PATH);
							if ($materialData['error'] == 0) {
								$fansMsgMaterialInfo->material_type  = static::VIDEO_TYPE;
								$fansMsgMaterialInfo->file_name      = $materialData['file_name'];
								$fansMsgMaterialInfo->file_length    = $materialData['file_length'];
								$fansMsgMaterialInfo->media_width    = $materialData['local_data']['width'];
								$fansMsgMaterialInfo->media_height   = $materialData['local_data']['height'];
								$fansMsgMaterialInfo->media_duration = $materialData['local_data']['media_duration'];
								$fansMsgMaterialInfo->content_type   = $materialData['local_data']['content_type'];
								$fansMsgMaterialInfo->local_path     = $materialData['local_path'];
							}

							break;
						case static::SHORT_VIDEO_TYPE:
							$materialData = MaterialDownload::download($downloadUrl, MaterialDownload::SHORT_VIDEO_TYPE, MaterialDownload::SHORT_VIDEO_PATH);
							if ($materialData['error'] == 0) {
								$fansMsgMaterialInfo->material_type  = static::VIDEO_TYPE;
								$fansMsgMaterialInfo->file_name      = $materialData['file_name'];
								$fansMsgMaterialInfo->file_length    = $materialData['file_length'];
								$fansMsgMaterialInfo->media_width    = $materialData['local_data']['width'];
								$fansMsgMaterialInfo->media_height   = $materialData['local_data']['height'];
								$fansMsgMaterialInfo->media_duration = $materialData['local_data']['media_duration'];
								$fansMsgMaterialInfo->content_type   = $materialData['local_data']['content_type'];
								$fansMsgMaterialInfo->local_path     = $materialData['local_path'];
							}

							break;
						case static::MUSIC_TYPE:
						default:
							break;
					}

					if ($fansMsgMaterialInfo->validate() && $fansMsgMaterialInfo->save()) {
						$fansMsgMaterialId = $fansMsgMaterialInfo->id;
					} else {
						throw new InvalidDataException(SUtils::modelError($fansMsgMaterialInfo));
					}
				} else {
					$fansMsgMaterialId = $fansMsgMaterialInfo->id;
				}
			}

			return $fansMsgMaterialId;
		}
	}
