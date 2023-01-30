<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%authority_sub_user_detail}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id      企业微信id
	 * @property int      $sub_id       子账户
	 * @property string   $department   部门
	 * @property string   $user_key     可见员工，默认是单人
	 * @property int      $type_all     1,全部；2、仅自己；3部门、4、指定成员
	 * @property int      $create_time
	 * @property string   $checked_list 选中人
	 *
	 * @property WorkCorp $corp
	 * @property SubUser  $sub
	 */
	class AuthoritySubUserDetail extends \yii\db\ActiveRecord
	{
		const TYPE_ALL = 1;

		const TYPE_ONE = 2;

		const TYPE_BU = 3;

		const TYPE_USER = 4;

		const FIND_USER_ALL = 99999999;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%authority_sub_user_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'sub_id', 'type_all', 'create_time'], 'integer'],
				[['checked_list'], 'string'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubUser::className(), 'targetAttribute' => ['sub_id' => 'sub_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'corp_id'      => Yii::t('app', '企业微信id'),
				'sub_id'       => Yii::t('app', '子账户'),
				'department'   => Yii::t('app', '部门'),
				'user_key'     => Yii::t('app', '可见员工，默认是单人'),
				'type_all'     => Yii::t('app', '1,全部；2、仅自己；3部门、4、指定成员'),
				'create_time'  => Yii::t('app', 'Create Time'),
				'checked_list' => Yii::t('app', '选中人'),
			];
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
		public function getSub ()
		{
			return $this->hasOne(SubUser::className(), ['sub_id' => 'sub_id']);
		}

		/**
		 * @param $sub_id
		 * @param $data
		 * 插入可查看员工部门权限
		 *
		 * @throws InvalidDataException
		 */
		public static function insertData ($sub_id, $data)
		{
			if (empty($sub_id) || empty($data)) {
				return;
			}
			foreach ($data as $item) {
				if (empty($item['corp_id'])) {
					return;
				}
				$detail = self::find()->where(['sub_id' => $sub_id, "corp_id" => $item['corp_id']])->one();
				if (empty($detail)) {
					$detail              = new self();
					$detail->create_time = time();
					$detail->sub_id      = $sub_id;
					$detail->corp_id     = $item['corp_id'];
				}
				if (!empty($item['department_id'])) {
					$detail->type_all = self::TYPE_BU;
				} elseif (!empty($item['user_key']) && empty($item['department_id'])) {
					$detail->type_all = self::TYPE_USER;
				}
				if (in_array(self::TYPE_ALL, $item['type'])) {
					$detail->type_all = self::TYPE_ALL;
				}
				if (empty($item['department_id']) && empty($item['user_key'])) {
					if (in_array(self::TYPE_ONE, $item['type']) && !in_array(self::TYPE_ALL, $item['type'])) {
						$detail->type_all    = self::TYPE_ONE;
						$item['checkedList'] = $item['department_id'] = $item['user_key'] = [];
					}
				}
				if (empty($item['type']) && empty($item['department_id']) && empty($item['user_key'])) {
					$detail->type_all    = self::TYPE_ONE;
					$item['checkedList'] = $item['department_id'] = $item['user_key'] = [];
				}
				$detail->user_key     = empty($item['user_key']) ? NULL : json_encode($item['user_key']);
				$detail->department   = empty($item['department_id']) ? NULL : json_encode($item['department_id']);
				$detail->checked_list = json_encode($item['checkedList'], JSON_UNESCAPED_UNICODE);
				if (!$detail->validate() || !$detail->save()) {
					throw new InvalidDataException(SUtils::modelError($detail));
				}
			}
		}

		public static function checkSubUser ($sub_id, $corp_id)
		{
			return self::find()->where(['sub_id' => $sub_id, "corp_id" => $corp_id])->asArray()->one();
		}

		/**
		 * @param $sub_id
		 * @param $corp_id
		 *获取部门 + 指定人员 所拥有范围人员
		 *
		 * @return array|bool
		 */
		public static function getDepartmentUserLists ($sub_id, $corp_id)
		{
			$row = self::find()->where(['sub_id' => $sub_id, "corp_id" => $corp_id])->asArray()->one();
			if (empty($row)) {
				$sub_user_id = WorkUser::find()->alias("s")
					->leftJoin("{{%sub_user}} as w", "s.mobile = w.account")
					->where(["w.sub_id" => $sub_id, "s.corp_id" => $corp_id, "w.type" => 0, "s.is_del" => 0])
					->select("s.id")->asArray()->all();
				if (!empty($sub_user_id)) {
					return array_column($sub_user_id, "id");
				} else {
					return false;
				}
			}
			if ($row['type_all'] == self::TYPE_ALL) {
				return true;
			}
			$user_key   = [];
			$department = [];
			if (!empty($row['department'])) {
				$departmentUser    = [];
				$row['department'] = json_decode($row['department'], 1);
				$tmp               = WorkDepartment::getDepartmentChildren($row['department'], $row['corp_id']);
				$departmentUser    = array_merge($departmentUser, $tmp);
				foreach ($departmentUser as $value) {
					$workUser    = WorkUser::find()->where("FIND_IN_SET(" . $value . ",department)")
						->andWhere(["corp_id" => $corp_id, 'is_del' => 0])
						->andWhere(["!=", "status", 4])
						->select("id")->asArray()->all();
					$workUserIds = array_column($workUser, "id");
					$department  = array_merge($department, $workUserIds);
				}
			}
			if (!empty($row['user_key'])) {
				$user_key = json_decode($row['user_key']);
			}
			$sub_user_id = WorkUser::find()->alias("s")
				->leftJoin("{{%sub_user}} as w", "s.mobile = w.account")
				->where(["w.sub_id" => $sub_id, "s.corp_id" => $corp_id, "w.type" => 0, "s.is_del" => 0])
				->select("s.id")->asArray()->all();
			if (!empty($sub_user_id)) {
				$sub_user_id = array_column($sub_user_id, "id");
				$tmp         = array_unique(array_merge($department, $user_key, $sub_user_id));
			} else {
				$tmp = array_unique(array_merge($department, $user_key));
			}
			if (empty($tmp)) {
				return false;
			}

			return $tmp;
		}

		/**
		 * @param $user_ids
		 *
		 * @return array
		 */
		public static function getUserUserId ($user_ids)
		{
			$userId = WorkUser::find()->where(["in", 'id', $user_ids])->asArray()->select("userid")->all();

			return array_column($userId, 'userid');
		}

		/**
		 * @param $user_ids
		 * @param $corp_id
		 *根据用户ids 获取所拥有的群列表ids
		 *
		 * @return array
		 */
		public static function getUserChatLists ($user_ids, $corp_id)
		{
			$chatList = WorkChat::find()->where(["in", 'owner_id', $user_ids])->andWhere(['corp_id' => $corp_id, 'group_chat' => 0])->asArray()->select("id")->all();

			return array_column($chatList, 'id');
		}

		/**
		 * @param $user_ids
		 * @param $corp_id
		 *根据用户ids 获取所拥有的群列表ids
		 *
		 * @return array
		 */
		public static function getUserInChatLists ($user_ids, $corp_id)
		{
			$chatList = WorkChatInfo::find()->where(["in", 'user_id', $user_ids])->asArray()->select("chat_id")->all();

			return array_column($chatList, 'chat_id');
		}

		/**
		 * @param $sub_id
		 * @param $uid
		 */
		public static function getAuthSubDetail ($sub_id, $uid)
		{
			$workCorpData = WorkCorp::find()->alias('wc');
			$workCorpData = $workCorpData->leftJoin('{{%user_corp_relation}} uc', '`wc`.`id` = `uc`.`corp_id`');
//			if (!empty($sub_id)) {
//				$sub_auth = SubUserAuthority::find()->andWhere(['sub_user_id' => $sub_id, 'type' => 2])->andWhere(['<>', 'authority_ids', ''])->asArray()->all();
//				if (!empty($sub_auth)) {
//					$wx_account_id = array_column($sub_auth, 'wx_id');
//					$workCorpData  = $workCorpData->andWhere(['in', 'wc.id', $wx_account_id]);
//				}
//			}
			$workCorpData = $workCorpData->andWhere(['uc.uid' => $uid])->select("wc.*")->orderBy(['wc.create_time' => SORT_ASC])->asArray()->all();
			$sub_auth     = [];
			foreach ($workCorpData as $datum) {
				/** @var AuthoritySubUserDetail $detail * */
				$detail = AuthoritySubUserDetail::find()->where(["sub_id" => $sub_id, "corp_id" => $datum["id"]])->one();
				$temp   = [
					"user_key"      => [],
					"department_id" => [],
					"checkedList"   => [],
					"chooseNum"     => 0,
					"type"          => [2],
					"corp_id"       => $datum["id"],
					"sub_id"        => $sub_id,
				];
				if (!empty($detail)) {
					if ($detail->type_all != 1) {
						$temp['department_id'] = empty($detail->department) ? [] : json_decode($detail->department, true);
						$temp['user_key']      = empty($detail->user_key) ? [] : json_decode($detail->user_key, true);
						$temp['checkedList']   = json_decode($detail->checked_list, true);
						$temp['chooseNum']     = count($temp['checkedList']);
						$Temp                  = true;
						if (!empty($temp['checkedList'])) {
							foreach ($temp['checkedList'] as &$value) {
								if (strpos($value["id"], 'd') !== false) {
									$Temp = false;
								}
								if (isset($value["name"])) {
									$value["title"]       = $value["name"];
									$value["scopedSlots"] = ["title" => "custom"];
									$value["key"]         = $value["user_key"];
								}
							}
						}
						if (!empty($temp['department_id']) && $Temp) {
							foreach ($temp['department_id'] as $str) {
								$D = WorkDepartment::findOne(["corp_id" => $datum["id"], "department_id" => $str]);
								if (!empty($D)) {
									$TempPart                  = [];
									$TempPart["scopedSlots"]   = ["title" => "title"];
									$TempPart["title"]         = $D->name;
									$TempPart["department_id"] = $D->department_id;
									$TempPart["id"]            = "d-" . $D->department_id;
									array_push($temp['checkedList'], $TempPart);
								}
							}
						}

					}
					if ($detail->type_all == 1) {
						$temp['type'] = [$detail->type_all];
					} else {
						$temp['type'] = [];
						if (empty($temp['department_id']) && empty($temp['user_key'])) {
							array_push($temp['type'], 2);
						} else if (!empty($temp['department_id']) && !empty($temp['user_key'])) {
							array_push($temp['type'], 2, 3, 4);
						} else if (!empty($temp['department_id']) && empty($temp['user_key'])) {
							array_push($temp['type'], 2, 3);
						} else if (empty($temp['department_id']) && !empty($temp['user_key'])) {
							array_push($temp['type'], 2, 4);
						}
						$temp['type'] = array_unique($temp['type']);
					}
				}
				array_push($sub_auth, $temp);
			}

			return $sub_auth;
		}

		/**
		 * @param $user_ids
		 *获取管理的子账户下的uid
		 *
		 * @return array
		 */
		public static function getUserSubId ($user_ids)
		{
			$row = WorkUser::find()->alias("wu")->leftJoin("{{%user}} as u", "wu.mobile = u.account")
				->andWhere("wu.mobile is not null")->select("u.uid")->andWhere(["in", "wu.id", $user_ids])->asArray()->all();

			return array_column($row, "uid");
		}

		public static function createHistory ()
		{
			$sql       = "SELECT
						        wu.corp_id,
						        sb.sub_id,
						        wu.department,
						        wu.is_leader_in_dept,
						        sb.uid,
						        sb.account,
						        wu.userid,
						        wu.NAME,
						        wu.mobile 
						FROM
						        {{%sub_user}} AS sb
						        LEFT JOIN {{%user}} AS u ON u.uid = sb.uid
						        LEFT JOIN {{%user_corp_relation}} AS ucr ON ucr.uid = u.uid
						        LEFT JOIN {{%work_user}} AS wu ON wu.corp_id = ucr.corp_id 
						        AND wu.mobile = sb.account 
						WHERE
						        sb.STATUS = 1 
						        AND sb.type = 0 
						GROUP BY
						        sub_id";
			$tableName = self::tableName();
			$data      = Yii::$app->db->createCommand($sql)->queryAll();
			foreach ($data as $item) {
				if (empty($item["corp_id"])) {
					continue;
				}
				$res = Yii::$app->db->createCommand("select sub_id from $tableName where sub_id = " . $item["sub_id"] . " and corp_id = " . $item["corp_id"] . " ")->queryOne();
				if (!empty($res)) {
					continue;
				}
				$type       = 2;
				$department = [];
				if (!empty($item['is_leader_in_dept'])) {
					$leaders     = explode(',', $item['is_leader_in_dept']);
					$departments = explode(',', $item['department']);
					foreach ($leaders as $key => $leader) {
						if ($leader == 1) {
							array_push($department, (int) $departments[$key]);
						}
					}
					if (count($department) > 0) {
						$type = 3;
					}
					if (empty($department)) {
						$department = NULL;
					}
				}
				Yii::$app->db->createCommand("insert into {$tableName} (`corp_id`,`sub_id`,`department`,`type_all`,`create_time`, `checked_list`) value (" . $item['corp_id'] . "," . $item['sub_id'] . "," . (($department == NULL) ? 'NULL' : '\'' . json_encode($department) . '\'') . ",{$type},UNIX_TIMESTAMP(), '[]')")->execute();
			}
		}

		/**
		 * 获取员工可见范围员工
		 *
		 * @param $userid
		 * @param $uid
		 * @param $corpid
		 *
		 * @return array|bool
		 *
		 * @throws InvalidParameterException
		 */
		public static function getUserSubUser ($userid, $uid, $corpid)
		{
			$workUser = WorkUser::findOne(['corp_id' => $corpid, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('当前员工数据错误！');
			}
			$user_ids   = [];
			$user_ids[] = $workUser->id;
			//分配员工数据
			if (isset($workUser->mobile) && !empty($workUser->mobile)) {
				$subUser = SubUser::findOne(['uid' => $uid, 'account' => $workUser->mobile, 'type' => 0]);
				if (!empty($subUser)) {
					$sub_detail = static::getDepartmentUserLists($subUser->sub_id, $corpid);
					if (is_array($sub_detail)) {
						$user_ids = $sub_detail;
					} elseif (!empty($sub_detail)) {
						return [];
					}
				}
			}

			return $user_ids;
		}

		/**
		 * 判断当前员工是否是主账户
		 *
		 * @param $userId
		 * @param $uid
		 * @param $corpId
		 *
		 * @return int
		 * @return_param type int 0子账户1主账户
		 * @throws InvalidParameterException
		 */
		public static function isMaster ($userId, $uid, $corpId)
		{
			$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $userId]);
			if (empty($workUser)) {
				throw new InvalidParameterException('当前员工数据错误！');
			}
			$type = 0;
			if (!empty($workUser->mobile)) {
				$subUser = SubUser::findOne(['uid' => $uid, 'account' => $workUser->mobile]);
				if (!empty($subUser)) {
					$type = $subUser->type;
				}
			}

			return $type;
		}

		/**
		 * 是否显示成员筛选
		 *
		 * @param $userId
		 * @param $uid
		 * @param $corpId
		 *
		 * @return int
		 * @throws InvalidParameterException
		 */
		public static function showMembers ($userId, $uid, $corpId)
		{
			$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $userId]);
			if (empty($workUser)) {
				throw new InvalidParameterException('当前员工数据错误！');
			}
			$show = 0; //0显示成员筛选1不显示
			//分配员工数据
			if (isset($workUser->mobile) && !empty($workUser->mobile)) {
				$subUser = SubUser::findOne(['uid' => $uid, 'account' => $workUser->mobile, 'type' => 0]);
				if (!empty($subUser)) {
					$detail      = AuthoritySubUserDetail::checkSubUser($subUser->sub_id, $corpId);
					$departments = WorkDepartment::getUserListsSubMember($detail, [], $subUser->sub_id, $corpId);
					//3 选择的成员 1选择的部门
					if (empty($departments[1]) && empty($departments[3])) {
						$show = 1;
					}
				}
			}

			return $show;
		}

		/**
		 * @param $user_id
		 * @param $uid
		 * @param $corpId
		 * @param $user_ids
		 *
		 * @return array
		 *
		 * @throws InvalidParameterException
		 */
		public static function getUserIds ($user_id, $uid, $corpId, $user_ids)
		{
			$userCount = 0;
			//判断当前员工是否是主账户 1是0否
			$isMaster = AuthoritySubUserDetail::isMaster($user_id, $uid, $corpId);
			//分配员工数据
			$userIds = AuthoritySubUserDetail::getUserSubUser($user_id, $uid, $corpId);
			if ($isMaster == 1 || $userIds == []) {
				$userIds = [];
			}
			//显示成员筛选
			if ($isMaster == 1 || $userIds == []) {
				$show = 0;
			} else {
				$show = AuthoritySubUserDetail::showMembers($user_id, $uid, $corpId);
			}
			if (!empty($user_ids)) {
				$uIds = WorkUser::getDepartUser($corpId, $user_ids);
				if (empty($uIds)) {
					$user_ids = ['0'];
				} else {
					if (!empty($userIds)) {
						$user_ids = array_intersect($userIds, $uIds);
						if (empty($user_ids)) {
							$user_ids = ['0'];
						}
					} else {
						$user_ids = $uIds;
					}
				}
				if (!empty($user_ids) && !in_array(0, $user_ids)) {
					$userCount = count($user_ids);
				}
			} else {
				if (!empty($userIds)) {
					$user_ids = $userIds;
				}
			}
			$user_ids = array_unique($user_ids);

			return [
				'show'      => $show,
				"userCount" => $userCount,
				'user_ids'  => $user_ids
			];

		}

	}
