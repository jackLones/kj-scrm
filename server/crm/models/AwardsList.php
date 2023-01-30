<?php

	namespace app\models;

	use Yii;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\components\InvalidDataException;

	/**
	 * This is the model class for table "{{%awards_list}}".
	 *
	 * @property int            $id
	 * @property int            $award_id     活动id
	 * @property string         $name         奖品名称
	 * @property int            $num          奖品数量
	 * @property int            $last_num     奖品剩余数量
	 * @property string         $percentage   中奖率
	 * @property string         $logo         奖品图片
	 * @property string         $description  说明
	 * @property string         $prize_type   奖品类型：0、实物，1、红包
	 * @property string         $amount       红包金额
	 * @property string         $create_time  创建时间
	 * @property int            $key          当前奖项索引
	 * @property string         $title        奖项
	 * @property string         $success_tags 完成后打上指定标签
	 *
	 * @property AwardsActivity $award
	 */
	class AwardsList extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%awards_list}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['award_id', 'num', 'last_num', 'key'], 'integer'],
				[['amount'], 'number'],
				[['create_time'], 'safe'],
				[['key', 'title'], 'required'],
				[['name', 'logo', 'description', 'success_tags'], 'string', 'max' => 255],
				[['title'], 'string', 'max' => 21],
				[['award_id'], 'exist', 'skipOnError' => true, 'targetClass' => AwardsActivity::className(), 'targetAttribute' => ['award_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'award_id'     => Yii::t('app', '活动id'),
				'name'         => Yii::t('app', '奖品名称'),
				'num'          => Yii::t('app', '奖品数量'),
				'last_num'     => Yii::t('app', '奖品剩余数量'),
				'percentage'   => Yii::t('app', '中奖率'),
				'logo'         => Yii::t('app', '奖品图片'),
				'description'  => Yii::t('app', '说明'),
				'prize_type'   => Yii::t('app', '奖品类型：0、实物，1、红包'),
				'amount'       => Yii::t('app', '红包金额'),
				'create_time'  => Yii::t('app', '创建时间'),
				'key'          => Yii::t('app', '当前奖项索引'),
				'title'        => Yii::t('app', '奖项'),
				'success_tags' => Yii::t('app', '完成后打上指定标签'),
			];
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
		 */
		public function dumpData ()
		{
			$result = [
				'key'          => $this->key,
				'id'           => $this->id,
				'name'         => $this->name,
				'num'          => $this->num,
				'last_num'     => $this->last_num,
				'percentage'   => $this->percentage,
				'logo'         => $this->logo,
				'description'  => $this->description,
				'prize_type'   => $this->prize_type,
				'amount'       => $this->amount,
				'title'        => $this->title,
				'success_tags' => empty($this->success_tags) ? [] : json_decode($this->success_tags),
			];

			return $result;
		}

		/**
		 * @param $data
		 * @param $award_id
		 *
		 * @throws InvalidDataException
		 */
		public static function setAwardList ($data, $award_id)
		{
			\Yii::error($data, 'setAwardList');
			if (!empty($data)) {
				$idArr = [];
				foreach ($data as $v) {
					$id = isset($v['id']) ? $v['id'] : 0;
					if (!empty($id)) {
						$awardList = AwardsList::findOne($v['id']);
					} else {
						$awardList = new AwardsList();
					}
					if (!empty($v['name']) && mb_strlen($v['name'], 'utf-8') > 20) {
						throw new InvalidDataException("奖品名称不能超过20个字");
					}
					if (intval($v['num']) > 99999999) {
						throw new InvalidDataException("奖品数量不能超过99999999");
					}
					if (empty($v['prize_type']) && !empty($v['description']) && mb_strlen($v['description'], 'utf-8') > 100) {
						throw new InvalidDataException("奖品说明不能超过100个字");
					}
					if (empty($v['prize_type'])) {
						$prize_type  = 0;
						$name        = $v['name'];
						$logo        = $v['logo'];
						$description = $v['description'];
						$amount      = 0;
					} else {
						$prize_type  = 1;
						$name        = $v['amount'] . '元红包';
						$logo        = AwardsActivity::REDPACK_LOGO;
						$description = '';
						$amount      = !empty($v['amount']) ? $v['amount'] : 0;
					}
					$awardList->award_id     = $award_id;
					$awardList->key          = $v['key'];
					$awardList->title        = $v['title'];
					$awardList->name         = $name;
					$awardList->num          = $v['num'];
					$awardList->last_num     = $v['num'];
					$awardList->success_tags = isset($v['success_tags']) ? json_encode($v['success_tags']) : '';
					$awardList->percentage   = trim($v['percentage']);
					$awardList->logo         = $logo;
					$awardList->description  = $description;
					$awardList->prize_type   = $prize_type;
					$awardList->amount       = $amount;
					$awardList->create_time  = DateUtil::getCurrentTime();
					if (!empty($awardList->dirtyAttributes)) {
						if (!$awardList->validate() || !$awardList->save()) {
							\Yii::error(SUtils::modelError($awardList), 'setAwardList');
							throw new InvalidDataException(SUtils::modelError($awardList));
						}
					}
					array_push($idArr, $awardList->id);
				}
				if (!empty($idArr)) {
					AwardsList::deleteAll(['and', ['award_id' => $award_id], ['not', ['id' => $idArr]]]);
				}
			}

		}
	}
