<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\queue\WorkGroupSendingJob;
	use app\queue\WorkTagPullGroupJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;
	use yii\debug\panels\EventPanel;

	/**
	 * This is the model class for table "{{%work_tag_pull_group}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id      授权的企业ID
	 * @property string   $title        标题
	 * @property int      $send_type    1、全部客户 2、按条件筛选客户
	 * @property int      $belong_id    成员归属1全部成员2部分成员
	 * @property string   $user_key     选择的成员或客户标志
	 * @property string   $content      入群引导语
	 * @property int      $is_filter    是否过滤0不过滤1过滤
	 * @property int      $status       发送状态 0未发送 1已发送 2发送失败
	 * @property string   $sender       成员确认信息
	 * @property string   $others       客户其他筛选字段值
	 * @property string   $fail_list    失败人员
	 * @property string   $success_list 成功人员
	 * @property int      $error_code   错误码
	 * @property string   $error_msg    错误信息
	 * @property int      $queue_id     队列id
	 * @property int      $real_num     实际发送人数
	 * @property int      $will_num     预计发送人数
	 * @property int      $is_del       删除状态 0 未删除 1 已删除
	 * @property string   $create_time  创建时间
	 *
	 * @property WorkCorp $corp
	 */
	class WorkTagPullGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_pull_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'send_type', 'belong_id', 'is_filter', 'status', 'error_code', 'queue_id', 'real_num', 'will_num', 'is_del'], 'integer'],
				[['user_key', 'content', 'others', 'sender', 'fail_list', 'success_list'], 'string'],
				[['create_time'], 'safe'],
				[['title'], 'string', 'max' => 64],
				[['error_msg'], 'string', 'max' => 255],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'corp_id'      => Yii::t('app', '授权的企业ID'),
				'title'        => Yii::t('app', '标题'),
				'send_type'    => Yii::t('app', '1、全部客户 2、按条件筛选客户'),
				'belong_id'    => Yii::t('app', '成员归属1全部成员2部分成员'),
				'user_key'     => Yii::t('app', '选择的成员或客户标志'),
				'content'      => Yii::t('app', '入群引导语'),
				'is_filter'    => Yii::t('app', '是否过滤0不过滤1过滤'),
				'status'       => Yii::t('app', '发送状态 0未发送 1已发送 2发送失败'),
				'sender'       => Yii::t('app', '成员确认信息'),
				'others'       => Yii::t('app', '客户其他筛选字段值'),
				'fail_list'    => Yii::t('app', '失败的人员'),
				'success_list' => Yii::t('app', '成功人员'),
				'error_code'   => Yii::t('app', '错误码'),
				'error_msg'    => Yii::t('app', '错误信息'),
				'queue_id'     => Yii::t('app', '队列id'),
				'real_num'     => Yii::t('app', '实际发送人数'),
				'will_num'     => Yii::t('app', '预计发送人数'),
				'is_del'       => Yii::t('app', '删除状态 0 未删除 1 已删除'),
				'create_time'  => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		public function dumpData ($type = 0)
		{
			$result = [
				'id'          => $this->id,
				'key'         => $this->id,
				'corp_id'     => $this->corp_id,
				'title'       => $this->title,
				'status'      => $this->status,
				'send_type'   => $this->send_type,
				'content'     => $this->content,
				'is_del'      => $this->is_del,
				'user_key'    => $this->user_key,
				'error_msg'   => $this->error_msg,
				'will_num'    => $this->will_num,
				'real_num'    => $this->real_num,
				'is_filter'   => $this->is_filter,
				'create_time' => $this->create_time,
				'belong_id'   => $this->belong_id,
			];

			$result['others'] = [];
			$user_name        = [];
			$ids              = [];
			if (!empty($this->others)) {
				$others                          = json_decode($this->others, true);
				$result['others']['attribute']   = $others['attribute'];
				$result['others']['province']    = $others['others']['province'];
				$result['others']['city']        = $others['others']['city'];
				$result['others']['user_ids']    = $others['others']['user_ids'];
				$result['others']['follow_id']   = $others['others']['follow_id'];
				$result['others']['start_time']  = $others['others']['start_time'];
				$result['others']['end_time']    = $others['others']['end_time'];
				$result['others']['sex']         = $others['others']['sex'];
				$result['others']['tag_ids']     = $others['others']['tag_ids'];
				$result['others']['update_time'] = isset($others['others']['update_time']) && !empty($others['others']['update_time']) ? $others['others']['update_time'] : [];
				$result['others']['chat_time']   = isset($others['others']['chat_time']) && !empty($others['others']['chat_time']) ? $others['others']['chat_time'] : [];
				$result['others']['sign_id']     = isset($others['others']['sign_id']) && !empty($others['others']['sign_id']) ? $others['others']['sign_id'] : [];
				$result['others']['follow_num1'] = isset($others['others']['follow_num1']) && !empty($others['others']['follow_num1']) ? $others['others']['follow_num1'] : '';
				$result['others']['follow_num2'] = isset($others['others']['follow_num2']) && !empty($others['others']['follow_num2']) ? $others['others']['follow_num2'] : '';
				if (isset($others['others']['user_ids']) && !empty($others['others']['user_ids'])) {
					$userIds = $others['others']['user_ids'];
					foreach ($userIds as &$user) {
						if (!isset($user["title"])) {
							$user["title"]       = isset($user['name']) ? $user['name'] : '';
							$user["scopedSlots"] = ["title" => "custom"];
							$user["key"]         = isset($user['user_key']) ? $user['user_key'] : '';
						}
						array_push($user_name, isset($user['name'])?$user['name']:$user['title']);
						array_push($ids, $user['id']);
					}
					$result['others']['user_ids'] = $userIds;
				}

				$attributeData = [];
				if (!empty($others['attribute'])) {
					foreach ($others['attribute'] as $k => $attr) {
						$field = CustomField::findOne($attr['field']);
						if (!empty($field)) {
							$attributeData[$k]['name']  = $field->title;
							$attributeData[$k]['value'] = $attr['match'];
						}
					}
				}
				$tagName                            = [];
				$result['others']['attribute_data'] = $attributeData;
				if (!empty($others['others']['tag_ids'])) {
					$tag_ids = explode(',', $others['others']['tag_ids']);
					$workTag = WorkTag::find()->where(['id' => $tag_ids])->select('tagname')->asArray()->all();
					if (!empty($workTag)) {
						foreach ($workTag as $tag) {
							array_push($tagName, $tag['tagname']);
						}
					}
				}
				$result['others']['tag_name'] = $tagName;
				$storeName                    = '';
				if (!empty($others['others']['sign_id'])) {
					$sign = ApplicationSign::findOne($others['others']['sign_id']);
					if (!empty($sign)) {
						$storeName = $sign->username;
					}
				}
				$result['others']['store_name'] = $storeName;
				$followName                     = '';
				if (!empty($others['others']['follow_id'])) {
					$follow = Follow::findOne($others['others']['follow_id']);
					if (!empty($follow)) {
						$followName = $follow->title;
					}
				}
				$result['others']['follow_name'] = $followName;

			}
			$userId = [];
			if (empty($user_name)) {
				if ($this->send_type == 2) {
					$userKey = json_decode($this->user_key, true);
				} else {
					$workData = static::returnExternalData($this->corp_id, $ids);
					$userKey  = array_column($workData, 'wid');
				}
				$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $userKey])->select('user_id')->asArray()->all();
				if (!empty($followUser)) {
					foreach ($followUser as $user) {
						array_push($userId, $user['user_id']);
					}
				}
				if (!empty($userId)) {
					$userId   = array_unique($userId);
					$workUser = WorkUser::find()->where(['id' => $userId])->select('name')->asArray()->all();
					if (!empty($workUser)) {
						foreach ($workUser as $user) {
							array_push($user_name, $user['name']);
						}
					}
				}
			}
			if (empty($this->user_key)) {
				$result['user_key'] = [];
			}
			if (!empty($this->user_key)) {
				$user_keys          = json_decode($this->user_key, true);
				$result['user_key'] = $user_keys;
			}
			$has_num       = 0;
			$invite_num    = 0;
			$userStatistic = WorkTagGroupUserStatistic::find()->where(['pull_id' => $this->id])->asArray()->all();
			if (!empty($userStatistic)) {
				foreach ($userStatistic as $sta) {
					$has_num    += intval($sta['has_num']);
					$invite_num += intval($sta['real_num']);
				}
			}
			$sender_name = [];
			if ($type == 0) {
				$result['has_group'] = $has_num;//已入群客户
				$result['no_group']  = $this->will_num - $has_num;//未入群客户
				if (!empty($sender)) {
					foreach ($sender as $send) {
						array_push($sender_name, $send['name']);
					}
				}
				$result['sender_name'] = $sender_name;
			}
			$chat_name = [];
			$chatList  = [];
			$list      = WorkChatWayList::find()->alias('w');
			$list      = $list->leftJoin('{{%work_chat}} s', '`w`.`chat_id` = `s`.`id`');
			$list      = $list->where(['w.tag_pull_id' => $this->id, 's.corp_id' => $this->corp_id])->select('s.id as id,s.name as name,w.chat_status,w.status,w.limit,w.local_path,w.media_id')->asArray()->all();
			if (!empty($list)) {
				foreach ($list as $k => $v) {
					$count                      = WorkChatInfo::find()->where(['chat_id' => $v['id'], 'status' => 1])->count();
					$chatList[$k]['name']       = WorkChat::getChatName($v['id']);
					$chatList[$k]['member_num'] = $count;
					$chatList[$k]['chat_id']    = $v['id'];
					$chatList[$k]['local_path'] = $v['local_path'];
					$chatList[$k]['media_id']   = $v['media_id'];
					$chatList[$k]['total']      = 200;
					array_push($chat_name, $chatList[$k]['name']);
				}
			}
			$result['chat_list']      = $chatList;
			$result['chat_name']      = $chat_name;
			$result['user_name']      = $user_name;
			$result['has_num']        = $has_num;
			$result['has_not_num']    = $this->will_num - $has_num;
			$result['invite_num']     = $invite_num;
			$result['invite_not_num'] = $this->will_num - $invite_num;
			$result['real_num']       = $invite_num;

			return $result;
		}

		public static function returnExternalData ($corp_id, $ids)
		{
			$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
			$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
			$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $corp_id, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
			if (!empty($ids)) {
				$workExternalUserData = $workExternalUserData->andWhere(['wf.user_id' => $ids]);
			}
			$workExternalUserData = $workExternalUserData->select('we.id as wid')->groupBy('we.id')->asArray()->all();

			return $workExternalUserData;
		}

		public static function add ($data)
		{
			$transaction = Yii::$app->db->beginTransaction();
			try {
				if (empty($data['title'])) {
					throw new InvalidParameterException("名称不能为空");
				}
				if (isset($data['id']) && !empty($data['id'])) {
					$group_sending = WorkTagPullGroup::find()->andWhere(['title' => $data['title'], 'corp_id' => $data['corp_id'], 'is_del' => 0])->andWhere(['<>', 'id', $data['id']])->one();
					if (!empty($group_sending)) {
						throw new InvalidParameterException("名称存在重复");
					}
				} else {
					$group_sending = WorkTagPullGroup::findOne(['title' => $data['title'], 'corp_id' => $data['corp_id'], 'is_del' => 0]);
					if (!empty($group_sending)) {
						throw new InvalidParameterException("消息名称存在重复");
					}
				}
				if (!empty($data['title']) && mb_strlen($data['title'], 'utf-8') > 30) {
					throw new InvalidParameterException('名称最多30个字！');
				}
//				if (empty($data['users'])) {
//					if ($data['send_type'] == 2) {
//						throw new InvalidParameterException("请选择客户");
//					}
//				}
				if (empty($data['chat_list'])) {
					throw new InvalidDataException('群聊不能为空');
				}
				if (isset($data['id']) && !empty($data['id'])) {
					$sending = WorkTagPullGroup::findOne($data['id']);
				} else {
					$sending              = new WorkTagPullGroup();
					$sending->create_time = DateUtil::getCurrentTime();
				}
				$chatId = [];
				if (!empty($data['chat_list'])) {
					foreach ($data['chat_list'] as $list) {
						array_push($chatId, $list['chat_id']);
					}
				}
				if ($data['is_filter'] == 0) {
					$chatId = [];
				}
				if ($data['belong_id'] == 1) {
					$sendId   = [];
					$workUser = WorkUser::find()->where(['corp_id' => $data['corp_id'], 'status' => 1, 'is_del' => 0]);
					if ($data['isMasterAccount'] == 2) {
						$sub_detail = AuthoritySubUserDetail::getDepartmentUserLists($data['sub_id'], $data['corp_id']);
						if (is_array($sub_detail)) {
							$workUser = $workUser->andWhere(['id' => $sub_detail]);
						} else if ($sub_detail === false) {
							throw new InvalidParameterException("当前客户归属成员为空，无法创建");
						}
					}
					$workUser = $workUser->asArray()->all();
					if (!empty($workUser)) {
						foreach ($workUser as $k => $user) {
							$sendId[$k]['id']   = $user['id'];
							$sendId[$k]['name'] = $user['name'];
						}
					}
					$data['user_ids'] = $sendId;
				}
				if (empty($data['user_ids'])) {
					throw new InvalidParameterException("请选择群发成员");
				}
				$user_id = [];
				if (!empty($data['user_ids'])) {
					foreach ($data['user_ids'] as $user) {
						array_push($user_id, $user['id']);
					}
				}
				$uData['corp_id']         = $data['corp_id'];
				$uData['isMasterAccount'] = $data['isMasterAccount'];
				$uData['sub_id']          = $data['sub_id'];
				$uData['sex']             = $data['sex'];
				$uData['province']        = $data['province'];
				$uData['city']            = $data['city'];
				$uData['follow_id']       = $data['follow_id'];
				$uData['fieldData']       = $data['attribute'];
				$uData['tag_ids']         = $data['tag_ids'];
				$uData['tag_type']        = $data['tag_type'];
				$uData['start_time']      = $data['start_time'];
				$uData['end_time']        = $data['end_time'];
				$uData['correctness']     = $data['send_type'];
				$uData['update_time']     = $data['update_time'];
				$uData['follow_num1']     = $data['follow_num1'];
				$uData['follow_num2']     = $data['follow_num2'];
				$uData['chat_time']       = $data['chat_time'];
				$uData['sign_id']         = $data['sign_id'];
				$uData['is_fans']         = isset($data['is_fans']) ? $data['is_fans'] : 0;
				$uData['belong_id']       = $data['belong_id'];
				$uData['chat_id']         = $chatId;
				$uData['user_ids']        = $user_id;
				$uData['uid']             = $data['uid'];
				$info                     = WorkExternalContactFollowUser::getData($uData);
				if ($data['is_filter'] == 1) {
					$willNum = $info['real_num'];
				} else {
					$willNum = count($info['result']);
				}
				$realUsers = [];
				$result    = $info['result'];
				if (!empty($result)) {
					foreach ($result as $val) {
						array_push($realUsers, $val['key']);
					}
				}
				$data['users'] = $realUsers;
				if ($data['is_filter'] == 1 && $willNum == 0) {
					throw new InvalidDataException("客户过滤后，没有客户发送，无法创建任务");
				}
				if (empty($willNum)) {
					throw new InvalidDataException("请选择客户");
				}
				$attribute                       = $data['attribute'];
				$others                          = [];
				$others['attribute']             = $attribute;
				$others['others']['province']    = $data['province'];
				$others['others']['city']        = $data['city'];
				$others['others']['user_ids']    = $data['user_ids'];
				$others['others']['follow_id']   = $data['follow_id'];
				$others['others']['start_time']  = $data['start_time'];
				$others['others']['end_time']    = $data['end_time'];
				$others['others']['update_time'] = $data['update_time'];
				$others['others']['chat_time']   = $data['chat_time'];
				$others['others']['sign_id']     = $data['sign_id'];
				$others['others']['follow_num1'] = $data['follow_num1'];
				$others['others']['follow_num2'] = $data['follow_num2'];
				$others['others']['sex']         = $data['sex'];
				$others['others']['tag_ids']     = $data['tag_ids'];
				$others['others']['tag_type']    = $data['tag_type'];
				$others['others']['is_fans']     = isset($data['is_fans']) ? $data['is_fans'] : 0;
				$sending->others                 = json_encode($others);
				$sending->corp_id                = $data['corp_id'];
				$sending->belong_id              = $data['belong_id'];
				$sending->title                  = $data['title'];
				$sending->send_type              = $data['send_type'];
				$sending->content                = $data['content'];
				$sending->is_filter              = $data['is_filter'];
				$sending->user_key               = !empty($data['users']) ? json_encode($data['users']) : '';
				$sending->will_num               = $willNum;
				if (!empty($sending->dirtyAttributes)) {
					if (!$sending->validate() || !$sending->save()) {
						throw new InvalidDataException(SUtils::modelError($sending));
					}
				}

				$jobId = \Yii::$app->queue->push(new WorkTagPullGroupJob([
					'groupId' => $sending->id
				]));

				$status            = 3;
				$sending->status   = $status;
				$sending->queue_id = $jobId;
				$sending->save();

				if (!empty($data['chat_list'])) {
					$chatId = [];
					WorkChatWayList::deleteAll(['tag_pull_id' => $sending->id]);
					foreach ($data['chat_list'] as $key => $list) {
						$chat              = new WorkChatWayList();
						$chat->tag_pull_id = $sending->id;
						$chat->create_time = DateUtil::getCurrentTime();
						$chat->chat_id     = $list['chat_id'];
						$chat->media_id    = $list['media_id'];
						$chat->local_path  = $list['local_path'];
						$chat->sort        = $key;
						if (!$chat->validate() || !$chat->save()) {
							throw new InvalidDataException("创建失败" . SUtils::modelError($chat));
						}
						array_push($chatId, $list['chat_id']);
					}
					$totalNum  = 200 * (count($data['chat_list']));
					$chatCount = WorkChatInfo::find()->where(['chat_id' => $chatId, 'status' => 1])->count();
					$totalNum  = $totalNum - $chatCount;
					if ($willNum > $totalNum) {
						throw new InvalidDataException("当前群码不能容纳所添加的客户数，请继续添加群码");
					}
				}

				$transaction->commit();

				return $sending->id;
			} catch (\Exception $e) {
				$transaction->rollBack();
				\Yii::error($e->getMessage(), 'tag_pull');
				throw new InvalidDataException("创建失败：" . $e->getMessage());
			}

		}

	}
