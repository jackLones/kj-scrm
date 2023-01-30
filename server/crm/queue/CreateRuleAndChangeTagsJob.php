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
	 * 根据条件打标签-创建标签规则
	 * File: queue/CreateRuleAndChangeTagsJob.php
	 * Class: TaggingFromConditionsJob
	 *
	 * User: BeenLee
	 * Date: 2021/1/11 1:38 下午
	 *
	 * @package app\queue
	 *
	 */
	class CreateRuleAndChangeTagsJob extends BaseObject implements JobInterface
	{
		public $type;//操作类型 0-创建规则，1-修改规则，2-修改标签，3-删除标签
		public $param_id; //标签id
		public $corp_id;
		public $uid;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			try {
				\Yii::error('param_id:' . $this->param_id, 'CreateRuleAndChangeTagsJob');
				//查询所有条件标签
				$workTaskTag = WorkTaskTag::findOne(['corp_id' => $this->corp_id, 'id' => $this->param_id]);
				if ($workTaskTag) {
					$date = $workTaskTag->getUserTagByRule($this->uid);
					//判断是否未打标签 未打则打标签
					if ($date) {
						$date['corp_id'] = $this->corp_id;
						$date['uid']     = $this->uid;
						$date['tag_id']  = $workTaskTag->tag_id;
						\Yii::$app->queue->push(new BatchChangeTagsJob($date));
					}
				}

				return true;
			} catch (\Exception $e) {
				return false;
			}
		}
	}