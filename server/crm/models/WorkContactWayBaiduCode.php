<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncWorkWayBaiduJob;
	use app\util\BaiduApiUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;

	/**
	 * This is the model class for table "{{%work_contact_way_baidu_code}}".
	 *
	 * @property int                 $id
	 * @property int                 $way_id        企业微信联系我表ID
	 * @property string              $config_id     联系方式的配置id
	 * @property string              $qr_code       联系二维码的URL
	 * @property string              $state         企业自定义的state参数，用于区分不同的添加渠道，在调用获取外部联系人详情时会返回该参数值
	 * @property int                 $add_num       添加人数
	 * @property string              $update_time   更新时间
	 * @property string              $create_time   创建时间
	 * @property string              $config_status 活码状态：0删除、1可用
	 * @property string              $expire_time   活码过期时间
	 * @property string              $user          成员数据
	 * @property string              $party         部门数据
	 * @property string              $queue_id      队列id
	 * @property string              $bd_vid        转化页bd_vid
	 * @property string              $logidUrl      转化页URL
	 * @property string              $newType       转化类型
	 *
	 * @property WorkContactWayBaidu $way
	 */
	class WorkContactWayBaiduCode extends \yii\db\ActiveRecord
	{
		const BAIDU_HEAD = 'baidu';
		const EXPIRE_TIME = 86400;//二维码过期时间

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_baidu_code}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['way_id'], 'required'],
				[['way_id', 'add_num'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['config_id', 'state'], 'string', 'max' => 64],
				[['qr_code'], 'string', 'max' => 255],
				[['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayBaidu::className(), 'targetAttribute' => ['way_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'way_id'        => Yii::t('app', '企业微信联系我表ID'),
				'config_id'     => Yii::t('app', '联系方式的配置id'),
				'qr_code'       => Yii::t('app', '联系二维码的URL'),
				'state'         => Yii::t('app', '企业自定义的state参数，用于区分不同的添加渠道，在调用获取外部联系人详情时会返回该参数值'),
				'add_num'       => Yii::t('app', '添加人数'),
				'update_time'   => Yii::t('app', '修改时间'),
				'create_time'   => Yii::t('app', '添加时间'),
				'config_status' => Yii::t('app', '活码状态：0删除、1可用'),
				'expire_time'   => Yii::t('app', '活码过期时间'),
				'user'          => Yii::t('app', '成员数据'),
				'party'         => Yii::t('app', '部门数据'),
				'queue_id'      => Yii::t('app', '队列id'),
				'bd_vid'        => Yii::t('app', '转化页bd_vid'),
				'logidUrl'      => Yii::t('app', '转化页URL'),
				'newType'       => Yii::t('app', '转化类型'),
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
		public function getWay ()
		{
			return $this->hasOne(WorkContactWayBaidu::className(), ['id' => 'way_id']);
		}

		//生成config_id
		public static function addConfigId ($corp_id, $contactWayInfo)
		{
			try {
				$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
				if (empty($workApi)) {
					throw new InvalidDataException('未获取到api，请检查配置');
				}
				$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
				$wayResult = $workApi->ECAddContactWay($sendData);
				if ($wayResult['errcode'] != 0) {
					throw new InvalidDataException($wayResult['errmsg']);
				}
				$wayInfo        = $workApi->ECGetContactWay($wayResult['config_id']);
				$wayInfo        = SUtils::Object2Array($wayInfo);
				$contactWayData = $wayInfo['contact_way'];

				return ['config_id' => $contactWayData['config_id'], 'qr_code' => $contactWayData['qr_code']];
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

		//根据config_id进行修改
		public static function updateConfigId ($corp_id, $contactWayInfo)
		{
			try {
				$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
				if (empty($workApi)) {
					throw new InvalidDataException('未获取到api，请检查配置');
				}
				$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
				$workApi->ECUpdateContactWay($sendData);
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

		//删除config_id
		public static function delConfigId ($corp_id, $config_id)
		{
			try {
				$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$workApi->ECDelContactWay($config_id);
				}
			} catch (\Exception $e) {

			}
		}

		//获取活动参数二维码
		public static function getCode ($way_id, $bd_vid, $logidUrl, $newType)
		{
			$contactWay = WorkContactWayBaidu::findOne($way_id);
			if (empty($contactWay)) {
				throw new InvalidDataException('此活动不存在');
			}
			if (empty($bd_vid) || empty($logidUrl) || empty($newType)) {
				throw new InvalidDataException('二维码参数不能为空');
			}
			$corp_id   = $contactWay->corp_id;
			$baiDuInfo = static::findOne(['way_id' => $way_id, 'bd_vid' => $bd_vid]);

			//获取活码成员部门数据
			$userPartyArr = WorkContactWayBaidu::getUserPartyId($way_id);
			$userId       = $userPartyArr['userId'];
			$partyId      = $userPartyArr['partyId'];
			if (empty($userId) && empty($partyId)) {
				throw new InvalidDataException('此活动没有符合的成员和部门');
			}
			$skip_verify    = empty($contactWay['skip_verify']) ? true : false;
			$contactWayInfo = [
				'type'        => (int) $contactWay['type'],
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => $skip_verify,
				'user'        => $userId,
				'party'       => $partyId
			];

			$time = time();
			try {
				$expire_time = WorkContactWayBaiduCode::EXPIRE_TIME;
				$workApi     = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
				if (empty($workApi)) {
					throw new InvalidDataException('未获取到api，请检查配置');
				}
				$userJson  = json_encode($userId, JSON_UNESCAPED_UNICODE);
				$partyJson = json_encode($partyId, JSON_UNESCAPED_UNICODE);
				$is_add    = 0;
				if (!empty($baiDuInfo)) {
					if ($baiDuInfo->config_status == 0 || empty($baiDuInfo->qr_code)) {
						$baiDuInfo->config_status = 1;
						$baiDuInfo->user          = $userJson;
						$baiDuInfo->party         = $partyJson;
						$is_add                   = 1;
					} else {
						if ($baiDuInfo->user != $userJson || $baiDuInfo->party != $partyJson) {
							$contactWayInfo['state']     = $baiDuInfo->state;
							$contactWayInfo['config_id'] = $baiDuInfo->config_id;
							static::updateConfigId($corp_id, $contactWayInfo);
							$baiDuInfo->user  = $userJson;
							$baiDuInfo->party = $partyJson;
						}
					}
				} else {
					$baiDuInfo         = new WorkContactWayBaiduCode();
					$baiDuInfo->way_id = $way_id;
					$baiDuInfo->user   = $userJson;
					$baiDuInfo->party  = $partyJson;
					$is_add            = 1;
				}
				$baiDuInfo->expire_time = $time + $expire_time;
				$baiDuInfo->bd_vid      = $bd_vid;
				$baiDuInfo->logidUrl    = $logidUrl;
				$baiDuInfo->newType     = $newType;
				if (!$baiDuInfo->validate() || !$baiDuInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($baiDuInfo));
				}

				if ($is_add) {
					$state                   = static::BAIDU_HEAD . '_' . $way_id . '_' . $baiDuInfo->id;
					$contactWayInfo['state'] = $state;
					$contactWayData          = static::addConfigId($corp_id, $contactWayInfo);
					$baiDuInfo->config_id    = $contactWayData['config_id'];
					$baiDuInfo->qr_code      = $contactWayData['qr_code'];
					$baiDuInfo->state        = $state;
					$baiDuInfo->update();
				}

				//进队列
				if (!empty($baiDuInfo->queue_id)) {
					\Yii::$app->queue->remove($baiDuInfo->queue_id);
				}
				$queue_id            = \Yii::$app->queue->delay($expire_time)->push(new SyncWorkWayBaiduJob([
					'baidu_code_id' => $baiDuInfo->id,
					'corp_id'       => $corp_id
				]));
				$baiDuInfo->queue_id = $queue_id;
				$baiDuInfo->update();
			} catch (InvalidDataException $e) {
				\Yii::error($state . '_' . $e->getMessage(), 'getCode');
				throw new InvalidDataException($e->getMessage());
			}

			return $baiDuInfo->qr_code;
		}

		//修改时更新参数二维码
		public static function updateCode ($way_id, $code_id, $user = [], $party = [])
		{
			$contactWay = WorkContactWayBaidu::findOne($way_id);
			if (empty($contactWay)) {
				throw new InvalidDataException('此活动不存在');
			}
			$baiDuInfo = static::findOne(['way_id' => $way_id, 'id' => $code_id]);
			if (!empty($baiDuInfo)) {
				$time           = time();
				$expire_time    = WorkContactWayBaiduCode::EXPIRE_TIME;
				$skip_verify    = empty($contactWay->skip_verify) ? true : false;
				$contactWayInfo = [
					'type'        => (int) $contactWay->type,
					'scene'       => 2,
					'style'       => 1,
					'remark'      => '',
					'skip_verify' => $skip_verify,
					'state'       => $baiDuInfo->state,
					'user'        => $user,
					'party'       => $party
				];
				$userJson       = json_encode($user, JSON_UNESCAPED_UNICODE);
				$partyJson      = json_encode($party, JSON_UNESCAPED_UNICODE);
				if ($baiDuInfo->user != $userJson || $baiDuInfo->party != $partyJson) {
					$contactWayInfo['config_id'] = $baiDuInfo->config_id;
					static::updateConfigId($contactWay->corp_id, $contactWayInfo);
					$baiDuInfo->user        = $userJson;
					$baiDuInfo->party       = $partyJson;
					$baiDuInfo->expire_time = $time + $expire_time;
					$baiDuInfo->update();
					//进队列
					if (!empty($baiDuInfo->queue_id)) {
						\Yii::$app->queue->remove($baiDuInfo->queue_id);
					}
					$queue_id            = \Yii::$app->queue->delay($expire_time)->push(new SyncWorkWayBaiduJob([
						'baidu_code_id' => $baiDuInfo->id,
						'corp_id'       => $contactWay->corp_id
					]));
					$baiDuInfo->queue_id = $queue_id;
					$baiDuInfo->update();
				}
			}
		}

		//回传百度数据
		public static function sendConvertData ($baiDuCode)
		{
			/** @var WorkContactWayBaiduCode $baiDuCode * */
			$token            = '';
			$way_id           = $baiDuCode->way_id;
			$wayInfo          = WorkContactWayBaidu::findOne($way_id);
			$userCorpRelation = UserCorpRelation::findOne(['corp_id' => $wayInfo->corp_id]);
			$userBaiDu        = UserBaidu::findOne(['uid' => $userCorpRelation->uid]);
			if (!empty($userBaiDu)) {
				$token = $userBaiDu->token;
			}
			if (empty($token)) {
				return '';
			}
			$data              = [];
			$data['logidUrl']  = $baiDuCode->logidUrl;
			$data['newType']   = $baiDuCode->newType;
			$data['isConvert'] = 1;
			BaiduApiUtil::sendConvertData($token, [$data]);
		}
	}
