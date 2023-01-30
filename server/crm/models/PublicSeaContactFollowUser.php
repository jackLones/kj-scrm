<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_contact_follow_user}}".
	 *
	 * @property int               $id
	 * @property int               $corp_id           授权的企业ID
	 * @property int               $sea_id            公海客户ID
	 * @property int               $user_id           成员ID
	 * @property int               $follow_id         状态id
	 * @property int               $last_follow_time  最后一次跟进状态时间
	 * @property int               $is_chat           沟通状态：0一直未沟通、1已沟通
	 * @property int               $follow_num        跟进次数
	 * @property int               $close_rate        预计成交率
	 * @property string            $description       设置的用户描述
	 * @property string            $company_name      公司名称
	 * @property string            $is_reclaim        是否已回收：0否、1是
	 * @property string            $is_protect        是否客户保护：0否、1是
	 * @property int               $add_time          添加时间
	 * @property int               $update_time       修改时间
	 * @property int               $follow_user_id    绑定的企微客户关系表id
	 *
	 * @property PublicSeaCustomer $sea
	 * @property WorkUser          $user
	 */
	class PublicSeaContactFollowUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_contact_follow_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['sea_id', 'user_id', 'follow_id', 'last_follow_time', 'close_rate', 'add_time', 'update_time'], 'integer'],
				[['description'], 'string', 'max' => 255],
				[['sea_id'], 'exist', 'skipOnError' => true, 'targetClass' => PublicSeaCustomer::className(), 'targetAttribute' => ['sea_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'corp_id'          => Yii::t('app', '授权的企业ID'),
				'sea_id'           => Yii::t('app', '公海客户ID'),
				'user_id'          => Yii::t('app', '成员ID'),
				'follow_id'        => Yii::t('app', '状态id'),
				'last_follow_time' => Yii::t('app', '最后一次跟进状态时间'),
				'is_chat'          => Yii::t('app', '沟通状态：0一直未沟通、1已沟通'),
				'follow_num'       => Yii::t('app', '跟进次数'),
				'close_rate'       => Yii::t('app', '预计成交率'),
				'description'      => Yii::t('app', '设置的用户描述'),
				'company_name'     => Yii::t('app', '公司名称'),
				'is_reclaim'       => Yii::t('app', '是否已回收：0否、1是'),
				'is_protect'       => Yii::t('app', '是否客户保护：0否、1是'),
				'add_time'         => Yii::t('app', '添加时间'),
				'update_time'      => Yii::t('app', '修改时间'),
				'follow_user_id'   => Yii::t('app', '绑定的企微客户关系表id'),
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
		public function getSea ()
		{
			return $this->hasOne(PublicSeaCustomer::className(), ['id' => 'sea_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		//生成查询条件
		public static function getCondition ($followUserData, $whereData = [])
		{
			$userIds   = !empty($whereData['user_ids']) ? $whereData['user_ids'] : '';
			$name      = !empty($whereData['name']) ? $whereData['name'] : '';
			$sex       = !empty($whereData['sex']) ? $whereData['sex'] : '-1';
			$followId  = !empty($whereData['follow_id']) ? $whereData['follow_id'] : '-1';
			$tagIds    = !empty($whereData['tag_ids']) ? $whereData['tag_ids'] : '';
			$type      = !empty($whereData['type']) ? $whereData['type'] : '';
			$startTime = !empty($whereData['start_time']) ? $whereData['start_time'] : '';
			$endTime   = !empty($whereData['end_time']) ? $whereData['end_time'] : '';
			$statusId  = !empty($whereData['status_id']) ? $whereData['status_id'] : '';
			$isProtect = isset($whereData['is_protect']) ? $whereData['is_protect'] : '-1';

			if (!empty($userIds)) {
				$followUserData = $followUserData->andWhere(['fu.user_id' => $userIds]);
			}

			if (!empty($name)) {
				$followUserData = $followUserData->leftJoin('{{%custom_field_value}} cf', '`cf`.`cid` = `sc`.`id` AND `cf`.`type`=4');
				$emailInfo      = CustomField::findOne(['uid' => 0, 'key' => 'email']);
				$followUserData = $followUserData->andWhere(' sc.name like \'%' . $name . '%\' or sc.wx_num like \'%' . $name . '%\'  or sc.phone like \'%' . $name . '%\' or sc.qq like \'%' . $name . '%\' or (cf.fieldid =' . $emailInfo->id . ' and cf.value like \'%' . $name . '%\')');
			}

			if ($sex != '-1') {
				$sexInfo = CustomField::findOne(['uid' => 0, 'key' => 'sex']);
				if (empty($name)) {
					$followUserData = $followUserData->leftJoin('{{%custom_field_value}} cf', '`cf`.`cid` = `sc`.`id` AND `cf`.`type`=4');
				}
				if ($sex == 1) {
					$sex = '男';
				} elseif ($sex == 2) {
					$sex = '女';
				} else {
					$sex = '未知';
				}
				$followUserData = $followUserData->andWhere(['cf.fieldid' => $sexInfo->id, 'value' => $sex]);
			}

			if ($followId != '-1') {
				$followUserData = $followUserData->andWhere(['fu.follow_id' => $followId]);
			}

			if ($isProtect != '-1') {
				$followUserData = $followUserData->andWhere(['fu.is_protect' => $isProtect]);
			}

			if (!empty($tagIds)) {
				if (!is_array($tagIds)) {
					$tagIds = explode(',', $tagIds);
				}
				$followUserData = $followUserData->leftJoin('{{%public_sea_tag}} st', '`st`.`follow_user_id` = `fu`.`id`')->andWhere(['st.tag_id' => $tagIds, 'st.status' => 1]);
			}

			if (!empty($type)) {
				$sTime = '';
				$eTime = '';
				switch ($type) {
					case 1:
						$sTime = strtotime(date('Y-m-d'));
						$eTime = strtotime(date('Y-m-d') . ' 23:59:59');
						break;
					case 2:
						$sDefaultDate = date("Y-m-d");
						$w            = date('w', strtotime($sDefaultDate));
						$weekStart    = date('Y-m-d', strtotime("$sDefaultDate -" . ($w ? $w - 1 : 6) . ' days'));
						$weekEnd      = date('Y-m-d', strtotime("$weekStart +6 days"));
						$sTime        = strtotime($weekStart);
						$eTime        = strtotime($weekEnd . ' 23:59:59');
						break;
					case 3:
						$firstDay = date('Y-m-01', strtotime(date("Y-m-d")));
						$lastDay  = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
						$sTime    = strtotime($firstDay);
						$eTime    = strtotime($lastDay . ' 23:59:59');
						break;
				}
				if (!empty($sTime) && !empty($eTime)) {
					$followUserData = $followUserData->andWhere(['between', 'fu.add_time', $sTime, $eTime]);
				}
			}

			if ($type == 4 && (!empty($startTime) || !empty($endTime))) {
				if (!empty($startTime) && !empty($endTime)) {
					$followUserData = $followUserData->andWhere(['between', 'fu.add_time', strtotime($startTime), strtotime($endTime . ':59')]);
				} elseif (!empty($startTime)) {
					$followUserData = $followUserData->andWhere(['>', 'fu.add_time', strtotime($startTime)]);
				} else {
					$followUserData = $followUserData->andWhere(['<', 'fu.add_time', strtotime($endTime . ':59')]);
				}
			}

			$eDate = date('Y-m-d');
			if ($statusId == WorkExternalContactFollowRecord::ALL_DAY) {
				$followUserData = $followUserData->andWhere('fu.last_follow_time = fu.add_time');
			}
			if ($statusId == WorkExternalContactFollowRecord::ONE_DAY) {
				$time2          = strtotime($eDate) - (24 * 3600 * 1);
				$followUserData = $followUserData->andWhere(['or', ['<', 'fu.last_follow_time', $time2], 'fu.last_follow_time = fu.add_time']);
			}
			if ($statusId == WorkExternalContactFollowRecord::THREE_DAY) {
				$time2          = strtotime($eDate) - (24 * 3600 * 3);
				$followUserData = $followUserData->andWhere(['or', ['<', 'fu.last_follow_time', $time2], 'fu.last_follow_time = fu.add_time']);
			}
			if ($statusId > 0) {
				$day            = WorkNotFollowDay::findOne($statusId);
				$time2          = strtotime($eDate) - (24 * 3600 * $day->day);
				$followUserData = $followUserData->andWhere(['or', ['<', 'fu.last_follow_time', $time2], 'fu.last_follow_time = fu.add_time']);
			}

			return $followUserData;
		}
	}
