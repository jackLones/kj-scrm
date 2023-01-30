<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_chat_statistic}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id        授权的企业ID
	 * @property int      $owner_id       群主用户ID
	 * @property string   $owner          群主ID
	 * @property int      $new_chat_cnt   新增客户群数量
	 * @property int      $chat_total     截至当天客户群总数量
	 * @property int      $chat_has_msg   截至当天有发过消息的客户群数量
	 * @property int      $new_member_cnt 客户群新增群人数
	 * @property int      $member_total   截至当天客户群总人数
	 * @property int      $member_has_msg 截至当天有发过消息的群成员数
	 * @property int      $msg_total      截至当天客户群消息总数
	 * @property int      $time           数据当日0点的时间戳
	 * @property int      $create_time    创建时间
	 *
	 * @property WorkUser $ownerUser
	 * @property WorkCorp $corp
	 */
	class WorkChatStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'owner_id', 'new_chat_cnt', 'chat_total', 'chat_has_msg', 'new_member_cnt', 'member_total', 'member_has_msg', 'msg_total', 'time', 'create_time'], 'integer'],
				[['owner'], 'string', 'max' => 64],
				[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['owner_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'corp_id'        => Yii::t('app', '授权的企业ID'),
				'owner_id'       => Yii::t('app', '群主用户ID'),
				'owner'          => Yii::t('app', '群主ID'),
				'new_chat_cnt'   => Yii::t('app', '新增客户群数量'),
				'chat_total'     => Yii::t('app', '截至当天客户群总数量'),
				'chat_has_msg'   => Yii::t('app', '截至当天有发过消息的客户群数量'),
				'new_member_cnt' => Yii::t('app', '客户群新增群人数'),
				'member_total'   => Yii::t('app', '截至当天客户群总人数'),
				'member_has_msg' => Yii::t('app', '截至当天有发过消息的群成员数'),
				'msg_total'      => Yii::t('app', '截至当天客户群消息总数'),
				'time'           => Yii::t('app', '数据当日0点的时间戳'),
				'create_time'    => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getOwnerUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'owner_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * 群昨日数据总览
		 *
		 * @param corp_id int 企业微信号
		 *
		 * @return array
		 *
		 */
		public static function getWorkChatStatisticData ($corp_id,$user_ids=[])
		{
			$start1      = strtotime(date('Y-m-d', strtotime('-1 day')));//昨日0点
			$start2      = $start1 - 86400;//前日0点
			$field       = 'sum(`new_chat_cnt`) new_chat_sum,sum(`chat_total`) chat_total_sum,sum(`chat_has_msg`) chat_msg_sum,sum(`new_member_cnt`) new_member_sum,sum(`member_total`) member_total_sum,sum(`member_has_msg`) member_msg_sum,sum(`msg_total`) msg_total_sum';
			$chatStatic1 = static::find()->select($field)->where(['corp_id' => $corp_id, 'time' => $start1])->groupBy('corp_id');
			$chatStatic2 = static::find()->select($field)->where(['corp_id' => $corp_id, 'time' => $start2])->groupBy('corp_id');
			if(!empty($user_ids)){
				$chatStatic1 = $chatStatic1->andWhere(['in','owner_id',$user_ids]);
				$chatStatic2 = $chatStatic2->andWhere(['in','owner_id',$user_ids]);
			}
			$chatStatic1 = $chatStatic1->asArray()->all();
			$chatStatic2 = $chatStatic2->asArray()->all();

			$yester_new_chat_sum      = isset($chatStatic1[0]['new_chat_sum']) ? $chatStatic1[0]['new_chat_sum'] : 0; //昨日新增客户群数量
			$lastday_new_chat_sum     = isset($chatStatic2[0]['new_chat_sum']) ? $chatStatic2[0]['new_chat_sum'] : 0; //前日新增客户群数量
			$yester_chat_total_sum    = isset($chatStatic1[0]['chat_total_sum']) ? $chatStatic1[0]['chat_total_sum'] : 0; //截至昨日客户群总数量
			$lastday_chat_total_sum   = isset($chatStatic2[0]['chat_total_sum']) ? $chatStatic2[0]['chat_total_sum'] : 0; //截至前日客户群总数量
			$yester_chat_msg_sum      = isset($chatStatic1[0]['chat_msg_sum']) ? $chatStatic1[0]['chat_msg_sum'] : 0; //昨天有发过消息的客户群数量
			$lastday_chat_msg_sum     = isset($chatStatic2[0]['chat_msg_sum']) ? $chatStatic2[0]['chat_msg_sum'] : 0; //前天有发过消息的客户群数量
			$yester_new_member_sum    = isset($chatStatic1[0]['new_member_sum']) ? $chatStatic1[0]['new_member_sum'] : 0; //昨日客户群新增群人数
			$lastday_new_member_sum   = isset($chatStatic2[0]['new_member_sum']) ? $chatStatic2[0]['new_member_sum'] : 0; //前日客户群新增群人数
			$yester_member_total_sum  = isset($chatStatic1[0]['member_total_sum']) ? $chatStatic1[0]['member_total_sum'] : 0; //截至昨天客户群总人数
			$lastday_member_total_sum = isset($chatStatic2[0]['member_total_sum']) ? $chatStatic2[0]['member_total_sum'] : 0; //截至前天客户群总人数
			$yester_member_msg_sum    = isset($chatStatic1[0]['member_msg_sum']) ? $chatStatic1[0]['member_msg_sum'] : 0; //昨天有发过消息的群成员数
			$lastday_member_msg_sum   = isset($chatStatic2[0]['member_msg_sum']) ? $chatStatic2[0]['member_msg_sum'] : 0; //前天有发过消息的群成员数
			$yester_msg_total_sum     = isset($chatStatic1[0]['msg_total_sum']) ? $chatStatic1[0]['msg_total_sum'] : 0; //昨天客户群消息总数
			$lastday_msg_total_sum    = isset($chatStatic2[0]['msg_total_sum']) ? $chatStatic2[0]['msg_total_sum'] : 0; //前天客户群消息总数

			$result = [];
			//昨日新增群成员
			$data          = [];
			$data['title'] = '昨日新增群成员数';
			$data['dec']   = '';
			if ($yester_new_member_sum > $lastday_new_member_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_new_member_sum < $lastday_new_member_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_new_member_sum > 0) {
				$num = round(abs($yester_new_member_sum - $lastday_new_member_sum) / $lastday_new_member_sum, 3);
			} else {
				$num = $yester_new_member_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_new_member_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;
			//昨天有发过消息的群成员数
			$data          = [];
			$data['title'] = '昨日有发过消息的群成员数';
			$data['dec']   = '';
			if ($yester_member_msg_sum > $lastday_member_msg_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_member_msg_sum < $lastday_member_msg_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_member_msg_sum > 0) {
				$num = round(abs($yester_member_msg_sum - $lastday_member_msg_sum) / $lastday_member_msg_sum, 3);
			} else {
				$num = $yester_member_msg_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_member_msg_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;
			//昨日退群人群
			$chatData = WorkChat::find()->alias('a');
			$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
			$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
			$chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', $start1, $start1 + 86399]);
			$yester_count            = $chatData->select('b.`id`')->all();
			$yester_member_leave_sum = count($yester_count);

			$chatData = WorkChat::find()->alias('a');
			$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
			$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
			$chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', $start2, $start2 + 86399]);
			$lastday_count            = $chatData->select('b.`id`')->all();
			$lastday_member_leave_sum = count($lastday_count);

			$data          = [];
			$data['title'] = '昨日退群人群';
			$data['dec']   = '';
			if ($yester_member_leave_sum > $lastday_member_leave_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_member_leave_sum < $lastday_member_leave_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_member_leave_sum > 0) {
				$num = round(abs($yester_member_leave_sum - $lastday_member_leave_sum) / $lastday_member_leave_sum, 3);
			} else {
				$num = $yester_member_leave_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_member_leave_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;
			//截至昨天客户群消息总数
			$data          = [];
			$data['title'] = '昨日客户群消息总数';
			$data['dec']   = '';
			if ($yester_msg_total_sum > $lastday_msg_total_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_msg_total_sum < $lastday_msg_total_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_msg_total_sum > 0) {
				$num = round(abs($yester_msg_total_sum - $lastday_msg_total_sum) / $lastday_msg_total_sum, 3);
			} else {
				$num = $yester_msg_total_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_msg_total_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;
			//昨日新增客户群数量
			$data          = [];
			$data['title'] = '昨日新增客户群数';
			$data['dec']   = '';
			if ($yester_new_chat_sum > $lastday_new_chat_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_new_chat_sum < $lastday_new_chat_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_new_chat_sum > 0) {
				$num = round(abs($yester_new_chat_sum - $lastday_new_chat_sum) / $lastday_new_chat_sum, 3);
			} else {
				$num = $yester_new_chat_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_new_chat_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;
			//昨天有发过消息的客户群数量
			$data          = [];
			$data['title'] = '昨日有发过消息的客户群数量';
			$data['dec']   = '';
			if ($yester_chat_msg_sum > $lastday_chat_msg_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_chat_msg_sum < $lastday_chat_msg_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_chat_msg_sum > 0) {
				$num = round(abs($yester_chat_msg_sum - $lastday_chat_msg_sum) / $lastday_chat_msg_sum, 3);
			} else {
				$num = $yester_chat_msg_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_chat_msg_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;
			//当前群总数
			$data          = [];
			$data['title'] = '群总数';
			$data['dec']   = '';
			if ($yester_chat_total_sum > $lastday_chat_total_sum) {
				$data['status'] = 1;//1上升2下降0持平
			} elseif ($yester_chat_total_sum < $lastday_chat_total_sum) {
				$data['status'] = 2;
			} else {
				$data['status'] = 0;
			}
			if ($lastday_chat_total_sum > 0) {
				$num = round(abs($yester_chat_total_sum - $lastday_chat_total_sum) / $lastday_chat_total_sum, 3);
			} else {
				$num = $yester_chat_total_sum;
			}
			$num           = sprintf("%.1f", $num * 100);
			$data['count'] = $yester_chat_total_sum;
			$data['per']   = $num . '%';
			$result[]      = $data;

			return $result;
		}

		//根据群主获取群单位时间内微信统计数据
		public static function getChatStatisticsByDataType ($corp_id, $user_ids, $data_type, $stime, $etime)
		{
			if ($data_type == 8) {
				$chatData = WorkChat::find()->alias('a');
				$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
				if (is_array($user_ids) && !empty($user_ids)) {
					$chatData = $chatData->andWhere(['in', 'a.owner_id', $user_ids]);
				}
				$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
				$chatData = $chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', strtotime($stime), strtotime($etime . ' 23:59:59')]);
				$count    = $chatData->select('b.`id`')->all();
				$count    = count($count);
			} else {
				switch ($data_type) {
					case 2:
						$field = 'SUM(`new_chat_cnt`) chat_snum';
						break;
					case 3:
						$field = 'SUM(`chat_has_msg`) chat_snum';
						break;
					case 4:
						$field = 'SUM(`member_total`) chat_snum';
						break;
					case 5:
						$field = 'SUM(`new_member_cnt`) chat_snum';
						break;
					case 6:
						$field = 'SUM(`member_has_msg`) chat_snum';
						break;
					case 7:
						$field = 'SUM(`msg_total`) chat_snum';
						break;
					default:
					case 1:
						$field = 'SUM(`chat_total`) chat_snum';
						break;
				}

				$stime    = strtotime($stime);
				$etime    = strtotime($etime);
				$chatData = static::find()->where(['corp_id' => $corp_id]);
				if (in_array($data_type, [1, 4])){
					$chatData = $chatData->andWhere(['time' => $etime]);
				}else{
					$chatData = $chatData->andFilterWhere(['between', '`time`', $stime, $etime]);
				}
				if (!empty($user_ids) && is_array($user_ids)) {
					$chatData = $chatData->andWhere(['in', 'owner_id', $user_ids]);
				}
				$chatData = $chatData->select($field)->asArray()->all();
				$count    = isset($chatData[0]['chat_snum']) ? $chatData[0]['chat_snum'] : 0;
			}

			return $count;
		}
	}
