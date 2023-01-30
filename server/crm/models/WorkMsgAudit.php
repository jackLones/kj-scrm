<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use dovechen\yii2\weWork\Work;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_msg_audit}}".
	 *
	 * @property int                      $id
	 * @property int                      $corp_id              企业ID
	 * @property string                   $credit_code          统一社会信用代码
	 * @property string                   $contact_user         接口联系人
	 * @property string                   $contact_phone        接口信息人联系电话
	 * @property string                   $secret               聊天内容存档的Secret，可以在企业微信管理端--管理工具--聊天内容存档查看
	 * @property string                   $access_token         会话存档的access_token
	 * @property string                   $access_token_expires access_token有效期
	 * @property int                      $status               是否开启：1、开启；0、关闭；-1、未开启
	 * @property int                      $seq                  从指定的seq开始拉取消息，注意的是返回的消息从seq+1开始返回，seq为之前接口返回的最大seq值。首次使用请使用seq：0
	 * @property string                   $create_time          创建时间
	 *
	 * @property WorkCorp                 $corp
	 * @property WorkMsgAuditAgree[]      $workMsgAuditAgrees
	 * @property WorkMsgAuditInfo[]       $workMsgAuditInfos
	 * @property WorkMsgAuditKey[]        $workMsgAuditKeys
	 * @property WorkMsgAuditNoticeRule[] $workMsgAuditNoticeRules
	 * @property WorkMsgAuditUser[]       $workMsgAuditUsers
	 */
	class WorkMsgAudit extends \yii\db\ActiveRecord
	{
		const MSG_AUDIT_FORBIDDEN = -1;
		const MSG_AUDIT_CLOSE = 0;
		const MSG_AUDIT_OPEN = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'status', 'seq'], 'integer'],
				[['create_time'], 'safe'],
				[['credit_code'], 'string', 'max' => 64],
				[['contact_user'], 'string', 'max' => 32],
				[['secret', 'access_token'], 'string', 'max' => 255],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                   => Yii::t('app', 'ID'),
				'corp_id'              => Yii::t('app', '企业ID'),
				'credit_code'          => Yii::t('app', '统一社会信用代码'),
				'contact_user'         => Yii::t('app', '接口联系人'),
				'contact_phone'        => Yii::t('app', '接口信息人联系电话'),
				'secret'               => Yii::t('app', '聊天内容存档的Secret，可以在企业微信管理端--管理工具--聊天内容存档查看'),
				'access_token'         => Yii::t('app', '会话存档的access_token'),
				'access_token_expires' => Yii::t('app', 'access_token有效期'),
				'status'               => Yii::t('app', '是否开启：1、开启；0、关闭；-1、未开启'),
				'seq'                  => Yii::t('app', '从指定的seq开始拉取消息，注意的是返回的消息从seq+1开始返回，seq为之前接口返回的最大seq值。首次使用请使用seq：0'),
				'create_time'          => Yii::t('app', '创建时间'),
			];
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
		public function getWorkMsgAuditAgrees ()
		{
			return $this->hasMany(WorkMsgAuditAgree::className(), ['audit_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfo::className(), ['audit_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditKeys ()
		{
			return $this->hasMany(WorkMsgAuditKey::className(), ['audit_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditNoticeRules ()
		{
			return $this->hasMany(WorkMsgAuditNoticeRule::className(), ['audit_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditUsers ()
		{
			return $this->hasMany(WorkMsgAuditUser::className(), ['audit_id' => 'id']);
		}

		/**
		 * @param bool $withKey
		 * @param bool $withCorp
		 * @param bool $withConfig
		 * @param bool $withAuth
		 *
		 * @return array
		 */
		public function dumpData ($withKey = false, $withCorp = false, $withConfig = false, $withAuth = false)
		{
			$data = [
				'credit_code'   => $this->credit_code,
				'contact_user'  => $this->contact_user,
				'contact_phone' => $this->contact_phone,
				'secret'        => $this->secret,
				'status'        => $this->status,
				'seq'           => $this->seq,
				'create_time'   => $this->create_time,
			];

			if ($withKey) {
				$data['key_list'] = [];

				if (!empty($this->workMsgAuditKeys)) {
					foreach ($this->workMsgAuditKeys as $msgAuditKey) {
						array_push($data['key_list'], $msgAuditKey->dumpDate());
					}
				}
			}

			if ($withCorp) {
				$data['corp'] = $this->corp->dumpData($withConfig, $withAuth);
			}

			return $data;
		}

		/**
		 * @return array
		 */
		public function dumpAccessTokenData ()
		{
			return [
				'secret'               => $this->secret,
				'access_token'         => $this->access_token,
				'access_token_expires' => $this->access_token_expires,
			];
		}

		/**
		 * @param       $corpId
		 * @param array $data
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($corpId, $data = [])
		{
			$msgAudit = self::findOne(['corp_id' => $corpId]);

			if (empty($msgAudit)) {
				$msgAudit              = new self();
				$msgAudit->corp_id     = $corpId;
				$msgAudit->status      = self::MSG_AUDIT_FORBIDDEN;
				$msgAudit->seq         = 0;
				$msgAudit->create_time = DateUtil::getCurrentTime();
			}

			if (!empty($data['credit_code'])) {
				$msgAudit->credit_code = $data['credit_code'];
			}

			if (!empty($data['contact_user'])) {
				$msgAudit->contact_user = $data['contact_user'];
			}

			if (!empty($data['contact_phone'])) {
				$msgAudit->contact_phone = $data['contact_phone'];
			}

			if (!empty($data['secret'])) {
				$msgAudit->secret = $data['secret'];

				if ($msgAudit->status == self::MSG_AUDIT_FORBIDDEN) {
					$msgAudit->status = self::MSG_AUDIT_OPEN;
				}
			}

			if ($msgAudit->dirtyAttributes) {
				if (!$msgAudit->validate() || !$msgAudit->save()) {
					throw new InvalidDataException(SUtils::modelError($msgAudit));
				}
			}

			return $msgAudit->id;
		}

		/**
		 * @param $corpId
		 *
		 * @return array
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getTokenInfo ($corpId)
		{
			$result   = [];
			$workCorp = WorkCorp::findOne($corpId);

			if (!empty($workCorp) && !empty($workCorp->workMsgAudit)) {
				$workMsgAudit = $workCorp->workMsgAudit;
				if (empty($workMsgAudit->access_token) || $workMsgAudit->access_token_expires < (time() - 60)) {
					/** @var Work $work */
					$work = Yii::createObject([
						'class'  => Work::className(),
						'corpid' => $workCorp->corpid,
						'secret' => $workMsgAudit->secret,
					]);

					try {
						$work->GetAccessToken(true);
					} catch (\Exception $e) {
						$message = $e->getMessage();
						if (strpos($message, '40001') !== false) {
							$workMsgAudit->secret = NULL;
							$workMsgAudit->save();
						}

						throw new InvalidConfigException($message);
					}

					$workMsgAudit->access_token         = $work->access_token;
					$workMsgAudit->access_token_expires = $work->access_token_expire;
					$workMsgAudit->save();

					$result = $workMsgAudit->dumpAccessTokenData();
				} else {
					$result = $workMsgAudit->dumpAccessTokenData();
				}
			}

			return $result;
		}
	}
