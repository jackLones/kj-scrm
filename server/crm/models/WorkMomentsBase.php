<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MomentsJob;
	use app\util\DateUtil;
	use app\util\WorkUtils;
    use dovechen\yii2\weWork\ServiceWork;
    use dovechen\yii2\weWork\src\dataStructure\ImageMesssageContent;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use dovechen\yii2\weWork\src\dataStructure\VideoMesssageContent;
    use dovechen\yii2\weWork\Work;
    use ParameterError;
	use QyApiError;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\db\Exception;

	/**
	 * This is the model class for table "{{%work_moments_base}}".
	 *
	 * @property int                $id
	 * @property int                $corp_id          企业id
	 * @property int                $agent_id         应用id
	 * @property int                $sub_id           子账户id
	 * @property int                $user_id          创建员工id
	 * @property int                $is_mobile        来源1pc2手机端
	 * @property string             $title            标题
	 * @property string             $ownership        归属成员
	 * @property string             $user_ids         归属成员id
	 * @property string             $condition        条件[]代表全部
	 * @property int                $advanced_setting 同步设置,1开，0关
	 * @property string             $send_time        发送时间null立即
	 * @property int                $type             类型：1、仅文本；2、图片；3、视频；4、链接
	 * @property string             $context          朋友圈内容
	 * @property int                $status           1已审核,2未审核,3审核失败
	 * @property int                $send_success     0失败,1成功,2待发送
	 * @property int                $create_time
	 * @property int                $update_time
	 * @property string             $info             详细内容
	 * @property int                $is_del           是否删除
	 * @property string             $province         省
	 * @property string             $city             市
	 * @property int                $audit_id         审核表id
	 *
	 * @property WorkMoments[]      $workMoments
	 * @property WorkMomentsAudit[] $workMomentsAudits
	 * @property WorkCorpAgent      $agent
	 * @property WorkCorp           $corp
	 * @property SubUser            $sub
	 * @property WorkMomentsAudit   $audit
	 * @property WorkUser           $user
	 */
	class WorkMomentsBase extends \yii\db\ActiveRecord
	{
		const BASE_URL = '/h5/pages/moments/list';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moments_base}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agent_id', 'sub_id', 'user_id', 'is_mobile', 'advanced_setting', 'type', 'status', 'send_success', 'create_time', 'update_time', 'is_del', 'audit_id', 'visible_type'], 'integer'],
				[['ownership', 'user_ids', 'condition', 'context', 'info'], 'string'],
				[['title'], 'string', 'max' => 255],
				[['send_time'], 'string', 'max' => 30],
				[['province', 'city'], 'string', 'max' => 20],
                [['synchro_moment_id'], 'string', 'max' => 34],
				[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubUser::className(), 'targetAttribute' => ['sub_id' => 'sub_id']],
				[['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMomentsAudit::className(), 'targetAttribute' => ['audit_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'corp_id'          => Yii::t('app', '企业id'),
				'agent_id'         => Yii::t('app', '应用id'),
				'sub_id'           => Yii::t('app', '子账户id'),
				'user_id'          => Yii::t('app', '创建员工id'),
				'is_mobile'        => Yii::t('app', '来源1pc2手机端'),
				'title'            => Yii::t('app', '标题'),
				'ownership'        => Yii::t('app', '归属成员'),
				'user_ids'         => Yii::t('app', '归属成员id'),
				'condition'        => Yii::t('app', '条件[]代表全部'),
				'advanced_setting' => Yii::t('app', '同步设置,1开，0关'),
				'send_time'        => Yii::t('app', '发送时间null立即'),
				'type'             => Yii::t('app', '类型：1、仅文本；2、图片；3、视频；4、链接'),
				'context'          => Yii::t('app', '朋友圈内容'),
				'status'           => Yii::t('app', '1已审核,2未审核,3审核失败'),
				'send_success'     => Yii::t('app', '0失败,1成功,2待发送'),
				'create_time'      => Yii::t('app', 'Create Time'),
				'update_time'      => Yii::t('app', 'Update Time'),
				'info'             => Yii::t('app', '详细内容'),
				'is_del'           => Yii::t('app', '是否删除'),
				'province'         => Yii::t('app', '省'),
				'city'             => Yii::t('app', '市'),
				'audit_id'         => Yii::t('app', '审核表id'),
				'synchro_moment_id'=> Yii::t('app', '同步企业微信朋友圈id'),
				'visible_type'     => Yii::t('app', '可见范围类型。0：部分可见 1：公开'),
			];
		}

		/**
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->context = rawurlencode(rawurldecode($this->context));

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->context)) {
				$this->context = rawurldecode($this->context);
			}

			parent::afterFind();
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMoments ()
		{
			return $this->hasMany(WorkMoments::className(), ['base_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent_id']);
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
		public function getSub ()
		{
			return $this->hasOne(SubUser::className(), ['sub_id' => 'sub_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAudit ()
		{
			return $this->hasOne(WorkMomentsAudit::className(), ['id' => 'audit_id']);
		}

		/**
		 * @param $info
		 * @param $type
		 */
		public static function getDataInfo (&$info, $type)
		{
			if (is_array($info)) {
				foreach ($info as &$item) {
					if ($type == 4) {
						if (is_array($item)) {
							if (isset($item["attachment"]) && !empty($item["attachment"])) {
								WorkMomentNews::getAttachmentInfo($item["attachment"], $item);
							}
						} else {
							if (isset($info["attachment"]) && !empty($info["attachment"])) {
								WorkMomentNews::getAttachmentInfo($info["attachment"], $info);
							}
							$info = [$info];
						}

						return;
					} else {
						//可能是内容引擎的内容
						if (isset($item["attachment"]) && !empty($item["attachment"])) {
							WorkMomentMedia::getAttachmentInfo($item["attachment"], $item);
							continue;
						}
					}
				}
			} else {
				$info = [];
			}
		}

		/**
		 * @param self $base
		 * @param      $agentId
		 * @param      $userId
		 * @param      $info
		 *
		 * @throws InvalidDataException
		 * @throws ParameterError
		 * @throws QyApiError
		 * @throws InvalidConfigException
		 */
		public static function Send ($base, $agentId, $userId, $info)
		{
			$serviceWork = WorkUtils::getWorkApi($base->corp_id);
			$str         = "已将内容发至员工的历史朋友圈。\r\n提醒你将以下内容【手动同步】至【微信朋友圈】\r\n具体内容如下\r\n";
			$start_str   = "=======内容如下=======";
			if (!empty($base->context)) {
				$str .= "1段文字";
			}
			if ($base->type == 2 && !empty($info)) {
				$str .= (count($info)) . '张图片';
			} elseif ($base->type == 3 && !empty($info)) {
				$str .= "1个视频";
			} elseif ($base->type == 4 && !empty($info)) {
				$str .= "1个图文连接";
			}
			self::SendAgentMessage($userId, $agentId, $str, $base->corp_id);
			self::SendAgentMessage($userId, $agentId, $start_str, $base->corp_id);
			if (!empty($base->context)) {
				self::SendAgentMessage($userId, $agentId, urldecode($base->context), $base->corp_id);
			}
			if (!empty($info)) {
				foreach ($info as $value) {
					Yii::error($value, '$value');
					if ($base->type == 4) {
						if (!empty($value["url"])) {
							if (isset($value["attachment"]) && !empty($value["attachment"])) {
								WorkMomentMedia::getAttachmentInfo($value["attachment"], $value);
							}
							self::SendAgentMessage($userId, $agentId, $value["url"], $base->corp_id);

							return;
						}
					} else {
						if (isset($value["attachment"]) && !empty($value["attachment"])) {
							WorkMomentMedia::getAttachmentInfo($value["attachment"], $image);
						}
						if (isset($value["md5"]) && empty($value["md5"])) {
							$tempMedia = TempMedia::findOne(["md5" => $value["md5"]]);
						}
						if (!empty($tempMedia) && !empty($tempMedia->media_id) && strtotime($tempMedia->create_time) + 259299 < time()) {
							$media_id = $tempMedia->media_id;
						} else {
							if ($base->type == 3) {
								$media_id = $serviceWork->MediaUpload(\Yii::$app->basePath . $value["local_path"], 'video');
							} else {
								$media_id = $serviceWork->MediaUpload(\Yii::$app->basePath . $value["local_path"], 'image');
							}
							if (!empty($tempMedia)) {
								$tempMedia->media_id = $media_id;
								$tempMedia->save();
							}
						}
						if ($base->type == 3) {
							self::SendAgentVideoMessage($userId, $agentId, urldecode($base->context), $base->corp_id, $media_id);
						} else {
							self::SendAgentImageMessage($userId, $agentId, urldecode($base->context), $base->corp_id, $media_id);
						}
					}
				}
			}

		}

		/**
		 * @param $corp_id
		 * @param $user_id
		 *
		 * @return array
		 */
		public static function getWorkUserIds ($corp_id, $user_id = NULL)
		{
			if (!empty($user_id)) {
				$user = WorkUser::find()->where(["in", "id", $user_id])->andWhere(["status" => 1, "is_external" => 1, 'is_del'=>0])->select("id,userid")->asArray()->all();
			} else {
				$user = WorkUser::find()->where(["corp_id" => $corp_id])->andWhere(["status" => 1, "is_external" => 1, 'is_del'=>0])->select("id,userid")->asArray()->all();
			}

			return array_column($user, "id", "userid");
		}

		public static function SendAgentMessage ($toUser, $agentId, $messageContent, $corp_id)
		{
			$workApi = WorkUtils::getAgentApi($corp_id, $agentId);

			$messageContent = [
				'content' => $messageContent,
			];
			$messageContent = TextMesssageContent::parseFromArray($messageContent);
			$agent          = WorkCorpAgent::findOne($agentId);
			$message        = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'toparty'                  => [],
				'totag'                    => [],
				'duplicate_check_interval' => 10,
			];
			$message        = Message::pareFromArray($message);
			try {
				$workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
			} catch (\Exception $e) {
				Yii::error($e->getLine(), "symLine");
				Yii::error($e->getMessage(), "symMessage");
			}
		}

		public static function SendAgentImageMessage ($toUser, $agentId, $messageContent, $corp_id, $media_id)
		{

			$workApi = WorkUtils::getAgentApi($corp_id, $agentId);

			$messageContent = [
				'content'  => $messageContent,
				'msgtype'  => "image",
				'media_id' => $media_id,
			];
			$messageContent = ImageMesssageContent::parseFromArray($messageContent);
			$agent          = WorkCorpAgent::findOne($agentId);
			$message        = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'toparty'                  => [],
				'totag'                    => [],
				'duplicate_check_interval' => 10,
			];
			$message        = Message::pareFromArray($message);

			try {
				$workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
			} catch (\Exception $e) {
				Yii::error($e->getLine(), "symLine");
				Yii::error($e->getMessage(), "symMessage");
			}
		}

		public static function SendAgentVideoMessage ($toUser, $agentId, $messageContent, $corp_id, $media_id)
		{

			$workApi = WorkUtils::getAgentApi($corp_id, $agentId);

			$messageContent = [
				'content'  => $messageContent,
				'msgtype'  => "video",
				'media_id' => $media_id,
			];
			$messageContent = VideoMesssageContent::parseFromArray($messageContent);
			$agent          = WorkCorpAgent::findOne($agentId);
			$message        = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'toparty'                  => [],
				'totag'                    => [],
				'duplicate_check_interval' => 10,
			];
			$message        = Message::pareFromArray($message);

			try {
				$workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
			} catch (\Exception $e) {
				Yii::error($e->getLine(), "symLine");
				Yii::error($e->getMessage(), "symMessage");
			}
		}

		/**
		 * @param self $base
		 * @param      $agentId   //应用id
		 * @param      $info      //图文。图片。视频
		 * @param bool $job       //是否来自队列
		 * @param bool $is_mobile //是否来自手机端
		 *                        朋友圈内容创建
		 *
		 * @return int
		 * @throws InvalidConfigException
		 * @throws InvalidDataException
		 * @throws ParameterError
		 * @throws QyApiError
		 */
		public static function setMomentContext ($base, $agentId, $info, $job = false, $is_mobile = false)
		{
			$base      = self::findOne($base->id);
			$ownership = json_decode($base->ownership, true);
			$user_ids  = NULL;
			//获取发送的所有成员
			$userKey = [];
			if ($is_mobile || $base->is_mobile == 2) {
				$user_ids = $user_id = [$base->user_id];
			} else {
				if (empty($ownership)) {
					$user_id = self::getWorkUserIds($base->corp_id);
				} else {
					$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($ownership,true);
					$user_id  = WorkDepartment::GiveDepartmentReturnUserData($base->corp_id, $Temp["department"], $Temp["user"], 1, true);
					if(empty($user_id)){
						throw new InvalidDataException("未选择成员");
					}
					$user_id = self::getWorkUserIds($base->corp_id, $user_id);
				}
				$userKey = array_keys($user_id);
			}


			//是否存在发送时间 | 如果是队列立即发送 | 手机端立即发送但是状态是待审核
			if (empty($base->send_time) || $job || $is_mobile) {
				foreach ($user_id as $key => $item) {
					//手机端发布审核被修改后只修改内容和附加内容
					$moment = WorkMoments::createData($base, $item, []);
					if ($base->type == 4 && !empty($info)) {
						WorkMomentNews::deleteAll(["moment_id" => $moment->id]);
						WorkMomentNews::createData($info, $moment->id);
					} else {
						if (!empty($info)) {
							WorkMomentMedia::deleteAll(["moment_id" => $moment->id]);
							foreach ($info as $img) {
								WorkMomentMedia::createData($img, $moment->id);
							}
						}
					}
				}
//				if ($base->advanced_setting == 1 && !$is_mobile && !empty($userKey)) {
//					WorkMomentsBase::send($base, $agentId, $userKey, $info);
//				}
				$base->user_ids     = is_null($user_ids) ? NULL : implode(",", $user_ids);
				$base->send_success = 1;
				$base->save();

				return 1;
			} else {
				//定时发送
				$difference = strtotime($base->send_time) - time();
				if ($difference < 0) {
					throw new InvalidDataException("时间相差错误");
				}
				//保存jobid 修改时需要
				$job_id = \Yii::$app->work->delay($difference)->push(new MomentsJob([
					'momentsId' => $base->id,
				]));
				\Yii::$app->cache->set($base->id . "moment-add", $job_id, $difference);
				$base->send_success = 2;
				$base->save();

				return 2;
			}
		}

		public static function getUserKey($base)
        {
            $ownership = json_decode($base->ownership, true);
            if (empty($ownership)) {
                $user_id = self::getWorkUserIds($base->corp_id);
            } else {
                $Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($ownership,true);
                $user_id  = WorkDepartment::GiveDepartmentReturnUserData($base->corp_id, $Temp["department"], $Temp["user"], 1, true);
                if(empty($user_id)){
                    throw new InvalidDataException("未选择成员");
                }
                $user_id = self::getWorkUserIds($base->corp_id, $user_id);
            }
            $userKey = array_keys($user_id);
            return $userKey;
        }

		public function UpdateMomentsAll ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$startTime = '2020-01-01';
			$date      = DateUtil::getCurrentYMD();
			$day1      = strtotime($date);
			$day2      = strtotime($startTime);
			$Days      = round(($day1 - $day2) / 3600 / 24);
			$day       = ceil($Days / 30);
			$setting   = WorkMomentSetting::find()->where(['is_synchro_all' => 0])->andWhere(['is_synchro' => 1])->asArray()->all();

			if (!empty($setting)) {
				foreach ($setting as $key => $val) {
					try {
						$workApi = WorkUtils::getWorkApi($val['corp_id'], WorkUtils::EXTERNAL_API);
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), __CLASS__ . ':' . __FUNCTION__ . ':getWorkApi');
						continue;
					}

					$end      = '';
					$workUser = WorkUser::find()->select(['id', 'corp_id'])->where(['corp_id' => $val['corp_id']])->andWhere(['dimission_time' => 0, 'enable' => 1, 'status' => 1])->asArray()->all();
					if (!empty($workUser)) {
						for ($i = 1; $i <= $day; $i++) {
							if ($i == 1) {
								$start = $startTime;
							} else {
								$start = $end;
							}
							$end     = date('Y-m-d', strtotime("+30 day", strtotime($start)));
							$time    = (string) strtotime($start);
							$endTime = (string) strtotime($end);
							//因企业微信过于频繁调取，会出现请求频繁的情况，所以每次调取延迟一秒
							sleep(1);
							try {
								$momentList = $workApi->GetMomentList($time, $endTime);
							} catch (\Exception $e) {
								Yii::error($e->getMessage(), __CLASS__ . ':' . __FUNCTION__ . ':GetMomentList');
								continue;
							}
							if (empty($momentList['moment_list'])) {
								continue;
							}
							foreach ($workUser as $k => $v) {
								try {
									$this->Synchro($v['corp_id'], $v['id'], $start, $end);
								} catch (\Exception $e) {
									$message = $e->getMessage();
									Yii::error($message, '$message-update-moments-all');
								}
							}
						}
						$workMomentSetting                 = WorkMomentSetting::findOne($val['id']);
						$workMomentSetting->is_synchro_all = 1;
						$workMomentSetting->save();
					}
				}
			}

			return true;

		}

		public function UpdateMoments ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$startTime = DateUtil::getPreviousSecondsTime(1 * 24 * 60 * 60);
			$endTime   = DateUtil::getCurrentTime();
			$setting   = WorkMomentSetting::find()->where(['is_synchro' => 1])->asArray()->all();
			if (!empty($setting)) {
				$corpId   = array_column($setting, 'corp_id');
				$workUser = WorkUser::find()->select(['id', 'corp_id'])->where(['corp_id' => $corpId])->andWhere(['dimission_time' => 0, 'enable' => 1, 'status' => 1])->asArray()->all();
				if (!empty($workUser)) {
					foreach ($workUser as $key => $val) {
						try {
							$this->Synchro($val['corp_id'], $val['id'], $startTime, $endTime);
						} catch (\Exception $e) {
							$message = $e->getMessage();
							Yii::error($message, '$message-update-moments');
						}
					}
				}
			}

			return true;
		}

		/**
         * 同步朋友圈数据
         */
		public function Synchro($corp_id = '', $user_id ='', $startTime = '', $endTime = '')
        {
            if(empty($corp_id) || empty($user_id)) {
                throw new InvalidDataException("参数错误");
            }
            $momentSetting = WorkMomentSetting::findOne(['corp_id' => $corp_id]);
            if(!$momentSetting) {
                throw new InvalidDataException("朋友圈企业设置未设置");
            }
            if(!$momentSetting->is_synchro) {
                throw new InvalidDataException("同步朋友圈设置未开启");
            }
            //因企业微信过于频繁调取，会出现请求频繁的情况，所以每次调取延迟一秒
            sleep(1);
            try{
                //同步个人发表的朋友圈
                $this->SynchroPersonalMoment($corp_id, $user_id, $startTime, $endTime);
                //同步企业发表的朋友圈
                $this->SynchroEnterpriseMoment($corp_id, $user_id, $startTime, $endTime);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                Yii::error($message, '$message-update-moments');
                return false;
            }
            return true;
        }

        /**
         * 同步个人发表的朋友圈
         * @param $momenr
         * @return bool
         */
        public function SynchroPersonalMoment($corp_id, $user_id = '', $startTime = '', $endTime = '')
        {
            if(empty($user_id)) {
                throw new InvalidDataException("员工信息查询失败");
            }
            $workUser = WorkUser::findOne($user_id);
            if(!$workUser) {
                throw new InvalidDataException("员工信息查询失败");
            }
            $workApi   = WorkUtils::getWorkApi($corp_id,WorkUtils::EXTERNAL_API);
            $userId = $workUser->userid;
            $corp_id = $workUser->corp_id;
            $is_mobile = 4;
            $timeArr = $this->momentStartTime($startTime, $endTime, $userId, $is_mobile);
            $startTime = $timeArr['startTime'];
            $endTime = $timeArr['endTime'];
            $this->Errmsg(
                $momenr = $workApi->GetMomentList($startTime, $endTime, $userId, 1)
            );
            if(empty($momenr['moment_list'])) {
                return true;
            }
            return $this->SynchroMoment($workApi, $momenr, $userId, $user_id, $corp_id);
        }

        //同步企业发表的朋友圈
        public function SynchroEnterpriseMoment($corp_id, $user_id = '', $startTime = '', $endTime = '')
        {
            if(empty($user_id)) {
                throw new InvalidDataException("员工信息查询失败");
            }
            $workUser = WorkUser::findOne($user_id);
            if(!$workUser) {
                throw new InvalidDataException("员工信息查询失败");
            }
            $workApi   = WorkUtils::getWorkApi($corp_id,WorkUtils::EXTERNAL_API);
            $userId = $workUser->userid;
            $corp_id = $workUser->corp_id;
            $is_mobile = 3;
            $timeArr = $this->momentStartTime($startTime, $endTime, $user_id, $is_mobile);
            $startTime = $timeArr['startTime'];
            $endTime = $timeArr['endTime'];
            //企业发表并不一定是该用户发的，所以拉取该时间段全部企业发表朋友圈
            $this->Errmsg(
                $momenr = $workApi->GetMomentList($startTime, $endTime, '', 0)
            );
            $momenr['moment_list'] = array_filter($momenr['moment_list'], function($val) use ($userId, $workApi) {
                return $this->MomentTask($workApi, $val['moment_id'], $userId);
            });
            if(empty($momenr['moment_list'])) {
                return true;
            }
            //查询该朋友圈该用户是否同步
            $momenr['moment_list'] = array_filter($momenr['moment_list'], function($val) use ($user_id, $workApi) {
                return empty(WorkMomentsBase::find()
                    ->alias('a')
                    ->leftJoin(WorkMoments::tableName(). ' b', 'b.base_id = a.id')
                    ->where(['a.synchro_moment_id' => $val['moment_id']])
                    ->andWhere(['b.user_id' => $user_id])
                    ->asArray()
                    ->one());
            });
            if(empty($momenr['moment_list'])) {
                return false;
            }

            return $this->SynchroMoment($workApi, $momenr, $userId, $user_id, $corp_id);
        }

        //获取拉取的开始时间
        public function momentStartTime($startTime = '', $endTime = '', $user_id, $is_mobile = 4)
        {
            if(empty($startTime) && empty($endTime)) {//时间为空，查询员工最后一次同步时间
                $endTime = (string)time();
                $momentCreate = WorkMomentsBase::find()
                    ->alias('a')
                    ->select('b.create_time')
                    ->leftJoin(WorkMoments::tableName(). ' b', 'b.base_id = a.id')
                    ->where(['a.is_mobile' => $is_mobile])
                    ->andWhere(['b.user_id' => $user_id])
                    ->orderBy('b.create_time desc')
                    ->asArray()
                    ->one();
                if($momentCreate) {//不存在 当前时间往前推一个月
                    $day= date("Y-m-d H:i:s");
                    $day1 = strtotime($day);
                    $day2 = strtotime($momentCreate['create_time']);
                    $Days = round(($day1-$day2)/3600/24);
                    if($Days > 30) {//超过一个月 当前时间往前推一个月
                        $startTime = (string)strtotime(date("Y-m-d H:i:s", strtotime("-1 month")));
                    } else {
                        $startTime = (string)strtotime($momentCreate['create_time']);
                    }
                } else {
                    $startTime = (string)strtotime(date("Y-m-d H:i:s", strtotime("-1 month")));
                }
            } else {
                if($is_mobile == 3) {
                    $startTime = (string)strtotime("-1 month",strtotime($endTime));
                    $endTime = (string)strtotime($endTime);
                } else {
                    $momentCreate = WorkMomentsBase::find()
                        ->alias('a')
                        ->select('b.create_time')
                        ->leftJoin(WorkMoments::tableName(). ' b', 'b.base_id = a.id')
                        ->where(['a.is_mobile' => $is_mobile])
                        ->andWhere(['b.user_id' => $user_id])
                        ->orderBy('b.create_time desc')
                        ->asArray()
                        ->one();
                    if($momentCreate) {
                        $day  = date("Y-m-d H:i:s");
                        $day1 = strtotime($day);
                        $day2 = strtotime($momentCreate['create_time']);
                        $Days = round(($day1-$day2)/3600/24);
                        if($Days > 30) {//超过一个月 当前时间往前推一个月
                            $startTime = (string)strtotime(date("Y-m-d H:i:s", strtotime("-1 month")));
                            $endTime   = (string)$day1;
                        } else {
                            $startTime = (string)$day2;
                            $endTime = (string)$day1;
                        }
                    } else {
                        $day1 = strtotime($endTime);
                        $day2 = strtotime($startTime);
                        $Days = round(($day1-$day2)/3600/24);
                        if($Days > 30) {//超过一个月
                            throw new InvalidDataException("查询区间最多30天");
                        }
                        $startTime = (string)$day2;
                        $endTime = (string)$day1;
                    }
                }
            }
            return ['startTime' => $startTime, 'endTime' => $endTime];
        }


        public function SynchroMoment($workApi, $momenr, $userId, $user_id, $corp_id)
        {
            if(empty($momenr['moment_list'])) {
                return true;
            }
            foreach ($momenr['moment_list'] as $key => $val) {
                $momentsBase = WorkMomentsBase::find()
                    ->where(['corp_id' => $corp_id])
                    ->andWhere(['synchro_moment_id' => $val['moment_id']])
                    ->one();
                if($val['create_type'] == 1 && $momentsBase) {
                    continue;
                }
                if(!$momentsBase) {
                    //存储朋友圈信息
                    $momentSetting = WorkMomentSetting::findOne(['corp_id' => $corp_id]);
                    $momentsBase = new WorkMomentsBase();
                    $momentsBase->corp_id = $corp_id;
                    $momentsBase->agent_id = empty($momentSetting) ? '' : $momentSetting->agent_id;
                    $momentsBase->synchro_moment_id = $val['moment_id'];
                    $momentsBase->title = '官方拉取';
                    $momentsBase->ownership = '[]';
                    $momentsBase->condition = '[]';
                    $momentsBase->send_success = 1;
                    $momentsBase->create_time = $val['create_time'];
                    $momentsBase->is_mobile = empty($val['create_type']) ? 3 : 4;//来源 3企业  4 企业个人
                    $momentsBase->visible_type = $val['visible_type'];
                    $momentsBase->context = empty($val['text']) ? '' : $val['text']['content'];
                    $momentType = '';
                    if(isset($val['link']) && isset($val['image'])) {//链接
                        $type = WorkMoments::NEWS_MOMENT;
                        $momentType = 'link';
                    } else if(isset($val['video'])) {//视频
                        $type = WorkMoments::VIDEO_MOMENT;
                        $momentType = 'video';
                    } else if(isset($val['image']) && !empty($val['image'])) {//图文
                        $type = WorkMoments::IMAGE_MOMENT;
                        $momentType = 'image';
                    } else {
                        $type = WorkMoments::TEXT_MOMENT;
                    }
                    if($momentType == 'link') {
                        $pic_url = $this->MediaGet($workApi, $val['image'], $momentType);
                        $info = [
                            'url' => $val['link']['url'],
                            'title' => $val['link']['title'],
                            'pic_url' => $pic_url,
                            'sort' => 1
                        ];
                    } elseif($momentType == 'video') {
                        $info = $this->MediaGet($workApi, $val['video'], $momentType);
                    } else {
                        if(empty($val['image'])) {
                            $info = [];
                        } else {
                            $info = $this->MediaGet($workApi, $val['image']);
                        }
                    }
                    //获取发布员工id
                    $user = WorkUser::findOne(['userid' => $val['creator'], 'corp_id' => $corp_id]);
                    $momentsBase->user_id = empty($user) ? 0 : $user->id;
                    $momentsBase->type = $type;
                    $momentsBase->info = json_encode($info);
                    $momentsBase->save();
                }
                //发布人员
                $this->Ownership($momentsBase, $user_id);

                $external_userid = '';
                if(!$val['visible_type']) {//部分可见
                    $external_userid = $this->SendResult($workApi, $momentsBase->synchro_moment_id, $userId, $corp_id);
                }
                //存储朋友圈于员工的绑定关系
                $workMoments = new WorkMoments();
                $workMoments->corp_id = $momentsBase->corp_id;
                $workMoments->user_id = $user_id;
                $workMoments->open_status = 1;
                $workMoments->type = $momentsBase->type;
                $workMoments->text = empty($val['text']) ? '' : $val['text']['content'];
                $workMoments->base_id = $momentsBase->id;
                $workMoments->external_status = empty($external_userid) ? 1 : 2;
                $workMoments->external_userid = $external_userid;
                $workMoments->create_time = date('Y-m-d H:i:s', $momentsBase->create_time);
                $workMoments->save();

                //存储图片相关信息
                if(!empty($momentsBase->info)) {
                    $info = json_decode($momentsBase->info, true);
                    if($momentsBase->type == WorkMoments::VIDEO_MOMENT || $momentsBase->type == WorkMoments::IMAGE_MOMENT) {//图文，视频
                        $this->mediaMoment($info, $workMoments->id);
                    } else if($momentsBase->type == WorkMoments::NEWS_MOMENT) {//链接
                        $workMomentNews = new WorkMomentNews();
                        $workMomentNews->moment_id = $workMoments->id;
                        $workMomentNews->title = $info['title'];
                        $workMomentNews->url = $info['url'];
                        $workMomentNews->pic_path = $info['pic_url'];
                        $workMomentNews->save();
                    }
                }
                //存储互动数据,接口暂时不完善，完善之后添加
            }
            return true;
        }

        //存储图片于视频相关数据
        public function mediaMoment($info, $moment_id)
        {
            $media = [];
            foreach ($info as $v) {
                $media[] = [
                    'moment_id' => $moment_id,
                    'sort' => $v['sort'],
                    'local_path' => $v['local_path'],
                ];
            }

            \Yii::$app->db->createCommand()
                ->batchInsert(WorkMomentMedia::tableName(), [
                    'moment_id',
                    'sort',
                    'local_path',
                ], $media)
                ->execute();
            return true;
        }

        //获取企业类型发表的列表
        public function MomentTask($workApi, $moment_id, $userid)
        {
            $arr = $workApi->GetMomentTask($moment_id);
            $this->Errmsg($arr);
            if(empty($arr['task_list'])) {
                return false;
            }
            $userStatus = array_column($arr['task_list'], 'publish_status', 'userid');
            if(!isset($userStatus[$userid])) {
                return false;
            }
            if($userStatus[$userid] == 0) {
                return false;
            }
            return true;
        }

        //发表后的可见客户列表
        public function SendResult($workApi, $moment_id, $userid, $corp_id)
        {
            $arr = $workApi->GetMomentSendResult($moment_id, $userid);
            $this->Errmsg($arr);
            if(empty($arr['customer_list'])) {
                return '';
            }
            $external_userid = array_column($arr['customer_list'], 'external_userid');
            //获取用户id
            $contactId = WorkExternalContact::find()
                ->select('id')
                ->where(['external_userid' => $external_userid])
                ->andWhere(['corp_id' => $corp_id])
                ->asArray()->all();
            if(!$contactId) {
                return '';
            }
            $contactId = array_column($contactId, 'id');
            return implode(',',$contactId);
        }

        //发布人员添加
        public function Ownership($workMomentsBase, $user_id)
        {
            $workUser = WorkUser::findOne($user_id);
            if(!$workUser) {
                return true;
            }
            $ownership[] = [
                'title' => $workUser->name,
                'avatar' => '',
                'department' => '',
                'id' => $workUser->id,
                'scopedSlots' => [
                    'title' => 'custom'
                ],
                'isLeaf' => true
            ];
            if($workMomentsBase->ownership != '[]') {
                $baseownership = json_decode($workMomentsBase->ownership, true);
                array_push($baseownership, $ownership[0]);
                $ownership = $baseownership;
            }
            $workMomentsBase->ownership = json_encode($ownership);
            $workMomentsBase->save();
            return true;
        }

        //获取互动数据 接口暂时不完善
        public function MomentComments($workApi, $moment_id, $userid)
        {
            $arr = $workApi->GetMomentComments($moment_id, $userid);
        }

        /**
         * 获取临时资源数据
         */
        public function MediaGet($workApi, $arr, $type = 'image')
        {
            if(empty($arr)) {
                return [];
            }
            if($type == 'video') {
                $media = $workApi->MediaGet($arr['media_id']);
                $local_path = $this->Upload($media, $type);
                $info[] = [
                    'local_path' => $local_path,
                    's_local_path' => null,
                    'attchment' => '',
                    'sort' => 1,
                ];
                return $info;
            }
            $info = [];
            foreach ($arr as $key => $val) {
                $arr = $workApi->MediaGet($val['media_id']);
                if($type == 'link') {
                    return $this->Upload($arr, $type);
                }
                $info[$key]['local_path'] = $this->Upload($arr, $type);
                $info[$key]['s_local_path'] = null;
                $info[$key]['sort'] = $key+1;
                $info[$key]['attchment'] = '';
            }

            return $info;
        }

        public function Errmsg($momenr)
        {
            if($momenr['errcode'] == 0 && $momenr['errmsg'] == 'ok') {
                return true;
            }
            throw new InvalidDataException("同步失败");
        }

        public function Upload($file_url, $type = 'image')
        {
            if(empty($file_url)) {
                return false;
            }
            if($type == 'video') {
                $paths = 'moment/videos';
                $ext = 'mp3';
            } else {
                $paths = 'moment/temp';
                $ext = 'jpg';
            }
            $path = \Yii::getAlias('@upload');
            $savePaths = '/'.$paths.'/' . date('Ymd') . '/';
            $path = $path.$savePaths;
            $filename = date("YmdHis",time()).'-'.uniqid().'.'.$ext; //重命名文件名
            $savePath = '/upload/'.$savePaths.$filename;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $destPath1 = $path.$filename; //存放url
            file_put_contents($destPath1, $file_url);

            return $savePath;
        }

	}
