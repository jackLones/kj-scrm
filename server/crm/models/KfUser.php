<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%kf_user}}".
	 *
	 * @property int         $id
	 * @property int         $author_id     公众号ID
	 * @property int         $kf_id         客服工号
	 * @property string      $kf_account    完整客服账号，格式为：账号前缀@公众号微信号
	 * @property string      $kf_nick       客服昵称
	 * @property string      $kf_wx         客服微信号
	 * @property string      $kf_headimgurl 客服头像
	 * @property string      $create_time   创建时间
	 *
	 * @property FansMsg[]   $fansMsgs
	 * @property WxAuthorize $author
	 * @property MiniMsg[]   $miniMsgs
	 */
	class KfUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%kf_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'kf_id'], 'integer'],
				[['kf_headimgurl'], 'string'],
				[['create_time'], 'safe'],
				[['kf_account'], 'string', 'max' => 80],
				[['kf_nick'], 'string', 'max' => 32],
				[['kf_wx'], 'string', 'max' => 16],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'author_id'     => Yii::t('app', '公众号ID'),
				'kf_id'         => Yii::t('app', '客服工号'),
				'kf_account'    => Yii::t('app', '完整客服账号，格式为：账号前缀@公众号微信号'),
				'kf_nick'       => Yii::t('app', '客服昵称'),
				'kf_wx'         => Yii::t('app', '客服微信号'),
				'kf_headimgurl' => Yii::t('app', '客服头像'),
				'create_time'   => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansMsgs ()
		{
			return $this->hasMany(FansMsg::className(), ['kf_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMiniMsgs ()
		{
			return $this->hasMany(MiniMsg::className(), ['kf_id' => 'id']);
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			$result = [
				'id'     => $this->id,
				'kf_id'  => $this->kf_id,
				'nick'   => $this->kf_account,
				'wx'     => $this->kf_wx,
				'avatar' => $this->kf_headimgurl,
			];

			return $result;
		}
	}
