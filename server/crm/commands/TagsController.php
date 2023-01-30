<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/23
	 * Time: 10:32
	 */

	namespace app\commands;

	use app\models\AuthoritySubUserStatistic;
	use app\models\Fans;
    use app\models\InspectionRemind;
    use app\models\PublicSeaReclaimSet;
	use app\models\RedPack;
	use app\models\WaitUserRemind;
	use app\models\WorkChat;
	use app\models\WorkContactWay;
	use app\models\WorkContactWayRedpacket;
	use app\models\WorkContactWayStatistic;
	use app\models\WorkExternalContactFollowStatistic;
	use app\models\WorkFollowMsg;
    use app\models\WorkMomentsBase;
    use app\models\WorkPublicActivity;
	use app\models\WorkPublicActivityStatistic;
	use app\models\WorkSop;
	use app\models\WorkUser;
	use app\models\SceneStatistic;
	use app\models\Tags;
	use app\models\WorkTag;
	use app\models\WorkUserCommissionRemind;
	use app\models\WorkUserDelFollowUserDetail;
	use yii\console\Controller;

	class TagsController extends Controller
	{
		/**
		 * 每日凌晨自动执行，获取标签在微信中的粉丝数
		 *
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public function actionGetFansCount ()
		{
			Tags::getFansCountByTagId();
		}

		//获取渠道二维码每日统计数据
		public function actionGetScene ()
		{
			SceneStatistic::create();
		}

		//获取渠道二维码每日统计数据
		public function actionGetSceneMonth ()
		{
			SceneStatistic::create(1);
		}

		//粉丝日统计
		public function actionGetFansDay ()
		{
			Fans::fans_statistic(1);
		}

		//粉丝周统计
		public function actionGetFansWeek ()
		{
			Fans::fans_statistic(2);
		}

		//粉丝月统计
		public function actionGetFansMonth ()
		{
			Fans::fans_statistic(3);
		}

		//成员日统计
		public function actionGetUserDay ()
		{
			WorkUser::getUserDayStatistic();
		}

		//成员首次统计
		public function actionGetUserDayFirst ()
		{
			WorkUser::getUserDayStatistic(1);
		}

		//企业标签及客户标签同步
		public function actionGetWorkTag ()
		{
			WorkTag::getWorkTagSync();
		}

		//每日整点更新活码成员
		public function actionUpdateContactWay ()
		{
			WorkContactWay::updateContactWay();
		}

		//渠道活码按天统计
		public function actionWorkContactWayStatisticDay ()
		{
			WorkContactWayStatistic::create(0);
		}

		//渠道活码按月统计
		public function actionWorkContactWayStatisticMonth ()
		{
			WorkContactWayStatistic::create(1);
		}

		//渠道活码按周统计
		public function actionWorkContactWayStatisticWeek ()
		{
			WorkContactWayStatistic::create(2);
		}

		//红包裂变
		public function actionRedPack ()
		{
			RedPack::syncRedPack();
		}

		//客户群日统计
		public function actionGetChatDay ()
		{
			WorkChat::getChatDayStatistic();
		}

		//每日跟进提醒
		public function actionGetFollowUser ()
		{
			WorkFollowMsg::getFollowUser();
		}

		/**
		 * 每日客户跟进数据
		 *
		 * @param string $startTime
		 */
		public function actionFollowStatisticDay ($startTime = '')
		{
			if (empty($startTime)) {
				$startTime = time();
			}

			WorkExternalContactFollowStatistic::followStatistic(1, $startTime);
		}

		/**
		 * 每周客户跟进数据
		 *
		 * @param string $startTime
		 */
		public function actionFollowStatisticWeek ($startTime = '')
		{
			if (empty($startTime)) {
				$startTime = time();
			}

			WorkExternalContactFollowStatistic::followStatistic(2, $startTime);
		}

		/**
		 * 每月客户跟进数据
		 *
		 * @param string $startTime
		 */
		public function actionFollowStatisticMonth ($startTime = '')
		{
			if (empty($startTime)) {
				$startTime = time();
			}

			WorkExternalContactFollowStatistic::followStatistic(3, $startTime);
		}

		//任务宝活动统计天
		public function actionWorkActivityStatisticDay ()
		{
			WorkPublicActivity::create(1);
		}

		//任务宝活动统计周
		public function actionWorkActivityStatisticWeek ()
		{
			WorkPublicActivity::create(2);
		}

		//任务宝活动统计月
		public function actionWorkActivityStatisticMonth ()
		{
			WorkPublicActivity::create(3);
		}

		/**
		 * 子权限活码范围数据统计每天
		 *
		 */
		public function actionSubScopeStatistics ()
		{
			AuthoritySubUserStatistic::setDataALL();
		}

		/**
		 * 子权限活码范围数据统计周
		 *
		 */
		public function actionSubScopeStatisticsWeek ()
		{
			AuthoritySubUserStatistic::setDataALL(2);
		}

		/**
		 * 子权限活码范围数据统计月
		 *
		 */
		public function actionSubScopeStatisticsMonth ()
		{
			AuthoritySubUserStatistic::setDataALL(1);
		}

		/**
		 * 每天早上9点发送员工删除
		 *
		 */
		public function actionUserDelFollowNine ()
		{
			WorkUserDelFollowUserDetail::sendTimingMessageDelFollowUser();

		}

		//每日客户回收
		public function actionReclaimCustomer ()
		{
			PublicSeaReclaimSet::reclaimCustomer();
		}

		//代办提醒凌晨重置
		public function actionUserRemindMessageZero()
		{
			WorkUserCommissionRemind::CreateZeroData();
		}
		//代办提醒每天
		public function actionUserRemindMessageDay()
		{
			WorkUserCommissionRemind::sendMessage('',strtotime(date("Y-m-d",time())),2);
		}

		//代办提醒每月
		public function actionUserRemindMessageMonth()
		{
			WorkUserCommissionRemind::sendMessage('',strtotime(date("Y-m",time())),3,3);
		}

		/**
		 * 每天早上9点发送待办事项
		 *
		 */
		public function actionWaitUserRemind ()
		{
			WaitUserRemind::getEveryDayData();
		}

		/**
		 * 每日整点更新红包拉新活码成员
		 */
		public function actionUpdateContactWayRedpacket ()
		{
			WorkContactWayRedpacket::updateContactWayRedpacket();
		}

		/**
		 * 每日0点更新红包拉新活码是否失效
		 */
		public function actionUpdateContactWayRedpacketStatus ()
		{
			WorkContactWayRedpacket::updateContactWayRedpacketStatus();
		}

		/**
         * 每天凌晨拉取昨天企业微信朋友圈数据
         */
		public function actionUpdateMoments()
        {
            $WorkMomentsBase = new WorkMomentsBase();
            $WorkMomentsBase->UpdateMoments();
        }

        /**
         * 每天凌晨拉取
         * 已拉取的不会再次拉取
         * 同步2020。1.1到现在的企业微信朋友圈数据
         */
        public function  actionUpdateMomentsAll()
        {
            $WorkMomentsBase = new WorkMomentsBase();
            $WorkMomentsBase->UpdateMomentsAll();
        }

		/**
		 * 每日0点设置SOP规则消息定时发送
		 */
		public function actionSopMsgSending ()
		{
			WorkSop::sopMsgSendingTime();
		}

        /**
         * 每天9点推送汇报数据
         * 质检汇报
         */
        public function actionTestingReport()
        {
            $InspectionRemind = new InspectionRemind();
            $InspectionRemind->getTestingReport();
        }

        /**
         * 每天9点10分推送汇报数据
         * 质检结果反馈
         */
        public function actionQualitySned()
        {
            $InspectionRemind = new InspectionRemind();
            $InspectionRemind->getQualitySned();
        }
	}