<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/9/20
	 * Time: 16:23
	 */

	namespace app\channels;

	use app\components\InvalidDataException;
	use app\models\Attachment;
	use app\models\AttachmentStatistic;
	use app\models\ExternalTimeLine;
	use app\models\Websocket;
	use yii\base\BaseObject;
	use yiiplus\websocket\ChannelInterface;

	class PullChannel extends BaseObject implements ChannelInterface
	{
		const UPDATE_ATTACHMENT_LEAVE_TIME = 1;
		const UPDATE_MOMENT_TRACE_TIME     = 2;

		public function execute ($fd, $data)
		{
			$type = !empty($data->info->type) ? $data->info->type : '';
			if (empty($type)) {
				return [
					$fd,
					'{"message": "fail00"}'
				];
			}

			switch ($type) {
				case self::UPDATE_ATTACHMENT_LEAVE_TIME:
					$attachmentStatisticId = !empty($data->info->statistic_id) ? $data->info->statistic_id : 0;
					if ($attachmentStatisticId == 0) {
						return [
							$fd,
							'{"message": "fail001"}'
						];
					}

					$attachmentStatistic = AttachmentStatistic::findOne($attachmentStatisticId);
					if ($attachmentStatistic === NULL) {
						return [
							$fd,
							'{"message": "fail002"}'
						];
					}

					$attachment = Attachment::findOne($attachmentStatistic->attachment_id);
					if ($attachment === NULL) {
						return [
							$fd,
							'{"message": "fail0021"}'
						];
					}

					if (!in_array($attachment->file_type, [1, 3, 4, 5],false)) {
						return [
							$fd,
							'{"message": "fail0022"}'
						];
					}

					if ($attachment->file_type == 4 && $attachment->is_editor == 0 && $attachment->material_id == 0) {
						return [
							$fd,
							'{"message": "fail0023"}'
						];
					}

					if ($attachment->file_type == 5 && !in_array($attachment->file_content_type, ['text/plain', 'application/pdf'])) {
						return [
							$fd,
							'{"message": "fail0024"}'
						];
					}

					try {
						$attachmentStatistic->setLeaveTime();
					} catch (\Exception $e) {
						return [
							$fd,
							'{"message": "fail003"}'
						];
					}

					break;
				case self::UPDATE_MOMENT_TRACE_TIME:
					$timeLine = !empty($data->info->timeLine) ? $data->info->timeLine : 0;
					if ($timeLine == 0) {
						return [
							$fd,
							'{"message": "fail1"}'
						];
					}

					$TimeLineModel = ExternalTimeLine::findOne($timeLine);
					if (empty($TimeLineModel)) {
						return [
							$fd,
							'{"message": "fail2"}'
						];
					}

					try {
						ExternalTimeLine::setMomentLiveTime($timeLine, $data->info->settingId, $data->info->momentType, $fd);
					} catch (\Exception $e) {
						return [
							$fd,
							'{"message": "' . $e->getMessage() . '"}'
						];
					}

					break;
				default:
					return [
						$fd,
						'{"message": "fail4"}'
					];

					break;
			}

			return [
				$fd, // 第一个参数返回客户端ID，多个以数组形式返回
				'{"message": "success"}' // 第二个参数返回需要返回给客户端的消息
			];
		}

		public function close ($fd)
		{
			Websocket::deleteAll(['id' => $fd]);

			return;
		}
	}