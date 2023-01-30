<?php

	namespace app\models;

	use app\components\InvalidParameterException;
	use app\util\StringUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_register_code}}".
	 *
	 * @property int                  $id
	 * @property int                  $template_id                       推广包ID
	 * @property string               $state                             用户自定义的状态值。只支持英文字母和数字。若指定该参数，接口 查询注册状态 及 注册完成回调事件 会相应返回该字段值
	 * @property string               $register_code                     注册码，只能消费一次。在访问注册链接时消费。
	 * @property string               $register_code_expires             register_code有效期，生成链接需要在有效期内点击跳转
	 * @property string               $corpid                            企业的corpid
	 * @property string               $contact_sync_access_token         通讯录api接口调用凭证，有全部通讯录读写权限。
	 * @property string               $contact_sync_access_token_expires access_token凭证的有效时间
	 * @property string               $create_time                       创建时间
	 * @property string               $auth_user_info_user_id            授权管理员的userid
	 *
	 * @property WorkProviderTemplate $template
	 */
	class WorkRegisterCode extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_register_code}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['template_id'], 'integer'],
				[['create_time'], 'safe'],
				[['state'], 'string', 'max' => 8],
				[['register_code', 'contact_sync_access_token', 'auth_user_info_user_id'], 'string', 'max' => 255],
				[['register_code_expires', 'contact_sync_access_token_expires'], 'string', 'max' => 16],
				[['corpid'], 'string', 'max' => 64],
				[['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkProviderTemplate::className(), 'targetAttribute' => ['template_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                                => Yii::t('app', 'ID'),
				'template_id'                       => Yii::t('app', '推广包ID'),
				'state'                             => Yii::t('app', '用户自定义的状态值。只支持英文字母和数字。若指定该参数，接口 查询注册状态 及 注册完成回调事件 会相应返回该字段值'),
				'register_code'                     => Yii::t('app', '注册码，只能消费一次。在访问注册链接时消费。'),
				'register_code_expires'             => Yii::t('app', 'register_code有效期，生成链接需要在有效期内点击跳转'),
				'corpid'                            => Yii::t('app', '企业的corpid'),
				'contact_sync_access_token'         => Yii::t('app', '通讯录api接口调用凭证，有全部通讯录读写权限。'),
				'contact_sync_access_token_expires' => Yii::t('app', 'access_token凭证的有效时间'),
				'create_time'                       => Yii::t('app', '创建时间'),
				'auth_user_info_user_id'            => Yii::t('app', '授权管理员的userid'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplate ()
		{
			return $this->hasOne(WorkProviderTemplate::className(), ['id' => 'template_id']);
		}

		/**
		 * @return string
		 */
		public static function getState ()
		{
			$state = StringUtil::randomStr(8);
			$info  = self::findOne(['state' => $state]);

			if (empty($info)) {
				return $state;
			} else {
				return self::getState();
			}
		}

		/**
		 * @param $registerInfo
		 *
		 * @return $this
		 *
		 * @throws InvalidParameterException
		 */
		public function setData ($registerInfo)
		{
			if (!empty($registerInfo['corpid'])) {
				$this->corpid = $registerInfo['corpid'];
			}
			if (!empty($registerInfo['contact_sync']) && !empty($registerInfo['contact_sync']['access_token'])) {
				$this->contact_sync_access_token = $registerInfo['contact_sync']['access_token'];
			}
			if (!empty($registerInfo['contact_sync']) && !empty($registerInfo['contact_sync']['expires_in'])) {
				$this->contact_sync_access_token_expires = (string) (time() + $registerInfo['contact_sync']['expires_in']);
			}
			if (!empty($registerInfo['auth_user_info']) && !empty($registerInfo['auth_user_info']['userid'])) {
				$this->auth_user_info_user_id = $registerInfo['auth_user_info']['userid'];
			}

			if ($this->dirtyAttributes) {
				if (!$this->validate() || !$this->save()) {
					throw new InvalidParameterException(SUtils::modelError($this));
				}
			}

			return $this;
		}
	}
