<?php

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\AuthStore;
	use app\models\AuthStoreUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class AuthStoreReName extends BaseObject implements JobInterface
	{
		/**员工数组**/
		public $storeId;
		public $corpId;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			try {
				$authStores = AuthStore::find()->where(["id" => $this->storeId, "corp_id" => $this->corpId])->all();
				/**@var $store AuthStore* */
				foreach ($authStores as $store) {
					$authStoreUsers = AuthStoreUser::find()->where(["store_id" => $store->id, "status" => 1])->all();
					/**@var $user AuthStoreUser* */
					foreach ($authStoreUsers as $user) {
						if (!empty($user->qc_url)) {
							$temp = \Yii::$app->basePath . $user->qc_url;
							//将渠道码生成到本地
							$file     = file_get_contents(\Yii::$app->basePath . $user->qc_url);
							$fileName = $store->shop_name . "-" . $user->user->name . "-" . rand(1, 10000) . time() . ".jpg";//定义图片名
							$save_dir = \Yii::getAlias('@upload') . '/store/' . date('Ymd') . '/';
							//创建保存目录
							if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
								throw new InvalidDataException("文件创建失败，权限不足");
							}
							file_put_contents($save_dir . $fileName, $file);
							$user->qc_url = "/upload/store/" . date('Ymd') . '/' . $fileName;
							$user->save();
							unlink($temp);
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error([$e->getMessage(), $e->getLine()], 'err-user' . date("Y-m-d H:i:s"));
			}
		}
	}