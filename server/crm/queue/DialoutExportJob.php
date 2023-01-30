<?php

	namespace app\queue;

	use app\models\WorkPublicActivity;
	use app\util\WebsocketUtil;
	use moonland\phpexcel\Excel;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use ZipArchive;

	class DialoutExportJob extends BaseObject implements JobInterface
	{
		public $exportData;
		public $headers;
		public $uid;
		public $corpId;
		public $fileName;

		public function execute ($queue)
		{
			\Yii::error(11111, 'DialoutExport-111111');
			try {
				$save_dir = \Yii::getAlias('@upload') . '/exportfile/' . date('Ymd') . '/';
                if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
                    return ['error' => 1, 'msg' => '无法创建目录'];
                }
				$fileName = $this->fileName ?: '呼叫统计' . '_' . date("YmdHis", time());
				$columns  = array_keys($this->headers);
				$num      = count($this->exportData);
				$this->sendWebsocket('', $num, 0);
                Excel::export([
                    'models'       => $this->exportData,//数库
                    'fileName'     => $fileName,//文件名
                    'savePath'     => $save_dir,//下载保存的路径
                    'asAttachment' => true,//是否下载
                    'columns'      => $columns,//要导出的字段
                    'headers'      => $this->headers
                ]);
                $url = \Yii::$app->params["site_url"] . '/upload/exportfile/' . date('Ymd') . '/' . $fileName . '.xlsx';
				\Yii::error($num, 'DialoutExport-111111');
				\Yii::error($url, 'DialoutExport-url');
				$this->sendWebsocket($url, $num, $num);
			} catch (\Exception $e) {
				\Yii::error($e->getLine(), 'DialoutExport-111111');
				\Yii::error($e->getFile(), 'DialoutExport-111111');
				\Yii::error($e->getMessage(), 'DialoutExport-111111');

			}


		}

		public function sendWebsocket ($url, $num, $export_num)
		{
			\Yii::$app->websocket->send([
				'channel' => 'push-message',
				'to'      => $this->uid,
				'type'    => WebsocketUtil::EXPORT_EXTERNAL_TYPE,
				'info'    => [
					'type'       => 'export_work_user_statistic',
					'from'       => $this->uid,
					'corpid'     => $this->corpId,
					'num'        => $num,
					'export_num' => $export_num,
					'url'        => $url,
				]
			]);
		}
	}