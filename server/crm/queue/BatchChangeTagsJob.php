<?php
	/**
	 * Create by PhpStorm
	 * User: BeenLee
	 * Date: 2021/01/09
	 * Time: 09:25
	 */

	namespace app\queue;

	use app\models\WorkTag;
	use app\models\WorkTaskTag;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\Tag;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	/**
	 * 批量改变用户标签
	 * File: queue/BatchChangeTagsJob.php
	 * Class: BatchChangeTagsJob
	 *
	 * User: BeenLee
	 * Date: 2021/1/11 1:37 下午
	 *
	 * @package app\queue
	 *
	 */
	class BatchChangeTagsJob extends BaseObject implements JobInterface
	{
		public $hasTagFollowUsers;
		public $needTagFollowUsers;
		public $notNeedFollowUsers;
		public $corp_id;
		public $uid;
		public $tag_id;
		public $type;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error('hasTagFollowUsers:' . json_encode($this->hasTagFollowUsers), 'BatchChangeTagsJob');
			\Yii::error('needTagFollowUsers:' . json_encode($this->needTagFollowUsers), 'BatchChangeTagsJob');
			\Yii::error('notNeedFollowUsers:' . json_encode($this->notNeedFollowUsers), 'BatchChangeTagsJob');

			if (in_array($this->type, [0, 1, 3, 4, 5], true)) {
				//需要打标签
				if (!empty($this->needTagFollowUsers)) {
					$needTagFollowUser = array_column($this->needTagFollowUsers, 'key');
					try {
						$otherData = ['type' => 'auto_rule_tag'];
						WorkTag::addUserTag(2, $needTagFollowUser, [$this->tag_id], $otherData);
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), 'BatchChangeTagsJob');
					}
				}

				//需要取消标签
				if (!empty($this->notNeedFollowUsers)) {
					$notNeedFollowUser = array_column($this->notNeedFollowUsers, 'key');
					try {
						WorkTag::removeUserTag(2, $notNeedFollowUser, [$this->tag_id]);
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), 'BatchChangeTagsJob');
					}
				}
			}

			/*if ($this->type === 2) {
				//修改标签
				//同步标签到企业微信

			}

			if ($this->type === 3) {
				//删除标签同步到企业微信

			}*/

		}
	}