<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_contact_way_baidu_date}}".
	 *
	 * @property int                 $id
	 * @property int                 $way_id      企业微信联系我表ID
	 * @property int                 $type        0周1日期
	 * @property string              $start_date  开始日期
	 * @property string              $end_date    结束日期
	 * @property string              $day         周几
	 * @property string              $create_time 创建时间
	 *
	 * @property WorkContactWayBaidu $way
	 */
	class WorkContactWayBaiduDate extends \yii\db\ActiveRecord
	{
		const MONDAY_DAY = 'mon';
		const TUESDAY_DAY = 'tues';
		const WEDNESDAY_DAY = 'wednes';
		const THURSDAY_DAY = 'thurs';
		const FRIDAY_DAY = 'fri';
		const SATURDAY_DAY = 'satur';
		const SUNDAY_DAY = 'sun';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_baidu_date}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['way_id'], 'required'],
				[['way_id', 'type'], 'integer'],
				[['start_date', 'end_date', 'create_time'], 'safe'],
				[['day'], 'string', 'max' => 32],
				[['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayBaidu::className(), 'targetAttribute' => ['way_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'way_id'      => Yii::t('app', '企业微信联系我表ID'),
				'type'        => Yii::t('app', '0周1日期'),
				'start_date'  => Yii::t('app', '开始日期'),
				'end_date'    => Yii::t('app', '结束日期'),
				'day'         => Yii::t('app', '周几'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWay ()
		{
			return $this->hasOne(WorkContactWayBaidu::className(), ['id' => 'way_id']);
		}

		/**
		 * @param $data
		 * @param $way_id
		 * @param $flag
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function setData ($data, $way_id, $flag = 0)
		{
			if (!empty($data)) {
				if ($flag == 0){
					$dateUser = static::find()->where(['way_id' => $way_id])->all();
					if (!empty($dateUser)) {
						/** @var WorkContactWayDate $user */
						foreach ($dateUser as $user) {
							WorkContactWayBaiduDateUser::deleteAll(['date_id' => $user->id]);
						}
					}
					static::deleteAll(['way_id' => $way_id]);
				}
				foreach ($data as $val) {
//					if ($flag == 1 || empty($val['id'])) {
						$contactWayDate              = new WorkContactWayBaiduDate();
						$contactWayDate->create_time = DateUtil::getCurrentTime();
//					} else {
//						$contactWayDate = static::findOne($val['id']);
//					}
					$contactWayDate->way_id     = $way_id;
					$contactWayDate->type       = 1;
					$contactWayDate->start_date = $val['date'][0];
					$contactWayDate->end_date   = $val['date'][1];
					$contactWayDate->day        = '';
					if (!$contactWayDate->validate() || !$contactWayDate->save()) {
						throw new InvalidDataException(SUtils::modelError($contactWayDate));
					}
					$dataUser = $val['time'];
					WorkContactWayBaiduDateUser::setData($dataUser, $contactWayDate->id);
				}
			}

			return true;
		}

		/**
		 * @param $weekUser
		 * @param $way_id
		 *
		 * @throws InvalidDataException
		 */
		public static function setWeekData ($weekUser, $way_id)
		{
			static::add($way_id, $weekUser, 'mon');
			static::add($way_id, $weekUser, 'tues');
			static::add($way_id, $weekUser, 'wednes');
			static::add($way_id, $weekUser, 'thurs');
			static::add($way_id, $weekUser, 'fri');
			static::add($way_id, $weekUser, 'satur');
			static::add($way_id, $weekUser, 'sun');
		}

		/**
		 * @param $way_id
		 * @param $weekUser
		 * @param $day
		 * @param $flag
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function add ($way_id, $weekUser, $day)
		{
			$user = static::findOne(['way_id' => $way_id, 'type' => 0, 'day' => $day]);
			if (empty($user)) {
				$user              = new WorkContactWayBaiduDate();
				$user->create_time = DateUtil::getCurrentTime();
			}
			$user->way_id = $way_id;
			$user->type   = 0;
			$user->day    = $day;
			if (!$user->validate() || !$user->save()) {
				throw new InvalidDataException(SUtils::modelError($user));
			}
			$dataUser = $weekUser[0][$day];
			WorkContactWayBaiduDateUser::setData($dataUser, $user->id);

			return true;
		}

		/**
		 * @param $wayId
		 * @param $corpId
		 *
		 * @return array
		 *
		 */
		public static function getChooseDate ($wayId, $corpId)
		{
			$choose_date = [];
			$wayDate     = static::find()->where(['way_id' => $wayId, 'type' => 1])->asArray()->all();
			if (!empty($wayDate)) {
				foreach ($wayDate as $key => $date) {
					$choose_date[$key]['id']   = $date['id'];
					$choose_date[$key]['date'] = [$date['start_date'], $date['end_date']];
					$time                      = [];
					$dateUser                  = WorkContactWayBaiduDateUser::find()->where(['date_id' => $date['id']])->asArray()->all();
					if (!empty($dateUser)) {
						foreach ($dateUser as $k => $user) {
							$userList           = json_decode($user['user_key'], true);
							$time[$k]['is_del'] = 0;
							if (!is_array($userList)) {
								$userList   = intval($userList);
								$followUser = WorkFollowUser::findOne(['corp_id' => $corpId, 'user_id' => $userList, 'status' => 1]);
								if (empty($followUser)) {
									$time[$k]['is_del'] = 1;
									$workUser           = WorkUser::findOne($userList);
									if (!empty($workUser)) {
										if ($workUser->is_del == 1) {
											$userList = $workUser->name . '（已删除）';
										}
										$userList = $workUser->name . '（无权限）';
									}
								}
							} else {
								WorkDepartment::ActivityDataFormat($userList, $corpId,$user["department"]);
							}
							$time[$k]['userList']   = $userList;
							$userTime               = explode('-', $user['time']);
							$time[$k]['start_time'] = $userTime[0];
							$time[$k]['end_time']   = $userTime[1];
							$time[$k]['party']      = [];
							if (!empty($user['department'])) {
								$time[$k]['party'] = json_decode($user['department'], true);
							}
						}
					}
					$choose_date[$key]['time'] = $time;
				}
			}

			return $choose_date;
		}

		/**
		 * @param $wayId
		 * @param $corpId
		 *
		 * @return mixed
		 *
		 */
		public static function getWeekUser ($wayId, $corpId)
		{
			$weekUser['mon']    = static::getData($wayId, self::MONDAY_DAY, $corpId);
			$weekUser['tues']   = static::getData($wayId, self::TUESDAY_DAY, $corpId);
			$weekUser['wednes'] = static::getData($wayId, self::WEDNESDAY_DAY, $corpId);
			$weekUser['thurs']  = static::getData($wayId, self::THURSDAY_DAY, $corpId);
			$weekUser['fri']    = static::getData($wayId, self::FRIDAY_DAY, $corpId);
			$weekUser['satur']  = static::getData($wayId, self::SATURDAY_DAY, $corpId);
			$weekUser['sun']    = static::getData($wayId, self::SUNDAY_DAY, $corpId);
			$weekUserNew[]      = $weekUser;

			return $weekUserNew;
		}

		/**
		 * @param $wayId
		 * @param $day
		 * @param $corpId
		 *
		 * @return array
		 *
		 */
		public static function getData ($wayId, $day, $corpId)
		{
			$mon  = static::findOne(['way_id' => $wayId, 'type' => 0, 'day' => $day]);
			$time = [];
			if (!empty($mon)) {
				$dateUser = WorkContactWayBaiduDateUser::find()->where(['date_id' => $mon->id])->asArray()->all();
				if (!empty($dateUser)) {
					foreach ($dateUser as $key => $user) {
						$userList = json_decode($user['user_key'], true);
						if (!is_array($userList)) {
							$userList = strval($userList);
						}
						$time[$key]['is_del'] = 0;
						if (!is_array($userList)) {
							$userList   = intval($userList);
							$followUser = WorkFollowUser::findOne(['corp_id' => $corpId, 'user_id' => $userList, 'status' => 1]);
							if (empty($followUser)) {
								$time[$key]['is_del'] = 1;
								$workUser             = WorkUser::findOne($userList);
								if (!empty($workUser)) {
									if ($workUser->is_del == 1) {
										$userList = $workUser->name . '（已删除）';
									}
									$userList = $workUser->name . '（无权限）';
								}

							}
						} else {
							WorkDepartment::ActivityDataFormat($userList, $corpId,$user["department"]);
						}

						$time[$key]['userList'] = $userList;
						$time[$key]['party']    = [];
						if (!empty($user['department'])) {
							$time[$key]['party'] = json_decode($user['department'], true);
						}
						$userTime                 = explode('-', $user['time']);
						$time[$key]['start_time'] = $userTime[0];
						$time[$key]['end_time']   = $userTime[1];
					}
				}
			}

			return $time;
		}

		/**
		 * @param $weekData
		 * @param $type
		 *
		 * @return  bool
		 * @throws InvalidDataException
		 */
		public static function verifyData ($weekData, $type)
		{
			static::check($weekData[0][self::MONDAY_DAY], $type);
			static::check($weekData[0][self::TUESDAY_DAY], $type);
			static::check($weekData[0][self::WEDNESDAY_DAY], $type);
			static::check($weekData[0][self::THURSDAY_DAY], $type);
			static::check($weekData[0][self::FRIDAY_DAY], $type);
			static::check($weekData[0][self::SATURDAY_DAY], $type);
			static::check($weekData[0][self::SUNDAY_DAY], $type);

			return true;
		}

		/**
		 * @param $weekData
		 * @param $type
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function check ($weekData, $type)
		{
			$flag = false;
			foreach ($weekData as $data) {
				if ($type == 1) {
					//单人
					if (empty($data['userList'])) {
						throw new InvalidDataException("用户不能为空");
					}
				} else {
					//多人
					if (empty($data['userList']) && empty($data['party'])) {
						throw new InvalidDataException("用户和部门不能同时为空");
					}
				}
				if ($data['start_time'] == '00:00' && $data['end_time'] == '00:00') {
					$flag = true;
				}
			}
			if (!$flag) {
				throw new InvalidDataException("参数不正确");
			}

			return true;
		}

		/**
		 * @param $weekData
		 * @param $chooseData
		 * @param $corp_id
		 * @param $open_date
		 * @param $type
		 * @param $from
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 */
		public static function getNowUser ($weekData, $chooseData, $corp_id, $open_date, $type, $from = 0)
		{
			if (!empty($weekData)) {
				$weekData = $weekData[0];
				$week     = date("l"); //获取是周几  Wednesday
				$date     = date("Y-m-d", time()); //日期 2020-04-15 16:03:46
				$day      = '';
				$userId   = [];
				$partyId  = [];
				switch ($week) {
					case 'Monday':
						$day = self::MONDAY_DAY;
						break;
					case 'Tuesday':
						$day = self::TUESDAY_DAY;
						break;
					case 'Wednesday':
						$day = self::WEDNESDAY_DAY;
						break;
					case 'Thursday':
						$day = self::THURSDAY_DAY;
						break;
					case 'Friday':
						$day = self::FRIDAY_DAY;
						break;
					case 'Saturday':
						$day = self::SATURDAY_DAY;
						break;
					case 'Sunday':
						$day = self::SUNDAY_DAY;
						break;
				}
				$nowWeek = $weekData[$day];
				foreach ($nowWeek as $week) {
					$result = static::getUserId($week, $date, $corp_id, $type);
					if (!empty($result['userId'])) {
						$userId = $result['userId'];
					}
					if (!empty($result['partyId'])) {
						$partyId = $result['partyId'];
					}
				}
			}
			if (!empty($chooseData) && $open_date) {
				foreach ($chooseData as $data) {
					$date1 = $data['date'][0];
					$date2 = $data['date'][1] . ' 23:59:59';
					if (time() >= strtotime($date1) && time() <= strtotime($date2)) {
						$userId  = [];
						$partyId = [];
						foreach ($data['time'] as $time) {
							$result = static::getUserId($time, $date1, $corp_id, $type);
							if (!empty($result['userId'])) {
								$userId = $result['userId'];
							}
							if (!empty($result['partyId'])) {
								$partyId = $result['partyId'];
							}
						}
					}
				}
			}
			if (empty($userId) && $type == 1 && $from == 0) {
				throw new InvalidDataException("请选择活码成员");
			}
			if (empty($partyId) && empty($userId) && $type == 2 && $from == 0) {
				throw new InvalidDataException("请选择活码成员");
			}

			return [
				'userId'  => $userId,
				'partyId' => $partyId,
			];

		}

		/**
		 * @param $week
		 * @param $date
		 * @param $corp_id
		 * @param $type
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 */
		public static function getUserId ($week, $date, $corp_id, $type)
		{
			$userId  = [];
			$partyId = [];
			if ($week['start_time'] == '00:00' && $week['end_time'] == '00:00') {
				if (empty($week['userList']) && $type == 1) {
					throw new InvalidDataException("参数不正确");
				}
				$userList       = isset($week['userList']) ? $week['userList'] : '';
				$departmentList = isset($week['party']) ? $week['party'] : '';
				if (!empty($userList)) {
					if (is_array($userList)) {
						foreach ($userList as $val) {
							$workFollow = WorkFollowUser::findOne(['corp_id' => $corp_id, 'user_id' => $val['id'], 'status' => 1]);
							if (!empty($workFollow)) {
								$workUser = WorkUser::findOne($val['id']);
								if (!empty($workUser) && $workUser->corp_id == $corp_id) {
									array_push($userId, $workUser->userid);
								}
							}
						}
					} else {
						$workFollow = WorkFollowUser::findOne(['corp_id' => $corp_id, 'user_id' => $userList, 'status' => 1]);
						if (!empty($workFollow)) {
							$workUser = WorkUser::findOne($userList);
							if (!empty($workUser)) {
								array_push($userId, $workUser->userid);
							}
						}
					}
				}
				if (!empty($departmentList)) {
					foreach ($departmentList as $depart) {
						$department = WorkDepartment::findOne($depart);
						if (!empty($department) && $department->corp_id == $corp_id) {
							array_push($partyId, $department->department_id);
						}
					}
				}

			}
			if ($week['end_time'] == '00:00') {
				$endTime = '23:59';
			} else {
				$endTime = $week['end_time'];
			}
			$startTime = $week['start_time'];
			$time1     = strtotime($date . ' ' . $startTime . ':00');
			$time2     = strtotime($date . ' ' . $endTime . ':00');
			if (time() > $time1 && time() <= $time2) {
				if (!empty($week['userList'])) {
					$userId = [];
					if (is_array($week['userList'])) {
						foreach ($week['userList'] as $val) {
							$workUser = WorkUser::findOne($val['id']);
							if (!empty($workUser) && $workUser->corp_id == $corp_id) {
								array_push($userId, $workUser->userid);
							}
						}
					} else {
						$workUser = WorkUser::findOne($week['userList']);
						if (!empty($workUser) && $workUser->corp_id == $corp_id) {
							array_push($userId, $workUser->userid);
						}
					}

				}
				if (!empty($week['party'])) {
					foreach ($week['party'] as $depart) {
						$department = WorkDepartment::findOne($depart);
						if (!empty($department) && $department->corp_id == $corp_id) {
							array_push($partyId, $department->department_id);
						}
					}
				}
			}

			return [
				'userId'  => $userId,
				'partyId' => $partyId,
			];
		}

		/**
		 * @param $id
		 * @param $type 0周1日期
		 * @param $day
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 */
		public static function getEditDateUser ($id, $type, $day)
		{
			$userId  = [];
			$partyId = [];
			$date    = WorkContactWayBaiduDate::findOne(['way_id' => $id, 'day' => $day]);
			if (empty($date)) {
				throw new InvalidDataException("参数不正确");
			}
			$dateUser = WorkContactWayBaiduDateUser::find()->where(['date_id' => $date->id])->asArray()->all();
			if (!empty($dateUser)) {
				foreach ($dateUser as $user) {
					if ($type == 1) {
						if ($user['time'] == '00:00-00:00') {
							$workUser = WorkUser::findOne($user['user_key']);
							$userId[] = $workUser->userid;
						}
						if ($user['time'] != '00:00-00:00') {
							$time    = explode('-', $user['time']);
							$nowDate = date('Y-m-d');
							$s_time  = strtotime($nowDate . ' ' . $time[0]);
							$e_time  = strtotime($nowDate . ' ' . $time[1] . ':59');
							if (time() >= $s_time && time() <= $e_time) {
								$workUser = WorkUser::findOne($user['user_key']);
								$userId[] = $workUser->userid;
							}
						}
					} else {
						if ($user['time'] == '00:00-00:00') {
							$userKey = json_decode($user['user_key'], true);
							if (!empty($userKey)) {
								foreach ($userKey as $key) {
									$workUser = WorkUser::findOne($key['id']);
									array_push($userId, $workUser->userid);
								}
							}
							if (!empty($user['department'])) {
								$userDepart = json_decode($user['department'], true);
								foreach ($userDepart as $kk) {
									$workDepart = WorkDepartment::findOne($kk);
									array_push($partyId, $workDepart->department_id);
								}
							}
						}
						if ($user['time'] != '00:00-00:00') {
							$time    = explode('-', $user['time']);
							$nowDate = date('Y-m-d');
							$s_time  = strtotime($nowDate . ' ' . $time[0]);
							$e_time  = strtotime($nowDate . ' ' . $time[1] . ':59');
							if (time() >= $s_time && time() <= $e_time) {
								$userId  = [];
								$userKey = json_decode($user['user_key'], true);
								if (!empty($userKey)) {
									foreach ($userKey as $key) {
										$workUser = WorkUser::findOne($key['id']);
										array_push($userId, $workUser->userid);
									}
								}
								$partyId = [];
								if (!empty($user['department'])) {
									$userDepart = json_decode($user['department'], true);
									foreach ($userDepart as $kk) {
										$workDepart = WorkDepartment::findOne($kk);
										array_push($partyId, $workDepart->department_id);
									}
								}
							}
						}
					}

				}
			}

			return [
				'userId'  => $userId,
				'partyId' => $partyId,
			];
		}
	}
