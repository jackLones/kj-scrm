<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%money_payconfig}}".
	 *
	 * @property int    $id
	 * @property int    $uid            商户id
	 * @property int    $corp_id        企业微信id
	 * @property string $appid          appid(corpid)
	 * @property string $mchid          分配的商户号
	 * @property string $key            商户密钥
	 * @property string $apiclient_cert 证书apiclient_cert.pem文件路径
	 * @property string $apiclient_key  证书密钥apiclient_key.pem文件路径
	 * @property string $rootca         CA证书文件路径
	 * @property int    $status         状态：1启用2未启用3删除
	 * @property int    $add_time       添加时间
	 * @property int    $upt_time       修改时间
	 */
	class MoneyPayconfig extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%money_payconfig}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id'], 'required'],
				[['uid', 'corp_id', 'status', 'add_time', 'upt_time'], 'integer'],
				[['appid'], 'string', 'max' => 64],
				[['mchid'], 'string', 'max' => 30],
				[['key'], 'string', 'max' => 200],
				[['apiclient_cert', 'apiclient_key', 'rootca'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'uid'            => Yii::t('app', '商户id'),
				'corp_id'        => Yii::t('app', '企业微信id'),
				'appid'          => Yii::t('app', 'appid(corpid)'),
				'mchid'          => Yii::t('app', '分配的商户号'),
				'key'            => Yii::t('app', '商户密钥'),
				'apiclient_cert' => Yii::t('app', '证书apiclient_cert.pem文件路径'),
				'apiclient_key'  => Yii::t('app', '证书密钥apiclient_key.pem文件路径'),
				'rootca'         => Yii::t('app', 'CA证书文件路径'),
				'status'         => Yii::t('app', '状态：1启用2未启用3删除'),
				'add_time'       => Yii::t('app', '添加时间'),
				'upt_time'       => Yii::t('app', '修改时间'),
			];
		}
	}
