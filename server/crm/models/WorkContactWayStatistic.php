<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;
	use yii\db\Expression;
	use yii\debug\panels\EventPanel;

	/**
	 * This is the model class for table "{{%work_contact_way_statistic}}".
	 *
	 * @property int            $id
	 * @property int            $way_id                渠道二维码ID
	 * @property int            $new_contact_cnt       新增客户数
	 * @property int            $negative_feedback_cnt 删除/拉黑成员的客户数
	 * @property int            $delete_cnt            员工删除的客户数
	 * @property int            $increase_cnt          净增客户数
	 * @property string         $data_time             统计时间
	 * @property int            $is_month              0:按天，1、按月
	 * @property int            $group_id              分组id
	 *
	 * @property WorkContactWay $way
	 */
	class WorkContactWayStatistic extends \yii\db\ActiveRecord
	{

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['way_id', 'new_contact_cnt', 'negative_feedback_cnt', 'is_month', 'delete_cnt', 'increase_cnt'], 'integer'],
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
				'id'                    => 'ID',
				'way_id'                => 'Way ID',
				'new_contact_cnt'       => 'New Contact Cnt',
				'negative_feedback_cnt' => 'Negative Feedback Cnt',
				'data_time'             => 'Data Time',
				'is_month'              => 'Is Month',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWay ()
		{
			return $this->hasOne(WorkContactWay::className(), ['id' => 'way_id']);
		}

		/**
		 * @param $type 0 按天 1 按月 2 按周
		 *
		 */
		public static function create ($type)
		{
			try {
				\Yii::error($type, 'type_WorkContactWayStatistic');
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
				\Yii::error($data_time, '$data_time');
				$workContactWay = WorkContactWay::find()->asArray()->all();
				if (!empty($workContactWay)) {
					foreach ($workContactWay as $way) {
						$wayLine = WorkContactWayStatistic::findOne(['data_time' => $data_time, 'is_month' => $type, 'way_id' => $way['id']]);
						if (empty($wayLine)) {
							$wayLine = new WorkContactWayStatistic();
						}

						$newDayCount             = WorkContactWayLine::find()->where(['way_id' => $way['id'], 'type' => 1])->andFilterWhere(['between', 'create_time', $start_date, $end_date])->count();
						$newDayDeleteCount       = WorkContactWayLine::find()->where(['way_id' => $way['id'], 'type' => 2])->andFilterWhere(['between', 'create_time', $start_date, $end_date])->count();
						$newDayDeleteByUserCount = WorkContactWayLine::find()->where(['way_id' => $way['id'], 'type' => 3])->andFilterWhere(['between', 'create_time', $start_date, $end_date])->count();
						$newDayIncreaseCount     = $newDayCount - $newDayDeleteCount;
						if ($newDayIncreaseCount <= 0) {
							$newDayIncreaseCount = 0;
						}

						$wayLine->negative_feedback_cnt = $newDayDeleteCount;
						$wayLine->new_contact_cnt       = $newDayCount;
						$wayLine->delete_cnt            = $newDayDeleteByUserCount;
						$wayLine->increase_cnt          = $newDayIncreaseCount;
						$wayLine->data_time             = $data_time;
						$wayLine->way_id                = $way['id'];
						$wayLine->is_month              = $type;
						$wayLine->group_id              = $way['way_group_id'];

						if (!$wayLine->save()) {
							\Yii::error(SUtils::modelError($wayLine), 'WorkContactWayStatistic_error');
						}

					}
				}

			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WorkContactWayStatistic');
			}

		}

		/**
		 * @param $corpId
		 *
		 * @return array
		 *
		 */
		public static function getLastData ($corpId,$sub_detail=[])
		{
			$result     = [];
			$start1     = date('Y-m-d', strtotime('-1 day'));
			$start2     = date('Y-m-d', strtotime('-2 day'));
			$contactWay = WorkContactWay::find()->where(['corp_id' => $corpId])->select('id');
			$contactWay = $contactWay->asArray()->all();
			if (!empty($contactWay)) {
				if(!empty($sub_detail) && is_array($sub_detail)){
					$line = WorkContactWayLine::find()->where(["in","user_id",$sub_detail])
						->andFilterWhere(['between', 'create_time', $start1, $start2])
						->groupBy("way_id")
						->asArray()->all();
					$contactIds = array_column($line,"way_id");
				}else{
					$contactIds    = array_column($contactWay, 'id');
				}
				$expression    = new Expression('sum(new_contact_cnt) new_contact_cnt,sum(negative_feedback_cnt) negative_feedback_cnt,sum(delete_cnt) delete_cnt,sum(increase_cnt) increase_cnt');
				$workStatistic = WorkContactWayStatistic::find()->where(['in', 'way_id', $contactIds])->andWhere(['is_month' => 0]);
				//昨日
				$yesterdayStatistic = $workStatistic->andWhere(['data_time' => $start1])->select($expression);
				$yesterdayStatistic = $yesterdayStatistic->asArray()->one();
				//前日
				$lastDayStatistic = WorkContactWayStatistic::find()->where(['in', 'way_id', $contactIds])->andWhere(['is_month' => 0])->andWhere(['data_time' => $start2])->select($expression);
				$lastDayStatistic = $lastDayStatistic->asArray()->one();
				//昨日新增客户数
				$new_contact_cnt = isset($yesterdayStatistic['new_contact_cnt']) ? $yesterdayStatistic['new_contact_cnt'] : 0;
				//前日新增客户数
				$last_new_contact_cnt = isset($lastDayStatistic['new_contact_cnt']) ? $lastDayStatistic['new_contact_cnt'] : 0;
				//昨日删除/拉黑成员的客户数
				$negative_feedback_cnt = isset($yesterdayStatistic['negative_feedback_cnt']) ? $yesterdayStatistic['negative_feedback_cnt'] : 0;
				//前日删除/拉黑成员的客户数
				$last_negative_feedback_cnt = isset($lastDayStatistic['negative_feedback_cnt']) ? $lastDayStatistic['negative_feedback_cnt'] : 0;
				//昨日员工删除的客户数
				$delete_cnt = isset($yesterdayStatistic['delete_cnt']) ? $yesterdayStatistic['delete_cnt'] : 0;
				//前日员工删除的客户数
				$last_delete_cnt = isset($lastDayStatistic['delete_cnt']) ? $lastDayStatistic['delete_cnt'] : 0;
				//昨日净增客户数
				$increase_cnt = isset($yesterdayStatistic['increase_cnt']) ? $yesterdayStatistic['increase_cnt'] : 0;
				//前日净增客户数
				$last_increase_cnt = isset($lastDayStatistic['increase_cnt']) ? $lastDayStatistic['increase_cnt'] : 0;

				$data          = [];
				$data['title'] = '昨日新增客户数';
				$data['des']   = '新增客户数，成员新添加的客户数量。';
				if ($new_contact_cnt > $last_new_contact_cnt) {
					$data['status'] = 1;
				} elseif ($new_contact_cnt < $last_new_contact_cnt) {
					$data['status'] = 2;
				} else {
					$data['status'] = 0;
				}
				if ($last_new_contact_cnt > 0) {
					$num = round(abs($new_contact_cnt - $last_new_contact_cnt) / $last_new_contact_cnt, 3);
				} else {
					$num = $new_contact_cnt;
				}
				$num           = sprintf("%.1f", $num * 100);
				$data['count'] = $new_contact_cnt;
				$data['per']   = $num . '%';
				$result[]      = $data;

				$data          = [];
				$data['title'] = '昨日被客户删除/拉黑人数';
				$data['des']   = '删除/拉黑成员的客户数，即将成员删除或加入黑名单的客户数。';
				if ($negative_feedback_cnt > $last_negative_feedback_cnt) {
					$data['status'] = 1;
				} elseif ($negative_feedback_cnt < $last_negative_feedback_cnt) {
					$data['status'] = 2;
				} else {
					$data['status'] = 0;
				}
				if ($last_negative_feedback_cnt > 0) {
					$num = round(abs($negative_feedback_cnt - $last_negative_feedback_cnt) / $last_negative_feedback_cnt, 3);
				} else {
					$num = $negative_feedback_cnt;
				}
				$num           = sprintf("%.1f", $num * 100);
				$data['count'] = $negative_feedback_cnt;
				$data['per']   = $num . '%';
				$result[]      = $data;

				$data          = [];
				$data['title'] = '昨日删除人数';
				$data['des']   = '员工删除的客户数。';
				if ($delete_cnt > $last_delete_cnt) {
					$data['status'] = 1;
				} elseif ($delete_cnt < $last_delete_cnt) {
					$data['status'] = 2;
				} else {
					$data['status'] = 0;
				}
				if ($last_delete_cnt > 0) {
					$num = round(abs($delete_cnt - $last_delete_cnt) / $last_delete_cnt, 3);
				} else {
					$num = $delete_cnt;
				}
				$num           = sprintf("%.1f", $num * 100);
				$data['count'] = $delete_cnt;
				$data['per']   = $num . '%';
				$result[]      = $data;

				$data          = [];
				$data['title'] = '昨日净增人数';
				$data['des']   = '新增客户数减去昨日被客户删除/拉黑人数。';
				if ($increase_cnt > $last_increase_cnt) {
					$data['status'] = 1;
				} elseif ($increase_cnt < $last_increase_cnt) {
					$data['status'] = 2;
				} else {
					$data['status'] = 0;
				}
				if ($last_increase_cnt > 0) {
					$num = round(abs($increase_cnt - $last_increase_cnt) / $last_increase_cnt, 3);
				} else {
					$num = $increase_cnt;
				}
				$num           = sprintf("%.1f", $num * 100);
				$data['count'] = $increase_cnt;
				$data['per']   = $num . '%';
				$result[]      = $data;

			} else {
				for ($i = 0; $i <= 3; $i++) {
					if ($i == 0) {
						$result[$i]['title'] = '昨日新增客户数';
						$result[$i]['des']   = '新增客户数，成员新添加的客户数量。';
					} elseif ($i == 1) {
						$result[$i]['title'] = '昨日被客户删除/拉黑人数';
						$result[$i]['des']   = '删除/拉黑成员的客户数，即将成员删除或加入黑名单的客户数。';
					} elseif ($i == 2) {
						$result[$i]['title'] = '昨日删除人数';
						$result[$i]['des']   = '员工删除的客户数。';
					} elseif ($i == 3) {
						$result[$i]['title'] = '昨日净增人数';
						$result[$i]['des']   = '新增客户数减去昨日被客户删除/拉黑人数。';
					}
					$result[$i]['status'] = 0;
					$result[$i]['count']  = "0";
					$result[$i]['per']    = '0.0%';
				}
			}

			return $result;
		}

		/**
		 * @param $corp_id
		 * @param $s_date
		 * @param $e_date
		 * @param $data_type
		 * @param $type
		 * @param $group_id
		 * @param $page
		 * @param $pageSize
		 * @param $is_export
		 * @param $sub_detail
		 *
		 * @return array
		 *
		 */
		public static function getTopData ($corp_id, $s_date, $e_date, $data_type, $type, $group_id, $page = 1, $pageSize = 10, $is_export = 0 ,$sub_detail)
		{
			$xData   = [];//X轴
			$newData = [];//Y轴数据
			$allData = [];//详细数据

			$expression = new Expression('sum(new_contact_cnt) new_contact_cnt,sum(negative_feedback_cnt) negative_feedback_cnt,sum(delete_cnt) delete_cnt,sum(increase_cnt) increase_cnt,c.title,c.is_del,c.way_group_id');
			if(is_array($sub_detail)){
				$statistic  = AuthoritySubUserStatistic::find()->alias('s');
				$statistic  = $statistic->leftJoin('{{%work_contact_way}} c', 'c.id=s.way_id');
				$statistic  = $statistic->andWhere(['in',"s.user_id",$sub_detail]);
			}else{
				$statistic  = static::find()->alias('s');
				$statistic  = $statistic->leftJoin('{{%work_contact_way}} c', 'c.id=s.way_id');
			}
			if($sub_detail === false){
				$info               = [];
				$info['xData']      = [];
				$info['seriesData'] = [];
				$info['data']       = [];
				$info['count']      = 0;
				return $info;
			}
			$statistic  = $statistic->andWhere(['c.corp_id' => $corp_id, 's.is_month' => 0])->andFilterWhere(['between', 's.data_time', $s_date, $e_date]);
			if (!empty($group_id)) {
				$statistic = $statistic->andWhere(['c.way_group_id' => $group_id]);
			}
			if ($type == 1) {
				$statistic = $statistic->select($expression)->groupBy('s.way_id');
			} elseif ($type == 2) {
				$statistic = $statistic->select($expression)->groupBy('s.group_id');
			}

			$count        = 0;
			$statisticNew = $statistic->asArray()->all();
			if (!empty($statisticNew)) {
				foreach ($statisticNew as $new) {
					switch ($data_type) {
						case 1:
							if ($new['new_contact_cnt'] > 0) {
								$count++;
							}
							break;
						case 2:
							if ($new['negative_feedback_cnt'] > 0) {
								$count++;
							}
							break;
						case 3:
							if ($new['delete_cnt'] > 0) {
								$count++;
							}
							break;
						case 4:
							if ($new['increase_cnt'] > 0) {
								$count++;
							}
							break;
					}
				}
			}
			$offset = ($page - 1) * $pageSize;
			switch ($data_type) {
				case 1:
					if ($is_export == 1) {
						$statistic = $statistic->orderBy(['new_contact_cnt' => SORT_DESC]);
					} else {
						$statistic = $statistic->orderBy(['new_contact_cnt' => SORT_DESC])->limit($pageSize)->offset($offset);
					}
					break;
				case 2:
					if ($is_export == 1) {
						$statistic = $statistic->orderBy(['negative_feedback_cnt' => SORT_DESC]);
					} else {
						$statistic = $statistic->orderBy(['negative_feedback_cnt' => SORT_DESC])->limit($pageSize)->offset($offset);
					}
					break;
				case 3:
					if ($is_export == 1) {
						$statistic = $statistic->orderBy(['delete_cnt' => SORT_DESC]);
					} else {
						$statistic = $statistic->orderBy(['delete_cnt' => SORT_DESC])->limit($pageSize)->offset($offset);
					}
					break;
				case 4:
					if ($is_export == 1) {
						$statistic = $statistic->orderBy(['increase_cnt' => SORT_DESC]);
					} else {
						$statistic = $statistic->orderBy(['increase_cnt' => SORT_DESC])->limit($pageSize)->offset($offset);
					}
					break;
			}
			$allGroup = [];
			$wayGroup = WorkContactWayGroup::find()->where(['corp_id' => $corp_id, 'status' => 1])->asArray()->all();
			if (!empty($wayGroup)) {
				foreach ($wayGroup as $group) {
					$allGroup[$group['id']] = $group['title'];
				}
			}
			//echo $statistic->createCommand()->getRawSql();
			$statistic = $statistic->asArray()->all();
			if (!empty($statistic)) {
				foreach ($statistic as $k => $v) {
					if ($v['is_del'] == WorkContactWay::WAY_IS_DEL) {
						$v['title'] = $v['title'] . '【已删除】';
					}
					switch ($data_type) {
						case 1:
							if ($v['new_contact_cnt'] > 0) {
								if (count($xData) >= 10) {
									break;
								}
								array_push($newData, $v['new_contact_cnt']);
								if ($type == 1) {
									array_push($xData, $v['title']);
								} elseif ($type == 2) {
									array_push($xData, $allGroup[$v['way_group_id']]);
								}
							}
							break;
						case 2:
							if ($v['negative_feedback_cnt'] > 0) {
								if (count($xData) >= 10) {
									break;
								}
								array_push($newData, $v['negative_feedback_cnt']);
								if ($type == 1) {
									array_push($xData, $v['title']);
								} elseif ($type == 2) {
									array_push($xData, $allGroup[$v['way_group_id']]);
								}
							}
							break;
						case 3:
							if ($v['delete_cnt'] > 0) {
								if (count($xData) >= 10) {
									break;
								}
								array_push($newData, $v['delete_cnt']);
								if ($type == 1) {
									array_push($xData, $v['title']);
								} elseif ($type == 2) {
									array_push($xData, $allGroup[$v['way_group_id']]);
								}
							}
							break;
						case 4:
							if ($v['increase_cnt'] > 0) {
								if (count($xData) >= 10) {
									break;
								}
								array_push($newData, $v['increase_cnt']);
								if ($type == 1) {
									array_push($xData, $v['title']);
								} elseif ($type == 2) {
									array_push($xData, $allGroup[$v['way_group_id']]);
								}
							}
							break;
					}
					switch ($data_type) {
						case 1:
							if ($v['new_contact_cnt'] > 0) {
								$allData[$k]['sort']       = $k + 1;
								$allData[$k]['name']       = $v['title'];
								$allData[$k]['all_num']    = $v['new_contact_cnt'];
								$allData[$k]['group_name'] = $allGroup[$v['way_group_id']];
							}
							break;
						case 2:
							if ($v['negative_feedback_cnt'] > 0) {
								$allData[$k]['sort']       = $k + 1;
								$allData[$k]['name']       = $v['title'];
								$allData[$k]['all_num']    = $v['negative_feedback_cnt'];
								$allData[$k]['group_name'] = $allGroup[$v['way_group_id']];
							}
							break;
						case 3:
							if ($v['delete_cnt'] > 0) {
								$allData[$k]['sort']       = $k + 1;
								$allData[$k]['name']       = $v['title'];
								$allData[$k]['all_num']    = $v['delete_cnt'];
								$allData[$k]['group_name'] = $allGroup[$v['way_group_id']];
							}
							break;
						case 4:
							if ($v['increase_cnt'] > 0) {
								$allData[$k]['sort']       = $k + 1;
								$allData[$k]['name']       = $v['title'];
								$allData[$k]['all_num']    = $v['increase_cnt'];
								$allData[$k]['group_name'] = $allGroup[$v['way_group_id']];
							}
							break;
					}
				}

				$info               = [];
				$info['xData']      = $xData;
				$info['seriesData'] = $newData;
				$info['data']       = $allData;
				$info['count']      = $count;

				return $info;
			}

		}

		/**
		 * @param $corp_id
		 * @param $s_date
		 * @param $e_date
		 * @param $s_week
		 * @param $type
		 * @param $group_id
		 * @param $count
		 * @param $sub_ids
		 *
		 * @return array
		 *
		 */
		public static function getIncreaseData ($corp_id, $s_date, $e_date, $s_week, $type, $group_id, $count,$sub_ids)
		{
			$xData                      = [];//X轴
			$new_contact_cnt_data       = [];//新增客户数
			$negative_feedback_cnt_data = [];//被客户删除
			$delete_cnt_data            = [];//员工删除客户人数
			$increase_cnt_data          = [];//净增长
			$new_contact_cnt            = 0; //新增客户
			$negative_feedback_cnt      = 0; //被客户删除
			$delete_cnt                 = 0; //删除人数
			$increase_cnt               = 0; //净增人数
			switch ($type) {
				case 1:
					//按天
					$data   = DateUtil::getDateFromRange($s_date, $e_date);
					$result = [];
					foreach ($data as $k => $v) {
						$increaseData                        = static::getIncrease($corp_id, $v, $group_id, $type,0,$sub_ids);
						$d_new_contact_cnt                   = isset($increaseData['new_contact_cnt']) && !empty($increaseData['new_contact_cnt']) ? $increaseData['new_contact_cnt'] : 0;
						$d_negative_feedback_cnt             = isset($increaseData['negative_feedback_cnt']) && !empty($increaseData['negative_feedback_cnt']) ? $increaseData['negative_feedback_cnt'] : 0;
						$d_delete_cnt                        = isset($increaseData['delete_cnt']) && !empty($increaseData['delete_cnt']) ? $increaseData['delete_cnt'] : 0;
						$d_increase_cnt                      = isset($increaseData['increase_cnt']) && !empty($increaseData['increase_cnt']) ? $increaseData['increase_cnt'] : 0;
						$result[$k]['new_contact_cnt']       = $d_new_contact_cnt;
						$result[$k]['negative_feedback_cnt'] = $d_negative_feedback_cnt;
						$result[$k]['delete_cnt']            = $d_delete_cnt;
						$result[$k]['increase_cnt']          = $d_increase_cnt;
						if (empty($count)) {
							$per = '0.0%';
						} else {
							$num = round(($increaseData['delete_cnt'] + $increaseData['negative_feedback_cnt']) / $count, 3);
							$num = sprintf("%.1f", $num * 100);
							$per = $num . '%';
						}
						$result[$k]['per']     = $per;
						$result[$k]['time']    = $v;
						$new_contact_cnt       += $d_new_contact_cnt;
						$negative_feedback_cnt += $d_negative_feedback_cnt;
						$delete_cnt            += $d_delete_cnt;
						$increase_cnt          += $d_increase_cnt;

						array_push($new_contact_cnt_data, intval($d_new_contact_cnt));
						array_push($negative_feedback_cnt_data, intval($d_negative_feedback_cnt));
						array_push($delete_cnt_data, intval($d_delete_cnt));
						array_push($increase_cnt_data, intval($d_increase_cnt));
					}
					$xData = $data;
					break;
				case 2:
					//按周
					$data    = DateUtil::getDateFromRange($s_date, $e_date);
					$data    = DateUtil::getWeekFromRange($data);
					$s_date1 = $data['s_date'];
					$e_date1 = $data['e_date'];
					$result  = [];
					foreach ($s_date1 as $k => $v) {
						foreach ($e_date1 as $kk => $vv) {
							if ($k == $kk) {
								if ($s_week == 53) {
									$s_week = 1;
								}
								$increaseData                        = static::getIncrease($corp_id, $v, $group_id, $type,0,$sub_ids);
								$d_new_contact_cnt                   = isset($increaseData['new_contact_cnt']) && !empty($increaseData['new_contact_cnt']) ? $increaseData['new_contact_cnt'] : 0;
								$d_negative_feedback_cnt             = isset($increaseData['negative_feedback_cnt']) && !empty($increaseData['negative_feedback_cnt']) ? $increaseData['negative_feedback_cnt'] : 0;
								$d_delete_cnt                        = isset($increaseData['delete_cnt']) && !empty($increaseData['delete_cnt']) ? $increaseData['delete_cnt'] : 0;
								$d_increase_cnt                      = isset($increaseData['increase_cnt']) && !empty($increaseData['increase_cnt']) ? $increaseData['increase_cnt'] : 0;
								$result[$k]['new_contact_cnt']       = $d_new_contact_cnt;
								$result[$k]['negative_feedback_cnt'] = $d_negative_feedback_cnt;
								$result[$k]['delete_cnt']            = $d_delete_cnt;
								$result[$k]['increase_cnt']          = $d_increase_cnt;
								if (empty($count)) {
									$per = '0.0%';
								} else {
									$num = round(($increaseData['delete_cnt'] + $increaseData['negative_feedback_cnt']) / $count, 3);
									$num = sprintf("%.1f", $num * 100);
									$per = $num . '%';
								}
								$result[$k]['per']     = $per;
								$result[$k]['time']    = $v . '~' . $vv . '(' . $s_week . '周)';
								$new_contact_cnt       += $d_new_contact_cnt;
								$negative_feedback_cnt += $d_negative_feedback_cnt;
								$delete_cnt            += $d_delete_cnt;
								$increase_cnt          += $d_increase_cnt;
								array_push($new_contact_cnt_data, intval($d_new_contact_cnt));
								array_push($negative_feedback_cnt_data, intval($d_negative_feedback_cnt));
								array_push($delete_cnt_data, intval($d_delete_cnt));
								array_push($increase_cnt_data, intval($d_increase_cnt));
								array_push($xData, $result[$k]['time']);
								$s_week++;
							}
						}
					}
					break;
				case 3:
					$date   = DateUtil::getLastMonth();
					$result = [];
					foreach ($date as $k => $v) {
						$date_time                           = explode('/', $v['time']);
						$date_time                           = $date_time[0] . '-' . $date_time[1] . '-' . '01';
						$increaseData                        = static::getIncrease($corp_id, $date_time, $group_id, $type, $k,$sub_ids);
						$d_new_contact_cnt                   = isset($increaseData['new_contact_cnt']) && !empty($increaseData['new_contact_cnt']) ? $increaseData['new_contact_cnt'] : 0;
						$d_negative_feedback_cnt             = isset($increaseData['negative_feedback_cnt']) && !empty($increaseData['negative_feedback_cnt']) ? $increaseData['negative_feedback_cnt'] : 0;
						$d_delete_cnt                        = isset($increaseData['delete_cnt']) && !empty($increaseData['delete_cnt']) ? $increaseData['delete_cnt'] : 0;
						$d_increase_cnt                      = isset($increaseData['increase_cnt']) && !empty($increaseData['increase_cnt']) ? $increaseData['increase_cnt'] : 0;
						$result[$k]['new_contact_cnt']       = $d_new_contact_cnt;
						$result[$k]['negative_feedback_cnt'] = $d_negative_feedback_cnt;
						$result[$k]['delete_cnt']            = $d_delete_cnt;
						$result[$k]['increase_cnt']          = $d_increase_cnt;
						if (empty($count)) {
							$per = '0.0%';
						} else {
							$num = round(($increaseData['delete_cnt'] + $increaseData['negative_feedback_cnt']) / $count, 3);
							$num = sprintf("%.1f", $num * 100);
							$per = $num . '%';
						}
						$result[$k]['per']     = $per;
						$result[$k]['time']    = $v['time'];
						$new_contact_cnt       += $d_new_contact_cnt;
						$negative_feedback_cnt += $d_negative_feedback_cnt;
						$delete_cnt            += $d_delete_cnt;
						$increase_cnt          += $d_increase_cnt;
						array_push($new_contact_cnt_data, intval($d_new_contact_cnt));
						array_push($negative_feedback_cnt_data, intval($d_negative_feedback_cnt));
						array_push($delete_cnt_data, intval($d_delete_cnt));
						array_push($increase_cnt_data, intval($d_increase_cnt));
						array_push($xData, $v['time']);
					}

					break;
			}
			$info                          = [];
			$info['new_contact_cnt']       = $new_contact_cnt;
			$info['negative_feedback_cnt'] = $negative_feedback_cnt;
			$info['delete_cnt']            = $delete_cnt;
			$info['increase_cnt']          = $increase_cnt;
			$info['data']                  = $result;
			$info['xData']                 = $xData;
			$seriesData                    = [
				[
					'name'   => '新增客户数',
					'type'   => 'line',
					'smooth' => true,
					'data'   => $new_contact_cnt_data,
				],
				[
					'name'   => '被客户删除/拉黑人数',
					'type'   => 'line',
					'smooth' => true,
					'data'   => $negative_feedback_cnt_data,
				],
				[
					'name'   => '员工删除客户人数',
					'type'   => 'line',
					'smooth' => true,
					'data'   => $delete_cnt_data,
				],
				[
					'name'   => '净增长',
					'type'   => 'line',
					'smooth' => true,
					'data'   => $increase_cnt_data,
				],
			];
			$info['seriesData']            = $seriesData;

			return $info;
		}

		/**
		 * @param     $corp_id
		 * @param     $date
		 * @param int $group_id
		 * @param int $type
		 * @param int $k
		 * @param int $sub_ids
		 *
		 * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord|null
		 *
		 */
		public static function getIncrease ($corp_id, $date, $group_id = 0, $type, $k = 0,$user_ids)
		{
			if ($type == 1) {
				$is_month = 0;
			} elseif ($type == 2) {
				$is_month = 2;
			} elseif ($type == 3) {
				$is_month = 1;
			}
			$expression = new Expression('way_id,sum(new_contact_cnt) new_contact_cnt,sum(negative_feedback_cnt) negative_feedback_cnt,sum(delete_cnt) delete_cnt,sum(increase_cnt) increase_cnt,c.title,c.way_group_id');
			if(is_array($user_ids)){
				$statistic  = AuthoritySubUserStatistic::find()->alias('s')
				->andWhere(["in","s.user_id",$user_ids]);
			}else{
				$statistic  = static::find()->alias('s');
			}
			$statistic  = $statistic->leftJoin('{{%work_contact_way}} c', 'c.id=s.way_id');
			$now       = date('Y-m-d', time());
			if ($k == 11) {
				$statistic = $statistic->andWhere(['c.corp_id' => $corp_id, 's.is_month' => 0])->andFilterWhere(['between', 's.data_time', $date, $now]);
			} else {
				$statistic = $statistic->andWhere(['c.corp_id' => $corp_id, 's.data_time' => $date, 's.is_month' => $is_month]);
			}
			if (!empty($group_id)) {
				$statistic = $statistic->andWhere(['c.way_group_id' => $group_id]);
			}
			$statistic = $statistic->select($expression)->asArray()->one();
//			if(is_array($user_ids) && $group_id ==0){
//				$startDate = $date;
//				if ($type == 1) {
//					$endDate = $now;
//				} elseif ($type == 2) {
//					$endDate= date("Y-m-d",strtotime($date)+604800);
//				} elseif ($type == 3) {
//					$endDate= date("Y-m-d",strtotime("next month", strtotime($date)));
//				}
//				$line = WorkContactWayLine::find()->from(
//					WorkContactWayLine::find()
//						->where(["in", "user_id", $user_ids])
//						->andWhere(['between', 'create_time', $startDate, $endDate])
//						->select(new Expression('count(id) as num, type'))
//						->groupBy(['type', 'external_userid'])
//				)->alias("s")
//					->select(new Expression('SUM(num) as num ,type'))
//					->groupBy(['type'])
//					->asArray()
//					->all();
//				$statistic['increase_cnt']          = 0;
//				$statistic['new_contact_cnt']       = 0;
//				$statistic['negative_feedback_cnt'] = 0;
//				$statistic['delete_cnt']            = 0;
//				if(!empty($line)){
//					$count = 0;
//					foreach ($line as $item){
//						switch ((int)$item["type"]){
//							case 1:$statistic['new_contact_cnt'] = $item['num'];break;
//							case 2:$statistic['negative_feedback_cnt'] = $item['num'];break;
//							case 3:$statistic['delete_cnt'] = $item['num'];break;
//						}
//						$count +=$item['num'];
//					}
//					$statistic['increase_cnt'] = $count-$statistic['delete_cnt']-$statistic['negative_feedback_cnt'];
//				}
//			}
			return $statistic;
		}

		/**
		 * @param $corp_id
		 * @param $s_date
		 * @param $e_date
		 * @param $group_id
		 *
		 * @return array
		 *
		 */
		public static function getAttributeData ($corp_id, $s_date, $e_date, $group_id,$user_ids=true)
		{
			$legData1      = ['男', '女', '未知'];//按性别
			$legData2      = [];//按活码名称
			$pieData1      = []; //按性别
			$pieData2      = [];//按活码名称
			$seriesData1   = [];  //按性别
			$seriesData2   = [];  //按活码名称
			$sourceDetail  = []; //导出的活码数据
			$increaseCount = 0;//新增客户数

			$line = static::getAttrData($corp_id, $group_id, $s_date, $e_date,"",[],999,$user_ids);

			$name      = [];
			$codeValue = [];
			$flag      = 0;
			if (!empty($line)) {
				foreach ($line as $li) {
					if ($li['cc'] > 0) {
						$flag = 1;
					}
				}
				$i          = 0;
				$otherCount = 0;
				foreach ($line as $li) {
					$contactWay = WorkContactWay::findOne(['title' => $li['title'], 'corp_id' => $corp_id]);
					if (!empty($contactWay)) {
						if ($contactWay->is_del == WorkContactWay::WAY_IS_DEL) {
							$li['title'] = $li['title'] . '【已删除】';
						}
					}
					if ($flag == 1) {
						if ($i == 7) {
							$li['title'] = '其他';
							$otherCount  += $li['cc'];
						}
						if ($i < 7) {
							array_push($codeValue, $li['cc']);
							array_push($name, $li['title']);
						} else {
							array_push($name, '其他');
						}
						$i++;
						$increaseCount += $li['cc'];
					}
				}
				$legData2 = array_unique($name);
				if (count($legData2) == 8) {
					array_push($codeValue, $otherCount);
				}
			}

			//当前客户总数 未删除的
			$followUser = WorkExternalContactFollowUser::find()->alias('wf');
			$followUser = $followUser->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
//			if(is_array($user_ids)){
//				$followUser = $followUser->andWhere(["in","wf.user_id",$user_ids]);
//			}
			$followUser = $followUser->andWhere(['we.corp_id' => $corp_id, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->groupBy('we.id');
			$userCount  = $followUser->count();

			$data  = DateUtil::getDateFromRange($s_date, $e_date);
			$xData = $data;
			if (!empty($legData2)) {
				foreach ($legData2 as $k => $v) {
					$contactWay = WorkContactWay::findOne(['title' => $v, 'corp_id' => $corp_id]);
					if (!empty($contactWay)) {
						if ($contactWay->is_del == WorkContactWay::WAY_IS_DEL) {
							$v = $v . '【已删除】';
						}
					}
					foreach ($codeValue as $kk => $val) {
						if ($k == $kk) {
							$pieData2[$k]['value']     = $val;
							$pieData2[$k]['name']      = $v;
							$sourceDetail[$k]['name']  = $v;
							$sourceDetail[$k]['count'] = $val;
							$per                       = static::getPer($val, $userCount);
							$sourceDetail[$k]['per']   = $per;
						}
					}
				}
			}
			$dataRange = DateUtil::getDateFromRange($s_date, $e_date);
			if ($flag == 1) {
				$seData      = static::getSeriesData($dataRange, $corp_id, $group_id, $legData2,$user_ids);
				$seriesData2 = static::getSexData($seData, $legData2);
			}
			$sexData     = static::getSeriesSexData($dataRange, $corp_id, $group_id, $legData1,$user_ids);
			$seriesData1 = static::getSexData($sexData, $legData1);
			if (!empty($legData1) && !empty($sexData)) {
				foreach ($legData1 as $k => $v) {
					$contactWay = WorkContactWay::findOne(['title' => $v, 'corp_id' => $corp_id]);
					if (!empty($contactWay)) {
						if ($contactWay->is_del == WorkContactWay::WAY_IS_DEL) {
							$v = $v . '【已删除】';
						}
					}
					$count = 0;
					foreach ($sexData as $kk => $dt) {
						if ($k == $kk) {
							foreach ($dt['data'] as $val) {
								$count += $val;
							}
						}
					}
					$pieData1[$k]['value'] = $count;
					$pieData1[$k]['name']  = $v;
				}
			}
			Yii::error($increaseCount,'$increaseCount');
			Yii::error($userCount,'$userCount');
			$per = static::getPer($increaseCount, $userCount);

			return [
				'increaseCount' => $increaseCount,
				'per'           => $per,
				'seriesData1'   => $seriesData1,
				'seriesData2'   => $seriesData2,
				'legData1'      => $legData1,
				'legData2'      => $legData2,
				'pieData1'      => $pieData1,
				'pieData2'      => $pieData2,
				'xData'         => $xData,
				'sourceDetail'  => $sourceDetail,
			];

		}

		/**
		 * @param $increaseCount
		 * @param $userCount
		 *
		 * @return string
		 *
		 */
		public static function getPer ($increaseCount, $userCount)
		{
			if (empty($increaseCount) || empty($userCount)) {
				$per = '0.0%';
			} else {
				$num = round($increaseCount / $userCount, 3);
				$num = sprintf("%.1f", $num * 100);
				$per = $num . '%';
			}

			return $per;
		}

		/**
		 * @param        $corp_id
		 * @param        $group_id
		 * @param        $s_date
		 * @param        $e_date
		 * @param string $name
		 * @param array  $legData2
		 * @param int    $sex
		 *
		 * @return \yii\db\ActiveQuery
		 *
		 */
		public static function getAttrData ($corp_id, $group_id, $s_date, $e_date, $name = '', $legData2 = [], $sex = 999,$user_ids=true)
		{
			$expression = new Expression('count(l.`id`) cc,c.title');
			$line       = WorkContactWayLine::find()->alias('l');
			$line       = $line->leftJoin('{{%work_contact_way}} c', 'c.id=l.way_id');
			if(is_array($user_ids)){
				$line       = $line->andWhere(["in",'l.user_id',$user_ids]);
			}
			$line       = $line->andWhere(['c.corp_id' => $corp_id, 'l.type' => 1])->andFilterWhere(['between', 'l.create_time', $s_date, $e_date . ' 23:59:59']);
			if (!empty($group_id)) {
				$line = $line->andWhere(['c.way_group_id' => $group_id]);
			}
			if (!empty($name)) {
				if ($name == '其他') {
					array_pop($legData2);
					$line = $line->andWhere(['not', ['c.title' => $legData2]]);
				} else {
					$line = $line->andWhere(['c.title' => $name]);
				}
			}
			if ($sex != 999) {
				$line = $line->andWhere(['l.gender' => $sex]);
				$line = $line->select($expression)->groupBy('l.gender')->orderBy(['cc' => SORT_DESC]);
			} else {
				$line = $line->select($expression)->groupBy('l.way_id')->orderBy(['cc' => SORT_DESC]);
			}

			$line = $line->asArray()->all();

			return $line;
		}

		/**
		 * @param $dataRange
		 * @param $corp_id
		 * @param $group_id
		 * @param $legData2
		 *
		 * @return array
		 *
		 */
		public static function getSeriesData ($dataRange, $corp_id, $group_id, $legData2,$user_ids=true)
		{
			$result = [];
			if (!empty($legData2)) {
				foreach ($legData2 as $kk => $name) {
					$data       = [];
					$otherCount = 0;
					foreach ($dataRange as $k => $v) {
						$dayData = static::getAttrData($corp_id, $group_id, $v, $v, $name, $legData2,999,$user_ids);
						if (!empty($dayData)) {
							foreach ($dayData as $day) {
								if ($name == '其他') {
									$otherCount += $day['cc'];
								} else {
									array_push($data, $day['cc']);
								}
							}
							if ($name == '其他') {
								array_push($data, $otherCount);
							}
						} else {
							array_push($data, 0);
						}
					}
					$result[$kk]['data'] = $data;
				}
			}

			return $result;
		}

		/**x
		 *
		 * @param $dataRange
		 * @param $corp_id
		 * @param $group_id
		 * @param $legData1
		 *
		 * @return array
		 *
		 */
		public static function getSeriesSexData ($dataRange, $corp_id, $group_id, $legData1,$user_ids=true)
		{
			$result = [];
			if (!empty($legData1)) {
				foreach ($legData1 as $kk => $name) {
					$data = [];
					if ($name == '男') {
						$sex = 1;
					} elseif ($name == '女') {
						$sex = 2;
					} else {
						$sex = 0;
					}
					foreach ($dataRange as $k => $v) {
						$dayData = static::getAttrData($corp_id, $group_id, $v, $v, '', $legData1, $sex,$user_ids);
						if (!empty($dayData)) {
							foreach ($dayData as $day) {
								array_push($data, $day['cc']);
							}
						} else {
							array_push($data, 0);
						}
					}
					$result[$kk]['data'] = $data;
				}
			}

			return $result;
		}

		/**
		 * @param $seData
		 * @param $legData1
		 *
		 * @return array
		 *
		 */
		public static function getSexData ($seData, $legData1)
		{
			$seriesData1 = [];
			if (!empty($seData) && !empty($legData1)) {
				foreach ($legData1 as $k => $dt) {
					foreach ($seData as $kk => $da) {
						if ($k == $kk) {
							$seriesData1[$k]['name']   = $dt;
							$seriesData1[$k]['type']   = 'line';
							$seriesData1[$k]['smooth'] = true;
							$seriesData1[$k]['data']   = $da['data'];
						}
					}
				}
			}

			return $seriesData1;
		}

	}
