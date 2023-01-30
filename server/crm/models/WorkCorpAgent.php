<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncWorkDepartmentListJob;
	use app\queue\SyncWorkExternalContactJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\Agent;
	use dovechen\yii2\weWork\Work;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_corp_agent}}".
	 *
	 * @property int                          $id
	 * @property int                          $corp_id              授权方ID
	 * @property int                          $agentid              授权方应用id
	 * @property int                          $suite_id             授权平台ID
	 * @property string                       $secret               应用secret
	 * @property string                       $access_token         应用access_token
	 * @property string                       $access_token_expires access_token有效期
	 * @property int                          $agent_type           应用类型：1、基础；2、自建；3、授权；4、小程序
	 * @property string                       $basic_agent_type     基础应用类型：moneyPay企业支付...
	 * @property int                          $agent_use_type       应用用途：0、通用；1、侧边栏（后续在扩展）
	 * @property int                          $agent_is_money       1红包应用
	 * @property string                       $name                 授权方应用名字
	 * @property string                       $round_logo_url       授权方应用方形头像
	 * @property string                       $square_logo_url      授权方应用圆形头像
	 * @property string                       $description          企业应用详情
	 * @property int                          $appid                旧的多应用套件中的对应应用id，新开发者请忽略
	 * @property int                          $level                权限等级。1:通讯录基本信息只读；2:通讯录全部信息只读；3:通讯录全部信息读写；4:单个基本信息只读；5:通讯录全部信息只写
	 * @property string                       $allow_party          应用可见范围（部门）
	 * @property string                       $allow_user           应用可见范围（成员）
	 * @property string                       $allow_tag            应用可见范围（标签）
	 * @property string                       $extra_party          额外通讯录（部门）
	 * @property string                       $extra_user           额外通讯录（成员）
	 * @property string                       $extra_tag            额外通讯录（标签）
	 * @property int                          $close                企业应用是否被停用
	 * @property string                       $redirect_domain      企业应用可信域名
	 * @property int                          $report_location_flag 企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；
	 * @property int                          $isreportenter        是否上报用户进入应用事件。0：不接收；1：接收
	 * @property string                       $home_url             应用主页url
	 * @property int                          $is_del               0：未删除；1：已删除
	 * @property string                       $create_time          创建时间
	 *
	 * @property WorkChatRemind[]             $workChatReminds
	 * @property WorkCorp                     $corp
	 * @property WorkSuiteConfig              $suite
	 * @property WorkMsgAuditNoticeRuleInfo[] $workMsgAuditNoticeRuleInfos
	 * @property WorkUserAuthorRelation[]     $workUserAuthorRelations
	 */
	class WorkCorpAgent extends \yii\db\ActiveRecord
	{
		const NORMAL_AGENT = 1;
		const CUSTOM_AGENT = 2;
		const AUTH_AGENT = 3;
		const MINIAPP_AGENT = 4;

		const PUB_AGENT = 0;
		const SLIDER_AGENT = 1;

		const AGENT_NO_DEL = 0;
		const AGENT_IS_DEL = 1;

		const AGENT_NOT_CLOSE = 0;
		const AGENT_IS_CLOSE = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_corp_agent}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id'], 'required'],
				[['corp_id', 'agentid', 'agent_type', 'agent_use_type', 'agent_is_money', 'appid', 'level', 'close', 'report_location_flag', 'isreportenter', 'is_del'], 'integer'],
				[['description', 'allow_party', 'allow_user', 'allow_tag', 'extra_party', 'extra_user', 'extra_tag'], 'string'],
				[['create_time'], 'safe'],
				[['secret', 'name', 'basic_agent_type'], 'string', 'max' => 64],
				[['access_token', 'round_logo_url', 'square_logo_url'], 'string', 'max' => 255],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['suite_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkSuiteConfig::className(), 'targetAttribute' => ['suite_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                   => Yii::t('app', 'ID'),
				'corp_id'              => Yii::t('app', '授权方ID'),
				'agentid'              => Yii::t('app', '授权方应用id'),
				'suite_id'             => Yii::t('app', '授权平台ID'),
				'secret'               => Yii::t('app', '应用secret'),
				'access_token'         => Yii::t('app', '应用access_token'),
				'access_token_expires' => Yii::t('app', 'access_token有效期'),
				'agent_type'           => Yii::t('app', '应用类型：1、基础；2、自建；3、授权；4、小程序'),
				'basic_agent_type'     => Yii::t('app', '基础应用类型：moneyPay企业支付...'),
				'agent_use_type'       => Yii::t('app', '应用用途：0、通用；1、侧边栏（后续在扩展）'),
				'agent_is_money'       => Yii::t('app', '1红包应用'),
				'name'                 => Yii::t('app', '授权方应用名字'),
				'round_logo_url'       => Yii::t('app', '授权方应用方形头像'),
				'square_logo_url'      => Yii::t('app', '授权方应用圆形头像'),
				'description'          => Yii::t('app', '企业应用详情'),
				'appid'                => Yii::t('app', '旧的多应用套件中的对应应用id，新开发者请忽略'),
				'level'                => Yii::t('app', '权限等级。1:通讯录基本信息只读；2:通讯录全部信息只读；3:通讯录全部信息读写；4:单个基本信息只读；5:通讯录全部信息只写'),
				'allow_party'          => Yii::t('app', '应用可见范围（部门）'),
				'allow_user'           => Yii::t('app', '应用可见范围（成员）'),
				'allow_tag'            => Yii::t('app', '应用可见范围（标签）'),
				'extra_party'          => Yii::t('app', '额外通讯录（部门）'),
				'extra_user'           => Yii::t('app', '额外通讯录（成员）'),
				'extra_tag'            => Yii::t('app', '额外通讯录（标签）'),
				'close'                => Yii::t('app', '企业应用是否被停用'),
				'redirect_domain'      => Yii::t('app', '企业应用可信域名'),
				'report_location_flag' => Yii::t('app', '企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；'),
				'isreportenter'        => Yii::t('app', '是否上报用户进入应用事件。0：不接收；1：接收'),
				'home_url'             => Yii::t('app', '应用主页url'),
				'is_del'               => Yii::t('app', '0：未删除；1：已删除'),
				'create_time'          => Yii::t('app', '创建时间'),
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
		public function getWorkChatReminds ()
		{
			return $this->hasMany(WorkChatRemind::className(), ['agentid' => 'id']);
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
		public function getSuite ()
		{
			return $this->hasOne(WorkSuiteConfig::className(), ['id' => 'suite_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditNoticeRuleInfos ()
		{
			return $this->hasMany(WorkMsgAuditNoticeRuleInfo::className(), ['agent_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkUserAuthorRelations ()
		{
			return $this->hasMany(WorkUserAuthorRelation::className(), ['agent_id' => 'id']);
		}

		/**
		 * @param $users
		 *
		 * @return string
		 */
		public static function getAllowUser ($users)
		{
			$allowUserArray = [];

			foreach ($users as $user) {
				array_push($allowUserArray, $user['userid']);
			}

			$allowUser = implode(',', $allowUserArray);

			return $allowUser;
		}

		public function dumpData ()
		{
			return [
				'id'                   => $this->id,
				'agentid'              => $this->agentid,
				'agent_type'           => $this->agent_type,
				'basic_agent_type'     => $this->basic_agent_type,
				'agent_use_type'       => $this->agent_use_type,
				'agent_is_money'       => $this->agent_is_money,
				'name'                 => $this->name,
				'round_logo_url'       => $this->round_logo_url,
				'square_logo_url'      => $this->square_logo_url,
				'description'          => $this->description,
				'level'                => $this->level,
				'allow_party'          => $this->allow_party,
				'allow_user'           => $this->allow_user,
				'allow_tag'            => $this->allow_tag,
				'extra_party'          => $this->extra_party,
				'extra_user'           => $this->extra_user,
				'extra_tag'            => $this->extra_tag,
				'close'                => $this->close,
				'redirect_domain'      => $this->redirect_domain,
				'report_location_flag' => $this->report_location_flag,
				'isreportenter'        => $this->isreportenter,
				'home_url'             => $this->home_url,
			];
		}

		public function dumpMiniData ()
		{
			return [
				'agentid'              => $this->agentid,
				'access_token'         => $this->access_token,
				'access_token_expires' => $this->access_token_expires,
				'agent_type'           => $this->agent_type,
				'agent_use_type'       => $this->agent_use_type,
			];
		}

		/**
		 * @param $corpId
		 * @param $agentId
		 * @param $agentInfo
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function setAgent ($corpId, $agentId, $agentInfo, $ignore = false)
		{
			$workCorp = WorkCorp::findOne($corpId);

			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$corpAgent = static::findOne($agentId);

			if (empty($corpAgent) || $corpAgent->corp_id != $corpId) {
				throw new InvalidDataException('参数不正确。');
			}

			try {
				$agentApi = WorkUtils::getAgentApi($corpId, $agentId);
			} catch (\Exception $e) {
				if (!$ignore) {
					static::deleteAll(['id' => $agentId]);
				}
				Yii::error($e->getMessage(), 'workCorpAgentGetApi');
				throw new InvalidDataException($e->getMessage());
			}

			try {
				$agentSendData = Agent::parseFromArray($agentInfo);
				$agentApi->AgentSet($agentSendData);

				static::getAgent($corpId, $agentId, $ignore);
			} catch (\Exception $e) {
				Yii::error($e->getMessage(), 'workCorpAgentSetApi');
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		/**
		 * @param      $corpId
		 * @param      $agentId
		 * @param bool $ignore
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 */
		public static function getAgent ($corpId, $agentId, $ignore = false, $isTip = 0)
		{
			$workCorp = WorkCorp::findOne($corpId);

			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$corpAgent = static::findOne($agentId);

			if (empty($corpAgent) || $corpAgent->corp_id != $corpId) {
				throw new InvalidDataException('参数不正确。');
			}

			try {
				$agentApi = WorkUtils::getAgentApi($corpId, $agentId);
			} catch (\Exception $e) {
				if (!$ignore) {
					static::updateAll(['is_del' => 1], ['id' => $agentId]);
				}

				Yii::error($e->getMessage(), 'workCorpAgentGetApi');
				$message = $e->getMessage();
				if (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数';
					static::updateAll(['is_del' => 1], ['id' => $agentId]);
				}
				throw new InvalidDataException($message);
			}

			try {
				$agentInfo = $agentApi->AgentGet($corpAgent->agentid);
				Yii::error($agentInfo, 'workCorpAgentGet');
				static::setCorpAgent($corpId, $agentInfo);
				if (!empty($isTip)) {
					$corpAgent = static::findOne($agentId);
					if ($corpAgent->close == 1) {
						throw new InvalidDataException('该自建应用已在企业微信官方后台停用，无法操作');
					}
				}
			} catch (\Exception $e) {
				if (!$ignore) {
					static::updateAll(['is_del' => 1], ['id' => $agentId]);
				}

				Yii::error($e->getMessage(), 'workCorpAgentGet');
				$message = $e->getMessage();
				if (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数';
					static::updateAll(['is_del' => 1], ['id' => $agentId]);
				}
				throw new InvalidDataException($message);
			}

			return static::findOne($agentId)->dumpData();
		}

		/**
		 * 设置授权信息。如果是通讯录应用，且没开启实体应用，是没有该项的。通讯录应用拥有企业通讯录的全部信息读写权限
		 *
		 * @param int|string $corpId
		 * @param array      $agentInfo
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setCorpAgent ($corpId, $agentInfo)
		{
			$agent = static::findOne(['corp_id' => $corpId, 'agentid' => $agentInfo['agentid']]);

			if (empty($agent)) {
				$agent              = new WorkCorpAgent();
				$agent->create_time = DateUtil::getCurrentTime();
			}

			$agent->corp_id = $corpId;
			$agent->agentid = $agentInfo['agentid'];

			if (!empty($agentInfo['suite_id'])) {
				$agent->suite_id = $agentInfo['suite_id'];
			}

			if (!empty($agentInfo['secret'])) {
				$agent->secret = $agentInfo['secret'];
			}

			if (empty($agent->agent_type)) {
				$agent->agent_type = !empty($agentInfo['agent_type']) ? $agentInfo['agent_type'] : static::CUSTOM_AGENT;
			}

			if (isset($agentInfo['basic_agent_type']) && $agent->agent_type == 1) {
				$agent->basic_agent_type = $agentInfo['basic_agent_type'];
			}

			if (isset($agentInfo['agent_use_type'])) {
				$agent->agent_use_type = $agentInfo['agent_use_type'];
			}

			if (isset($agentInfo['agent_is_money']) && $agentInfo['agent_is_money'] == 1) {
				$agent->agent_is_money = $agentInfo['agent_is_money'];
			}

			if (!empty($agentInfo['name'])) {
				if ($agent->agent_type == 2 && !empty(Yii::$app->params['work_agent']) && !empty(Yii::$app->params['work_agent']['must_begin_with']) && strpos($agentInfo['name'], Yii::$app->params['work_agent']['must_begin_with']) !== 0) {
					throw new InvalidDataException("自建应用名称必须以 " . Yii::$app->params['work_agent']['must_begin_with'] . " 开头");
				}

				$agent->name = $agentInfo['name'];
			}

			if (!empty($agentInfo['round_logo_url'])) {
				$agent->round_logo_url = $agentInfo['round_logo_url'];
			}

			if (!empty($agentInfo['square_logo_url'])) {
				$agent->square_logo_url = $agentInfo['square_logo_url'];
			}

			if (!empty($agentInfo['description'])) {
				$agent->description = $agentInfo['description'];
			}

			if (!empty($agentInfo['appid'])) {
				$agent->appid = $agentInfo['appid'];
			}

			$agent->level       = !empty($agentInfo['privilege']['level']) ? $agentInfo['privilege']['level'] : '';
			$agent->allow_party = !empty($agentInfo['privilege']['allow_party']) ? implode(',', $agentInfo['privilege']['allow_party']) : (!empty($agentInfo['allow_partys']) ? implode(',', $agentInfo['allow_partys']['partyid']) : '');
			$agent->allow_user  = !empty($agentInfo['privilege']['allow_user']) ? implode(',', $agentInfo['privilege']['allow_user']) : (!empty($agentInfo['allow_userinfos']) ? static::getAllowUser($agentInfo['allow_userinfos']['user']) : '');
			$agent->allow_tag   = !empty($agentInfo['privilege']['allow_tag']) ? implode(',', $agentInfo['privilege']['allow_tag']) : (!empty($agentInfo['allow_tags']) ? implode(',', $agentInfo['allow_tags']['tagid']) : '');
			$agent->extra_party = !empty($agentInfo['privilege']['extra_party']) ? implode(',', $agentInfo['privilege']['extra_party']) : '';
			$agent->extra_user  = !empty($agentInfo['privilege']['extra_user']) ? implode(',', $agentInfo['privilege']['extra_user']) : '';
			$agent->extra_tag   = !empty($agentInfo['privilege']['extra_tag']) ? implode(',', $agentInfo['privilege']['extra_tag']) : '';

			if (isset($agentInfo['close'])) {
				$agent->close = $agent->agent_type == 1 ? 0 : $agentInfo['close'];
			}

			if (!empty($agentInfo['redirect_domain'])) {
				$agent->redirect_domain = $agentInfo['redirect_domain'];
			}

			if (isset($agentInfo['report_location_flag'])) {
				$agent->report_location_flag = $agentInfo['report_location_flag'];
			}

			if (isset($agentInfo['isreportenter'])) {
				$agent->isreportenter = $agentInfo['isreportenter'];
			}

			if (!empty($agentInfo['home_url'])) {
				$agent->home_url = $agentInfo['home_url'];
			}

			$agent->is_del = 0;

			$needPush = false;
			if ($agent->dirtyAttributes) {
				if ($agent->agent_type == self::CUSTOM_AGENT && !empty($agent->getDirtyAttributes(['allow_party', 'allow_user']))) {
					$needPush = true;
				}

				if (!$agent->validate() || !$agent->save()) {
					throw new InvalidDataException(SUtils::modelError($agent));
				}
			}

			if (empty($agent->corp->workCorpBind)) {
				$workCorpBind                 = new WorkCorpBind();
				$workCorpBind->corp_id        = $corpId;
				$workCorpBind->token          = WorkCorp::getRandom();
				$workCorpBind->encode_aes_key = WorkCorp::getRandom(43);
				$workCorpBind->create_time    = DateUtil::getCurrentTime();

				if ($workCorpBind->dirtyAttributes) {
					if (!$workCorpBind->validate() || !$workCorpBind->save()) {
						Yii::error(SUtils::modelError($workCorpBind), 'setCorpAgent');
					}
				}
			}

			if ($needPush) {
				\Yii::$app->work->push(new SyncWorkDepartmentListJob([
					'corp'          => $agent->corp,
					'need_external' => $agent->agent_type == self::CUSTOM_AGENT,
				]));
			}

			return $agent->id;
		}

		/**
		 * @param $corpId
		 * @param $agentId
		 *
		 * @return array
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getTokenInfo ($corpId, $agentId)
		{
			$result        = [];
			$workCorpAgent = static::findOne($agentId);

			if (!empty($workCorpAgent) && $workCorpAgent->corp_id == $corpId && $workCorpAgent->is_del == static::AGENT_NO_DEL && $workCorpAgent->close == static::AGENT_NOT_CLOSE) {
				if (empty($workCorpAgent->access_token) || $workCorpAgent->access_token_expires < (time() - 60)) {
					/** @var Work $work */
					$work = Yii::createObject([
						'class'  => Work::className(),
						'corpid' => $workCorpAgent->corp->corpid,
						'secret' => $workCorpAgent->secret,
					]);

					try {
						$work->GetAccessToken(true);
					} catch (\Exception $e) {
						$message = $e->getMessage();
						if (strpos($message, '40001') !== false) {
							$workCorpAgent->close  = self::AGENT_IS_CLOSE;
							$workCorpAgent->is_del = self::AGENT_IS_DEL;
							$workCorpAgent->secret = NULL;
							$workCorpAgent->save();
						}

						throw new InvalidConfigException($message);
					}

					$workCorpAgent->access_token         = $work->access_token;
					$workCorpAgent->access_token_expires = $work->access_token_expire;
					$workCorpAgent->save();

					$result = $workCorpAgent->dumpMiniData();
				} else {
					$result = $workCorpAgent->dumpMiniData();
				}
			} else {
				throw new InvalidConfigException('应用不合法！');
			}

			return $result;
		}
	}
