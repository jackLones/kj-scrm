<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\PemUtils;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_key}}".
	 *
	 * @property int          $id
	 * @property int          $audit_id         会话存档ID
	 * @property int          $key_version      加密此条消息使用的公钥版本号。Uint32类型
	 * @property string       $private_key      私钥内容
	 * @property string       $private_key_path 私钥证书地址
	 * @property string       $public_key       公钥内容
	 * @property string       $public_key_path  公钥证书地址
	 *
	 * @property WorkMsgAudit $audit
	 */
	class WorkMsgAuditKey extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_key}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_id', 'key_version'], 'integer'],
				[['private_key', 'private_key_path', 'public_key', 'public_key_path'], 'string'],
				[['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAudit::className(), 'targetAttribute' => ['audit_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'audit_id'         => Yii::t('app', '会话存档ID'),
				'key_version'      => Yii::t('app', '加密此条消息使用的公钥版本号。Uint32类型'),
				'private_key'      => Yii::t('app', '私钥内容'),
				'private_key_path' => Yii::t('app', '私钥证书地址'),
				'public_key'       => Yii::t('app', '公钥内容'),
				'public_key_path'  => Yii::t('app', '公钥证书地址'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAudit ()
		{
			return $this->hasOne(WorkMsgAudit::className(), ['id' => 'audit_id']);
		}

		/**
		 * @param bool $withMsgAudit
		 * @param bool $withCorp
		 * @param bool $withConfig
		 * @param bool $withAuth
		 *
		 * @return array
		 *
		 */
		public function dumpDate ($withMsgAudit = false, $withCorp = false, $withConfig = false, $withAuth = false)
		{
			$data = [
				'key_id'           => $this->id,
				'key_version'      => $this->key_version,
				'private_key'      => $this->private_key,
				'private_key_path' => $this->private_key_path,
				'public_key'       => $this->public_key,
				'public_key_path'  => $this->public_key_path,
			];

			if ($withMsgAudit) {
				$data['audit'] = $this->audit->dumpData(false, $withCorp, $withConfig, $withAuth);
			}

			return $data;
		}

		/**
		 * @param $corpId
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 */
		public static function create ($corpId)
		{
			if (empty($corpId)) {
				throw new InvalidParameterException('缺少必要参数');
			}
			$workCorp = WorkCorp::findOne($corpId);
			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确');
			}

			$result = PemUtils::create2048($workCorp->userCorpRelations[0]->uid);

			if ($result['error'] != 0) {
				throw new InvalidDataException($result['msg']);
			}

			$msgAuditKey = new self();
			if (empty($workCorp->workMsgAudit)) {
				$msgAuditId = WorkMsgAudit::create($corpId);
			} else {
				$msgAuditId = $workCorp->workMsgAudit->id;
			}

			$msgAuditKey->audit_id         = $msgAuditId;
			$msgAuditKey->private_key      = $result['private_key'];
			$msgAuditKey->private_key_path = $result['private_path'];
			$msgAuditKey->public_key       = $result['public_key'];
			$msgAuditKey->public_key_path  = $result['public_path'];

			if (!$msgAuditKey->validate() || !$msgAuditKey->save()) {
				throw new InvalidDataException(SUtils::modelError($msgAuditKey));
			}

			return $msgAuditKey->dumpDate();
		}

		/**
		 * @param $keyId
		 * @param $version
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 */
		public static function changeVersion ($keyId, $version)
		{
			if (empty($keyId) || empty($version)) {
				throw new InvalidParameterException('缺少必要参数');
			}

			$msgAuditKey = self::findOne($keyId);
			if (empty($msgAuditKey)) {
				throw new InvalidDataException('参数不正确');
			}

			$msgAuditKey->key_version = $version;

			if (!$msgAuditKey->validate() || !$msgAuditKey->save()) {
				throw new InvalidDataException(SUtils::modelError($msgAuditKey));
			}

			return $msgAuditKey->dumpDate();
		}
	}
