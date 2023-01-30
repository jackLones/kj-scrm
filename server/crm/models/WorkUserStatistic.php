<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%work_user_statistic}}".
	 *
	 * @property int    $id
	 * @property int    $corp_id               授权的企业ID
	 * @property string $userid                企业成员userid
	 * @property int    $new_apply_cnt         发起申请数
	 * @property int    $new_contact_cnt       新增客户数
	 * @property int    $negative_feedback_cnt 删除/拉黑成员的客户数
	 * @property int    $chat_cnt              聊天总数
	 * @property int    $message_cnt           发送消息数
	 * @property string $reply_percentage      已回复聊天占比
	 * @property string $avg_reply_time        平均首次回复时长(分钟)
	 * @property int    $time                  数据当日0点的时间戳
	 * @property string $data_time             统计时间 如2020-02-07
	 * @property string $create_time           创建日期
	 */
	class WorkUserStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'new_apply_cnt', 'new_contact_cnt', 'negative_feedback_cnt', 'chat_cnt', 'message_cnt', 'time'], 'integer'],
				[['create_time'], 'safe'],
				[['userid'], 'string', 'max' => 64],
				[['reply_percentage'], 'string', 'max' => 8],
				[['avg_reply_time', 'data_time'], 'string', 'max' => 16],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                    => Yii::t('app', 'ID'),
				'corp_id'               => Yii::t('app', '授权的企业ID'),
				'userid'                => Yii::t('app', '企业成员userid'),
				'new_apply_cnt'         => Yii::t('app', '发起申请数'),
				'new_contact_cnt'       => Yii::t('app', '新增客户数'),
				'negative_feedback_cnt' => Yii::t('app', '删除/拉黑成员的客户数'),
				'chat_cnt'              => Yii::t('app', '聊天总数'),
				'message_cnt'           => Yii::t('app', '发送消息数'),
				'reply_percentage'      => Yii::t('app', '已回复聊天占比'),
				'avg_reply_time'        => Yii::t('app', '平均首次回复时长(分钟)'),
				'time'                  => Yii::t('app', '数据当日0点的时间戳'),
				'data_time'             => Yii::t('app', '统计时间 如2020-02-07 '),
				'create_time'           => Yii::t('app', '创建日期'),
			];
		}

		/**
		 * 首页昨日数据总览
		 *
		 * @param corp_id int 企业微信号
		 *
		 * @return array
		 *
		 */
		public static function getWorkUserStatisticData ($corp_id,$userid=[])
		{
			$start1       = date('Y-m-d', strtotime('-1 day'));
			$start2       = date('Y-m-d', strtotime('-2 day'));
			$user_static1 = static::find()->select('sum(`new_apply_cnt`) new_apply_sum,sum(`new_contact_cnt`) new_contact_sum,sum(`negative_feedback_cnt`) negative_feedback_sum')->where(['corp_id' => $corp_id, 'data_time' => $start1])->groupBy('corp_id');
			$user_static2 = static::find()->select('sum(`new_apply_cnt`) new_apply_sum,sum(`new_contact_cnt`) new_contact_sum,sum(`negative_feedback_cnt`) negative_feedback_sum')->where(['corp_id' => $corp_id, 'data_time' => $start2])->groupBy('corp_id');
			if (!empty($userid)) {
				$user_static1 = $user_static1->andWhere(["in", "userid", $userid]);
				$user_static2 = $user_static2->andWhere(["in", "userid", $userid]);
			}
			$user_static1 = $user_static1->asArray()->all();
			$user_static2 = $user_static2->asArray()->all();
			$yester_new_apply_sum          = isset($user_static1[0]['new_apply_sum']) ? $user_static1[0]['new_apply_sum'] : 0; //昨日发起申请数
			$lastday_new_apply_sum         = isset($user_static2[0]['new_apply_sum']) ? $user_static2[0]['new_apply_sum'] : 0; //前日发起申请数
			$yester_new_contact_sum        = isset($user_static1[0]['new_contact_sum']) ? $user_static1[0]['new_contact_sum'] : 0; //昨日新增客户数
			$lastday_new_contact_sum       = isset($user_static2[0]['new_contact_sum']) ? $user_static2[0]['new_contact_sum'] : 0; //前日新增客户数
			$yester_negative_feedback_sum  = isset($user_static1[0]['negative_feedback_sum']) ? $user_static1[0]['negative_feedback_sum'] : 0; //删除/拉黑成员的客户数
			$lastday_negative_feedback_sum = isset($user_static2[0]['negative_feedback_sum']) ? $user_static2[0]['negative_feedback_sum'] : 0; //删除/拉黑成员的客户数

			//昨日发起申请数
			$one = [];
			if ($yester_new_apply_sum >= $lastday_new_apply_sum) {
				$one['status'] = 1; //上升
			} else {
				$one['status'] = 0; //下降
			}
			$num = 0;
			if ($lastday_new_apply_sum > 0) {
				$num = round(abs($yester_new_apply_sum - $lastday_new_apply_sum) / $lastday_new_apply_sum, 3);
			} else {
				$num = $yester_new_apply_sum;
			}
			$num          = sprintf("%.1f", $num * 100);
			$one['count'] = $yester_new_apply_sum;
			$one['per']   = $num . '%';

			//昨日新增客户数
			$two = [];
			if ($yester_new_contact_sum >= $lastday_new_contact_sum) {
				$two['status'] = 1; //上升
			} else {
				$two['status'] = 0; //下降
			}
			$num = 0;
			if ($lastday_new_contact_sum > 0) {
				$num = round(abs($yester_new_contact_sum - $lastday_new_contact_sum) / $lastday_new_contact_sum, 3);
			} else {
				$num = $yester_new_contact_sum;
			}
			$num          = sprintf("%.1f", $num * 100);
			$two['count'] = $yester_new_contact_sum;
			$two['per']   = $num . '%';

			//昨日删除/拉黑成员的客户数
			$three = [];
			if ($yester_negative_feedback_sum >= $lastday_negative_feedback_sum) {
				$three['status'] = 1; //上升
			} else {
				$three['status'] = 0; //下降
			}
			$num = 0;
			if ($lastday_negative_feedback_sum > 0) {
				$num = round(abs($yester_negative_feedback_sum - $lastday_negative_feedback_sum) / $lastday_negative_feedback_sum, 3);
			} else {
				$num = $yester_negative_feedback_sum;
			}
			$num            = sprintf("%.1f", $num * 100);
			$three['count'] = $yester_negative_feedback_sum;
			$three['per']   = $num . '%';

			$result['one']   = $one;
			$result['two']   = $two;
			$result['three'] = $three;

			return $result;
		}

		/**
		 * @param $corpId
		 * @param $userId
		 * @param $type
		 *
		 * @return int
		 *
		 */
		public static function getReplyPercentage ($corpId, $userId, $type)
		{
			$count = 0;
			$where = [
				'userid'  => $userId,
				'corp_id' => $corpId,
			];
			if ($type == 1) {
				$field = 'reply_percentage';
			} else {
				$field = 'avg_reply_time';
			}
			$repCount = static::find()->andWhere($where)->andWhere(['<>', $field, ''])->count();
			if (!empty($repCount)) {
				$expression  = new Expression('sum(' . $field . ') sum');
				$work_static = static::find()->andWhere($where)->select($expression)->groupBy('userid')->asArray()->one();
				$sum         = $work_static['sum'];
				if ($type == 1) {
					$count = round($sum / ($repCount * 100), 2);
				} else {
					$count = round($sum / $repCount, 2);
				}
			}

			return $count;
		}

		/**
		 * 获取时间段的统计数据
		 *
		 * @return array
		 *
		 */
		public static function getWorkUserDataNum ($corp_id, $user_ids, $userids, $data_type, $sdate, $edate = '')
		{
			if ($data_type == 4) {
				//总客户数
				$userStatic = WorkExternalContactFollowUser::find()->alias('wf');
				$userStatic = $userStatic->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
				$userStatic = $userStatic->andWhere(['we.corp_id' => $corp_id]);
				if (empty($edate)) {
					$userStatic = $userStatic->andWhere(['<', 'wf.createtime', strtotime($sdate . ' 23:59:59')]);
					$userStatic = $userStatic->andWhere(['or', ['wf.del_type' => 0], ['and', ['in', 'wf.del_type', [1, 2]], ['>=', 'wf.del_time', strtotime($sdate . ' 23:59:59')]]]);
				} else {
					$userStatic = $userStatic->andWhere(['<', 'wf.createtime', strtotime($edate . ' 23:59:59')]);
					$userStatic = $userStatic->andWhere(['or', ['wf.del_type' => 0], ['and', ['in', 'wf.del_type', [1, 2]], ['>=', 'wf.del_time', strtotime($edate . ' 23:59:59')]]]);
				}
				if (!empty($user_ids)) {
					$userStatic = $userStatic->andWhere(['in', 'wf.user_id', $user_ids]);
				}
				$cnt_num = $userStatic->count();
			} else {
				switch ($data_type) {
					case 2:
						$field = 'sum(`new_contact_cnt`) cnt_num';
						break;
					case 3:
						$field = 'sum(`negative_feedback_cnt`) cnt_num';
						break;
					default:
					case 1:
						$field = 'sum(`new_apply_cnt`) cnt_num';
						break;
				}

				$userStatic = static::find()->select($field)->where(['corp_id' => $corp_id]);
				if (empty($edate)) {
					$userStatic = $userStatic->andWhere(['data_time' => $sdate]);
				} else {
					$userStatic = $userStatic->andFilterWhere(['between', 'time', strtotime($sdate), strtotime($edate)]);
				}
				if (!empty($userids) && is_array($userids)) {
					$userStatic = $userStatic->andWhere(['in', 'userid', $userids]);
				}
				$userStatic = $userStatic->groupBy('corp_id')->asArray()->all();

				$cnt_num = isset($userStatic[0]['cnt_num']) ? $userStatic[0]['cnt_num'] : 0;
			}

			return $cnt_num;
		}

		/**
		 * 获取成员的统计数据
		 *
		 * @return array
		 *
		 */
		public static function getWorkUserTopNum ($corp_id, $data_type, $sdate, $edate, $user_ids = [], $userids = [])
		{
			$userStatic = [];
			if ($data_type == 4) {
				//总客户数
				$field      = 'wf.userid,count(wf.id) cnt_num';
				$userStatic = WorkExternalContactFollowUser::find()->alias('wf');
				if (!empty($user_ids)) {
					$userStatic = $userStatic->andWhere(['in', 'wf.user_id', $user_ids]);
				}
				$userStatic = $userStatic->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
				$userStatic = $userStatic->andWhere(['we.corp_id' => $corp_id]);
				$userStatic = $userStatic->andWhere(['<', 'wf.createtime', strtotime($edate . ' 23:59:59')]);
				$userStatic = $userStatic->andWhere(['or', ['wf.del_type' => 0], ['and', ['in', 'wf.del_type', [1, 2]], ['>=', 'wf.del_time', strtotime($edate . ' 23:59:59')]]]);
				$userStatic = $userStatic->select($field)->groupBy('wf.userid')->orderBy(['cnt_num' => SORT_DESC])->asArray()->all();
			} else {
				switch ($data_type) {
					case 2:
						$field = 'userid,sum(`new_contact_cnt`) cnt_num';
						break;
					case 3:
						$field = 'userid,sum(`negative_feedback_cnt`) cnt_num';
						break;
					default:
					case 1:
						$field = 'userid,sum(`new_apply_cnt`) cnt_num';
						break;
				}

				$userStatic = static::find()->select($field)->where(['corp_id' => $corp_id])->andFilterWhere(['between', 'time', strtotime($sdate), strtotime($edate)]);
				if (!empty($userids)) {
					$userStatic = $userStatic->andWhere(['in', 'userid', $userids]);
				}
				$userStatic = $userStatic->groupBy('userid')->orderBy(['cnt_num' => SORT_DESC])->asArray()->all();
			}

			return $userStatic;
		}

		/**
		 * 获取成员top数据
		 */
		public static function getUserTopByType ($data_type, $corp_id, $date1, $date2, $user_ids)
		{
			$xData   = [];//X轴
			$newData = [];//Y轴数据
			$allData = [];//详细数据

			$userInfo = WorkUser::find()->select('userid, name')->where(['corp_id' => $corp_id, 'is_del' => 0]);
			if (!empty($user_ids)) {
				$userInfo = $userInfo->andWhere(['id' => $user_ids]);
			}
			$userInfo = $userInfo->asArray()->all();

			$userNameInfo = [];
			$useridInfo   = [];
			if(count($user_ids) == 1 && in_array(0,$user_ids)){
				$useridInfo   = $user_ids;
			}
			foreach ($userInfo as $k => $v) {
				$userNameInfo[$v['userid']] = $v['name'];
				if (!empty($user_ids)) {
					array_push($useridInfo, $v['userid']);
				}
			}

			$userData = WorkUserStatistic::getWorkUserTopNum($corp_id, $data_type, $date1, $date2, $user_ids, $useridInfo);
			//top10数据
			$userData10 = array_slice($userData, 0, 10);
			foreach ($userData10 as $k => $v) {
				if ($v['cnt_num']){
					$userName = isset($userNameInfo[$v['userid']]) ? $userNameInfo[$v['userid']] : $v['userid'];
					array_push($xData, $userName);
					array_push($newData, $v['cnt_num']);
				}
			}
			//列表数据
			$sort = 1;
			foreach ($userData as $k => $v) {
				if ($v['cnt_num'] > 0) {
					$allD            = [];
					$allD['sort']    = $sort;
					$allD['name']    = isset($userNameInfo[$v['userid']]) ? $userNameInfo[$v['userid']] : $v['userid'];
					$allD['cnt_num'] = $v['cnt_num'];
					$allData[]       = $allD;
					$sort++;
				}
			}

			$info               = [];
			$info['xData']      = $xData;
			$info['seriesData'] = $newData;
			$info['data']       = $allData;

			return $info;
		}

		/**
		 * 获取成员趋势图数据
		 *
		 */
		public static function getUserIncreaseByType ($type, $data_type, $corp_id, $user_ids, $date1, $date2, $s_week)
		{
			$xData   = [];//X轴
			$newData = [];//统计数据
			$newNum  = 0; //统计数值

			//根据类型获取数据
			$userIdData = [];
			if(count($user_ids) == 1 && in_array(0,$user_ids)){
				$userIdData   = $user_ids;
			}
			if (!empty($user_ids)) {
				$workUser = WorkUser::find()->select('`userid`')->where(['in', 'id', $user_ids])->asArray()->all();
				foreach ($workUser as $v) {
					array_push($userIdData, $v['userid']);
				}
			}

			switch ($type) {
				case 2:
					//按天
					$data   = DateUtil::getDateFromRange($date1, $date2);
					$result = [];
					foreach ($data as $k => $v) {
						$cnt_num               = WorkUserStatistic::getWorkUserDataNum($corp_id, $user_ids, $userIdData, $data_type, $v);
						$result[$k]['cnt_num'] = $cnt_num;
						$result[$k]['time']    = $v;
						$newNum                += $cnt_num;
						array_push($newData, intval($cnt_num));
					}
					$xData = $data;
					break;
				case 3:
					//按周
					$data    = DateUtil::getDateFromRange($date1, $date2);
					$data    = DateUtil::getWeekFromRange($data);
					$s_date1 = $data['s_date'];
					$e_date1 = $data['e_date'];
					$result  = [];
					foreach ($s_date1 as $k => $v) {
						foreach ($e_date1 as $kk => $vv) {
							if ($k == $kk) {
								if ($s_week == 53) {
									$s_week = 1;
								}
								$cnt_num               = WorkUserStatistic::getWorkUserDataNum($corp_id, $user_ids, $userIdData, $data_type, $v, $vv);
								$result[$k]['cnt_num'] = $cnt_num;
								$result[$k]['time']    = $v . '~' . $vv . '(' . $s_week . '周)';
								$newNum                += $cnt_num;
								array_push($newData, intval($cnt_num));
								array_push($xData, $result[$k]['time']);
								$s_week++;
							}
						}
					}
					break;
				case 4:
					//按月
					$date   = DateUtil::getLastMonth();
					$result = [];
					foreach ($date as $k => $v) {
						$cnt_num               = WorkUserStatistic::getWorkUserDataNum($corp_id, $user_ids, $userIdData, $data_type, $v['firstday'], $v['lastday']);
						$result[$k]['cnt_num'] = $cnt_num;
						$result[$k]['time']    = $v['time'];
						$newNum                += $cnt_num;
						array_push($newData, intval($cnt_num));
						array_push($xData, $result[$k]['time']);
					}

					break;
			}
			$info['newNum']     = $newNum;
			$info['data']       = $result;
			$info['xData']      = $xData;

			$name = '统计数值';
			switch ($data_type){
				case 1:
					$name = '发起申请数';
					break;
				case 2:
					$name = '新增客户数';
					break;
				case 3:
					$name = '流失客户数';
					break;
				case 4:
					$name = '总客户数';
					break;
			}
			$seriesData         = [
				[
					'name'   => $name,
					'type'   => 'line',
					'smooth' => true,
					'data'   => $newData,
				],
			];
			$info['seriesData'] = $seriesData;

			return $info;
		}

	}
