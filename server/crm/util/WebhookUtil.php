<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/4/1
	 * Time: 09:45
	 */

	namespace app\util;

	use app\components\InvalidParameterException;
	use linslin\yii2\curl\Curl;
	use yii\helpers\Json;

	class WebhookUtil
	{
		const TEXT_MESSAGE = 'text';
		const MARKDOWN_MESSAGE = 'markdown';
		const IMAGE_MESSAGE = 'image';
		const NEWS_MESSAGE = 'news';

		/**
		 * 发送机器人消息
		 *
		 * @param string $webhookUrl
		 * @param array  $content
		 * @param string $msgType
		 *
		 * @return bool
		 *
		 * @throws InvalidParameterException
		 * @throws \Exception
		 */
		public static function send ($webhookUrl, $content, $msgType = self::TEXT_MESSAGE)
		{
			if (empty($webhookUrl) || empty($content)) {
				throw new InvalidParameterException('缺少必要参数');
			}

			$sendContent = [
				'msgtype' => $msgType,
			];

			switch ($msgType) {
				case self::TEXT_MESSAGE;
					$sendContent['text'] = $content;

					break;
				case self::MARKDOWN_MESSAGE;
					$sendContent['markdown'] = $content;

					break;
				case self::IMAGE_MESSAGE;
					$sendContent['image'] = $content;

					break;
				case self::NEWS_MESSAGE;
					$sendContent['news']['articles'] = $content;

					break;
				default:
					throw new InvalidParameterException('不合法的消息类型');

					break;
			}

			$curl     = new Curl();
			$response = $curl->setOptions([
				CURLOPT_POST       => true,
				CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			])->setRawPostData(
				Json::encode($sendContent, JSON_UNESCAPED_UNICODE)
			)->post($webhookUrl);

			if ($curl->responseCode == 200) {
				return true;
			} else {
				return false;
			}
		}
	}