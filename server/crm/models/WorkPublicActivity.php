<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%work_public_activity}}".
	 *
	 * @property int                                $id
	 * @property int                                $type             1公众号2企业微信3企业+公众号
	 * @property int                                $uid              企业uid
	 * @property int                                $sub_id           子账户id
	 * @property int                                $corp_id          企业id
	 * @property int                                $corp_agent       应用id
	 * @property int                                $public_id        公众号id
	 * @property int                                $is_over          1未结束，2时间结束，3阶段结束,4手动结束，5进行中
	 * @property string                             $activity_name    活动名称
	 * @property string                             $activity_rule    活动规则
	 * @property string                             $describe         描述
	 * @property int                                $poster_open      活动海报描述1发送2不发送
	 * @property string                             $poster_describe  海报描述
	 * @property int                                $sex_type         性别参与 1男，2女，3未知，4不限制
	 * @property int                                $region_type      1不限，2指定地区
	 * @property string                             $region           地区
	 * @property string                             $label_id         标签id
	 * @property int                                $mutual           是否允许互助1允许2不允许
	 * @property int                                $number           活动主力次数默认0不限制(单人)
	 * @property string                             $keyword          关键词触发
	 * @property int                                $not_attention    1不扣除,2取关扣除人数，3取关删除扣除（企业），4删除（企业）
	 * @property int                                $action_type      2客服领取1H5领取
	 * @property string                             $channel_user     企业微信员工id生成渠道码
	 * @property string                             $channel_user_id  企业微信员工id
	 * @property string                             $user_key         客服员工id
	 * @property string                             $hfive_url        h5地址
	 * @property string                             $hfive_config     h5配置
	 * @property int                                $start_time       开始时间
	 * @property string                             $code_url         活动二维码
	 * @property string                             $qc_url           渠道活码
	 * @property string                             $config_id        渠道活码id
	 * @property int                                $config_del       1已刪除0未刪除
	 * @property string                             $user_url         客服二维码
	 * @property string                             $qr_scene_str     二维码参数
	 * @property string                             $welcome          欢迎语
	 * @property string                             $welcome_help     助力者欢迎语
	 * @property string                             $welcome_url      企业图文
	 * @property string                             $welcome_title    企业图片标题
	 * @property string                             $welcome_describe 企业描述
	 * @property int                                $end_time         结束时间
	 * @property int                                $tickets_start    兑奖开始时间
	 * @property int                                $tickets_end      兑奖结束时间
	 * @property string                             $level_end        阶段结束
	 * @property int                                $create_time      创建时间
	 * @property int                                $update_time      修改时间
	 * @property int                                $flow             流程1直接发放资格，2选择奖品
	 * @property string                             $success_tags     完成后打上指定标签
	 *
	 * @property Fans[]                             $fans
	 * @property WorkExternalContactFollowUser[]    $workExternalContactFollowUsers
	 * @property WorkCorpAgent                      $corpAgent
	 * @property WorkCorp                           $corp
	 * @property SubUser                            $sub
	 * @property User                               $u
	 * @property WorkPublicActivityConfigCall[]     $workPublicActivityConfigCalls
	 * @property WorkPublicActivityConfigLevel[]    $workPublicActivityConfigLevels
	 * @property WorkPublicActivityFansUser[]       $workPublicActivityFansUsers
	 * @property WorkPublicActivityFansUserDetail[] $workPublicActivityFansUserDetails
	 * @property WorkPublicActivityPosterConfig[]   $workPublicActivityPosterConfigs
	 * @property WorkPublicActivityPrizeUser[]      $workPublicActivityPrizeUsers
	 * @property WorkPublicActivityStatistic[]      $workPublicActivityStatistics
	 * @property WorkPublicActivityTier[]           $workPublicActivityTiers
	 */
	class WorkPublicActivity extends \yii\db\ActiveRecord
	{
		const ACTIVITY_TYPE_ONE = 1;
		const ACTIVITY_TYPE_TWO = 2;
		const ACTIVITY_TYPE_THREE = 3;
		const ACTION_TYPE_ONE = 1;
		const ACTION_TYPE_TWO = 2;
		const NOT_ATT_ONE = 1;
		const NOT_ATT_TWO = 2;
		const NOT_ATT_THREE = 3;
		const NOT_ATT_FOUR = 4;
		const IS_OVER_ONE = 1;
		const IS_OVER_TWO = 2;
		const IS_OVER_THREE = 3;
		const IS_OVER_FOUR = 4;
		const STATE_NAME = "activity";
		const H5_URL = "/h5/pages/marketFission/index";
		const H5_URL_P = "/h5/pages/marketFission/preview";
		const TICKET_H5_URL = "/h5/pages/marketFission/ticket";
		const RANKING = "/h5/pages/marketFission/list";
		const REDEEM = "/h5/pages/marketFission/address";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['type', 'uid', 'sub_id', 'corp_id', 'corp_agent', 'public_id', 'is_over', 'poster_open', 'sex_type', 'region_type', 'mutual', 'number', 'not_attention', 'action_type', 'start_time', 'config_del', 'end_time', 'tickets_start', 'tickets_end', 'create_time', 'update_time', 'flow'], 'integer'],
				[['activity_rule', 'describe', 'poster_describe', 'region', 'channel_user', 'channel_user_id', 'user_key', 'welcome', 'welcome_help', 'welcome_describe'], 'string'],
				[['activity_name', 'label_id', 'keyword', 'hfive_url', 'hfive_config', 'code_url', 'qc_url', 'config_id', 'user_url', 'qr_scene_str', 'welcome_url', 'welcome_title', 'success_tags'], 'string', 'max' => 255],
				[['level_end'], 'string', 'max' => 60],
				[['corp_agent'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['corp_agent' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubUser::className(), 'targetAttribute' => ['sub_id' => 'sub_id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'type'             => Yii::t('app', '1公众号2企业微信3企业+公众号'),
				'uid'              => Yii::t('app', '企业uid'),
				'sub_id'           => Yii::t('app', '子账户id'),
				'corp_id'          => Yii::t('app', '企业id'),
				'corp_agent'       => Yii::t('app', '应用id'),
				'public_id'        => Yii::t('app', '公众号id'),
				'is_over'          => Yii::t('app', '1未结束，2时间结束，3阶段结束,4手动结束，5进行中'),
				'activity_name'    => Yii::t('app', '活动名称'),
				'activity_rule'    => Yii::t('app', '活动规则'),
				'describe'         => Yii::t('app', '描述'),
				'poster_open'      => Yii::t('app', '活动海报描述1发送2不发送'),
				'poster_describe'  => Yii::t('app', '海报描述'),
				'sex_type'         => Yii::t('app', '性别参与 1男，2女，3未知，4不限制'),
				'region_type'      => Yii::t('app', '1不限，2指定地区 '),
				'region'           => Yii::t('app', '地区'),
				'label_id'         => Yii::t('app', '标签id'),
				'mutual'           => Yii::t('app', '是否允许互助1允许2不允许'),
				'number'           => Yii::t('app', '活动主力次数默认0不限制(单人)'),
				'keyword'          => Yii::t('app', '关键词触发'),
				'not_attention'    => Yii::t('app', '1不扣除,2取关扣除人数，3取关删除扣除（企业），4删除（企业）'),
				'action_type'      => Yii::t('app', '2客服领取1H5领取'),
				'channel_user'     => Yii::t('app', '企业微信员工id生成渠道码'),
				'channel_user_id'  => Yii::t('app', '企业微信员工id'),
				'user_key'         => Yii::t('app', '客服员工id'),
				'hfive_url'        => Yii::t('app', 'h5地址'),
				'hfive_config'     => Yii::t('app', 'h5配置'),
				'start_time'       => Yii::t('app', '开始时间'),
				'code_url'         => Yii::t('app', '活动二维码'),
				'qc_url'           => Yii::t('app', '渠道活码'),
				'config_id'        => Yii::t('app', '渠道活码id'),
				'config_del'       => Yii::t('app', '1已刪除0未刪除'),
				'user_url'         => Yii::t('app', '客服二维码'),
				'qr_scene_str'     => Yii::t('app', '二维码参数'),
				'welcome'          => Yii::t('app', '欢迎语'),
				'welcome_help'     => Yii::t('app', '助力者欢迎语'),
				'welcome_url'      => Yii::t('app', '企业图文'),
				'welcome_title'    => Yii::t('app', '企业图片标题'),
				'welcome_describe' => Yii::t('app', '企业描述'),
				'end_time'         => Yii::t('app', '结束时间'),
				'tickets_start'    => Yii::t('app', '兑奖开始时间'),
				'tickets_end'      => Yii::t('app', '兑奖结束时间'),
				'level_end'        => Yii::t('app', '阶段结束'),
				'create_time'      => Yii::t('app', '创建时间'),
				'update_time'      => Yii::t('app', '修改时间'),
				'flow'             => Yii::t('app', '流程1直接发放资格，2选择奖品'),
				'success_tags'     => Yii::t('app', '完成后打上指定标签'),
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
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->describe        = rawurlencode(rawurldecode($this->describe));
			$this->poster_describe = rawurlencode(rawurldecode($this->poster_describe));
			$this->welcome         = rawurlencode(rawurldecode($this->welcome));
			$this->welcome_help    = rawurlencode(rawurldecode($this->welcome_help));

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->describe)) {
				$this->describe = rawurldecode($this->describe);
			}
			if (!empty($this->poster_describe)) {
				$this->poster_describe = rawurldecode($this->poster_describe);
			}
			if (!empty($this->welcome)) {
				$this->welcome = rawurldecode($this->welcome);
			}
			if (!empty($this->welcome_help)) {
				$this->welcome_help = rawurldecode($this->welcome_help);
			}

			parent::afterFind(); // TODO: Change the autogenerated stub
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
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkPublicActivityConfigCalls ()
		{
			return $this->hasMany(WorkPublicActivityConfigCall::className(), ['activity_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkPublicActivityConfigLevels ()
		{
			return $this->hasMany(WorkPublicActivityConfigLevel::className(), ['activity_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkPublicActivityFansUsers ()
		{
			return $this->hasMany(WorkPublicActivityFansUser::className(), ['activity_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkPublicActivityFansUserDetails ()
		{
			return $this->hasMany(WorkPublicActivityFansUserDetail::className(), ['activity_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkPublicActivityPosterConfigs ()
		{
			return $this->hasMany(WorkPublicActivityPosterConfig::className(), ['activity_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkPublicActivityPrizeUsers ()
		{
			return $this->hasMany(WorkPublicActivityPrizeUser::className(), ['activity_id' => 'id']);
		}

		//生成渠道活码
		public static function CheckCorpUser ($workApi, $activityId, $channel_user, $fans_id = 0, $tierId = 0, $action = false, $source = 1)
		{
			try {
				$ActivityData   = self::findOne($activityId);
				$workUser       = WorkUser::find()->where(["in", "id", $channel_user])->asArray()->all();
				$workUser       = array_column($workUser, "userid");
				$contactWayInfo = [
					'type'        => 2,
					'scene'       => 2,
					'style'       => 1,
					'remark'      => '',
					'skip_verify' => true,
					'state'       => "activity_" . $activityId . "_" . $fans_id . "_" . $tierId,
					'user'        => $workUser,
					'party'       => [],
				];
				$config         = [];
				if (!empty($ActivityData->config_id)) {
					$config = json_decode($ActivityData->config_id, true);
				}
				if (!empty($config)) {
					if ($source == 1) {
						if (isset($config["user_key"])) {
							$contactWayInfo["config_id"] = $config["user_key"];
						}
					} else {
						if (isset($config["channel_user"]) && empty($fans_id)) {
							$contactWayInfo["config_id"] = $config["channel_user"];
						}else{
							if(!empty($fans_id)){
								$fansUser = WorkPublicActivityFansUser::findOne($fans_id);
								if(!empty($fansUser)){
									$contactWayInfo["config_id"] = $fansUser->config_id;
								}
							}
						}
					}
				}
				if (!empty($contactWayInfo["config_id"])) {
					$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
					$workApi->ECUpdateContactWay($sendData);
					$GetData                = $workApi->ECGetContactWay($contactWayInfo["config_id"]);
					$GetData                = SUtils::Object2Array($GetData);
					$wayResult["config_id"] = $contactWayInfo["config_id"];
					$wayResult["qr_code"]   = $GetData["contact_way"]["qr_code"];
				} else {
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECAddContactWay($sendData);
				}
				if (!empty($ActivityData) && (!isset($config["user_key"]) || !isset($config["channel_user"]))) {
					if ($source == 1) {
						$config["user_key"] = $wayResult['config_id'];
					}
					if ($source == 2 && empty($fans_id)) {
						$config["channel_user"] = $wayResult['config_id'];
					}
					if (empty($fans_id)) {
						self::updateAll(["config_id" => json_encode($config, 288)], ["id" => $activityId]);
					}
				}
				if (!empty($fans_id)) {
					WorkPublicActivityFansUser::updateAll(["qc_url" => $wayResult['qr_code'], "config_id" => $wayResult['config_id']], ["id" => $fans_id]);
				}
				if ($action) {
					//将渠道码生成到本地
					$file     = file_get_contents($wayResult["qr_code"]);
					$fileName = "activity_qc_url_" . rand(1, 10000) . time() . ".jpg";//定义图片名
					$save_dir = \Yii::getAlias('@upload') . '/poster/' . date('Ymd') . '/';
					//创建保存目录
					if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
						throw new InvalidDataException("文件创建失败，权限不足");
					}
					file_put_contents($save_dir . $fileName, $file);

					return "/upload/poster/" . date('Ymd') . '/' . $fileName;
				}

				return $wayResult;
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				} elseif (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				} elseif (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				} elseif (strpos($message, '-1') !== false) {
					$message = '系统繁忙，建议重试';
				}
				\Yii::error($message, 'err-user');
				throw new InvalidDataException($message);
			}
		}

		public static function ActivityStatisticsAll ($activityId)
		{
			$newTime  = strtotime(date("Y-m-d", time()));
			$lastTime = $newTime - 86400;
			$nowTime  = $newTime + 86400;
			$activity = self::findOne($activityId);
			//参与
			$res["net_fans"] = WorkPublicActivityFansUser::find()->where(["activity_id" => $activity->id])->count();
			//今日日参与
			$res["net_now_fans"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", $newTime, $nowTime])
				->count();
			//昨日参与
			$res["net_last_fans"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", $lastTime, $newTime])
				->count();
			//今日新增
			$res["now_new_fans"] = Fans::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", date("Y-m-d H:i:s", $newTime), date("Y-m-d H:i:s", $nowTime)])
				->count();
			//新增
			$res["new_fans"] = Fans::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", date("Y-m-d H:i:s", $activity->start_time), date("Y-m-d H:i:s", $activity->end_time)])
				->count();
			//昨日新增
			$res["new_last_fans"] = Fans::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", date("Y-m-d H:i:s", $lastTime), date("Y-m-d H:i:s", $newTime)])
				->count();
			//今日去关(新)
			$res["now_not_day"] = WorkPublicActivityFansUser::find()->alias("fu")
				->leftJoin("{{%fans}} as f", "fu.fans_id = f.id")
				->where(["fu.activity_id" => $activity->id, "f.subscribe" => 0])
				->andFilterWhere(["between", "f.unsubscribe_time", $newTime, $nowTime])
				->andFilterWhere(["between", "f.create_time", date("Y-m-d H:i:s", $activity->start_time), date("Y-m-d H:i:s", $activity->end_time)])
				->count();
			//累计取关(新)
			$res["not_day_count"] = WorkPublicActivityFansUser::find()->alias("fu")
				->leftJoin("{{%fans}} as f", "fu.fans_id = f.id")
				->where(["fu.activity_id" => $activity->id, "f.subscribe" => 0])
				->andFilterWhere(["between", "f.create_time", date("Y-m-d H:i:s", $activity->start_time), date("Y-m-d H:i:s", $activity->end_time)])
				->count();
			//昨日去关(新)
			$res["last_now_not_day"] = WorkPublicActivityFansUser::find()->alias("fu")
				->leftJoin("{{%fans}} as f", "fu.fans_id = f.id")
				->where(["fu.activity_id" => $activity->id])
				->andWhere(["f.subscribe" => 0])
				->andFilterWhere(["between", "f.unsubscribe_time", $lastTime, $newTime])
				->andFilterWhere(["between", "f.create_time", date("Y-m-d H:i:s", $activity->start_time), date("Y-m-d H:i:s", $activity->end_time)])
				->count();
			//新加好友
			$followUser            = WorkExternalContactFollowUser::find()->where(["activity_id" => $activity->id])->select("createtime,del_type")->asArray()->all();
			$res["new_add"]        = count($followUser);
			$res["now_new_add"]    = 0;
			$res["last_add"]       = 0;
			$res["lose_fans"]      = 0;
			$res["now_lose_fans"]  = 0;
			$res["last_lose_fans"] = 0;
			foreach ($followUser as $item) {
				//今日新加好友
				if ($item["createtime"] > $newTime && $item["createtime"] < $nowTime) {
					$res["now_new_add"] += 1;
				}
				//昨日新增好友
				if ($item["createtime"] > $lastTime && $item["createtime"] < $newTime) {
					$res["last_add"] += 1;
				}
				//流失好友
				if ($item["del_type"] == 2) {
					$res["lose_fans"] += 1;
					//今日新加好友
					if ($item["createtime"] > $newTime && $item["createtime"] < $nowTime) {
						$res["now_lose_fans"] += 1;
					}
					//昨日新增好友
					if ($item["createtime"] > $lastTime && $item["createtime"] < $newTime) {
						$res["last_lose_fans"] += 1;
					}
				}
			}
			//完成任务;
			$res["success"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andWhere("prize is not null")
				->count();
			//昨日完成任务
			$res["last_success"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andWhere("prize is not null")
				->andFilterWhere(["between", "success_time", $lastTime, $newTime])
				->count();
			//今日日完成任务
			$res["now_success"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andWhere("prize is not null")
				->andFilterWhere(["between", "success_time", $newTime, $nowTime])
				->count();
			//净增
			$res["participation"] = $res["new_fans"] - $res["not_day_count"];
			//昨日净增
			$res["last_participation"] = $res["new_last_fans"] - $res["last_now_not_day"];
			//今日日净增
			$res["now_participation"] = $res["now_new_fans"] - $res["now_not_day"];
			$res["keep"]              = $res["now_keep"] = $res["last_keep"] = 0;
			//留存率
			if ($res["new_add"] != 0) {
				$res["keep"] = round(($res["new_add"] - $res["lose_fans"]) / $res["new_add"], 2) * 100;
			}
			//今日留存率
			if ($res["now_new_add"] != 0) {
				$res["now_keep"] = round(($res["now_new_add"] - $res["now_lose_fans"]) / $res["now_new_add"], 2) * 100;
			}
			//昨日留存率
			if ($res["last_add"] != 0) {
				$res["last_keep"] = round(($res["last_add"] - $res["last_lose_fans"]) / $res["last_add"], 2) * 100;
			}

			$res["time"] = [date("Y-m-d", $activity->start_time), date("Y-m-d", $activity->end_time)];

			return $res;
		}

		/**
		 * @param        $activityId
		 * @param string $deta1
		 * @param string $deta2
		 *
		 * @return mixed
		 */
		public static function ActivityStatisticsNow ($activityId, $deta1 = '', $deta2 = '')
		{
			$res["new_fans"] = $res["net_fans"] = $res["participation"] = $res["now_not_day"] = 0;
			$res["new_add"]  = $res["lose_fans"] = $res["last_lose_fans"] = 0;
			$res["success"]  = $res["keep"] = $res["last_keep"] = 0;
			if (!empty($deta1) && !empty($deta2)) {
				$lastTime = strtotime($deta1);
				$newTime  = strtotime($deta2) - 1;
			} else {
				$lastTime = strtotime(date("Y-m-d", time()));
				$newTime  = time();
			}
			$res["time"] = date("Y-m-d", $lastTime);
			$activity    = self::findOne($activityId);
			//今日参与
			$res["net_fans"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", $lastTime, $newTime])
				->count();
			//今日新增粉丝（第一次）
			$res["new_fans"] = Fans::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "create_time", $deta1, $deta2])
				->count();
			//今日去关
			$res["now_not_day"] = WorkPublicActivityFansUser::find()->alias("fu")
				->leftJoin("{{%fans}} as f", "fu.fans_id = f.id")
				->where(["fu.activity_id" => $activity->id])
				->andWhere(["f.subscribe" => 0])
				->andFilterWhere(["between", "f.unsubscribe_time", $lastTime, $newTime])
				->andFilterWhere(["between", "f.create_time", date("Y-m-d H:i:s", $activity->start_time), date("Y-m-d H:i:s", $activity->end_time)])
				->count();
			//今日新增好友
			$res["new_add"] = WorkExternalContactFollowUser::find()
				->where(["activity_id" => $activity->id])
				->andFilterWhere(["between", "createtime", $lastTime, $newTime])
				->count();
			//今日流失好友
			$res["lose_fans"] += WorkExternalContactFollowUser::find()
				->where(["activity_id" => $activity->id, "del_type" => 2])
				->andFilterWhere(["between", "del_time", $lastTime, $newTime])
				->count();
			//今日完成任务
			$res["success"] = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activity->id])
				->andWhere("prize is not null")
				->andFilterWhere(["between", "success_time", $lastTime, $newTime])
				->count();
			//今日净增
			$res["participation"] = $res["new_fans"] - $res["now_not_day"];
			//留存率
			if ($res["new_add"] != 0) {
				$res["keep"] = round(($res["new_add"] - $res["lose_fans"]) / $res["new_add"] * 100, 2);
			}

			return $res;
		}

		public static function ActivityStatisticsAllDay ($activityId, $date1, $date2, $type = 1)
		{
			$data     = DateUtil::getDateFromRange($date1, $date2);
			$legData1 = $legData = ["new_fans"      => "新增粉丝",
			                        "participation" => "净增新粉丝",
			                        "now_not_day"   => "取关新粉丝",
			                        "net_fans"      => "参与人",
			                        "success"       => "完成任务",
			                        "new_add"       => '新添加好友',
			                        "lose_fans"     => '流失好友',
			                        "keep"          => "好友留存率%"];
			$activity = self::findOne($activityId);
			if ($activity->type == 1) {
				for ($i = 0; $i < 3; $i++) {
					array_pop($legData);
					array_pop($legData1);
				}
			} elseif ($activity->type == 2) {
				for ($i = 0; $i < 3; $i++) {
					array_shift($legData);
					array_shift($legData1);
				}
			}
			//数据不存在用空代替
			$NULLData = ["new_fans"  => 0, "participation" => 0, "now_not_day" => 0,
			             "net_fans"  => 0, "success" => 0, "new_add" => 0,
			             "lose_fans" => 0, "keep" => 0];
			$tmpData  = $xData = $res = [];
			switch ((int) $type) {
				case 0:
					$res = self::ActivityStatisticsHours($activityId, $date1, $date2);
					//24小时时间
					$xData = [
						"0:00-1:00", "1:00-2:00", "2:00-3:00", "3:00-4:00", "4:00-5:00", "5:00-6:00", "6:00-7:00",
						"7:00-8:00", "8:00-9:00", "9:00-10:00", "10:00-11:00", "11:00-12:00", "12:00-13:00", "13:00-14:00",
						"14:00-15:00", "15:00-16:00", "16:00-17:00", "17:00-18:00", "18:00-19:00", "19:00-20:00",
						"20:00-21:00", "21:00-22:00", "22:00-23:00", "23:00-24:00",
					];
					break;
				case 1:
					foreach ($data as $key => $item) {
						$NULLData["time"] = $xData[] = $item;
						if (strtotime(date("Y-m-d", strtotime($item))) == strtotime(date("Y-m-d", time()))) {
							$static = self::ActivityStatisticsNow($activityId, $item, date("Y-m-d", strtotime($item) + 86400));
						} else {
							$static = WorkPublicActivityStatistic::find()->where(["activity_id" => $activityId, "time" => $item, "type" => 1])->asArray()->one();
							if (empty($static)) {
								$static = self::ActivityStatisticsNow($activityId, $item, date("Y-m-d", strtotime($item) + 86400));
							}
						}
						if (empty($static)) {
							$res[] = $NULLData;
						} else {
							$static["time"] = $item;
							$res[]          = $static;
						}
					}
					break;
				case 2:
					//按周
					$data    = DateUtil::getWeekFromRange($data);
					$s_date1 = $data['s_date'];
					$e_date1 = $data['e_date'];
					foreach ($s_date1 as $key => $item) {
						$NULLData["time"] = $xData[] = $item . '-' . $e_date1[$key] . " 第(" . date("W", strtotime($item)) . ")周";
						$sdefaultDate     = date("Y-m-d");
						$first            = 1;
						$w                = date('w', strtotime($sdefaultDate));
						$week_start       = date('Y-m-d', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
						if ($item == $week_start) {
							$static = self::ActivityStatisticsNow($activityId, $week_start, date("Y-m-d", strtotime($week_start) + 604800));
						} else {
							$static = WorkPublicActivityStatistic::find()->where(["activity_id" => $activityId, "time" => $item, "type" => 2])->asArray()->one();
						}
						if (empty($static)) {
							$res[] = $NULLData;
						} else {
							$static["time"] = $item . '-' . $e_date1[$key] . " 第(" . date("W", strtotime($item)) . ")周";
							$res[]          = $static;
						}
					}
					break;
				case 3:
					$date = [];
					//按月
					$aStartTime = date("Y-m", $activity->start_time);
					$eEndTime   = date("Y-m", $activity->end_time);
					$i          = 0;
					while ($aStartTime) {
						if ($aStartTime > $eEndTime) {
							break;
						}
						$firstday            = $date[$i]["firstday"] = $aStartTime . "-01";
						$lastday             = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
						$date[$i]["lastday"] = $lastday;
						$aStartTime          = date('Y-m', strtotime("$firstday +1 month"));
						$i++;
					}
					foreach ($date as $key => $item) {
						$NULLData["time"] = $xData[] = date("Y-m", strtotime($item["firstday"]));
						if ($item["firstday"] == date("Y-m-01", time())) {
							$static = self::ActivityStatisticsNow($activityId, $item["firstday"], $item["lastday"] . " 23:59:59");
						} else {
							$static = WorkPublicActivityStatistic::find()->where(["activity_id" => $activityId, "time" => $item["firstday"], "type" => 3])->asArray()->one();
						}
						if (empty($static)) {
							$res[] = $NULLData;
						} else {
							$static["time"] = date("Y-m", strtotime($item["firstday"]));
							$res[]          = $static;
						}
					}
					break;
			}
			foreach ($legData as $key => $datum) {
				$tmp           = [];
				$tmp["name"]   = $datum;
				$tmp["smooth"] = true;
				$tmp["type"]   = 'line';
				$tmpSeriesData = array_column($res, $key);
				if ($type == 0) {
					$tmp["data"] = array_reverse($tmpSeriesData);
				} else {
					$tmp["data"] = $tmpSeriesData;
				}

				$tmpData[] = $tmp;
			}
			$seriesData = $tmpData;

			return [
				"seriesData" => $seriesData,
				"legData"    => array_values($legData1),
				"xData"      => $xData,
				"wayData"    => $res,
			];
		}

		/**
		 * @param $type 0 按天 1 按月 2 按周
		 *
		 */
		public static function create ($type)
		{
			try {
				$activity = self::find();
				if ($type == 1) {
					//按天
					$start_date = strtotime(date("Y-m-d", time())) - 86400;
					$end_date   = strtotime(date("Y-m-d", time())) - 1;
					$activity   = $activity->andFilterWhere(["between", "start_time", $start_date, $end_date])->orWhere(["between", "end_time", $start_date, $end_date]);
				} elseif ($type == 2) {
					//按周
					$start_date = strtotime(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y'))));
					$end_date   = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y"))));
					$activity   = $activity->andFilterWhere(["between", "start_time", $start_date, $end_date])->orWhere(["between", "end_time", $start_date, $end_date]);
				} else {
					//按月
					$start_date = strtotime(date('Y-m', strtotime('-1 month ')));
					$end_date   = strtotime(date('Y-m-t 23:59:59', strtotime('-1 month')));
					$activity   = $activity->andFilterWhere(["between", "start_time", $start_date, $end_date])->orWhere(["between", "end_time", $start_date, $end_date]);
				}
				$activity = $activity->asArray()->all();
				foreach ($activity as $item) {
					$result                   = self::ActivityStatisticsNow($item['id'], date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date));
					$statistic                = new WorkPublicActivityStatistic();
					$statistic->activity_id   = $item["id"];
					$statistic->new_add       = $result["new_add"];
					$statistic->net_fans      = $result["net_fans"];
					$statistic->now_not_day   = $result["now_not_day"];
					$statistic->new_fans      = $result["new_fans"];
					$statistic->lose_fans     = $result["lose_fans"];
					$statistic->success       = $result["success"];
					$statistic->participation = $result["participation"];
					$statistic->keep          = $result["keep"];
					$statistic->time          = date("Y-m-d", $start_date);
					$statistic->type          = $type;
					$statistic->save();
				}

			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), '$activity');
			}

		}

		/**
		 * @param        $activityId
		 * @param        $level
		 * @param string $dete
		 * 参与人数
		 *
		 * @return array|int[]
		 */
		public static function ActivityStatisticsIndicator ($activityId, $level, $type, $date1, $date2, $action = false)
		{
			$activityModel              = self::findOne($activityId);
			$WorkPublicActivityFansUser = WorkPublicActivityFansUser::find()
				->where(["activity_id" => $activityId]);
			$data                       = DateUtil::getDateFromRange($date1, $date2);
			$userFansCount              = $successUserFans = $userFans = 0;
			if ($level == 0) {
				$level = [1, 2, 3];
			}
			$level         = 1;//当前限制1阶段
			$userFansModel = WorkPublicActivityFansUser::find()->alias("b")
				->leftJoin("{{%work_public_activity_config_level}} as a", "a.activity_id = b.activity_id")
				->where(["b.activity_id" => $activityId, "a.is_open" => 1])
				->andWhere(["in", "a.level", $level]);
			if ($activityModel->flow == 1) {
				$successModel = WorkPublicActivityFansUser::find()->alias("a")
					->leftJoin("{{%work_public_activity_prize_user}} as b", "a.prize = b.id")
					->where(["a.activity_id" => $activityId])
					->andWhere(["in", "b.level", $level]);
			} else {
				$successModel = clone $userFansModel;
			}
			switch ((int) $type) {
				case 0:
				case 1:
					$userFansCount = $WorkPublicActivityFansUser
						->andWhere(["activity_num" => 0])
						->andWhere("prize is null")
						->andFilterWhere(["between", "create_time", strtotime($date1), strtotime($date2) + 86400])
						->count();
					if ($activityModel->flow == 1) {
						$successUserFans = WorkPublicActivityFansUser::find()->alias("a")
							->leftJoin("{{%work_public_activity_prize_user}} as  b", "a.prize = b.id")
							->where(["a.activity_id" => $activityId])
							->andFilterWhere(["between", "a.success_time", strtotime($date1), strtotime($date2) + 86400])
							->count();
					} else {
						$successUserFans = $successModel
							->andWhere("b.activity_num >= a.number")
							->andFilterWhere(["between", "b.success_time", strtotime($date1), strtotime($date2) + 86400])
							->groupBy("b.id")
							->count();
					}
					$userFans = $userFansModel
						->andFilterWhere(["between", "b.create_time", strtotime($date1), strtotime($date2) + 86400])
						->andWhere("b.activity_num < a.number and b.activity_num !=0")
						->groupBy("b.id")
						->count();
					break;
				case 2:
				case 3:
					if ($type == 2) {
						//按周
						$data    = DateUtil::getWeekFromRange($data);
						$s_date1 = $data['s_date'];
						$s_date2 = $data['e_date'];
						$sTime   = array_shift($s_date1);
						$lTime   = date("Y-m-d", strtotime(array_pop($s_date2)) + 86400);
					} else {
						//按月
						$data    = DateUtil::getLastMonth();
						$s_date1 = array_shift($data);
						$s_date2 = array_pop($data);
						$sTime   = $s_date1['firstday'];
						$lTime   = $s_date2['lastday'] . " 23:59:59";
					}
					$userFansCount = $WorkPublicActivityFansUser
						->andWhere(["activity_num" => 0])
						->andWhere("prize is null")
						->andFilterWhere(["between", "create_time", strtotime($sTime), strtotime($lTime)])
						->count();

					if ($activityModel->flow == 1) {
						$successUserFans = WorkPublicActivityFansUser::find()->alias("a")
							->leftJoin("{{%work_public_activity_prize_user}} as  b", "a.prize = b.id")
							->where(["a.activity_id" => $activityId])
							->andFilterWhere(["between", "a.success_time", strtotime($sTime), strtotime($lTime)])
							->count();
					} else {
						$successUserFans = $successModel
							->andWhere("b.activity_num >= a.number")
							->andFilterWhere(["between", "b.success_time", strtotime($sTime), strtotime($lTime)])
							->groupBy("a.id")
							->count();
					}
					$userFans = $userFansModel
						->andFilterWhere(["between", "b.create_time", strtotime($sTime), strtotime($lTime)])
						->andWhere("b.activity_num < a.number and b.activity_num !=0")
						->groupBy("a.id")
						->count();
					break;
			}
			if ($action) {
				return [
					"userFansCount"   => $userFansCount,
					"userFans"        => $userFans,
					"successUserFans" => $successUserFans,
				];
			}

			return [
				["name" => "参与未推广", "value" => $userFansCount],
				["name" => "推广未完成", "value" => $userFans],
				["name" => "已完成", "value" => $successUserFans],
			];
		}

		public static function ActivityStatisticsHours ($activityId, $date1, $date2)
		{

			$xData    = [
				"00:00-01:00",
				"01:00-02:00",
				"02:00-03:00",
				"03:00-04:00",
				"04:00-05:00",
				"05:00-06:00",
				"06:00-07:00",
				"07:00-08:00",
				"08:00-09:00",
				"09:00-10:00",
				"10:00-11:00",
				"11:00-12:00",
				"12:00-13:00",
				"13:00-14:00",
				"14:00-15:00",
				"15:00-16:00",
				"16:00-17:00",
				"17:00-18:00",
				"18:00-19:00",
				"19:00-20:00",
				"20:00-21:00",
				"21:00-22:00",
				"22:00-23:00",
				"23:00-24:00",
			];
			$NULLData = ["new_fans"      => 0,
			             "participation" => 0,
			             "userFansCount" => 0,
			             "now_not_day"   => 0,
			             "net_fans"      => 0,
			             "success"       => 0,
			             "userFans"      => 0,
			             "new_add"       => 0,
			             "lose_fans"     => 0,
			             "keep"          => 0];
			$TimeData = [
				$NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData,
				$NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData,
				$NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData, $NULLData];
			$time1    = strtotime($date1);
			$time2    = strtotime($date2) + 86400;
			$activity = WorkPublicActivity::findOne($activityId);
			//-- 参加未推广
			$userFansCount = Yii::$app->db->createCommand(
				'SELECT FROM_UNIXTIME(create_time,"%H") as hours ,count(*) as userFansCount from {{%work_public_activity_fans_user}} WHERE activity_id = ' . $activityId . ' and activity_num = 0 and create_time between ' . $time1 . ' and ' . $time2 . '  GROUP BY hours'
			)->queryAll();
			foreach ($userFansCount as $userFansCoun) {
				if (empty($userFansCoun["hours"])) {
					continue;
				}
				$TimeData[(integer) $userFansCoun["hours"]]["userFansCount"] += $userFansCoun["userFansCount"];
			}
			//-- 参与
			$net_fans = Yii::$app->db->createCommand(
				'SELECT FROM_UNIXTIME(create_time,"%H") as hours ,count(*) as net_fans from {{%work_public_activity_fans_user}} WHERE activity_id = ' . $activityId . ' and create_time between ' . $time1 . ' and ' . $time2 . '  GROUP BY hours'
			)->queryAll();
			foreach ($net_fans as $net_fan) {
				if (empty($net_fan["hours"])) {
					continue;
				}
				$TimeData[(integer) $net_fan["hours"]]["net_fans"] += $net_fan["net_fans"];
			}
			//-- 流失
			$lose_fans = Yii::$app->db->createCommand(
				'SELECT FROM_UNIXTIME(del_time,"%H") as hours ,count(*) as lose_fans FROM {{%work_external_contact_follow_user}} where activity_id = ' . $activityId . '  and del_time between ' . $time1 . ' and ' . $time2 . '  and del_type = 2 GROUP BY hours'
			)->queryAll();
			foreach ($lose_fans as $lose_fan) {
				if (empty($lose_fan["hours"])) {
					continue;
				}
				$TimeData[(integer) $lose_fan["hours"]]["lose_fans"] += $lose_fan["lose_fans"];
			}
			//-- 新增
			$new_fans = Yii::$app->db->createCommand(
				'SELECT DATE_FORMAT(create_time,"%H") as hours ,count(*) as new_fans from {{%fans}}  WHERE activity_id = ' . $activityId . ' and create_time BETWEEN "' . date("Y-m-d H:i:s", $time1) . '" and "' . date("Y-m-d H:i:s", $time2) . '" GROUP BY hours'
			)->queryAll();
			foreach ($new_fans as $new_fan) {
				if (empty($new_fan["hours"])) {
					continue;
				}
				$TimeData[(integer) $new_fan["hours"]]["new_fans"] += $new_fan["new_fans"];
			}
			//-- 取关
			$now_not_day = Yii::$app->db->createCommand(
				'SELECT DATE_FORMAT(b.create_time,"%H") as hours ,count(*) as now_not_day from {{%work_public_activity_fans_user}} as a LEFT JOIN {{%fans}} as b on a.fans_id = b.id WHERE a.activity_id = ' . $activityId . ' and b.subscribe=0 and b.create_time BETWEEN "' . date('Y-m-d H:i:s', $activity->start_time) . '" and "' . date('Y-m-d H:i:s', $activity->end_time) . '"  and b.create_time BETWEEN "' . date("Y-m-d H:i:s", $time1) . '" and "' . date("Y-m-d H:i:s", $time2) . '" GROUP BY hours'
			)->queryAll();
			foreach ($now_not_day as $now_not_da) {
				if (empty($now_not_da["hours"])) {
					continue;
				}
				$TimeData[(integer) $now_not_da["hours"]]["now_not_day"] += $now_not_da["now_not_day"];
			}
			//-- 新加好友
			$new_add = Yii::$app->db->createCommand(
				'SELECT FROM_UNIXTIME(createtime,"%H") as hours ,count(*) as new_add FROM {{%work_external_contact_follow_user}} where activity_id = ' . $activityId . ' and createtime BETWEEN "' . $time1 . '" and "' . $time2 . '" GROUP BY hours'
			)->queryAll();
			foreach ($new_add as $new_ad) {
				if (empty($new_ad["hours"])) {
					continue;
				}
				$TimeData[(integer) $new_ad["hours"]]["new_add"] += $new_ad["new_add"];
			}
			//-- 完成任务
			$success = Yii::$app->db->createCommand(
				'select FROM_UNIXTIME(success_time,"%H") as hours ,count(*) as success from {{%work_public_activity_fans_user}} where activity_id = ' . $activityId . ' and success_time BETWEEN ' . $time1 . ' and ' . $time2 . ' GROUP BY hours'
			)->queryAll();

			foreach ($success as $succes) {
				if (empty($succes["hours"])) {
					continue;
				}
				$TimeData[(integer) $succes["hours"]]["success"] += $succes["success"];
			}
			//-- 推广未完成
			$userFans = Yii::$app->db->createCommand(
				'select FROM_UNIXTIME(a.create_time,"%H") as hours ,count(*) as userFans FROM {{%work_public_activity_fans_user}} as a LEFT JOIN {{%work_public_activity_config_level}} as b on a.activity_id = b.activity_id WHERE b.`level` in(1) and a.activity_id =' . $activityId . ' and a.activity_num < b.number and a.activity_num !=0 and a.create_time BETWEEN ' . $time1 . ' and ' . $time2 . ' GROUP BY hours'
			)->queryAll();
			foreach ($userFans as $userFan) {
				if (empty($userFan["hours"])) {
					continue;
				}
				$TimeData[(integer) $userFan["hours"]]["userFans"] += $userFan["userFans"];
			}
			foreach ($TimeData as $key => &$item) {
				$item['time'] = $xData[$key];
				//净增
				$item['participation'] = $item['new_fans'] - $item['now_not_day'];
				//留存率
				if ($item['new_add'] != 0) {
					$item['keep'] = (round(($item['new_add'] - $item['lose_fans']) / $item['new_add'], 2) * 100);
				}
			}
			$TimeData = array_reverse($TimeData);

			return $TimeData;

		}

		public static function repeatTemplate ($template, $wxTile, $template_id, $action = false, $del_type = 0)
		{
			$templateData = explode("{{", $template);
			if (!empty($templateData[0])) {
				unset($templateData[0]);
				$templateData = implode("{{", $templateData);
				$template     = "{{" . $templateData;
			}
			$arr     = explode(PHP_EOL, $template);
			$newData = [];
			$tmpData = [];
			if (!empty($arr)) {
				foreach ($arr as $key => $value) {
					if (!empty($value)) {
						$data                     = explode(".DATA}}", $value);
						$data1                    = explode("{{", $data[0]);
						$newData[$key][$data1[1]] = $data1[0];
						if (isset($data[1]) && !empty(trim($data[1]))) {
							$data2                    = explode("{{", $data[1]);
							$newData[$key][$data2[1]] = $data2[0];
						}
						if (isset($data[2]) && !empty(trim($data[2]))) {
							$data3                    = explode("{{", $data[2]);
							$newData[$key][$data3[1]] = $data3[0];
						}
						$show    = 1;
						$type    = 0;//不显示前后
						$start   = '';
						$end     = '';
						$title   = '';
						$keyWord = '';
						if (count($data) > 2) {
							if (empty($data2[0])) {
								$show = 0;//不显示文字
							} else {
								$title = $data2[0];
							}
							$keyWord = $data2[1];
						} else {
							if (empty($data1[0])) {
								$show = 0;//不显示文字
							}
							$title   = $data1[0];
							$keyWord = $data1[1];
						}
						if (!empty($data[1]) && empty($data[2])) {
							$type  = 1;//显示前
							$start = $data1[1];
						}
						if (empty($data[1]) && !empty($data[2])) {
							$type = 2;//显示后
							$end  = $data3[1];
						}
						if (!empty($data[1]) && !empty($data[2])) {
							$type  = 3;//显示前后
							$start = $data1[1];
							$end   = $data3[1];
						}
						$value = '';
						if ($wxTile == "助力成功通知") {
							switch ($key) {
								case 0:
									$value = '您好，您的好友 {nickname} 支持你了！';
									break;
								case 1:
									$value = '{activityName}';
									break;
								case 2:
									$value = '{success}';
									break;
								case 3:
									$value = "{success} 个好友的支持，还差 {error} 个好友支持，即可获得奖励。继续努力邀请吧~";
									break;
							}
						}
						if ($wxTile == "助力成功通知" && $action) {
							switch ($key) {
								case 0:
									$value = '您好，您的好友 {nickname} 支持你了！';
									break;
								case 1:
									$value = '{activityName}';
									break;
								case 2:
									$value = '{success}';
									break;
								case 3:
									$value = "您已获得{success}个好友的支持，恭喜您完成任务！点此领取奖品吧~";
									break;
							}
						}
						if ($wxTile == "助力失败通知") {
							switch ($key) {
								case 0:
									$value = '您的好友 {nickname} 放弃为你助力！';
									break;
								case 1:
									$value = '{activityName}';
									break;
								case 2:
									if ($del_type == 2) {
										$value = '取消关注，放弃为你助力';
									} elseif ($del_type == 3) {
										$value = '取关+客户删除，放弃为你助力';
									} elseif ($del_type == 4) {
										$value = '客户删除，放弃为你助力';
									} else {
										$value = '';
									}
									break;
								case 3:
									$value = "人气值-1，还差 {error} 个好友的支持。继续努力邀请吧~";
									break;
							}
						}
						$tmpData[$key]['show']  = $show;
						$tmpData[$key]['type']  = $type;
						$tmpData[$key]['start'] = ['key' => $start, 'value' => ''];
						$tmpData[$key]['end']   = ['key' => $end, 'value' => ''];
						$tmpData[$key]['title'] = $title;
						$tmpData[$key]['key']   = $keyWord;
						$tmpData[$key]['value'] = $value;
					}
				}
			}
			$newData = array_values($tmpData);
			if ($action) {
				$result['title'] = "任务完成提醒";
			} else {
				$result['title'] = $wxTile;
			}
			$result['template_id'] = $template_id;
			$result['is_url']      = '';
			$result['data']        = $newData;

			return $result;
		}

		/*
		 * 检查当前参与的活动人数是否已经达到上限
		 * */
		public static function checkJoinNumIsMax ($activity_id = 0)
		{
			if (!$activity_id) {
				return false;
			}
			$data = static::findOne($activity_id);
			if (!$data)
				return false;
			$fissionNum = (int) User::getPackageAboutNum($data->uid, 'fission_num');

			if ($fissionNum > 0) {
				$countFans = WorkPublicActivityFansUser::find()->where(['activity_id' => $activity_id])->count();

				if ($countFans >= $fissionNum) {
					return true;
				}
			}

			return false;
		}

		/*
		 * 将活动置为结束
		 * */
		public static function setActivityOver ($id)
		{
			if (!$id) {
				return false;
			}
			$activity = WorkPublicActivity::findOne($id);
			if (empty($activity)) {
				return false;
			}
			if ($activity->config_del == 0) {
				WorkPublicActivityFansUser::DelActivityConfig($activity);
			}

			if ($activity->end_time < time() || $activity->is_over == 2) {
				return false;
			}
			if ($activity->is_over == 3) {
				return false;
			}

			$activity->is_over = 4;
			$activity->save();

			return true;
		}
	}
