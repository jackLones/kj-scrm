<?php

	namespace app\util;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\models\Attachment;

	class ImageTextUtil
	{
		public static function getCurl ($url)
		{
			$header = [
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36 Edg/86.0.622.38',
			];
			$curl   = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_TIMEOUT, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			$data = curl_exec($curl);
			curl_close($curl);

			return $data;
		}

		public static function getTitle ($html)
		{
			preg_match("/<title>(.*)<\/title>/", $html, $titleName);
			$title = '';
			if (empty($titleName[1])) {
				preg_match_all("/var msg_title = \\'(.*?)\\'./si", $html, $title, PREG_PATTERN_ORDER);
				if (isset($title[1]) && isset($title[1][0]) && !empty($title[1][0])) {
					$title = htmlspecialchars_decode($title[1][0]);
				} else {
					$title = '';
				}
			} else {
				$title = $titleName[1];
			}

			return $title;
		}

		public static function getDescription ($html)
		{
			preg_match('/<meta name=\"description\" content=\"(.*?)\" \/>/s', $html, $description);
			if (empty($description[1])) {
				preg_match_all("/var msg_desc = \"(.*?)\"./si", $html, $description, PREG_PATTERN_ORDER);
				if (isset($description[1]) && isset($description[1][0]) && !empty($description[1][0])) {
					$description = htmlspecialchars_decode($description[1][0]);
				} else {
					$description = '';
				}
			} else {
				$description = $description[1];
				$description = mb_substr($description, 0, 254);
			}

			return $description;
		}

		public static function getImageUrl ($html, $url)
		{
			try {
				$save_dir   = \Yii::getAlias('@upload') . '/webimg/' . date('Ymd') . '/';
				$s_filename = '/upload/webimg/' . date('Ymd') . '/';
				preg_match('/^((https|http|ftp|rtsp|mms)?:\/\/)([^\s]+)(\.com|\.cn)/', $url, $TempUrl);
				if (isset($TempUrl[0])) {
					try {
						$file = file_get_contents($TempUrl[0] . "/favicon.ico");
						if (!empty($file)) {
							$fileName = "web_img_" . rand(1, 10000) . time() . ".ico";//定义图片名
							file_put_contents($save_dir . $fileName, $file);
							return $s_filename . $fileName;
						}
					} catch (\Exception $e) {

					}
				}
				preg_match('/<img[^\.>]*src=([\'|"])([^\'"]*)([\'|"])([^>]*)>/', $html, $img);
				if ((isset($img[2]) && empty($img[2])) || (isset($img[2]) && $img[2] == '')) {
					preg_match_all('/var msg_cdn_url = \"(.*?)\";/si', $html, $m);
					$imgScr = htmlspecialchars_decode($m[1][0]); //公众号头像
					if (isset($m[1]) && isset($m[1][0]) && !empty($m[1][0])) {
						$imgScr = htmlspecialchars_decode($m[1][0]);
					}
				} else {
					$imgScr = !isset($img[2]) ? '' : $img[2];
				}
				if (!empty($imgScr)) {
					//创建保存目录
					if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
						throw new InvalidDataException("文件创建失败，权限不足");
					}
					if (strpos($imgScr, "http://") !== false || strpos($imgScr, "https://") !== false) {
						$fileName = "web_img_" . rand(1, 10000) . time() . ".jpg";//定义图片名
						$file     = file_get_contents($imgScr);
						file_put_contents($save_dir . $fileName, $file);

						return $s_filename . $fileName;
					}
				}
			} catch (\Exception $e) {

			}

			return '/static/image/url.png';

		}

		/**
		 * 通过 url 解析标题 图片 描述
		 * File: util/ImageTextUtil.php
		 * Class: ImageTextUtil
		 * Function: getUrlAll
		 *
		 * @param     $url
		 * @param int $is_import
		 * @param int $corp_id
		 * @param int $uid
		 * @param int $sub_id
		 * @param int $isMasterAccount
		 *
		 * @return array
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 */
		public static function getUrlAll ($url, $is_import = 0, $corp_id = 0, $uid = 0, $sub_id = 0, $isMasterAccount = 0)
		{
			$context = [];
			$html    = self::getCurl($url);
			if (empty($html)) {
				throw new InvalidDataException("网页获取失败");
			}
			$context['title'] = self::getTitle($html);
			$array            = ["ASCII", "UTF-8", "GB2312", "GBK", "BIG5"];
			$encode           = mb_detect_encoding($context['title'], $array);
			if ($encode != "UTF-8") {
				$context['title'] = iconv($encode, "UTF-8", $context['title']);
			}
			$context['description'] = self::getDescription($html);
			$context['url']         = self::getImageUrl($html, $url);
			if ($is_import > 0 && !empty($context['url'])) {
				$extension = pathinfo($context['url'], PATHINFO_EXTENSION);
				if ($extension == 'ico') {
					throw new InvalidDataException("微信不允许该图片格式上传临时素材请自定义图片");
				}
				//beenlee 同步至内容引擎 导入微信素材库
				$imgDate = [
					'uid'             => $uid,
					'sub_id'          => $sub_id,
					'isMasterAccount' => $isMasterAccount,
					'file_type'       => 1,
					'group_id'        => 0,
					'local_path'      => $context['url'],
					'is_temp'         => 1,
				];

				try {
					$context['id'] = Attachment::syncAttachment($imgDate);
				} catch (\Exception $e) {
					throw new InvalidParameterException('同步内容引擎失败！');
				}
			}

			$encode                 = mb_detect_encoding($context['description'], $array);
			if ($encode != "UTF-8") {
				$context['description'] = iconv($encode, "UTF-8", $context['description']);
			}
			if (empty($context['title']) && empty($context['description']) && empty($context['url'])) {
				throw new InvalidDataException("网页获取失败");
			}

			return $context;
		}

	}