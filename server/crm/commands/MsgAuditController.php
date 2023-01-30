<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/6/5
	 * Time: 14:58
	 */

	namespace app\commands;

	use app\models\WorkMsgAudit;
	use linslin\yii2\curl\Curl;
	use yii\console\Controller;
	use yii\console\ExitCode;

	class MsgAuditController extends Controller
	{
		private $msgAuditUrl = "http://localhost:18080/msgaudit/";

		private function start ()
		{
			shell_exec(\Yii::getAlias("@app") . "/yii msg-audit/run > /dev/null 2>&1 &");
		}

		private function stop ()
		{
			echo "======== Start ========\n";
			// Main service
			exec("ps aux | grep \"crm/server/crm/yii msg-audit/run\" | grep -v \"grep\" | awk -F \" \" '{print $2}'", $runData, $runStatus);
			if (!empty($runData) && $runStatus == 0) {
				echo "Stop main service : " . $runData[0] . " start\n";
				shell_exec("kill -9 " . $runData[0]);
				echo "Stop main service: " . $runData[0] . " end\n";
			}

			// Execute service
			exec("ps aux | grep \"crm/server/crm/yii msg-audit/execute\" | grep -v grep | wc -l", $lineData, $lineStatus);

			if ($lineStatus == 0) {
				$lines = (int) $lineData[0];

				echo "======== Stop execute service start ========\n";
				for ($i = 1; $i <= $lines; $i++) {
					$runData   = [];
					$runStatus = 1;

					// 获取子进程的标识
					exec("ps aux | grep \"crm/server/crm/yii msg-audit/execute\" | grep -v \"grep\" | awk -F \" \" '{print $2}'", $runData, $runStatus);

					// 判断当前标识的进程是否存在
					if (!empty($runData) && $runStatus == 0) {
						echo "Stop the ". $i . " execute service : " . $runData[0] . " start\n";
						shell_exec("kill -9 " . $runData[0]);
						echo "Stop the ". $i . " execute service : " . $runData[0] . " end\n";
					}
				}
			}
			echo "======== Complete ========\n";
		}

		private function execute ()
		{
			$msgAuditData = WorkMsgAudit::findAll(['status' => WorkMsgAudit::MSG_AUDIT_OPEN]);

			if (!empty($msgAuditData)) {
				foreach ($msgAuditData as $msgAudit) {
					echo "======== Start ========\n";
					echo "Msg audit id: " . $msgAudit->id . "\n";
					$hasQueue   = false;
					$lineData   = [];
					$lineStatus = 1;

					// 查询当前一共执行了多少相应的队列
					exec("ps aux | grep \"crm/server/crm/yii msg-audit/execute\" | grep -v grep | wc -l", $lineData, $lineStatus);

					if ($lineStatus == 0) {
						$lines = (int) $lineData[0];
						for ($i = 1; $i <= $lines; $i++) {
							$runData   = [];
							$runStatus = 1;

							// 获取子进程的标识
							exec("ps aux | grep \"crm/server/crm/yii msg-audit/execute\" | grep -v \"grep\" | sed -n '" . $i . "p' | awk -F \" \" '{print $14}'", $runData, $runStatus);

							// 判断当前标识的进程是否存在
							if (!empty($runData) && $runStatus == 0 && $runData[0] == $msgAudit->id) {
								$hasQueue = true;

								break;
							}
						}
					}

					// 如果需要的子进程不存在，开启相应的子进程
					if (!$hasQueue) {
							shell_exec(\Yii::getAlias("@app") . "/yii msg-audit/execute " . $msgAudit->id . " " . $msgAudit->seq . " > /dev/null 2>&1 &");
					}

					echo "======== End ========\n";
				}
			}
		}

		/**
		 * This is msg audit main service, it will always to execute until you terminate it.
		 * @return int
		 */
		public function actionRun ()
		{
			exec("ps aux | grep \"crm/server/crm/yii msg-audit/run\" | grep -v grep | wc -l", $runLineData, $runLineStatus);
			if (!empty($runLineData) && $runLineStatus == 0 && (int) $runLineData[0] > 1) {
				return ExitCode::OK;
			}

			while (true) {
				$this->execute();
				sleep(5);
			}

			return ExitCode::OK;
		}

		/**
		 * This is execute service, to get work weixin msg audit.
		 *
		 * @param int $cnfId The msg audit config id.
		 * @param int $seq   The msg starting position.
		 *
		 * @throws \Exception
		 */
		public function actionExecute ($cnfId, $seq = 0)
		{
			$url  = $this->msgAuditUrl . "run?cnfId=${cnfId}&seq=${seq}";
			$curl = new Curl();
			$curl->setOptions([
				CURLOPT_TCP_KEEPALIVE => "1L",
				CURLOPT_TCP_KEEPIDLE  => "120L",
				CURLOPT_TCP_KEEPINTVL => "60L",
				CURLOPT_TIMEOUT       => 0,
			])->get($url);
		}

		/**
		 * Stop msg audit main service.
		 * @return int
		 *
		 */
		public function actionStop ()
		{
			$this->stop();

			return ExitCode::OK;
		}

		/**
		 * Restart msg audit main service.
		 * @return int
		 *
		 */
		public function actionRestart ()
		{
			$this->stop();
			$this->start();

			return ExitCode::OK;
		}
	}