<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/11
	 * Time: 21:02
	 */

	namespace app\util;

	use app\components\ForbiddenException;
	use app\components\InvalidDataException;
	use app\models\FansMsg;
	use app\models\Material;
	use app\models\WorkCorp;
	use app\models\WorkCorpAuth;
	use app\models\WorkMaterial;
	use app\models\WorkSuiteConfig;
	use app\models\WxAuthorize;
	use callmez\wechat\sdk\Wechat;
	use dovechen\yii2\weWork\ServiceWork;

	class MsgUtil
	{
		/**
		 * 发送客服消息
		 *
		 * @param string $appid      公众号appid
		 * @param string $openid     关注者openid
		 * @param string $msgType    消息类型
		 * @param array  $msgContent 消息内容
		 *
		 * @return array
		 *
		 * @throws ForbiddenException
		 * @throws \Throwable
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function send ($appid, $openid, $msgType, $msgContent,$activity = false)
		{
			$result      = ['errcode' => '-1', 'errmsg' => '系统繁忙'];
			$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

			if (!empty($wxAuthorize)) {
				$wechat = \Yii::createObject([
					'class'          => Wechat::className(),
					'appId'          => $appid,
					'appSecret'      => $wxAuthorize['config']->appSecret,
					'token'          => $wxAuthorize['config']->token,
					'componentAppId' => $wxAuthorize['config']->appid,
				]);

				switch ($msgType) {
					case FansMsg::TEXT_MSG:
						$result = $wechat->sendText($openid, $msgContent['text']);

						break;
					case FansMsg::IMG_MSG:
						$materialInfo = Material::findOne(['id' => $msgContent['media_id']]);
						if (!empty($materialInfo)) {
							static::checkNeedReload($materialInfo);

							$result = $wechat->sendImage($openid, $materialInfo->media_id);
						}
						if($activity){
							$result = $wechat->sendImage($openid, $msgContent['media_id']);
						}
						break;
					case FansMsg::VOICE_MSG:
						$materialInfo = Material::findOne(['id' => $msgContent['media_id']]);
						if (!empty($materialInfo)) {
							static::checkNeedReload($materialInfo);

							$result = $wechat->sendVoice($openid, $materialInfo->media_id);
						}

						break;
					case FansMsg::VIDEO_MSG:
						$materialInfo = Material::findOne(['id' => $msgContent['media_id']]);
						$thumbMediaId = '';
						if (!empty($msgContent['thumb_media_id'])) {
							$thumbMaterialInfo = Material::findOne(['id' => $msgContent['thumb_media_id']]);
							if (!empty($thumbMaterialInfo)) {
								static::checkNeedReload($thumbMaterialInfo);
								$thumbMediaId = $thumbMaterialInfo->media_id;
							}
						}
						if (!empty($materialInfo)) {
							static::checkNeedReload($materialInfo);

							$mediaId = $materialInfo->media_id;
							$title   = $description = NULL;
							if (!empty($msgContent['title'])) {
								$title = $msgContent['title'];
							} elseif (!empty($materialInfo->title)) {
								$title = $materialInfo->title;
							}
							if (!empty($msgContent['description'])) {
								$description = $msgContent['description'];
							} elseif (!empty($materialInfo->introduction)) {
								$description = $materialInfo->introduction;
							}

							$result = $wechat->sendVideo($openid, $mediaId, $thumbMediaId, $title, $description);
						}

						break;
					case FansMsg::MUSIC_MSG:
						$materialInfo = Material::findOne(['id' => $msgContent['media_id']]);
						if (!empty($materialInfo)) {
							$thumbMediaId = $materialInfo->media_id;
							$musicUrl     = $msgContent['music_url'];
							$hqMusicUrl   = $msgContent['hq_music_url'];
							$title        = $msgContent['title'] ?: NULL;
							$description  = $msgContent['description'] ?: NULL;

							$result = $wechat->sendMusic($openid, $thumbMediaId, $musicUrl, $hqMusicUrl, $title, $description);
						}

						break;
					case FansMsg::NEWS_MSG:
						if (!empty($msgContent['media_id'])) {
							$materialInfo = Material::findOne(['id' => $msgContent['media_id']]);
							if (!empty($materialInfo)) {
								$result = $wechat->sendNewsByMediaId($openid, $materialInfo->media_id);
							}
						} else {
							$articles   = [];
							$articles[] = [
								'title'       => $msgContent['title'],
								'description' => $msgContent['description'],
								'url'         => $msgContent['url'],
								'picurl'      => $msgContent['pic_url']
							];

							$result = $wechat->sendNews($openid, $articles);
						}

						break;
					case FansMsg::MINI_MSG:
						$materialInfo = Material::findOne(['id' => $msgContent['media_id']]);
						if (!empty($materialInfo)) {
							static::checkNeedReload($materialInfo);
							$miniprogrampage = [
								'title'          => $msgContent['title'],
								'appid'          => $msgContent['appid'],
								'pagepath'       => $msgContent['pagepath'],
								'thumb_media_id' => $materialInfo->media_id
							];
							$result          = $wechat->sendMiniprogram($openid, $miniprogrampage);
						}
						break;
					default:
						break;
				}
			}

			return $result;
		}

		/**
		 * @param Material $material
		 *
		 * @throws ForbiddenException
		 * @throws \Throwable
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function checkNeedReload (&$material)
		{
			if ($material->type == Material::SHORT_TIME_MATERIAL && (empty($material->expire) || $material->expire < (time() - 60))) {
				$appid = $material->author->authorizer_appid;

				$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

				if (!empty($wxAuthorize)) {
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					$filePath = \Yii::getAlias('@app') . $material->local_path;

					$fileType = '';
					switch ($material->material_type) {
						case Material::IMG_MATERIAL:
							$fileType = 'image';

							break;
						case Material::VOICE_MATERIAL:
							$fileType = 'voice';

							break;
						case Material::VIDEO_MATERIAL:
							$fileType = 'video';

							break;
						case Material::THUMB_MATERIAL:
							$fileType = 'thumb';

							break;
						default:

							break;
					}

					$upResult = $wechat->uploadMedia($filePath, $fileType);
					if (!empty($upResult['media_id'])) {
						$expireTime           = time() + 3 * 24 * 60 * 60;
						$material->expire     = (string) $expireTime;
						$material->media_id   = $upResult['media_id'];
						$material->created_at = (string) $upResult['created_at'];
						if (!$material->validate() || !$material->update()) {
							throw new InvalidDataException(SUtils::modelError($material));
						}
					} else {
						throw new ForbiddenException($upResult['errmsg']);
					}
				}
			}
		}

		/**
		 * @param WorkMaterial $workMaterial
		 *
		 * @throws ForbiddenException
		 * @throws \Throwable
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function checkWorkNeedReload (&$workMaterial, $suiteId = 1)
		{
			$time = time();
			if ($workMaterial->type == WorkMaterial::SHORT_TIME_MATERIAL && (empty($workMaterial->expire) || $workMaterial->expire < ($time - 60)) && in_array($workMaterial->material_type, [2, 3, 4, 5])) {
				$corp         = WorkCorp::findOne($workMaterial->corp_id);
				$serviceWork = WorkUtils::getWorkApi($corp->id);
				$filePath = \Yii::getAlias('@app') . $workMaterial->local_path;

				$fileType = '';
				switch ($workMaterial->material_type) {
					case WorkMaterial::IMG_MATERIAL:
						$fileType = 'image';

						break;
					case WorkMaterial::VOICE_MATERIAL:
						$fileType = 'voice';

						break;
					case WorkMaterial::VIDEO_MATERIAL:
						$fileType = 'video';

						break;
					case WorkMaterial::FILE_MATERIAL:
						$fileType = 'file';

						break;
					default:

						break;
				}
				try {
					$media_id = $serviceWork->MediaUpload($filePath, $fileType, ['file_name' => $workMaterial->file_name]);
					if (!empty($media_id)) {
						$expireTime               = $time + 3 * 24 * 60 * 60;
						$workMaterial->expire     = (string) $expireTime;
						$workMaterial->media_id   = $media_id;
						$workMaterial->created_at = $time;
						if (!$workMaterial->validate() || !$workMaterial->update()) {
							throw new InvalidDataException(SUtils::modelError($workMaterial));
						}
					}
				} catch (\Exception $e) {
					throw new InvalidDataException($e->getMessage());
				}
			}
		}
	}