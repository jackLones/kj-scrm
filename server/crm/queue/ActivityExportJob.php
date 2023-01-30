<?php

	namespace app\queue;

	use app\models\WorkPublicActivity;
	use app\util\WebsocketUtil;
	use moonland\phpexcel\Excel;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use ZipArchive;

	class ActivityExportJob extends BaseObject implements JobInterface
	{
		public $result;
		public $headers;
		public $uid;
		public $corpId;
		public $remark;
		public $STATE_NAME;

		public function execute ($queue)
		{
			\Yii::error(11111, 'sym-111111');
			try {
				$save_dir = \Yii::getAlias('@upload') . '/exportfile/' . date('Ymd') . '/';
				$zipName  = $this->remark . '_' . date("YmdHis", time()) . ".zip";
				$fileName = $this->remark . '_' . date("YmdHis", time());
				$columns  = array_keys($this->headers);
				$num      = count($this->result);
				$this->sendWebsocket('', $num, 0);
				if ($num > 5000) {
					$resData = array_chunk($this->result, 5000);
					$files   = [];
					$num     = count($resData);
					foreach ($resData as $key => $datum) {
						Excel::export([
							'models'       => $datum,//数库
							'fileName'     => $fileName . $key,//文件名
							'savePath'     => $save_dir,//下载保存的路径
							'asAttachment' => true,//是否下载
							'columns'      => $columns,//要导出的字段
							'headers'      => $this->headers
						]);
						array_push($files, $save_dir . $fileName . $key);
						$this->sendWebsocket('', $num, $key + 1);
					}
					$zip = new ZipArchive();
					touch($save_dir . $zipName);
					$res = $zip->open($save_dir . $zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
					if ($res == true) {
						foreach ($files as $key => $file) {
							\Yii::error($file, 'sym-111111');
							$zip->addFile($file . ".xlsx", $fileName . $key . ".xlsx");
						}
						//关闭文件
						$zip->close();
					}
					$url = \Yii::$app->params["site_url"] . '/upload/exportfile/' . date('Ymd') . '/' . $zipName;
				} else {
					Excel::export([
						'models'       => $this->result,//数库
						'fileName'     => $fileName,//文件名
						'savePath'     => $save_dir,//下载保存的路径
						'asAttachment' => true,//是否下载
						'columns'      => $columns,//要导出的字段
						'headers'      => $this->headers
					]);

					$url = \Yii::$app->params["site_url"] . '/upload/exportfile/' . date('Ymd') . '/' . $fileName . '.xlsx';
				}
				\Yii::error($num, 'sym-111111');
				$this->sendWebsocket($url, $num, $num);
			} catch (\Exception $e) {
				\Yii::error($e->getLine(), 'sym-111111');
				\Yii::error($e->getFile(), 'sym-111111');
				\Yii::error($e->getMessage(), 'sym-111111');

			}


		}

		public function sendWebsocket ($url, $num, $export_num)
		{
			\Yii::$app->websocket->send([
				'channel' => 'push-message',
				'to'      => $this->uid,
				'type'    => WebsocketUtil::EXPORT_EXTERNAL_TYPE,
				'info'    => [
					'type'       => 'export_' . $this->STATE_NAME,
					'from'       => $this->uid,
					'corpid'     => $this->corpId,
					'num'        => $num,
					'export_num' => $export_num,
					'url'        => $url,
				]
			]);
		}
	}