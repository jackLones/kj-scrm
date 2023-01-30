<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\MaterialDownload;
	use app\util\SUtils;
	use callmez\wechat\sdk\Wechat;
	use Yii;

	/**
	 * This is the model class for table "{{%mini_msg_material}}".
	 *
	 * @property int         $id
	 * @property int         $author_id      小程序ID
	 * @property int         $mini_id        小程序用户ID
	 * @property int         $msg_id         消息ID
	 * @property string      $media_id       用户发送的media_id
	 * @property int         $material_type  素材类型：2、图片（image）
	 * @property string      $file_name      素材名称
	 * @property string      $media_width    素材宽度
	 * @property string      $media_height   素材高度
	 * @property string      $media_duration 多媒体素材时长
	 * @property string      $file_length    素材大小
	 * @property string      $content_type   素材类型
	 * @property string      $local_path     素材本地地址
	 * @property string      $yun_url        素材云端地址
	 * @property string      $wx_url         素材微信地址
	 * @property string      $create_time    创建时间
	 *
	 * @property WxAuthorize $author
	 * @property MiniUser    $mini
	 * @property MiniMsg     $msg
	 */
	class MiniMsgMaterial extends \yii\db\ActiveRecord
	{
		const IMAGE_TYPE = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%mini_msg_material}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'mini_id', 'msg_id', 'material_type'], 'integer'],
				[['media_id', 'material_type'], 'required'],
				[['local_path', 'yun_url', 'wx_url'], 'string'],
				[['create_time'], 'safe'],
				[['media_id'], 'string', 'max' => 64],
				[['file_name'], 'string', 'max' => 32],
				[['media_duration', 'content_type'], 'string', 'max' => 16],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['mini_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiniUser::className(), 'targetAttribute' => ['mini_id' => 'id']],
				[['msg_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiniMsg::className(), 'targetAttribute' => ['msg_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'author_id'      => Yii::t('app', '小程序ID'),
				'mini_id'        => Yii::t('app', '小程序用户ID'),
				'msg_id'         => Yii::t('app', '消息ID'),
				'media_id'       => Yii::t('app', '用户发送的media_id'),
				'material_type'  => Yii::t('app', '素材类型：2、图片（image）'),
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
		public function getMini ()
		{
			return $this->hasOne(MiniUser::className(), ['id' => 'mini_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMsg ()
		{
			return $this->hasOne(MiniMsg::className(), ['id' => 'msg_id']);
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			return [
				'material_type' => $this->material_type,
				'file_name'     => $this->file_name,
				'media_width'   => $this->media_width,
				'media_height'  => $this->media_height,
				'file_length'   => $this->file_length,
				'content_type'  => $this->content_type,
				'local_path'    => $this->local_path,
				'local_url'     => Yii::$app->params['site_url'] . $this->local_path,
				'yun_url'       => $this->yun_url,
				'wx_url'        => $this->wx_url,
				'create_time'   => $this->create_time,
			];
		}

		/**
		 * @return array
		 */
		public function dumpMiniData ()
		{
			return [
				'file_name'    => $this->file_name,
				'file_length'  => $this->file_length,
				'media_width'  => $this->media_width,
				'media_height' => $this->media_height,
				'content_type' => $this->content_type,
				'local_path'   => $this->local_path,
				'local_url'    => Yii::$app->params['site_url'] . $this->local_path,
				'yun_url'      => $this->yun_url,
				'wx_url'       => $this->wx_url,
			];
		}

		/**
		 * 保存小程序用户消息素材
		 *
		 * @param     $authorId
		 * @param     $miniId
		 * @param     $msgId
		 * @param     $mediaInfo
		 * @param int $mediaType
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function create ($authorId, $miniId, $msgId, $mediaInfo, $mediaType = self::IMAGE_TYPE)
		{
			$miniMsgMaterialId = 0;

			$mediaId = $mediaInfo['media_id'];

			$miniMsgMaterialInfo = static::findOne(['media_id' => $mediaId]);

			$wxAuthor = WxAuthorize::findOne(['author_id' => $authorId]);

			if (!empty($wxAuthor)) {
				$appid = $wxAuthor->authorizer_appid;

				$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

				if (empty($miniMsgMaterialInfo)) {
					$miniMsgMaterialInfo            = new MiniMsgMaterial();
					$miniMsgMaterialInfo->author_id = $authorId;
					$miniMsgMaterialInfo->mini_id   = $miniId;
					$miniMsgMaterialInfo->msg_id    = $msgId;
					$miniMsgMaterialInfo->media_id  = $mediaId;

					/** @var Wechat $wechat */
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
								$miniMsgMaterialInfo->material_type = static::IMAGE_TYPE;
								$miniMsgMaterialInfo->file_name     = $materialData['file_name'];
								$miniMsgMaterialInfo->file_length   = $materialData['file_length'];
								$miniMsgMaterialInfo->media_width   = $materialData['local_data']['width'];
								$miniMsgMaterialInfo->media_height  = $materialData['local_data']['height'];
								$miniMsgMaterialInfo->content_type  = $materialData['local_data']['mime'];
								$miniMsgMaterialInfo->local_path    = $materialData['local_path'];
								$miniMsgMaterialInfo->wx_url        = $mediaInfo['wx_url'];
							}

							break;
						default:
							break;
					}

					if ($miniMsgMaterialInfo->validate() && $miniMsgMaterialInfo->save()) {
						$miniMsgMaterialId = $miniMsgMaterialInfo->id;
					} else {
						throw new InvalidDataException(SUtils::modelError($miniMsgMaterialInfo));
					}
				} else {
					$miniMsgMaterialId = $miniMsgMaterialInfo->id;
				}
			}

			return $miniMsgMaterialId;
		}
	}
