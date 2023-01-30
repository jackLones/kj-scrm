<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2021/2/5
	 * Time: 16:20
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\WorkMsgAuditInfoEmotion;
	use app\models\WorkMsgAuditInfoFile;
	use app\models\WorkMsgAuditInfoImage;
	use app\models\WorkMsgAuditInfoMeetingVoiceCall;
	use app\models\WorkMsgAuditInfoVideo;
	use app\models\WorkMsgAuditInfoVoice;
	use app\models\WorkMsgAuditInfoVoipDocShare;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use linslin\yii2\curl\Curl;
	use yii\base\BaseObject;
	use yii\helpers\Json;
	use yii\queue\JobInterface;

	class MsgAuditMediaGetJob extends BaseObject implements JobInterface
	{
		public $config_id;
		public $index_buf;
		public $sdk_file_id;
		public $file_name;
		public $file_type;
		public $media_id;
		public $msg_date;

		/**
		 * {@inheritDoc}
		 * @param \yii\queue\Queue $queue
		 *
		 * @return false
		 *
		 * @throws \Exception
		 */
		public function execute ($queue)
		{
			if (!empty($this->config_id) && !empty($this->index_buf) && !empty($this->sdk_file_id) && !empty($this->file_name) && !empty($this->file_type) && !empty($this->media_id) && !empty($this->msg_date)) {
				$indexbuf  = rawurlencode($this->index_buf);
				$sdkFileId = rawurlencode($this->sdk_file_id);
				$fileNme   = rawurlencode($this->file_name);
				$fileType  = rawurlencode($this->file_type);
				$url       = "http://localhost:18080/msgaudit/mediaget?cnfId={$this->config_id}&indexbuf={$indexbuf}&fid={$sdkFileId}&fname={$fileNme}&ftype={$fileType}";
				$curl      = new Curl();
				$response  = $curl->setOptions([
					CURLOPT_TCP_KEEPALIVE => "1L",
					CURLOPT_TCP_KEEPIDLE  => "120L",
					CURLOPT_TCP_KEEPINTVL => "60L",
					CURLOPT_TIMEOUT       => 0,
				])->get($url);

				if ($curl->responseCode == 200) {
					$result = Json::decode($response, true);

					if ($result['error'] == 0) {
						$mediaData = [];
						switch ($this->file_type) {
							case WorkMsgAuditInfoImage::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoImage::findOne($this->media_id);
								break;
							case WorkMsgAuditInfoVoice::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoVoice::findOne($this->media_id);
								break;
							case WorkMsgAuditInfoVideo::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoVideo::findOne($this->media_id);
								break;
							case WorkMsgAuditInfoEmotion::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoEmotion::findOne($this->media_id);
								break;
							case WorkMsgAuditInfoFile::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoFile::findOne($this->media_id);
								break;
							case WorkMsgAuditInfoMeetingVoiceCall::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoMeetingVoiceCall::findOne($this->media_id);
								break;
							case WorkMsgAuditInfoVoipDocShare::MSG_TYPE:
								$mediaData = WorkMsgAuditInfoVoipDocShare::findOne($this->media_id);
								break;
						}

						if (!empty($mediaData)) {
							try {
								SUtils::saveMsgAuditFile($mediaData->auditInfo->audit->corp->userCorpRelations[0]->uid, $this->file_name, $result['file'], $this->msg_date, FILE_APPEND);

								/** @var WorkMsgAuditInfoImage|WorkMsgAuditInfoVoice|WorkMsgAuditInfoVideo|WorkMsgAuditInfoEmotion|WorkMsgAuditInfoFile|WorkMsgAuditInfoMeetingVoiceCall|WorkMsgAuditInfoVoipDocShare $mediaData */
								$mediaData->indexbuf  = !empty($result['indexbuf']) ? $result['indexbuf'] : '';
								$mediaData->is_finish = $result['is_finish'];

								if ($mediaData->dirtyAttributes) {
									if (!$mediaData->validate() || !$mediaData->save()) {
										throw new InvalidDataException(SUtils::modelError($mediaData));
									}

									if (!empty($mediaData->indexbuf) && $mediaData->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
										\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
											'config_id'   => $this->config_id,
											'index_buf'   => $mediaData->indexbuf,
											'sdk_file_id' => $this->sdk_file_id,
											'file_name'   => $this->file_name,
											'file_type'   => $this->file_type,
											'media_id'    => $this->media_id,
											'msg_date'    => $this->msg_date,
										]));
									}

								}
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), __CLASS__ . ':mediaSave');

								return false;
							}
						} else {
							$data = [
								'config_id'   => $this->config_id,
								'index_buf'   => $this->index_buf,
								'sdk_file_id' => $this->sdk_file_id,
								'file_name'   => $this->file_name,
								'file_type'   => $this->file_type,
								'media_id'    => $this->media_id,
								'msg_date'    => $this->msg_date,
							];
							\Yii::error($data, __CLASS__ . ':getMediaFail');

							return false;
						}
					} else {
						$data = [
							'config_id'   => $this->config_id,
							'index_buf'   => $this->index_buf,
							'sdk_file_id' => $this->sdk_file_id,
							'file_name'   => $this->file_name,
							'file_type'   => $this->file_type,
							'media_id'    => $this->media_id,
							'msg_date'    => $this->msg_date,
						];
						\Yii::error($data, __CLASS__ . ':getMediaFromFail');
						\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob($data));

						return false;
					}
				}
			} else {
				return false;
			}
		}
	}