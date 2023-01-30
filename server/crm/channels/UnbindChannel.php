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

	class UnbindChannel extends BaseObject implements ChannelInterface
	{
		public function execute ($fd, $data)
		{
			return [
				$fd, // 第一个参数返回客户端ID，多个以数组形式返回
				$data // 第二个参数返回需要返回给客户端的消息
			];
		}

		public function close ($fd)
		{
			Websocket::deleteAll(['id' => $fd]);

			return;
		}
	}