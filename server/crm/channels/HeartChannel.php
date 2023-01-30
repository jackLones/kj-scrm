<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/9/20
	 * Time: 16:23
	 */

	namespace app\channels;

	use app\models\Websocket;
	use yii\base\BaseObject;
	use yiiplus\websocket\ChannelInterface;

	class HeartChannel extends BaseObject implements ChannelInterface
	{
		public function execute ($fd, $data)
		{
			$websocket = Websocket::findOne($fd);

			if (empty($websocket)) {
				$websocket             = new Websocket();
				$websocket->id         = $fd;
				$websocket->created_at = time();
			}

			if (!empty($data->info->subId)) {
				$websocket->uid   = $data->info->uid;
				$websocket->subId = $data->info->subId;
			} elseif (!empty($data->info->uid)) {
				$websocket->uid   = $data->info->uid;
				$websocket->subId = '';
			} else {
				$websocket->uid   = '';
				$websocket->subId = '';
			}

			if (!empty($data->info->session_id)) {
				$websocket->session_id = $data->info->session_id;
			}

			if (!empty($data->info->openid)) {
				$websocket->openid = $data->info->openid;
			}

			$websocket->updated_at = time();

			$websocket->save();

			return [
				$fd, // 第一个参数返回客户端ID，多个以数组形式返回
				'{"message": "心跳接收成功"}' // 第二个参数返回需要返回给客户端的消息
			];
		}

		public function close ($fd)
		{
			Websocket::deleteAll(['id' => $fd]);

			return;
		}
	}