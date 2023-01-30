<?php

	namespace app\models;

	use Yii;
	use app\util\DateUtil;

	/**
	 * This is the model class for table "{{%work_chat_contact_way_group}}".
	 *
	 * @property int                       $id
	 * @property int                       $uid          用户ID
	 * @property int                       $corp_id      企业ID
	 * @property int                       $parent_id    分组父级ID
	 * @property string                    $title        分组名称
	 * @property int                       $status       1可用 0不可用
	 * @property string                    $update_time  修改时间
	 * @property string                    $create_time  创建时间
	 * @property int                       $sort         分组排序
	 * @property int                       $is_not_group 0已分组、1未分组
	 *
	 * @property WorkChatContactWay[]      $workChatContactWays
	 * @property WorkCorp                  $corp
	 * @property WorkChatContactWayGroup   $parent
	 * @property WorkChatContactWayGroup[] $workChatContactWayGroups
	 * @property User                      $u
	 */
	class WorkChatContactWayGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_contact_way_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'status', 'sort', 'is_not_group'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['title'], 'string', 'max' => 32],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChatContactWayGroup::className(), 'targetAttribute' => ['parent_id' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'uid'          => Yii::t('app', '用户ID'),
				'corp_id'      => Yii::t('app', '企业ID'),
				'parent_id'    => Yii::t('app', '分组父级ID'),
				'title'        => Yii::t('app', '分组名称'),
				'status'       => Yii::t('app', '1可用 0不可用'),
				'update_time'  => Yii::t('app', '修改时间'),
				'create_time'  => Yii::t('app', '创建时间'),
				'sort'         => Yii::t('app', '分组排序'),
				'is_not_group' => Yii::t('app', '0已分组、1未分组'),
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
		public function getWorkChatContactWays ()
		{
			return $this->hasMany(WorkChatContactWay::className(), ['way_group_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getParent ()
		{
			return $this->hasOne(WorkChatContactWayGroup::className(), ['id' => 'parent_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatContactWayGroups ()
		{
			return $this->hasMany(WorkChatContactWayGroup::className(), ['parent_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getGroups ()
		{
			return $this->hasMany(static::className(), ['parent_id' => 'id'])->where(['status' => 1])->orderBy('sort DESC');
		}

		/**
		 * 单条数据
		 * @return array
		 */
		public function dumpData ()
		{
			$data = [
				'id'           => (string) $this->id,
				'key'          => (string) $this->id,
				'value'        => (string) $this->id,
				'uid'          => $this->uid,
				'corp_id'      => $this->corp_id,
				'parent_id'    => $this->parent_id,
				'title'        => $this->title,
				'status'       => $this->status,
				'sort'         => $this->sort,
				'is_not_group' => $this->is_not_group,
				'scopedSlots'  => ['title' => 'custom'],//前端需要
				'num'          => "0",
			];

			return $data;
		}

		//更新渠道活码未分组数据
		public static function updateNotGroup ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$userCorpRelation = UserCorpRelation::find()->all();
			if (!empty($userCorpRelation)) {
				/**
				 * @var UserCorpRelation $relation
				 */
				foreach ($userCorpRelation as $relation) {
					static::setNoGroup($relation->uid, $relation->corp_id, 1);
				}
			}
		}

		/**
		 * 设置未分组
		 * $uid 用户id
		 * $corp_id 企业微信id
		 * $is_update 是否更新渠道活码
		 */
		public static function setNoGroup ($uid, $corp_id, $is_update = 0)
		{
			$group = static::findOne(['uid' => $uid, 'corp_id' => $corp_id, 'is_not_group' => 1]);
			if (empty($group)) {
				$group               = new WorkChatContactWayGroup();
				$group->uid          = $uid;
				$group->corp_id      = $corp_id;
				$group->title        = '未分组';
				$group->sort         = 1;
				$group->is_not_group = 1;
				$group->create_time  = DateUtil::getCurrentTime();
				if ($group->validate() && $group->save()) {
					if (!empty($is_update)) {
						WorkChatContactWay::updateAll(['way_group_id' => $group->id], ['corp_id' => $corp_id, 'is_del' => 0, 'way_group_id' => NULL]);
					}
				}
			}

			return $group;
		}

		//获取分组列表
		public static function getGroupData ($uid, $corp_id, $withSubGroup = true, $withNum = true)
		{
			$groupList    = [];
			$topGroupData = static::find()->where(['uid' => $uid, 'corp_id' => $corp_id, 'status' => 1, 'parent_id' => NULL]);
			$topGroupData = $topGroupData->orderBy('sort DESC')->all();
			if (!empty($topGroupData)) {
				foreach ($topGroupData as $groupData) {
					$groupInfo = $groupData->dumpData();
					//是否显示子分组
					if ($withSubGroup) {
						$children              = static::getSubGroupList($corp_id, $groupData->id, $withSubGroup, $withNum);
						$groupInfo['children'] = $children;
						//是否查询分组下的个数
						if ($withNum) {
							$workContactWay   = WorkChatContactWay::find()->where(['corp_id' => $corp_id, 'way_group_id' => $groupData->id]);
							$num              = $workContactWay->count();
							$groupInfo['num'] = (string) $num;
							if (!empty($children)) {
								foreach ($children as $child) {
									$groupInfo['num'] += $child['num'];
									$groupInfo['num'] = (string) $groupInfo['num'];
								}
							}
						}
					}
					array_push($groupList, $groupInfo);
				}
			} else {
				$groupData = static::setNoGroup($uid, $corp_id, 1);
				$groupInfo = $groupData->dumpData();
				array_push($groupList, $groupInfo);
			}

			return $groupList;
		}

		//根据父级ID获取子分组的列表
		public static function getSubGroupList ($corp_id, $parentId, $withSubGroup = true, $withNum = true)
		{
			$groupList = [];

			$groupData    = static::findOne($parentId);
			$subGroupList = $groupData->groups;
			if (!empty($subGroupList)) {
				foreach ($subGroupList as $subGroup) {
					$subGroupData = $subGroup->dumpData();
					if ($withSubGroup) {
						$children                 = static::getSubGroupList($corp_id, $subGroup->id, $withSubGroup, $withNum);
						$subGroupData['children'] = $children;
						if ($withNum) {
							$workContactWay      = WorkChatContactWay::find()->where(['corp_id' => $corp_id, 'way_group_id' => $subGroup->id]);
							$num                 = $workContactWay->count();
							$subGroupData['num'] = (string) $num;
							if (!empty($children)) {
								foreach ($children as $child) {
									$subGroupData['num'] += $child['num'];
									$subGroupData['num'] = (string) $subGroupData['num'];
								}
							}
						}
					}
					array_push($groupList, $subGroupData);
				}
			}

			return $groupList;
		}

		//根据父级id获取子id
		public static function getSubGroupId ($group_id)
		{
			$groupIdList = [$group_id];
			$groupList   = static::find()->where(['parent_id' => $group_id, 'status' => 1])->select('id')->all();
			if (!empty($groupList)) {
				foreach ($groupList as $group) {
					$idData      = static::getSubGroupId($group->id);
					$groupIdList = array_merge($groupIdList, $idData);
				}
			}

			return $groupIdList;
		}

	}
