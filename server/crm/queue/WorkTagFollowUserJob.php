<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/2/14
	 * Time: 15:14
	 */

	namespace app\queue;

	use app\models\WorkTag;
	use app\models\WorkTagFollowUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkTagFollowUserJob extends BaseObject implements JobInterface
	{

		public $followIds;

		public function execute ($queue)
		{
			//标签移动分组时 给对应的客户重新打上标签
			\Yii::error($this->followIds, 'followIds');
			try {
				$tagFollow = WorkTagFollowUser::find()->where(['id' => $this->followIds])->all();
				if (!empty($tagFollow)) {
					$tagIds      = [];
					$externalIds = [];
					/** @var WorkTagFollowUser $follow */
					foreach ($tagFollow as $follow) {
						array_push($externalIds, $follow->follow_user_id);
						array_push($tagIds, $follow->tag_id);
						$follow->status = 0;
						$follow->save();
					}
					WorkTag::addUserTag(2, $externalIds, $tagIds,[],1);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WorkTagFollowUser');
			}

		}

	}