<?php

	namespace app\models;

	use app\queue\SyncPublicReclaimJob;
	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_claim_user}}".
	 *
	 * @property int      $id
	 * @property int      $uid                   账户ID
	 * @property int      $sea_id                公海客户ID
	 * @property int      $corp_id               授权的企业ID
	 * @property int      $external_userid       企微外部联系人id
	 * @property int      $old_user_id           原归属成员id
	 * @property int      $old_follow_user_id    原外部联系人添加信息表id
	 * @property int      $new_user_id           认领成员id
	 * @property int      $new_follow_user_id    认领成员外部联系人添加信息表id
	 * @property string   $reclaim_rule          回收条件
	 * @property int      $reclaim_time          回收时间
	 * @property int      $status                添加状态：0未添加、1已添加
	 * @property int      $claim_str             成员添加后的轨迹
	 * @property int      $add_time              添加时间
	 *
	 * @property WorkCorp $corp
	 */
	class PublicSeaClaimUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_claim_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'external_userid', 'old_user_id', 'old_follow_user_id', 'new_user_id', 'new_follow_user_id', 'reclaim_time', 'status'], 'integer'],
				[['reclaim_rule'], 'string', 'max' => 64],
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
				'uid'                => Yii::t('app', '账户ID'),
				'sea_id'             => Yii::t('app', '公海客户ID'),
				'corp_id'            => Yii::t('app', '企微外部联系人id'),
				'external_userid'    => Yii::t('app', '企微外部联系人id'),
				'old_user_id'        => Yii::t('app', '原归属成员id'),
				'old_follow_user_id' => Yii::t('app', '原外部联系人添加信息表id'),
				'new_user_id'        => Yii::t('app', '认领成员id'),
				'new_follow_user_id' => Yii::t('app', '认领成员外部联系人添加信息表id'),
				'reclaim_rule'       => Yii::t('app', '回收条件'),
				'reclaim_time'       => Yii::t('app', '回收时间'),
				'status'             => Yii::t('app', '添加状态：0未添加、1已添加'),
				'claim_str'          => Yii::t('app', '成员添加后的轨迹'),
				'add_time'           => Yii::t('app', '添加时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		public function dumpData ()
		{
			$result                   = [
				'id'              => $this->id,
				'sea_id'          => $this->sea_id,
				'external_userid' => $this->external_userid,
				'reclaim_rule'    => $this->reclaim_rule,
			];
			$claimTip                 = '';
			$isDisplay                = 1;
			$contactInfo              = WorkExternalContact::findOne($this->external_userid);
			$result['name']           = !empty($contactInfo->name) ? $contactInfo->name : '';
			$result['avatar']         = !empty($contactInfo->avatar) ? $contactInfo->avatar : '';
			$result['corp_name']      = $contactInfo->corp_name;
			$result['externaluserid'] = !empty($contactInfo->external_userid) ? $contactInfo->external_userid : '';

			$oldWorkUser             = WorkUser::findOne($this->old_user_id);
			$departName              = WorkDepartment::getDepartNameByUserId($oldWorkUser->department, $oldWorkUser->corp_id);
			$result['userid']        = $oldWorkUser->userid;
			if (!empty($departName)) {
				$result['old_user_name'] = $oldWorkUser->name . '（' . $departName . '）';
			} else {
				$result['old_user_name'] = $oldWorkUser->name;
			}

			$workUser            = WorkUser::findOne($this->new_user_id);
			$result['user_name'] = !empty($workUser->name) ? $workUser->name : '';

			$result['add_time']     = date('Y-m-d H:i', $this->add_time);
			$result['reclaim_time'] = date('Y-m-d H:i', $this->reclaim_time);

			if ($this->status == 1) {
				$status     = '已添加';
				$followUser = WorkExternalContactFollowUser::findOne($this->new_follow_user_id);
				if (!empty($workUser)) {
					$result['userid'] = $workUser->userid;
				}
				if (!empty($followUser)) {
					if ($followUser->del_type == 1) {
						$isDisplay = 0;
					} elseif ($followUser->del_type == 2) {
						$isDisplay = 0;
					} elseif ($workUser->is_del == 1) {
						$isDisplay = 0;
					}
					$addWay = PublicSeaCustomer::getAddWay($followUser);
				} else {
					$isDisplay = 0;
				}
			} else {
				//提醒
				$followUser = WorkExternalContactFollowUser::findOne($this->old_follow_user_id);
				if (!empty($followUser)) {
					if ($followUser->del_type == 1) {
						$claimTip  = '员工已将客户删除，提醒无效';
						$isDisplay = 0;
					} elseif ($followUser->del_type == 2) {
						$claimTip  = '客户已将员工删除，提醒无效';
						$isDisplay = 0;
					} elseif ($oldWorkUser->is_del == 1) {
						$claimTip  = '原归属员工已离职，提醒无效';
						$isDisplay = 0;
					}
				} else {
					$isDisplay = 0;
				}

				if ($this->status == 2) {
					$status = '已拒绝';
				} else {
					$status = '未添加';
				}
			}
			$result['status']         = $status;
			$result['isDisplay']      = $isDisplay;
			$result['claimTip']       = !empty($claimTip) ? $claimTip : '';
			$result['add_other_info'] = !empty($addWay) ? $addWay['add_other_info'] : '';
			$result['add_way_info']   = !empty($addWay) ? $addWay['add_way_info'] : '';
			$result['add_way_title']  = !empty($addWay) ? $addWay['add_way_title'] : '';
			$result['follow_user_id'] = !empty($this->new_follow_user_id) ? $this->new_follow_user_id : $this->old_follow_user_id;

			$fieldInfo        = CustomField::findOne(['uid' => 0, 'key' => 'sex']);
			$fieldValue       = CustomFieldValue::findOne(['type' => 1, 'cid' => $this->external_userid, 'fieldid' => $fieldInfo->id]);
			$result['gender'] = !empty($fieldValue) ? $fieldValue->value : '未知';

			return $result;
		}

		//进队列,分配成员24小时后查询,若还没添加则把状态置为拒绝
		public static function updateStatusJob ($claimUserId)
		{
			$second = 86400 + 300;
			\Yii::$app->queue->delay($second)->push(new SyncPublicReclaimJob([
				'claim_user_id' => $claimUserId
			]));
		}

		//分配成员24小时后查询,若还没添加则把状态置为拒绝
		public static function updateStatus ($claimUserId)
		{
			$claimUser = static::findOne($claimUserId);
			if (!empty($claimUser) && ($claimUser->status == 0)) {
				$claimUser->status = 2;
				$claimUser->update();
			}
		}

		//更新老数据
		public static function otherWayBatch ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$cacheKey    = 'other_way_batch';
			$cacheChatId = \Yii::$app->cache->get($cacheKey);
			$id          = !empty($cacheChatId) ? $cacheChatId : 0;
			$page        = 1;
			$pageSize    = 1000;
			while (true) {
				$offset    = ($page - 1) * $pageSize;
				$claimList = PublicSeaClaimUser::find()->where(['>', 'id', $id])->andWhere(['status' => 1])->limit($pageSize)->offset($offset)->select('id,new_follow_user_id')->all();
				if (empty($claimList)) {
					break;
				}
				/**@var PublicSeaClaimUser $claim * */
				foreach ($claimList as $claim) {
					$claimId = $claim->id;
					if (!empty($claim->new_follow_user_id)) {
						$followUser = WorkExternalContactFollowUser::findOne($claim->new_follow_user_id);
						if (!empty($followUser)) {
							$followUser->other_way = 1;
							$followUser->update();
						}
					}
					\Yii::$app->cache->set($cacheKey, $claimId, 7200);
				}
				$page++;
			}
		}
	}
