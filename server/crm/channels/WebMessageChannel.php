<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/4/2
	 * Time: 16:12
	 */

	namespace app\channels;

	use app\models\Websocket;
	use yii\base\BaseObject;
	use yii\helpers\Json;
	use yiiplus\websocket\ChannelInterface;

	class WebMessageChannel extends BaseObject implements ChannelInterface
	{
		public function execute ($fd, $data)
		{
			$to = !empty($data->to) ? $data->to : '';
			if (empty($to)) {
				return false;
			}

			$clientData = Websocket::findAll(['openid' => $to]);
			if (empty($clientData)) {
				$clientData = Websocket::findAll(['session_id' => $to]);
				if (empty($clientData)) {
					return false;
				}
			}

			$fds = [];
			foreach ($clientData as $client) {
				array_push($fds, $client->id);
			}

			if (empty($data->info->type)) {
				$data->info->type = 'sys';
			}

			if (empty($data->info->from)) {
				$data->info->from = 'sys';
			}

			$message = Json::encode($data->info, JSON_FORCE_OBJECT);

			return [
				$fds, // 第一个参数返回客户端ID，多个以数组形式返回
				$message // 第二个参数返回需要返回给客户端的消息
			];
		}

		public function close ($fd)
		{
			Websocket::deleteAll(['id' => $fd]);

			return;
		}
	}