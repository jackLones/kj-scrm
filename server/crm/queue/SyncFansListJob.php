<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/6
	 * Time: 16:32
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\Fans;
	use app\models\WxAuthorizeInfo;
	use yii\base\BaseObject;
	use yii\helpers\ArrayHelper;
	use yii\queue\JobInterface;

	class SyncFansListJob extends BaseObject implements JobInterface
	{
		/** @var WxAuthorizeInfo */
		public $wxAuthorInfo;
		/** @var String */
		public $nextOpenid;

		/**
		 * @param \yii\queue\Queue $queue
		 *
		 * @return bool|mixed|void
		 */
		public function execute ($queue)
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);

			try {
				$this->syncFansList($this->nextOpenid);
			} catch (\Exception $e) {
				return false;
			}
		}

		/**
		 * @param $nextOpenid
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public function syncFansList ($nextOpenid)
		{
			// 获取粉丝 openid 列表
			$fansList = Fans::getFansList($this->wxAuthorInfo->authorizer_appid, $nextOpenid);

			if (!empty($fansList)) {
				$newNextOpenid = NULL;
				if ($fansList['count'] == Fans::MAX_USER_LIST) {
					$newNextOpenid = $fansList['next_openid'];
				}

				// 根据粉丝的 openid 刷新粉丝信息
				$openid = $fansList['data']['openid'];
				unset($fansList);
				$openidData[] = $openid;
				if (count($openid) > Fans::MAX_GET_USER_INFO) {
					$openidData = array_chunk($openid, Fans::MAX_GET_USER_INFO, true);
				}

				$length = count($openidData);
				for ($i = 0; $i < $length; $i++) {
					$fansInfoJobId = \Yii::$app->queue->push(new SyncFansInfoJob([
						'wxAuthorInfo' => $this->wxAuthorInfo,
						'openid'       => $openidData[$i]
					]));
				}

				if (!empty($newNextOpenid)) {
					$fansListJobId = \Yii::$app->queue->push(new SyncFansListJob([
						'wxAuthorInfo' => $this->wxAuthorInfo,
						'nextOpenid'   => $newNextOpenid
					]));
				}
			}
		}
	}