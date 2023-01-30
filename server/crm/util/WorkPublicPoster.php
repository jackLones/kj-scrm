<?php

	namespace app\util;

	use app\components\InvalidDataException;
	use app\models\WxAuthorize;
	use callmez\wechat\sdk\Wechat;

	class WorkPublicPoster
	{
		public static function getWxObject ($author_id)
		{
			$wxAuthInfo  = WxAuthorize::findOne(['author_id' => $author_id]);
			$wxAuthorize = WxAuthorize::getTokenInfo($wxAuthInfo->authorizer_appid, false, true);
			if (empty($wxAuthorize)) {
				\Yii::error("获取token失败", "sym-wx-token");
			}
			$wechat = \Yii::createObject([
				'class'          => Wechat::className(),
				'appId'          => $wxAuthInfo->authorizer_appid,
				'appSecret'      => $wxAuthorize['config']->appSecret,
				'token'          => $wxAuthorize['config']->token,
				'componentAppId' => $wxAuthorize['config']->appid,
			]);

			return $wechat;
		}

		public static function getPoster ($activity, $fans, $config, $tier, $corp = false)
		{
			$wechat = self::getWxObject($activity['public_id']);

			if (!empty($fans->poster_path)) {
				$fans->tier = $tier;
				$fans->save();
				if ($corp) {
					$serviceWork = WorkUtils::getWorkApi($activity["corp_id"]);

					return $serviceWork->MediaUpload(\Yii::$app->basePath . $fans->poster_path, 'image');
				}
				$result = $wechat->uploadMedia(\Yii::$app->basePath . $fans->poster_path, "image");

				return $result['media_id'];
			}

			$qrCodeData = ['expire_seconds' => 2592000, 'action_name' => 'QR_STR_SCENE', 'action_info' => ['scene' => ['scene_str' => "activity_" . $activity['id'] . "_fansId_" . $fans->id . "_tier_0"]]];
			$result     = $wechat->createQrCode($qrCodeData);
			if (isset($result["errcode"]) && $result["errcode"] == "48001") {
				throw new InvalidDataException('公众号未认证或未取得接口权限');
			}
			if (isset($result["errcode"])) {
				throw new InvalidDataException('创建二维码失败');
			}
			$qrCodeUrl  = $wechat->getQrCodeUrl($result['ticket']);
			$code       = imagecreatefromjpeg($qrCodeUrl);
			$codeX      = $config['code_left'] * 2;
			$codeY      = $config['code_top'] * 2;
			//二维码宽高
			$codeWidth  = imagesx($code);
			$codeHeight = imagesy($code);
			//按比列重新生成二维码
			$newCode = imagecreatetruecolor($config['code_width'] * 2, $config['code_height'] * 2);
			imagecopyresampled($newCode, $code, 0, 0, 0, 0, $config['code_width'] * 2, $config['code_height'] * 2, $codeWidth, $codeHeight);

			$array = getimagesize(\Yii::$app->basePath . $config["background_url"]);
			$type  = explode("/", $array['mime']);
			if (in_array($type[1], ["jpg", "jpeg"])) {
				$image = imagecreatefromjpeg(\Yii::$app->basePath . $config["background_url"]);
			} else {
				$image = imagecreatefrompng(\Yii::$app->basePath . $config["background_url"]);
			}
			if (empty($config['heard_url'])) {
				$heard = imagecreatefrompng(\Yii::$app->basePath . "/static/image/default-avatar.png");
			} else {
				$heard = imagecreatefromstring(file_get_contents($config['heard_url']));
			}
			$newImageWidth  = imagesx($image);
			$newImageHeight = imagesy($image);
			$per=round(750/$newImageWidth,3);

			$n_w=$newImageWidth*$per;
			$n_h=$newImageHeight*$per;
			//新背景
			$newImage = imagecreatetruecolor($n_w, $n_h);
			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $n_w, $n_h, $newImageWidth, $newImageHeight);

			//头像宽高
			$heardWidth = imagesx($heard);
			$hearHeight = imagesy($heard);
			$heardWidth = $hearHeight = min($heardWidth, $hearHeight);
			//重新生成头像
			$newHeard = imagecreatetruecolor($config['heard_width'] * 2, $config['heard_width'] * 2);
			imagecopyresampled($newHeard, $heard, 0, 0, 0, 0, $config['heard_width'] * 2, $config['heard_width'] * 2, $heardWidth, $heardWidth);
			$heardWidth = $hearHeight = $config['heard_width'] * 2;
			$r          = $heardWidth / 2;
			$heardX     = $config['heard_left'] * 2;
			$heardY     = $config['heard_top'] * 2;
			$hxr        = -1;
			$hxy        = -1;
			for ($x = 0; $x < imagesx($newImage); $x++) {
				for ($y = 0; $y < imagesy($newImage); $y++) {
					if ($x >= $heardX && $y >= $heardY && $config['is_heard'] == 1) {
						if ($x - $heardX < $heardWidth && $y - $heardY < $hearHeight) {
							$hxr     = $hxr == -1 ? $x : $hxr;
							$hxy     = $hxy == -1 ? $y : $hxy;
							$circleX = $x - $hxr;
							$circleY = $y - $hxy;
							if ($config["heard_type"] == 1) {
								$rgb = imagecolorat($newHeard, $circleX, $circleY);
							} else {
								// 勾股定理 新圆的面积是否小于等于本身圆的面积
								if ((($circleX - $r) * ($circleX - $r)) + (($circleY - $r) * ($circleY - $r)) <= ($r * $r)) {
									$rgb = imagecolorat($newHeard, $circleX, $circleY);
								} else {
									$rgb = imagecolorat($newImage, $x, $y);
								}
							}
						} else {
							$rgb = imagecolorat($newImage, $x, $y);
						}
					} else {
						$rgb = imagecolorat($newImage, $x, $y);
					}
					imagesetpixel($newImage, $x, $y, $rgb);
				}
			}
			$newImage2 = imagecreatetruecolor($n_w, $n_h);
			for ($x = 0; $x < imagesx($newImage); $x++) {
				for ($y = 0; $y < imagesy($newImage); $y++) {
					if ($x >= $codeX && $y >= $codeY) {
						if ($x - $codeX < $config['code_width'] * 2 && $y - $codeY < $config['code_height'] * 2) {
							$rgb = imagecolorat($newCode, $x - $codeX, $y - $codeY);
						} else {
							$rgb = imagecolorat($newImage, $x, $y);
						}
					} else {
						$rgb = imagecolorat($newImage, $x, $y);
					}
					imagesetpixel($newImage2, $x, $y, $rgb);
				}
			}
			$newImage = $newImage2;
			if ($config["is_font"]) {
				$rgb        = explode(",", $config['font_color']);
				$text_color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
				$nameLength = self::ReplaceNameLength($config["userName"], $config['font_size']);
				$fontX      = $config['font_left'] * 2;
				if ($nameLength + $fontX > 750) {
					$fontX = $fontX - (($nameLength + $fontX) - 750);
					$fontX = ($fontX < 0) ? 0 : $fontX;
				}
				$fontY = empty($config['font_top']) ? $config["font_size"] * 2 + $config["font_size"] * 2 : ($config["font_size"] + $config['font_top'] * 2) + $config["font_size"];

				$fontPath = \Yii::$app->basePath . "/static/fonts/apple-x.ttf";
				imagettftext($newImage, $config['font_size'] * 2, 0, $fontX, $fontY, $text_color, $fontPath, $config["userName"]);
			}
			$fileName = rand(1, 10000) . time() . ".jpg";//定义图片名
			$tmp_dir  = '/poster/' . date('Ymd') . '/';
			$save_dir = \Yii::getAlias('@upload') . $tmp_dir;
			//创建保存目录
			if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
				return ['file_name' => '', 'save_path' => '', 'error' => 1];
			}
			//保存海报二维码
			$fans->poster_path = "/upload" . $tmp_dir . $fileName;
			$fans->tier        = $tier;
			$fans->save();
			//保存第一次生成用于上传
			imagejpeg($newImage, $save_dir . $fileName);//保存图片
			if ($corp) {
				$serviceWork = WorkUtils::getWorkApi($activity["corp_id"]);

				return $serviceWork->MediaUpload($save_dir . $fileName, 'image');
			}
			$result = $wechat->uploadMedia($save_dir . $fileName, "image");

			return $result['media_id'];
		}

		public static function ReplaceNameLength ($name, $size)
		{
			$sumLength = 0;
			preg_match_all("/\w+/", $name, $TmpName);
			foreach ($TmpName as $item) {
				foreach ($item as $value) {
					$sumLength += strlen($value) * $size * 2;
				}
			}
			preg_match_all("/[\x7f-\xff]+/", $name, $ChinaTmpName);
			foreach ($ChinaTmpName as $item) {
				foreach ($item as $value) {
					$sumLength += strlen($value) * $size;
				}
			}

			return $sumLength - $size * 2;
		}
	}