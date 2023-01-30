<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/9/21
	 * Time: 17:16
	 */

	namespace app\channels;

	use app\models\Authority;
	use app\models\SubUserAuthority;
	use app\models\Websocket;
	use app\util\WebsocketUtil;
	use yii\base\BaseObject;
	use yii\helpers\Json;
	use yiiplus\websocket\ChannelInterface;

	class PushMessageChannel extends BaseObject implements ChannelInterface
	{
		public function execute ($fd, $data)
		{
			$to = !empty($data->to) ? $data->to : '';
			if (empty($to)) {
				return false;
			}

			$clientData = Websocket::findAll(['uid' => $to]);
			if (empty($clientData)) {
				$clientData = Websocket::findAll(['session_id' => $to]);
				if (empty($clientData)) {
					return false;
				}
			}

			$fds = [];
			foreach ($clientData as $client) {
				$needPush = false;

				if ($data->type == WebsocketUtil::WX_TYPE || $data->type == WebsocketUtil::WORK_TYPE) {
					if (!empty($client->subId)) {
						if ($data->type == WebsocketUtil::WX_TYPE) {
							$type  = SubUserAuthority::WX_TYPE;
							$route = 'fansMsg';
						} elseif ($data->type == WebsocketUtil::WORK_TYPE) {
							$type  = SubUserAuthority::WORK_TYPE;
							$route = 'archiveMsg';
						}
						$subUserAuthority = SubUserAuthority::findOne(['sub_user_id' => $client->subId, 'wx_id' => $data->wx_id, 'type' => $type]);
						if (!empty($subUserAuthority)) {
							$authorityInfo = explode(',', $subUserAuthority->authority_ids);
							$authorData = Authority::findOne(['route' => $route]);
							if (!empty($authorData)) {
								$needPush = in_array($authorData->id, $authorityInfo);
							}
						}
					} else {
						$needPush = true;
					}
				} else {
					$needPush = true;
				}
				//nohup /usr/local/php7/bin/php /home/wwwroot/crm/server/crm/yii websocket/start -p 7099 &

				if ($needPush) {
					array_push($fds, $client->id);
				}
			}

			if (empty($data->info->type)) {
				$data->info->type = 'chat';
			}

			if (empty($data->info->from)) {
				$data->info->from = Websocket::findOne($fd)->uid;
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