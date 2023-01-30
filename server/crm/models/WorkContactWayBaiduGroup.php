<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_contact_way_baidu_group}}".
	 *
	 * @property int                      $id
	 * @property int                      $uid          用户ID
	 * @property int                      $corp_id      企业ID
	 * @property int                      $parent_id    分组父级ID
	 * @property string                   $title        分组名称
	 * @property int                      $status       1可用 0不可用
	 * @property string                   $update_time  修改时间
	 * @property string                   $create_time  创建时间
	 * @property int                      $sort         分组排序
	 * @property int                      $is_not_group 0已分组、1未分组
	 *
	 * @property WorkCorp                 $corp
	 * @property WorkContactWayBaiduGroup $parent
	 * @property User                     $u
	 */
	class WorkContactWayBaiduGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_baidu_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'parent_id', 'status', 'sort', 'is_not_group'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['title'], 'string', 'max' => 32],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayGroup::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
				'parent_id'    => Yii::t('app', '父级ID'),
				'title'        => Yii::t('app', '分组名称'),
				'status'       => Yii::t('app', '1可用 0不可用'),
				'update_time'  => Yii::t('app', '修改时间'),
				'create_time'  => Yii::t('app', '创建时间'),
				'sort'         => Yii::t('app', '分组排序'),
				'is_not_group' => Yii::t('is_not_group', '0已分组、1未分组'),
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
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getParent ()
		{
			return $this->hasOne(WorkContactWayBaiduGroup::className(), ['id' => 'parent_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
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
				$group               = new WorkContactWayBaiduGroup();
				$group->uid          = $uid;
				$group->corp_id      = $corp_id;
				$group->title        = '未分组';
				$group->sort         = 1;
				$group->is_not_group = 1;
				$group->create_time  = DateUtil::getCurrentTime();
				if ($group->validate() && $group->save()) {
					if (!empty($is_update)) {
						WorkContactWayBaidu::updateAll(['way_group_id' => $group->id], ['corp_id' => $corp_id, 'is_del' => 0, 'way_group_id' => NULL]);
					}
				}
			}

			return $group;
		}
	}
