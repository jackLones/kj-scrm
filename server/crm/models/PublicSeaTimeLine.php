<?php

	namespace app\models;

	use app\util\DateUtil;
    use Yii;

	/**
	 * This is the model class for table "{{%public_sea_time_line}}".
	 *
	 * @property int    $id
	 * @property int    $uid         商户id
	 * @property int    $sea_id      公海客户ID
	 * @property int    $sub_id      子账户ID
	 * @property int    $user_id     成员ID
	 * @property string $event       行为，类别见model
	 * @property int    $event_time  行为时间
	 * @property int    $event_id    行为事件id
	 * @property int    $related_id  相关表id
	 * @property string $remark      行为相关备注
	 * @property string $is_sync     是否已同步：0否、1是
	 */
	class PublicSeaTimeLine extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_time_line}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sea_id', 'sub_id', 'user_id', 'event_time', 'event_id', 'related_id'], 'integer'],
				[['event'], 'string', 'max' => 32],
				[['remark'], 'string', 'max' => 500],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => Yii::t('app', 'ID'),
				'uid'        => Yii::t('app', '商户id'),
				'sea_id'     => Yii::t('app', '公海客户ID'),
				'sub_id'     => Yii::t('app', '子账户ID'),
				'user_id'    => Yii::t('app', '成员ID'),
				'event'      => Yii::t('app', '行为，类别见model'),
				'event_time' => Yii::t('app', '行为时间'),
				'event_id'   => Yii::t('app', '行为事件id'),
				'related_id' => Yii::t('app', '相关表id'),
				'remark'     => Yii::t('app', '行为相关备注'),
				'is_sync'    => Yii::t('app', '是否已同步：0否、1是'),
			];
		}

		/**
		 * 添加非企客户行为轨迹
		 */
		public static function addExternalTimeLine ($eventData)
		{
			if (!empty($eventData['sea_id']) && !empty($eventData['event'])) {
				$timeLine = new PublicSeaTimeLine();

				$timeLine->sea_id     = isset($eventData['sea_id']) ? $eventData['sea_id'] : 0;
				$timeLine->uid        = isset($eventData['uid']) ? $eventData['uid'] : 0;
				$timeLine->sub_id     = isset($eventData['sub_id']) ? $eventData['sub_id'] : 0;
				$timeLine->user_id    = isset($eventData['user_id']) ? $eventData['user_id'] : 0;
				$timeLine->event      = $eventData['event'];
				$timeLine->event_time = time();
				$timeLine->event_id   = isset($eventData['event_id']) ? $eventData['event_id'] : 0;
				$timeLine->related_id = isset($eventData['related_id']) ? $eventData['related_id'] : 0;
				$timeLine->remark     = isset($eventData['remark']) ? $eventData['remark'] : '';
				$timeLine->is_sync    = isset($eventData['is_sync']) ? $eventData['is_sync'] : 0;

				$timeLine->save();
			}
		}

		/**
		 * 非企微客户行为轨迹数据
		 */
		public static function getExternalTimeLine ($uid, $data)
		{
			$info = [];
			$userInfo = UserProfile::findOne(['uid' => $uid]);
			foreach ($data as $key => $val) {
				$infoData               = [];
				$icon                   = '5';
				$content                = '';
				$infoData['event_time'] = !empty($val['event_time']) ? date('Y-m-d H:i:s', $val['event_time']) : '';
				switch ($val['event']) {
					case 'set_field'://完善客户信息
						$icon = 10;
						$name = '';
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							$name     = $workUser->name . '的';
						}
						$content = '完善' . $name . '客户信息';
						if (!empty($val['remark'])) {
							$content .= '：' . $val['remark'];
						}
						break;
					case 'follow'://客户跟进
						$icon = 11;
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							$name     = !empty($workUser) ? $workUser->name : '';
						} elseif (!empty($val['sub_id'])) {
							$subInfo = SubUserProfile::findOne(['sub_user_id' => $val['sub_id']]);
							$name    = !empty($subInfo) ? $subInfo->name : '';
						} else {
							$name = $userInfo->nick_name;
						}

                        $followModle = PublicSeaContactFollowRecord::findOne($val['related_id']);
                        if ($followModle && $followModle->record_type == 1) {
                            $content = '';
                            if (is_numeric($followModle->record)) {
                                $dialoutRecord = DialoutRecord::findOne((int)$followModle->record);
                                if ($dialoutRecord) {
                                    if ($dialoutRecord->state ==1 && $dialoutRecord->begin > 0) {
                                        $content = "【" . $name . "】对该客户第" . $val['remark'] . "次通话：【已接通】通话时长" . DateUtil::getHumanFormatBySecond($dialoutRecord->end- $dialoutRecord->begin);
                                    }else{
                                        $waitSeconds = $dialoutRecord->ringing > 0 ? ($dialoutRecord->end-$dialoutRecord->ringing) . 's' : '-';
                                        $content = "【" . $name . "】对该客户第" . $val['remark'] . "次通话：【未接通】响铃时长" . $waitSeconds;
                                    }

                                }
                            }
                        }else{
                            $content = '【' . $name . '】 第' . $val['remark'] . '次跟进';
                            if (!empty($val['event_id'])) {
                                $follow  = Follow::findOne($val['event_id']);
                                $content .= '，为【' . $follow->title . '】状态';
                            }
                        }
						break;
					case 'add_tag'://打标签
						$icon = 1;
						$name = '';
						if (!empty($val['related_id'])) {
							$workUser = WorkUser::findOne($val['related_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
							$content = '【' . $name . '】对该客户打标签';
						} else {
							if (!empty($val['user_id'])) {
								$workUser = WorkUser::findOne($val['user_id']);
								$name     = $workUser->name . '的';
							}
							$content = '系统对' . $name . '客户打标签';
						}
						if (!empty($val['remark'])) {
							$content .= $val['remark'];
						}
						break;
					case 'del_tag'://移除标签
						$icon = 2;
						$name = '';
						if (!empty($val['related_id'])) {
							$workUser = WorkUser::findOne($val['related_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
							$content = '【' . $name . '】对该客户移除标签';
						} else {
							if (!empty($val['user_id'])) {
								$workUser = WorkUser::findOne($val['user_id']);
								$name     = $workUser->name . '的';
							}
							$content = '系统对' . $name . '客户移除标签';
						}
						if (!empty($val['remark'])) {
							$content .= $val['remark'];
						}
						break;
					case 'add_custom'://非企微客户添加
						$icon = 21;
						$content = $val['remark'];
						break;
					case 'claim_custom'://客户认领
						$icon = 22;
						$content = $val['remark'];
						break;
					case 'assign_custom'://客户分配
						$icon = 14;
						$content = $val['remark'];
						break;
					case 'transfer_custom'://转交
						$icon    = 14;
						$content = $val['remark'];
						break;
					case 'protect_custom'://客户保护
						$icon    = 23;
						$content = $val['remark'];
						break;
					case 'no_protect_custom'://客户取消保护
						$icon    = 25;
						$content = $val['remark'];
						break;
					case 'give_up_custom'://客户放弃
						$icon    = 17;
						$content = $val['remark'];
						break;
				}
				$infoData['icon']    = $icon;
				$infoData['content'] = $content;
				$info[]              = $infoData;
			}

			return $info;
		}
	}
