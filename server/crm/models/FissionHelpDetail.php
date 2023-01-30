<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%fission_help_detail}}".
	 *
	 * @property int         $id
	 * @property int         $fid            任务id
	 * @property int         $jid            参与表id
	 * @property int         $external_id    外部联系人id
	 * @property int         $status         是否是有效助力0否、1是
	 * @property int         $help_time      助力时间
	 * @property int         $is_remind      是否提醒过：0否、1是
	 *
	 * @property FissionJoin $j
	 */
	class FissionHelpDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fission_help_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['jid', 'external_id', 'status'], 'integer'],
				[['jid'], 'exist', 'skipOnError' => true, 'targetClass' => FissionJoin::className(), 'targetAttribute' => ['jid' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'fid'         => Yii::t('app', '任务id'),
				'jid'         => Yii::t('app', '参与表id'),
				'external_id' => Yii::t('app', '外部联系人id'),
				'status'      => Yii::t('app', '是否是有效助力0否、1是'),
				'help_time'   => Yii::t('app', '助力时间'),
				'is_remind'   => Yii::t('app', '是否提醒过：0否、1是'),
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
		public function getJ ()
		{
			return $this->hasOne(FissionJoin::className(), ['id' => 'jid']);
		}

		//更改助力状态
		public static function changeStatus ($data)
		{
			if (!empty($data['type'])) {
				$external_id = $data['external_userid'];
				$followUser  = WorkExternalContactFollowUser::find()->where(['user_id' => $data['user_id'], 'external_userid' => $external_id])->andWhere(['>', 'fission_id', 0])->one();
				if (empty($followUser)) {
					return false;
				}
				$state    = $followUser->state;
				$stateArr = explode('_', $state);
				if ($stateArr[0] != 'fission' || empty($stateArr[3])) {
					return false;
				}
				$fission_id  = $followUser->fission_id;
				$fissionInfo = Fission::findOne($fission_id);
				//如果任务过期，则不做修改
				$date = date('Y-m-d H:i:s');
				if (empty($fissionInfo) || $fissionInfo->end_time < $date || in_array($fissionInfo->status, [0, 3, 4, 5])) {
					return false;
				}
				$joinInfo = FissionJoin::findOne(['fid' => $fissionInfo->id, 'external_id' => $stateArr[3]]);
				if (empty($joinInfo)) {
					return false;
				}
				$detailInfo = static::findOne(['jid' => $joinInfo->id, 'external_id' => $external_id]);
				if (empty($detailInfo)) {
					return false;
				}

				$is_del = 0;
				try {
					if ($data['type'] == 'add') {
						if ($detailInfo->status == 1) {//表明已经添加过
							return false;
						}
						//如果助力者人数够了，就不给助力了
						if ($joinInfo->help_num >= $joinInfo->fission_num) {
							return false;
						}
						$detailInfo->status = 1;
						$detailInfo->update();
						$joinInfo->help_num += 1;
						$oldStatus          = $joinInfo->status;
						$is_complete        = 0;
						if ($joinInfo->help_num >= $joinInfo->fission_num) {
							if ($joinInfo->is_black == 0 && $oldStatus != 2) {
								$joinInfo->status        = 2;
								$joinInfo->complete_time = DateUtil::getCurrentTime();
								$is_complete             = 1;
								$joinInfo->update();
							}
						}
						if ($joinInfo->is_black == 0 && !empty($is_complete)) {
							$prizeRule = json_decode($fissionInfo->prize_rule, 1);
							$prize_num = $prizeRule[0]['prize_num'];
							if ($fissionInfo->complete_num < $prize_num) {
								$fissionInfo->complete_num += 1;
								$fissionInfo->update();
							}
							//没库存时结束此活动
							if (($fissionInfo->complete_num >= $prize_num) && !empty($fissionInfo->is_end) && $fissionInfo->status == 2) {
								$fissionInfo->status = 4;
								$fissionInfo->update();
								$is_del = 1;
							}
						}
					} elseif ($data['type'] == 'del') {
						//删企微好友/被拉黑助力失效是否失效:0否、1是
						if (empty($fissionInfo->is_invalid)) {
							return false;
						}
						if ($detailInfo->status == 0) {//表明已经删除过
							return false;
						}
						$detailInfo->status = 0;
						$detailInfo->update();
						$joinInfo->help_num -= 1;
						$oldStatus          = $joinInfo->status;
						$is_not_complete    = 0;
						if ($joinInfo->help_num < $joinInfo->fission_num) {
							if ($oldStatus == 2) {
								$joinInfo->status        = 1;
								$joinInfo->is_remind     = 0;
								$joinInfo->complete_time = '';
								$is_not_complete         = 1;
								$joinInfo->update();
							}
						}
						if (!empty($is_not_complete)) {
							$fissionInfo->complete_num -= 1;
							$fissionInfo->update();
						}
					}
				} catch (InvalidDataException $e) {
					throw new InvalidDataException($e->getMessage());
				}
				if (!empty($is_del)) {
					Fission::delConfigId($fissionInfo);
				}
			}
		}
	}
