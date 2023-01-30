<?php
	/**
	 * Create by PhpStorm
	 * User: BeenLee
	 * Date: 2021/01/09
	 * Time: 09:25
	 */

	namespace app\queue;

	use app\models\WorkTaskTag;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	/**
	 * 根据条件打标签
	 * File: queue/ChangeUserAndChangeTagsJob.php
	 * Class: TaggingFromConditionsJob
	 *
	 * User: BeenLee
	 * Date: 2021/1/11 1:38 下午
	 *
	 * @package app\queue
	 *
	 */
	class ChangeUserAndChangeTagsJob extends BaseObject implements JobInterface
	{
		public $type;//操作类型：4-新增用户，5-用户属性变更
		public $external_id; //用户id
		public $corp_id;
		public $uid;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			try {
				\Yii::error('external_id:' . $this->external_id, 'ChangeUserAndChangeTagsJob');

				//查询所有条件标签
				$workTaskTags = WorkTaskTag::findAll(['corp_id' => $this->corp_id]);
				if ($workTaskTags) {
					foreach ($workTaskTags as $workTaskTag) {
						$date = $workTaskTag->getTagByUser($this->external_id, $this->uid, $this->type);
						\Yii::error($date, 'ChangeUserAndChangeTagsJob');

						//判断是否未打标签 未打则打标签
						if ($date) {
							$date['corp_id'] = $this->corp_id;
							$date['uid']     = $this->uid;
							$date['type']    = $this->type;
							$date['tag_id']  = $workTaskTag->tag_id;
							\Yii::$app->queue->push(new BatchChangeTagsJob($date));
						}
					}
				}

				return true;
			} catch (\Exception $e) {
				return false;
			}
		}
	}