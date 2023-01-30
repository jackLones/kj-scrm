<?php

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\AuthStore;
	use app\models\AuthStoreUser;
	use app\models\WorkUser;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Matrix\Exception;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class AuthStoreUserJob extends BaseObject implements JobInterface
	{
		/**员工数组**/
		public $authUserId;
		/**员工数组**/
		public $storeAll;
		/**企业微信id**/
		public $corpId;
		/**门店id**/
		public $storeId;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			try {
				$workApi = WorkUtils::getWorkApi($this->corpId, 1);
				if (empty($this->storeId) && empty($this->storeAll)) {
					$StoreIds = [];
					foreach ($this->authUserId as $item) {
						$AuthUser = AuthStoreUser::findOne($item);
						if (!empty($AuthUser)) {
							$AuthStore = AuthStore::findOne($AuthUser->store_id);
							\Yii::error($AuthUser, '$AuthUser');
							if ($AuthUser->status == 0 && !empty($AuthUser->config_id)) {
								$workApi->ECDelContactWay($AuthUser->config_id);
								$AuthUser->config_id = NULL;
								$AuthUser->qc_url    = NULL;
							} else {
								$workUser = WorkUser::findOne($AuthUser->user_id);
								if ($workUser->is_external != 1) {
									$AuthUser->status = 0;
									$AuthUser->save();
									continue;
								}
								try {
									$contactWayInfo = [
										'type'        => 2,
										'scene'       => 2,
										'style'       => 1,
										'remark'      => '',
										'skip_verify' => true,
										'state'       => "store_" . $AuthUser->store_id,
										'user'        => [$workUser->userid],
										'party'       => [],
									];

									if (empty($AuthUser->config_id)) {
										$sendData            = ExternalContactWay::parseFromArray($contactWayInfo);
										$wayResult           = $workApi->ECAddContactWay($sendData);
										$AuthUser->config_id = $wayResult["config_id"];
										if (empty($AuthUser->qc_url)) {
											//将渠道码生成到本地
											$file     = file_get_contents($wayResult["qr_code"]);
											$fileName = $AuthStore->shop_name . "-" . $workUser->name . "-" . rand(1, 10000) . time() . ".jpg";//定义图片名
											$save_dir = \Yii::getAlias('@upload') . '/store/' . date('Ymd') . '/';
											//创建保存目录
											if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
												throw new InvalidDataException("文件创建失败，权限不足");
											}
											file_put_contents($save_dir . $fileName, $file);
											$AuthUser->qc_url = "/upload/store/" . date('Ymd') . '/' . $fileName;
										}
									} else {
										$contactWayInfo['config_id'] = $AuthUser->config_id;
										$sendData                    = ExternalContactWay::parseFromArray($contactWayInfo);
										$workApi->ECUpdateContactWay($sendData);
									}
								} catch (\Exception $e) {
									$message = $e->getMessage();
									if (strpos($message, '84074') !== false) {
										$message = '没有外部联系人权限';
									} elseif (strpos($message, '40096') !== false) {
										$message = '不合法的外部联系人userid';
									} elseif (strpos($message, '40098') !== false) {
										$message = '接替成员尚未实名认证';
									} elseif (strpos($message, '40001') !== false) {
										$message = '不合法的secret参数,请检查';
									} elseif (strpos($message, '41054') !== false) {
										$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
									} elseif (strpos($message, '-1') !== false) {
										$message = '系统繁忙，建议重试';
									}
									$AuthUser->status = 0;
									\Yii::error($message, 'err-user' . date("Y-m-d H:i:s"));
								}
							}
							$AuthUser->save();
							$StoreIds[] = $AuthUser->store_id;
						}
					}
					\Yii::error($StoreIds, '$contactWayInfo');
					foreach ($StoreIds as $storeId) {
						\Yii::$app->queue->push(new AuthStoreUserJob([
							'storeId' => $storeId,
							'corpId'  => $this->corpId
						]));
					}
				} else {
					if (!empty($this->storeAll)) {
						$authStores = AuthStore::find()->where(["corp_id" => $this->corpId])->all();
					} else {
						$authStores = AuthStore::find()->where(["id" => $this->storeId])->all();
					}
					\Yii::error($authStores, '$workUser');
					if (!empty($authStores)) {
						/**@var $authStore AuthStore* */
						foreach ($authStores as $authStore) {
							/**查询门店下所有人**/
							$workUser = AuthStoreUser::find()->alias("a")
								->leftJoin("{{%auth_store}} as b", "a.store_id = b.id")
								->leftJoin("{{%work_user}} as c", "a.user_id = c.id")
								->where(["b.corp_id" => $this->corpId, "c.is_external" => 1, "a.status" => 1, "a.store_id" => $authStore->id])
								->select("c.userid")->asArray()->all();
							if (empty($workUser)) {
								try {
									if (!empty($authStore->config_id)) {
										$workApi->ECDelContactWay($authStore->config_id);
									}
								} catch (Exception $exception) {
									\Yii::error($exception->getMessage(), '$authStore');
								}
								$authStore->config_id = NULL;
								$authStore->qc_url    = NULL;
								$authStore->save();
								$authUsers = AuthStoreUser::findAll(["store_id" => $authStore->id, "status" => 1]);
								/**@var $authUser AuthStoreUser* */
								foreach ($authUsers as $authUser) {
									if (!empty($authUser->config_id)) {
										try {
											$workApi->ECDelContactWay($authUser->config_id);
										} catch (Exception $exception) {
											\Yii::error($exception->getMessage(), '$authStore');
										}
										$authUser->status    = 0;
										$authUser->config_id = NULL;
										$authUser->qc_url    = NULL;
										$authUser->save();
									}
								}

								continue;
							}
							$workUserIds    = array_column($workUser, "userid");
							$contactWayInfo = [
								'type'        => 2,
								'scene'       => 2,
								'style'       => 1,
								'remark'      => '',
								'skip_verify' => true,
								'state'       => "store_" . $this->storeId,
								'user'        => $workUserIds,
								'party'       => [],
							];
							\Yii::error($contactWayInfo, '$contactWayInfo');
							$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
							if (empty($authStore->config_id)) {
								$wayResult            = $workApi->ECAddContactWay($sendData);
								$authStore->config_id = $wayResult["config_id"];
								if (empty($authStore->qc_url)) {
									//将渠道码生成到本地
									$file     = file_get_contents($wayResult["qr_code"]);
									$fileName = $authStore->shop_name . "-" . rand(1, 10000) . time() . ".jpg";//定义图片名
									$save_dir = \Yii::getAlias('@upload') . '/store/' . date('Ymd') . '/';
									//创建保存目录
									if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
										throw new InvalidDataException("文件创建失败，权限不足");
									}
									file_put_contents($save_dir . $fileName, $file);
									$authStore->qc_url = "/upload/store/" . date('Ymd') . '/' . $fileName;
								}
							} else {
								$contactWayInfo['config_id'] = $authStore->config_id;
								$sendData                    = ExternalContactWay::parseFromArray($contactWayInfo);
								$workApi->ECUpdateContactWay($sendData);
							}
							$authStore->save();
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error([$e->getMessage(), $e->getLine()], 'err-user' . date("Y-m-d H:i:s"));

			}
		}
	}