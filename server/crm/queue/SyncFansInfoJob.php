<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/7
	 * Time: 12:58
	 */

	namespace app\queue;

	use app\models\Fans;
	use app\models\WxAuthorizeInfo;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncFansInfoJob extends BaseObject implements JobInterface
	{
		/** @var WxAuthorizeInfo */
		public $wxAuthorInfo;
		/** @var string|array */
		public $openid;

		public function execute ($queue)
		{
			try {
				$authorInfo = $this->wxAuthorInfo->author;

				foreach ($this->openid as $openid) {
					try {
						Fans::create($authorInfo->author_id, $openid);
					} catch (\Exception $e) {
						continue;
					}
				}

				$fansInfoData = Fans::getFansInfo($this->wxAuthorInfo->authorizer_appid, $this->openid);
				if (!empty($fansInfoData)) {
					foreach ($fansInfoData as $fansInfo) {
						try {
							Fans::create($authorInfo->author_id, $fansInfo['openid'], $fansInfo, 1);
						} catch (\Exception $e) {
							continue;
						}
					}
				}
			} catch (\Exception $e) {
				return false;
			}
		}
	}