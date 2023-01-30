<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\queue\GetGroupMsgResultJob;
	use app\queue\WorkGroupSendingJob;
	use app\util\DateUtil;
	use app\util\MsgUtil;
	use app\util\SUtils;
	use Yii;
    use yii\db\Expression;

    /**
	 * This is the model class for table "{{%work_group_sending}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id                    企业ID
	 * @property int      $agentid                    授权方应用id
	 * @property string   $title                      消息名称
	 * @property int      $send_type                  1、全部客户 2、按条件筛选客户 3、企业成员 4、选择群主
	 * @property int      $push_type                  0立即发送1指定时间发送
	 * @property int      $belong_id                  成员归属1全部成员2部分成员
	 * @property string   $push_time                  发送时间
	 * @property int      $queue_id                   队列id
	 * @property string   $user_key                   选择的成员或客户标志
	 * @property string   $content                    发送内容
	 * @property int      $status                     发送状态 0未发送 1已发送 2发送失败
	 * @property int      $groupId                    分组id
	 * @property int      $material_sync              不同步到内容库1同步
	 * @property int      $attachment_id              内容引擎id
	 * @property int      $work_material_id           企业微信素材id
	 * @property int      $sync_attachment_id         同步后的素材id
	 * @property int      $msg_type                   消息类型1文本2图片3图文4音频5视频6小程序7文件
	 * @property int      $is_redpacket               是否群发红包1是0否
	 * @property int      $rule_id                    红包规则id
	 * @property string   $rule_text                  红包规则内容（非存储规则）
	 * @property string   $rule_type_set              初始单个红包金额类型：1、固定金额，2、随机金额
	 * @property string   $redpacket_amount           活动投放金额
	 * @property string   $send_amount                已领取金额
	 * @property string   $send_num                   已领取人数
	 * @property string   $condition                  筛选条件
	 * @property string   $error_msg                  错误信息
	 * @property int      $error_code                 错误码
	 * @property string   $msgid                      群发消息id
	 * @property string   $fail_list                  失败的人员
	 * @property string   $success_list               成功人员
	 * @property int      $real_num                   实际人数
	 * @property int      $will_num                   预计人数
	 * @property int      $is_del                     删除状态 0 未删除 1 已删除
	 * @property string   $create_time                创建时间
	 * @property string   $update_time                更新时间
	 * @property string   $attribute                  高级属性字段
	 * @property string   $others                     客户其他筛选字段值
	 * @property int      $interval                   是否开启间隔 1关 2开
	 * @property int      $interval_time              间隔时间 1 （30分钟）  2 （1小时）  3 （2小时）  4 （3小时）  5 （4小时）
	 * @property int      $interval_num               间隔人数
	 * @property string   $chat_ids                   群聊id
	 *
	 * @property WorkCorp $corp
	 */
	class WorkGroupSending extends \yii\db\ActiveRecord
	{

		const GROUP_REDPACKET = "groupRedpacket";
		const H5_URL = '/h5/pages/groupRedpacketSend/index';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_group_sending}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agentid', 'send_type', 'push_type', 'belong_id', 'queue_id', 'status', 'groupId', 'material_sync', 'attachment_id', 'work_material_id', 'sync_attachment_id', 'msg_type', 'error_code', 'real_num', 'will_num', 'is_del', 'interval', 'interval_time', 'interval_num', 'is_redpacket', 'rule_id', 'send_num', 'rule_type_set'], 'integer'],
				[['push_time', 'create_time', 'update_time'], 'safe'],
				[['user_key', 'content', 'fail_list', 'success_list', 'chat_ids', 'rule_text','condition'], 'string'],
				[['title'], 'string', 'max' => 32],
				[['error_msg'], 'string', 'max' => 255],
				[['msgid'], 'string', 'max' => 200],
				[['redpacket_amount', 'send_amount'], 'number'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                 => Yii::t('app', 'ID'),
				'corp_id'            => Yii::t('app', '企业ID'),
				'agentid'            => Yii::t('app', '授权方应用id'),
				'title'              => Yii::t('app', '消息名称'),
				'send_type'          => Yii::t('app', '1、全部客户 2、按条件筛选客户 3、企业成员 4、选择群主'),
				'push_type'          => Yii::t('app', '0立即发送1指定时间发送'),
				'belong_id'          => Yii::t('app', '成员归属1全部成员2部分成员'),
				'push_time'          => Yii::t('app', '发送时间'),
				'queue_id'           => Yii::t('app', '队列id'),
				'user_key'           => Yii::t('app', '选择的成员或客户标志'),
				'content'            => Yii::t('app', '发送内容'),
				'status'             => Yii::t('app', '发送状态 0未发送（定时发送） 1已发送（员工已确认） 2发送失败 3发送中（员工未确认）'),
				'groupId'            => Yii::t('app', '分组id'),
				'material_sync'      => Yii::t('app', '不同步到内容库1同步'),
				'attachment_id'      => Yii::t('app', '内容引擎id'),
				'work_material_id'   => Yii::t('app', '企业微信素材id'),
				'sync_attachment_id' => Yii::t('app', '同步后的素材id'),
				'msg_type'           => Yii::t('app', '消息类型1文本2图片3图文4音频5视频6小程序7文件'),
				'is_redpacket'       => Yii::t('app', '是否群发红包1是0否'),
				'rule_id'            => Yii::t('app', '红包规则id'),
				'rule_text'          => Yii::t('app', '红包规则内容（非存储规则）'),
				'rule_type_set'      => Yii::t('app', '初始单个红包金额类型：1、固定金额，2、随机金额'),
				'redpacket_amount'   => Yii::t('app', '活动投放金额'),
				'send_amount'        => Yii::t('app', '已领取金额'),
				'send_num'           => Yii::t('app', '已领取笔数'),
				'condition'          => Yii::t('app', '筛选条件'),
				'error_msg'          => Yii::t('app', '错误信息'),
				'error_code'         => Yii::t('app', '错误码'),
				'msgid'              => Yii::t('app', '群发消息id'),
				'fail_list'          => Yii::t('app', '失败的人员'),
				'success_list'       => Yii::t('app', '成功人员'),
				'real_num'           => Yii::t('app', '实际人数'),
				'will_num'           => Yii::t('app', '预计人数'),
				'is_del'             => Yii::t('app', '删除状态 0 未删除 1 已删除'),
				'create_time'        => Yii::t('app', '创建时间'),
				'update_time'        => Yii::t('app', '更新时间'),
				'attribute'          => Yii::t('app', '高级属性字段'),
				'others'             => Yii::t('app', '客户其他筛选字段值'),
				'interval'           => Yii::t('app', '是否开启间隔 1关 2开'),
				'interval_time'      => Yii::t('app', '间隔时间 1 （30分钟）  2 （1小时）  3 （2小时）  4 （3小时）  5 （4小时）'),
				'interval_num'       => Yii::t('app', '间隔人数'),
				'chat_ids'           => Yii::t('app', '群聊id'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @param int $type
		 *
		 * @return array
		 *
		 */
		public function dumpData ($type = 0)
		{
			$result = [
				'id'                 => $this->id,
				'key'                => $this->id,
				'corp_id'            => $this->corp_id,
				'title'              => $this->title,
				'agentid'            => $this->agentid,
				'belong_id'          => $this->belong_id,
				'status'             => $this->status,
				'push_type'          => $this->push_type,
				'send_type'          => $this->send_type,
				'msg_type'           => $this->msg_type,
				'groupId'            => $this->groupId,
				'material_sync'      => $this->material_sync,
				'sync_attachment_id' => $this->sync_attachment_id,
				'attachment_id'      => $this->attachment_id,
				'work_material_id'   => $this->work_material_id,
				'content'            => $this->content,
				'is_redpacket'       => $this->is_redpacket,
				'rule_id'            => $this->rule_id,
				'rule_text'          => $this->rule_text,
				'rule_type_set'      => $this->rule_type_set,
				'redpacket_amount'   => $this->redpacket_amount,
				'send_amount'        => $this->send_amount,
				'send_num'           => $this->send_num,
				'is_del'             => $this->is_del,
				'user_key'           => $this->user_key,
				'will_num'           => $this->will_num,
				'real_num'           => $this->real_num,
				'push_time'          => $this->push_time,
				'error_msg'          => $this->error_msg,
				'create_time'        => $this->create_time,
				'interval'           => $this->interval,
				'interval_time'      => $this->interval_time,
				'interval_num'       => $this->interval_num,
			];

			if ($result['is_redpacket'] == 1) {
				if ($this->rule_id > 0) {
					$redRule = RedPackRule::find()->andWhere(['id' => $this->rule_id])->asArray()->one();
				} else {
					$redRule = json_decode($this->rule_text, true);
				}
				$result['rule_type']              = (int)$redRule['type'];
				$result['rule_fixed_amount']      = $redRule['type'] == 1 ? $redRule['fixed_amount'] : 0;
				$result['rule_min_random_amount'] = $redRule['type'] == 2 ? $redRule['min_random_amount'] : 0;
				$result['rule_max_random_amount'] = $redRule['type'] == 2 ? $redRule['max_random_amount'] : 0;
				$result['rule_name']              = $redRule['name'];
				$result['rule_pic_url']           = $redRule['pic_url'];
				$result['rule_title']             = $redRule['title'];
				$result['rule_des']               = $redRule['des'];
				$result['rule_thanking']          = $redRule['thanking'];
			}

			$pushTime = !empty($this->push_time) ? strtotime($this->push_time) : '';
			if (!empty($pushTime)) {
				$result['push_time'] = date('Y-m-d H:i', $pushTime);
			}
			if ($this->interval == 1) {
				$result['inter_name'] = '不间隔';
			} else {
				switch ($this->interval_time) {
					case 1:
						$inter = '30分钟';
						break;
					case 2:
						$inter = '1小时';
						break;
					case 3:
						$inter = '2小时';
						break;
					case 4:
						$inter = '3小时';
						break;
					case 5:
						$inter = '4小时';
						break;
				}
				$result['inter_name'] = '间隔' . $inter . '发送' . $this->interval_num . '人';

			}
			$userCheck = [];
			if ($this->send_type == 1 || $this->send_type == 2 || $this->send_type == 4) {
				$result['real_num'] = WorkTagGroupStatistic::find()->where(['send_id' => $this->id, 'send' => 1])->count();
				$result['will_num'] = $this->will_num;
			}
			if ($this->send_type == 3) {
				$result['will_num'] = $this->will_num;
				$result['real_num'] = $this->real_num;
			}
			$willChatList = [];
			$realChatList = [];
//			if ($this->send_type == 4) {
//				$chatIds = !empty($this->chat_ids) ? json_decode($this->chat_ids, true) : [];
//				if (!empty($chatIds)) {
//					foreach ($chatIds as $k => $chat) {
//						$willChatList[$k]['name'] = WorkChat::getChatName($chat);
//					}
//				}
//				$result['will_num'] = count($chatIds);
//				$groupStat = WorkTagGroupStatistic::find()->where(['send_id' => $this->id, 'send' => 1])->asArray()->all();
//				if (!empty($groupStat)) {
//					foreach ($groupStat as $k => $stat) {
//						$realChatList[$k]['name'] = WorkChat::getChatName($stat['chat_id']);
//					}
//				}
//			}
			$result['will_chat_name'] = $willChatList;
			$result['real_chat_name'] = $realChatList;
			$result['attribute']      = [];
			$result['others']         = [];
			if (!empty($this->others)) {
				$others  = json_decode($this->others, true);
				$groupId = [];
				if (!empty($others['group_id'])) {
					foreach ($others['group_id'] as $group) {
						$tagGroup = WorkTagGroup::findOne($group);
						if (empty($tagGroup)) {
							$tag = WorkTagGroup::find()->where(['group_name' => '未分组', 'corp_id' => $this->corp_id, 'type' => 0])->one();
							array_push($groupId, $tag->id);
						} else {
							array_push($groupId, $group);;
						}
					}
				}
				if (!empty($groupId)) {
					$groupId = array_unique($groupId);
				}
				if (!empty($others['user_ids'])) {
					foreach ($others['user_ids'] as $key => $user) {
						if (strpos($user['id'], 'd') === false) {
							$userCheck[$key]['id']       = isset($user['id']) ? $user['id'] : '';
							if(isset($user['name'])){
								$userCheck[$key]['title']    = $user['name'];
							}else{
								$userCheck[$key]['title']    = isset($user['title']) ? $user['title'] : '';
							}
							$userCheck[$key]['user_key'] = isset($user['user_key']) ? $user['user_key'] : '';
						}
					}
				}

//				if($this->send_type == 4 && !empty($this->user_key)){
//					$userIdCheck = [];
//					$userKey     = json_decode($this->user_key, true);
//					foreach ($userKey as $key => $u) {
//						$userIdCheck[$key]['id'] = $u['id'];
//						$workUser                = WorkUser::findOne($u['id']);
//						$name                    = '';
//						if (!empty($workUser)) {
//							$name = $workUser->name;
//						}
//						$userIdCheck[$key]['name']   = $name;
//						$userIdCheck[$key]['status'] = 0;
//					}
//				}
				$result['others']['group_id']    = $groupId;
				$result['others']['tag_type']    = $others['tag_type'];
				$result['others']['province']    = $others['province'];
				$result['others']['city']        = $others['city'];
				$result['others']['is_fans']     = isset($others['is_fans']) ? $others['is_fans'] : 0;
				$result['others']['sex']         = isset($others['sex']) ? $others['sex'] : -1;
				$result['others']['user_ids']    = [];
				$result['others']['follow_id']   = $others['follow_id'];
				$result['others']['start_time']  = $others['start_time'];
				$result['others']['end_time']    = $others['end_time'];
				$result['others']['update_time'] = isset($others['update_time']) && !empty($others['update_time']) ? $others['update_time'] : [];
				$result['others']['chat_time']   = isset($others['chat_time']) && !empty($others['chat_time']) ? $others['chat_time'] : [];
				$result['others']['sign_id']     = isset($others['sign_id']) && !empty($others['sign_id']) ? $others['sign_id'] : [];
				$result['others']['follow_num1'] = isset($others['follow_num1']) && !empty($others['follow_num1']) ? $others['follow_num1'] : '';
				$result['others']['follow_num2'] = isset($others['follow_num2']) && !empty($others['follow_num2']) ? $others['follow_num2'] : '';
			} else {
				$result['others']['group_id']    = [];
				$result['others']['tag_type']    = 1;
				$result['others']['province']    = '';
				$result['others']['city']        = '';
				$result['others']['is_fans']     = 0;
				$result['others']['sex']         = -1;
				$result['others']['user_ids']    = [];
				$result['others']['follow_id']   = -1;
				$result['others']['start_time']  = '';
				$result['others']['end_time']    = '';
				$result['others']['update_time'] = [];
				$result['others']['chat_time']   = [];
				$result['others']['sign_id']     = [];
				$result['others']['follow_num1'] = '';
				$result['others']['follow_num2'] = '';
			}
//			if (!empty($this->user_key)) {
//				$result['others']['user_ids'] = json_decode($this->user_key, true);
//			}
			if (!empty($this->user_key)) {
				$userIdCheck = [];
				$userKey     = json_decode($this->user_key, true);
				foreach ($userKey as $k => $uu) {
					if(is_array($uu)){
						if (strpos($uu['id'], 'd') === false) {
							$uid              = isset($uu['id']) ? $uu['id'] : 0;
							$user_key         = isset($uu['user_key']) ? $uu['user_key'] : '';
							$Temp             = [];
							$Temp['id']       = $uid;
							if(isset($uu['key'])){
								$Temp['user_key'] = $Temp['key']  = isset($uu['key']) ? $uu['key'] : '';
							}else{
								$Temp['user_key'] = $user_key;
							}
							$workUser         = WorkUser::findOne($uid);
							$name             = '';
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
							if (isset($uu["name"])) {
								$Temp['name'] = $Temp['title'] = $name;
							}else{
								$Temp['title'] = $name;
							}
							$Temp["scopedSlots"] = ["title" => "custom"];
							array_push($userIdCheck, $Temp);
						} else {
							array_push($userIdCheck, $uu);
						}
					}else{
						$Temp = WorkDepartment::getUsers(0,$this->corp_id,[],0,[$uu]);
						if(is_array($Temp) && isset($Temp[0])){
							array_push($userIdCheck, $Temp[0]);
						}
					}
				}
				$result['others']['user_ids'] = $userIdCheck;
			}
			if (strtotime($this->create_time) < 1596181497 && ($this->send_type == 1 || $this->send_type == 2)) {
				if (!empty($userCheck)) {
					$result['others']['user_ids'] = $userCheck;
				}
			}
			$isShowSuccess = 0;
			if (strtotime($this->create_time) < 1597242600) {
				$result['others']['user_ids'] = [];
				$isShowSuccess = 1;
			}
			$result['isShowSuccess'] = $isShowSuccess;
			if (!empty($this->attribute) && $this->send_type != 4) {
				$result['attribute'] = json_decode($this->attribute, true);
			}
			if (empty($this->user_key)) {
				$result['user_key'] = [];
			}
			$users  = [];
			$userId = [];
			if (!empty($this->user_key) && ($this->send_type == 3 || $this->send_type == 4)) {
				$user_keys = json_decode($this->user_key, true);
				if (!empty($user_keys)) {
					foreach ($user_keys as $k => $u) {
						if (isset($u['id']) && !empty($u['id'])) {
							$work_user = WorkUser::findOne($u['id']);
							if (!empty($work_user)) {
								$dataUser             = $work_user->dumpData();
								$dataUser['user_key'] = isset($u['user_key']) ? $u['user_key'] : '';
								array_push($users, $dataUser);
							}
							array_push($userId, $u['id']);
						}
					}
				}
			}
			$chat_name = [];
			if (!empty($this->attribute) && $this->send_type == 4) {
				$chat_name = json_decode($this->attribute, true);
			}
			$result['chat_name'] = $chat_name;
			if ($type == 1) {
				$send_num  = WorkTagGroupStatistic::find()->where(['send_id' => $this->id, 'send' => 1])->count();
				$not_num   = WorkTagGroupStatistic::find()->where(['send_id' => $this->id, 'send' => 0])->count();
				$fail_num  = WorkTagGroupStatistic::find()->where(['send_id' => $this->id, 'send' => 2])->count();
				$limit_num = WorkTagGroupStatistic::find()->where(['send_id' => $this->id, 'send' => 3])->count();

				if ($result['is_redpacket'] == 1) {
					$time                    = time();
					$result['all_get_money'] = $result['send_amount'];
					$result['all_get_num']   = $result['send_num'];
					$all_not_get_money       = 0;
					$all_not_get_num         = 0;
					/*$all_expired_money       = 0;
					$all_expired_num         = 0;*/
					if ($result['send_type'] == 4) {
						$result['all_money'] = $send_num * $result['redpacket_amount'];
						if ($result['all_get_money'] > $result['all_money']) {
							$result['all_money'] = $result['all_get_money'];
						}

						$sendChatId = [];
						$redpacketSend = WorkGroupSendingRedpacketSend::find()->andWhere(['send_id' => $this->id, 'is_chat' => 1, 'is_send' => 1])->all();
						foreach ($redpacketSend as $k => $v) {
							$leftMoney = $v->send_money - $v->get_money;
							$all_not_get_money += $leftMoney;

							array_push($sendChatId, $v->external_userid);
						}
						//未领取群人数
						if ($sendChatId){
							$chatInfoNum = WorkChatInfo::find()->andWhere(['chat_id' => $sendChatId])->andWhere(['type' => 2, 'status' => 1])->andWhere(['<', 'join_time', $redpacketSend[0]['create_time']])->groupBy('external_id')->count();
							$all_not_get_num = $chatInfoNum > $result['all_get_num'] ? ($chatInfoNum - $result['all_get_num']) : 0;
						}
					} else {
						$result['all_money'] = $result['redpacket_amount'];
						$all_not_get_money   = $result['all_money'] - $result['all_get_money'];
						$redpacketSend       = WorkGroupSendingRedpacketSend::find()->where(['send_id' => $this->id])->andWhere(['status' => [0, 2]])->andWhere(['>', 'create_time', 0])->all();
						$not_get_money       = 0;
						foreach ($redpacketSend as $k => $v) {
							$not_get_money   += $v->send_money;
							$all_not_get_num += 1;
							if ($not_get_money > $all_not_get_money) {
								break;
							}
						}
					}

					$result['all_not_get_money'] = sprintf('%.2f', $all_not_get_money);
					$result['all_not_get_num']   = $all_not_get_num;
					/*$result['all_expired_money'] = $all_expired_money;
					$result['all_expired_num']   = $all_expired_num;*/
				}

				$result['user']         = $users;
				$condition              = $this->condition;
				$result['add_type']     = 0;
				$result['text_content'] = '';
				$result['sex']          = -1;
				$result['tag_ids']      = [];
				if (!empty($condition)) {
					$con               = json_decode($condition, true);
					$result['sex']     = $con['sex'];
					$result['tag_ids'] = !empty($con['tag_ids']) ? $con['tag_ids'] : [];
					$result['select_type'] = $con['select_type'] ?? 1; //筛选类型  1或查询 2 且查询 3 反选查询
				}

				$result['send_num']  = $send_num; //已送达
				$result['not_num']   = $not_num; //未送达
				$result['limit_num'] = $limit_num; //达到上限
				$result['fail_num']  = $fail_num; //因不是好友失败人数
				$tagName             = [];
				if (isset($con['tag_ids']) && !empty($con['tag_ids'])) {
					$tag_ids = is_array($con['tag_ids']) ?  $con['tag_ids'] : explode(',', $con['tag_ids']);
					$workTag = WorkTag::find()->where(['id' => $tag_ids])->select('tagname')->asArray()->all();
					if (!empty($workTag)) {
						foreach ($workTag as $tag) {
							array_push($tagName, $tag['tagname']);
						}
					}
				}
				$result['others']['tag_name'] = $tagName;
				$storeName                    = '';
				if (isset($others['sign_id']) && !empty($others['sign_id'])) {
					$sign = ApplicationSign::findOne($others['sign_id']);
					if (!empty($sign)) {
						$storeName = $sign->username;
					}
				}
				$result['others']['store_name'] = $storeName;
				$followName                     = '';
				if (isset($others['follow_id']) && !empty($others['follow_id'])) {
					$follow = Follow::findOne($others['follow_id']);
					if (!empty($follow)) {
						$followName = $follow->title;
					}
				}
				$result['others']['follow_name'] = $followName;
				if ($this->send_type != 4) {
					$attributeData = [];
					if (!empty($result['attribute'])) {
						foreach ($result['attribute'] as $k => $attr) {
							$field = CustomField::findOne($attr['field']);
							if (!empty($field)) {
								$attributeData[$k]['name']  = $field->title;
								$attributeData[$k]['value'] = $attr['match'];
							}
						}
					}
					$result['others']['attribute_data'] = $attributeData;
				}
			}
			$isShow = 0;
			if (strtotime($this->create_time) > 1597242600) {
				$isShow = 1;
			}
			$result['is_show'] = $isShow;

			return $result;
		}

		/**
		 * @param $data
		 *
		 * @return int
		 * @throws InvalidParameterException
		 * @throws \app\components\InvalidDataException
		 */
		public static function add ($data)
		{
			if (empty($data['corp_id']) || !in_array($data['send_type'], [1, 2, 3, 4])) {
				throw new InvalidParameterException("参数不正确");
			}
			if (empty($data['agent_id'])) {
				throw new InvalidParameterException("请选择企业应用");
			}
			if (empty($data['title'])) {
				throw new InvalidParameterException("名称不能为空");
			}
			if (!empty($data['id'])) {
				$group_sending = WorkGroupSending::find()->andWhere(['title' => $data['title'], 'corp_id' => $data['corp_id'], 'is_del' => 0])->andWhere(['<>', 'id', $data['id']])->one();
				if (!empty($group_sending)) {
					throw new InvalidParameterException("消息名称存在重复");
				}
			} else {
				$group_sending = WorkGroupSending::findOne(['title' => $data['title'], 'corp_id' => $data['corp_id'], 'is_del' => 0]);
				if (!empty($group_sending)) {
					throw new InvalidParameterException("消息名称存在重复");
				}
			}
			if (!empty($data['title']) && mb_strlen($data['title'], 'utf-8') > 20) {
				throw new InvalidParameterException('名称最多20个字！');
			}
			if ($data['send_type'] != 3) {
				$sendId = [];
				if ($data['isMasterAccount'] == 2) {
					$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($data["user_ids"]);
					if(empty($Temp["user"]) && empty($Temp["department"])){
						$sub_detail = WorkDepartment::GiveDepartmentReturnUserData($data['corp_id'], [], [], 1, true, 0, [],$data['sub_id'],0,true);
					}else{
						$sub_detail = WorkDepartment::GiveDepartmentReturnUserData($data['corp_id'], $Temp["department"], $Temp["user"], 1, true, 0, [],$data['sub_id']);
					}
					if (!empty($sub_detail)) {
						if ($data['send_type'] == 4) {
							//当前选择的是群主
							$ownerId = static::sendChat($data['corp_id'], $sub_detail);
							if (empty($ownerId)) {
								throw new InvalidParameterException('群归属成员为空，无法创建！');
							}
							$sub_detail = $ownerId;
						}
						$workUser = WorkUser::find()->where(['corp_id' => $data['corp_id'], 'status' => 1, 'is_del' => 0, 'id' => $sub_detail])->asArray()->all();
					} else {
						if ($data['send_type'] == 1) {
							throw new InvalidParameterException('客户归属成员为空，无法创建！');
						} elseif ($data['send_type'] == 4) {
							throw new InvalidParameterException('群归属成员为空，无法创建！');
						}
					}
				} else {
					$workUser = WorkUser::find()->where(['corp_id' => $data['corp_id'], 'status' => 1, 'is_del' => 0]);
					if ($data["belong_id"] == 1 && $data['isMasterAccount'] == 2) {
						$user_ids = WorkDepartment::GiveDepartmentReturnUserData($data['corp_id'], [], [], 1, true, 0, [], $data['sub_id'], 0, true);
					}
					if ($data["belong_id"] == 1 && $data['isMasterAccount'] == 1) {
						$user_ids = WorkDepartment::GiveDepartmentReturnUserData($data['corp_id'], [1], [], 1, true);
					}
					if($data["belong_id"] != 1){
						$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($data["user_ids"]);
						$user_ids = $Temp["user"];
					}
					if(empty($user_ids)){
						throw new InvalidParameterException('归属成员为空，无法创建！');
					}
					if(!empty($user_ids)){
						$workUser = $workUser->andWhere(['id' => $user_ids]);
					}
					if ($data['send_type'] == 4) {
						//当前选择的是群主
						$ownerId = static::sendChat($data['corp_id'], $user_ids);
						if (empty($ownerId)) {
							throw new InvalidParameterException('群归属成员为空，无法创建！');
						}
						$workUser = $workUser->andWhere(['id' => $ownerId]);
					}
					$workUser = $workUser->asArray()->all();

				}
				if (!empty($workUser)) {
					foreach ($workUser as $k => $user) {
						$sendId[$k]['id']   = $user['id'];
						$sendId[$k]['name'] = empty($user['name']) ? '' : $user['name'];
					}
				}
				$data['user_ids'] = $sendId;
			}
			if (empty($data['user_ids'])) {
				if ($data['send_type'] == 4) {
					throw new InvalidParameterException("请选择群主");
				}
				if ($data['send_type'] == 3) {
					throw new InvalidParameterException("请选择企业成员");
				}

			}
			if ($data['send_type'] == 2) {
				if (empty($data['users'])) {
					throw new InvalidParameterException("请选择群发客户");
				}
			}
			if ($data['send_type'] == 3 && $data['is_redpacket'] == 1){
				throw new InvalidParameterException("不支持对企业成员群发红包");
			}

			//群发内容
			if ($data['is_redpacket'] == 0){
				if ($data['send_type'] == 1 || $data['send_type'] == 2 || $data['send_type'] == 4) {
					//发送的是欢迎语
					$data['status']   = 1;
					$data['msg_type'] = 0;
					WorkWelcome::verify($data, 1);
				} else {
					//发送应用消息
					static::verifyData($data);
				}
			}

			if ($data['push_type'] == 1) {
				if (empty($data['push_time'])) {
					throw new InvalidParameterException("指定时间不能为空");
				}
				if (strtotime($data['push_time']) < time()) {
					throw new InvalidParameterException("指定时间必须大于当前时间");
				}
			}
			$sync_attachment_id = 0;
			if (!empty($data['id'])) {
				$sending              = WorkGroupSending::findOne($data['id']);
				$sync_attachment_id   = $sending->sync_attachment_id;
				$sending->update_time = DateUtil::getCurrentTime();
			} else {
				$sending              = new WorkGroupSending();
				$sending->create_time = DateUtil::getCurrentTime();
			}
			$userId = [];
			if (!empty($data['user_ids']) && $data['send_type'] == 4) {

				$ids = array_column($data['user_ids'],"id");
				array_push($userId, ...$ids);
//				foreach ($data['user_ids'] as $user) {
//					array_push($userId, $user['id']);
//				}
			}
			$attribute = [];
			$chatIds   = [];
			if (!empty($userId) && $data['send_type'] == 4) {
				$chat_name = WorkChatInfo::getChatList(3, $userId);
				$attribute = $chat_name;
				$chat      = WorkChat::find()->where(['owner_id' => $userId, 'group_chat' => 0])->andWhere(['status' => [0, 1, 2, 3]])->select('id')->asArray()->all();
				foreach ($chat as $v) {
					array_push($chatIds, $v['id']);
				}
			}
			if (!empty($chatIds)) {
				$chatIds = json_encode($chatIds);
			} else {
				$chatIds = '';
			}
			//同步到内容库
			if ($data['material_sync'] == 1 && empty($data['attachment_id'])) {
				$data['sync_attachment_id']  = $sync_attachment_id;
				$sync_attachment_id          = WorkWelcome::syncData($data, 1);
				$sending->sync_attachment_id = $sync_attachment_id;
			}
			//获取企业微信素材
			if (!empty($data['attachment_id'])) {
				$sending->work_material_id = WorkWelcome::getMaterialId($data);
			}
			if (!empty($data['attribute'])) {
				$sending->attribute = json_encode($data['attribute']);
			}
			if ($data['send_type'] == 4 && !empty($attribute)) {
				$sending->attribute = json_encode($attribute);
			}
			if (in_array($data['send_type'], [2, 3, 4])) {
				if ($data['send_type'] == 4) {
					$cc = 0;
					foreach ($data['user_ids'] as $id) {
						$chatCount = WorkChat::find()->where(['owner_id' => $id['id'], 'group_chat' => 0])->andWhere(['status' => [0, 1, 2, 3]])->count();
						$cc        += $chatCount;
					}
					$willNum = $cc;
				}
				if ($data['send_type'] == 2) {
					$willNum = count($data['users']);
				}
				if ($data['send_type'] == 3) {
					$willNum = count($data['user_ids']);
				}
			} else {
				$userIds = [];
				if (!empty($data['user_ids'])) {
					$userIds = array_column($data['user_ids'],"id");
//					foreach ($data['user_ids'] as $id) {
//						array_push($userIds, $id['id']);
//					}
				}
				$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
				$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
				$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $data['corp_id'], 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
				if (!empty($userIds)) {
					$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $userIds]);
				}
				$willNumData = $workExternalUserData->select('we.id as wid')->groupBy('we.id')->asArray()->all();
				$numData = [];
				$externalStatistic = WorkGroupSending::getExternalMonthCount($data['corp_id']);
				if (!empty($externalStatistic)) {
					foreach ($willNumData as $key => $val) {
						$flag = false;
						foreach ($externalStatistic as $sta) {
							if ($sta['external_id'] == $val['wid'] && $sta['cc'] >= 4) {
								//当前客户超过4次 需要过滤
								$flag = true;
							}
						}
						if (!$flag) {
							array_push($numData, $val['wid']);
						}
					}
					$willNum = count($numData);
				} else {
					$willNum = count($willNumData);
				}
				if (empty($willNum)) {
					throw new InvalidParameterException("请选择群发客户");
				}
			}
			if ($data['interval'] == 2) {
				if (empty(intval($data['interval_time']))) {
					throw new InvalidParameterException("请选择间隔时间");
				}
				if (empty(intval($data['interval_num']))) {
					throw new InvalidParameterException("请输入间隔人数");
				}
			}
			$others                = [];
			$others['sex']         = $data['sex'];
			$others['group_id']    = $data['group_id'];
			$others['tag_type']    = $data['tag_type'];
			$others['province']    = $data['province'];
			$others['city']        = $data['city'];
			$others['user_ids']    = $data['user_ids'];
			$others['follow_id']   = $data['follow_id'];
			$others['start_time']  = $data['start_time'];
			$others['end_time']    = $data['end_time'];
			$others['update_time'] = $data['update_time'];
			$others['chat_time']   = $data['chat_time'];
			$others['sign_id']     = $data['sign_id'];
			$others['follow_num1'] = $data['follow_num1'];
			$others['follow_num2'] = $data['follow_num2'];
			$others['is_fans']     = isset($data['is_fans']) ? $data['is_fans'] : 0;
			$sending->others       = json_encode($others);
			$condition['sex']      = $data['sex'];
			$condition['tag_ids']  = $data['tag_ids'];
			$condition['select_type']  = $data['select_type']; //筛选类型  1或查询 2 且查询 3 反选查询
			$content               = WorkWelcome::getContent($data);
			$sending->belong_id    = $data['belong_id'];
			$sending->corp_id      = $data['corp_id'];
			$sending->agentid      = $data['agent_id'];
			$sending->title        = $data['title'];
			$sending->send_type    = $data['send_type'];
			$sending->msg_type     = $data['msg_type'];
			$sending->push_type    = $data['push_type'];
			if ($data['push_type'] == 0) {
				$pushTime = date('Y-m-d H:i:s', time());
			} else {
				$pushTime = $data['push_time'] . ':00';
			}
			$sending->push_time     = $pushTime;
			$sending->content       = json_encode($content);
			$sending->user_key      = !empty($data['user_ids']) ? json_encode($data['user_ids']) : '';
			$sending->material_sync = $data['material_sync'];
			$sending->will_num      = $willNum;
			$sending->attachment_id = $data['attachment_id'];
			$sending->groupId       = $data['groupId'];
			$sending->interval      = $data['interval'];
			$sending->interval_time = $data['interval_time'];
			$sending->interval_num  = $data['interval_num'];
			$sending->chat_ids      = $chatIds;
			$sending->condition     = json_encode($condition);
			$sending->is_redpacket  = $data['is_redpacket'];

			//群发红包
			if ($data['is_redpacket'] == 1) {
				$rule_id   = $data['rule_id'];
				$rule_text = '';
				if (empty($rule_id)) {
					$ruleData                      = [];
					$ruleData['name']              = $data['rule_name'];
					$ruleData['type']              = $data['rule_type'];
					$ruleData['fixed_amount']      = $data['rule_fixed_amount'];
					$ruleData['min_random_amount'] = $data['rule_min_random_amount'];
					$ruleData['max_random_amount'] = $data['rule_max_random_amount'];
					$ruleData['pic_url']           = $data['rule_pic_url'];
					$ruleData['title']             = $data['rule_title'];
					$ruleData['des']               = $data['rule_des'];
					$ruleData['thanking']          = $data['rule_thanking'];

					if ($data['rule_save'] == 1) {
						//新建红包规则并保存
						$ruleData['id']  = 0;
						$ruleData['uid'] = $data['uid'];

						$rule_id = RedPackRule::setData($ruleData);
					} else {
						$rule_text = json_encode($ruleData, true);
					}
				}
				$sending->rule_id          = $rule_id;
				$sending->rule_text        = $rule_text;
				$sending->redpacket_amount = $data['redpacket_amount'];
			}

			if (!empty($sending->dirtyAttributes)) {
				if (!$sending->validate() || !$sending->save()) {
					throw new InvalidDataException(SUtils::modelError($sending));
				}
			}
			if ($data['push_type'] == 0) {
				$jobId  = \Yii::$app->queue->push(new WorkGroupSendingJob([
					'work_group_sending_id' => $sending->id
				]));
				$status = 3;
				\Yii::error($status, '$status');
				$sending->status   = $status;
				$sending->queue_id = $jobId;
				$sending->save();
			}
			if ($data['push_type'] == 1) {
				//指定时间发送
				$send_time = strtotime($data['push_time']);
				$second    = $send_time - time();
				if (!empty($sending->queue_id)) {
					\Yii::$app->queue->remove($sending->queue_id);
				}
				$jobId             = \Yii::$app->queue->delay($second)->push(new WorkGroupSendingJob([
					'work_group_sending_id' => $sending->id
				]));
				$sending->queue_id = $jobId;
				$sending->save();
			}

			return $sending->id;
		}

		/**
		 * 验证数据
		 *
		 * @param $data
		 *
		 * @return bool
		 *
		 * @throws InvalidParameterException
		 */
		public static function verifyData ($data)
		{
			$attachment_id      = isset($data['attachment_id']) ? $data['attachment_id'] : '';
			$voice_media_id     = isset($data['voice_media_id']) ? $data['voice_media_id'] : '';
			$video_media_id     = isset($data['video_media_id']) ? $data['video_media_id'] : '';
			$file_media_id      = isset($data['file_media_id']) ? $data['file_media_id'] : '';
			$link_attachment_id = isset($data['link_attachment_id']) ? $data['link_attachment_id'] : '';
			$link_title         = isset($data['link_title']) ? $data['link_title'] : '';
			$link_url           = isset($data['link_url']) ? $data['link_url'] : '';
			$link_desc          = isset($data['link_desc']) ? $data['link_desc'] : '';
			$mini_title         = isset($data['mini_title']) ? $data['mini_title'] : '';
			$mini_pic_media_id  = isset($data['mini_pic_media_id']) ? $data['mini_pic_media_id'] : '';
			$mini_appid         = isset($data['mini_appid']) ? $data['mini_appid'] : '';
			$mini_page          = isset($data['mini_page']) ? $data['mini_page'] : '';
			switch ($data['msg_type']) {
				case 1:
					if (!empty($data['text_content']) && mb_strlen($data['text_content'], 'utf-8') > 512) {
						throw new InvalidParameterException('文本内容最多512个字！');
					}
					break;
				case 2:
					if (empty($data['media_id'])) {
						throw new InvalidParameterException('图片内容不能为空！');
					}
					$attachment = Attachment::findOne($data['media_id']);
					if ($attachment->status == 0) {
						throw new InvalidParameterException('图片素材已删除！');
					}
					break;
				case 5:
					if (!empty($attachment_id)) {
						$attachment = Attachment::findOne($attachment_id);
						if ($attachment->status == 0) {
							throw new InvalidParameterException('图文素材已删除！');
						}
					} else {
						if (!empty($link_attachment_id)) {
							$attachment = Attachment::findOne($link_attachment_id);
							if ($attachment->status == 0) {
								throw new InvalidParameterException('图文素材的图片已删除！');
							}
						}
						if (empty($link_title)) {
							throw new InvalidParameterException('网页标题不能为空！');
						}
						if (empty($link_url)) {
							throw new InvalidParameterException('跳转链接不能为空！');
						}
						if (!empty($link_title) && mb_strlen($link_title, 'utf-8') > 32) {
							throw new InvalidParameterException('网页标题最多32个字！');
						}
						if (!empty($link_desc) && mb_strlen($link_desc, 'utf-8') > 128) {
							throw new InvalidParameterException('网页描述最多128个字！');
						}
						$preg = "/^http(s)?:\\/\\/.+/";
						if (!preg_match($preg, $link_url)) {
							throw new InvalidParameterException('网页的链接必须是以http或https开头！');
						}
					}
					break;
				case 3:
					if (empty($voice_media_id)) {
						throw new InvalidParameterException('音频不能为空！');
					}
					$attachment = Attachment::findOne($voice_media_id);
					if ($attachment->status == 0) {
						throw new InvalidParameterException('音频素材已删除！');
					}
					break;
				case 4:
					if (empty($video_media_id)) {
						throw new InvalidParameterException('视频不能为空！');
					}
					$attachment = Attachment::findOne($video_media_id);
					if ($attachment->status == 0) {
						throw new InvalidParameterException('视频素材已删除！');
					}
					break;
				case 6:
					if (!empty($attachment_id)) {
						$attachment = Attachment::findOne($attachment_id);
						if ($attachment->status == 0) {
							throw new InvalidParameterException('小程序素材已删除！');
						}
					} else {
						$attachment = Attachment::findOne($mini_pic_media_id);
						if (!empty($attachment)) {
							if ($attachment->status == 0) {
								throw new InvalidParameterException('小程序封面素材已删除！');
							}
						}
						if (empty($mini_title)) {
							throw new InvalidParameterException('小程序消息标题不能为空！');
						}
						if (!empty($mini_title) && mb_strlen($mini_title, 'utf-8') < 4) {
							throw new InvalidParameterException('小程序标题最少4个字！');
						}
						if (!empty($mini_title) && mb_strlen($mini_title, 'utf-8') > 12) {
							throw new InvalidParameterException('小程序标题最多12个字！');
						}
						if (empty($mini_pic_media_id)) {
							throw new InvalidParameterException('小程序封面不能为空！');
						}
						if (empty($mini_appid)) {
							throw new InvalidParameterException('小程序appid不能为空！');
						}
						if (empty($mini_page)) {
							throw new InvalidParameterException('小程序路径不能为空！');
						}
					}
					break;
				case 7:
					if (empty($file_media_id)) {
						throw new InvalidParameterException('文件不能为空！');
					}
					$attachment = Attachment::findOne($file_media_id);
					if ($attachment->status == 0) {
						throw new InvalidParameterException('文件素材已删除！');
					}
					break;
			}

			return true;
		}

		/**
		 * @param $data
		 *
		 * @return array|string
		 *
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 * @throws \app\components\ForbiddenException
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function sendData ($data)
		{
			$attachment_id = isset($data['attachment_id']) ? $data['attachment_id'] : 0;
			$content       = json_decode($data['content'], true);
			switch ($data['msg_type']) {
				case 1:
					$result = $content['text']['content'];
					break;
				case 2:
					$result   = '';
					$media_id = $content['image']['media_id'];
					$verify   = WorkWelcome::verifyAttachment($media_id);
					if ($verify) {
						$work_material = WorkMaterial::findOne(['attachment_id' => $media_id, 'corp_id' => $data['corp_id']]);
						MsgUtil::checkWorkNeedReload($work_material);
						$result = $work_material->media_id;
					}
					break;
				case 5:
					$result = [];
					if (!empty($attachment_id)) {
						$verify = WorkWelcome::verifyAttachment($attachment_id);
					} else {
						$id            = $content['link']['picurl'];
						$verify        = WorkWelcome::verifyAttachment($id);
						$attachment_id = $id;
					}
					if ($verify) {
						$attachment            = Attachment::findOne(['id' => $attachment_id]);
						$result['picurl']      = \Yii::$app->params['site_url'] . $attachment->local_path;
						$result['url']         = $content['link']['url'];
						$result['title']       = $content['link']['title'];
						$result['description'] = $content['link']['description'];

					}
					break;
				case 3:
					$result   = '';
					$media_id = $content['voice']['media_id'];
					$verify   = WorkWelcome::verifyAttachment($media_id);
					if ($verify) {
						$work_material = WorkMaterial::findOne(['attachment_id' => $media_id, 'corp_id' => $data['corp_id']]);
						MsgUtil::checkWorkNeedReload($work_material);
						$result = $work_material->media_id;
					}
					break;
				case 4:
					$result   = '';
					$media_id = $content['video']['media_id'];
					$verify   = WorkWelcome::verifyAttachment($media_id);
					if ($verify) {
						$work_material = WorkMaterial::findOne(['attachment_id' => $media_id, 'corp_id' => $data['corp_id']]);
						MsgUtil::checkWorkNeedReload($work_material);
						$result = $work_material->media_id;
					}
					break;
				case 6:
					if (!empty($attachment_id)) {
						$att             = Attachment::findOne($attachment_id);
						$result['appid'] = $att->appId;
						$result['page']  = $att->appPath;
					} else {
						$result['appid'] = $content['miniprogram']['appid'];
						$result['page']  = $content['miniprogram']['page'];
					}
					$result['title'] = $content['miniprogram']['title'];
					break;
				case 7:
					$result   = '';
					$media_id = $content['file']['media_id'];
					$verify   = WorkWelcome::verifyAttachment($media_id);
					if ($verify) {
						$work_material = WorkMaterial::findOne(['attachment_id' => $media_id, 'corp_id' => $data['corp_id']]);
						MsgUtil::checkWorkNeedReload($work_material);
						$result = $work_material->media_id;
					}
					break;
			}

			return $result;
		}

		/**
		 * @param $id
		 *
		 * @return array
		 *
		 * @throws \yii\db\Exception
		 */
		public static function sendExternalData ($id)
		{
			$groupSending = WorkGroupSending::findOne($id);
			$corpId       = $groupSending->corp_id;
			$others       = [];
			if (!empty($groupSending->others)) {
				$others = json_decode($groupSending->others, true);
			}
			$tag_ids = [];
			$sex     = '';
			if (!empty($groupSending->condition)) {
				$condition = json_decode($groupSending->condition, true);
				$tag_ids   = $condition['tag_ids'];
				$sex       = $condition['sex'];
			}
			$sign_id     = isset($others['sign_id']) ? $others['sign_id'] : 0;
			$follow_num1 = isset($others['follow_num1']) ? $others['follow_num1'] : 0;
			$follow_num2 = isset($others['follow_num2']) ? $others['follow_num2'] : 0;
			$chat_time   = isset($others['chat_time']) ? $others['chat_time'] : [];
			$update_time = isset($others['update_time']) ? $others['update_time'] : [];

			$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
			$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
			$workExternalUserData = $workExternalUserData->leftJoin('{{%work_user}} wk', 'wk.id=wf.user_id');
			$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $corpId, 'wk.status' => 1, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
			$uids                 = [];
			if (isset($others['user_ids']) && !empty($others['user_ids'])) {
				foreach ($others['user_ids'] as $uid) {
					array_push($uids, $uid['id']);
				}
			}

			$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $uids]);

			//高级属性搜索
			$fieldList    = CustomField::find()->where('is_define=0')->select('`id`,`key`')->asArray()->all();//默认属性
			$fieldD       = [];
			$contactField = [];//列表展示字段
			foreach ($fieldList as $k => $v) {
				$fieldD[$v['key']] = $v['id'];
				if (in_array($v['key'], ['name', 'sex', 'phone', 'area'])) {
					array_push($contactField, $v['id']);
				}
			}

			if (!empty($update_time)) {
				$workExternalUserData = $workExternalUserData->andFilterWhere(['between', 'wf.update_time', strtotime($update_time[0]), strtotime($update_time[1] . ':59')]);
			}

			if ($follow_num1 != '' || $follow_num2 != '') {
				$follow_num = '';
				if (($follow_num1 == '0' && $follow_num2 == '0') || ($follow_num1 == '' && $follow_num2 == '0') || ($follow_num1 == '0' && $follow_num2 == '')) {
					$follow_num = 0;
				}
				if ((($follow_num1 == '' || $follow_num == '0') && $follow_num2 > 0)) {
					$follow_num = $follow_num2;
				}
				if (($follow_num1 > 0 && ($follow_num2 == '' || $follow_num2 == '0'))) {
					$follow_num = $follow_num1;
				}
				if (!empty($follow_num) || $follow_num == '0') {
					$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_num' => $follow_num]);
				} else {
					$workExternalUserData = $workExternalUserData->andWhere(['>=', 'wf.follow_num', $follow_num1]);
					$workExternalUserData = $workExternalUserData->andWhere(['<=', 'wf.follow_num', $follow_num2]);
				}
			}

			if (!empty($sign_id)) {
				$contactId = [];
				$member    = WorkExternalContactMember::find()->where(['sign_id' => $sign_id, 'is_bind' => 1])->select('external_userid')->groupBy('external_userid');
				$member    = $member->asArray()->all();
				if (!empty($member)) {
					foreach ($member as $mem) {
						array_push($contactId, $mem['external_userid']);
					}
				}
				$workExternalUserData = $workExternalUserData->andWhere(['we.id' => $contactId]);
			}
			if (!empty($chat_time)) {
				$contactId      = [];
				$chartStartTime = strtotime($chat_time[0]) . '000';
				$chartEndTime   = strtotime($chat_time[1] . ':59') . '000';
				$sql            = 'SELECT external_id, msgtime FROM ( SELECT `wa`.`external_id`, wa.msgtime FROM {{%work_msg_audit_info}} `wa` LEFT JOIN {{%work_msg_audit}} `w` ON w.corp_id = wa.audit_id WHERE (`w`.`corp_id` = 1) AND (`wa`.`external_id` != \'\') ORDER BY msgtime DESC ) AS a GROUP BY external_id';
				$auditInfo      = \Yii::$app->getDb()->createCommand($sql)->queryAll();
				if (!empty($auditInfo)) {
					foreach ($auditInfo as $info) {
						if ($info['msgtime'] >= $chartStartTime && $info['msgtime'] <= $chartEndTime) {
							array_push($contactId, $info['external_id']);
						}
					}
				}
				$workExternalUserData = $workExternalUserData->andWhere(['we.id' => $contactId]);
			}
			if ($groupSending->send_type == 2) {
				$fieldData = json_decode($groupSending->attribute, true);
				$province  = $others['province'];
				$city      = $others['city'];
				if (!empty($others['start_time']) && !empty($others['end_time'])) {
					$workExternalUserData = $workExternalUserData->andFilterWhere(['between', 'createtime', strtotime($others['start_time']), strtotime($others['end_time'])]);
				}
				if ($others['follow_id'] != '-1') {
					$workExternalUserData = $workExternalUserData->andWhere(['we.follow_id' => $others['follow_id']]);
				}
				$tag_type = 1;
				if (!empty($groupSending->condition)) {
					$condition = json_decode($groupSending->condition, true);
					!empty($condition['select_type']) && in_array($condition['select_type'], [1, 2, 3]) && $tag_type = $condition['select_type'];
				}
                //标签搜索
                $tagIds = $tag_ids ? (is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids)) : [];
                if (!empty($tagIds) && in_array($tag_type, [1, 2, 3])) {
                    $userTag = WorkTagFollowUser::find()
                        ->alias('wtf')
                        ->innerJoin('{{%work_tag}} wtg', '`wtf`.`tag_id` = `wtg`.`id` AND wtg.`is_del` = 0')
                        ->where(['wtf.corp_id' => $corpId,'wtg.corp_id' => $corpId,'wtf.status' => 1])
                        ->groupBy('wtf.follow_user_id')
                        ->select('wtf.follow_user_id,GROUP_CONCAT(wtg.id) tag_ids');

                    $workExternalUserData = $workExternalUserData->leftJoin(['wt' => $userTag], '`wt`.`follow_user_id` = `wf`.`id`');
                    $tagsFilter = [];
                    if ($tag_type == 1) {//标签或
                        $tagsFilter[] = 'OR';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                        });
                    }elseif ($tag_type == 2) {//标签且
                        $tagsFilter[] = 'AND';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                        });
                    }elseif ($tag_type == 3) {//标签不包含
                        $tagsFilter[] = 'AND';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['not',['wt.tag_ids' => NULL]] : (new Expression("NOT FIND_IN_SET($value,IFNULL(wt.tag_ids,''))"));
                        });
                    }
                    $workExternalUserData->andWhere($tagsFilter);
                }
				if ($province || $sex != '-1' || !empty($fieldData)) {
					$i           = 0;
					$customValue = [];
					$newValue    = [];
					if ($sex != '-1') {
						if ($sex == 1) {
							$sex = '男';
						} elseif ($sex == 2) {
							$sex = '女';
						} else {
							$sex = '未知';
						}
						$custom   = CustomFieldValue::find()->where(['fieldid' => $fieldD['sex'], 'value' => $sex,'type'=>1])->select('cid')->groupBy('cid')->asArray()->all();
						$customId = array_column($custom, 'cid');

						array_push($customValue, $customId);
						$i++;
					}
					if (!empty($phone)) {
						$custom   = CustomFieldValue::find()->where(['and', ['in', 'fieldid', [$fieldD['phone'], $fieldD['qq']]], ['like', 'value', $phone]])->select('cid')->groupBy('cid')->asArray()->all();
						$customId = array_column($custom, 'cid');
						array_push($customValue, $customId);
						$i++;
					}
					if (!empty($work)) {
						$custom   = CustomFieldValue::find()->where(['fieldid' => $fieldD['work'], 'value' => $work])->select('cid')->groupBy('cid')->asArray()->all();
						$customId = array_column($custom, 'cid');
						array_push($customValue, $customId);
						$i++;
					}
					if (!empty($province)) {
						if (!empty($city)) {
							$custom   = CustomFieldValue::find()->where(['fieldid' => $fieldD['area'], 'value' => $province . '-' . $city])->select('cid')->groupBy('cid')->asArray()->all();
							$customId = array_column($custom, 'cid');
							array_push($customValue, $customId);
							$i++;

						} else {
							$custom   = CustomFieldValue::find()->where(['and', ['fieldid' => $fieldD['area']], ['like', 'value', $province . '-']])->select('cid')->groupBy('cid')->asArray()->all();
							$customId = array_column($custom, 'cid');
							array_push($customValue, $customId);
							$i++;
						}
					}
					if (!empty($fieldData)) {
						foreach ($fieldData as $val) {
							$custom = CustomFieldValue::find()->alias('c')->leftJoin('{{%custom_field}} f', '`f`.`id` = `c`.`fieldid`')->where(['c.fieldid' => $val['field'], 'f.status' => 1])->andWhere(['like', 'c.value', $val['match']])->select('c.cid')->groupBy('c.cid');
							$custom = $custom->asArray()->all();
							if (!empty($custom)) {
								$customId = array_column($custom, 'cid');
								array_push($customValue, $customId);
								$i++;
							}
						}
					}
					$new = [];
					if (!empty($customValue)) {
						foreach ($customValue as $value) {
							foreach ($value as $val) {
								array_push($new, $val);
							}
						}
						$newVal = array_count_values($new);
						if (!empty($newVal)) {
							foreach ($newVal as $k => $v) {
								if ($v == $i) {
									array_push($newValue, $k);
								}
							}
						}
					}
					$workExternalUserData = $workExternalUserData->andWhere(['in', 'we.id', $newValue]);
				}
			}
			$externalStatistic       = WorkGroupSending::getExternalMonthCount($corpId);
			$result                  = [];
			$newResult               = [];
			$workExternalUserDataNew = $workExternalUserData->select('we.external_userid as wid,wf.user_id,we.id as eid')->groupBy('we.id')->asArray()->all();
			if (!empty($workExternalUserDataNew)) {
				$i = 0;
				foreach ($workExternalUserDataNew as $new) {
					$flag = false;
					if (!empty($externalStatistic)) {
						foreach ($externalStatistic as $sta) {
							if ($sta['external_id'] == $new['eid'] && $sta['cc'] >= 4) {
								//当前客户超过4次 需要过滤
								$flag = true;
							}
						}
					}
					if (!$flag) {
						$newResult[$i]['wid']     = $new['wid'];
						$newResult[$i]['user_id'] = $new['user_id'];
						$i++;
					}
				}
			}

			$workExternalUserData = $workExternalUserData->select('we.external_userid as wid,we.id as eid')->groupBy('we.id');
			$workExternalUserData = $workExternalUserData->asArray()->all();
			if (!empty($workExternalUserData)) {
				foreach ($workExternalUserData as $key => $val) {
					$flag = false;
					if (!empty($externalStatistic)) {
						foreach ($externalStatistic as $sta) {
							if ($sta['external_id'] == $val['eid'] && $sta['cc'] >= 4) {
								//当前客户超过4次 需要过滤
								$flag = true;
							}
						}
					}
					if (!$flag) {
						array_push($result, $val['wid']);
					}

				}
			}

			return ['result' => $result, 'workExternalUserDataNew' => $newResult];

		}

		/**
		 * 获取当前月每个客户的群发次数
		 * @param $corpId
		 *
		 * @return array
		 *
		 */
		public static function getExternalMonthCount ($corpId)
		{
			$monthStart         = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
			$monthEnd           = date('Y-m-d', mktime(0, 0, 0, date('m'), date('t'), date('Y')));
			$groupUserStatistic = WorkTagGroupStatistic::find()->alias('s')->leftJoin('{{%work_group_sending}} g', 'g.id=s.send_id')->select('count(s.id) cc,s.external_id');
			$groupUserStatistic = $groupUserStatistic->where(['>', 's.send_id', 0])->andWhere(['>', 's.external_id', 0])->andWhere(['s.send' => 1, 'g.corp_id' => $corpId])->andFilterWhere(['between', 's.push_time', $monthStart, $monthEnd . ' 23:59:59'])->groupBy('s.external_id');
			$groupUserStatistic = $groupUserStatistic->asArray()->all();

			return $groupUserStatistic;
		}

		/**
		 * 获取群主的userId
		 *
		 * @param $corpId
		 * @param $ownerId
		 *
		 * @return array
		 *
		 */
		public static function sendChat ($corpId, $ownerId = [])
		{
			$userId   = [];
			$workChat = WorkChat::find()->where(['corp_id' => $corpId, 'group_chat' => 0]);
			if (!empty($ownerId)) {
				$workChat = $workChat->andWhere(['owner_id' => $ownerId]);
			}
			$workChat = $workChat->select('owner_id')->groupBy('owner_id')->asArray()->all();
			if (!empty($workChat)) {
				$userId = array_column($workChat,"owner_id");
			}

			return $userId;
		}

		/**
		 * @param $corpId
		 * @param $userKey
		 * @param $type
		 *
		 * @return array|string
		 *
		 */
		public static function getSendData ($corpId, $userKey, $type)
		{
			$userid = [];
			$chat   = [];
			$name   = '';
			if (!empty($userKey)) {
				$userKey = json_decode($userKey, true);
				foreach ($userKey as $val) {
					if (isset($val['id']) && !empty($val['id'])) {
						$user = WorkUser::findOne($val['id']);
						if (!empty($user) && $user->status == 1) {
							if ($type == 0) {
								array_push($userid, $user->userid);
							} else {
								$workChat = WorkChat::find()->where(['corp_id' => $corpId, 'owner_id' => $val['id'], 'group_chat' => 0])->andWhere(['status' => [0, 1, 2, 3]])->select('name')->asArray()->all();
								if (!empty($workChat)) {
									foreach ($workChat as $chatAt) {
										if (!empty($chatAt['name'])) {
											array_push($chat, $chatAt['name']);
										}
									}
								}
							}

						}
					}
				}
			}
			if ($type == 0) {
				return $userid;
			} else {
				if (!empty($chat)) {
					$name = implode(',', $chat);
				}

				return $name;
			}
		}

		/**
		 * 获取群发结果
		 * @param $corpId
		 *
		 */
		public static function getSendResult ($corpId)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$page     = 1;
			$pageSize = 100;
			$time     = date('Y-m-01 H:i:s', strtotime(date("Y-m-d")));
			while (true) {
				$offset       = ($page - 1) * $pageSize;
				$tagPullGroup = WorkGroupSending::find()->alias("a")
					->leftJoin("{{%work_tag_group_statistic}} as b", "a.id = b.send_id")
					->where(['a.corp_id' => $corpId, 'a.is_del' => 0, "b.push_type" => 0])
					->andWhere("a.create_time > '$time'")
					->select("a.*,b.push_type");
				$tagPullGroup = $tagPullGroup->groupBy('a.id')->limit($pageSize)->offset($offset)->asArray()->all();

				if (empty($tagPullGroup)) {
					break;
				}
				foreach ($tagPullGroup as $val) {
					if ($val['send_type'] == 1 || $val['send_type'] == 2 || $val['send_type'] == 4) {
						$type = 1;
						if ($val['send_type'] == 4) {
							$type = 2;
						}
						\Yii::$app->queue->push(new GetGroupMsgResultJob([
							'sendId' => $val['id'],
							'type'   => $type,
						]));
					}
				}
				$page++;
			}
		}

		/**
		 * 获取群发红包图文
		 *
		 * @param                  $corpid
		 * @param WorkGroupSending $sending
		 * @param                  $user_id
		 * @param                  $toChat
		 *
		 * @return array
		 */
		public static function sendRedpacketContent ($corpid, $sending, $user_id, $toChat = 0)
		{
			$content = [];
			$assist  = static::GROUP_REDPACKET . '_' . $sending->id . '_' . $user_id . '_' . $toChat;
			$web_url = \Yii::$app->params['web_url'];
			$url     = $web_url . static::H5_URL . '?corpid=' . $corpid . '&assist=' . $assist . '&agent_id=' . $sending->agentid;

			if ($sending->rule_id > 0) {
				$redRule = RedPackRule::find()->andWhere(['id' => $sending->rule_id])->asArray()->one();
			} else {
				$redRule = json_decode($sending->rule_text, true);
			}

			$content['link'] = [
				'title'  => $redRule['title'],
				'picurl' => \Yii::$app->params['site_url'] . $redRule['pic_url'],
				'desc'   => $redRule['des'],
				'url'    => $url,
			];

			return $content;
		}

	}
