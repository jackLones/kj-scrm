<?php

	namespace app\models;

	use app\queue\SyncAwardJob;
	use app\queue\SyncWorkAddTagJob;
	use Yii;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use app\components\InvalidDataException;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;

	/**
	 * This is the model class for table "{{%awards_activity}}".
	 *
	 * @property int             $id
	 * @property int             $uid                      账户id
	 * @property int             $corp_id                  授权的企业ID
	 * @property int             $agent_id                 应用id
	 * @property string          $config_id                联系方式的配置id
	 * @property string          $qr_code                  联系二维码的URL
	 * @property string          $state                    企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
	 * @property string          $welcome                  欢迎语
	 * @property string          $user_key                 引流成员
	 * @property string          $user                     用户userID列表
	 * @property int             $sub_id                   子账户id
	 * @property string          $title                    活动名称
	 * @property int             $status                   状态：0 未开启 1 进行中 2 已结束（到期结束） 3 已结束（奖品无库存结束） 4 已结束（手动提前结束）
	 * @property string          $start_time               开始时间
	 * @property string          $end_time                 结束时间
	 * @property int             $part_num                 参数人数
	 * @property int             $visitor_num              访问量
	 * @property string          $description              活动说明
	 * @property int             $style                    1 梦幻紫 2 喜庆红
	 * @property string          $pic_rule                 图片规则
	 * @property string          $poster_path              海报地址
	 * @property string          $share_title              分享标题
	 * @property int             $init_num                 初始次数
	 * @property string          $apply_setting            参与设置
	 * @property string          $award_setting            中奖设置
	 * @property string          $share_setting            分享设置
	 * @property int             $prize_send_type          奖品发放类型：1、活动期间，2、活动结束
	 * @property int             $sex_type                 性别类型：1、不限制，2、男性，3、女性，4、未知
	 * @property int             $area_type                区域类型：1、不限制，2、部分地区
	 * @property string          $area_data                区域数据
	 * @property string          $tag_ids                  给客户打的标签
	 * @property int             $is_del                   0未删除1已删除
	 * @property string          $create_time              创建时间
	 * @property string          $update_time              修改时间
	 * @property int             $tags_local               标签位置1总2奖品
	 * @property string          $success_tags             完成后打上指定标签
	 * @property string          $is_share_open            是否开启分享设置
	 *
	 * @property AwardsJoin[]    $awardsJoins
	 * @property AwardsList[]    $awardsLists
	 * @property AwardsRecords[] $awardsRecords
	 */
	class AwardsActivity extends \yii\db\ActiveRecord
	{
		const THANKS = '谢谢参与';
		const THANKS_LOGO = '/upload/raffle/message.png';
		const REDPACK_LOGO = '/upload/raffle/red_pack.png';

		const AWARD_HEAD = 'award';
		const H5_URL = '/h5/pages/raffle/index';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%awards_activity}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'agent_id', 'sub_id', 'status', 'part_num', 'visitor_num', 'style', 'init_num', 'prize_send_type', 'sex_type', 'area_type', 'is_del', 'tags_local'], 'integer'],
				[['welcome', 'user_key', 'user', 'description', 'pic_rule', 'area_data'], 'string'],
				[['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
				[['config_id', 'state'], 'string', 'max' => 64],
				[['qr_code', 'success_tags'], 'string', 'max' => 255],
				[['title'], 'string', 'max' => 50],
				[['poster_path', 'share_title', 'apply_setting', 'award_setting', 'share_setting'], 'string', 'max' => 100],
				[['tag_ids'], 'string', 'max' => 250],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => 'ID',
				'uid'             => '账户id',
				'corp_id'         => '授权的企业ID',
				'agent_id'        => '应用id',
				'config_id'       => '联系方式的配置id',
				'qr_code'         => '联系二维码的URL',
				'state'           => '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值',
				'welcome'         => '欢迎语',
				'user_key'        => '引流成员',
				'user'            => '用户userID列表',
				'sub_id'          => '子账户id',
				'title'           => '活动名称',
				'status'          => '状态：0 未开启 1 进行中 2 已结束',
				'start_time'      => '开始时间',
				'end_time'        => '结束时间',
				'part_num'        => '参数人数',
				'visitor_num'     => '访问量',
				'description'     => '活动说明',
				'style'           => '1 梦幻紫 2 喜庆红',
				'poster_path'     => '海报地址',
				'init_num'        => '初始次数',
				'share_title'     => '分享标题',
				'apply_setting'   => '参与设置',
				'award_setting'   => '中奖设置',
				'share_setting'   => '分享设置',
				'prize_send_type' => '奖品发放类型：1、活动期间，2、活动结束',
				'sex_type'        => '性别类型：1、不限制，2、男性，3、女性，4、未知',
				'area_type'       => '区域类型：1、不限制，2、部分地区',
				'area_data'       => '区域数据',
				'tag_ids'         => '给客户打的标签',
				'is_del'          => '0未删除1已删除',
				'create_time'     => '创建时间',
				'update_time'     => '修改时间',
				'tags_local'      => '标签位置1总2奖品',
				'success_tags'    => '完成后打上指定标签',
				'is_share_open'   => '是否开启分享设置',
			];
		}
		/**
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->welcome = rawurlencode(rawurldecode($this->welcome));

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->welcome)) {
				$this->welcome = rawurldecode($this->welcome);
			}

			parent::afterFind(); // TODO: Change the autogenerated stub
		}
		/**
		 *
		 * @return array
		 */
		public function dumpData ()
		{
			$user = '';
			if (!empty($this->user)) {
				$newUser  = json_decode($this->user, true);
				$userName = WorkUser::find()->where(['in', 'userid', $newUser])->andWhere(['corp_id' => $this->corp_id])->asArray()->all();
				if (!empty($userName)) {
					$name = array_column($userName, 'name');
					$user = $name;
				}
			}
			$userKeyArr = json_decode($this->user_key, 1);
			if (!empty($userKeyArr)) {
				foreach ($userKeyArr as $key => &$user) {
					if (!isset($user["title"])) {
						$workUser = WorkUser::findOne($user['id']);
						if (!empty($workUser)) {
							$user['title'] = $workUser->name;
						}
						$user["scopedSlots"] = ["title" => "custom"];
						$user["key"]         = $user["user_key"];
					}
				}
			}

			$corp_id   = '';
			$corp_name = '';
			$workCorp  = WorkCorp::findOne($this->corp_id);
			if (!empty($workCorp)) {
				$corp_id   = $workCorp->corpid;
				$corp_name = $workCorp->corp_name;
			}
			$reason_str = '';
			switch ($this->status) {
				case 0:
					$status_str = '未开始';
					break;
				case 1:
					$status_str = '进行中';
					break;
				case 2:
					$status_str = '已结束';
					$reason_str = '到期结束';
					break;
				case 3:
					$status_str = '已结束';
					$reason_str = '奖品无库存结束';
					break;
				case 4:
					$status_str = '已结束';
					$reason_str = '手动提前结束';
					break;
				default:
					$status_str = '已删除';
			}
			$status = $this->status;
			if ($status != 0 && $status != 1) {
				$status = 2;
			}
			$picRule = json_decode($this->pic_rule, 1);
			if (empty($picRule['is_avatar'])) {
				$picRule['is_avatar'] = false;
			} else {
				$picRule['is_avatar'] = true;
			}
			if (empty($picRule['is_nickname'])) {
				$picRule['is_nickname'] = false;
			} else {
				$picRule['is_nickname'] = true;
			}
			$corpAgent = WorkCorpAgent::findOne($this->agent_id);
			$count     = AwardsJoin::find()->where(['award_id' => $this->id])->count();
			$web_url   = \Yii::$app->params['web_url'];
			$url       = $web_url . static::H5_URL . '?corp_id=' . $this->corp_id . '&corpid=' . $workCorp->corpid . '&agent_id=' . $this->agent_id . '&assist=' . $this->state;
			if (!empty($corpAgent) && $corpAgent->agent_type == WorkCorpAgent::AUTH_AGENT) {
				$url .= '&suite_id=' . $corpAgent->suite->suite_id;
			}
			$result    = [
				'key'             => $this->id,
				'id'              => $this->id,
				'corp_id'         => $corp_id,
				'corp_name'       => $corp_name,
				'agent_id'        => $this->agent_id,
				'init_num'        => $this->init_num,
				'title'           => $this->title,
				'start_time'      => substr($this->start_time, 0, 16),
				'end_time'        => substr($this->end_time, 0, 16),
				'visitor_num'     => $this->visitor_num,
				'qr_code'         => $url,
				'part_num'        => $count,
				'status'          => $status,
				'create_time'     => $this->create_time,
				'description'     => $this->description,
				'picRule'         => $picRule,
				'poster_path'     => $this->poster_path,
				'share_title'     => $this->share_title,
				'tags_local'      => empty($this->tags_local) ? 1 : $this->tags_local,
				'success_tags'    => empty($this->success_tags) ? [] : json_decode($this->success_tags),
				'status_str'      => $status_str,
				'reason_str'      => $reason_str,
				'user'            => $user,
				'user_key'        => $userKeyArr,
				'welcome'         => json_decode($this->welcome, 1),
				'apply_setting'   => json_decode($this->apply_setting, true),
				'award_setting'   => json_decode($this->award_setting, true),
				'share_setting'   => json_decode($this->share_setting, true),
				'prize_send_type' => $this->prize_send_type,
				'sex_type'        => $this->sex_type,
				'area_type'       => $this->area_type,
				'is_share_open'   => (boolean) $this->is_share_open,
				'area_data'       => json_decode($this->area_data, true),
				'tag_ids'         => !empty($this->tag_ids) ? explode(',', $this->tag_ids) : [],
			];
			$awardList = AwardsList::find()->andWhere(['award_id' => $this->id])->asArray()->all();
			foreach ($awardList as $key => $list) {
				$awardList[$key]['prize_type'] = intval($list['prize_type']);
				if (!empty($list['prize_type'])) {
					$awardList[$key]['logo']        = '';
					$awardList[$key]['description'] = '';
				}
				if (empty($list['success_tags'])) {
					$awardList[$key]['success_tags'] = [];
				} else {
					$awardList[$key]['success_tags'] = json_decode($list['success_tags']);
				}
			}
			$result['content'] = $awardList;

			return $result;
		}

		/**
		 * @param $data
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function add ($data)
		{
			$user    = '';
			$is_edit = 1;
			if (empty($data['id'])) {
				$awardActivity              = new AwardsActivity();
				$awardActivity->part_num    = 0;
				$awardActivity->visitor_num = 0;
				$awardActivity->create_time = DateUtil::getCurrentTime();
			} else {
				$awardActivity              = AwardsActivity::findOne($data['id']);
				$awardActivity->update_time = DateUtil::getCurrentTime();
				$user                       = $awardActivity->user;
				if ($awardActivity->status > 0) {
					$is_edit = 0;
				}
			}
			\Yii::error($data, '$data');
			$back_pic_url = $data['back_pic_url'];
			$is_avatar    = $data['is_avatar'];
			$avatar       = $data['avatar'];
			$nickName     = $data['nickName'];
			$qrCode       = $data['qrCode'];
			$shape        = $data['shape'];
			$color        = $data['color'];
			$align        = $data['align'];
			$font_size    = $data['font_size'];
			$is_nickname  = $data['is_nickname'];
			//基础设置
			$pic_rule                = [
				'back_pic_url' => $back_pic_url,
				'is_avatar'    => $is_avatar,
				'avatar'       => $avatar,
				'shape'        => $shape,
				'is_nickname'  => $is_nickname,
				'nickName'     => $nickName,
				'qrCode'       => $qrCode,
				'color'        => $color,
				'font_size'    => $font_size,
				'align'        => $align
			];
			$awardActivity->pic_rule = json_encode($pic_rule, JSON_UNESCAPED_UNICODE);;

			$awardActivity->init_num        = $data['init_num'];
			$awardActivity->corp_id         = $data['corp_id'];
			$awardActivity->agent_id        = $data['agent_id'];
			$awardActivity->uid             = $data['uid'];
			$awardActivity->title           = $data['title'];
			$awardActivity->start_time      = $data['start_time'];
			$awardActivity->end_time        = $data['end_time'];
			$awardActivity->description     = $data['description'];
			$awardActivity->share_title     = $data['share_title'];
			$awardActivity->style           = $data['style'];
			$awardActivity->poster_path     = $data['poster_path'];
			$awardActivity->apply_setting   = json_encode($data['apply_setting']);
			$awardActivity->award_setting   = json_encode($data['award_setting']);
			$awardActivity->prize_send_type = $data['prize_send_type'];
			$awardActivity->sex_type        = $data['sex_type'];
			$awardActivity->area_type       = $data['area_type'];
			$awardActivity->area_data       = json_encode($data['area_data'], JSON_UNESCAPED_UNICODE);
			$awardActivity->tag_ids         = $data['tag_ids'];
			$awardActivity->tags_local      = $data['tags_local'];
			$awardActivity->success_tags    = json_encode($data['success_tags']);
			$awardActivity->is_share_open   = intval($data['is_share_open']);
			if (!empty($data['is_share_open'])) {
				$awardActivity->share_setting = json_encode($data['share_setting']);
			} else {
				$awardActivity->share_setting = json_encode([['total_num' => 1], ['limit' => '1', 'day_num' => 1]]);
			}
			$awardActivity->user_key = json_encode($data['user_key']);
			$awardActivity->user     = json_encode($data['userId']);
			//欢迎语
			$welcome                = [
				'text_content'     => $data['text_content'],
				'link_start_title' => $data['link_start_title'],
				'link_end_title'   => $data['link_end_title'],
				'link_desc'        => $data['link_desc'],
				'link_pic_url'     => $data['link_pic_url']
			];
			$awardActivity->welcome = json_encode($welcome, JSON_UNESCAPED_UNICODE);
			$userIdJson             = json_encode($data['userId']);
			$is_change              = 0;
			if (!empty($id)) {
				$is_change = ($user == $userIdJson) ? 0 : 1;
			}
			$transaction = \Yii::$app->db->beginTransaction();
			try {
//			$web_url                = \Yii::$app->params['web_url'];
//			$workCorp               = WorkCorp::findOne($awardActivity->corp_id);
//			$url                    = $web_url . '/h5/pages/fission/index?corp_id=' . $awardActivity->corp_id . '&corpid=' . $workCorp->corpid . '&agent_id=' . $awardActivity->agent_id . '&assist=' . $state;
//			$awardActivity->qr_code = $url;
//			$awardActivity->state   = $state;
				if (!$awardActivity->validate() || !$awardActivity->save()) {
					throw new InvalidDataException($awardActivity . SUtils::modelError($awardActivity));
				}

				if (empty($id)) {
					$configArr                = static::addConfigId($awardActivity);
					$awardActivity->config_id = $configArr['config_id'];
					$awardActivity->qr_code   = $configArr['qr_code'];
					$awardActivity->state     = self::AWARD_HEAD . '_' . $awardActivity->id . '_0';
					if (!$awardActivity->validate() || !$awardActivity->save()) {
						throw new InvalidDataException("创建失败" . SUtils::modelError($awardActivity));
					}
				} elseif (!empty($is_change)) {
					static::updateConfigId($awardActivity);
				}

				if (!empty($data['content']) && !empty($is_edit)) {
					$content = $data['content'];
					AwardsList::setAwardList($content, $awardActivity->id);
				}
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return $awardActivity->id;

		}

		/**
		 * @param        $awardActivity
		 * @param string $state
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function addConfigId ($awardActivity, $state = '')
		{
			if (empty($state)) {
				$state = self::AWARD_HEAD . '_' . $awardActivity->id . '_0';
			}
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => $state,
				'user'        => json_decode($awardActivity->user, 1),
				'party'       => [],
			];
			try {
				$workApi = WorkUtils::getWorkApi($awardActivity->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECAddContactWay($sendData);
					\Yii::error($wayResult, 'wayResult-1');
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
				}
				if (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				}
				if (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				}
				if (strpos($message, '40096') !== false) {
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
		 * @param $awardActivity
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function updateConfigId ($awardActivity)
		{
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => self::AWARD_HEAD . '_' . $awardActivity->id . '_0_0',
				'user'        => json_decode($awardActivity->user, 1),
				'party'       => [],
				'config_id'   => $awardActivity->config_id,
			];
			try {
				$workApi = WorkUtils::getWorkApi($awardActivity->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					\Yii::error($contactWayInfo, '$contactWayInfo');
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECUpdateContactWay($sendData);
					\Yii::error($wayResult, 'wayResult-0');
					//抽奖参与表中的config_id
					$joinList = AwardsJoin::find()->select('config_id,state')->where(['award_id' => $awardActivity->id])->all();
					if (!empty($joinList)) {
						foreach ($joinList as $join) {
							if (!empty($join->config_id)) {
								$contactWayInfo['config_id'] = $join->config_id;
								$contactWayInfo['state']     = $join->state;
								$sendData                    = ExternalContactWay::parseFromArray($contactWayInfo);
								$workApi->ECUpdateContactWay($sendData);
							}
						}
					}
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				}
				if (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				}
				if (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40100') !== false) {
					$message = '用户的外部联系人已经在转移流程中';
				}
				throw new InvalidDataException($message);
			}

			return [];
		}

		/**
		 * @param $awardId
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function deleteConfigId ($awardId)
		{
			$award = AwardsActivity::findOne($awardId);
			if (!empty($award->config_id)) {
				$workApi = WorkUtils::getWorkApi($award->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					try {
						$workApi->ECDelContactWay($award->config_id);
					} catch (\Exception $e) {

					}
				}
				$records = AwardsJoin::find()->where(['award_id' => $awardId])->andWhere(['!=', 'config_id', ''])->all();
				if (!empty($records)) {
					foreach ($records as $record) {
						if (!empty($workApi)) {
							try {
								$workApi->ECDelContactWay($record->config_id);
							} catch (\Exception $e) {

							}
						}
					}
				}
			}

			return true;
		}

		/**
		 * @param $content
		 * @param $join_id
		 * @param $activity_id
		 *
		 * @return array
		 *
		 */
		public static function getAwards ($content, $join_id = 0, $activity_id = 0)
		{
			$total_num = 0;//总中奖次数
			$day_num   = 0;//单日中奖次数
			$isRecord  = 0;//1 代表当前不能中奖
			if (!empty($activity_id) && !empty($join_id)) {
				$awardAct = AwardsActivity::findOne($activity_id);
				if (!empty($awardAct['award_setting'])) {
					$award_setting = json_decode($awardAct['award_setting'], true);
					$total_num     = $award_setting[0]['total_num'];
					$day_num       = $award_setting[1]['day_num'];
				}
				if ($day_num > 0 && !empty($award_setting[0]['limit'])) {
					$dayStart = date('Y-m-d') . ' 00:00:00';
					$dayEnd   = date('Y-m-d') . ' 23:59:59';
					$nowCount = AwardsRecords::find()->andWhere(['join_id' => $join_id, 'award_id' => $activity_id, 'is_record' => 1])->andFilterWhere(['between', 'create_time', $dayStart, $dayEnd])->count();
					if ($nowCount >= $day_num) {
						//当日中奖次数大于设置的单日中奖次数
						$isRecord = 1;
					}
				}
				if ($total_num > 0 && !empty($award_setting[0]['limit'])) {
					$nowCount = AwardsRecords::find()->andWhere(['join_id' => $join_id, 'award_id' => $activity_id, 'is_record' => 1])->count();
					if ($nowCount >= $total_num) {
						//当前中奖次数大于设置的总中奖次数
						$isRecord = 1;
					}
				}

			}
			\Yii::error($isRecord, '$isRecord');
			$awards = [];
			$count  = count($content);
			switch ($count) {
				case 1:
					if (empty($content[0]['last_num'])) {
						$content[0]['percentage'] = 0;
					}
					if ($isRecord == 1) {
						$content[0]['percentage'] = 0;
					}
					$percent = 100 - $content[0]['percentage'];
					//$percent = round((100 - $content[0]['percentage']) / 3, 2);
					if ($percent == 0) {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 2,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							]
						];
					} else {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 2,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							]
						];
					}
					break;

				case 2:
					if (empty($content[0]['last_num'])) {
						$content[0]['percentage'] = 0;
					}
					if (empty($content[1]['last_num'])) {
						$content[1]['percentage'] = 0;
					}
					if ($isRecord == 1) {
						$content[0]['percentage'] = $content[1]['percentage'] = 0;
					}
					//$percent = round((100 - ($content[0]['percentage'] + $content[1]['percentage'])) / 2, 2);
					$percent = 100 - ($content[0]['percentage'] + $content[1]['percentage']);
					if ($percent == 0) {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
						];
					} else {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 3,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							],
						];
					}
					break;
				case 3:
					if (empty($content[0]['last_num'])) {
						$content[0]['percentage'] = 0;
					}
					if (empty($content[1]['last_num'])) {
						$content[1]['percentage'] = 0;
					}
					if (empty($content[2]['last_num'])) {
						$content[2]['percentage'] = 0;
					}
					if ($isRecord == 1) {
						$content[0]['percentage'] = $content[1]['percentage'] = $content[2]['percentage'] = 0;
					}
					//$percent = round((100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'])) / 3, 2);
					$percent = 100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage']);
					if ($percent == 0) {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
						];
					} else {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 4,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							],
						];
					}

					break;
				case 4:
					if (empty($content[0]['last_num'])) {
						$content[0]['percentage'] = 0;
					}
					if (empty($content[1]['last_num'])) {
						$content[1]['percentage'] = 0;
					}
					if (empty($content[2]['last_num'])) {
						$content[2]['percentage'] = 0;
					}
					if (empty($content[3]['last_num'])) {
						$content[3]['percentage'] = 0;
					}
					if ($isRecord == 1) {
						$content[0]['percentage'] = $content[1]['percentage'] = $content[2]['percentage'] = $content[3]['percentage'] = 0;
					}
					//$percent = round((100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'] + $content[3]['percentage'])) / 2, 2);
					$percent = 100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'] + $content[3]['percentage']);
					if ($percent == 0) {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => intval($content[3]['id']),
								'key'   => 4,
								'name'  => $content[3]['name'],
								'image' => $content[3]['logo'],
								'per'   => $content[3]['percentage'],
							],
						];
					} else {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],

							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => intval($content[3]['id']),
								'key'   => 4,
								'name'  => $content[3]['name'],
								'image' => $content[3]['logo'],
								'per'   => $content[3]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 5,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							],
						];
					}

					break;
				case 5:
					if (empty($content[0]['last_num'])) {
						$content[0]['percentage'] = 0;
					}
					if (empty($content[1]['last_num'])) {
						$content[1]['percentage'] = 0;
					}
					if (empty($content[2]['last_num'])) {
						$content[2]['percentage'] = 0;
					}
					if (empty($content[3]['last_num'])) {
						$content[3]['percentage'] = 0;
					}
					if (empty($content[4]['last_num'])) {
						$content[4]['percentage'] = 0;
					}
					if ($isRecord == 1) {
						$content[0]['percentage'] = $content[1]['percentage'] = $content[2]['percentage'] = $content[3]['percentage'] = $content[4]['percentage'] = 0;
					}
					//$percent = round((100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'] + $content[3]['percentage'] + $content[4]['percentage'])) / 3, 2);
					$percent = 100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'] + $content[3]['percentage'] + $content[4]['percentage']);
					if ($percent == 0) {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => intval($content[3]['id']),
								'key'   => 4,
								'name'  => $content[3]['name'],
								'image' => $content[3]['logo'],
								'per'   => $content[3]['percentage'],
							],
							[
								'id'    => intval($content[4]['id']),
								'key'   => 5,
								'name'  => $content[4]['name'],
								'image' => $content[4]['logo'],
								'per'   => $content[4]['percentage'],
							],
						];
					} else {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => intval($content[3]['id']),
								'key'   => 4,
								'name'  => $content[3]['name'],
								'image' => $content[3]['logo'],
								'per'   => $content[3]['percentage'],
							],
							[
								'id'    => intval($content[4]['id']),
								'key'   => 5,
								'name'  => $content[4]['name'],
								'image' => $content[4]['logo'],
								'per'   => $content[4]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 6,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							],
						];
					}
					break;
				case 6:
					if (empty($content[0]['last_num'])) {
						$content[0]['percentage'] = 0;
					}
					if (empty($content[1]['last_num'])) {
						$content[1]['percentage'] = 0;
					}
					if (empty($content[2]['last_num'])) {
						$content[2]['percentage'] = 0;
					}
					if (empty($content[3]['last_num'])) {
						$content[3]['percentage'] = 0;
					}
					if (empty($content[4]['last_num'])) {
						$content[4]['percentage'] = 0;
					}
					if (empty($content[5]['last_num'])) {
						$content[5]['percentage'] = 0;
					}
					if ($isRecord == 1) {
						$content[0]['percentage'] = $content[1]['percentage'] = $content[2]['percentage'] = $content[3]['percentage'] = $content[4]['percentage'] = $content[5]['percentage'] = 0;
					}
					//$percent = round((100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'] + $content[3]['percentage'] + $content[4]['percentage'] + $content[5]['percentage'])) / 2, 2);
					$percent = 100 - ($content[0]['percentage'] + $content[1]['percentage'] + $content[2]['percentage'] + $content[3]['percentage'] + $content[4]['percentage'] + $content[5]['percentage']);
					if ($percent == 0) {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => intval($content[3]['id']),
								'key'   => 4,
								'name'  => $content[3]['name'],
								'image' => $content[3]['logo'],
								'per'   => $content[3]['percentage'],
							],
							[
								'id'    => intval($content[4]['id']),
								'key'   => 5,
								'name'  => $content[4]['name'],
								'image' => $content[4]['logo'],
								'per'   => $content[4]['percentage'],
							],
							[
								'id'    => intval($content[5]['id']),
								'key'   => 6,
								'name'  => $content[5]['name'],
								'image' => $content[5]['logo'],
								'per'   => $content[5]['percentage'],
							],
						];
					} else {
						$awards = [
							[
								'id'    => intval($content[0]['id']),
								'key'   => 1,
								'name'  => $content[0]['name'],
								'image' => $content[0]['logo'],
								'per'   => $content[0]['percentage'],
							],
							[
								'id'    => intval($content[1]['id']),
								'key'   => 2,
								'name'  => $content[1]['name'],
								'image' => $content[1]['logo'],
								'per'   => $content[1]['percentage'],
							],
							[
								'id'    => intval($content[2]['id']),
								'key'   => 3,
								'name'  => $content[2]['name'],
								'image' => $content[2]['logo'],
								'per'   => $content[2]['percentage'],
							],
							[
								'id'    => intval($content[3]['id']),
								'key'   => 4,
								'name'  => $content[3]['name'],
								'image' => $content[3]['logo'],
								'per'   => $content[3]['percentage'],
							],
							[
								'id'    => intval($content[4]['id']),
								'key'   => 5,
								'name'  => $content[4]['name'],
								'image' => $content[4]['logo'],
								'per'   => $content[4]['percentage'],
							],
							[
								'id'    => intval($content[5]['id']),
								'key'   => 6,
								'name'  => $content[5]['name'],
								'image' => $content[5]['logo'],
								'per'   => $content[5]['percentage'],
							],
							[
								'id'    => 0,
								'key'   => 7,
								'name'  => static::THANKS,
								'image' => static::THANKS_LOGO,
								'per'   => $percent,
							],
						];
					}
					break;
			}

			return $awards;
		}

		/**
		 * @param $proArr
		 *
		 * @return int|string
		 *
		 */
		public static function get_rand ($proArr)
		{
			$result = '';
			//概率是数组的总概率精度
			$proSum = array_sum($proArr); //对数组中所有值求和
			$proSum = intval($proSum * 100);
			//概率数组循环
			foreach ($proArr as $key => $proCur) {
				$randNum = mt_rand(1, $proSum);
				$proCur = intval($proCur * 100);
				if ($randNum <= $proCur) {
					$result = $key;
					break;
				} else {
					$proSum -= $proCur;
				}
			}
			unset($proArr);

			return $result;
		}

		/**
		 * 中奖结果
		 *
		 * @param $join_id
		 * @param $activity_id
		 * @param $isContact
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 */
		public static function getChance ($join_id, $activity_id)
		{
			$awardJoin = AwardsJoin::findOne($join_id);
			if (empty($awardJoin->num)) {
				throw new InvalidDataException("抽奖次数已用完，无法抽奖");
			}
			$dayStart      = date('Y-m-d') . ' 00:00:00';
			$dayEnd        = date('Y-m-d') . ' 23:59:59';
			$nowCount      = AwardsRecords::find()->andWhere(['join_id' => $join_id, 'award_id' => $activity_id])->andFilterWhere(['between', 'create_time', $dayStart, $dayEnd])->count();
			$totalCount    = AwardsRecords::find()->andWhere(['join_id' => $join_id, 'award_id' => $activity_id])->count();
			$activity      = AwardsActivity::findOne($activity_id);
			$apply_setting = json_decode($activity->apply_setting, true);
			$totalNum      = $apply_setting[0]['total_num'];//总次数
			$dayNum        = $apply_setting[1]['day_num'];//单日次数
			if ($nowCount >= $dayNum) {
				throw new InvalidDataException("今日已达抽奖上限次数，请明天再来");
			}
			if ($totalCount >= $totalNum && !empty($apply_setting[0]['limit'])) {
				throw new InvalidDataException("当前抽奖次数已达上限");
			}
			$content   = AwardsList::find()->where(['award_id' => $activity_id])->asArray()->all();
			$is_record = 0;
			foreach ($content as $con) {
				if (!empty($con['last_num'])) {
					$is_record = 1;
				}
			}
			if (empty($is_record)) {
				if (!empty($activity)) {
					$activity->status = 3;
					$activity->save();
					\Yii::$app->queue->push(new SyncAwardJob([
						'award_id'     => $activity->id,
						'award_status' => 3
					]));
				}
				throw new InvalidDataException("奖品无库存，不能抽奖");
			}

			$prize = static::getAwards($content, $join_id, $activity_id);
			$arr   = [];
			foreach ($prize as $key => $val) {
				$arr[$val['key']] = $val['per'];
			}
			if (!empty($arr)) {
				$rid   = static::get_rand($arr);//根据概率获取奖项id
				$binGo = isset($prize[$rid - 1]) ? $prize[$rid - 1]['id'] : 0;//中奖项 0 代表谢谢参与
			}
			\Yii::error($binGo, '$binGo');
			$join      = AwardsJoin::findOne($join_id);
			$nick_name = $join->nick_name;
			$avatar    = $join->avatar;
			if (!empty($join)) {
				$num             = $join->num - 1;
				$join->num       = $num;
				$avatar          = $join->avatar;
				$nick_name       = $join->nick_name;
				$join->last_time = DateUtil::getCurrentTime();
				$join->save();
			}
			$newRecord = new AwardsRecords();
			if (!empty($binGo)) {
				$award = AwardsList::findOne($binGo);
				if (!empty($award)) {
					$last_num = $award->last_num - 1;
					if ($last_num < 0) {
						$last_num = 0;
					}
					$award->last_num = $last_num;
					$award->save();
					$newRecord->award_name = $award->name;
					$newRecord->is_record  = 1;
					self::setTags($activity, $join, $binGo);
				}
			}
			$newRecord->aid         = $binGo;
			$newRecord->nick_name   = $nick_name;
			$newRecord->avatar      = $avatar;
			$newRecord->join_id     = $join_id;
			$newRecord->award_id    = $activity_id;
			$newRecord->create_time = DateUtil::getCurrentTime();
			if (!$newRecord->validate() || !$newRecord->save()) {
				throw new InvalidDataException(SUtils::modelError($newRecord));
			}

			if (($activity->prize_send_type == 1) && !empty($newRecord->is_record) && !empty($award) && $award->prize_type == 1) {
				$remark   = '恭喜中奖，' . $award->amount . '元红包拿走，不谢~~~';
				$sendData = [
					'uid'         => $activity->uid,
					'corp_id'     => $activity->corp_id,
					'rid'         => $activity->id,
					'jid'         => $join->id,
					'hid'         => $newRecord->id,
					'external_id' => $join->external_id,
					'openid'      => $join->openid,
					'amount'      => $award->amount,
					'remark'      => $remark,
				];
				\Yii::$app->queue->push(new SyncAwardJob([
					'award_id'     => $activity->id,
					'award_status' => -1,
					'sendData'     => $sendData,
				]));
			}

			//中奖数据
			$records = AwardsRecords::getRecords($activity_id);
			$join    = AwardsJoin::findOne($join_id);
			$chance  = $join->num;
			$data    = [];
			if (empty($binGo)) {
				$data['id']              = 0;
				$data['name']            = static::THANKS;
				$data['logo']            = static::THANKS_LOGO;
				$data['prize_type']      = 0;
				$data['prize_send_type'] = 0;
				$data['end_time']        = '';
				$data['amount']          = 0;
			} else {
				$data['id']              = $binGo;
				$data['name']            = $award->name;
				$data['logo']            = $award->logo;
				$awardInfo               = AwardsList::findOne($binGo);
				$data['prize_type']      = !empty($awardInfo) ? $awardInfo->prize_type : 0;
				$data['amount']          = !empty($awardInfo) ? $awardInfo->amount : 0;
				$data['prize_send_type'] = $activity->prize_send_type;
				$data['end_time']        = substr($activity->end_time, 0, 16);
			}
			$info = [
				'info'    => $data,
				'chance'  => $chance,
				'records' => $records,
			];
			\Yii::error($info, '$info');

			return $info;

		}

		/**
		 * Title: setTags
		 * User: sym
		 * Date: 2020/12/25
		 * Time: 16:35
		 *
		 * @param AwardsActivity $awardActivity
		 * @param AwardsJoin     $join
		 * @param                $binGo
		 *
		 * @remark
		 */
		public static function setTags ($awardActivity, $join, $binGo = 0)
		{
			$userKeys = !empty($awardActivity->user_key) ? json_decode($awardActivity->user_key, true) : [];
			if (!empty($userKeys)) {
				$users      = array_column($userKeys, "id");
				$followId   = [];
				$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $join->external_id, 'user_id' => $users])->asArray()->all();
				if (!empty($followUser)) {
					$followId = array_column($followUser, "id");
				}
				$tag_ids = [];
				if ($awardActivity->tags_local == 1) {
					$tag_ids = empty($awardActivity->success_tags) ? [] : json_decode($awardActivity->success_tags, true);
				} else {
					$award = AwardsList::findOne($binGo);
					if (!empty($award)) {
						$tag_ids = empty($award->success_tags) ? [] : json_decode($award->success_tags, true);
					}
				}
				if (empty($tag_ids)) {
					return;
				}
				if (!empty($join->tags)) {
					$joinInfoTags = explode(",", $join->tags);
					$tag_ids      = array_diff($tag_ids, $joinInfoTags);
					$TempTags     = array_merge($joinInfoTags, $tag_ids);
					$join->tags   = implode(",", $TempTags);
				}
				if (empty($join->tags)) {
					$join->tags = is_array($tag_ids) ? implode(",", $tag_ids) : NULL;
				}
				$join->save();

				if (!empty($tag_ids)) {
					\Yii::$app->queue->push(new SyncWorkAddTagJob([
						'type'      => 2,
						'user_ids'  => $followId,
						'tag_ids'   => $tag_ids,
						'otherData' => ['type' => 'fission', 'msg' => '抽奖引流【' . $awardActivity->title . '】完成'],
					]));
				}
			}
		}

		/**
		 * @param $activity_id
		 * @param $externalId
		 *
		 * @return int|string
		 *
		 */
		public static function chance ($activity_id, $externalId)
		{
			$chance        = 0;
			$awardActivity = AwardsActivity::findOne($activity_id);
			$apply_setting = json_decode($awardActivity->apply_setting, true);
			$total_num     = $apply_setting[0]['total_num'];//总次数
			$day_num       = $apply_setting[1]['day_num'];//单日参与次数
			$awardJoin     = AwardsJoin::findOne(['award_id' => $activity_id, 'external_id' => $externalId]);
			if (!empty($awardJoin->num)) {
				$dayStart   = date('Y-m-d') . ' 00:00:00';
				$dayEnd     = date('Y-m-d') . ' 23:59:59';
				$nowCount   = AwardsRecords::find()->andWhere(['join_id' => $awardJoin->id, 'award_id' => $activity_id])->andFilterWhere(['between', 'create_time', $dayStart, $dayEnd])->count();
				$totalCount = AwardsRecords::find()->andWhere(['join_id' => $awardJoin->id, 'award_id' => $activity_id])->count();
				if ($day_num > $nowCount) {
					$chance = $day_num - $nowCount;//中奖机会
				}
				if (!empty($total_num) && $totalCount >= $total_num) {
					$chance = 0;
				}
			}

			return $chance;
		}

		//活动结束时处理数据
		public static function handleData ($award, $status = 0)
		{
			/** @var AwardsActivity $award * */
			if (empty($award)) {
				return '';
			}

			if (!empty($status)) {
				//删除config
				static::deleteConfigId($award->id);
				//更改状态
				$award->status = $status;
				$award->update();
			}

			//发送红包
			$recordList = AwardsRecords::find()->alias('ar');
			$recordList = $recordList->leftJoin('{{%awards_list}} al', 'ar.aid=al.id');
			$recordList = $recordList->where(['ar.award_id' => $award->id, 'ar.is_record' => 1, 'ar.status' => 0, 'al.prize_type' => 1]);
			$recordList = $recordList->select('ar.id,ar.award_id,ar.join_id,al.amount');
			$recordList = $recordList->asArray()->all();
			if (!empty($recordList)) {
				$is_send = 0;
				foreach ($recordList as $record) {
					try {
						$amount     = $record['amount'];
						$remark     = '恭喜中奖，' . $amount . '元红包拿走，不谢~~~';
						$awardJoin  = AwardsJoin::findOne($record['join_id']);
						$recordInfo = AwardsRecords::findOne($record['id']);
						if (empty($awardJoin) || empty($recordInfo)) {
							continue;
						}

						$sendData = [
							'uid'         => $award->uid,
							'corp_id'     => $award->corp_id,
							'rid'         => $award->id,
							'jid'         => $record['join_id'],
							'hid'         => $record['id'],
							'external_id' => $awardJoin->external_id,
							'openid'      => $awardJoin->openid,
							'amount'      => $amount,
							'remark'      => $remark,
						];
						$res      = RedPackOrder::sendRedPack($sendData, 3);
						if (!empty($res)) {
							$recordInfo->status = 1;
							$recordInfo->update();
							$is_send = 1;
						}
					} catch (InvalidDataException $e) {
						$is_send = 0;
						\Yii::error($e->getMessage(), 'handSendAward');
						break;
					}
				}

				//补发剩余的
				if (!empty($is_send)) {
					\Yii::$app->queue->delay(10)->push(new SyncAwardJob([
						'award_id' => $award->id,
						'sendData' => ['is_all' => 1, 'uid' => $award->uid]
					]));
				}
			}
		}

		//如果微信支付有钱，补发之前没发的
		public static function supplySend ($uid)
		{
			$cacheSendKey = 'supplySend_award_' . $uid;
			$cacheSend    = \Yii::$app->cache->get($cacheSendKey);
			if (!empty($cacheSend)) {
				return '';
			}
			\Yii::$app->cache->set($cacheSendKey, 1, 600);

			$recordList = AwardsRecords::find()->alias('ar');
			$recordList = $recordList->leftJoin('{{%awards_list}} al', 'ar.aid=al.id');
			$recordList = $recordList->leftJoin('{{%awards_activity}} aa', 'ar.award_id=aa.id');
			$recordList = $recordList->where(['aa.uid' => $uid, 'aa.status' => [2, 3, 4], 'ar.is_record' => 1, 'ar.status' => 0, 'al.prize_type' => 1]);
			$recordList = $recordList->select('ar.id,ar.award_id,ar.join_id,al.amount,aa.corp_id');
			$recordList = $recordList->asArray()->all();
			if (!empty($recordList)) {
				foreach ($recordList as $record) {
					try {
						$amount     = $record['amount'];
						$remark     = '恭喜中奖，' . $amount . '元红包拿走，不谢~~~';
						$awardJoin  = AwardsJoin::findOne($record['join_id']);
						$recordInfo = AwardsRecords::findOne($record['id']);
						if (empty($awardJoin) || empty($recordInfo)) {
							continue;
						}
						$sendData = [
							'uid'         => $uid,
							'corp_id'     => $record['corp_id'],
							'rid'         => $record['award_id'],
							'jid'         => $record['join_id'],
							'hid'         => $record['id'],
							'external_id' => $awardJoin->external_id,
							'openid'      => $awardJoin->openid,
							'amount'      => $amount,
							'remark'      => $remark,
						];
						$res      = RedPackOrder::sendRedPack($sendData, 3);
						if (!empty($res)) {
							$recordInfo->status = 1;
							$recordInfo->update();
						}
					} catch (InvalidDataException $e) {
						\Yii::error($e->getMessage(), 'supplySend_award');
						break;
					}
				}
			}

			\Yii::$app->cache->delete($cacheSendKey);
		}

		/*
		 * 将活动置为结束
		 * */
		public static function setActivityOver ($id)
		{
			if (!$id) {
				return false;
			}
			$award = AwardsActivity::findOne($id);
			if (empty($award)) {
				return false;
			}
			$status        = 4;
			$award->status = $status;
			$award->save();
			if ($status == 4) {
				\Yii::$app->queue->push(new SyncAwardJob([
					'award_id'     => $award->id,
					'award_status' => 4
				]));
			}

			return true;
		}
	}
