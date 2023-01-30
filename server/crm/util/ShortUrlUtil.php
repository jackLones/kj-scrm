<?php

	namespace app\util;

	use app\models\WorkPublicActivityUrl;

	class ShortUrlUtil
	{
		/**
		 * @param $baseUrl
		 * @param $ref
		 */
		public static function setShortUrl ($baseUrl, $ref = false)
		{
			$url  = md5($baseUrl);
			$url1 = strtoupper(substr($url, 0, 5));
			$url2 = strtolower(substr($url, 5, 5));
			$url  = $url1 . $url2;
			if ($ref) {
				$url = str_shuffle($url);
			}
			$short = WorkPublicActivityUrl::findOne(["short_url" => $url]);
			if (!empty($short)) {
				if ($short->url != \Yii::$app->params["web_url"] .$baseUrl) {
					self::setShortUrl($baseUrl, true);
				}

				return $url;

			}
			$short              = new WorkPublicActivityUrl();
			$short->short_url   = $url;
			$short->url         = \Yii::$app->params["web_url"] . $baseUrl;
			$short->create_time = time();
			$short->save();

			return $url;
		}

		public static function getLongUrl ($short_url)
		{
			$short = WorkPublicActivityUrl::findOne(["short_url" => $short_url]);
			if (!empty($short)) {
				return $short->url;
			}

			return '';
		}

	}