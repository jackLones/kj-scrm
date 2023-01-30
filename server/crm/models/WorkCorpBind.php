<?php

	namespace app\models;

	use dovechen\yii2\weWork\Work;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_corp_bind}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id                       企业ID
	 * @property string   $token                         Token用于计算签名
	 * @property string   $encode_aes_key                EncodingAESKey用于消息内容加密
	 * @property string   $book_secret                   通讯录管理secret。在“管理工具”-“通讯录同步”里面查看（需开启“API接口同步”）
	 * @property string   $book_access_token             通讯录管理access_token
	 * @property string   $book_access_token_expires     book_access_token有效期
	 * @property int      $book_status                   是否开启通讯录事件0：不开启；1：开启
	 * @property string   $external_secret               外部联系人管理secret。在“客户联系”栏，点开“API”小按钮，即可看到
	 * @property string   $external_access_token         外部联系人管理saccess_token
	 * @property string   $external_access_token_expires external_access_token有效期
	 * @property int      $external_status               是否开启外部联系人事件0：不开启；1：开启
	 * @property string   $create_time                   创建时间
	 *
	 * @property WorkCorp $corp
	 */
	class WorkCorpBind extends \yii\db\ActiveRecord
	{
		const BOOK_CLOSE = 0;
		const BOOK_OPEN = 1;

		const EXTERNAL_CLOSE = 0;
		const EXTERNAL_OPEN = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_corp_bind}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'book_status', 'external_status'], 'integer'],
				[['create_time'], 'safe'],
				[['token', 'encode_aes_key', 'book_access_token', 'external_access_token'], 'string', 'max' => 255],
				[['book_secret', 'external_secret'], 'string', 'max' => 64],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                            => Yii::t('app', 'ID'),
				'corp_id'                       => Yii::t('app', '企业ID'),
				'token'                         => Yii::t('app', 'Token用于计算签名'),
				'encode_aes_key'                => Yii::t('app', 'EncodingAESKey用于消息内容加密'),
				'book_secret'                   => Yii::t('app', '通讯录管理secret。在“管理工具”-“通讯录同步”里面查看（需开启“API接口同步”）'),
				'book_access_token'             => Yii::t('app', '通讯录管理access_token'),
				'book_access_token_expires'     => Yii::t('app', 'book_access_token有效期'),
				'book_status'                   => Yii::t('app', '是否开启通讯录事件0：不开启；1：开启'),
				'external_secret'               => Yii::t('app', '外部联系人管理secret。在“客户联系”栏，点开“API”小按钮，即可看到'),
				'external_access_token'         => Yii::t('app', '外部联系人管理saccess_token'),
				'external_access_token_expires' => Yii::t('app', 'external_access_token有效期'),
				'external_status'               => Yii::t('app', '是否开启外部联系人事件0：不开启；1：开启'),
				'create_time'                   => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		public function dumpData ()
		{
			return [
				'corp_id'                       => $this->corp_id,
				'token'                         => $this->token,
				'encode_aes_key'                => $this->encode_aes_key,
				'book_secret'                   => $this->book_secret,
				'book_access_token'             => $this->book_access_token,
				'book_access_token_expires'     => $this->book_access_token_expires,
				'book_status'                   => $this->book_status,
				'book_url'                      => \Yii::$app->params['site_url'] . '/work/event/index/' . $this->corp_id,
				'external_secret'               => $this->external_secret,
				'external_access_token'         => $this->external_access_token,
				'external_access_token_expires' => $this->external_access_token_expires,
				'external_status'               => $this->external_status,
				'external_url'                  => \Yii::$app->params['site_url'] . '/work/event/index/' . $this->corp_id,
			];
		}

		public function dumpBookData ()
		{
			return [
				'corp_id'                   => $this->corp_id,
				'token'                     => $this->token,
				'encode_aes_key'            => $this->encode_aes_key,
				'book_secret'               => $this->book_secret,
				'book_access_token'         => $this->book_access_token,
				'book_access_token_expires' => $this->book_access_token_expires,
				'book_status'               => $this->book_status,
				'book_url'                  => \Yii::$app->params['site_url'] . '/work/event/index/' . $this->corp_id,
			];
		}

		public function dumpExternalData ()
		{
			return [
				'corp_id'                       => $this->corp_id,
				'token'                         => $this->token,
				'encode_aes_key'                => $this->encode_aes_key,
				'external_secret'               => $this->external_secret,
				'external_access_token'         => $this->external_access_token,
				'external_access_token_expires' => $this->external_access_token_expires,
				'external_status'               => $this->external_status,
				'external_url'                  => \Yii::$app->params['site_url'] . '/work/event/index/' . $this->corp_id,
			];
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
		public static function getBookTokenInfo ($corpId)
		{
			$result       = [];
			$workCorpBind = static::findOne(['corp_id' => $corpId]);

			if (!empty($workCorpBind)) {
				if (empty($workCorpBind->book_access_token) || $workCorpBind->book_access_token_expires < (time() - 60)) {
					/** @var Work $work */
					$work = Yii::createObject([
						'class'  => Work::className(),
						'corpid' => $workCorpBind->corp->corpid,
						'secret' => $workCorpBind->book_secret,
					]);

					try {
						$work->GetAccessToken(true);
					} catch (\Exception $e) {
						$message = $e->getMessage();
						if (strpos($message, '40001') !== false) {
							$workCorpBind->book_secret = NULL;
							$workCorpBind->book_status = self::BOOK_CLOSE;
							$workCorpBind->save();
						}

						throw new InvalidConfigException($message);
					}

					$workCorpBind->book_access_token         = $work->access_token;
					$workCorpBind->book_access_token_expires = $work->access_token_expire;
					$workCorpBind->save();

					$result = $workCorpBind->dumpBookData();
				} else {
					$result = $workCorpBind->dumpBookData();
				}
			}

			return $result;
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
		public static function getExternalTokenInfo ($corpId)
		{
			$result       = [];
			$workCorpBind = static::findOne(['corp_id' => $corpId]);

			if (!empty($workCorpBind)) {
				if (empty($workCorpBind->external_access_token) || $workCorpBind->external_access_token_expires < (time() - 60)) {
					/** @var Work $work */
					$work = Yii::createObject([
						'class'  => Work::className(),
						'corpid' => $workCorpBind->corp->corpid,
						'secret' => $workCorpBind->external_secret,
					]);

					try {
						$work->GetAccessToken(true);
					} catch (\Exception $e) {
						$message = $e->getMessage();
						if (strpos($message, '40001') !== false) {
							$workCorpBind->external_secret = NULL;
							$workCorpBind->external_status = self::EXTERNAL_CLOSE;
							$workCorpBind->save();
						}

						throw new InvalidConfigException($message);
					}

					$workCorpBind->external_access_token         = $work->access_token;
					$workCorpBind->external_access_token_expires = $work->access_token_expire;
					$workCorpBind->save();

					$result = $workCorpBind->dumpExternalData();
				} else {
					$result = $workCorpBind->dumpExternalData();
				}
			}

			return $result;
		}
	}
