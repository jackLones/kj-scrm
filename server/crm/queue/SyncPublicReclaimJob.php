<?php
	/**
	 * 客户回收设置
	 * User: xingchangyu
	 * Date: 2020/09/07
	 * Time: 13：00
	 */

	namespace app\queue;

	use app\models\PublicSeaClaimUser;
	use app\models\PublicSeaReclaimSet;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\components\InvalidDataException;

	class SyncPublicReclaimJob extends BaseObject implements JobInterface
	{
		public $reclaim_id;//回收规则id
		public $corp_id;//企业微信id
		public $sendData;//待发送消息数据
		public $claim_user_id;//成员客户认领表id

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			if (!empty($this->reclaim_id)) {//单条规则
				PublicSeaReclaimSet::dealData($this->reclaim_id);
			} elseif (!empty($this->sendData)) {//待发送消息数据
				PublicSeaReclaimSet::messageSend($this->sendData);
			} elseif (!empty($this->corp_id)) {//脚本走这里
				PublicSeaReclaimSet::syncDealData($this->corp_id);
			} elseif (!empty($this->claim_user_id)) {
				PublicSeaClaimUser::updateStatus($this->claim_user_id);
			}
		}
	}