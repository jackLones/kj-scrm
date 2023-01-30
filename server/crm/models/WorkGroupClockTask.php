<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_group_clock_task}}".
	 *
	 * @property int                    $id
	 * @property int                    $activity_id   活动ID
	 * @property int                    $days          打卡天数
	 * @property int                    $type          奖品类型 1实物 2红包
	 * @property string                 $reward_name   奖品名称
	 * @property int                    $reward_stock  奖品库存
	 * @property string                 $money_amount  红包金额
	 * @property int                    $money_count   红包数量
	 * @property int                    $reward_type   奖品方式：联系客服 2兑换链接
	 * @property string                 $user_key      客服人员
	 * @property int                    $is_open       0不可用1可用
	 * @property string                 $config_id     联系方式的配置id
	 * @property string                 $qr_code       联系二维码的URL
	 * @property int                    $create_time   创建时间
	 *
	 * @property WorkGroupClockActivity $activity
	 */
	class WorkGroupClockTask extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_group_clock_task}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['activity_id', 'days', 'type', 'reward_stock', 'money_count', 'reward_type', 'is_open', 'create_time'], 'integer'],
				[['money_amount'], 'number'],
				[['user_key'], 'string'],
				[['reward_name'], 'string', 'max' => 50],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupClockActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => 'ID',
				'activity_id'  => '活动ID',
				'days'         => '打卡天数',
				'type'         => '奖品类型 1实物 2红包',
				'reward_name'  => '奖品名称',
				'reward_stock' => '奖品库存',
				'money_amount' => '红包金额',
				'money_count'  => '红包数量',
				'reward_type'  => '奖品方式：联系客服 2兑换链接',
				'user_key'     => '客服人员',
				'is_open'      => '0不可用1可用',
				'config_id'    => '联系方式的配置id',
				'qr_code'      => '联系二维码的URL',
				'create_time'  => '创建时间',
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
		public function getActivity ()
		{
			return $this->hasOne(WorkGroupClockActivity::className(), ['id' => 'activity_id']);
		}

		/**
		 * @param $data
		 * @param $id
		 * @param $corpId
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function add ($data, $id, $corpId)
		{
			$activity = WorkGroupClockActivity::findOne($id);
			if (!empty($activity) && ($activity->status == 0)) {
				$oldId     = array_unique(array_column($data, 'id'));
				$nowId     = [];
				$clockData = self::find()->where(['activity_id' => $id])->select('id')->asArray()->all();
				if (!empty($clockData)) {
					foreach ($clockData as $da) {
						array_push($nowId, $da['id']);
					}
					foreach ($nowId as $ids) {
						if (!in_array($ids, $oldId)) {
							self::deleteConfigId($ids);
							self::deleteAll(['id' => $ids]);
						}
					}
				}
			}
			$isOpen = 0;
			foreach ($data as $val) {
				if (empty($val['id'])) {
					$task              = new WorkGroupClockTask();
					$task->create_time = time();
					if (($val['reward_type'] == 1) && !empty($val['user_keys'])) {
						$configArr = self::addConfigId($activity, $val['user_keys']);
						if (!empty($configArr)) {
							$task->config_id = $configArr['config_id'];
							$task->qr_code   = $configArr['qr_code'];
						}
					}
				} else {
					$task = self::findOne($val['id']);
					if ($val['reward_type'] == 1) {
						if (!empty($task->user_key)) {
							$userKey = Json::decode($task->user_key, true);
							if ($userKey != $val['user_keys']) {
								if (!empty($task->config_id)) {
									self::updateConfigId($task, $activity, $val['user_keys']);
								} else {
									$configArr = self::addConfigId($activity, $val['user_keys']);
									if (!empty($configArr)) {
										$task->config_id = $configArr['config_id'];
										$task->qr_code   = $configArr['qr_code'];
									}
								}
							}
						} else {
							if (!empty($val['user_keys'])) {
								$configArr = self::addConfigId($activity, $val['user_keys']);
								if (!empty($configArr)) {
									$task->config_id = $configArr['config_id'];
									$task->qr_code   = $configArr['qr_code'];
								}
							}
						}
					}
				}
				$task->activity_id = $id;
				$task->days        = $val['days'];
				$task->type        = $val['type'];
				$task->is_open     = intval($val['is_open']);
				if ($val['type'] == 1) {
					$task->reward_name  = $val['reward_name'];
					$task->money_amount = 0;
				} elseif ($val['type'] == 2) {
					$task->money_amount = $val['money_amount'];
					$task->reward_name  = '';
				}
				$task->reward_type = $val['reward_type'];
				$task->user_key    = Json::encode($val['user_keys']);
				if (!$task->validate() || !$task->save()) {
					throw new InvalidDataException(SUtils::modelError($task));
				}
				if (!empty($val['is_open'])) {
					$isOpen = 1;
				}
			}

			if (empty($isOpen)) {
				throw new InvalidDataException('至少开启一个打卡任务！');
			}

			return true;
		}

		/**
		 * 验证当前成员是否可以生产活码
		 *
		 * @param $userKey
		 * @param $activityId
		 * @param $corpId
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function verifyCode ($userKey, $activityId, $corpId)
		{
			$state = WorkGroupClockActivity::NAME . '_' . $activityId;
			$users = [];
			foreach ($userKey as $u) {
				$workUser = WorkUser::findOne($u['id']);
				if (!empty($workUser)) {
					array_push($users, $workUser->userid);
				}
			}
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => $state,
				'user'        => $users,
				'party'       => [],
			];

			try {
				$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECAddContactWay($sendData);
					\Yii::error($wayResult, 'getCode_1');
					if ($wayResult['errcode'] != 0) {
						throw new InvalidDataException($wayResult['errmsg']);
					}

					return true;
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				} elseif (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				} elseif (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				} elseif (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40100') !== false) {
					$message = '用户的外部联系人已经在转移流程中';
				}
				throw new InvalidDataException($message);
			}
		}

		//生成config_id
		public static function addConfigId ($activity, $userKey)
		{
			$state = WorkGroupClockActivity::NAME . '_' . $activity->id;
			$users = [];
			foreach ($userKey as $u) {
				$workUser = WorkUser::findOne($u['id']);
				if (!empty($workUser)) {
					array_push($users, $workUser->userid);
				}
			}
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => $state,
				'user'        => $users,
				'party'       => [],
			];
			try {
				$workApi = WorkUtils::getWorkApi($activity->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECAddContactWay($sendData);
					\Yii::error($wayResult, 'wayResult');
					if ($wayResult['errcode'] != 0) {
						throw new InvalidDataException($wayResult['errmsg']);
					}
					$wayInfo        = $workApi->ECGetContactWay($wayResult['config_id']);
					$wayInfo        = SUtils::Object2Array($wayInfo);
					$contactWayInfo = $wayInfo['contact_way'];

					return ['config_id' => $contactWayInfo['config_id'], 'qr_code' => $contactWayInfo['qr_code']];
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				} elseif (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				} elseif (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				} elseif (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40100') !== false) {
					$message = '用户的外部联系人已经在转移流程中';
				} elseif (strpos($message, '-1') !== false) {
					$message = '系统繁忙，建议重试';
				}

				throw new InvalidDataException($message);
			}

			return [];
		}

		//根据config_id进行修改
		public static function updateConfigId ($task, $activity, $userKey)
		{
			$state = WorkGroupClockActivity::NAME . '_' . $activity->id;
			$users = [];
			foreach ($userKey as $u) {
				$workUser = WorkUser::findOne($u['id']);
				if (!empty($workUser)) {
					array_push($users, $workUser->userid);
				}
			}
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => $state,
				'user'        => $users,
				'party'       => [],
				'config_id'   => $task->config_id,
			];

			try {
				$workApi = WorkUtils::getWorkApi($activity->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
					$workApi->ECUpdateContactWay($sendData);
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				} elseif (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				} elseif (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				} elseif (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40100') !== false) {
					$message = '用户的外部联系人已经在转移流程中';
				} elseif (strpos($message, '-1') !== false) {
					$message = '系统繁忙，建议重试';
				}

				throw new InvalidDataException($message);
			}

			return [];
		}

		/**
		 * @param $taskId
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function deleteConfigId ($taskId)
		{
			$task = self::findOne($taskId);
			if (!empty($task)) {
				if (!empty($task->config_id)) {
					$workApi = WorkUtils::getWorkApi($task->activity->corp_id, WorkUtils::EXTERNAL_API);
					if (!empty($workApi)) {
						try {
							$workApi->ECDelContactWay($task->config_id);
						} catch (\Exception $e) {

						}
					}
				}
			}

			return true;
		}

	}
