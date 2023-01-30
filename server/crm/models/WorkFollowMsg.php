<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\WorkFollowMsgSendingJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_follow_msg}}".
	 *
	 * @property int                    $id
	 * @property int                    $uid          uid
	 * @property int                    $corp_id      企业微信id
	 * @property int                    $agentid      应用id
	 * @property int                    $user_id      成员ID
	 * @property int                    $is_all       是否接收全员数据1是0否
	 * @property string                 $follow_party 接收部门
	 * @property string                 $follow_user  接收成员
	 * @property string                 $send_time    发送时间json
	 * @property string                 $send_content 发送内容
	 * @property int                    $status       是否有效1是0否
	 * @property int                    $create_time  创建时间
	 * @property int                    $upt_time     更新时间
	 *
	 * @property WorkFollowMsgSending[] $workFollowMsgSendings
	 */
	class WorkFollowMsg extends \yii\db\ActiveRecord
	{
		const H5_URL = "/h5/pages/scrm/customer";
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_follow_msg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid'], 'required'],
				[['uid', 'corp_id', 'agentid', 'user_id', 'is_all', 'status', 'create_time', 'upt_time'], 'integer'],
				[['send_time', 'send_content'], 'string'],
				[['follow_party'], 'string'],
				[['follow_user'], 'string'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'uid'          => Yii::t('app', 'uid'),
				'corp_id'      => Yii::t('app', '企业微信id'),
				'agentid'      => Yii::t('app', '应用id'),
				'user_id'      => Yii::t('app', '成员ID'),
				'is_all'       => Yii::t('app', '是否接收全员数据1是0否'),
				'follow_party' => Yii::t('app', '接收部门'),
				'follow_user'  => Yii::t('app', '接收成员'),
				'send_time'    => Yii::t('app', '发送时间json'),
				'send_content' => Yii::t('app', '发送内容'),
				'status'       => Yii::t('app', '是否有效1是0否'),
				'create_time'  => Yii::t('app', '创建时间'),
				'upt_time'     => Yii::t('app', '更新时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkFollowMsgSendings ()
		{
			return $this->hasMany(WorkFollowMsgSending::className(), ['msg_id' => 'id']);
		}

		/**
		 * @param $data
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException]
		 */
		public static function setFollowUser ($data)
		{
			if (!empty($data['follow_id'])) {
				$follow = static::findOne($data['follow_id']);
				if (empty($follow)) {
					throw new InvalidDataException('跟进提醒数据错误');
				}
				if (!empty($data['is_all'])) {
					$follow->is_all       = 1;
					$follow->follow_party = '';
					$follow->follow_user  = '';
				} else {
					$follow->is_all       = 0;
					$follow->follow_party = !empty($data['follow_party']) ? json_encode($data['follow_party']) : '';
					$follow->follow_user  = !empty($data['follow_user']) ? json_encode($data['follow_user']) : '';
				}
				$follow->send_time    = json_encode($data['send_time']);
				$follow->send_content = json_encode($data['send_content']);
				$follow->send_content = str_replace('"textContent":"\n"', '"textContent":""', $follow->send_content);
				$follow->status       = $data['status'];
				$follow->upt_time     = time();

				if (!$follow->validate() || !$follow->save()) {
					throw new InvalidDataException(SUtils::modelError($follow));
				}

				static::setFollowUserMsg($follow);

			} elseif (!empty($data['user_ids'])) {
				foreach ($data['user_ids'] as $user_id) {
					if(is_array($user_id)){
						$user_id = $user_id["id"];
					}
					$follow = static::findOne(['corp_id' => $data['corp_id'], 'agentid' => $data['agentid'], 'user_id' => $user_id]);

					if (empty($follow)) {
						$follow              = new WorkFollowMsg();
						$follow->uid         = $data['uid'];
						$follow->corp_id     = $data['corp_id'];
						$follow->agentid     = $data['agentid'];
						$follow->user_id     = $user_id;
						$follow->create_time = time();
					} else {
						$follow->upt_time = time();
					}

					if (!empty($data['is_all'])) {
						$follow->is_all       = 1;
						$follow->follow_party = '';
						$follow->follow_user  = '';
					} else {
						$follow->is_all       = 0;
						$follow->follow_party = !empty($data['follow_party']) ? json_encode($data['follow_party']) : '';
						$follow->follow_user  = !empty($data['follow_user']) ? json_encode($data['follow_user']) : '';
					}
					$follow->send_time    = json_encode($data['send_time']);
					$follow->send_content = json_encode($data['send_content']);
					$follow->send_content = str_replace('"textContent":"\n"', '"textContent":""', $follow->send_content);
					$follow->status       = $data['status'];

					if (!$follow->validate() || !$follow->save()) {
						throw new InvalidDataException(SUtils::modelError($follow));
					}

					static::setFollowUserMsg($follow);
				}
			}
//			throw new InvalidDataException(11111);

			return true;
		}

		/**
		 * 设置跟进提醒添加到队列
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function setFollowUserMsg ($follow)
		{
			$nowTime  = time();
			$dateTime = date('Ymd');
			$hour     = date('H:i');

			$send_time = !empty($follow->send_time) ? json_decode($follow->send_time, true) : [];

			foreach ($send_time as $time) {
				if ($time > $hour) {
					$sending = WorkFollowMsgSending::findOne(['msg_id' => $follow->id, 'date_time' => $dateTime, 'send_time' => $time]);
					if (empty($sending)) {
						$sending              = new WorkFollowMsgSending();
						$sending->create_time = DateUtil::getCurrentTime();
						$sending->corp_id     = $follow->corp_id;
						$sending->agentid     = $follow->agentid;
						$sending->msg_id      = $follow->id;
						$sending->date_time   = $dateTime;
						$sending->send_time   = $time;
						$sending->push_type   = 1;

						if ($sending->save()) {
							//指定时间发送
							$sendTime          = strtotime(date('Y-m-d') . ' ' . $time);
							$second            = $sendTime - $nowTime;
							$jobId             = \Yii::$app->work->delay($second)->push(new WorkFollowMsgSendingJob([
								'work_follow_msg_sending_id' => $sending->id
							]));
							$sending->queue_id = $jobId;
							$sending->save();
						}
					}
				}
			}

			return true;
		}

		/**
		 * 每日跟进提醒
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getFollowUser ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			//不再应用可见范围 followMsg置为关闭
			$nowTime    = time();
			$dateTime   = date('Ymd');
			$hour       = date('H:i');
			$followUser = static::find()->andWhere(['status' => 1])->asArray()->all();

			foreach ($followUser as $follow) {
				$send_time = !empty($follow['send_time']) ? json_decode($follow['send_time'], true) : [];

				foreach ($send_time as $time) {
					$sending              = new WorkFollowMsgSending();
					$sending->create_time = DateUtil::getCurrentTime();
					$sending->corp_id     = $follow['corp_id'];
					$sending->agentid     = $follow['agentid'];
					$sending->msg_id      = $follow['id'];
					$sending->date_time   = $dateTime;
					$sending->send_time   = $time;
					$sending->push_type   = $time <= $hour ? 0 : 1;

					if ($sending->save()) {
						if ($sending->push_type == 0) {
							$jobId             = \Yii::$app->work->push(new WorkFollowMsgSendingJob([
								'work_follow_msg_sending_id' => $sending->id
							]));
							$status            = 3;
							$sending->status   = $status;
							$sending->queue_id = $jobId;
							$sending->save();
						}
						if ($sending->push_type == 1) {
							//指定时间发送
							$sendTime          = strtotime(date('Y-m-d') . ' ' . $time);
							$second            = $sendTime - $nowTime;
							$jobId             = \Yii::$app->work->delay($second)->push(new WorkFollowMsgSendingJob([
								'work_follow_msg_sending_id' => $sending->id
							]));
							$sending->queue_id = $jobId;
							$sending->save();
						}
					}
				}
			}

			return true;
		}

		/**
		 * 跟进提醒文本内容
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function sendData ($followUser, $sendTime)
		{
			//变量字段
			$variate     = [
				'username', 'sendTime', 'followUser', 'newMemberNum', 'follow_id', 'followMemberNum', 'followNum', 'changeFollowNum', 'notChangeNum', 'notFollowDay_1', 'notFollowDay_3'
			];
			$variateName = [
				'员工姓名', '发送时间点', '可见员工', '新增客户数', '跟进状态名称', '已跟进人数', '已跟进条数', '当前状态变化人数', '当前阶段状态未改人数', '超过1天未跟进人数', '超过3天未跟进人数'
			];

			//自定义未跟进天数
			$notFollowDay = WorkNotFollowDay::find()->where(['uid' => $followUser->uid])->orderBy(['day' => SORT_ASC])->asArray()->all();

			//员工信息
			$workUser = WorkUser::findOne($followUser->user_id);

			//时间
			$stime = strtotime(date('Y-m-d'));
			$etime = strtotime(date('Y-m-d') . ' ' . $sendTime);

			//跟进员工
			if ($followUser->is_all == 1) {
				$followUserIds = [];
				$followName    = '全员';
			} elseif (!empty($followUser->follow_party) || !empty($followUser->follow_user)) {
				$followName    = '';
				$departmentIds = !empty($followUser->follow_party) ? json_decode($followUser->follow_party, true) : [];

				if (!empty($departmentIds)) {
					$department     = WorkDepartment::find()->andWhere(['in', 'department_id', $departmentIds])->andWhere(['corp_id' => $followUser->corp_id])->asArray()->all();
					$TempDepartment = array_column($department, "name");
					$followName     .= implode("/", $TempDepartment)."/";
				}
				$userKeyArr = json_decode($followUser->follow_user, true);
				$userIds = [];
				if (!empty($userKeyArr)) {
					$userKeyArr     = array_column($userKeyArr, "id");
					$followWorkUser = WorkUser::find()->where(["in", "id", $userKeyArr])->select("userid,name")->asArray()->all();
					$TempUser       = array_column($followWorkUser, "name", "userid");
					$userIds        = array_keys($TempUser);
					$TempName       = array_values($TempUser);
					$followName     .= implode("/", $TempName)."/";
				}
				$followUserIds = WorkDepartment::getDepartmentUser($followUser->corp_id, $departmentIds, $userIds);
				$followName = rtrim($followName, '/');
				if (count($followUserIds) == 1 && in_array($followUser->user_id, $followUserIds)) {
					$followName = '/自己';
				} elseif (!in_array($followUser->user_id, $followUserIds)) {
					array_push($followUserIds, $followUser->user_id);
					$followName .= '/自己';
				}
			} else {
				$followUserIds[] = $followUser->user_id;
				$followName      = '自己';
			}

			//新增客户数(归属多个员工算多条)
			$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
			$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
			$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
			$workExternalUserData = $workExternalUserData->andFilterWhere(['between', 'wf.createtime', $stime, $etime]);
			if (!empty($followUserIds)) {
				$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
			}
			$newMemberNum = $workExternalUserData->groupBy('wf.id')->count();

			//跟进状态
			$followData = [];
			$follow     = Follow::find()->andWhere(['uid' => $followUser->uid])->select('id,title,status')->asArray()->all();
			foreach ($follow as $f) {
				$followData[$f['id']] = $f['status'] == 1 ? $f['title'] : $f['title'] . '【已删除】';
			}
			//发送内容增加新增状态
			$contentStr  = '';
			$sendContent = !empty($followUser->send_content) ? json_decode($followUser->send_content, true) : [];
			$send_key    = array_column($sendContent,"id");
			foreach ($follow as $f) {
				if (!in_array($f['id'], $send_key) && $f['status'] == 1) {
					$sendData                = [];
					$sendData['id']          = $f['id'];
					$sendData['textContent'] = "{follow_id}阶段，有{notChangeNum}人停留，请尽快沟通落实。\n";
					array_push($sendContent, $sendData);
				}
			}

			foreach ($sendContent as $val) {
				if (!empty($val['textContent'])) {
					if ($val['id'] > 0 && !isset($followData[$val['id']])) {
						//跟进状态已删除  删除也统计20200805
						continue;
					}
					$content = $val['textContent'];
					//员工姓名
					if (strpos($content, '{username}') !== false) {
						$content = str_replace('{username}', $workUser->name, $content);
					}
					//发送时间点
					if (strpos($content, '{sendTime}') !== false) {
						$content = str_replace('{sendTime}', $sendTime, $content);
					}
					//可见员工
					if (strpos($content, '{followUser}') !== false) {
						$content = str_replace('{followUser}', $followName, $content);
					}
					//新增客户数
					if (strpos($content, '{newMemberNum}') !== false) {
						$content = str_replace('{newMemberNum}', $newMemberNum, $content);
					}
					//跟进状态名称
					if (strpos($content, '{follow_id}') !== false) {
						$followTitle = isset($followData[$val['id']]) ? $followData[$val['id']] : '';
						$content     = str_replace('{follow_id}', $followTitle, $content);
					}
					//已跟进人数
					if (strpos($content, '{followMemberNum}') !== false) {
						$followRecord = WorkExternalContactFollowRecord::find()->andWhere(['uid' => $followUser->uid]);
						$followRecord = $followRecord->andFilterWhere(['between', 'time', $stime, $etime]);
						if (!empty($followUserIds)) {
							$followRecord = $followRecord->andWhere(['in', 'user_id', $followUserIds]);
						}
						if ($val['id'] > 0) {
							$followRecord = $followRecord->andWhere(['follow_id' => $val['id']]);
						}
						$followMemberNum = $followRecord->groupBy('external_id')->count();

						$content = str_replace('{followMemberNum}', $followMemberNum, $content);
					}
					//已跟进条数
					if (strpos($content, '{followNum}') !== false) {
						$followRecord = WorkExternalContactFollowRecord::find()->andWhere(['uid' => $followUser->uid]);
						$followRecord = $followRecord->andFilterWhere(['between', 'time', $stime, $etime]);
						if (!empty($followUserIds)) {
							$followRecord = $followRecord->andWhere(['in', 'user_id', $followUserIds]);
						}
						if ($val['id'] > 0) {
							$followRecord = $followRecord->andWhere(['follow_id' => $val['id']]);
						}
						$followNum = $followRecord->count();

						$content = str_replace('{followNum}', $followNum, $content);
					}
					//当前状态变化人数
					if (strpos($content, '{changeFollowNum}') !== false) {
						/*$followRecord = WorkExternalContactFollowRecord::find()->andWhere(['uid' => $followUser->uid]);
						$followRecord = $followRecord->andFilterWhere(['between', 'time', $stime, $etime]);
						$followRecord = $followRecord->andWhere(['follow_id' => $val['id']]);
						if (!empty($followUserIds)) {
							$followRecord = $followRecord->andWhere(['in', 'user_id', $followUserIds]);
						}
						$followInfo = $followRecord->select('external_id')->groupBy('external_id')->asArray()->all();

						$followD = [];
						foreach ($followInfo as $f) {
							array_push($followD, $f['external_id']);
						}

						$changeFollowNum = 0;
						if (!empty($followD)) {
							$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
							$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
							$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => 0]);
							$workExternalUserData = $workExternalUserData->andWhere(['!=', 'wf.follow_id', $val['id']]);
							$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.external_userid', $followD]);
							if (!empty($followUserIds)) {
								$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
							}

							$changeFollowNum = $workExternalUserData->groupBy('wf.id')->count();
						}*/

						//客户现状态
						$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
						$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
						$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
						if (!empty($followUserIds)) {
							$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
						}
						$workExternalUserData = $workExternalUserData->andWhere(['>', 'wf.follow_id', $follow[0]['id']]);
						$workExternalUserData = $workExternalUserData->andWhere(['>=', 'wf.update_time', $stime]);
						$nowFollowData        = $workExternalUserData->select('wf.external_userid external_id,wf.user_id,wf.follow_id')->groupBy('wf.id')->asArray()->all();
						//客户原状态
						$followUserIdSql = '';
						if (!empty($followUserIds)) {
							$followUserIdSql = ' and user_id in (' . implode(',', $followUserIds) . ')';
						}
						$followInfo = WorkExternalContactFollowRecord::find()->where(' id in (select max(id) from {{%work_external_contact_follow_record}} where uid=' . $followUser->uid . $followUserIdSql . ' and time<' . $stime . ' group by external_id,user_id)');
						$followInfo = $followInfo->select('external_id,follow_id,user_id')->asArray()->all();
						$oldFollowD = [];
						foreach ($followInfo as $f) {
							$oldFollowD[$f['external_id'] . '_' . $f['user_id']] = $f['follow_id'];
						}
						$changeFollowNum = 0;
						foreach ($nowFollowData as $n) {
							$key = $n['external_id'] . '_' . $n['user_id'];
							if (isset($oldFollowD[$key]) && $oldFollowD[$key] == $val['id'] && $oldFollowD[$key] != $n['follow_id']) {
								$changeFollowNum++;
							} elseif ($val['id'] == $follow[0]['id'] && (!isset($oldFollowD[$key]) || $oldFollowD[$key] == $val['id'])) {
								$changeFollowNum++;
							}
						}

						$content = str_replace('{changeFollowNum}', $changeFollowNum, $content);
					}
					//当前阶段状态未改人数
					if (strpos($content, '{notChangeNum}') !== false) {
						//客户现状态
						$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
						$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
						$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
						if (!empty($followUserIds)) {
							$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
						}
						if ($val['id'] > 0) {
							$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_id' => $val['id']]);
						}
						$nowFollowData = $workExternalUserData->select('wf.external_userid external_id,wf.user_id,wf.follow_id')->groupBy('wf.id')->asArray()->all();
						$notChangeNum  = count($nowFollowData);

						//客户原状态
						/*$followUserIdSql = '';
						if (!empty($followUserIds)) {
							$followUserIdSql = ' and user_id in (' . implode(',', $followUserIds) . ')';
						}
						$followInfo = WorkExternalContactFollowRecord::find()->where(' id in (select max(id) from {{%work_external_contact_follow_record}} where uid=' . $followUser->uid . $followUserIdSql . ' and time<' . $stime . ' group by external_id,user_id)');
						$followInfo = $followInfo->select('external_id,follow_id,user_id')->asArray()->all();
						$oldFollowD = [];
						foreach ($followInfo as $f){
							$oldFollowD[$f['external_id'] . '_' . $f['user_id']] = $f['follow_id'];
						}

						foreach ($nowFollowData as $n){
							if (isset($oldFollowD[$n['external_id'] . '_' . $n['user_id']]) && $oldFollowD[$n['external_id'] . '_' . $n['user_id']] != $n['follow_id']){
								$notChangeNum--;
							}
						}*/

						$content = str_replace('{notChangeNum}', $notChangeNum, $content);
					}
					//超过1天数未跟进人数
					if (strpos($content, '{notFollowDay_1}') !== false) {
						$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
						$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
						$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
						$workExternalUserData = $workExternalUserData->andWhere(['<', 'wf.update_time', $etime - 86400]);
						if (!empty($followUserIds)) {
							$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
						}
						if ($val['id'] > 0) {
							$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_id' => $val['id']]);
						} else {
							$workExternalUserData = $workExternalUserData->andWhere(['>', 'wf.follow_id', 0]);
						}
						$notFollowDay_1 = $workExternalUserData->groupBy('wf.id')->count();

						$content = str_replace('{notFollowDay_1}', $notFollowDay_1, $content);
					}
					//超过3天数未跟进人数
					if (strpos($content, '{notFollowDay_3}') !== false) {
						$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
						$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
						$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
						$workExternalUserData = $workExternalUserData->andWhere(['<', 'wf.update_time', $etime - 3 * 86400]);
						if (!empty($followUserIds)) {
							$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
						}
						if ($val['id'] > 0) {
							$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_id' => $val['id']]);
						} else {
							$workExternalUserData = $workExternalUserData->andWhere(['>', 'wf.follow_id', 0]);
						}
						$notFollowDay_3 = $workExternalUserData->groupBy('wf.id')->count();

						$content = str_replace('{notFollowDay_3}', $notFollowDay_3, $content);
					}
					//自定义未跟进天数
					if (!empty($notFollowDay)) {
						foreach ($notFollowDay as $day) {
							$dayStr = '{notFollowDay_' . $day['day'] . '}';
							if (strpos($content, $dayStr) !== false) {
								$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
								$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
								$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $followUser->corp_id, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
								$workExternalUserData = $workExternalUserData->andWhere(['<', 'wf.update_time', $etime - $day['day'] * 86400]);
								if (!empty($followUserIds)) {
									$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $followUserIds]);
								}
								if ($val['id'] > 0) {
									$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_id' => $val['id']]);
								} else {
									$workExternalUserData = $workExternalUserData->andWhere(['>', 'wf.follow_id', 0]);
								}
								$notFollowDayNum = $workExternalUserData->groupBy('wf.id')->count();

								$content = str_replace($dayStr, $notFollowDayNum, $content);
							}
						}
					}
					$contentStr .= $content . "\n";
				}
			}

			return $contentStr;
		}

	}
