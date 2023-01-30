<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_claim}}".
	 *
	 * @property int      $id
	 * @property int      $uid                    帐号id
	 * @property int      $corp_id                授权的企业ID
	 * @property int      $sea_id                 公海客户id
	 * @property int      $type                   0非企微客户、1企微客户
	 * @property int      $claim_type             0回收、1认领
	 * @property int      $user_id                认领成员
	 * @property int      $external_userid        企微外部联系人id
	 * @property int      $follow_user_id         关联表id
	 * @property string   $claim_time             认领时间
	 * @property string   $reclaim_time           回收时间
	 * @property string   $is_claim               是否算作认领次数
	 *
	 * @property WorkCorp $corp
	 */
	class PublicSeaClaim extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_claim}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'sea_id', 'type', 'claim_type', 'user_id', 'follow_user_id'], 'integer'],
				[['claim_time', 'reclaim_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'uid'             => Yii::t('app', '帐号id'),
				'corp_id'         => Yii::t('app', '授权的企业ID'),
				'sea_id'          => Yii::t('app', '公海客户id'),
				'type'            => Yii::t('app', '0非企微客户、1企微客户'),
				'claim_type'      => Yii::t('app', '0回收、1认领'),
				'user_id'         => Yii::t('app', '认领成员'),
				'external_userid' => Yii::t('app', '企微外部联系人id'),
				'follow_user_id'  => Yii::t('app', '关联表id'),
				'claim_time'      => Yii::t('app', '认领时间'),
				'reclaim_time'    => Yii::t('app', '回收时间'),
				'is_claim'        => Yii::t('app', '是否算作认领次数'),
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

		//获取认领数据
		public static function getClaimData ($seaId, $corpId)
		{
			$claimData = [];
			$claimList = PublicSeaClaim::find()->where(['corp_id' => $corpId, 'sea_id' => $seaId, 'claim_type' => 1]);
			$count     = $claimList->count();
			$claimList = $claimList->orderBy(['id' => SORT_DESC])->all();
			/**@var PublicSeaClaim $claim * */
			foreach ($claimList as $key => $claim) {
				$workUser                        = WorkUser::findOne($claim->user_id);
				$claimData[$key]['user_id']      = $claim->user_id;
				$claimData[$key]['num']          = $count - $key;
				$claimData[$key]['name']         = !empty($workUser) ? $workUser->name : '未知';
				$claimData[$key]['claim_time']   = date('Y-m-d H:i', $claim->claim_time);
				$claimData[$key]['reclaim_time'] = date('Y-m-d', $claim->reclaim_time);
			}

			return $claimData;
		}
	}
