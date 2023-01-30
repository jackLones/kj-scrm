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

	class BindChannel extends BaseObject implements ChannelInterface
	{
		public function execute ($fd, $data)
		{
			$websocket = Websocket::findOne($fd);

			if (empty($websocket)) {
				$websocket             = new Websocket();
				$websocket->id         = $fd;
				$websocket->created_at = time();
			}
			$bindType = !empty($data->info->bindType) ? $data->info->bindType : Websocket::PC_BIND;

			$websocket->bind_type = $bindType;

			if (!empty($data->info->subId)) {
//				Websocket::deleteAll(['and', ['uid' => $data->info->uid, 'subId' => $data->info->subId, 'bind_type' => $bindType], ['not', ['id' => $fd]]]);

				$websocket->uid   = $data->info->uid;
				$websocket->subId = $data->info->subId;
			} elseif (!empty($data->info->uid)) {
//				Websocket::deleteAll(['and', ['uid' => $data->info->uid, 'subId' => '', 'bind_type' => $bindType], ['not', ['id' => $fd]]]);

				$websocket->uid   = $data->info->uid;
				$websocket->subId = '';
			} else {
				$websocket->uid   = '';
				$websocket->subId = '';
			}

			if (!empty($data->info->session_id)) {
				Websocket::deleteAll(['and', ['session_id' => $data->info->session_id, 'bind_type' => $bindType], ['not', ['id' => $fd]]]);

				$websocket->session_id = $data->info->session_id;
			}

			if (!empty($data->info->openid)) {
//				Websocket::deleteAll(['and', ['openid' => $data->info->openid, 'bind_type' => $bindType], ['not', ['id' => $fd]]]);

				$websocket->openid = $data->info->openid;
			}

			$websocket->updated_at = time();

			$websocket->save();

			return [
				$fd, // 第一个参数返回客户端ID，多个以数组形式返回
				'{"message": "绑定成功"}' // 第二个参数返回需要返回给客户端的消息
			];
		}

		public function close ($fd)
		{
			Websocket::deleteAll(['id' => $fd]);

			return;
		}
	}