<?php

	namespace app\models;

	use app\components\InvalidParameterException;
	use Yii;
	use yii\db\Exception;

	/**
	 * This is the model class for table "{{%work_task_tag}}".
	 *
	 * @property int                    $id
	 * @property int                    $corp_id     授权的企业ID
	 * @property int                    $tag_id      标签id
	 * @property string                 $tagname     标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名
	 * @property string                 $condition   筛选条件
	 * @property string                 $create_time 创建时间
	 * @property string                 $update_time 更新时间
	 *
	 * @property WorkCorp               $corp
	 * @property WorkTag                $tag
	 * @property WorkTaskTagRelations[] $workTaskTagRelations
	 */
	class WorkTaskTag extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_task_tag}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'tag_id'], 'integer'],
				[['condition'], 'string'],
				[['create_time', 'update_time'], 'safe'],
				[['tagname'], 'string', 'max' => 32],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::class, 'targetAttribute' => ['corp_id' => 'id']],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::class, 'targetAttribute' => ['tag_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '授权的企业ID'),
				'tag_id'      => Yii::t('app', '标签id'),
				'tagname'     => Yii::t('app', '标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名'),
				'condition'   => Yii::t('app', '筛选条件'),
				'create_time' => Yii::t('app', '创建时间'),
				'update_time' => Yii::t('app', '更新时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::class, ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTag ()
		{
			return $this->hasOne(WorkTag::class, ['id' => 'tag_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTaskTagRelations ()
		{
			return $this->hasMany(WorkTaskTagRelations::class, ['work_tag_id' => 'id']);
		}

		public function dumpData ($task_id = 0, $show_schedule = 0)
		{
			$result = [
				'id'          => $this->id,
				'task_tag_id' => $this->id,
				'tag_id'      => $this->tag_id,
				'tagname'     => $this->tagname,
				'condition'   => !empty($this->condition) ? json_decode($this->condition, true) : [],
				'create_time' => $this->create_time,
				'update_time' => $this->update_time
			];

			if ($this->tag->tagname !== $this->tagname) {
				$result['tagname'] = $this->tagname = $this->tag->tagname;
				$this->save();
			}

			return $result;
		}

		/**
		 * 获取符合规则的联系人
		 * File: models/WorkTaskTag.php
		 * Class: WorkTaskTag
		 * Function: getConformFollowUsers
		 *
		 * Author: BeenLee
		 * Time: 2021/1/13 4:40 下午
		 *
		 * @param     $uid
		 * @param int $external_id
		 *
		 * @return array|mixed
		 */
		public function getConformFollowUsers ($uid, $external_id = -1)
		{
			$conformFollowUsers = [];
			$workTaskTagInfo    = $this->dumpData();

			//符合条件的人
			if (!empty($workTaskTagInfo['condition'])) {
				//根据条件获取外部联系人 external_id
				$name        = isset($workTaskTagInfo['condition']['name']) ? trim($workTaskTagInfo['condition']['name']) : '';
				$phone       = isset($workTaskTagInfo['condition']['phone']) ? trim($workTaskTagInfo['condition']['phone']) : '';
				$group_id    = isset($workTaskTagInfo['condition']['group_id']) ? trim($workTaskTagInfo['condition']['group_id']) : '';
				$tag_type    = isset($workTaskTagInfo['condition']['tag_type']) ? (int) $workTaskTagInfo['condition']['tag_type'] : 1;
				$belong_id   = isset($workTaskTagInfo['condition']['belong_id']) ? (int) $workTaskTagInfo['condition']['belong_id'] : 0;
				$start_time  = isset($workTaskTagInfo['condition']['start_time']) ? $workTaskTagInfo['condition']['start_time'] : NULL;
				$end_time    = isset($workTaskTagInfo['condition']['end_time']) ? $workTaskTagInfo['condition']['end_time'] : NULL;
				$correctness = isset($workTaskTagInfo['condition']['correctness']) ? (int) $workTaskTagInfo['condition']['correctness'] : 2;
				$update_time = isset($workTaskTagInfo['condition']['update_time']) ? $workTaskTagInfo['condition']['update_time'] : NULL;
				$follow_num1 = isset($workTaskTagInfo['condition']['follow_num1']) ? $workTaskTagInfo['condition']['follow_num1'] : NULL;
				$follow_num2 = isset($workTaskTagInfo['condition']['follow_num2']) ? $workTaskTagInfo['condition']['follow_num2'] : NULL;
				$chat_time   = isset($workTaskTagInfo['condition']['chat_time']) ? $workTaskTagInfo['condition']['chat_time'] : NULL;
				$chat_id     = isset($workTaskTagInfo['condition']['chat_id']) ? $workTaskTagInfo['condition']['chat_id'] : NULL;
				$is_moment   = isset($workTaskTagInfo['condition']['is_moment']) ? (int) $workTaskTagInfo['condition']['is_moment'] : 0;
				$user_ids    = isset($workTaskTagInfo['condition']['user_ids']) ? $workTaskTagInfo['condition']['user_ids'] : NULL;
				$sex         = isset($workTaskTagInfo['condition']['sex']) ? (int) $workTaskTagInfo['condition']['sex'] : -1;
				$work        = isset($workTaskTagInfo['condition']['work']) ? $workTaskTagInfo['condition']['work'] : NULL;
				$location    = isset($workTaskTagInfo['condition']['location']) ? $workTaskTagInfo['condition']['location'] : NULL;
				$sign_id     = isset($workTaskTagInfo['condition']['signId']) ? $workTaskTagInfo['condition']['signId'] : NULL;
				$follow_id   = isset($workTaskTagInfo['condition']['follow_status']) ? (int) $workTaskTagInfo['condition']['follow_status'] : -1;
				$is_fans     = isset($workTaskTagInfo['condition']['isPublic']) ? $workTaskTagInfo['condition']['isPublic'] : NULL;
				$tag_ids     = isset($workTaskTagInfo['condition']['tag_arr']) ? $workTaskTagInfo['condition']['tag_arr'] : [];

				if ($location && is_array($location) && count($location) === 2) {
					[$province, $city] = $location;
					if ($city === '全部') {
						$city = '';
					}
				} else {
					$province = '';
					$city     = '';
				}

				$fieldData = [];
				$postDate  = $workTaskTagInfo['condition'];
				if (!empty($postDate) && is_array($postDate)) {
					$postKey   = array_keys($postDate);
					$fieldInfo = CustomField::findAll($postKey);
					if ($fieldInfo) {
						foreach ($fieldInfo as $field) {
							if ($field['key'] === 'work') {
								$work = $postDate[$field['id']];
							} else /*if (is_array($postDate[$field['id']])) {
							foreach ($postDate[$field['id']] as $fieldDate) {
								$fieldData[] = [
									'field' => $field['id'],
									'match' => $fieldDate,
								];
							}
						} else */ {
								if (!empty($postDate[$field['id']])) {
									$fieldData[] = [
										'field' => $field['id'],
										'match' => $postDate[$field['id']],
									];
								}
							}
						}
					}
				}

				if (!empty($user_ids)) {
					$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
					$user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp_id, $Temp["department"], $Temp["user"], 1, true);
					$user_ids = empty($user_ids) ? [0] : $user_ids;
				}

				$data['isMasterAccount'] = 1;
				$data['uid']             = $uid;
				$data['sub_id']          = $uid;
				$data['name']            = $name;
				$data['phone']           = $phone;
				$data['sex']             = $sex;
				$data['work']            = $work;
				$data['province']        = $province;
				$data['city']            = $city;
				$data['follow_status']   = -1;
				$data['follow_id']       = $follow_id;
				$data['fieldData']       = $fieldData;
				$data['tag_ids']         = implode(',', $tag_ids);
				$data['group_id']        = $group_id;
				$data['tag_type']        = $tag_type;
				$data['start_time']      = $start_time;
				$data['end_time']        = $end_time;
				$data['correctness']     = $correctness;
				$data['update_time']     = $update_time;
				$data['follow_num1']     = $follow_num1;
				$data['follow_num2']     = $follow_num2;
				$data['chat_time']       = $chat_time;
				$data['sign_id']         = $sign_id;
				$data['chat_id']         = $chat_id;
				$data['user_ids']        = $user_ids;
				$data['belong_id']       = $belong_id;
				$data['is_fans']         = $is_fans;
				$data['is_moment']       = $is_moment;
				$data['is_follow_full']  = 1;
				if ($external_id >= 0) {
					$data['external_id'] = $external_id;
				}

				try {
					$result             = WorkExternalContactFollowUser::getData($data);
					$conformFollowUsers = $result['result'];
				} catch (InvalidParameterException | Exception $e) {
					$conformFollowUsers = [];
				}
			}

			return $conformFollowUsers;
		}

		/**
		 * 根据规则获取用户标签
		 * File: models/WorkTaskTag.php
		 * Class: WorkTaskTag
		 * Function: getUserTagByRule
		 *
		 * Author: BeenLee
		 * Time: 2021/1/11 10:07 上午
		 *
		 * @param $uid
		 * @param $type //操作类型 0-创建规则，1-修改规则，2-修改标签，3-删除标签
		 *
		 * @return array
		 */
		public function getUserTagByRule ($uid, $type = 0)
		{
			$conformFollowUsers = [];
			$hasTagFollowUsers  = [];
			$doNotDeal          = [];
			$needTagFollowUsers = [];
			$notNeedFollowUsers = [];

			if (in_array($type, [0, 1], true)) {
				$conformFollowUsers = $this->getConformFollowUsers($uid);
			}

			if (in_array($type, [1, 2, 3], true)) {
				//已有标签的人
				$hasTagFollowUsers = WorkTagFollowUser::find()->where(['corp_id' => $this->corp_id, 'tag_id' => $this->tag_id])->select('follow_user_id')->asArray()->all();
			}

			//操作类型 0-创建规则，1-修改规则，2-修改标签，3-删除标签
			if ($type === 0) {
				//只打标签
				$needTagFollowUsers = $conformFollowUsers;
			} elseif ($type === 1) {
				//无需处理的人
				$doNotDeal = array_intersect($conformFollowUsers, $hasTagFollowUsers);

				//需要打标签的人
				$needTagFollowUsers = array_diff($conformFollowUsers, $doNotDeal);

				//需要取消标签的人
				$notNeedFollowUsers = array_diff($hasTagFollowUsers, $doNotDeal);
			} elseif ($type === 2) {
				//全部改标签
				//$needTagFollowUsers = $hasTagFollowUsers;
			} elseif ($type === 3) {
				//只取消标签
				$notNeedFollowUsers = $hasTagFollowUsers;
			}

			return [
				'hasTagFollowUsers'  => $hasTagFollowUsers,
				'needTagFollowUsers' => $needTagFollowUsers,
				'notNeedFollowUsers' => $notNeedFollowUsers,
			];
		}

		/**
		 * 通过用户获取用户标签变更
		 * File: models/WorkTaskTag.php
		 * Class: WorkTaskTag
		 * Function: getTagByUser
		 *
		 * Author: BeenLee
		 * Time: 2021/1/12 4:20 下午
		 *
		 * @param     $external_id
		 * @param     $uid
		 * @param int $type 4-新增用户，5-属性变更
		 *
		 * @return array[]
		 */
		public function getTagByUser ($external_id, $uid, $type = 4)
		{
			$conformFollowUsers = [];
			$hasTagFollowUsers  = [];
			$doNotDeal          = [];
			$needTagFollowUsers = [];
			$notNeedFollowUsers = [];

			//是否符合此规则的用户
			$conformFollowUsers = $this->getConformFollowUsers($uid, $external_id);

			//已经有此标签的用户
			$hasTagFollowUsers = WorkTagFollowUser::find()->alias('fu')->leftJoin('{{%work_external_contact_follow_user}} we', 'we.id=fu.follow_user_id')
				->andWhere(['we.external_userid' => $external_id, 'fu.tag_id' => $this->tag_id])->asArray()->all();

			if ($type === 4) {
				//新增用户 只打标签
				$needTagFollowUsers = $conformFollowUsers;
			} elseif ($type === 5) {
				//无需处理的人
				$doNotDeal = array_intersect($conformFollowUsers, $hasTagFollowUsers);

				//需要打标签的人
				$needTagFollowUsers = array_diff($conformFollowUsers, $doNotDeal);

				//需要取消标签的人
				$notNeedFollowUsers = array_diff($hasTagFollowUsers, $doNotDeal);
			}

			return [
				'hasTagFollowUsers'  => $hasTagFollowUsers,
				'needTagFollowUsers' => $needTagFollowUsers,
				'notNeedFollowUsers' => $notNeedFollowUsers,
			];
		}

	}

