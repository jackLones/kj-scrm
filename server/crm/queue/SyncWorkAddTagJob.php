<?php
	/**
	 * 给员工/客户添加标签
	 * User: xingchangyu
	 * Date: 2020/07/06
	 * Time: 17：00
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\WorkTag;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkAddTagJob extends BaseObject implements JobInterface
	{
		public $type;//1 通讯录 2 外部联系人
		public $user_ids;//用户 array
		public $tag_ids;//标签 array
		public $otherData;//其它数据 array

		public function execute ($queue)
		{
			try {
				WorkTag::addUserTag($this->type, $this->user_ids, $this->tag_ids, $this->otherData);
			} catch (InvalidDataException $e) {
				\Yii::error($e->getMessage(), 'SyncWorkAddTag');
			}
		}
	}