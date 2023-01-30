<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\DateUtil;
use app\util\SUtils;
use app\util\WorkUtils;
use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
use dovechen\yii2\weWork\Work;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%work_contact_way_redpacket}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $agent_id 应用ID
 * @property string $name 活动名称
 * @property int $time_type 时间设置1永久有效2时间区间
 * @property string $start_time 开始日期
 * @property string $end_time 结束日期
 * @property int $disabled_time 活动提前结束时间
 * @property int $reserve_day 活动结束后渠道活码保留期（天）
 * @property int $rule_id 红包规则id
 * @property string $rule_text 红包规则内容（非存储规则）
 * @property string $redpacket_amount 活动投放金额
 * @property string $out_amount 活动已发出金额
 * @property string $send_amount 活动已发放领取金额
 * @property int $redpacket_status 红包活动状态1未发布2已发布3已失效(红包活动)4已失效(渠道活码)5已删除
 * @property string $update_time 更新时间
 * @property string $create_time 创建时间
 * @property string $config_id 联系方式的配置id
 * @property string $title 活码名称
 * @property int $type 联系方式类型,1-单人, 2-多人
 * @property int $scene 场景，1-在小程序中联系，2-通过二维码联系
 * @property int $style 在小程序中联系时使用的控件样式，详见附表
 * @property string $remark 联系方式的备注信息，用于助记，不超过30个字符
 * @property int $skip_verify 是否需要验证，1需要 0不需要
 * @property int $verify_all_day 自动验证1全天开启2分时段
 * @property string $state 企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
 * @property string $spare_employee 备用员工
 * @property int $is_welcome_date 欢迎语时段日期 1关 2开
 * @property int $is_welcome_week 欢迎语时段周 1关 2开
 * @property int $is_limit 员工上限 1关 2开
 * @property int $is_del 0：未删除；1：已删除
 * @property string $qr_code 联系二维码的URL
 * @property int $open_date 0关闭1开启
 * @property int $add_num 添加人数
 * @property string $tag_ids 给客户打的标签
 * @property string $user_key 用户选择的key值
 * @property string $content 渠道活码的欢迎语内容
 * @property int $status 渠道活码的欢迎语是否开启0关闭1开启
 * @property int $sync_attachment_id 同步后的素材id
 * @property int $work_material_id 企业微信素材id
 * @property int $groupId 分组id
 * @property int $material_sync 不同步到内容库1同步
 * @property int $attachment_id 内容引擎id
 * @property string $local_path 二维码图片本地地址
 *
 * @property WorkCorp                        $corp
 * @property WorkContactWayDepartment[]      $workContactWayDepartments
 * @property WorkContactWayUser[]            $workContactWayUsers
 * @property WorkExternalContactFollowUser[] $workExternalContactFollowUsers
 */
class WorkContactWayRedpacket extends \yii\db\ActiveRecord
{
	const RED_WAY_NOT_ISSUE = 1;//未发布
	const RED_WAY_ISSUE = 2;//已发布
	const RED_PACKET_DISABLED = 3;//已失效(红包活动)
	const RED_WAY_DISABLED = 4;//已失效(渠道活码)
	const RED_WAY_DEL = 5;//已删除

	const REDPACKET_WAY = "RedWay";
	const H5_URL = '/h5/pages/redForNew/index';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'agent_id', 'time_type', 'rule_id', 'redpacket_status', 'type', 'scene', 'style', 'skip_verify', 'verify_all_day', 'is_welcome_date', 'is_welcome_week', 'is_limit', 'is_del', 'open_date', 'add_num', 'status', 'sync_attachment_id', 'work_material_id', 'groupId', 'material_sync', 'attachment_id', 'disabled_time', 'reserve_day'], 'integer'],
            [['start_time', 'end_time', 'update_time', 'create_time'], 'safe'],
            [['rule_text', 'spare_employee', 'tag_ids', 'content', 'local_path'], 'string'],
            [['redpacket_amount', 'out_amount', 'send_amount'], 'number'],
            [['name', 'qr_code', 'user_key'], 'string', 'max' => 255],
            [['config_id', 'remark', 'state'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 200],
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
			'corp_id'            => Yii::t('app', '授权的企业ID'),
			'agent_id'           => Yii::t('app', '应用ID'),
			'name'               => Yii::t('app', '活动名称'),
			'time_type'          => Yii::t('app', '时间设置1永久有效2时间区间'),
			'start_time'         => Yii::t('app', '开始日期'),
			'end_time'           => Yii::t('app', '结束日期'),
			'disabled_time'      => Yii::t('app', '活动提前结束时间'),
			'reserve_day'        => Yii::t('app', '活动结束后渠道活码保留期（天）'),
			'rule_id'            => Yii::t('app', '红包规则id'),
			'rule_text'          => Yii::t('app', '红包规则内容（非存储规则）'),
			'redpacket_amount'   => Yii::t('app', '活动投放金额'),
			'out_amount'         => Yii::t('app', '活动已发出金额'),
			'send_amount'        => Yii::t('app', '活动已发放领取金额'),
			'redpacket_status'   => Yii::t('app', '红包活动状态1未发布2已发布3已失效(红包活动)4已失效(渠道活码)5已删除'),
			'update_time'        => Yii::t('app', '更新时间'),
			'create_time'        => Yii::t('app', '创建时间'),
			'config_id'          => Yii::t('app', '联系方式的配置id'),
			'title'              => Yii::t('app', '活码名称'),
			'type'               => Yii::t('app', '联系方式类型,1-单人, 2-多人'),
			'scene'              => Yii::t('app', '场景，1-在小程序中联系，2-通过二维码联系'),
			'style'              => Yii::t('app', '在小程序中联系时使用的控件样式，详见附表'),
			'remark'             => Yii::t('app', '联系方式的备注信息，用于助记，不超过30个字符'),
			'skip_verify'        => Yii::t('app', '是否需要验证，1需要 0不需要'),
			'verify_all_day'     => Yii::t('app', '自动验证1全天开启2分时段'),
			'state'              => Yii::t('app', '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
			'spare_employee'     => Yii::t('app', '备用员工'),
			'is_welcome_date'    => Yii::t('app', '欢迎语时段日期 1关 2开'),
			'is_welcome_week'    => Yii::t('app', '欢迎语时段周 1关 2开'),
			'is_limit'           => Yii::t('app', '员工上限 1关 2开'),
			'is_del'             => Yii::t('app', '0：未删除；1：已删除'),
			'qr_code'            => Yii::t('app', '联系二维码的URL'),
			'open_date'          => Yii::t('app', '0关闭1开启'),
			'add_num'            => Yii::t('app', '添加人数'),
			'tag_ids'            => Yii::t('app', '给客户打的标签'),
			'user_key'           => Yii::t('app', '用户选择的key值'),
			'content'            => Yii::t('app', '渠道活码的欢迎语内容'),
			'status'             => Yii::t('app', '渠道活码的欢迎语是否开启0关闭1开启'),
			'sync_attachment_id' => Yii::t('app', '同步后的素材id'),
			'work_material_id'   => Yii::t('app', '企业微信素材id'),
			'groupId'            => Yii::t('app', '分组id'),
			'material_sync'      => Yii::t('app', '不同步到内容库1同步'),
			'attachment_id'      => Yii::t('app', '内容引擎id'),
			'local_path'         => Yii::t('app', '二维码图片本地地址'),
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
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getWorkContactWayDepartments ()
	{
		return $this->hasMany(WorkContactWayRedpacketDepartment::className(), ['config_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getWorkContactWayUsers ()
	{
		return $this->hasMany(WorkContactWayRedpacketUser::className(), ['config_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getWorkExternalContactFollowUsers ()
	{
		return $this->hasMany(WorkExternalContactFollowUser::className(), ['way_id' => 'id']);
	}

	/**
	 * @param bool $withUser
	 * @param bool $withParty
	 *
	 * @return array
	 */
	public function dumpData ($withUser = false, $withParty = false)
	{
		$result = [
			'id'               => strval($this->id),
			'corp_id'          => $this->corp_id,
			'agent_id'         => $this->agent_id,
			'name'             => $this->name,
			'time_type'        => $this->time_type,
			'start_time'       => $this->time_type == 2 ? $this->start_time : '',
			'end_time'         => $this->time_type == 2 ? $this->end_time : '',
			'reserve_day'      => $this->reserve_day,
			'rule_id'          => $this->rule_id,
			'redpacket_amount' => $this->redpacket_amount,
			'out_amount'       => $this->out_amount,
			'redpacket_status' => $this->redpacket_status,
			'config_id'       => $this->config_id,
			'type'            => $this->type,
			'title'           => $this->title,
			'scene'           => $this->scene,
			'style'           => $this->style,
			'remark'          => $this->remark,
			'skip_verify'     => $this->skip_verify,
			'state'           => $this->state,
			'is_del'          => $this->is_del,
			'qr_code'         => $this->qr_code,
			'add_num'         => $this->add_num,
			'update_time'     => $this->update_time,
			'create_time'     => $this->create_time,
			'tag_ids'         => $this->tag_ids,
			'user_key'        => $this->user_key,
			'material_sync'   => $this->material_sync,
			'attachment_id'   => $this->attachment_id,
			'groupId'         => $this->groupId,
			'status'          => $this->status,
			'content'         => $this->content,
			'open_date'       => $this->open_date,
			'specialTime'     => $this->open_date,
			'local_path'      => $this->local_path,
			'verify_all_day'  => $this->verify_all_day,
			'is_welcome_date' => $this->is_welcome_date,
			'is_welcome_week' => $this->is_welcome_week,
			'is_limit'        => $this->is_limit,
		];

		if (empty($result['rule_id'])) {
			$ruleData = Json::decode($this->rule_text, true);
		} else {
			$ruleData = RedPackRule::find()->andWhere(['id' => $result['rule_id']])->asArray()->one();
		}
		$result['rule_name']              = $ruleData['name'];
		$result['rule_type']              = (int)$ruleData['type'];
		$result['rule_fixed_amount']      = $ruleData['fixed_amount'];
		$result['rule_min_random_amount'] = $ruleData['min_random_amount'];
		$result['rule_max_random_amount'] = $ruleData['max_random_amount'];
		$result['rule_pic_url']           = $ruleData['pic_url'];
		$result['rule_title']             = $ruleData['title'];
		$result['rule_des']               = $ruleData['des'];
		$result['rule_thanking']          = $ruleData['thanking'];

		$sEmployee = [];
		if (!empty($this->spare_employee)) {
			$sEmployee = Json::decode($this->spare_employee, true);
		}
		$result['spare_employee'] = $sEmployee; //备用员工

		$limitInfo = [];
		if ($this->is_limit == 2) {
			//开启员工上限
			$userLimit = WorkContactWayRedpacketUserLimit::find()->where(['way_id' => $this->id])->all();
			if (!empty($userLimit)) {
				/** @var WorkContactWayUserLimit $limit */
				foreach ($userLimit as $limit) {
					array_push($limitInfo, $limit->dumpData());
				}
			}
		}
		$result['user_limit'] = $limitInfo;

		$verifyDate = [];
		if ($this->verify_all_day == 2) {
			//开启了分时段验证
			$date = WorkContactWayRedpacketVerifyDate::find()->where(['way_id' => $this->id])->asArray()->all();
			if (!empty($date)) {
				foreach ($date as $key => $val) {
					$verifyDate[$key]['start_time'] = $val['start_time'];
					$verifyDate[$key]['end_time']   = $val['end_time'];
				}
			}
		}
		$result['verify_date'] = $verifyDate;

		$welcomeDateList = [];
		if ($this->is_welcome_date == 2) {
			//开启了日期欢迎语
			$dateData = WorkContactWayRedpacketDateWelcome::find()->where(['way_id' => $this->id, 'type' => 2])->asArray()->all();
			if (!empty($dateData)) {
				foreach ($dateData as $key => $data) {
					$dateCon[]                     = $data['start_date'];
					$dateCon[]                     = $data['end_date'];
					$welcomeDateList[$key]['date'] = $dateCon;
					$timeDate                      = WorkContactWayRedpacketDateWelcomeContent::getData($data['id']);
					$welcomeDateList[$key]['time'] = $timeDate;
				}
			}
		}
		$result['welcome_date_list'] = $welcomeDateList;

		$welcomeWeekList = [];
		if ($this->is_welcome_week == 2) {
			//开启了周欢迎语
			$dateData = WorkContactWayRedpacketDateWelcome::find()->where(['way_id' => $this->id, 'type' => 1])->asArray()->all();
			if (!empty($dateData)) {
				foreach ($dateData as $key => $data) {
					$weekCont                      = Json::decode($data['day'], true);
					$welcomeWeekList[$key]['date'] = $weekCont;
					$timeDate                      = WorkContactWayRedpacketDateWelcomeContent::getData($data['id']);
					$welcomeWeekList[$key]['time'] = $timeDate;
				}
			}
		}
		$result['welcome_week_list'] = $welcomeWeekList;

		$result['add_num'] = WorkExternalContactFollowUser::find()->where(['way_id' => $this->id, 'del_type' => [WorkExternalContactFollowUser::WORK_CON_EX,WorkExternalContactFollowUser::NO_ASSIGN]])->count();

		if ($withUser) {
			$result['user'] = [];
			if (!empty($this->workContactWayUsers)) {
				foreach ($this->workContactWayUsers as $wayUser) {
					array_push($result['user'], $wayUser->user->dumpData());
				}
			}
		}

		if ($withParty) {
			$result['department'] = [];
			if (!empty($this->workContactWayDepartments)) {
				foreach ($this->workContactWayDepartments as $wayDepartment) {
					array_push($result['department'], $wayDepartment->department->dumpData());
				}
			}
		}

		return $result;
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException
	 */
	public static function verify ($data)
	{
		$isLimit         = isset($data['is_limit']) ? $data['is_limit'] : 1;
		$userLimit       = isset($data['user_limit']) ? $data['user_limit'] : [];
		$verifyAllDay    = isset($data['verify_all_day']) ? $data['verify_all_day'] : 1;
		$verifyDate      = isset($data['verify_date']) ? $data['verify_date'] : [];
		$spareEmployee   = isset($data['spare_employee']) ? $data['spare_employee'] : [];
		$welcomeDate     = isset($data['is_welcome_date']) ? $data['is_welcome_date'] : 1;
		$welcomeWeek     = isset($data['is_welcome_week']) ? $data['is_welcome_week'] : 1;
		$welcomeDateList = isset($data['welcome_date_list']) ? $data['welcome_date_list'] : [];
		$welcomeWeekList = isset($data['welcome_week_list']) ? $data['welcome_week_list'] : [];
		if ($isLimit == 2) {
			if (empty($userLimit)) {
				throw new InvalidDataException("请填写员工每日添加客户上限");
			}
			if (!is_array($userLimit)) {
				throw new InvalidDataException("数据格式不对");
			}
			foreach ($userLimit as $limit) {
				if ($limit['limit'] > 99999999) {
					throw new InvalidDataException("员工添加上限不能超过99999999");
				}
			}
			if (empty($spareEmployee)) {
				throw new InvalidDataException("请选择备用员工");
			}
		}
		if ($verifyAllDay == 2) {
			if (empty($verifyDate)) {
				throw new InvalidDataException("自动通过好友时间段不能为空");
			}
			if (!is_array($verifyDate)) {
				throw new InvalidDataException("数据格式不对");
			}
		}
		if ($welcomeDate == 2) {
			//验证日期欢迎语
			if (empty($welcomeDateList)) {
				throw new InvalidDataException("日期欢迎语不能为空");
			}
			if (!is_array($welcomeDateList)) {
				throw new InvalidDataException("数据格式不对");
			}
			foreach ($welcomeDateList as $list) {
				if (empty($list['date'])) {
					throw new InvalidDataException("请选择时期");
				}
				foreach ($list['time'] as $val) {
					if ($val['start_time'] == 'Invalid date' || $val['end_time'] == 'Invalid date') {
						throw new InvalidDataException("请填写时间段");
					}
					if (!empty($val['content'])) {
						$con                     = $val['content'];
						$welcome                 = [];
						$welcome['text_content'] = $con['text_content'];
						$welcome['add_type']     = $con['add_type'];
						WorkWelcome::verify($welcome, 0);
					}
				}
			}
		}
		if ($welcomeWeek == 2) {
			//验证周欢迎语
			if (empty($welcomeWeekList)) {
				throw new InvalidDataException("周欢迎语不能为空");
			}
			if (!is_array($welcomeWeekList)) {
				throw new InvalidDataException("数据格式不对");
			}
			foreach ($welcomeWeekList as $list) {
				if (empty($list['date'])) {
					throw new InvalidDataException("请选择周期");
				}
				foreach ($list['time'] as $val) {
					if ($val['start_time'] == 'Invalid date' || $val['end_time'] == 'Invalid date') {
						throw new InvalidDataException("请填写时间段");
					}
					if (!empty($val['content'])) {
						$con                     = $val['content'];
						$welcome                 = [];
						$welcome['text_content'] = $con['text_content'];
						$welcome['add_type']     = $con['add_type'];
						WorkWelcome::verify($welcome, 0);
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param $corpId
	 * @param $contactWayInfo
	 * @param $otherInfo
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function addWay ($corpId, $contactWayInfo, $otherInfo)
	{
		$authCorp = WorkCorp::findOne($corpId);

		if (empty($authCorp)) {
			throw new InvalidDataException('参数不正确。');
		}

		$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
		$wayId   = 0;
		try {
			if (!empty($workApi)) {
				$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
				$way      = $workApi->ECAddContactWay($sendData);

				$wayId = static::getWay($corpId, $way['config_id'], $otherInfo);
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
			} elseif (strpos($message, '40003') !== false) {
				$message = '无效的UserID';
			}
			throw new InvalidDataException($message);
		}

		return $wayId;
	}

	/**
	 * @param $corpId
	 * @param $configId
	 * @param $otherInfo
	 *
	 * @return int
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \app\components\InvalidParameterException
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getWay ($corpId, $configId, $otherInfo)
	{
		$authCorp = WorkCorp::findOne($corpId);

		if (empty($authCorp)) {
			throw new InvalidDataException('参数不正确。');
		}

		$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
		$wayId   = 0;

		if (!empty($workApi)) {
			$wayInfo = $workApi->ECGetContactWay($configId);
			$wayInfo = SUtils::Object2Array($wayInfo);

			$wayId = static::setWay($corpId, $wayInfo['contact_way'], $otherInfo);
		}

		return $wayId;
	}

	/**
	 * @param $corpId
	 * @param $contactWayInfo
	 * @param $otherInfo
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function setWay ($corpId, $contactWayInfo, $otherInfo)
	{
		$transaction = \Yii::$app->db->beginTransaction();
		try {
			$way    = static::findOne(['corp_id' => $corpId, 'config_id' => $contactWayInfo['config_id']]);
			$is_add = 0;
			$uid    = !empty($otherInfo['uid']) ? $otherInfo['uid'] : 0;
			if (empty($way)) {
				$way                   = new WorkContactWayRedpacket();
				$way->redpacket_status = static::RED_WAY_NOT_ISSUE;
				$way->create_time      = DateUtil::getCurrentTime();
				$is_add                = 1;
			}

			$way->corp_id   = $corpId;
			$way->config_id = $contactWayInfo['config_id'];
			$way->agent_id  = $otherInfo['agent_id'];
			//红包活动信息
			$way->name        = $otherInfo['name'];
			$way->time_type   = $otherInfo['time_type'];
			$way->start_time  = $otherInfo['start_time'];
			$way->end_time    = $otherInfo['end_time'];
			$way->reserve_day = $otherInfo['reserve_day'];
			$way->rule_id     = $otherInfo['rule_id'];
			if (empty($way->rule_id)) {
				$ruleData                      = [];
				$ruleData['name']              = $otherInfo['rule_name'];
				$ruleData['type']              = $otherInfo['rule_type'];
				$ruleData['fixed_amount']      = $otherInfo['rule_fixed_amount'];
				$ruleData['min_random_amount'] = $otherInfo['rule_min_random_amount'];
				$ruleData['max_random_amount'] = $otherInfo['rule_max_random_amount'];
				$ruleData['pic_url']           = $otherInfo['rule_pic_url'];
				$ruleData['title']             = $otherInfo['rule_title'];
				$ruleData['des']               = $otherInfo['rule_des'];
				$ruleData['thanking']          = $otherInfo['rule_thanking'];
				$way->rule_text                = json_encode($ruleData, true);
			}
			$way->redpacket_amount = $otherInfo['redpacket_amount'];

			if ($otherInfo['open_date']) {
				$way->open_date = 1;
			} else {
				$way->open_date = 0;
			}
			$choose_date = $otherInfo['choose_date'];

			if (!empty($contactWayInfo['type'])) {
				$way->type = $contactWayInfo['type'];
			}

			if (!empty($contactWayInfo['scene'])) {
				$way->scene = $contactWayInfo['scene'];
			}

			if (!empty($contactWayInfo['style'])) {
				$way->style = $contactWayInfo['style'];
			}

			if (!empty($contactWayInfo['remark'])) {
				$way->remark = $contactWayInfo['remark'];
			}

			$way->skip_verify = $otherInfo['skip_verify'];

			if (!empty($contactWayInfo['state'])) {
				$way->state = $contactWayInfo['state'];
			}

			if (!empty($contactWayInfo['qr_code'])){
				$way->qr_code = $contactWayInfo['qr_code'];
				if (!empty($is_add)) {
					$imageData = Material::getImage($contactWayInfo['qr_code'], 'qrcode/' . $uid . '/wxwork');
					Yii::error($imageData, '$imageData');
					if (!empty($imageData['local_path'])) {
						$way->local_path = $imageData['local_path'];
					}
				}
			}
			$content       = WorkWelcome::getContent($otherInfo);
			$way->content  = json_encode($content);
			$way->tag_ids  = isset($otherInfo['tag_ids']) ? trim($otherInfo['tag_ids'], ',') : '';
			$way->status   = isset($otherInfo['status']) ? $otherInfo['status'] : 0;
			$way->title    = isset($otherInfo['title']) ? $otherInfo['title'] : '';

			//新加数据
			$way->verify_all_day  = isset($otherInfo['verify_all_day']) ? $otherInfo['verify_all_day'] : 1;
			$way->is_limit        = isset($otherInfo['is_limit']) ? $otherInfo['is_limit'] : 1;  //2开启员工上限
			$userLimit            = isset($otherInfo['user_limit']) ? $otherInfo['user_limit'] : '';  //员工上限列表
			$spareEmployee        = isset($otherInfo['spare_employee']) && !empty($otherInfo['spare_employee']) ? json_encode($otherInfo['spare_employee']) : '';   //备用员工
			$verifyDate           = isset($otherInfo['verify_date']) && !empty($otherInfo['verify_date']) ? $otherInfo['verify_date'] : '';
			$way->is_welcome_date = isset($otherInfo['is_welcome_date']) ? $otherInfo['is_welcome_date'] : 1;
			$way->is_welcome_week = isset($otherInfo['is_welcome_week']) ? $otherInfo['is_welcome_week'] : 1;
			$welcomeDateList      = isset($otherInfo['welcome_date_list']) ? $otherInfo['welcome_date_list'] : [];
			$welcomeWeekList      = isset($otherInfo['welcome_week_list']) ? $otherInfo['welcome_week_list'] : [];
			if ($way->is_limit == 2) {
				$way->spare_employee = $spareEmployee;
			} else {
				$way->spare_employee = '';
			}
			/**sym 刪除選擇部門但是查询需要回写*/
			WorkDepartment::FormatData($choose_date,$otherInfo['week_user']);

			if ($way->dirtyAttributes) {
				if (!$way->validate() || !$way->save()) {
					throw new InvalidDataException(SUtils::modelError($way));
				}
			}
			if (!empty($is_add)) {
				//添加至内容引擎
				$imageData['uid']       = !empty($uid) ? $uid : '';
				$imageData['file_name'] = $way->name;
				Attachment::addChannel($way->id, $imageData, Attachment::WORK_TYPE);
			}
			if (empty($contactWayInfo['state'])) {
				/** @var Work $workApi */
				try {
					$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				} catch (\Exception $e) {
					Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":getWorkApi");
					throw new InvalidDataException($e->getMessage());
				}

				try {
					if (!empty($workApi)) {
						$sendData = ExternalContactWay::parseFromArray(['config_id' => $way->config_id, 'state' => self::REDPACKET_WAY . '_' . $way->corp_id . '_' . $way->id]);
						$workApi->ECUpdateContactWay($sendData);
					}
				} catch (\Exception $e) {
					Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":updateWay");
					throw new InvalidDataException($e->getMessage());
				}
			}
			if ($way->open_date == 1) {
				//同步到渠道活码红包活动日期成员表
				$res = WorkContactWayRedpacketDate::setData($choose_date, $way->id);
				Yii::error($res, '$res');
			}
			if (!empty($otherInfo['week_user'])) {
				$weekUser = $otherInfo['week_user'];
				WorkContactWayRedpacketDate::setWeekData($weekUser, $way->id);
			}
			//分时段验证通过好友
			if ($way->verify_all_day == 2) {
				//开启分时段
				$wayId = $way->id;
				WorkContactWayRedpacketVerifyDate::add($verifyDate, $wayId);
			}
			//添加员工上限
			if ($way->is_limit == 2) {
				$wayId = $way->id;
				WorkContactWayRedpacketUserLimit::add($userLimit, $wayId);
			}
			//设置分时段欢迎语
			if ($way->is_welcome_date == 2) {
				$wayId = $way->id;
				WorkContactWayRedpacketDateWelcome::add($welcomeDateList, $wayId, 2);
			}
			//设置每周欢迎语
			if ($way->is_welcome_week == 2) {
				$wayId = $way->id;
				WorkContactWayRedpacketDateWelcome::add($welcomeWeekList, $wayId, 1);
			}

			if (empty($is_add) && $way->is_limit == 2) {
				//根据添加上限再次生成活码
				static::getNewCode($way->id, $way->corp_id, $way->open_date, 1);
			}

			$transaction->commit();
			return $way->id;

		} catch (\Exception $e) {
			$transaction->rollBack();
			throw new InvalidDataException($e->getMessage());
		}
	}

	/**
	 * @param $id
	 * @param $corpId
	 * @param $openDate
	 * @param $isEdit
	 *
	 * @return bool
	 *
	 */
	public static function getNewCode ($id, $corpId, $openDate, $isEdit = 0)
	{
		$week    = static::returnDay();
		$date    = date('Y-m-d');
		$newTime = time();
		$h       = date('H');
		if ($h == 23) {
			$date = date("Y-m-d", strtotime("+1 day"));
		}
		$contactWay['id']        = $id;
		$contactWay['corp_id']   = $corpId;
		$contactWay['open_date'] = $openDate;
		$contactData             = static::getDepartUser($contactWay, $week, $date, $newTime);
		\Yii::error($contactData,'$contactData');
		$contactUserId           = $contactData['userId'];
		$partyId                 = $contactData['partyId'];
		$contactWayNew           = static::find()->where(['id' => $id])->asArray()->one();
		$userId                  = static::getUserId($contactUserId, $contactWayNew);
		//判断是否开启了分时段自动通过
		$verify = !(boolean) $contactWayNew['skip_verify'];
		$verify = static::getVerify($contactWayNew, $verify);
		if (!empty($userId) || !empty($partyId)) {
			$contactWayInfo = [
				'type'        => (int) $contactWayNew['type'],
				'scene'       => (int) $contactWayNew['scene'],
				'style'       => (int) $contactWayNew['style'],
				'remark'      => $contactWayNew['remark'],
				'skip_verify' => $verify,
				'state'       => $contactWayNew['state'],
				'user'        => $userId,
				'party'       => $partyId,
				'config_id'   => $contactWayNew['config_id'],
			];
			Yii::error($contactWayInfo, '$contactWayInfo');
			try {
				$result = static::editContact($contactWayNew['corp_id'], $contactWayInfo);
				Yii::error($result, 'editContact-' . $contactWayNew['corp_id'] . '-' . $contactWayNew['id']);
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if ($isEdit == 1){
					throw new InvalidDataException($e->getMessage());
				}
				Yii::error($contactWayInfo, '$contactWayInfo');
				Yii::error($message, '$message');
			}

		}

		return true;
	}

	/**
	 *
	 * @return string
	 *
	 */
	public static function returnDay ()
	{
		$weekNow = date('l');
		$day     = '';
		switch ($weekNow) {
			case 'Monday':
				$day = WorkContactWayRedpacketDate::MONDAY_DAY;
				break;
			case 'Tuesday':
				$day = WorkContactWayRedpacketDate::TUESDAY_DAY;
				break;
			case 'Wednesday':
				$day = WorkContactWayRedpacketDate::WEDNESDAY_DAY;
				break;
			case 'Thursday':
				$day = WorkContactWayRedpacketDate::THURSDAY_DAY;
				break;
			case 'Friday':
				$day = WorkContactWayRedpacketDate::FRIDAY_DAY;
				break;
			case 'Saturday':
				$day = WorkContactWayRedpacketDate::SATURDAY_DAY;
				break;
			case 'Sunday':
				$day = WorkContactWayRedpacketDate::SUNDAY_DAY;
				break;
		}

		return $day;
	}

	/**
	 * @param $contactWay
	 * @param $day
	 * @param $date
	 * @param $newTime
	 *
	 * @return array
	 *
	 */
	public static function getDepartUser ($contactWay, $day, $date, $newTime)
	{
		$userId  = [];
		$partyId = [];
		$wayDate = WorkContactWayRedpacketDate::findOne(['way_id' => $contactWay['id'], 'type' => 0, 'day' => $day]);
		if (!empty($wayDate)) {
			$dateUser = WorkContactWayRedpacketDateUser::find()->andWhere(['date_id' => $wayDate->id])->asArray()->all();
			if (!empty($dateUser)) {
				foreach ($dateUser as $user) {
					if ($user['time'] == '00:00-00:00') {
						$userDepart = static::getUserDepart($user['user_key'], $user['department'], $contactWay['corp_id']);
						$userId     = $userDepart['userId'];
						$partyId    = $userDepart['partyId'];
					}
					if ($user['time'] != '00:00-00:00') {
						$time  = explode('-', $user['time']);
						$date1 = $date . ' ' . $time[0] . ':00';
						$date2 = $date . ' ' . $time[1] . ':00';
						if ($time[1] == '00:00') {
							$date2 = $date . ' ' . '23:59:59';
						}
						if ($newTime >= strtotime($date1) && $newTime <= strtotime($date2)) {
							$userDepart = static::getUserDepart($user['user_key'], $user['department'], $contactWay['corp_id']);
							$userId     = $userDepart['userId'];
							$partyId    = $userDepart['partyId'];
						}
					}


				}
			}
		}
		if ($contactWay['open_date'] == 1) {
			$workDateUser = WorkContactWayRedpacketDate::find()->where(['type' => 1, 'way_id' => $contactWay['id']])->asArray()->all();
			if (!empty($workDateUser)) {
				foreach ($workDateUser as $user) {
					$time1 = strtotime($user['start_date']);
					$time2 = strtotime($user['end_date'] . ' 23:59:59');
					if ($newTime >= $time1 && $newTime <= $time2) {
						$dateUser = WorkContactWayRedpacketDateUser::find()->andWhere(['date_id' => $user['id']])->asArray()->all();
						foreach ($dateUser as $user) {
							if ($user['time'] == '00:00-00:00') {
								$userDepart = static::getUserDepart($user['user_key'], $user['department'], $contactWay['corp_id']);
								$userId     = $userDepart['userId'];
								$partyId    = $userDepart['partyId'];
							}
							if ($user['time'] != '00:00-00:00') {
								$time  = explode('-', $user['time']);
								$date1 = $date . ' ' . $time[0] . ':00';
								$date2 = $date . ' ' . $time[1] . ':00';
								if ($time[1] == '00:00') {
									$date2 = $date . ' ' . '23:59:59';
								}
								if ($newTime >= strtotime($date1) && $newTime <= strtotime($date2)) {
									$userDepart = static::getUserDepart($user['user_key'], $user['department'], $contactWay['corp_id']);
									$userId     = $userDepart['userId'];
									$partyId    = $userDepart['partyId'];
								}
							}

						}

					}

				}
			}

		}

		return [
			'userId'  => $userId,
			'partyId' => $partyId,
		];
	}

	/**
	 * @param $userList
	 * @param $departmentList
	 * @param $corp_id
	 *
	 * @return array
	 *
	 */
	public static function getUserDepart ($userList, $departmentList, $corp_id)
	{
		$userId  = [];
		$partyId = [];
		if (!empty($userList)) {
			$userList = json_decode($userList, true);
			if (is_array($userList)) {
				foreach ($userList as $val) {
					$workUser = WorkUser::findOne($val['id']);
					if (!empty($workUser) && $workUser->corp_id == $corp_id) {
						array_push($userId, $workUser->userid);
					}
				}
			} else {
				$workUser = WorkUser::findOne($userList);
				if (!empty($workUser) && $workUser->corp_id == $corp_id) {
					array_push($userId, $workUser->userid);
				}
			}

		}
		if (!empty($departmentList)) {
			$departments = json_decode($departmentList, true);
			if (!empty($departments)) {
				foreach ($departments as $depart) {
					$department = WorkDepartment::findOne($depart);
					if (!empty($department) && $department->corp_id == $corp_id) {
						array_push($partyId, $department->department_id);
					}
				}
			}
		}

		return [
			'userId'  => $userId,
			'partyId' => $partyId,
		];

	}

	/**
	 * @param $userId
	 * @param $contactWay
	 *
	 * @return array
	 *
	 */
	public static function getUserId ($userId, $contactWay)
	{
		\Yii::error($userId,'$userId');
		if ($contactWay['is_limit'] == 2) {
			$nowUser       = [];
			$workUserLimit = WorkContactWayRedpacketUserLimit::find()->where(['way_id' => $contactWay['id']])->all();
			if (!empty($workUserLimit)) {
				/** @var WorkContactWayUserLimit $limit */
				foreach ($workUserLimit as $limit) {
					if ($limit->limit > 0) {
						$todayTimeStart = strtotime(date('Y-m-d'));
						//获取当前员工今日添加客户数
						$hasCount = WorkExternalContactFollowUser::find()->where(['way_redpack_id' => $contactWay['id'], 'user_id' => $limit->user_id])->andFilterWhere(['between', 'createtime', $todayTimeStart, time()]);
						\Yii::error($hasCount->createCommand()->getRawSql(),'sql1111');
						$hasCount = $hasCount->count();
						if ($hasCount >= $limit->limit) {
							array_push($nowUser, $limit->user_id);
						}
					}
				}
			}
			\Yii::error($nowUser,'$nowUser');
			if (!empty($userId) && !empty($nowUser)) {
				$limitUser = WorkUser::find()->where(['id' => $nowUser])->all();
				if (!empty($limitUser)) {
					/** @var WorkUser $user */
					foreach ($limitUser as $user) {
						if (in_array($user->userid, $userId)) {
							$numKey = array_search($user->userid, $userId);
							unset($userId[$numKey]);
						}
					}
				}
				\Yii::error($userId,'$userId90909');
				//当活码的员工为空时启用备用员工
				if (empty($userId) && !empty($contactWay['spare_employee'])) {
					$spEmployee = Json::decode($contactWay['spare_employee'], true);
					if(is_array($spEmployee)){
						foreach ($spEmployee as $emp) {
							$spUser = WorkUser::findOne($emp['id']);
							if (!empty($spUser)) {
								array_push($userId, $spUser->userid);
							}
						}
					}else{
						$spUser = WorkUser::findOne($spEmployee);
						if (!empty($spUser)) {
							array_push($userId, $spUser->userid);
						}
					}

				}
			}

		}

		return $userId;
	}

	/**
	 * @param $contactWay
	 * @param $verify
	 * @param $time
	 *
	 * @return bool
	 *
	 */
	public static function getVerify ($contactWay, $verify, $time = 0)
	{
		if ($contactWay['verify_all_day'] == 2) {
			$time = $time == 0 ? time() : $time;
			try {
				$flag       = 0;
				$verifyDate = WorkContactWayRedpacketVerifyDate::find()->where(['way_id' => $contactWay['id']]);
				$verifyDate = $verifyDate->asArray()->all();
				if (!empty($verifyDate)) {
					foreach ($verifyDate as $dateTime) {
						$startTime = strtotime(date('Y-m-d') . ' ' . $dateTime['start_time']);
						if ($dateTime['end_time'] == '00:00') {
							$dateTime['end_time'] = '23:59:59';
						}
						$endTime = strtotime(date('Y-m-d') . ' ' . $dateTime['end_time']);
						if ($startTime <= $time && $time <= $endTime) {
							$flag = 1;//当前时间在分时段自动通过的范围内
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'message123');
			}

			if ($flag == 0) {
				$verify = false; //需认证
			}
		}

		return $verify;
	}

	/**
	 * @param $corpId
	 * @param $contactWayInfo
	 *
	 * @return array|null
	 *
	 * @throws InvalidDataException
	 */
	public static function editContact ($corpId, $contactWayInfo)
	{
		$result = [];
		try {
			$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);

			if (!empty($workApi)) {
				$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
				$result   = $workApi->ECUpdateContactWay($sendData);
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

		return $result;
	}

	/**
	 * 更新红包活动渠道活码
	 * @param $corpId
	 * @param $contactWayInfo
	 * @param $otherInfo
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function updateWay ($corpId, $contactWayInfo, $otherInfo)
	{
		$authCorp = WorkCorp::findOne($corpId);

		if (empty($authCorp)) {
			throw new InvalidDataException('参数不正确。');
		}

		$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
		$wayId   = 0;
		try {
			if (!empty($workApi)) {
				$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
				$res = $workApi->ECUpdateContactWay($sendData);

				$wayId = static::setWay($corpId, $contactWayInfo, $otherInfo);
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
			} elseif (strpos($message, '40003') !== false) {
				$message = '无效的UserID';
			}
			throw new InvalidDataException($message);
		}

		return $wayId;
	}

	/**
	 * 删除渠道活码
	 */
	public static function delWay ($id, $status = WorkContactWayRedpacket::RED_WAY_DEL)
	{
		$way = static::findOne($id);
		try {
			if (!empty($way)) {
				$workApi = WorkUtils::getWorkApi($way->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$workApi->ECDelContactWay($way->config_id);
				}
				$way->redpacket_status = $status;
				$way->save();
				$attachment = Attachment::findOne(['channel_type' => 2, 'channel_id' => $id]);
				if (!empty($attachment)) {
					$attachment->status = 0;
					$attachment->save();
				}
			}
		} catch (\Exception $e) {
			$message = $e->getMessage();
			\Yii::error($message, 'deleteRedpacketMessage');
		}

		return true;
	}

	/**
	 * @param $id
	 *
	 * @return array
	 *
	 */
	public static function getDateWeekWelcome ($id)
	{
		$content    = [];
		$contactWay = WorkContactWayRedpacket::findOne($id);
		if (!empty($contactWay)) {
			if ($contactWay->status == 1) {
				//开启了日期欢迎语
				if ($contactWay->is_welcome_date == 2) {
					$dateWelcome = WorkContactWayRedpacketDateWelcome::find()->where(['way_id' => $id, 'type' => 2])->asArray()->all();
					if (!empty($dateWelcome)) {
						$dateNow = date('Y-m-d');
						foreach ($dateWelcome as $wel) {
							if (strtotime($dateNow)>=strtotime($wel['start_date']) && strtotime($dateNow)<=strtotime($wel['end_date'])) {
								$content = WorkContactWayRedpacketDateWelcomeContent::getContent($dateNow, $wel['id']);
							}
						}
					}
				}
				//开启了周欢迎语
				if ($contactWay->is_welcome_week == 2 && empty($content)) {
					$day         = static::returnDay();
					$weekWelcome = WorkContactWayRedpacketDateWelcome::find()->where(['way_id' => $id, 'type' => 1])->asArray()->all();
					if (!empty($weekWelcome)) {
						foreach ($weekWelcome as $wel) {
							$week = Json::decode($wel['day'], true);
							$flag = 0;
							foreach ($week as $we) {
								if ($day == $we) {
									$flag = 1;
								}
							}
							if ($flag == 1) {
								$dateNow = date('Y-m-d');
								$content = WorkContactWayRedpacketDateWelcomeContent::getContent($dateNow, $wel['id']);
							}
						}
					}
				}

			}
		}

		return $content;
	}

	/**
	 * 每个整点执行的脚本-更新红包拉新活码成员
	 *
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function updateContactWayRedpacket ()
	{
		ini_set('memory_limit', '2048M');
		set_time_limit(0);
		$workContactWay = WorkContactWayRedpacket::find()->where(['redpacket_status' => [1, 2, 3]])->asArray()->all();
		$week           = date('l');
		$day            = '';
		switch ($week) {
			case 'Monday':
				$day = WorkContactWayRedpacketDate::MONDAY_DAY;
				break;
			case 'Tuesday':
				$day = WorkContactWayRedpacketDate::TUESDAY_DAY;
				break;
			case 'Wednesday':
				$day = WorkContactWayRedpacketDate::WEDNESDAY_DAY;
				break;
			case 'Thursday':
				$day = WorkContactWayRedpacketDate::THURSDAY_DAY;
				break;
			case 'Friday':
				$day = WorkContactWayRedpacketDate::FRIDAY_DAY;
				break;
			case 'Saturday':
				$day = WorkContactWayRedpacketDate::SATURDAY_DAY;
				break;
			case 'Sunday':
				$day = WorkContactWayRedpacketDate::SUNDAY_DAY;
				break;
		}
		$date    = date('Y-m-d');
		$newTime = time() + 300;
		$h       = date('H');
		if ($h == 23) {
			$date = date("Y-m-d", strtotime("+1 day"));
		}
		foreach ($workContactWay as $contactWay) {
			Yii::error($contactWay['id'], 'redpacket_way_id');
			$resultData = static::getDepartUser($contactWay, $day, $date, $newTime);
			$userId     = $resultData['userId'];
			$partyId    = $resultData['partyId'];

			Yii::error($userId, '$userId-Hour-redpacket');
			Yii::error($partyId, '$partyId-Hour-redpacket');

			//开启了员工每日添加上限
			$userId = static::getUserId($userId, $contactWay);

			//判断是否开启了分时段自动通过
			$verify = !(boolean) $contactWay['skip_verify'];
			$verify = static::getVerify($contactWay, $verify);

			if (!empty($userId) || !empty($partyId)) {
				$contactWayInfo = [
					'type'        => (int) $contactWay['type'],
					'scene'       => (int) $contactWay['scene'],
					'style'       => (int) $contactWay['style'],
					'remark'      => $contactWay['remark'],
					'skip_verify' => $verify,
					'state'       => $contactWay['state'],
					'user'        => $userId,
					'party'       => $partyId,
					'config_id'   => $contactWay['config_id'],
				];
				try {
					$result = static::editContact($contactWay['corp_id'], $contactWayInfo);
				} catch (\Exception $e) {
					$message = $e->getMessage();
					Yii::error($contactWayInfo, '$contactWayInfo-redpacket');
					Yii::error($message, '$message-redpacket');
				}

			}
		}

		return true;
	}

	/**
	 * 每日0点更新红包拉新活码是否失效
	 *
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function updateContactWayRedpacketStatus ()
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(0);

		$time       = time();
		$dateTime   = date('Y-m-d H:i:s');
		$contactWay = static::find()->where(['time_type' => 2])->andWhere(['redpacket_status' => [static::RED_WAY_ISSUE, static::RED_PACKET_DISABLED]])->andWhere(['or', ['<', 'end_time', $dateTime], ['and', ['>', 'disabled_time', 0], ['<', 'disabled_time', $time]]])->asArray()->all();

		foreach ($contactWay as $way) {
			$endTime = $way['disabled_time'] > 0 ? $way['disabled_time'] : strtotime($way['end_time']);
			if (($endTime + $way['reserve_day'] * 86400) < $time) {
				try {
					WorkContactWayRedpacket::delWay($way['id'], static::RED_WAY_DISABLED);
				} catch (\Exception $e) {
					$message = $e->getMessage();
					Yii::error($message, '$message-redpacket-disabled');
				}
			}
		}

		return true;
	}

	/**
	 * 获取红包拉新活动添加人数（一次性执行）
	 *
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function updateAddNum ()
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(0);

		$followUserData = WorkExternalContactFollowUser::find()->andWhere(['>', 'way_redpack_id', 0])->select('`way_redpack_id`, count(`id`) num')->groupBy('way_redpack_id')->asArray()->all();

		foreach ($followUserData as $val){
			$wayRedpacket = WorkContactWayRedpacket::findOne($val['way_redpack_id']);
			if ($wayRedpacket){
				$wayRedpacket->add_num = $val['num'];
				if (!$wayRedpacket->save()){
					throw new InvalidDataException(SUtils::modelError($wayRedpacket));
				}
			}
		}

		return true;
	}

}
