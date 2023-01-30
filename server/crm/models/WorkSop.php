<?php

namespace app\models;

use app\components\InvalidDataException;
use app\queue\WorkSopMsgSendingJob;
use app\util\DateUtil;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_sop}}".
 *
 * @property int $id
 * @property int $uid 账户ID
 * @property int $sub_id 子账户id
 * @property int $corp_id 授权的企业ID
 * @property int $create_user_id 创建者员工ID
 * @property int $is_chat 是否群SOP规则1是0否
 * @property int $type 1新客培育、2客户跟进
 * @property string $title 规则名称
 * @property string $user_ids 规则成员
 * @property string $chat_ids 规则群id
 * @property int $follow_id 跟进状态id(type=2)
 * @property int $is_all 是否全部客户1是0否
 * @property int $task_id 任务标签id(type=2)
 * @property int $no_send_type 不推送时间段1开启0关闭
 * @property string $no_send_time 不推送时间段
 * @property int $create_time 创建时间
 * @property int $status 是否开启1是0否
 * @property int $is_del 是否删除1是0否
 *
 * @property WorkCorp $corp
 * @property User $u
 */
class WorkSop extends \yii\db\ActiveRecord
{
	const H5_URL = "/h5/pages/scrm/sop";

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_sop}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'sub_id', 'corp_id', 'create_user_id', 'is_chat', 'type', 'follow_id', 'is_all', 'task_id', 'no_send_type', 'create_time', 'status', 'is_del'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['user_ids', 'chat_ids'], 'string', 'max' => 5000],
            [['no_send_time'], 'string', 'max' => 32],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'             => Yii::t('app', 'ID'),
			'uid'            => Yii::t('app', '账户ID'),
			'sub_id'         => Yii::t('app', '子账户id'),
			'corp_id'        => Yii::t('app', '授权的企业ID'),
			'create_user_id' => Yii::t('app', '创建者员工id'),
			'is_chat'        => Yii::t('app', '是否群SOP规则1是0否'),
			'type'           => Yii::t('app', '1新客培育、2客户跟进'),
			'title'          => Yii::t('app', '规则名称'),
			'user_ids'       => Yii::t('app', '规则成员'),
			'chat_ids'       => Yii::t('app', '规则群id'),
			'follow_id'      => Yii::t('app', '跟进状态id(type=2)'),
			'is_all'         => Yii::t('app', '是否全部客户1是0否'),
			'task_id'        => Yii::t('app', '任务标签id(type=2)'),
			'no_send_type'   => Yii::t('app', '不推送时间段1开启0关闭'),
			'no_send_time'   => Yii::t('app', '不推送时间段'),
			'create_time'    => Yii::t('app', '创建时间'),
			'status'         => Yii::t('app', '是否开启1是0否'),
			'is_del'         => Yii::t('app', '是否删除1是0否'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(User::className(), ['uid' => 'uid']);
    }

	/**
	 * @param $data
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function setSop ($data)
	{
		$transaction = \Yii::$app->mdb->beginTransaction();

		try {
			$corp_id = $data['corp_id'];
			$sop_id  = $data['sop_id'];
			$title   = $data['title'];
			$is_chat = $data['is_chat'];
			$type    = $is_chat == 1 ? 0 : $data['type'];

			$hasSop = static::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0, 'is_chat' => $is_chat, 'title' => $title]);
			if ($is_chat == 0){
				$hasSop = $hasSop->andWhere(['type' => $type]);
			}
			if ($sop_id) {
				$hasSop = $hasSop->andWhere(['!=', 'id', $sop_id]);
			}
			$hasSop = $hasSop->one();

			if (!empty($hasSop)) {
				throw new InvalidDataException('规则名称已存在，请更换');
			}
			//群SOP群唯一验证
			if ($is_chat == 1){
				foreach ($data['chat_ids'] as $chat_id){
					$hasSopChat = static::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0, 'is_chat' => $is_chat])->andWhere("find_in_set ($chat_id,chat_ids) ");
					if ($sop_id){
						$hasSopChat = $hasSopChat->andWhere(['!=', 'id', $sop_id]);
					}
					$hasSopChat = $hasSopChat->one();

					if (!empty($hasSopChat)) {
						$chatName = WorkChat::getChatName($chat_id);
						$chatName = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;
						throw new InvalidDataException('客户群【' . $chatName . '】在群SOP【' . $hasSopChat->title . '】已存在，不能重复设置');
					}
				}
			}

			if ($sop_id) {
				$sop = static::findOne($sop_id);
				if (empty($sop)) {
					throw new InvalidDataException('SOP规则数据错误');
				}
			} else {
				$sop                 = new WorkSop();
				$sop->uid            = $data['uid'];
				$sop->sub_id         = $data['sub_id'];
				$sop->create_user_id = $data['create_user_id'];
				$sop->corp_id        = $corp_id;
				$sop->is_chat        = $is_chat;
				$sop->type           = $type;
				$sop->create_time    = time();
			}
			$sop->title    = $title;
			$sop->user_ids = implode(',', $data['user_ids']);
			if ($is_chat == 1){
				$sop->chat_ids = implode(',', $data['chat_ids']);
			}
			if ($is_chat == 0 && $data['type'] == 2) {
				$sop->follow_id = $data['follow_id'];
				$sop->is_all    = $data['is_all'];
				$sop->task_id   = $data['task_id'];
			}
			$sop->no_send_type = $data['no_send_type'];
			$no_send_time      = '';
			if ($data['no_send_type'] == 1) {
				$no_send_time_data   = [];
				$no_send_time_data[] = $data['no_send_stime'];
				$no_send_time_data[] = $data['no_send_etime'];
				$no_send_time        = json_encode($no_send_time_data);
			}
			$sop->no_send_time = $no_send_time;

			if (!$sop->validate() || !$sop->save()) {
				throw new InvalidDataException(SUtils::modelError($sop));
			}

			$timeData = $data['timeData'];

			if ($sop_id) {
				WorkSopTime::updateAll(['is_del' => 1], ['sop_id' => $sop_id, 'is_del' => 0]);
			}

			foreach ($timeData as $time) {
				$sopTime                = [];
				$sopTime['corp_id']     = $corp_id;
				$sopTime['sop_id']      = $sop->id;
				$sopTime['time_type']   = $time['time_type'];
				$sopTime['time_one']    = $time['time_one'];
				$sopTime['time_two']    = $time['time_two'];
				$sopTime['sop_time_id'] = $sop_id > 0 && isset($time['sop_time_id']) ? $time['sop_time_id'] : 0;
				$sopTime['contentData'] = $time['contentData'];

				$sopTimeId = WorkSopTime::setSopTime($sopTime);

				//客户SOP提醒时间变更
				if ($is_chat == 0 && !empty($sopTime['sop_time_id'])){
					static::updateCustomSopMsg($sopTimeId);
				}

				//历史跟进状态提醒
				if ($type == 2 && empty($sopTime['sop_time_id'])){
					static::sendFollowSopMsg($corp_id, $data['sub_id'], $data['follow_id'], $sopTimeId, $data['user_ids']);
				}

				//群提醒队列
				if ($is_chat == 1){
					static::sendChatSopMsg($corp_id, $sop->id, $sopTimeId, $data['chat_ids']);
				}
			}

			$transaction->commit();

			return $sop->id;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw new InvalidDataException($e->getMessage());
		}
	}

	/**
	 * @param $sop_id
	 * @param $sub_id
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function getSop ($sop_id, $sub_id = 0)
	{
		$workSop = static::findOne($sop_id);
		if (empty($workSop)) {
			throw new InvalidDataException('规则参数错误');
		}

		$subUser = [];
		if ($sub_id) {
			//子账户创建者及可见执行人
			[$subUser, $subDepartment, $all, $subDepartmentOld] = WorkDepartment::GiveSubIdReturnDepartmentOrUser($workSop->corp_id, $sub_id);
		}

		$result                 = [];
		$result['sop_id']       = $workSop->id;
		$result['is_chat']      = $workSop->is_chat;
		$result['type']         = $workSop->type;
		$result['title']        = $workSop->title;
		$result['user_ids']     = $workSop->is_chat == 0 ? explode(',', $workSop->user_ids) : explode(',', $workSop->chat_ids);
		$result['follow_id']    = $workSop->follow_id;
		$result['is_all']       = $workSop->is_all;
		$result['task_id']      = $workSop->task_id;

		$creat_name             = '总经理';
		if ($workSop->sub_id) {
			$subInfo    = SubUserProfile::findOne(['sub_user_id' => $workSop->sub_id]);
			$creat_name = !empty($subInfo->department) ? $subInfo->name . '（' . $subInfo->department . '）' : $subInfo->name;
		}else{
			if ($workSop->create_user_id){
				$workUser = WorkUser::findOne($workSop->create_user_id);
				if (!empty($workUser)){
					$creat_name = $workUser->name;
				}
			}else{
				$userInfo = UserProfile::findOne(['uid' => $workSop->uid]);
				if (!empty($userInfo) && !empty($userInfo->nick_name)){
					$creat_name = !empty($userInfo->department) ? $userInfo->nick_name . '（' . $userInfo->department . '）' : $userInfo->nick_name;
				}
			}
		}
		$result['creat_name'] = $creat_name;

		if ($workSop->is_chat == 0) {
			$user_names = [];
			$Temp       = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($result['user_ids']);
			if ($Temp["user"]) {
				$workUser = WorkUser::find()->where(['id' => $Temp["user"]])->all();
				foreach ($workUser as $user) {
					if ($sub_id && $all == true && !in_array($user->id, $subUser)){
						continue;
					}
					$user_key                = [];
					$user_key['id']          = $user->id;
					$user_key['title']       = $user->name;
					$user_key['scopedSlots'] = ['title' => 'custom'];

					$user_names[] = $user_key;
				}
			}

			if ($sub_id && $all == true && $Temp["department"]){
				//子账户可见员工所属部门及父部门
				$workUserSub = WorkUser::find()->where(['id' => $subUser])->select('department')->all();
				foreach ($workUserSub as $wu){
					if ($wu->department) {
						$department = explode(',', $wu->department);
						foreach ($department as $dep) {
							$parentId = \Yii::$app->db->createCommand("SELECT getParentList(" . $dep . "," . $workSop->corp_id . ") as department;")->queryOne();
							if (!empty($parentId)) {
								$parentId       = explode(",", $parentId["department"]);
								$departmentData = WorkDepartment::find()->where(['department_id' => $parentId, "corp_id" => $workSop->corp_id, "is_del" => 0])->select('department_id,name')->asArray()->all();
								if (!empty($departmentData)) {
									foreach ($departmentData as $vv){
										if (in_array($vv['department_id'], $Temp["department"])){
											$user_key                = [];
											$user_key['id']          = 'd-' . $vv['department_id'];
											$user_key['ids']         = $vv['department_id'];
											$user_key['title']       = $vv['name'];
											$user_key['scopedSlots'] = ['title' => 'title'];

											$user_names[] = $user_key;

											$Temp["department"] = array_diff($Temp["department"], [$vv['department_id']]);
										}
									}
								}
							}
						}
					}
				}
			}
			
			if ($Temp["department"]) {
				$department = WorkDepartment::find()->where(['corp_id' => $workSop->corp_id,'department_id' => $Temp["department"]])->all();
				foreach ($department as $dep) {
					if ($sub_id && $all == true && !in_array($dep->department_id, $subDepartment)){
						continue;
					}
					$user_key                = [];
					$user_key['id']          = 'd-' . $dep->department_id;
					$user_key['ids']         = $dep->department_id;
					$user_key['title']       = $dep->name;
					$user_key['scopedSlots'] = ['title' => 'title'];

					$user_names[] = $user_key;
				}
			}

			$result['user_names'] = $user_names;

			//已设置过SOP规则的员工
			$hasSopUserData = static::find()->where(['corp_id' => $workSop->corp_id, 'is_chat' => 0, 'type' => $workSop->type, 'is_del' => 0])->asArray()->all();
			$hasSopUser     = [];
			foreach ($hasSopUserData as $v) {
				$hasUser    = explode(',', $v['user_ids']);
				$hasSopUser = array_merge($hasSopUser, $hasUser);
			}
			if ($hasSopUser) {
				$hasSopUser = array_unique($hasSopUser);
				$Temp       = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($hasSopUser);
				$hasSopUser = WorkDepartment::GiveDepartmentReturnUserData($workSop->corp_id, $Temp["department"], $Temp["user"], 0, true, 0, [], 0);
				$hasSopUser = empty($hasSopUser) ? [0] : $hasSopUser;
			}
			$result['hasSopUser'] = $hasSopUser;
		} else {
			$chatData = [];
			foreach ($result['user_ids'] as $chat_id) {
				$workChat = WorkChat::findOne($chat_id);
				if ($sub_id && $all == true) {
					if (!in_array($workChat->owner_id, $subUser)) {
						continue;
					}
				}
				$chatD               = [];
				$chatD['name']       = WorkChat::getChatName($chat_id);
				$chatD['member_num'] = WorkChatInfo::find()->andWhere(['chat_id' => $chat_id, 'status' => 1])->count();
				$chatD['avatarData'] = WorkChat::getChatAvatar($chat_id);
				$workUser            = WorkUser::findOne($workChat->owner_id);
				$chatD['ownerName']  = !empty($workUser) ? $workUser->name : '--';

				$chatData[] = $chatD;
			}
			$result['chat_data'] = $chatData;
		}

		$task_name = '';
		if ($workSop->task_id > 0) {
			$taskTag   = WorkTaskTag::findOne($workSop->task_id);
			$task_name = !empty($taskTag) ? $taskTag->tagname : '';
		}
		$result['task_name'] = $task_name;
		$follow_name         = '';
		if ($workSop->follow_id > 0) {
			$follow      = Follow::findOne($workSop->follow_id);
			$follow_name = !empty($follow) ? $follow->title : '';
		}
		$result['follow_name'] = $follow_name;

		$result['no_send_type'] = $workSop->no_send_type;
		if ($result['no_send_type'] == 1) {
			$no_send_time_data       = json_decode($workSop->no_send_time, true);
			$result['no_send_stime'] = $no_send_time_data[0];
			$result['no_send_etime'] = $no_send_time_data[1];
		}

		$result['timeData'] = WorkSopTime::getSopTime($workSop->id, $subUser);

		return $result;
	}

	/**
	 * @param $corpId
	 * @param $type 1新客培训2客户生命周期
	 * @param $user_id
	 * @param $external_id
	 * @param $follow_id
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function sendSopMsg ($corpId, $type, $user_id, $external_id, $follow_id = 0)
	{
		try {
			$workUser  = WorkUser::findOne($user_id);
			$userWhere = "find_in_set ($workUser->id,user_ids) ";
			if ($workUser->department) {
				$department = explode(',', $workUser->department);
				foreach ($department as $dep) {
					$key       = 'd-' . $dep;
					$userWhere .= " or find_in_set ('$key',user_ids)";
				}
			}
			$workSop = WorkSop::find()->where(['corp_id' => $corpId, 'type' => $type, 'status' => 1, 'is_del' => 0])->andWhere($userWhere);
			if ($type == 2) {
				$workSop = $workSop->andwhere(['follow_id' => $follow_id]);
			}
			$workSop = $workSop->asArray()->all();
			$nowTime = time();

			if (!empty($workSop)) {
				foreach ($workSop as $sop) {
					$workSopTime = WorkSopTime::find()->where(['sop_id' => $sop['id'], 'is_del' => 0])->asArray()->all();
					foreach ($workSopTime as $time) {
						$create_time = DateUtil::getCurrentTime();
						$sopMsg      = WorkSopMsgSending::findOne(['sop_id' => $sop['id'], 'sop_time_id' => $time['id'], 'user_id' => $user_id, 'external_id' => $external_id, 'create_time' => $create_time]);
						if (empty($sopMsg)) {
							$sopMsg              = new WorkSopMsgSending();
							$sopMsg->corp_id     = $corpId;
							$sopMsg->sop_id      = $sop['id'];
							$sopMsg->sop_time_id = $time['id'];
							$sopMsg->user_id     = $user_id;
							$sopMsg->external_id = $external_id;
							$sopMsg->create_time = $create_time;
						}

						$time_one = $time['time_one'] ? $time['time_one'] : 0;
						$time_two = $time['time_two'] ? $time['time_two'] : 0;
						if ($time['time_type'] == 1) {
							$send_time = $nowTime + $time_one * 3600 + $time_two * 60;
						} else {
							$send_time = strtotime("+$time_one days");
							if ($time_two) {
								$send_time = strtotime(date('Y-m-d', $send_time) . ' ' . $time_two);
							}else{
								$send_time = strtotime(date('Y-m-d', $send_time));
							}
						}

						$sopContent  = WorkSopTimeContent::find()->where(['sop_time_id' => $time['id'], 'status' => 1])->all();
						$contentData = [];
						foreach ($sopContent as $content) {
							$contentData[] = json_decode($content->content, true);
						}
						$content = json_encode($contentData);

						$sopMsg->send_time = $send_time;
						$sopMsg->content   = $content;

						if (!$sopMsg->save()){
							\Yii::error(SUtils::modelError($sopMsg), 'message_sop');
						}

						/*if ($sopMsg->save()) {
							//指定时间发送
							$second           = $send_time - $nowTime;
							$second           = $second > 0 ? $second : 0;
							$jobId            = \Yii::$app->work->delay($second)->push(new WorkSopMsgSendingJob([
								'work_sop_msg_sending_id' => $sopMsg->id
							]));
							$sopMsg->queue_id = $jobId;
							$sopMsg->save();
						}*/
					}
				}
			}

			return true;
		} catch (\Exception $e) {
			\Yii::error($e->getMessage(), 'message_sop');
		}

		return true;
	}

	/**
	 * @param $corpId
	 * @param $sop_id
	 * @param $sopTimeId
	 * @param $chat_ids
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function sendChatSopMsg ($corpId, $sop_id, $sopTimeId, $chat_ids)
	{
		try {
			$sopTime      = WorkSopTime::findOne($sopTimeId);
			$nowTime      = time();
			$todayEndTime = strtotime(date('Y-m-d')) + 86400;

			if ($sopTime) {
				$time_one = $sopTime->time_one ? $sopTime->time_one : 0;
				$time_two = $sopTime->time_two ? $sopTime->time_two : 0;
				if ($sopTime->time_type == 1) {
					$send_time = $nowTime + $time_one * 3600 + $time_two * 60;
				} else {
					$send_time = strtotime("+$time_one days");
					if ($time_two) {
						$send_time = strtotime(date('Y-m-d', $send_time) . ' ' . $time_two);
					}else{
						$send_time = strtotime(date('Y-m-d', $send_time));
					}
				}

				$second = $send_time - $nowTime;
				$second = $second > 0 ? $second : 0;

				$sopContent  = WorkSopTimeContent::find()->where(['sop_time_id' => $sopTimeId, 'status' => 1])->all();
				$contentData = [];
				foreach ($sopContent as $content) {
					$contentData[] = json_decode($content->content, true);
				}
				$content = json_encode($contentData);

				$sopMsgIds = [];
				foreach ($chat_ids as $chat_id) {
					$is_repeat  = 0;
					$sopChatMsg = WorkSopMsgSending::findOne(['is_chat' => 1, 'sop_id' => $sop_id, 'sop_time_id' => $sopTimeId, 'external_id' => $chat_id, 'is_del' => 0]);

					if ($sopChatMsg) {
						$is_repeat  = 1;
						$createTime = strtotime($sopChatMsg->create_time);
						$oldSecond  = $sopChatMsg->send_time - $createTime;
						//新的发送时间
						if ($sopTime->time_type == 1) {
							$nowSendTime = $createTime + $time_one * 3600 + $time_two * 60;
						} else {
							$nowSendTime = $createTime + $time_one * 86400;
							if ($time_two) {
								$nowSendTime = strtotime(date('Y-m-d', $nowSendTime) . ' ' . $time_two);
							}else{
								$nowSendTime = strtotime(date('Y-m-d', $nowSendTime));
							}
						}
						$nowSendTime = $nowSendTime < $nowTime ? $nowTime : $nowSendTime;

						if ($oldSecond != $second && $sopChatMsg->status != 1) {
							$sopChatMsg->is_del      = 1;
							$sopChatMsg->update_time = DateUtil::getCurrentTime();
							$sopChatMsg->error_msg   = 'SOP规则时间已变更';
							$sopChatMsg->save();
						}
						elseif ($oldSecond != $second && $sopChatMsg->status == 1){
							if ($nowSendTime <= $nowTime){
								continue;
							}
						}
						else {
							continue;
						}
					}
					$workChat = WorkChat::findOne($chat_id);

					$sopChatMsg              = new WorkSopMsgSending();
					$sopChatMsg->corp_id     = $corpId;
					$sopChatMsg->is_chat     = 1;
					$sopChatMsg->sop_id      = $sop_id;
					$sopChatMsg->sop_time_id = $sopTimeId;
					$sopChatMsg->user_id     = $workChat->owner_id;
					$sopChatMsg->external_id = $chat_id;
					$sopChatMsg->send_time   = $is_repeat == 0 ? $send_time : $nowSendTime;
					$sopChatMsg->create_time = $is_repeat == 0 ? date('Y-m-d H:i:s', $nowTime) : date('Y-m-d H:i:s', $createTime);
					$sopChatMsg->content     = $content;

					if ($sopChatMsg->save()) {
						if ($sopChatMsg->send_time < $todayEndTime) {
							$key               = $workChat->owner_id . '_' . $sopChatMsg->send_time;
							$sopMsgIds[$key][] = $sopChatMsg->id;
						}
					}else{
						throw new InvalidDataException(SUtils::modelError($sopChatMsg));
					}
				}

				if ($sopMsgIds) {
					//当日发送
					foreach ($sopMsgIds as $key => $sopMsgId) {
						$keyData     = explode('_', $key);
						$delaySecond = $keyData[1] - $nowTime;
						$delaySecond = $delaySecond > 0 ? $delaySecond : 0;
						$jobId       = \Yii::$app->work->delay($delaySecond)->push(new WorkSopMsgSendingJob([
							'work_sop_msg_sending_id' => $sopMsgId
						]));

						WorkSopMsgSending::updateAll(['queue_id' => $jobId], ['id' => $sopMsgId]);
					}
				}
			}

			return true;
		} catch (\Exception $e) {
			\Yii::error($e->getMessage(), 'message_sop');
		}

		return true;
	}

	/**
	 * 客户SOP提醒时间变更
	 *
	 * @param $sopTimeId
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function updateCustomSopMsg ($sopTimeId)
	{
		try {
			$sopMsgList = WorkSopMsgSending::find()->where(['sop_time_id' => $sopTimeId, 'status' => 0, 'is_del' => 0])->all();
			$sopTime    = WorkSopTime::findOne($sopTimeId);

			if (!empty($sopMsgList) && !empty($sopTime)) {
				$nowTime      = time();
				$todayEndTime = strtotime(date('Y-m-d')) + 86400;

				$time_one = $sopTime->time_one ? $sopTime->time_one : 0;
				$time_two = $sopTime->time_two ? $sopTime->time_two : 0;
				if ($sopTime->time_type == 1) {
					$send_time = $nowTime + $time_one * 3600 + $time_two * 60;
				} else {
					$send_time = strtotime("+$time_one days");
					if ($time_two) {
						$send_time = strtotime(date('Y-m-d', $send_time) . ' ' . $time_two);
					} else {
						$send_time = strtotime(date('Y-m-d', $send_time));
					}
				}

				$second = $send_time - $nowTime;
				$second = $second > 0 ? $second : 0;

				$sopContent  = WorkSopTimeContent::find()->where(['sop_time_id' => $sopTimeId, 'status' => 1])->all();
				$contentData = [];
				foreach ($sopContent as $content) {
					$contentData[] = json_decode($content->content, true);
				}
				$content = json_encode($contentData);

				$sopMsgIds = [];
				foreach ($sopMsgList as $sopMsg) {
					$createTime = strtotime($sopMsg->create_time);
					$oldSecond  = $sopMsg->send_time - $createTime;
					if ($oldSecond != $second && $sopMsg->status != 1) {
						$sopMsg->is_del      = 1;
						$sopMsg->update_time = DateUtil::getCurrentTime();
						$sopMsg->error_msg   = 'SOP规则时间已变更';
						$sopMsg->save();
					} else {
						continue;
					}
					//新的发送时间
					if ($sopTime->time_type == 1) {
						$nowSendTime = $createTime + $time_one * 3600 + $time_two * 60;
					} else {
						$nowSendTime = $createTime + $time_one * 86400;
						if ($time_two) {
							$nowSendTime = strtotime(date('Y-m-d', $nowSendTime) . ' ' . $time_two);
						}else{
							$nowSendTime = strtotime(date('Y-m-d', $nowSendTime));
						}
					}
					$nowSendTime = $nowSendTime < $nowTime ? $nowTime : $nowSendTime;

					$sopMsgAdd              = new WorkSopMsgSending();
					$sopMsgAdd->corp_id     = $sopMsg->corp_id;
					$sopMsgAdd->sop_id      = $sopMsg->sop_id;
					$sopMsgAdd->sop_time_id = $sopTimeId;
					$sopMsgAdd->user_id     = $sopMsg->user_id;
					$sopMsgAdd->external_id = $sopMsg->external_id;
					$sopMsgAdd->send_time   = $nowSendTime;
					$sopMsgAdd->create_time = $sopMsg->create_time;
					$sopMsgAdd->content     = $content;

					if ($sopMsgAdd->save()) {
						if ($nowSendTime < $todayEndTime) {
							$key               = $sopMsgAdd->user_id . '_' . $nowSendTime;
							$sopMsgIds[$key][] = $sopMsgAdd->id;
						}
					} else {
						\Yii::error(SUtils::modelError($sopMsgAdd), 'message_update_sop');
					}
				}

				if ($sopMsgIds) {
					//当日发送
					foreach ($sopMsgIds as $key => $sopMsgId) {
						$keyData     = explode('_', $key);
						$delaySecond = $keyData[1] - $nowTime;
						$delaySecond = $delaySecond > 0 ? $delaySecond : 0;
						$jobId       = \Yii::$app->work->delay($delaySecond)->push(new WorkSopMsgSendingJob([
							'work_sop_msg_sending_id' => $sopMsgId
						]));

						WorkSopMsgSending::updateAll(['queue_id' => $jobId], ['id' => $sopMsgId]);
					}
				}

			}

			return true;
		} catch (\Exception $e) {
			\Yii::error($e->getMessage(), 'message_sop');
		}

		return true;
	}

	/**
	 * 历史跟进状态提醒
	 *
	 * @param $corpId
	 * @param $sub_id
	 * @param $follow_id
	 * @param $sopTimeId
	 * @param $user_ids
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function sendFollowSopMsg ($corpId, $sub_id, $follow_id, $sopTimeId, $user_ids)
	{
		try {
			$sopTime = WorkSopTime::findOne($sopTimeId);
			$nowTime = time();

			if ($sopTime) {
				//发送时间
				$time_one = $sopTime->time_one ? $sopTime->time_one : 0;
				$time_two = $sopTime->time_two ? $sopTime->time_two : 0;
				if ($sopTime->time_type == 1) {
					$send_time = $nowTime + $time_one * 3600 + $time_two * 60;
				} else {
					$send_time = strtotime("+$time_one days");
					if ($time_two) {
						$send_time = strtotime(date('Y-m-d', $send_time) . ' ' . $time_two);
					} else {
						$send_time = strtotime(date('Y-m-d', $send_time));
					}
				}
				//发送内容
				$sopContent  = WorkSopTimeContent::find()->where(['sop_time_id' => $sopTimeId, 'status' => 1])->all();
				$contentData = [];
				foreach ($sopContent as $content) {
					$contentData[] = json_decode($content->content, true);
				}
				$content = json_encode($contentData);
				//发送人员
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($corpId, $Temp["department"], $Temp["user"], 1, true, 0, [], $sub_id);

				if (!empty($user_ids)) {
					$followUser = WorkExternalContactFollowUser::find()->where(['user_id' => $user_ids, 'del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN], 'follow_id' => $follow_id])->select('`user_id`,`external_userid`')->asArray()->all();

					foreach ($followUser as $follow) {
						$sopMsg              = new WorkSopMsgSending();
						$sopMsg->corp_id     = $corpId;
						$sopMsg->sop_id      = $sopTime->sop_id;
						$sopMsg->sop_time_id = $sopTimeId;
						$sopMsg->user_id     = $follow['user_id'];
						$sopMsg->external_id = $follow['external_userid'];
						$sopMsg->send_time   = $send_time;
						$sopMsg->create_time = date('Y-m-d H:i:s', $nowTime);
						$sopMsg->content     = $content;

						if (!$sopMsg->save()) {
							\Yii::error(SUtils::modelError($sopMsg), 'message_follow_sop');
						}
					}
				}
			}

			return true;
		} catch (\Exception $e) {
			\Yii::error($e->getMessage(), 'message_sop');
		}

		return true;
	}

	/**
	 * 每日0点设置SOP规则消息定时发送
	 */
	public static function sopMsgSendingTime ()
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(0);

		try {
			$stime = strtotime(date('Y-m-d'));
			$etime = $stime + 86399;

			$allSopMsg = WorkSopMsgSending::find()->where(['status' => 0, 'is_del' => 0])->andFilterWhere(['between', 'send_time', $stime, $etime]);
			$allSopMsg = $allSopMsg->select('count(`id`) num, `id`, `is_chat`, `sop_time_id`, `user_id`, `send_time`');
			$allSopMsg = $allSopMsg->groupBy('is_chat,sop_time_id,user_id,send_time');
			$allSopMsg = $allSopMsg->asArray()->all();

			foreach ($allSopMsg as $k => $v) {
				$sopMsgIds = [];
				if ($v['num'] == 1) {
					$sopMsgIds[] = $v['id'];
				} else {
					$sopMsgTime = WorkSopMsgSending::find()->where(['status' => 0, 'is_del' => 0])->andFilterWhere(['between', 'send_time', $stime, $etime]);
					$sopMsgTime = $sopMsgTime->andWhere(['is_chat' => $v['is_chat'], 'sop_time_id' => $v['sop_time_id'], 'user_id' => $v['user_id'], 'send_time' => $v['send_time']]);
					$sopMsgTime = $sopMsgTime->select('id')->asArray()->all();
					foreach ($sopMsgTime as $val) {
						$sopMsgIds[] = $val['id'];
					}
				}

				//指定时间发送
				$second = $v['send_time'] - time();
				$second = $second > 0 ? $second : 0;
				$jobId  = \Yii::$app->work->delay($second)->push(new WorkSopMsgSendingJob([
					'work_sop_msg_sending_id' => $sopMsgIds
				]));

				WorkSopMsgSending::updateAll(['queue_id' => $jobId], ['id' => $sopMsgIds]);
			}

			return true;
		} catch (\Exception $e) {
			\Yii::error($e->getMessage(), 'message_sop_sending_time');
		}

		return true;
	}
}
