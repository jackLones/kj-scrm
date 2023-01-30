<?php

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\AuthStore;
	use app\models\AuthStoreGroup;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class AuthStoreExeclAddJob extends BaseObject implements JobInterface
	{
		public $import;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$success = 0;
			$error   = 0;
			$error1  = 0;
			$error2  = 0;
			$error3  = 0;
			$error4  = 0;
			try {
				$num   = count($this->import["importData"]) - 6;
				$Group = AuthStoreGroup::findOne(["corp_id" => $this->import["corp_id"], "name" => "未分组"]);
				foreach ($this->import["importData"] as $key => $datum) {
					try {
						$pidAll = [];
						if ($key <= 6) {
							continue;
						}
						$datum["A"] = str_replace(" ", "", $datum["A"]);
						$authStoreCount = AuthStore::find()->where(["corp_id" => $this->import["corp_id"], "shop_name" => $datum["A"], "address" => $datum["B"]])->count();
						if ($authStoreCount > 0) {
							++$error;
							++$error1;
							$str = "尚有" . ($num - ($error + $success)) . "家待导入；已成功导入" . $success . "家门店；失败" . $error . "家门店其中" . $error3 . "家因地址格式不正确" . $error1 . "家门店名称已存在、" . $error2 . "家导入的分组不存在及" . $error4 . "其他错误原因";
							$this->sendWebsocket($success, $pidAll, $error, $num, $this->import["uid"], $this->import["corp_id"], 0, $str);
							continue;
						}
						$StoreModel = new AuthStore();
						if (empty($datum["C"])) {
							$StoreModel->group_id = $Group->id;
						} else {
							$GroupModel = AuthStoreGroup::findOne(["corp_id" => $this->import["corp_id"], "name" => $datum["C"]]);
							if (!empty($GroupModel)) {
								$StoreModel->group_id = $GroupModel->id;
								$pidAll               = !empty($GroupModel->parent_ids) ? array_values(explode(",", $GroupModel->parent_ids)) : [$GroupModel->id];
							} else {
								++$error;
								++$error2;
								$str = "尚有" . ($num - ($error + $success)) . "家待导入；已成功导入" . $success . "家门店；失败" . $error . "家门店其中" . $error3 . "家因地址格式不正确" . $error1 . "家门店名称已存在、" . $error2 . "家导入的分组不存在及" . $error4 . "其他错误原因";
								$this->sendWebsocket($success, $pidAll, $error, $num, $this->import["uid"], $this->import["corp_id"], 0, $str);
								continue;
							}
						}
						$StoreModel->uid     = $this->import["uid"];
						$StoreModel->corp_id = $this->import["corp_id"];
						$returnAddress       = AuthStore::GiveAddressReturnLngAndLat($datum["B"]);
						if (!empty($returnAddress)) {
							$StoreModel->province = $returnAddress["address_components"]["province"];
							$StoreModel->city     = $returnAddress["address_components"]["city"];
							$StoreModel->district = $returnAddress["address_components"]["district"];
							$StoreModel->lat      = "{$returnAddress["location"]["lat"]}";
							$StoreModel->lng      = "{$returnAddress["location"]["lng"]}";
						} else {
							++$error;
							++$error3;
							$str = "尚有" . ($num - ($error + $success)) . "家待导入；已成功导入" . $success . "家门店；失败" . $error . "家门店其中" . $error3 . "家因地址格式不正确" . $error1 . "家门店名称已存在、" . $error2 . "家导入的分组不存在及" . $error4 . "其他错误原因";
							$this->sendWebsocket($success, $pidAll, $error, $num, $this->import["uid"], $this->import["corp_id"], 0, $str);
							continue;
						}
						$StoreModel->status    = 1;
						$StoreModel->address   = $datum["B"];
						$StoreModel->shop_name = $datum["A"];
						if (!$StoreModel->validate() || !$StoreModel->save()) {
							\Yii::error(SUtils::modelError($StoreModel));
							++$error;
							++$error4;
							$str = "尚有" . ($num - ($error + $success)) . "家待导入；已成功导入" . $success . "家门店；失败" . $error . "家门店其中" . $error3 . "家因地址格式不正确" . $error1 . "家门店名称已存在、" . $error2 . "家导入的分组不存在及" . $error4 . "其他错误原因";
							$this->sendWebsocket($success, $pidAll, $error, $num, $this->import["uid"], $this->import["corp_id"], 0, $str);
							continue;
						}
						++$success;
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), '$returnAddress');
						\Yii::error($e->getLine(), '$returnAddress');
						++$error;
						++$error4;
					}
					$str = "尚有" . ($num - ($error + $success)) . "家待导入；已成功导入" . $success . "家门店；失败" . $error . "家门店其中" . $error3 . "家因地址格式不正确" . $error1 . "家门店名称已存在、" . $error2 . "家导入的分组不存在及" . $error4 . "其他错误原因";

					$this->sendWebsocket($success, $pidAll, $error, $num, $this->import["uid"], $this->import["corp_id"], 1, $str);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'error-msg');
				\Yii::error($e->getLine(), 'error-msg');
			}
		}

		public function sendWebsocket ($success, $pidAll, $error, $num, $uid, $corp_id, $hair, $str = '')
		{
			\Yii::$app->websocket->send([
				'channel' => 'push-message',
				'to'      => $uid,
				'type'    => 'export_store_add',
				'info'    => [
					'type'      => 'export_store_add',
					'from'      => $uid,
					'corpid'    => $corp_id,
					'error_msg' => $str,
					'success'   => $success,
					'error'     => $error,
					'num'       => $num,
					'pid_all'   => $pidAll,
					'hair'      => $hair,
				]
			]);
		}
	}