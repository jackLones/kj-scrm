<?php

	namespace app\models;

	use app\util\DateUtil;
	use app\util\SUtils;
	use PhpOffice\PhpSpreadsheet\Shared\Date;
	use Yii;
	use yii\db\Expression;
	use yii\debug\panels\EventPanel;

	/**
	 * This is the model class for table "{{%work_external_contact_follow_record}}".
	 *
	 * @property int    $id
	 * @property int    $uid         商户id
	 * @property int    $type        跟进类型：1客户2粉丝3客户群
	 * @property int    $external_id 外部联系人ID
	 * @property int    $chat_id     客户群ID
	 * @property int    $sub_id      子账户ID
	 * @property int    $user_id     成员ID
	 * @property string $record      跟进记录
	 * @property string $file        图片附件
	 * @property int    $time        时间
	 * @property int    $upt_time    更新时间
	 * @property int    $status      是否有效1是0否
	 * @property int    $follow_id   跟进状态id
	 * @property int    $is_master   状态 0 主账户添加 1 子账户添加
	 * @property int    $lose_id   状态 0 主账户添加 1 子账户添加
     * @property int    $record_type 0：手动添加；1：电话记录
	 */
	class WorkExternalContactFollowRecord extends \yii\db\ActiveRecord
	{
		const ALL_DAY = -1;
		const ONE_DAY = -3;
		const THREE_DAY = -2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_external_contact_follow_record}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'type', 'external_id','lose_id', 'sub_id', 'user_id', 'time', 'upt_time', 'status', 'follow_id', 'record_type'], 'integer'],
				[['record', 'file'], 'string'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'uid'         => Yii::t('app', '商户id'),
				'type'        => Yii::t('app', '跟进类型：1客户2粉丝3客户群'),
				'external_id' => Yii::t('app', '外部联系人ID'),
				'chat_id'     => Yii::t('app', '客户群ID'),
				'sub_id'      => Yii::t('app', '子账户ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'record'      => Yii::t('app', '跟进记录'),
				'file'        => Yii::t('app', '图片附件'),
				'time'        => Yii::t('app', '时间'),
				'upt_time'    => Yii::t('app', '更新时间'),
				'status'      => Yii::t('app', '是否有效1是0否'),
				'follow_id'   => Yii::t('app', '跟进状态id'),
				'is_master'   => Yii::t('app', '状态 0 主账户添加 1 子账户添加 '),
				'lose_id'     => Yii::t('app', '输单原因id'),
                'record_type' => Yii::t('app', '0：手动添加；1：电话记录'),
			];
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @param     $user_ids
		 * @param     $uid
		 * @param     $type 1、跟进客户数 2、跟进次数
		 * @param     $corpId
		 * @param int $page
		 * @param int $pageSize
		 * @param     $s_date
		 * @param     $e_date
		 * @param     $follow_id
		 * @param     $is_export
		 * @param     $status_id
		 *
		 * @return array
		 *
		 */
		public static function getData ($user_ids, $uid, $type, $corpId, $page = 1, $pageSize = 10, $s_date, $e_date, $follow_id, $is_export, $status_id)
		{
			$count      = 0;
			$allData    = [];//详细数据
			$xData      = [];//X轴
			$seriesData = [];//Y轴数据
			if ($type == 2 || $type == 3) {
				$increase = static::userCount($uid, $s_date, $e_date, $corpId, $follow_id, $type, $user_ids);
				$offset   = ($page - 1) * $pageSize;
				if (!empty($increase)) {
					$i = 0;
					foreach ($increase as $key => $inc) {
						if ($is_export == 1) {
							$allData[$i]['sort']   = $key + 1;
							$allData[$i]['name']   = $inc['name'];
							$allData[$i]['status'] = $inc['status'];
							$allData[$i]['count']  = $inc['count'];
							$i++;
						} else {
							if ($key < ($page * $pageSize) && $key >= $offset) {
								array_push($xData, $inc['name']);
								array_push($seriesData, $inc['count']);
								$allData[$i]['sort']   = $key + 1;
								$allData[$i]['name']   = $inc['name'];
								$allData[$i]['status'] = $inc['status'];
								$allData[$i]['count']  = $inc['count'];
								$i++;
							}
						}

					}
				}
				$count = count($increase);
			} else {
				$notUser = static::getNotUserCount($corpId, $follow_id, $s_date, $e_date, $status_id, $user_ids);
				$offset  = ($page - 1) * $pageSize;
				if (!empty($notUser)) {
					$i = 0;
					foreach ($notUser as $key => $inc) {
						if ($is_export == 1) {
							$allData[$i]['sort']   = $key + 1;
							$allData[$i]['name']   = $inc['name'];
							$allData[$i]['status'] = $inc['status'];
							$allData[$i]['days']   = $inc['days'];
							$allData[$i]['count']  = $inc['count'];
							$i++;
						} else {
							if ($key < ($page * $pageSize) && $key >= $offset) {
								array_push($xData, $inc['name']);
								array_push($seriesData, $inc['count']);
								$allData[$i]['sort']   = $key + 1;
								$allData[$i]['name']   = $inc['name'];
								$allData[$i]['status'] = $inc['status'];
								$allData[$i]['days']   = $inc['days'];
								$allData[$i]['count']  = $inc['count'];
								$i++;
							}
						}

					}
				}
				$count = count($notUser);
			}

			return [
				'count'      => $count,
				'allData'    => $allData,
				'seriesData' => $seriesData,
				'xData'      => $xData,
			];
		}

		/**
		 * @param $corpId
		 * @param $follow_id
		 * @param $s_date
		 * @param $e_date
		 * @param $status_id
		 * @param $user_ids
		 * @param $type
		 *
		 * @return array
		 *
		 */
		public static function getNotUserCount ($corpId, $follow_id, $s_date, $e_date, $status_id, $user_ids, $type = 0)
		{
			$followUser = WorkExternalContactFollowUser::find()->alias('f');
			$followUser = $followUser->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`');
			$followUser = $followUser->leftJoin('{{%work_user}} u', 'u.id=f.user_id');
			$followUser = $followUser->where(['c.corp_id' => $corpId, 'u.is_del' => 0, 'f.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
			if (!empty($s_date) && !empty($e_date)) {
				$followUser = $followUser->andWhere(['<', 'f.update_time', strtotime($s_date)]);
			}
			if (!empty($user_ids)) {
				$followUser = $followUser->andWhere(['f.user_id' => $user_ids]);
			}
			$status = '全部阶段';
			if (!empty($follow_id)) {
				$followUser = $followUser->andWhere(['f.follow_id' => $follow_id]);
				$follow     = Follow::findOne($follow_id);
				$status     = $follow->title;
			}
			$time1 = strtotime($s_date);
			$title = '全部';
			if ($status_id == WorkExternalContactFollowRecord::ALL_DAY) {
				$time2 = strtotime($e_date);
				//$followUser = $followUser->andWhere(['<', 'f.update_time', $time2]);
				$followUser = $followUser->andWhere('f.update_time = f.createtime');

			}
			if ($status_id == WorkExternalContactFollowRecord::ONE_DAY) {
				$time2      = strtotime($e_date) - (24 * 3600 * 1);
				$followUser = $followUser->andWhere(['<', 'f.update_time', $time2]);
				$title      = '1天';
			}
			if ($status_id == WorkExternalContactFollowRecord::THREE_DAY) {
				$time2      = strtotime($e_date) - (24 * 3600 * 3);
				$followUser = $followUser->andWhere(['<', 'f.update_time', $time2]);
				$title      = '3天';
			}
			if ($status_id > 0) {
				$day        = WorkNotFollowDay::findOne($status_id);
				$time2      = strtotime($e_date) - (24 * 3600 * $day->day);
				$followUser = $followUser->andWhere(['<', 'f.update_time', $time2]);
				$title      = $day->day . '天';
			}
			$result = [];
			if ($type == 0) {
				$select     = new Expression('count(DISTINCT(f.external_userid)) cc,f.user_id');
				$followUser = $followUser->select($select)->groupBy('f.user_id')->orderBy('cc desc');
				$followUser = $followUser->asArray()->all();
				if (!empty($followUser)) {
					foreach ($followUser as $key => $user) {
						$userInfo               = WorkUser::findOne($user['user_id']);
						$result[$key]['name']   = $userInfo->name;
						$result[$key]['status'] = $status;
						$result[$key]['days']   = $title;
						$result[$key]['count']  = $user['cc'];
					}
				}
			} else {
				$select     = new Expression('count(DISTINCT(f.external_userid)) cc,f.follow_id');
				$followUser = $followUser->select($select)->groupBy('f.follow_id')->orderBy('cc desc');
				//var_dump($followUser->createCommand()->getRawSql());die();
				$followUser = $followUser->asArray()->all();
				if (!empty($followUser)) {
					foreach ($followUser as $key => $user) {
						$follow                    = Follow::findOne($user['follow_id']);
						$result[$key]['name']      = $follow->title;
						$result[$key]['count']     = $user['cc'];
						$result[$key]['follow_id'] = $user['follow_id'];
					}
				}
			}

			return $result;
		}

		/**
		 * 获取未跟进天数
		 *
		 * @param     $uid
		 * @param int $type
		 *
		 * @return array
		 *
		 */
		public static function getDays ($uid)
		{
			$array1 = $array2 = [];
			for ($i = 0; $i < 2; $i++) {
				if ($i == 0) {
					$name = '超过1天未跟进';
					$id   = WorkExternalContactFollowRecord::ONE_DAY;
					$date = 1;
				} elseif ($i == 1) {
					$name = '超过3天未跟进';
					$id   = WorkExternalContactFollowRecord::THREE_DAY;
					$date = 3;
				}
				$array1[$i]['name'] = $name;
				$array1[$i]['id']   = $id;
				$array1[$i]['day']  = $date;
				$array1[$i]['num']  = $date . '_day';
			}
			$followDay = WorkNotFollowDay::find()->where(['uid' => $uid, 'is_del' => 0])->asArray()->all();
			if (!empty($followDay)) {
				foreach ($followDay as $key => $day) {
					$array2[$key]['id']   = $day['id'];
					$array2[$key]['day']  = $day['day'];
					$array2[$key]['name'] = '超过' . $day['day'] . '天未跟进';
					$array2[$key]['num']  = $day['day'] . '_day';
				}
			}
			$days       = array_merge($array1, $array2);
			$last_names = array_column($days, 'day');
			array_multisort($last_names, SORT_ASC, $days);
			if (!empty($days)) {
				foreach ($days as $k => $v) {
					$days[$k]['key'] = $k;
				}
			}

			return $days;
		}

		/**
		 * @param $uid
		 * @param $s_date
		 * @param $e_date
		 * @param $corpId
		 * @param $follow_id
		 * @param $source 2 跟进客户数 3 跟进次数
		 * @param $user_ids
		 *
		 * @return array
		 *
		 */
		public static function userCount ($uid, $s_date, $e_date, $corpId, $follow_id, $source, $user_ids)
		{
			$array1 = [];//主账户的
			$array2 = [];//子账户的
			$array3 = [];//员工的

			$master = static::returnData($corpId, $s_date, $e_date, $follow_id, 1, $source, $user_ids);
			if (!empty($master['master'])) {
				$userInfo            = UserProfile::findOne(['uid' => $uid]);
				$name                = $userInfo->nick_name;
				$array1[0]['name']   = $name;
				$array1[0]['status'] = $master['status'];
				$array1[0]['count']  = $master['master'];
				$array1[0]['depart'] = '';
				$array1[0]['avatar'] = '';
				$array1[0]['gender'] = 0;
			}
			$subUser = static::returnData($corpId, $s_date, $e_date, $follow_id, 2, $source, $user_ids);
			if (!empty($subUser['master'])) {
				foreach ($subUser['master'] as $key => $sub) {
					$subInfo = SubUserProfile::findOne(['sub_user_id' => $sub['sub_id']]);
					$depart  = '';
					$avatar  = '';
					$gender  = 0;
					$sub     = SubUser::findOne($subInfo->sub_user_id);
					if (!empty($sub)) {
						$workUser = WorkUser::findOne(['mobile' => $sub->account, 'is_del' => 0]);
						if (!empty($workUser)) {
							$avatar = $workUser->avatar;
							$gender = $workUser->gender;
							$depart = WorkDepartment::getDepartNameByUserId($workUser->id);
						}
					}
					$array2[$key]['name']   = $subInfo->name;
					$array2[$key]['status'] = $subUser['status'];
					$array2[$key]['count']  = $sub['cc'];
					$array2[$key]['depart'] = $depart;
					$array2[$key]['avatar'] = $avatar;
					$array2[$key]['gender'] = $gender;
				}
			}
			$userId = [];
			$user   = static::returnData($corpId, $s_date, $e_date, $follow_id, 3, $source, $user_ids);
			if (!empty($user['master'])) {
				$i = 0;
				foreach ($user['master'] as $key => $sub) {
					$userInfo = WorkUser::findOne(['id' => $sub['user_id'], 'is_del' => 0]);
					if (isset($userInfo) && $userInfo->is_external == 1) {
						array_push($userId, $userInfo->id);
						$array3[$i]['name']   = $userInfo->name;
						$array3[$i]['gender'] = $userInfo->gender;
						$array3[$i]['status'] = $user['status'];
						$array3[$i]['count']  = $sub['cc'];
						$array3[$i]['avatar'] = $userInfo->avatar;
						$departName           = WorkDepartment::getDepartNameByUserId($userInfo->id);
						$array3[$i]['depart'] = $departName;
						$i++;
					}
				}
			}
			$array  = (array_merge($array1, $array2, $array3));
			$status = '全部阶段';
			if (!empty($follow_id)) {
				$follow = Follow::findOne($follow_id);
				$status = $follow->title;
			}
			$uArray   = [];
			$workUser = WorkUser::find()->where(['corp_id' => $corpId, 'is_del' => 0, 'is_external' => 1]);
			if (!empty($user_ids)) {
				$workUser = $workUser->andWhere(['id' => $user_ids]);
			}
			$workUser = $workUser->asArray()->all();
			if (!empty($workUser)) {
				$i = 0;
				foreach ($workUser as $key => $user) {
					if (empty($userId)) {
						$uArray[$key]['name']   = $user['name'];
						$uArray[$key]['gender'] = $user['gender'];
						$uArray[$key]['status'] = $status;
						$uArray[$key]['count']  = 0;
						$uArray[$key]['avatar'] = $user['avatar'];
						$departName             = WorkDepartment::getDepartNameByUserId($user['id']);
						$uArray[$key]['depart'] = $departName;
					} else {
						if (!in_array($user['id'], $userId)) {
							$uArray[$i]['name']   = $user['name'];
							$uArray[$i]['gender'] = $user['gender'];
							$uArray[$i]['status'] = $status;
							$uArray[$i]['count']  = 0;
							$uArray[$i]['avatar'] = $user['avatar'];
							$departName           = WorkDepartment::getDepartNameByUserId($user['id']);
							$uArray[$i]['depart'] = $departName;
							$i++;
						}
					}
				}
			}
			if (empty($array)) {
				$array = $uArray;
			} else {
				$array = array_merge($array, $uArray);
			}
			$last_names = array_column($array, 'count');
			array_multisort($last_names, SORT_DESC, $array);

			return $array;
		}

		/**
		 * @param     $corpId
		 * @param     $s_date
		 * @param     $e_date
		 * @param     $follow_id
		 * @param     $type
		 * @param int $source 2 跟进客户数 3 跟进次数
		 * @param     $user_ids
		 *
		 * @return array
		 *
		 */
		public static function returnData ($corpId, $s_date, $e_date, $follow_id, $type, $source = 0, $user_ids)
		{
			$e_date = $e_date . ' 23:59:59';
			$master = static::find()->alias('r')->leftJoin('{{%work_external_contact}} c', '`r`.`external_id` = `c`.`id`')->where(['c.corp_id' => $corpId]);
			if (!empty($s_date) && !empty($e_date)) {
				$master = $master->andFilterWhere(['between', 'r.time', strtotime($s_date), strtotime($e_date)]);
			}
			if (!empty($user_ids)) {
				$master = $master->andWhere(['r.user_id' => $user_ids]);
			}
			$status = '全部阶段';
			if (!empty($follow_id)) {
				$master = $master->andWhere(['r.follow_id' => $follow_id]);
				$follow = Follow::findOne($follow_id);
				$status = $follow->title;
			}
			if ($type == 1) {
				if ($source == 3) {
					$master = $master->andWhere(['r.sub_id' => 0, 'r.user_id' => 0])->count();
				} else {
					$master = $master->andWhere(['r.sub_id' => 0, 'r.user_id' => 0])->groupBy('r.external_id')->count();
				}
			} elseif ($type == 2) {
				if ($source == 3) {
					$select = new Expression('count(r.external_id) cc,r.sub_id');
				} else {
					$select = new Expression('count(DISTINCT(r.external_id)) cc,r.sub_id');
				}
				$master = $master->andWhere(['r.user_id' => 0])->andWhere(['!=', 'r.sub_id', 0])->select($select)->groupBy('r.sub_id');
				$master = $master->asArray()->all();
			} elseif ($type == 3) {
				if ($source == 3) {
					$select = new Expression('count(r.external_id) cc,r.user_id');
				} else {
					$select = new Expression('count(DISTINCT(r.external_id)) cc,r.user_id');
				}
				$master = $master->andWhere(['!=', 'r.user_id', 0])->select($select)->groupBy('r.user_id');
				$master = $master->asArray()->all();
			}

			return [
				'status' => $status,
				'master' => $master,
			];
		}

		/**
		 * @param $type
		 * @param $corpId
		 * @param $follow_id
		 * @param $s_date
		 * @param $e_date
		 * @param $user_ids
		 * @param $days
		 * @param $page
		 * @param $pageSize
		 * @param $is_export
		 *
		 * @return array
		 *
		 */
		public static function getFollowStatus ($type, $corpId, $follow_id, $s_date, $e_date, $user_ids, $days, $page, $pageSize, $is_export)
		{
			$dateData = $userData = [];
			if ($type == 1) {
				$userData = static::getNotUserData($corpId, $s_date, $e_date, $follow_id, $user_ids, $days, $page, $pageSize, $is_export);
			} else {
				$dateData = static::getNotDateData($corpId, $s_date, $e_date, $days, $page, $pageSize, $is_export, $follow_id, $user_ids);
			}

			return [
				'userData' => $userData,
				'dateData' => $dateData,
			];
		}

		/**
		 * @param     $corpId
		 * @param     $s_date
		 * @param     $e_date
		 * @param     $follow_id
		 * @param     $user_ids
		 * @param     $days
		 * @param int $page
		 * @param int $pageSize
		 * @param int $export
		 *
		 * @return array
		 *
		 */
		public static function getNotUserData ($corpId, $s_date, $e_date, $follow_id, $user_ids, $days, $page = 1, $pageSize = 10, $export = 0)
		{
//			$workUser = WorkExternalContactFollowUser::find()->alias('f');
//			$workUser = $workUser->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')->where(['c.corp_id' => $corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
//			if (!empty($user_ids)) {
//				$workUser = $workUser->andWhere(['f.user_id' => $user_ids]);
//			}
//			$count  = $workUser->select('f.user_id')->groupBy('f.user_id')->count();
//			$offset = ($page - 1) * $pageSize;
//			if ($export == 0) {
//				$workUser = $workUser->limit($pageSize)->offset($offset);
//			}
//			$workUser  = $workUser->select('f.user_id')->groupBy('f.user_id');
//			$workUser  = $workUser->asArray()->all();
//			$userIds   = array_column($workUser, 'user_id');
			$workUser = WorkUser::find()->where(['corp_id' => $corpId, 'is_external' => 1]);
			if (!empty($user_ids)) {
				$workUser = $workUser->andWhere(['id' => $user_ids]);
			}
			$count  = $workUser->count();
			$offset = ($page - 1) * $pageSize;
			if ($export == 0) {
				$workUser = $workUser->limit($pageSize)->offset($offset);
			}
			$workUser = $workUser->select('id')->asArray()->all();
			$userIds  = array_column($workUser, 'id');
			$userData = [];
			if (!empty($days) && !empty($userIds)) {
				foreach ($userIds as $key => $userId) {
					$user                     = WorkUser::findOne($userId);
					$userData[$key]['name']   = $user->name;
					$userData[$key]['avatar'] = $user->avatar;
					$userData[$key]['gender'] = $user->gender;
					$departName               = WorkDepartment::getDepartNameByUserId($user->id);
					$userData[$key]['depart'] = $departName;
					$flag                     = 0;
					foreach ($days as $dd) {
						$userData[$key][$dd['num']] = 0;
						$followUser                 = WorkExternalContactFollowUser::find()->alias('f');
						$followUser                 = $followUser->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')->where(['c.corp_id' => $corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
						if (!empty($user_ids)) {
							$followUser = $followUser->andWhere(['f.user_id' => $user_ids]);
						}
						if (!empty($follow_id)) {
							$followUser = $followUser->andWhere(['f.follow_id' => $follow_id]);
						}
						$time1      = strtotime($s_date);
						$time2      = strtotime($e_date) - (24 * 3600 * $dd['day']);
						$followUser = $followUser->andWhere(['<', 'f.update_time', $time2]);
						$select     = new Expression('count(DISTINCT(f.external_userid)) cc,f.user_id');
						$followUser = $followUser->select($select)->groupBy('f.user_id')->orderBy('cc desc');
						$followUser = $followUser->asArray()->all();
						if (!empty($followUser)) {
							foreach ($followUser as $user) {
								if ($user['user_id'] == $userId) {
									$flag                       = 1;
									$userData[$key][$dd['num']] = $user['cc'];
								}
							}
							if ($flag == 0) {
								$userData[$key][$dd['num']] = 0;
							}
						}
					}
				}

			}
			$info = [
				'count'    => $count,
				'userData' => $userData,
			];

			return $info;
		}

		/**
		 * @param       $corpId
		 * @param       $s_date
		 * @param       $e_date
		 * @param       $days
		 * @param int   $page
		 * @param int   $pageSize
		 * @param int   $export
		 * @param int   $follow_id
		 * @param array $user_ids
		 *
		 * @return array
		 *
		 */
		public static function getNotDateData ($corpId, $s_date, $e_date, $days, $page = 1, $pageSize = 10, $export = 0, $follow_id, $user_ids)
		{
			$dateData = [];
			$offset   = ($page - 1) * $pageSize;
			$date     = DateUtil::getDateFromRange($s_date, $e_date);
			if (!empty($days) && !empty($date)) {
				$i = 0;
				foreach ($date as $key => $day) {
					if ($export == 1) {
						$dateData[$key]['name'] = $day;
						foreach ($days as $dd) {
							$num                        = static::getFollowUser($s_date, $day, $dd['day'], $corpId, $follow_id, $user_ids);
							$dateData[$key][$dd['num']] = $num;
						}
					} else {
						if ($key < ($page * $pageSize) && $key >= $offset) {
							$dateData[$i]['name'] = $day;
							foreach ($days as $dd) {
								$num                      = static::getFollowUser($s_date, $day, $dd['day'], $corpId, $follow_id, $user_ids);
								$dateData[$i][$dd['num']] = $num;
							}
							$i++;
						}
					}
				}
			}
			$info = [
				'count'    => count($date),
				'dateData' => $dateData,
			];

			return $info;
		}

		/**
		 * @param       $s_date
		 * @param       $e_date
		 * @param       $day
		 * @param       $corpId
		 * @param int   $follow_id
		 * @param array $user_ids
		 *
		 * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord|null
		 *
		 */
		public static function getFollowUser ($s_date, $e_date, $day, $corpId, $follow_id = 0, $user_ids)
		{
//			$followUser1 = WorkExternalContactFollowUser::find()->alias('f');
//			$followUser1 = $followUser1->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')->leftJoin('{{%work_external_contact_follow_record}} r', '`f`.`external_userid` = `r`.`external_id` and `f`.user_id=`r`.user_id')->where(['c.corp_id' => $corpId, 'f.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX,WorkExternalContactFollowUser::NO_ASSIGN]]);
//			if (!empty($user_ids)) {
//				$followUser1 = $followUser1->andWhere(['f.user_id' => $user_ids]);
//			}
//			if (!empty($follow_id)) {
//				$followUser1 = $followUser1->andWhere(['f.follow_id' => $follow_id]);
//			}
//			$time2       = strtotime($e_date) - (24 * 3600 * $day);
//			$followUser1 = $followUser1->andWhere(['<', 'f.update_time', $time2]);
//			$followUser1 = $followUser1->andFilterWhere(['between', 'r.time', $time2, strtotime($e_date . ' 23:59:59')]);
//			$followUser1 = $followUser1->select('f.external_userid,f.user_id');
//			$followUser1 = $followUser1->asArray()->all();
//			$resData     = SUtils::array_unique_two($followUser1);
//			$num         = count($resData);

			$followUser = WorkExternalContactFollowUser::find()->alias('f');
			$followUser = $followUser->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')->where(['c.corp_id' => $corpId, 'f.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
			if (!empty($user_ids)) {
				$followUser = $followUser->andWhere(['f.user_id' => $user_ids]);
			}
			if (!empty($follow_id)) {
				$followUser = $followUser->andWhere(['f.follow_id' => $follow_id]);
			}

			$e_date     = $e_date . ' ' . date('H:i');
			$time2      = strtotime($e_date) - (24 * 3600 * $day);
			$followUser = $followUser->andWhere(['<', 'f.update_time', $time2]);
			$followUser = $followUser->select('f.external_userid,f.user_id');
			//$followUser = $followUser->asArray()->all();
			$num1 = $followUser->count();
			//$num1       = count($followUser);
//			if (!empty($followUser)) {
//				foreach ($followUser as $user) {
//					$record = WorkExternalContactFollowRecord::find()->where(['external_id' => $user['external_userid'], 'user_id' => $user['user_id']])->andFilterWhere(['between', 'time', $time2, strtotime($e_date . ' 23:59:59')])->one();
//					if (!empty($record)) {
//						$num1--;
//					}
//				}
//			}

			//$num1 = $num1 - $num;

			return $num1;
		}

		/**
		 * @param $corpId
		 * @param $follow_id
		 * @param $s_date
		 * @param $e_date
		 * @param $status_id
		 * @param $user_ids
		 * @param $uid
		 *
		 * @return array
		 *
		 */
		public static function getPiedata ($corpId, $follow_id, $s_date, $e_date, $status_id, $user_ids, $uid)
		{
			$legData    = [];
			$xData      = [];//X轴
			$seriesData = [];//Y轴数据
			$pieData    = [];//饼状图
			$notUser    = static::getNotUserCount($corpId, $follow_id, $s_date, $e_date, $status_id, $user_ids, 1);
			$nowDay     = 0;
			if ($status_id == WorkExternalContactFollowRecord::ONE_DAY) {
				$nowDay = 1;
			}
			if ($status_id == WorkExternalContactFollowRecord::THREE_DAY) {
				$nowDay = 3;
			}
			if ($status_id > 0) {
				$day    = WorkNotFollowDay::findOne($status_id);
				$nowDay = $day->day;
			}

			$follow = Follow::find()->where(['status' => 1, 'uid' => $uid])->asArray()->all();
			$xData  = DateUtil::getDateFromRange($s_date, $e_date);
			if (!empty($follow)) {
				foreach ($follow as $key => $foll) {
					array_push($legData, $foll['title']);
					if (!empty($notUser)) {
						$flag = 0;
						foreach ($notUser as $k => $inc) {
							if ($inc['follow_id'] == $foll['id']) {
								$flag                   = 1;
								$pieData[$key]['name']  = $inc['name'];
								$pieData[$key]['value'] = $inc['count'];
							}
						}
						if ($flag == 0) {
							$pieData[$key]['name']  = $foll['title'];
							$pieData[$key]['value'] = 0;
						}
					} else {
						$pieData[$key]['name']  = $foll['title'];
						$pieData[$key]['value'] = 0;
					}
					$datData = [];
					foreach ($xData as $day) {
						$num = static::getFollowUser($s_date, $day, $nowDay, $corpId, $foll['id'], $user_ids);
						array_push($datData, $num);
					}
					$seriesData[$key]['name']   = $foll['title'];
					$seriesData[$key]['type']   = 'line';
					$seriesData[$key]['smooth'] = true;
					$seriesData[$key]['data']   = $datData;
				}
			}
			$value = 0;
			if (count($pieData) >= 8) {
				foreach ($pieData as $key => $val) {
					if ($key >= 7) {
						$value += $val['value'];
					}
				}
				$pieData[7]['其他'] = $value;
			}

			return [
				'legData'    => $legData,
				'xData'      => $xData,
				'seriesData' => $seriesData,
				'pieData'    => $pieData,
			];

		}

		/**
		 * @param     $corpId
		 * @param     $s_date
		 * @param     $e_date
		 * @param     $user_ids
		 * @param int $page
		 * @param int $pageSize
		 * @param int $export
		 * @param     $uid
		 * @param     $type
		 *
		 * @return array
		 *
		 */
		public static function getThreeData ($corpId, $s_date, $e_date, $user_ids, $page = 1, $pageSize = 10, $export = 0, $uid, $type)
		{
			$xData      = [];
			$legData    = ['跟进客户数', '跟进次数'];
			$seriesData = [];
			$allData    = [];
			$date       = DateUtil::getDateFromRange($s_date, $e_date);
			$data1      = [];
			$data2      = [];
			$offset     = ($page - 1) * $pageSize;
			$i          = 0;
			foreach ($date as $key => $day) {
				if ($export == 1) {
					$time1      = strtotime($day);
					$time2      = strtotime($day . ' 23:59:59');
					$workRecord = static::getRecordCount($corpId, $time1, $time2, $user_ids, 1);
					$userRecord = static::getRecordCount($corpId, $time1, $time2, $user_ids, 2);
					array_push($data1, $workRecord['cc']);
					array_push($data2, $userRecord['cc']);
					$allData[$key]['name']      = $day;
					$allData[$key]['userNum']   = $workRecord['cc'];
					$allData[$key]['recordNum'] = $userRecord['cc'];
				} else {
					$time1       = strtotime($day);
					$time2       = strtotime($day . ' 23:59:59');
					$workRecord1 = static::getRecordCount($corpId, $time1, $time2, $user_ids, 1);
					$userRecord1 = static::getRecordCount($corpId, $time1, $time2, $user_ids, 2);
					array_push($data1, $workRecord1['cc']);
					array_push($data2, $userRecord1['cc']);
					if ($key < ($page * $pageSize) && $key >= $offset) {
						$workRecord               = static::getRecordCount($corpId, $time1, $time2, $user_ids, 1);
						$userRecord               = static::getRecordCount($corpId, $time1, $time2, $user_ids, 2);
						$allData[$i]['name']      = $day;
						$allData[$i]['userNum']   = $workRecord['cc'];
						$allData[$i]['recordNum'] = $userRecord['cc'];
						$i++;
					}
				}
			}
			$seriesData[0]['name']   = '跟进客户数';
			$seriesData[0]['type']   = 'line';
			$seriesData[0]['smooth'] = true;
			$seriesData[0]['data']   = $data1;
			$seriesData[1]['name']   = '跟进次数';
			$seriesData[1]['type']   = 'line';
			$seriesData[1]['smooth'] = true;
			$seriesData[1]['data']   = $data2;

			$xData = $date;
			$users = [];
			$count = count($date);
			if ($type == 2) {
				$userData = static::userCount($uid, $s_date, $e_date, $corpId, 0, 1, $user_ids);
				\Yii::error($userData, '$userData');
				$recordData = static::userCount($uid, $s_date, $e_date, $corpId, 0, 3, $user_ids);
				$i          = 0;
				foreach ($userData as $data) {
					foreach ($recordData as $key => $record) {
						if ($record['name'] == $data['name']) {
							if ($export == 1) {
								$users[$key]['gender']    = $record['gender'];
								$users[$key]['name']      = $record['name'];
								$users[$key]['userNum']   = $data['count'];
								$users[$key]['recordNum'] = $record['count'];
								$users[$key]['avatar']    = $data['avatar'];
								$users[$key]['depart']    = $data['depart'];
							} else {
								if ($key < ($page * $pageSize) && $key >= $offset) {
									$users[$i]['gender']    = $record['gender'];
									$users[$i]['name']      = $record['name'];
									$users[$i]['userNum']   = $data['count'];
									$users[$i]['recordNum'] = $record['count'];
									$users[$i]['avatar']    = $data['avatar'];
									$users[$i]['depart']    = $data['depart'];
									$i++;
								}
							}

						}
					}
				}
				$count = count($userData);
			}

			return [
				'users'      => $users,
				'legData'    => $legData,
				'xData'      => $xData,
				'seriesData' => $seriesData,
				'allData'    => $allData,
				'count'      => $count,
			];
		}

		/**
		 * @param $corpId
		 * @param $time1
		 * @param $time2
		 * @param $user_ids
		 * @param $type
		 *
		 * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord|null
		 *
		 */
		public static function getRecordCount ($corpId, $time1, $time2, $user_ids, $type)
		{
			$workRecord = WorkExternalContactFollowRecord::find()->alias('r');
			$workRecord = $workRecord->leftJoin('{{%work_external_contact}} c', '`r`.`external_id` = `c`.`id`')->where(['c.corp_id' => $corpId]);
			$workRecord = $workRecord->andFilterWhere(['between', 'r.time', $time1, $time2]);
			if (!empty($user_ids)) {
				$workRecord = $workRecord->andWhere(['r.user_id' => $user_ids]);
			}
			if ($type == 1) {
				$select = new Expression('count(DISTINCT(r.external_id)) cc');
			} else {
				$select = new Expression('count(r.id) cc');
			}
			$workRecord = $workRecord->select($select)->orderBy('cc desc');
			$workRecord = $workRecord->asArray()->one();

			return $workRecord;
		}

		/**
		 * @param $followId
		 * @param $uid
		 * @param $corpId
		 * @param $user_ids
		 * @param $s_date
		 * @param $e_date
		 *
		 * @return array
		 *
		 */
		public static function getHopper ($followId, $uid, $corpId, $user_ids, $s_date, $e_date)
		{
//			$s_date     = "2020-07-01";
//			$e_date     = "2020-12-29";
			$lastFollow = Follow::find()->where(['uid' => $uid, 'status' => 1])->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])->one();
			$data       = [];
			$follow     = Follow::find()->where(['uid' => $uid, 'status' => 1])->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->asArray()->all();
			$num        = 0;
			if (!empty($follow)) {
				foreach ($follow as $key => $foll) {
					$data[$key]['name'] = $foll['title'];
					$StartTime          = microtime(true);
					$user               = WorkExternalContactFollowRecord::getDateUser($followId, $uid, $corpId, $foll['id'], $user_ids, $s_date, $e_date, 0);
					$EndTime            = microtime(true);
					Yii::error($EndTime - $StartTime, "sym-run-time");
					$data[$key]['nextNum'] = $user['nextNum'];
					$data[$key]['num']     = $user['num'];
					$num                   += $user['num'];
					$everyDay              = 0;
					if ($user['totalDay'] > 0) {
						$everyDay = round($user['totalDay'] / ($user['nextNum'] + $user['num']));
					}
					if ($user['longDay'] <= 0) {
						$user['longDay'] = 0;
					}
					$data[$key]['everyDay'] = intval($everyDay);
					$data[$key]['day']      = $user['longDay'];//最长停留天数
					$rate                   = '0%';
					if ($user['nextNum'] > 0) {
						$number = number_format($user['nextNum'] / ($user['nextNum'] + $user['num']), 4);
						$number = $number * 100;
						$rate   = number_format($number, 2) . '%';
					}
					if ($lastFollow->id == $foll['id']) {
						$rate = '--';
					}
					$data[$key]['rate'] = $rate;
				}
			}

			return [
				'data' => $data,
				'num'  => $num,
			];
		}

		/**
		 * @param     $followId
		 * @param     $uid
		 * @param     $corpId
		 * @param     $follow_id
		 * @param     $user_ids
		 * @param     $s_date
		 * @param     $e_date
		 * @param     $follUser
		 * @param     $recordNew
		 * @param int $from
		 * @param int $type
		 *
		 * @return array
		 *
		 */
		public static function getUserData ($followId, $uid, $corpId, $follow_id, $user_ids, $s_date, $e_date, $follUser, $recordNew, $from = 0, $type = 0)
		{
			$startTime = microtime(true);
			Yii::error($startTime, '$startTime');
			$stayDay    = $userDay = $result = [];
			$e_date     = $e_date . ' 23:59:59';
			$num        = 0; //当前客户人数
			$nextNum    = 0; //转化人数
			$followUser = WorkExternalContactFollowUser::find()->alias('f')
				->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')
				->where(['c.corp_id' => $corpId, 'f.follow_id' => $follow_id, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
			if (!empty($user_ids)) {
				$followUser = $followUser->andWhere(['user_id' => $user_ids]);
			}
			if (!empty($s_date) && !empty($e_date)) {
				$followUser = $followUser->andFilterWhere(['between', 'f.createtime', strtotime($s_date), strtotime($e_date)]);
			}
			$followUser = $followUser->select('f.update_time,f.createtime,f.follow_id,f.external_userid,f.user_id');
			Yii::error($followUser->createCommand()->getRawSql(), "WorkExternalContactFollowUser");
			$followUser = $followUser->asArray()->all(); //当前客户人数
			if (!empty($followUser)) {
				foreach ($followUser as $user) {
					if ($user['createtime'] > $user['update_time']) {
						$user['update_time'] = $user['createtime'];
					}
					if ($user['update_time'] >= strtotime($s_date) && $user['update_time'] <= strtotime($e_date)) {
						++$num;
						$stayDay[] = $user['update_time'];
						$userDay[] = $user['external_userid'] . '-' . $user['user_id'];
					}
				}
			}
			$nextFollowUser = static::getNowExternalContact($corpId, $user_ids, $s_date, $e_date);
			if (!empty($nextFollowUser)) {
				/**提取外部联系人权限**/
				$extIds = array_column($nextFollowUser, "external_id");
				$userIds = array_column($nextFollowUser, "user_id");

				$record  = WorkExternalContactFollowRecord::find()->where(['follow_id' => $follow_id]);
				$record  = $record->andWhere(["in", "external_id", $extIds]);
				$record  = $record->andWhere(["in", "user_id", $userIds]);
				$record  = $record->andWhere(["uid"=>$uid]);
				$record  = $record->select("external_id,id")->asArray()->all();
				$newData = array_column($record, "id", "external_id");

				$nowRecord = WorkExternalContactFollowRecord::find()->alias("a")
					->leftJoin("{{%work_external_contact_follow_user}} as f", "a.external_id = f.external_userid and a.user_id = f.user_id")
					->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`');
				$nowRecord = $nowRecord->where(['c.corp_id' => $corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
				if (!empty($user_ids)) {
					$nowRecord = $nowRecord->andWhere(['f.user_id' => $user_ids]);
				}
				if (!empty($s_date) && !empty($e_date)) {
					$nowRecord = $nowRecord->andFilterWhere(['between', 'f.createtime', strtotime($s_date), strtotime($e_date)]);
				}
				$nowRecord = $nowRecord->andWhere(['>', 'a.follow_id', $follow_id]);
				$nowRecord = $nowRecord->andFilterWhere(['between', 'a.time', strtotime($s_date), strtotime($e_date)]);
				Yii::error($nowRecord->createCommand()->getRawSql(), "WorkExternalContactFollowRecord");
				$nowRecord    = $nowRecord->select("a.external_id,a.user_id,a.time")->asArray()->all();
				$newData2     = array_column($nowRecord, "user_id", "external_id");
				$newData2Temp = array_column($nowRecord, "time", "external_id");

				foreach ($nextFollowUser as $user) {
					if (isset($newData[$user['external_userid']])) {
						if (isset($newData2[$user['external_userid']])) {
							$nextNum++;
							$stayDay[] = $newData2Temp[$user['external_userid']];
							$userDay[] = $user['external_userid'] . '-' . $newData2[$user['external_userid']];
						}
					} else {
						if ($followId == $follow_id) {
							if (isset($newData2[$user['external_userid']])) {
								$nextNum++;
								$stayDay[] = $newData2Temp[$user['external_userid']];
								$userDay[] = $user['external_userid'] . '-' . $newData2[$user['external_userid']];
							}
						}
					}
				}
			}
			$endTime = microtime(true);
			Yii::error($endTime - $startTime, 'endTime');
			$data1 = $stayDay;
			$data2 = $userDay;
			if ($from == 0 && $type == 1) {
				$resultDay = static::returnDay($data1, $data2, $follow_id, $followId, time(), $follUser, $recordNew);
				$longDay   = $resultDay['longDay'];
				$totalDay  = $resultDay['totalDay'];
			} else {
				$longDay  = 0;
				$totalDay = 0;
			}
			$newData = [];
			if (!empty($userDay) && !empty($stayDay)) {
				foreach ($userDay as $k => $user) {
					if (isset($stayDay[$k])) {
						$temp         = [];
						$temp['user'] = $user;
						$temp['time'] = $stayDay[$k];
						array_push($newData, $temp);
					}
				}
			}
			$endTime = microtime(true);
			Yii::error($endTime - $startTime, 'endTime');

			return [
				'num'      => $num,
				'nextNum'  => $nextNum,
				'longDay'  => $longDay,
				'totalDay' => $totalDay,
				'stayDay'  => $stayDay,
				'userDay'  => $userDay,
				'newData'  => $newData,
			];

		}

		/**
		 * @param $corpId
		 * @param $user_ids
		 * @param $s_date
		 * @param $e_date
		 *
		 * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord[]
		 *
		 */
		public static function getNowExternalContact ($corpId, $user_ids, $s_date, $e_date)
		{
			$followUser = WorkExternalContactFollowUser::find()->alias('f')
				->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')
				->leftJoin('{{%work_user}} as d', 'f.user_id = d.id')
				->where(['d.corp_id' => $corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
			if (!empty($user_ids)) {
				$followUser = $followUser->andWhere(['f.user_id' => $user_ids]);
			}
			$time = 'f.createtime';
			if (!empty($s_date) && !empty($e_date)) {
				$followUser = $followUser->andFilterWhere(['between', $time, strtotime($s_date), strtotime($e_date)]);
			}
			$followUser = $followUser->select('f.user_id,f.external_userid,f.follow_id');
			Yii::error($followUser->createCommand()->getRawSql(), "getNowExternalContact");
			$followUser = $followUser->asArray()->all();

			return $followUser;
		}

		/**
		 * @param $followId
		 * @param $uid
		 * @param $corpId
		 * @param $follow_id
		 * @param $user_ids
		 * @param $s_date
		 * @param $e_date
		 * @param $from 1 简报 0 下面的详细数据
		 *
		 * @return array
		 *
		 */
		public static function getDateUser ($followId, $uid, $corpId, $follow_id, $user_ids, $s_date, $e_date, $from = 0)
		{
			$StartTime1 = microtime(true);
			Yii::error($StartTime1, "getDateUser");
			$follUser = [];
			$record   = [];
			if ($from == 0) {
				if ($followId == $follow_id) {
					$follUser = WorkExternalContactFollowUser::find()->alias('f')->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`');
					$follUser = $follUser->where(['c.corp_id' => $corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->select('f.createtime,f.user_id,f.external_userid');
					$follUser = $follUser->asArray()->all();
				} else {
					$record = WorkExternalContactFollowRecord::find()->where(['uid' => $uid])->asArray()->all();
				}
			}
			$nextNum  = 0;
			$flag     = 0;
			$num      = 0;
			$longDay  = 0;
			$totalDay = 0;
			$date     = date('Y-m-d');
			if ($e_date == $date) {
				$flag = 1;
			}
			if ($flag == 1 && strtotime($e_date) >= strtotime($s_date)) {
				if (strtotime($e_date) == strtotime($s_date)) {
					$StartTime = microtime(true);
					Yii::error($StartTime, "getUserData1");
					$userData = static::getUserData($followId, $uid, $corpId, $follow_id, $user_ids, $s_date, $e_date, $follUser, $record, $from, 1);
					$EndTime  = microtime(true);
					Yii::error($EndTime - $StartTime, "getUserData1");
					$num      = $userData['num'];
					$nextNum  = $userData['nextNum'];
					$longDay  = $userData['longDay'];
					$totalDay = $userData['totalDay'];
				}
				if (strtotime($e_date) > strtotime($s_date)) {

					$StartTime = microtime(true);
					Yii::error($StartTime, "getUserData2");
					$userData = static::getUserData($followId, $uid, $corpId, $follow_id, $user_ids, $e_date, $e_date, $follUser, $record, $from, 0);
					$EndTime  = microtime(true);
					Yii::error($EndTime - $StartTime, "getUserData2");
					$endDate   = date('Y-m-d', strtotime('-1 day', time()));
					$StartTime = microtime(true);
					Yii::error($StartTime, "getCount");
					$userCount = static::getCount($followId, $corpId, $follow_id, $s_date, $endDate, $uid, $user_ids, $from, $follUser, $record);
					$EndTime   = microtime(true);
					Yii::error($EndTime - $StartTime, "returnDay");
					$num     = $userData['num'] + $userCount['num'];
					$nextNum = $userData['nextNum'] + $userCount['nextNum'];
					$newData = $userCount['newData'] + $userData['newData'];
					if (!empty($newData)) {
						$userDay = [];
						$stayDay = [];
						$T1      = array_column($newData, "user");
						$T2      = array_column($newData, "time");
						array_push($userDay, ...$T1);
						array_push($stayDay, ...$T2);
						$userDay   = array_unique($userDay);
						$stayDay   = array_unique($stayDay);
						$StartTime = microtime(true);
						Yii::error($StartTime, "returnDay");
						$resultDay = static::returnDay($stayDay, $userDay, $follow_id, $followId, strtotime($e_date . ' 23:59:59'), $follUser, $record);
						$EndTime   = microtime(true);
						Yii::error($EndTime - $StartTime, "returnDay");
						$longDay  = $resultDay['longDay'];
						$totalDay = $resultDay['totalDay'];
					}

				}
			}
			if ($flag == 0) {
				$userCount = static::getCount($followId, $corpId, $follow_id, $s_date, $e_date, $uid, $user_ids, $from, $follUser, $record);
				$num       = $userCount['num'];
				$nextNum   = $userCount['nextNum'];
				$longDay   = $userCount['longDay'];
				$totalDay  = $userCount['totalDay'];
			}
			$EndTime1 = microtime(true);
			Yii::error($EndTime1 - $StartTime1, "getDateUser");
			if ($num <= 0) {
				$num = 0;
			}

			return [
				'num'      => $num,
				'nextNum'  => $nextNum,
				'longDay'  => $longDay,
				'totalDay' => $totalDay,
			];
		}

		/**
		 * @param $corpId
		 * @param $follow_id
		 * @param $s_date
		 * @param $e_date
		 * @param $uid
		 * @param $user_ids
		 * @param $followStatistic
		 * @param $from 1 简报
		 *
		 * @return array
		 *
		 */
		public static function getCount1 ($corpId, $follow_id, $s_date, $e_date, $uid, $user_ids, $followStatistic, $from)
		{
			$stayDay   = [];
			$userDay   = [];
			$result    = [];
			$num       = 0;
			$nextNum   = 0;
			$followNew = Follow::findOne(['uid' => $uid, 'status' => 1]);
			//当前日期下的每个状态的客户数
			$followStatistic1 = WorkExternalContactFollowStatistic::find()->where(['corp_id' => $corpId, 'follow_id' => $follow_id, 'type' => 1]);
			if (!empty($user_ids)) {
				$followStatistic1 = $followStatistic1->andWhere(['user_id' => $user_ids]);
			}
			$num = $followStatistic1->andFilterWhere(['between', 'data_time', $s_date, $e_date])->count();

			//统计表里 当前所有状态的客户
			if (!empty($followStatistic)) {
				foreach ($followStatistic as $follow) {
					//查询当前跟进状态 下一个跟进状态的记录
					$record = WorkExternalContactFollowRecord::find()->where(['external_id' => $follow['external_userid'], 'user_id' => $follow['user_id']])->andWhere(['>', 'follow_id', $follow_id]);
					$record = $record->andFilterWhere(['between', 'time', strtotime($s_date), strtotime($e_date . ' 23:59:59')])->groupBy('external_id')->asArray()->all();
					if (!empty($record)) {
						//如果当前是未跟进 则下一个跟进状态的所有客户都算转化人数
						if ($followNew->id == $follow_id) {
							$nextNum += count($record);
							if (!empty($record)) {
								foreach ($record as $rec) {
									array_push($stayDay, $rec['time']);
									array_push($userDay, $rec['external_id'] . '-' . $rec['user_id']);
								}
							}
						} else {
							//当前不是未跟进
							$record = WorkExternalContactFollowRecord::find()->where(['external_id' => $follow['external_userid'], 'user_id' => $follow['user_id'], 'follow_id' => $follow_id]);
							$record = $record->andFilterWhere(['between', 'time', strtotime($s_date), strtotime($e_date . ' 23:59:59')])->orderBy(['id' => SORT_DESC])->one();
							if (!empty($record)) {
								$nextNum += count($record);
								if (!empty($record)) {
									array_push($stayDay, $record['time']);
									array_push($userDay, $record['external_id'] . '-' . $record['user_id']);
								}
							}
						}
					} else {
						if ($from == 0) {
							$followUser = WorkExternalContactFollowUser::findOne(['external_userid' => $follow['external_userid'], 'user_id' => $follow['user_id']]);
							if (!empty($followUser) && $followNew->id == $follow_id) {
								array_push($stayDay, $followUser->update_time);
								array_push($userDay, $followUser->external_userid . '-' . $followUser->user_id);
							}
						}
					}
					$result[$follow_id][0] = $stayDay;
					$result[$follow_id][1] = $userDay;
				}
			}
			if ($from == 0) {
				$data1     = isset($result[$follow_id][0]) ? $result[$follow_id][0] : [];
				$data2     = isset($result[$follow_id][1]) ? $result[$follow_id][1] : [];
				$resultDay = static::returnDay($data1, $data2, $follow_id, $followNew->id, strtotime($e_date . ' 23:59:59'));
				$longDay   = $resultDay['longDay'];
				$totalDay  = $resultDay['totalDay'];
			} else {
				$longDay  = 0;
				$totalDay = 0;
			}

			return [
				'longDay'  => $longDay,
				'totalDay' => $totalDay,
				'num'      => $num,
				'nextNum'  => $nextNum,
			];

		}

		/**
		 * @param $followId
		 * @param $corpId
		 * @param $follow_id
		 * @param $s_date
		 * @param $e_date
		 * @param $uid
		 * @param $user_ids
		 * @param $from
		 * @param $follUser
		 * @param $record
		 *
		 * @return array
		 *
		 */
		public static function getCount ($followId, $corpId, $follow_id, $s_date, $e_date, $uid, $user_ids, $from, $follUser, $record)
		{

			$stayDay   = [];
			$userDay   = [];
			$nextNum   = 0;
			$StartTime = microtime(true);
			//当前日期下的每个状态的客户数
			$followStatistic1 = WorkExternalContactFollowStatistic::find()->where(['corp_id' => $corpId, 'follow_id' => $follow_id, 'type' => 1]);
			if (!empty($user_ids)) {
				$followStatistic1 = $followStatistic1->andWhere(['user_id' => $user_ids]);
			}
			$num = $followStatistic1->andFilterWhere(['between', 'data_time', $s_date, $e_date])->count();

			$notFollow        = [];
			$notFollowTime    = [];
			$i                = 0;
			$followStatistic3 = WorkExternalContactFollowStatistic::find()->alias('f')
				->leftJoin('{{%work_external_contact_follow_record}} c', '`f`.`external_userid` = `c`.`external_id` and f.user_id=c.user_id')
				->leftJoin('{{%work_external_contact_follow_user}} u', '`u`.`external_userid` = `f`.`external_userid` and f.user_id=u.user_id')
				->where(['f.corp_id' => $corpId, 'f.type' => 1, "c.uid" => $uid]);
			if (!empty($user_ids)) {
				$followStatistic3 = $followStatistic3->andWhere(['f.user_id' => $user_ids]);
			}
			$followStatistic3 = $followStatistic3->andFilterWhere(['between', 'f.data_time', $s_date, $e_date])->andFilterWhere(['between', 'c.time', strtotime($s_date), strtotime($e_date . ' 23:59:59')])->select('c.id cid,u.id uid,u.update_time,c.time,f.user_id,f.external_userid,f.follow_id,c.follow_id fid');
			Yii::error($followStatistic3->createCommand()->getRawSql(), "sym-run-1");
			$followStatistic3 = $followStatistic3->asArray()->all();
			$EndTime          = microtime(true);
			Yii::error($EndTime - $StartTime, "sym-run-1");
			if (!empty($followStatistic3)) {
				foreach ($followStatistic3 as $k => $sta) {
					if ($sta['follow_id'] > $followId && !empty($sta['cid']) && $followId == $follow_id) {
						$notFollow[$i]['user_id']             = $sta['user_id'];
						$notFollow[$i]['external_userid']     = $sta['external_userid'];
						$notFollowTime[$i]['user_id']         = $sta['user_id'];
						$notFollowTime[$i]['external_userid'] = $sta['external_userid'];
						$notFollowTime[$i]['time']            = $sta['time'];
						$i++;
					}
					if ($followId != $follow_id && $sta['fid'] > $follow_id && $sta['fid'] > $followId) {
						/**sym重写*/
						$followStatus[$sta['user_id'] . "-" . $sta['external_userid']][] = $sta['user_id'];
						$followStatus[$sta['user_id'] . "-" . $sta['external_userid']][] = $sta['external_userid'];
						$i++;
					}
				}
			}

			if (!empty($notFollow)) {
				//去重
				$resData = SUtils::array_unique_two($notFollow);
				if (!empty($resData)) {
					foreach ($resData as $data) {
						/**sym重写*/
						$userDay[$data['external_userid'] . '-' . $data['user_id']] = $data['external_userid'] . '-' . $data['user_id'];
					}
				}
				$newTime = [];
				if (!empty($notFollowTime) && !empty($userDay) && $from == 0) {
					foreach ($notFollowTime as $time) {
						/**sym重写*/
						$str = $time['user_id'] . "-" . $time['external_userid'];
						if (isset($userDay[$str])) {
							$newTime[$str][] = $time['time'];
						}
					}
				}
				if (!empty($newTime) && !empty($userDay) && $from == 0) {
					foreach ($newTime as $key => $time) {
						/**sym重写*/
						if (isset($userDay[$key])) {
							$stayDay[$key] = max($time);
						}
					}
				}
				$nextNum = count($resData);
			}
			$data3    = [];
			$dataTime = [];
			$i        = 0;
			$EndTime  = microtime(true);
			Yii::error($EndTime - $StartTime, "sym-run-2");
			if (!empty($followStatistic3) && !empty($followStatus)) {
				foreach ($followStatistic3 as $sta) {
					/**sym重写*/
					$str = $sta['user_id'] . "-" . $sta['external_userid'];
					if (isset($followStatus[$str]) && $sta['fid'] == $follow_id) {
						$data3[$i]['user_id'] = $sta['user_id'];;
						$data3[$i]['external_userid']    = $sta['external_userid'];
						$dataTime[$i]['user_id']         = $sta['user_id'];
						$dataTime[$i]['external_userid'] = $sta['external_userid'];
						$dataTime[$i]['time']            = $sta['time'];
						$i++;
					}
				}
			}
			$EndTime = microtime(true);
			Yii::error($EndTime - $StartTime, "sym-run-3");
			if (!empty($data3)) {
				$resData = SUtils::array_unique_two($data3);
				if (!empty($resData)) {
					foreach ($resData as $data) {
						/**sym重写*/
						$userDay[$data['external_userid'] . '-' . $data['user_id']] = $data['external_userid'] . '-' . $data['user_id'];
					}
				}
				$newTime = [];
				if ($from == 0) {
					if (!empty($dataTime) && !empty($userDay)) {
						foreach ($dataTime as $time) {
							/**sym重写*/
							$str = $time['user_id'] . "-" . $time['external_userid'];
							if (isset($userDay[$str])) {
								$newTime[$str][] = $time['time'];
							}
						}
					}
					if (!empty($newTime) && !empty($userDay)) {
						foreach ($newTime as $key => $time) {
							/**sym重写*/
							if (isset($userDay[$key])) {
								$stayDay[$key] = max($time);
							}
						}
					}
				}
				$nextNum = count($resData);
			}
			$EndTime = microtime(true);
			Yii::error($EndTime - $StartTime, "sym-run-4");
			if ($from == 0) {
				$data1     = $stayDay;
				$data2     = $userDay;
				$resultDay = static::returnDay($data1, $data2, $follow_id, $followId, strtotime($e_date . ' 23:59:59'), $follUser, $record);
				$longDay   = $resultDay['longDay'];
				$totalDay  = $resultDay['totalDay'];
			} else {
				$longDay  = 0;
				$totalDay = 0;
			}
			$newData = [];
			$i       = 0;
			if (!empty($userDay) && !empty($stayDay) && $from == 0) {
				/**sym重写*/
				foreach ($stayDay as $kk => $day) {
					if (isset($userDay[$day])) {
						$newData[$i]['user'] = $userDay[$day];
						$newData[$i]['time'] = $day;
						$i++;
					}
				}
			}
			$EndTime = microtime(true);
			Yii::error($EndTime - $StartTime, "sym-run-5");

			return [
				'longDay'  => $longDay,
				'totalDay' => $totalDay,
				'num'      => $num,
				'nextNum'  => $nextNum,
				'stayDay'  => $stayDay,
				'userDay'  => $userDay,
				'newData'  => $newData,
			];

		}

		/**
		 * @param $data1
		 * @param $data2
		 * @param $follow_id
		 * @param $followId
		 * @param $time1
		 * @param $follUser
		 * @param $recordNew
		 *
		 * @return array
		 *
		 */
		public static function returnDay ($data1, $data2, $follow_id, $followId, $time1, $follUser, $recordNew)
		{
			$day       = [];
			$totalTime = 0;
			$newData1  = [];
			foreach ($follUser as $vv) {
				$newData1[$vv["external_userid"] . '-' . $vv['user_id']][] = $vv;
			}
			//临时变量修改初始值
			$data1      = array_flip($data1);
			$data2      = array_flip($data2);
			$recordNewT = [];
			foreach ($recordNew as $vv) {
				if ($vv['follow_id'] > $follow_id) {
					$str              = $vv['external_id'] . "-" . $vv['user_id'];
					$recordNewT[$str] = $vv['time'];
				}
			}
			if (!empty($data1) && !empty($data2)) {
				foreach ($data1 as $k1 => $value1) {
					/** sym 修改初始方法*/
					if (isset($data2[$k1])) {
						if ($followId == $follow_id && !empty($follUser)) {
							if (isset($newData1[$k1])) {
								foreach ($newData1[$k1] as $vv) {
									if ($vv['createtime'] == $k1) {
										$time = $time1 - $k1;
									} else {
										$time = $value1 - $vv['createtime'];
									}
									$day[]     = $time;
									$totalTime += $time;
								}
							}
						}
					} else {
						if (!empty($recordNew)) {
							$timeAll = [];
							if (isset($recordNewT[$k1])) {
								$timeAll[] = $recordNewT[$k1];
							}
							if (!empty($timeAll)) {
								$newTime   = min($timeAll);
								$time      = $newTime - $k1;
								$day[]     = $time;
								$totalTime += $time;
							}
						}
					}
				}
			}
			$max = 0;
			if (!empty($day)) {
				$max = max($day);
			}
			$longDay  = round($max / (24 * 3600));
			$totalDay = round($totalTime / (24 * 3600));

			return [
				'longDay'  => $longDay,
				'totalDay' => $totalDay,
			];
		}

		/**
		 * @param     $corpId
		 * @param     $user_ids
		 * @param     $s_date
		 * @param     $e_date
		 * @param int $type
		 *
		 * @return array
		 *
		 */
		public static function getReport ($corpId, $user_ids, $s_date, $e_date, $type = 0)
		{
			$userCount = WorkExternalContactFollowRecord::find()->alias('r')->leftJoin('{{%work_external_contact}} c', '`r`.`external_id` = `c`.`id`')->where(['c.corp_id' => $corpId]);
			$record    = WorkExternalContactFollowRecord::find()->alias('r')->leftJoin('{{%work_external_contact}} c', '`r`.`external_id` = `c`.`id`')->where(['c.corp_id' => $corpId]);
			if (!empty($user_ids)) {
				$userCount = $userCount->andWhere(['r.user_id' => $user_ids]);
				$record    = $record->andWhere(['r.user_id' => $user_ids]);
			}
			if ($type == 1) {
				$date      = date('Y-m-d');
				$e_date    = $date . ' 23:59:59';
				$userCount = $userCount->andFilterWhere(['between', 'r.time', strtotime($date), strtotime($e_date)]);
				$record    = $record->andFilterWhere(['between', 'r.time', strtotime($date), strtotime($e_date)]);
			} else {
				if (!empty($s_date) && !empty($e_date)) {
					$e_date    = $e_date . ' 23:59:59';
					$userCount = $userCount->andFilterWhere(['between', 'r.time', strtotime($s_date), strtotime($e_date)]);
					$record    = $record->andFilterWhere(['between', 'r.time', strtotime($s_date), strtotime($e_date)]);
				}
			}

			$userCount   = $userCount->groupBy('r.external_id');
			$userCount   = $userCount->count();
			$recordCount = $record->count();

			return [
				'userCount'   => $userCount,
				'recordCount' => $recordCount,
			];
		}

	}
