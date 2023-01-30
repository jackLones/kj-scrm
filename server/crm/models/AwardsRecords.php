<?php

	namespace app\models;

	use app\util\DateUtil;
	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%awards_records}}".
	 *
	 * @property int            $id
	 * @property int            $award_id         活动id
	 * @property int            $join_id          参与人id
	 * @property int            $aid              奖品id
	 * @property string         $nick_name        昵称
	 * @property string         $avatar           头像
	 * @property string         $award_name       奖品名称
	 * @property int            $is_record        是否中奖 0 未中奖 1 已中奖
	 * @property int            $status           状态 0 未核销 1 已核销
	 * @property string         $create_time      创建时间
	 *
	 * @property AwardsActivity $award
	 */
	class AwardsRecords extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%awards_records}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['award_id', 'status', 'is_record', 'aid', 'join_id'], 'integer'],
				[['create_time'], 'safe'],
				[['nick_name', 'avatar'], 'string', 'max' => 255],
				[['award_name'], 'string', 'max' => 100],
				[['award_id'], 'exist', 'skipOnError' => true, 'targetClass' => AwardsActivity::className(), 'targetAttribute' => ['award_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'award_id'    => '活动id',
				'aid'         => '奖品id 关联awards_list表',
				'join_id'     => '参与人id',
				'nick_name'   => '昵称',
				'avatar'      => '头像',
				'award_name'  => '奖品名称',
				'is_record'   => '是否中奖 0 未中奖 1 已中奖',
				'status'      => '状态 0 未核销 1 已核销',
				'create_time' => '创建时间',
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
		public function getAward ()
		{
			return $this->hasOne(AwardsActivity::className(), ['id' => 'award_id']);
		}

		/**
		 *
		 * @return array
		 *
		 */
		public function dumpData ()
		{
			$result               = [
				'key'         => $this->id,
				'id'          => $this->id,
				'nick_name'   => urldecode($this->nick_name),
				'avatar'      => $this->avatar,
				'award_name'  => !empty($this->award_name) ? $this->award_name : '--',
				'is_record'   => $this->is_record,
				'status'      => $this->status,
				'create_time' => $this->create_time,
			];
			$awardList            = AwardsList::findOne($this->aid);
			$result['prize_type'] = !empty($awardList) ? $awardList->prize_type : 0;

			return $result;
		}

		/**
		 * @param $data
		 *
		 * @throws InvalidDataException
		 */
		public static function add ($data)
		{
			$awardRecord              = new AwardsRecords();
			$awardRecord->create_time = DateUtil::getCurrentTime();
			$awardRecord->award_id    = $data['award_id'];
			if (!$awardRecord->validate() || !$awardRecord->save()) {
				throw new InvalidDataException(SUtils::modelError($awardRecord));
			}
		}

		/***
		 * @param $id
		 *
		 * @return array
		 *
		 */
		public static function getRecords ($id)
		{
			$records = static::find()->andWhere(['award_id' => $id, 'is_record' => 1])->orderBy('id desc')->all();
			$data    = [];//中奖数据
			if (!empty($records)) {
				foreach ($records as $key => $recordData) {
					$data[$key]['nick_name']  = urldecode($recordData->nick_name);
					$data[$key]['award_name'] = '获得' . $recordData->award_name;
				}
			}

			return $data;
		}

	}
