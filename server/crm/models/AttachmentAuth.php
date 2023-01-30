<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%attachment_auth}}".
	 *
	 * @property int        $id
	 * @property int        $corp_id       授权的企业ID
	 * @property int        $attachment_id 附件ID
	 * @property string     $user_key      生效成员
	 * @property string     $user          用户userID列表
	 * @property string     $party         生效部门
	 * @property int        $add_time      添加时间
	 *
	 * @property WorkCorp   $corp
	 * @property Attachment $attachment
	 */
	class AttachmentAuth extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%attachment_auth}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'attachment_id', 'add_time'], 'integer'],
				[['user_key', 'user', 'party'], 'string'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['attachment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attachment::className(), 'targetAttribute' => ['attachment_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'corp_id'       => Yii::t('app', '授权的企业ID'),
				'attachment_id' => Yii::t('app', '附件ID'),
				'user_key'      => Yii::t('app', '生效成员'),
				'user'          => Yii::t('app', '用户userID列表'),
				'party'         => Yii::t('app', '生效部门'),
				'add_time'      => Yii::t('app', '添加时间'),
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
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachment ()
		{
			return $this->hasOne(Attachment::className(), ['id' => 'attachment_id']);
		}

		//获取数据
		public static function getData ($attachmentId)
		{
			$result   = [];
			$authList = static::find()->where(['attachment_id' => $attachmentId])->all();
			if (!empty($authList)) {
				/**@var AttachmentAuth $auth**/
				foreach ($authList as $key => $auth) {
					$user = $party = [];
					if(!empty($auth->user)){
						$user = explode(',',$auth->user);
					}
					if(!empty($auth->party)){
						$party = explode(',',$auth->party);
					}
					$result[$key] = [
						'corp_id' => $auth->corp_id,
						'data'    => [
							'user'  => $user,
							'party' => $party
						]
					];
				}
			}

			return $result;
		}

		//设置数据
		public static function setData ($attachmentId, $data)
		{
			if (!is_array($data) || empty($attachmentId)) {
				return '';
			}
			$time     = time();
			$insertId = [];
			foreach ($data as $v) {
				$corpId = $v['corp_id'];
				$auth   = static::findOne(['attachment_id' => $attachmentId, 'corp_id' => $corpId]);
				if (empty($auth)) {
					$auth                = new AttachmentAuth();
					$auth->attachment_id = $attachmentId;
					$auth->corp_id       = $corpId;
				}
				$auth->user_key = json_encode($v);
				if (!empty($v['data']['user'])) {
					$userIds    = array_column($v['data']['user'], 'id');
					$auth->user = implode(',', $userIds);
				} else {
					$auth->user = '';
				}

				if (!empty($v['data']['party'])) {
					$partyIds    = array_column($v['data']['party'], 'id');
					$auth->party = implode(',', $partyIds);
				} else {
					$auth->party = '';
				}
				$auth->add_time = $time;
				if (!$auth->validate() || !$auth->save()) {
					throw new InvalidDataException(SUtils::modelError($auth));
				}
				array_push($insertId, $auth->id);
			}

			//删除已不存在企业微信规则
			static::deleteAll(['and', ['attachment_id' => $attachmentId], ['not in', 'id', $insertId]]);

		}

		//删除数据
		public static function delData ($attachmentId)
		{
			static::deleteAll(['attachment_id' => $attachmentId]);
		}
	}
