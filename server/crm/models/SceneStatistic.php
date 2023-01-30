<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%scene_statistic}}".
	 *
	 * @property int    $id
	 * @property int    $scene_id     参数二维码ID
	 * @property int    $scan_times   扫码次数
	 * @property int    $scan_num     扫码人数
	 * @property int    $subscribe    新增粉丝数
	 * @property int    $unsubscribe  流失粉丝数
	 * @property int    $net_increase 净增粉丝数
	 * @property string $data_time    统计时间
	 * @property int    $is_month     0:按天，1、按月
	 *
	 * @property Scene  $scene
	 */
	class SceneStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%scene_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['scene_id', 'scan_times', 'scan_num', 'subscribe', 'unsubscribe', 'net_increase'], 'integer'],
				[['data_time'], 'safe'],
				[['scene_id'], 'exist', 'skipOnError' => true, 'targetClass' => Scene::className(), 'targetAttribute' => ['scene_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'scene_id'     => Yii::t('app', '参数二维码ID'),
				'scan_times'   => Yii::t('app', '扫码次数'),
				'scan_num'     => Yii::t('app', '扫码人数'),
				'subscribe'    => Yii::t('app', '新增粉丝数'),
				'unsubscribe'  => Yii::t('app', '流失粉丝数'),
				'net_increase' => Yii::t('app', '净增粉丝数'),
				'data_time'    => Yii::t('app', '统计时间'),
				'is_month'     => Yii::t('app', '0:按天，1、按月'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getScene ()
		{
			return $this->hasOne(Scene::className(), ['id' => 'scene_id']);
		}

		//每日统计扫码数据

		/**
		 * 每日统计扫码数据
		 * $is_month 0:按天，1、按月
		 *
		 */
		public static function create ($is_month = 0)
		{
			if (empty($is_month)) {
				$start_date  = date('Y-m-d', strtotime('-1 day'));
				$end_date    = $start_date . ' 23:59:59';
				$create_time = strtotime($end_date) - 2592000;
				$timeStr     = date('Y-m-d H:i:s', $create_time);
				$sceneData   = Scene::find()->where(['or', ['>', 'create_time', $timeStr], ['scene_expire' => '']])->select('id')->all();
				$data_time   = $start_date;
			} else {
				$data_time   = date('Y-m', strtotime('-1 month '));
				$start_date  = $data_time . '-01';
				$end_date    = date('Y-m-t 23:59:59', strtotime('-1 month'));
				$create_time = strtotime($end_date) - 2592000;
				$timeStr     = date('Y-m-d H:i:s', $create_time);
				$sceneData   = Scene::find()->where(['or', ['>', 'create_time', $timeStr], ['scene_expire' => '']])->select('id')->all();
				$data_time   = date('Y-m', strtotime('-1 month '));
			}

			if (!empty($sceneData)) {
				foreach ($sceneData as $sv) {
					$statistic = static::findOne(['scene_id' => $sv['id'], 'data_time' => $data_time, 'is_month' => $is_month]);
					if (empty($statistic)) {
						$statistic = new SceneStatistic();
					}
					//扫码次数
					$scan_times = FansTimeLine::find()->where(['scene_id' => $sv['id'], 'event' => ['scan', 'subscribe']])->andWhere(['between', 'event_time', $start_date, $end_date])->count();
					//扫码人数
					$scan_num = FansTimeLine::find()->where(['scene_id' => $sv['id'], 'event' => ['scan', 'subscribe']])->andWhere(['between', 'event_time', $start_date, $end_date])->groupBy('fans_id')->count();
					//新增粉丝数
					$subscribe = FansTimeLine::find()->where(['scene_id' => $sv['id'], 'event' => 'subscribe'])->andWhere(['between', 'event_time', $start_date, $end_date])->groupBy('fans_id')->count();
					//流失粉丝数
					$unsubscribe = FansTimeLine::find()->where(['scene_id' => $sv['id'], 'event' => 'unsubscribe'])->andWhere(['between', 'event_time', $start_date, $end_date])->groupBy('fans_id')->count();
					//净增粉丝数
					$net_increase = $subscribe - $unsubscribe;
					//添加数据
					$statistic->scene_id     = $sv['id'];
					$statistic->scan_times   = $scan_times;
					$statistic->scan_num     = $scan_num;
					$statistic->subscribe    = $subscribe;
					$statistic->unsubscribe  = $unsubscribe;
					$statistic->net_increase = $net_increase;
					$statistic->data_time    = $data_time;
					$statistic->is_month     = $is_month;
					$statistic->save();
				}
			}
		}

	}
