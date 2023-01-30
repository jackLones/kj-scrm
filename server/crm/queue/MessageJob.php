<?php
	/**
	 * 短信群发发送
	 * User: xingchangyu
	 * Date: 2019/12/18
	 * Time: 13：00
	 */

	namespace app\queue;

	use app\models\MessagePush;
	use app\models\MessagePushDetail;
	use app\models\MessageSign;
	use app\models\MessageType;
	use app\models\User;
	use yii\base\BaseObject;
	use yii\base\InvalidParamException;
	use yii\queue\JobInterface;

	class MessageJob extends BaseObject implements JobInterface
	{
		public $message_push_id;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->message_push_id, 'message_push_id');
			$messagePush = MessagePush::findOne(['id' => $this->message_push_id, 'is_del' => 0]);

			if (empty($messagePush)) {
				return false;
			}
			$sign_name  = $messagePush->sign->title;
			$content    = $messagePush->content;
			$send_data  = $messagePush->send_data;
			$contentStr = $content . '回T退订【' . $sign_name . '】';
			try {
				$phoneArr = explode(',', $send_data);
				$result   = \Yii::$app->ihuyi->sendMarketing($phoneArr, $contentStr);
			} catch (InvalidParamException $e) {
				$result = ['error' => $e->getcode(), 'error_msg' => $e->getMessage()];
			}
			\Yii::error($result, 'messageResult-' . $messagePush->id);
			if ($result['error'] == 0) {
				$messagePush->status      = 1;
				$messagePush->queue_id    = 0;
				$messagePush->smsid       = !empty($result['smsid']) ? $result['smsid'] : '';
				$messagePush->save();
				//添加发送明细
				$signInfo = MessageSign::findOne($messagePush->sign_id);
				$typeInfo = MessageType::findOne($messagePush->type_id);
				//内容所占短信数
				$length = mb_strlen($contentStr, 'utf-8');
				$num    = ceil($length / 66);//营销短信按照66字/每条
				foreach ($phoneArr as $phone) {
					$detail             = new MessagePushDetail();
					$detail->uid        = $messagePush->uid;
					$detail->message_id = $messagePush->id;
					$detail->title      = $messagePush->title;
					$detail->phone      = $phone;
					$detail->sign_name  = $signInfo->title;
					$detail->type_name  = $typeInfo->title;
					$detail->content    = $content;
					$detail->status     = 3;
					$detail->push_time  = $messagePush->push_time;
					$detail->num        = $num;
					$detail->save();
				}
			} else {
				$messagePush->status     = 2;//发送失败
				$messagePush->queue_id   = 0;
				$messagePush->error_code = !empty($result['error']) ? $result['error'] : 0;
				$messagePush->error_msg  = !empty($result['error_msg']) ? $result['error_msg'] : '';
				$messagePush->save();
				//退还短信数
				$length      = mb_strlen($contentStr, 'utf-8');
				$num         = ceil($length / 66);//营销短信按照66字/每条
				$message_num = $num * $messagePush->target_num;
				$user        = User::findOne($messagePush->uid);
				if (!empty($user)) {
					$user->updateCounters(['message_num' => +$message_num]);
				}
			}
		}
	}