<?php

	namespace app\controllers;

	use app\components\ForbiddenException;
	use app\components\InvalidDataException;
	use app\controllers\common\BaseController;
	use app\models\AuthoritySubUserDetail;
	use app\models\AuthoritySubUserStatistic;
	use app\models\WorkCorp;
	use app\models\WorkDepartment;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkMsgAuditInfo;
	use app\models\WorkMsgAuditInfoAgree;
	use app\models\WorkMsgAuditInfoCalendar;
	use app\models\WorkMsgAuditInfoCard;
	use app\models\WorkMsgAuditInfoChatrecord;
	use app\models\WorkMsgAuditInfoCollect;
	use app\models\WorkMsgAuditInfoDocmsg;
	use app\models\WorkMsgAuditInfoEmotion;
	use app\models\WorkMsgAuditInfoFile;
	use app\models\WorkMsgAuditInfoImage;
	use app\models\WorkMsgAuditInfoLink;
	use app\models\WorkMsgAuditInfoLocation;
	use app\models\WorkMsgAuditInfoMarkdown;
	use app\models\WorkMsgAuditInfoMeeting;
	use app\models\WorkMsgAuditInfoMixed;
	use app\models\WorkMsgAuditInfoNews;
	use app\models\WorkMsgAuditInfoRedpacket;
	use app\models\WorkMsgAuditInfoRevoke;
	use app\models\WorkMsgAuditInfoText;
	use app\models\WorkMsgAuditInfoTodo;
	use app\models\WorkMsgAuditInfoVideo;
	use app\models\WorkMsgAuditInfoVoice;
	use app\models\WorkMsgAuditInfoVote;
	use app\models\WorkMsgAuditInfoWeapp;
	use app\models\WorkUser;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;
	use yii\filters\AccessControl;
	use yii\filters\VerbFilter;
	use yii\helpers\ArrayHelper;
	use yii\web\Response;

	class SiteController extends BaseController
	{
		/**
		 * {@inheritdoc}
		 */
		public function behaviors ()
		{
			return ArrayHelper::merge(parent::behaviors(), [
				'access' => [
					'class' => AccessControl::className(),
					'only'  => ['logout'],
					'rules' => [
						[
							'actions' => ['logout'],
							'allow'   => true,
							'roles'   => ['@'],
						],
					],
				],
				'verbs'  => [
					'class'   => VerbFilter::className(),
					'actions' => [
						'logout' => ['post'],
					],
				],
			]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function actions ()
		{
			return [
				'error'   => [
					'class' => 'yii\web\ErrorAction',
				],
				'captcha' => [
					'class'           => 'yii\captcha\CaptchaAction',
					'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : NULL,
				],
			];
		}

		/**
		 * Displays homepage.
		 *
		 * @return string
		 */
		public function actionIndex ()
		{
			return $this->redirect('http://www.51lick.com');
		}

		/**
		 * Login action.
		 *
		 * @return Response|string
		 */
		public function actionLogin ()
		{
			return $this->redirect('http://www.51lick.com');
		}

		/**
		 * Logout action.
		 *
		 * @return Response
		 */
		public function actionLogout ()
		{
			Yii::$app->user->logout();

			return $this->goHome();
		}

		/**
		 * Displays contact page.
		 *
		 * @return Response|string
		 */
		public function actionContact ()
		{
			return $this->redirect('http://www.51lick.com');
		}

		/**
		 * Displays about page.
		 *
		 * @return string
		 */
		public function actionAbout ()
		{
			return $this->redirect('http://www.51lick.com');
		}

		public function actionSocketTest ()
		{
			Yii::$app->websocket->send([
				'channel' => 'server-message',
				'info'    => [
					'type'    => 'system',
					'from'    => 'Server',
					'message' => '用户 xxx 加入群聊！'
				]
			]);
		}

		public function actionExpire ()
		{
			throw new ForbiddenException('未授权，请联系管理员进行授权。');
		}

		public function actionInitMsg ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$count = WorkMsgAuditInfo::find()->where(['roomid' => NULL, 'to_type' => 0])->count();
			$sum   = ceil($count / 5000);
			for ($i = 0; $i < $sum; $i++) {
				$msg = WorkMsgAuditInfo::find()->where(['roomid' => NULL, 'to_type' => 0])->limit(5000)->offset($i)->orderBy(['id' => SORT_DESC])->all();

				if (!empty($msg)) {
					/** @var WorkMsgAuditInfo $info */
					foreach ($msg as $info) {
						try {
							$corpId = $info->audit->corp_id;
							switch (SUtils::getUserType($info->tolist)) {
								case SUtils::IS_WORK_USER:
									$info->to_type = WorkMsgAuditInfo::IS_WORK_USER;

									$workUserId = WorkUser::getUserId($corpId, $info->tolist);
									if (!empty($workUserId)) {
										$info->to_user_id = $workUserId;
									}

									break;
								case SUtils::IS_EXTERNAL_USER:
									$info->to_type = WorkMsgAuditInfo::IS_EXTERNAL_USER;

									$externalId = WorkExternalContact::getExternalId($corpId, $info->tolist, true);
									if (!empty($externalId)) {
										$info->to_external_id = $externalId;
									}

									break;
								case SUtils::IS_ROBOT_USER:
									$info->to_type = WorkMsgAuditInfo::IS_ROBOT_USER;

									break;
								default:

									break;
							}
							if (!$info->validate() || !$info->save()) {
								throw new InvalidDataException(SUtils::modelError($info));
							}
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'initMsg');
						}
					}
				}
			}
		}

		public function actionInitMsgContent ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$count = WorkMsgAuditInfo::find()->where(['content' => NULL])->count();
			$sum   = ceil($count / 5000);
			for ($i = 0; $i < $sum; $i++) {
				$msg = WorkMsgAuditInfo::find()->where(['content' => NULL])->limit(5000)->offset($i)->orderBy(['id' => SORT_DESC])->all();

				if (!empty($msg)) {
					/** @var WorkMsgAuditInfo $info */
					foreach ($msg as $info) {
						switch ($info->msgtype) {
							case WorkMsgAuditInfoText::MSG_TYPE:
								if (!empty($info->workMsgAuditInfoTexts)) {
									$info->content = $info->workMsgAuditInfoTexts[0]->content;
								} else {
									$info->content = '文本';
								}

								break;
							case WorkMsgAuditInfoImage::MSG_TYPE:
								$info->content = '图片';

								break;
							case WorkMsgAuditInfoRevoke::MSG_TYPE:
								$info->content = '撤回';

								break;
							case WorkMsgAuditInfoAgree::AGREE_MSG_TYPE:
								$info->content = '同意会话存档';

								break;
							case WorkMsgAuditInfoAgree::DISAGREE_MSG_TYPE:
								$info->content = '拒绝会话存档';

								break;
							case WorkMsgAuditInfoVoice::MSG_TYPE:
								$info->content = '语音';

								break;
							case WorkMsgAuditInfoVideo::MSG_TYPE:
								$info->content = '视频';

								break;
							case WorkMsgAuditInfoCard::MSG_TYPE:
								$info->content = '名片';

								break;
							case WorkMsgAuditInfoLocation::MSG_TYPE:
								$info->content = '位置';

								break;
							case WorkMsgAuditInfoEmotion::MSG_TYPE:
								$info->content = '表情';

								break;
							case WorkMsgAuditInfoFile::MSG_TYPE:
								$info->content = '文件';

								break;
							case WorkMsgAuditInfoLink::MSG_TYPE:
								$info->content = '链接';

								break;
							case WorkMsgAuditInfoWeapp::MSG_TYPE:
								$info->content = '小程序';

								break;
							case WorkMsgAuditInfoChatrecord::MSG_TYPE:
								$info->content = '聊天记录';

								break;
							case WorkMsgAuditInfoTodo::MSG_TYPE:
								$info->content = '待办';

								break;
							case WorkMsgAuditInfoVote::MSG_TYPE:
								$info->content = '投票';

								break;
							case WorkMsgAuditInfoCollect::MSG_TYPE:
								$info->content = '填表';

								break;
							case WorkMsgAuditInfoRedpacket::MSG_TYPE:
								$info->content = '红包';

								break;
							case WorkMsgAuditInfoMeeting::MSG_TYPE:
								$info->content = '会议';

								break;
							case WorkMsgAuditInfoDocmsg::MSG_TYPE:
								$info->content = '在线文档';

								break;
							case WorkMsgAuditInfoMarkdown::MSG_TYPE:
								$info->content = 'MarkDown';

								break;
							case WorkMsgAuditInfoNews::MSG_TYPE:
								$info->content = '图文';

								break;
							case WorkMsgAuditInfoCalendar::MSG_TYPE:
								$info->content = '日程';

								break;
							case WorkMsgAuditInfoMixed::MSG_TYPE:
								$info->content = '混合';

								break;
							default:
								break;
						}
						$info->save();
					}
				}
			}
		}

		public function actionInitWorkState ()
		{
			$workCorpInfo = WorkCorp::find()->where(['or', ['state' => ''], ['state' => NULL]])->all();

			if (!empty($workCorpInfo)) {
				/** @var WorkCorp $workCorp */
				foreach ($workCorpInfo as $workCorp) {
					$workCorp->state = WorkUtils::getCorpState();
					$workCorp->save();
				}
			}
		}

		public function actionSymSubDetailDay ()
		{
			AuthoritySubUserStatistic::setDataHistoryDay();
		}

		public function actionSymSubDetailWeek ()
		{
			AuthoritySubUserStatistic::setDataHistoryWeek();
		}

		public function actionSymSubDetailMonth ()
		{
			AuthoritySubUserStatistic::setDataHistoryMonth();
		}

		public function actionSymSubDetailLists ()
		{
			AuthoritySubUserDetail::createHistory();
		}

		public function actionSymDelRepetitionData ()
		{

		}
	}
