<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%authority_sub_user_statistic}}".
	 *
	 * @property int            $id
	 * @property int            $way_id                渠道二维码ID
	 * @property int            $user_id               企业成员id
	 * @property int            $new_contact_cnt       新增客户数
	 * @property int            $increase_cnt          净增客户数
	 * @property int            $delete_cnt            员工删除的客户数
	 * @property int            $negative_feedback_cnt 删除/拉黑成员的客户数
	 * @property string         $data_time             统计时间
	 * @property int            $is_month              0:按天，1、按月,2按周
	 * @property int            $group_id              分组id
	 *
	 * @property WorkContactWay $way
	 */
	class AuthoritySubUserStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%authority_sub_user_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['way_id', 'user_id', 'new_contact_cnt', 'increase_cnt', 'delete_cnt', 'negative_feedback_cnt', 'is_month', 'group_id'], 'integer'],
				[['data_time'], 'string', 'max' => 16],
				[['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWay::className(), 'targetAttribute' => ['way_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                    => Yii::t('app', 'ID'),
				'way_id'                => Yii::t('app', '渠道二维码ID'),
				'user_id'               => Yii::t('app', '企业成员id'),
				'new_contact_cnt'       => Yii::t('app', '新增客户数'),
				'increase_cnt'          => Yii::t('app', '净增客户数'),
				'delete_cnt'            => Yii::t('app', '员工删除的客户数'),
				'negative_feedback_cnt' => Yii::t('app', '删除/拉黑成员的客户数'),
				'data_time'             => Yii::t('app', '统计时间'),
				'is_month'              => Yii::t('app', '0:按天，1、按月,2按周'),
				'group_id'              => Yii::t('app', '分组id'),
			];
		}
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWay ()
		{
			return $this->hasOne(WorkContactWay::className(), ['id' => 'way_id']);
		}
		//天历史数据
		public static function setDataHistoryDay($workContactWay=[],$i=1,$max=0,$time=true)
		{
			if(file_exists(Yii::$app->basePath."/static/time1.txt")){
				$i = file_get_contents(Yii::$app->basePath."/static/time1.txt");
				if(empty($i)){
					return;
				}
			}
			if($time){
				$tableNameLine  = WorkContactWayLine::tableName();
				$lineTime = Yii::$app->db->createCommand("select create_time from $tableNameLine WHERE create_time is not null order by create_time asc limit 1")->queryOne();
				$max = ceil((time()-strtotime($lineTime['create_time']))/86400);
				echo $max;
			}
			if($i>=$max){
				file_put_contents(Yii::$app->basePath."/static/time1.txt",NULL);
				return;
			}
			//按天
			$start_date = date('Y-m-d', strtotime("-$i day"));
			$end_date   = $start_date . ' 23:59:59';
			$data_time  = $start_date;
			if(empty($workContactWay)){
				$workContactWay = WorkContactWay::find()->asArray()->all();
			}
			$tableNameLine  = WorkContactWayLine::tableName();
			$tableName = self::tableName();
			if (!empty($workContactWay)) {
				foreach ($workContactWay as $way) {
					$newDayAll = Yii::$app->db->createCommand("select user_id from $tableNameLine  WHERE way_id = " . $way['id'] . " and (create_time between '$start_date' and '$end_date') GROUP BY user_id")->queryAll();
					if (empty($newDayAll)) {
						continue;
					}
					foreach ($newDayAll as $item) {
						$tmp = Yii::$app->db->createCommand("select id from $tableName  WHERE way_id = " . $way['id'] . " and user_id = ".$item["user_id"]." and data_time = '$data_time' and is_month = 0")->queryOne();
						if(!empty($tmp)){
							continue;
						}
						$newDayCount                    = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 1 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayDeleteCount              = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 2 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayDeleteByUserCount        = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 3 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayIncreaseCount            = ($newDayCount['num'] - $newDayDeleteCount['num'] < 0)?0:$newDayCount['num'] - $newDayDeleteCount['num'];
						Yii::$app->db->createCommand("INSERT INTO $tableName(`way_id`, `user_id`, `new_contact_cnt`, `increase_cnt`, `delete_cnt`, `negative_feedback_cnt`, `data_time`, `is_month`, `group_id`) VALUES (".$way['id'].", ".$item["user_id"].", ".$newDayCount['num'].", ".$newDayIncreaseCount.", ".$newDayDeleteByUserCount['num'].", ".$newDayDeleteCount['num'].", '".$data_time."', 0,".$way['way_group_id'].");")->execute();

					}
				}
			}
			$i = $i+1;
			file_put_contents(Yii::$app->basePath."/static/time1.txt",$i);
			self::setDataHistoryDay($workContactWay,$i,$max,false);
		}
		//周历史数据
		public static function setDataHistoryWeek($workContactWay=[],$time=604800,$i=1,$max=0,$a = true)
		{
			if(file_exists(Yii::$app->basePath."/static/time2.txt")){
				$i = file_get_contents(Yii::$app->basePath."/static/time2.txt");
				if(empty($i)){
					return;
				}
			}
			if($a){
				$tableNameLine  = WorkContactWayLine::tableName();
				$lineTime = Yii::$app->db->createCommand("select create_time from $tableNameLine WHERE create_time is not null order by create_time asc limit 1")->queryOne();
				$max = ceil((time()-strtotime($lineTime['create_time']))/604800);
			}
			if($i>=$max){
				file_put_contents(Yii::$app->basePath."/static/time2.txt",NULL);
				return;
			}
			$start_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y'))-$time);
			$end_date   = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y"))-$time);
			$data_time  = $start_date;
			if(empty($workContactWay)){
				$workContactWay = WorkContactWay::find()->asArray()->all();
			}
			$tableNameLine  = WorkContactWayLine::tableName();
			$tableName = self::tableName();
			if (!empty($workContactWay)) {
				foreach ($workContactWay as $way) {
					$newDayAll = Yii::$app->db->createCommand("select user_id from $tableNameLine  WHERE way_id = " . $way['id'] . " and (create_time between '$start_date' and '$end_date') GROUP BY user_id")->queryAll();
					if (empty($newDayAll)) {
						continue;
					}
					foreach ($newDayAll as $item) {
						$tmp = Yii::$app->db->createCommand("select id from $tableName  WHERE way_id = " . $way['id'] . " and user_id = ".$item["user_id"]." and data_time = '$data_time' and is_month = 2")->queryOne();
						if(!empty($tmp)){
							continue;
						}
						$newDayCount                    = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 1 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayDeleteCount              = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 2 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayDeleteByUserCount        = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 3 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayIncreaseCount            = ($newDayCount['num'] - $newDayDeleteCount['num'] < 0)?0:$newDayCount['num'] - $newDayDeleteCount['num'];
						Yii::$app->db->createCommand("INSERT INTO $tableName(`way_id`, `user_id`, `new_contact_cnt`, `increase_cnt`, `delete_cnt`, `negative_feedback_cnt`, `data_time`, `is_month`, `group_id`) VALUES (".$way['id'].", ".$item["user_id"].", ".$newDayCount['num'].", ".$newDayIncreaseCount.", ".$newDayDeleteByUserCount['num'].", ".$newDayDeleteCount['num'].", '".$data_time."', 2,".$way['way_group_id'].");")->execute();
					}
				}
			}
			$i = $i+1;
			file_put_contents(Yii::$app->basePath."/static/time2.txt",$i);
			self::setDataHistoryWeek($workContactWay,604800*$i ,$i,$max,false);
		}
		//月历史数据
		public static function setDataHistoryMonth($workContactWay=[],$i=1,$max=0,$time=true)
		{
			if(file_exists(Yii::$app->basePath."/static/time3.txt")){
				$i = file_get_contents(Yii::$app->basePath."/static/time3.txt");
				if(empty($i)){
					return;
				}
			}
			if($time){
				$tableNameLine  = WorkContactWayLine::tableName();
				$lineTime = Yii::$app->db->createCommand("select create_time from $tableNameLine WHERE create_time is not null order by create_time asc limit 1")->queryOne();
				$date1 = explode('-',$lineTime['create_time']);
				$date2 = explode('-',date("Y-m-d",time()));
				$max = abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
			}
			if($i>=$max){
				file_put_contents(Yii::$app->basePath."/static/time3.txt",NULL);
				return;
			}
			$data_time1 = date('Y-m', strtotime("-$i month"));
			$start_date = $data_time1 . '-01';
			$end_date   = date('Y-m-t 23:59:59', strtotime("-$i month"));
			$data_time  = $start_date;
			if(empty($workContactWay)){
				$workContactWay = WorkContactWay::find()->asArray()->all();
			}
			$tableName = self::tableName();
			$tableNameLine  = WorkContactWayLine::tableName();
			if (!empty($workContactWay)) {
				foreach ($workContactWay as $way) {
					$newDayAll = Yii::$app->db->createCommand("select user_id from $tableNameLine  WHERE way_id = " . $way['id'] . " and (create_time between '$start_date' and '$end_date') GROUP BY user_id")->queryAll();
					if (empty($newDayAll)) {
						continue;
					}
					foreach ($newDayAll as $item) {
						$tmp = Yii::$app->db->createCommand("select id from $tableName  WHERE way_id = " . $way['id'] . " and user_id = ".$item["user_id"]." and data_time = '$data_time' and is_month = 1")->queryOne();
						if(!empty($tmp)){
							continue;
						}
						$newDayCount                    = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 1 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayDeleteCount              = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 2 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayDeleteByUserCount        = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 3 and (create_time between '$start_date' and '$end_date')")->queryOne();
						$newDayIncreaseCount            = ($newDayCount['num'] - $newDayDeleteCount['num'] < 0)?0:$newDayCount['num'] - $newDayDeleteCount['num'];
						Yii::$app->db->createCommand("INSERT INTO $tableName(`way_id`, `user_id`, `new_contact_cnt`, `increase_cnt`, `delete_cnt`, `negative_feedback_cnt`, `data_time`, `is_month`, `group_id`) VALUES (".$way['id'].", ".$item["user_id"].", ".$newDayCount['num'].", ".$newDayIncreaseCount.", ".$newDayDeleteByUserCount['num'].", ".$newDayDeleteCount['num'].", '".$data_time."', 1,".$way['way_group_id'].");")->execute();
					}
				}
			}
			$i = $i+1;
			file_put_contents(Yii::$app->basePath."/static/time3.txt",$i);
			self::setDataHistoryMonth($workContactWay,$i,$max,false);
		}

		public static function setDataALL ($type = 0)
		{
			try {
				if ($type > 3) {
					return;
				}
				if ($type == 0) {
					//按天
					$start_date = date('Y-m-d', strtotime('-1 day'));
					$end_date   = $start_date . ' 23:59:59';
					$data_time  = $start_date;
				} elseif ($type == 2) {
					//按周
					$start_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y')));
					$end_date   = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y")));
					$data_time  = $start_date;
				} elseif ($type == 1) {
					//按月
					$data_time1 = date('Y-m', strtotime('-1 month '));
					$start_date = $data_time1 . '-01';
					$end_date   = date('Y-m-t 23:59:59', strtotime('-1 month'));
					$data_time  = $start_date;
				}
				$workContactWay = WorkContactWay::find()->asArray()->all();
				$tableNameLine  = WorkContactWayLine::tableName();
				$tableName = self::tableName();
				if (!empty($workContactWay)) {
					foreach ($workContactWay as $way) {
						$newDayAll = Yii::$app->db->createCommand("select user_id from $tableNameLine  WHERE way_id = " . $way['id'] . " and (create_time between '$start_date' and '$end_date') GROUP BY user_id")->queryAll();
						if (empty($newDayAll)) {
							continue;
						}
						foreach ($newDayAll as $item) {
							$newDayCount                    = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 1 and (create_time between '$start_date' and '$end_date')")->queryOne();
							$newDayDeleteCount              = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 2 and (create_time between '$start_date' and '$end_date')")->queryOne();
							$newDayDeleteByUserCount        = Yii::$app->db->createCommand("select count(*) as num from $tableNameLine  WHERE way_id = " . $way['id'] . " and user_id=" . $item["user_id"] . " and type = 3 and (create_time between '$start_date' and '$end_date')")->queryOne();
							$newDayIncreaseCount            = ($newDayCount['num'] - $newDayDeleteCount['num'] < 0)?0:$newDayCount['num'] - $newDayDeleteCount['num'];
							Yii::$app->db->createCommand("INSERT INTO $tableName(`way_id`, `user_id`, `new_contact_cnt`, `increase_cnt`, `delete_cnt`, `negative_feedback_cnt`, `data_time`, `is_month`, `group_id`) VALUES (".$way['id'].", ".$item["user_id"].", ".$newDayCount['num'].", ".$newDayIncreaseCount.", ".$newDayDeleteByUserCount['num'].", ".$newDayDeleteCount['num'].", '".$data_time."', $type,".$way['way_group_id'].");")->execute();
						}
					}
				}
			} catch (\Exception $e) {
				echo $e->getMessage();
			}
		}
	}
