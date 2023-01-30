<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/3
	 * Time: 18:40
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\WorkChat;
	use app\models\WorkCorp;
	use app\models\WorkCorpAgent;
	use app\models\WorkCorpAuth;
	use app\models\WorkCorpBind;
	use app\models\WorkDismissUserDetail;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkSuiteConfig;
	use app\models\WorkUser;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkUserJob extends BaseObject implements JobInterface
	{
		public $xml;
		public $from = WorkUtils::FROM_SERVICE;

		public function execute ($queue)
		{
			$userInfoData = SUtils::Xml2Array($this->xml);
			\Yii::error($userInfoData,'$userInfoData');
			SUtils::arrayCase($userInfoData);
			if ($this->from == WorkUtils::FROM_SERVICE) {
				$workSuiteConfig = WorkSuiteConfig::findOne(['suite_id' => $userInfoData['suiteid']]);

				if (empty($workSuiteConfig)) {
					return false;
				}

				$authCorp = WorkCorp::findOne(['corpid' => $userInfoData['authcorpid']]);
				if (empty($authCorp) || (!empty($authCorp->workCorpBind) && $authCorp->workCorpBind->book_status == WorkCorpBind::BOOK_CLOSE)) {
					return false;
				}

				$authCorpAuth = WorkCorpAuth::findOne(['suite_id' => $workSuiteConfig->id, 'corp_id' => $authCorp->id]);

				if (empty($authCorpAuth)) {
					return false;
				}
			} elseif ($this->from == WorkUtils::FROM_AGENT) {
				$authCorp = WorkCorp::findOne(['corpid' => $userInfoData['tousername']]);

				$agentInfo = WorkCorpAgent::findOne(['corp_id' => $authCorp->id, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'is_del' => WorkCorpAgent::AGENT_NO_DEL]);

				if (empty($authCorp) || empty($agentInfo)) {
					return false;
				}
			} else {
				$authCorp = WorkCorp::findOne(['corpid' => $userInfoData['tousername']]);

				if (empty($authCorp) || empty($authCorp->workCorpBind) || $authCorp->workCorpBind->book_status == WorkCorpBind::BOOK_CLOSE) {
					return false;
				}
			}
			try {
				if ($userInfoData['changetype'] == WorkUser::DELETE_USER) {
					$workUser = WorkUser::findOne(['corp_id' => $authCorp->id, 'userid' => $userInfoData['userid']]);
					if (empty($workUser)) {
						return false;
					}

					$workUser->is_del         = WorkUser::USER_IS_DEL;
					$workUser->is_external    = WorkUser::IS_EXTERNAL;
					$workUser->dimission_time = time();
					$workUser->update();

					//将该成员的客户变为离职未分配状态
					$followUser = WorkExternalContactFollowUser::find()->alias('f')->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`');
					$followUser = $followUser->where(['f.user_id' => $workUser->id, 'c.corp_id' => $authCorp->id, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->all();
					if (!empty($followUser)) {
						/** @var WorkExternalContactFollowUser $user */
						foreach ($followUser as $user) {
							$user->del_type = WorkExternalContactFollowUser::NO_ASSIGN;
							$user->save();
						}
					}

					//离职成员明细表
					$followUser = WorkExternalContactFollowUser::find()->alias('f')->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`');
					$followUser = $followUser->where(['f.user_id' => $workUser->id, 'c.corp_id' => $authCorp->id])->all();
					if (!empty($followUser)) {
						/** @var WorkExternalContactFollowUser $user */
						foreach ($followUser as $user) {
							$data                    = [];
							$data['corp_id']         = $user->externalUser->corp_id;
							$data['user_id']         = $user->user_id;
							$data['external_userid'] = $user->external_userid;
							WorkDismissUserDetail::add($data, 1);
						}
					}
					$workChat = WorkChat::find()->where(['owner_id' => $workUser->id, 'corp_id' => $workUser->corp_id, 'group_chat' => 0])->all();
					if (!empty($workChat)) {
						/** @var WorkChat $chat */
						foreach ($workChat as $chat) {
							$data            = [];
							$data['corp_id'] = $chat->corp_id;
							$data['user_id'] = $chat->owner_id;
							$data['chat_id'] = $chat->id;
							WorkDismissUserDetail::add($data, 2);
						}
					}

				} else {
					$workUserId        = WorkUser::getUserSuite($authCorp->id, $userInfoData['userid']);
					$work_user         = WorkUser::findOne($workUserId);
					$work_user->is_del = WorkUser::USER_NO_DEL;
					$work_user->save();
				}
			} catch (InvalidDataException $e) {
				return false;
			}
		}
	}