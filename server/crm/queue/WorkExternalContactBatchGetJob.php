<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/11/226
	 * Time: 11:24
	 */

	namespace app\queue;

	use app\models\WorkExternalContact;
	use app\models\WorkTag;
	use app\models\WorkTagGroup;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactBatchGetByUser;
	use dovechen\yii2\weWork\Work;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use yii\queue\redis\Queue;

	/**
	 * Class WorkExternalContactBatchGetJob
	 * @package app\queue
	 */
	class WorkExternalContactBatchGetJob extends BaseObject implements JobInterface
	{
		public $corp_id;
		public $user_id;
		public $cursor = '';
		public $limit = 100;

		/**
		 * @param \yii\queue\Queue $queue
		 *
		 * @return bool|mixed|void
		 *
		 * @throws \Throwable
		 */
		public function execute ($queue)
		{
			/** @var Work $workApi */
			$workApi = WorkUtils::getWorkApi($this->corp_id, WorkUtils::EXTERNAL_API);
			if (empty($workApi)) {
				return false;
			}

			$args         = ExternalContactBatchGetByUser::parseFromArray([
				'userid' => $this->user_id,
				'cursor' => $this->cursor,
				'limit'  => $this->limit,
			]);
			$externalList = $workApi->ECBatchGetByUser($args);

			if (!empty($externalList['external_contact_list'])) {
				foreach ($externalList['external_contact_list'] as $externalInfo) {
					try {
						$externalData = $externalInfo['external_contact'];
						$followInfo = $externalInfo['follow_info'];
						unset($externalInfo);

						$followInfo['userid'] = $this->user_id;

						if (!empty($followInfo['tag_id'])) {
							$tags     = [];
							$tagsData = WorkTag::findAll(['tagid' => $followInfo['tag_id']]);
							unset($followInfo['tag_id']);

							if (!empty($tagsData)) {
								$groupId = [];
								foreach ($tagsData as $tag) {
									$tagInfo = [
										'group_name' => '',
										'tag_name'   => $tag->tagname,
										'tag_id'     => $tag->tagid,
										'type'       => WorkTag::CORP_TAG,
										'group_id'   => $tag->group_id,
									];

									array_push($tags, $tagInfo);
									if (!in_array($tag->group_id, $groupId)) {
										array_push($groupId, $tag->group_id);
									}
								}

								$tagGroupData = WorkTagGroup::find()->select(['id', 'group_name'])->where(['id' => $groupId])->asArray()->all();
								if (!empty($tagGroupData)) {
									$tagGroupData = array_column($tagGroupData, 'group_name', 'id');

									foreach ($tags as $key => $tagInfo) {
										$tagInfo['group_name'] = $tagGroupData[$tagInfo['group_id']] ?: '';
										unset($tagInfo['group_id']);

										$tags[$key] = $tagInfo;
									}
								}
							}

							$followInfo['tags'] = $tags;
						}
						$followInfo['scrm_from'] = WorkUtils::BATCH_GET_BY_USER;

						$externalData['follow_user'] = [$followInfo];

						WorkExternalContact::setUser($this->corp_id, $externalData);
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), __CLASS__ . ':setUser');
					}
				}
			}

			if (!empty($externalList['next_cursor'])) {
				/** @var Queue $workQueue */
				$workQueue = \Yii::$app->work;
				$workQueue->push(new WorkExternalContactBatchGetJob([
					'corp_id' => $this->corp_id,
					'user_id' => $this->user_id,
					'cursor'  => $externalList['next_cursor'],
				]));
			}
		}
	}