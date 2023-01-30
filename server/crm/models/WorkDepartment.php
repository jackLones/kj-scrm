<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Matrix\Exception;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_department}}".
	 *
	 * @property int                        $id
	 * @property int                        $corp_id       授权的企业ID
	 * @property int                        $department_id 创建的部门id
	 * @property string                     $name          部门名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示部门名称
	 * @property string                     $name_en       英文名称
	 * @property int                        $parentid      父亲部门id。根部门为1
	 * @property int                        $order         在父部门中的次序值。order值大的排序靠前。值范围是[0, 2^32)
	 * @property int                        $is_del        0：未删除；1：已删除
	 *
	 * @property WorkContactWayDepartment[] $workContactWayDepartments
	 * @property WorkCorp                   $corp
	 * @property WorkTagDepartment[]        $workTagDepartments
	 * @property WorkWelcome[]              $workWelcomes
	 */
	class WorkDepartment extends \yii\db\ActiveRecord
	{
		const CREATE_PARTY = 'create_party';
		const UPDATE_PARTY = 'update_party';
		const DELETE_PARTY = 'delete_party';

		const PARTY_NO_DEL = 0;
		const PARTY_IS_DEL = 1;

		const PARTY_USER_LIST = 1;
		const PARTY_USER_INFO = 2;

		const NOT_FETCH_CHILD = 0;
		const IS_FETCH_CHILD = 1;

		const DEPARTMENT_TYPE = "work_department_parent";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_department}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'department_id', 'parentid', 'order', 'is_del'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
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
				'department_id' => Yii::t('app', '创建的部门id'),
				'name'          => Yii::t('app', '部门名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示部门名称'),
				'name_en'       => Yii::t('app', '英文名称'),
				'parentid'      => Yii::t('app', '父亲部门id。根部门为1'),
				'order'         => Yii::t('app', '在父部门中的次序值。order值大的排序靠前。值范围是[0, 2^32)'),
				'is_del'        => Yii::t('app', '0：未删除；1：已删除'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayDepartments ()
		{
			return $this->hasMany(WorkContactWayDepartment::className(), ['department_id' => 'id']);
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
		public function getWorkTagDepartments ()
		{
			return $this->hasMany(WorkTagDepartment::className(), ['department_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkWelcomes ()
		{
			return $this->hasMany(WorkWelcome::className(), ['department_id' => 'id']);
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			return [
				'id'            => $this->id,
				'corp_id'       => $this->corp_id,
				'department_id' => $this->department_id,
				'name'          => $this->name,
				'name_en'       => $this->name_en,
				'parentid'      => $this->parentid,
				'order'         => $this->order,
				'is_del'        => $this->is_del,
			];
		}

		/**
		 * @param      $corpId
		 * @param null $departId
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDepartmentList ($corpId, $departId = NULL)
		{
			$authCorp = WorkCorp::findOne($corpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$workApi = WorkUtils::getWorkApi($corpId);

			if (!empty($workApi)) {
				return $workApi->departmentList($departId);
			}

			return [];
		}

		/**
		 * @param $corpId
		 * @param $partyInfo
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setDepartment ($corpId, $partyInfo)
		{
			$workDepartment = static::findOne(['corp_id' => $corpId, 'department_id' => $partyInfo['id']]);

			if (empty($workDepartment)) {
				$workDepartment          = new WorkDepartment();
				$workDepartment->corp_id = $corpId;
				$workDepartment->is_del  = self::PARTY_NO_DEL;
			}

			$workDepartment->department_id = $partyInfo['id'];

			if (!empty($partyInfo['name'])) {
				$workDepartment->name = $partyInfo['name'];
			}

			if (!empty($partyInfo['name_en'])) {
				$workDepartment->name_en = $partyInfo['name_en'];
			}

			if (!empty($partyInfo['parentid'])) {
				$workDepartment->parentid = $partyInfo['parentid'];
			}

			if (isset($partyInfo['order'])) {
				$workDepartment->order = $partyInfo['order'];
			}

			if (isset($partyInfo['is_del'])) {
				$workDepartment->is_del = $partyInfo['is_del'];
			}

			if ($workDepartment->dirtyAttributes) {
				if (!$workDepartment->validate() || !$workDepartment->save()) {
					throw new InvalidDataException(SUtils::modelError($workDepartment));
				}
			}

			return $workDepartment->id;
		}

		/**
		 * @param int $type
		 * @param int $fetchChild
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public function getDepartUser ($type = self::PARTY_USER_LIST, $fetchChild = self::NOT_FETCH_CHILD)
		{
			$workApi = WorkUtils::getWorkApi($this->corp_id);

			$departUser = [];
			if (!empty($workApi)) {
				switch ($type) {
					case self::PARTY_USER_LIST:
						$departUser = $workApi->userSimpleList($this->department_id, $fetchChild);

						break;
					case self::PARTY_USER_INFO:
						$departUser = $workApi->userList($this->department_id, $fetchChild);

						break;
					default:
						$departUser = [];

						break;
				}
			}

			return $departUser;
		}

		/**
		 * @param int   $parentId  父级id
		 * @param int   $get_users 0 不获取成员 1 获取成员
		 * @param int   $corp_id   企业唯一标志
		 * @param array $user_param
		 * @param int   $from_chat 1 来源于群主
		 *
		 * @return array
		 *
		 */
		public static function getDepartment ($parentId = 0, $get_users = 1, $corp_id, $user_param, $from_chat, $user = [], $departmentData = [], $userInit = [], $sub_user_id = [], $checkedData = [], $is_sub = false)
		{
			$departLists = [];
			if (!empty($parentId)) {
				$departLists = static::getSubDepartment($parentId, $get_users, $corp_id, [], $user_param, $from_chat);
			} else {
				$parents = static::find()->where(['parentid' => NULL, 'is_del' => 0, 'corp_id' => $corp_id])->select(['department_id', 'name'])->asArray()->one();
				if (!empty($parents)) {
					$departLists = static::getDepartments($departLists, $parents, $get_users, $corp_id, $user_param, $from_chat);
				} else {
					//查询所有的父级id
					$parentIds  = static::find()->andWhere(['is_del' => 0, 'corp_id' => $corp_id])->select('parentid')->groupBy('parentid')->asArray()->all();
					$parent_ids = array_column($parentIds, 'parentid');
					$par_ids    = [];
					foreach ($parent_ids as $id) {
						$departsCount = static::find()->where(['corp_id' => $corp_id, 'department_id' => $id])->count();
						if ($departsCount == 0) {
							array_push($par_ids, $id);
						}
					}
					//查询所有没有父级的部门
					$departments = static::find()->where(['corp_id' => $corp_id])->andWhere(['in', 'parentid', $par_ids])->select(['department_id', 'name'])->asArray()->all();
					if (!empty($departments)) {
						foreach ($departments as $depart) {
							$departLists = static::getDepartments($departLists, $depart, $get_users, $corp_id, $user_param, $from_chat);
						}
					}
				}


			}

			if ($get_users == 1) {
				//获取所有的成员
				$getAllUsers = static::getAllUsers($corp_id, $user_param, $from_chat, $user);
				if (!empty($getAllUsers) && !empty($departLists)) {
					if ($is_sub) {
						foreach ($getAllUsers as &$item) {
							if (in_array($item['id'], $userInit) && !empty($userInit)) {
								continue;
							}
							if (in_array($item['id'], $sub_user_id) && !empty($sub_user_id)) {
								foreach ($item['department'] as $k => $v) {
									if ($v == 1) {
										continue;
									}
									if (!in_array($v, $checkedData) && !in_array($v, $departmentData) && !empty($checkedData)) {
										unset($item['department'][$k]);
									}
								}
								continue;
							}
							foreach ($item['department'] as $k => $v) {
								if ($v == 1) {
									continue;
								}
								if (!in_array($v, $departmentData) && !in_array($item['id'], $userInit) && !empty($departmentData) && !empty($userInit)) {
									unset($item['department'][$k]);
								}
							}
						}
					}
					foreach ($departLists as $key => $deartList) {
						$departListsNew = static::mergeDepartUsers($getAllUsers, $deartList, $departmentData, $user);
						if (!empty($deartList['children'])) {
							static::getSubChildren($getAllUsers, $deartList['children'], $departmentData, $user);
						}
						$children                      = array_merge($departListsNew, $deartList['children']);
						$departLists[$key]['children'] = $children;
						unset($departListsNew);
						unset($deartList);
						unset($children);
					}
				}
			}

			return $departLists;
		}

		/**
		 * @param $getAllUsers
		 * @param $departLists
		 *
		 * @return mixed
		 */
		public static function getSubChildren ($getAllUsers, &$departLists, $departmentData = [], $user = [])
		{
			foreach ($departLists as $key => $list) {
				$departLists[$key]["disabled"] = false;
				$departListsNew                = static::mergeDepartUsers($getAllUsers, $list, $departmentData, $user);
				if (!empty($list['children'])) {
					static::getSubChildren($getAllUsers, $list['children'], $departmentData, $user);
				} else {
					$list['children'] = [];
				}

				$children = array_merge($departListsNew, $list['children']);
				unset($list);
				unset($departListsNew);

				$disabled = true;
				if (!empty($children)) {
					foreach ($children as $child) {
						if (!isset($child['disabled']) || (isset($child['disabled']) && !$child['disabled'])) {
							$disabled = false;
							break;
						}
					}
				}

				$departLists[$key]["disabled"] = $disabled;
				$departLists[$key]['children'] = $children;
				unset($disabled);
				unset($children);
			}
		}

		/**
		 * @param $getAllUsers
		 * @param $departLists
		 *
		 * @return array
		 *
		 */
		public static function mergeDepartUsers ($getAllUsers, $departLists, $departmentData = [], $user = [])
		{
			$userNew = [];
			$i       = 0;
			foreach ($getAllUsers as $user) {
				if (in_array($departLists['id'], $user['department'])) {
					$user        = array_merge($user, ['key' => $user['id'] . '-' . $departLists['id']]);
					$userNew[$i] = $user;
					$i++;
				}
			}

			return $userNew;
		}

		/**
		 * 获取部分部门及员工
		 *
		 * @param int   $corp_id        企业唯一标志
		 * @param array $department_ids 部门
		 * @param array $user_ids       员工
		 * @param int   $get_users      0 不获取成员 1 获取成员
		 * @param array $user_param
		 * @param int   $from_chat
		 *
		 * @return array
		 */
		public static function getSelectDepartment ($corp_id, $department_ids = [], $user_ids = [], $get_users = 1, $user_param = [], $from_chat = 0, $user = [], $departmentData = [], $userInit = [], $sub_user_id = [], $checkedData = [], $is_sub = false)
		{
			$departLists = [];

			if (!empty($department_ids)) {
				$departments = static::find()->where(['corp_id' => $corp_id, 'is_del' => 0])->andWhere(['in', 'department_id', $department_ids])->select(['department_id', 'name'])->asArray()->all();
				if (!empty($departments)) {
					foreach ($departments as $depart) {
						$departLists = static::getDepartments($departLists, $depart, $get_users, $corp_id, $user_param, $from_chat, 1);
					}
				}
			}
//			if ($get_users == 1) {
//				//获取所有的成员
//				$getAllUsers = static::getAllUsers($corp_id, $user_param, $from_chat,$user);
//				if (!empty($getAllUsers) && !empty($departLists)) {
//					if ($is_sub) {
//						foreach ($getAllUsers as &$item) {
//							if (in_array($item['id'], $userInit) && !empty($userInit)) {
//								continue;
//							}
//							if (in_array($item['id'], $sub_user_id) && !empty($sub_user_id)) {
//								foreach ($item['department'] as $k => $v) {
//									if ($v == 1) {
//										continue;
//									}
//									if (!in_array($v, $checkedData) && !empty($checkedData)) {
//										unset($item['department'][$k]);
//									}
//								}
//								continue;
//							}
//							foreach ($item['department'] as $k => $v) {
//								if ($v == 1) {
//									continue;
//								}
//								if (!in_array($v, $departmentData) && !in_array($item['id'], $userInit) && !empty($departmentData) && !empty($userInit)) {
//									unset($item['department'][$k]);
//								}
//							}
//						}
//					}
//					foreach ($departLists as $key => $deartList) {
//						$departListsNew = static::mergeDepartUsers($getAllUsers, $deartList);
//						if (!empty($deartList['children'])) {
//							static::getSubChildren($getAllUsers, $deartList['children']);
//						}
//						$children                      = array_merge($departListsNew, $deartList['children']);
//						$departLists[$key]['children'] = $children;
//					}
//				}
//			}

			//单独成员
			if (!empty($user_ids)) {
				$users = WorkUser::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0])->andWhere(['in', 'userid', $user_ids])->select('id,corp_id,is_del,userid,name,avatar,department')->all();
				foreach ($users as $key => $user) {
					$userD                = [];
					$userD['title']       = $user->name;
					$userD['key']         = strval($user->id . '-' . $user->department);
					$userD['id']          = $user->id;
					$userD['scopedSlots'] = ['title' => 'custom'];
					$userD['avatar']      = $user->avatar;

					array_push($departLists, $userD);
				}
			}

			return $departLists;
		}

		/**
		 * 获取所有的子部门
		 *
		 * @param array $departLists 部门数组
		 * @param array $parents     子部门
		 * @param int   $get_users   0 不获取成员 1 获取成员
		 * @param int   $corp_id     企业唯一标志
		 * @param array $user_param
		 * @param int   $from_chat
		 * @param int   $fromType
		 *
		 * @return mixed
		 */
		private static function getDepartments ($departLists, $parents, $get_users, $corp_id, $user_param, $from_chat, $fromType = 0)
		{
			$data['key']         = strval($parents['department_id']);
			$children            = [];
			$children            = static::getSubDepartment($parents['department_id'], $get_users, $corp_id, $children, $user_param, $from_chat, $fromType);
			$data['id']          = strval($parents['department_id']);
			$data['title']       = $parents['name'];
			$data['scopedSlots'] = ['title' => 'title'];
			if ($fromType == 1 && $get_users == 1) {
				$result   = static::getUsers($parents['department_id'], $corp_id, $user_param, $from_chat);
				$children = array_merge($result, $children);
			}
			$data['children'] = $children;
			unset($children);
			array_push($departLists, $data);
			unset($data);

			return $departLists;
		}

		/**
		 * 获取部门员工ID
		 *
		 * @param int   $corp_id        企业唯一标志
		 * @param array $department_ids 部门
		 * @param array $user_ids       员工
		 *
		 * @return array
		 */
		public static function getDepartmentUser ($corp_id, $department_ids = [], $user_ids = [])
		{
			$departUser = [];

			if (!empty($department_ids)) {
				$departments = static::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0])->andWhere(['in', 'department_id', $department_ids])->all();
				if (!empty($departments)) {
					foreach ($departments as $depart) {
						//$departUser = static::getDepartmentsUser($departUser, $depart, $corp_id);
						$users      = WorkUser::find()->andWhere("find_in_set ($depart->department_id,department)")->andWhere(['corp_id' => $corp_id, 'is_del' => 0])->select('id')->asArray()->all();
						$usersId    = array_column($users, 'id');
						$departUser = array_merge($departUser, $usersId);

						$childrenUser = [];
						$childrenUser = static::getSubDepartmentUser($depart->department_id, $corp_id, $childrenUser);
						$departUser   = array_merge($departUser, $childrenUser);
					}
				}
			}

			if (!empty($user_ids)) {
				$users = WorkUser::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0])->andWhere(['in', 'userid', $user_ids])->select('id')->all();
				foreach ($users as $key => $user) {
					array_push($departUser, $user->id);
				}
			}

			$departUser = array_unique($departUser);

			return $departUser;
		}

		/**
		 * 获取所有的子部门员工ID
		 *
		 * @param int   $parentId 父级id
		 * @param int   $corp_id  企业唯一标志
		 * @param array $childrenUser
		 *
		 * @return array
		 *
		 */
		public static function getSubDepartmentUser ($parentId, $corp_id, $childrenUser)
		{
			$departments = static::find()->where(['parentid' => $parentId, 'corp_id' => $corp_id, 'is_del' => 0])->orderBy(['order' => SORT_DESC])->all();
			if (!empty($departments)) {
				foreach ($departments as $depart) {
					$users        = WorkUser::find()->andWhere("find_in_set ($depart->department_id,department)")->andWhere(['corp_id' => $corp_id, 'is_del' => 0])->select('id')->asArray()->all();
					$usersId      = array_column($users, 'id');
					$childrenUser = array_merge($usersId, $childrenUser);

					$childrenUser1 = static::getSubDepartmentUser($depart->department_id, $corp_id, []);
					$childrenUser  = array_merge($childrenUser1, $childrenUser);

				}
			}

			return $childrenUser;
		}

		/**
		 * 获取所有的部门成员
		 *
		 * @param int    $department_id 部门id
		 * @param int    $corp_id       企业唯一标志
		 * @param array  $user_param
		 * @param int    $from_chat     1 群主
		 * @param array  $subUser       子账户下可见成员范围
		 * @param string $name          搜索名称
		 * @param bool   $select        查询部门下是否存在人员
		 * @param bool   $departName    获取成员完整部门名称
		 *
		 * @return array|bool
		 *
		 */
		public static function getUsers ($department_id, $corp_id, $user_param, $from_chat, $subUser = [], $name = '', $select = false, $departName = false)
		{

			$result                = [];
			$disabled              = isset($user_param['disabled']) ? $user_param['disabled'] : 0;
			$welcome_user_ids      = isset($user_param['welcome_user_ids']) ? $user_param['welcome_user_ids'] : [];
			$welcome_user_ids_edit = isset($user_param['welcome_user_ids_edit']) ? $user_param['welcome_user_ids_edit'] : [];
			$agentid               = isset($user_param['agentid']) ? $user_param['agentid'] : 0;
			$form                  = isset($user_param['form']) ? $user_param['form'] : 0;
			$is_external           = isset($user_param['is_external']) ? $user_param['is_external'] : 0;
			$userDisabled          = isset($user_param['userDisabled']) ? $user_param['userDisabled'] : 0;

			$users = WorkUser::find()->where(['corp_id' => $corp_id]);
			if (in_array($form, [1, 7, 9])) {
				//$workFollwer = WorkFollowUser::find()->andWhere(['status' => 1, 'corp_id' => $corp_id])->asArray()->all();
				$workFollower = WorkFollowUser::find()->alias('f');;
				$workFollowerCount = $workFollower->leftJoin('{{%work_user}} w', 'w.id = f.user_id')->where(['f.corp_id' => $corp_id])->andWhere(['!=', 'w.status', 4])->count();

				if ($workFollowerCount == 0) {
					return $result;
				}
			}
			if ($from_chat == 8) {
				$ownerId = WorkGroupSending::sendChat($corp_id);
				if (!empty($ownerId)) {
					$users = $users->andWhere(['id' => $ownerId]);
				}
			}
			if ($form == 11) {
				$bind_userids = DialoutBindWorkUser::find()->select(['distinct IFNULL(user_id, 0) user_id'])->where(['corp_id' => $corp_id])->asArray()->all();
				$bind_userids = array_column($bind_userids, 'user_id');
				$users        = $users->andWhere(['id' => $bind_userids]);
			}
			if (!empty($user_param['audit_user_ids'])) {
				$users = $users->andWhere(['id' => $user_param['audit_user_ids']]);
			}
			if (!empty($user_param['is_del'])) {
//				$users = $users->andWhere(['status' => [2,5]]);
				$users = $users->andWhere(['is_del' => $user_param['is_del']]);
			} else {
				$users = $users->andWhere(['status' => 1]);
				$users = $users->andWhere(['is_del' => 0]);
			}
			if (!empty($subUser)) {
				$users = $users->andWhere(["in", "id", $subUser]);
			}
			if (!empty($is_external)) {
				$users = $users->andWhere(["is_external" => $is_external]);
			}

			if ((!empty($name) || $name == 0) && $name != '') {
				$users = $users->andWhere("name like '%$name%'");
			}
			if ($select) {
				if (!empty($department_id) && $department_id != 1) {
					$users = $users->andWhere("find_in_set ($department_id,department)");
				}
				$users = $users->count();

				return $users;
			} else {
				if (!empty($department_id)) {
					$users = $users->andWhere("find_in_set ($department_id,department)");
				}
				$users = $users->select('id,corp_id,is_del,userid,name,avatar,department')->asArray()->all();
			}
			if (!empty($users)) {

				$TempUserDisabled = [];
				//员工删除已设置员工禁选
				if ($form == 2 && !empty($agentid)) {
					$WorkUserDelFollowUser = WorkUserDelFollowUser::find()->andWhere(['corp_id' => $corp_id, 'agent' => $agentid])->all();
					$TempUserDisabled      = array_column($WorkUserDelFollowUser, "user_id");
				}
				//员工待办已设置员工禁选
				if ($form == 3 && !empty($agentid)) {
					$WorkUserCommissionRemind = WorkUserCommissionRemind::find()->where(['corp_id' => $corp_id, 'agent' => $agentid])->asArray()->all();
					$TempUserDisabled         = array_column($WorkUserCommissionRemind, "user_id");
				}
				//员工跟进设置员工禁选
				if ($form == 4 && !empty($agentid)) {
					$WorkFollowMsg    = WorkFollowMsg::find()->where(['corp_id' => $corp_id, "agentid" => $agentid])->asArray()->all();
					$TempUserDisabled = array_column($WorkFollowMsg, "user_id");
				}
				foreach ($users as $user) {
					$userData          = [];
					$userData['title'] = $user['name'];
					if (in_array($form, [1, 7, 9])) {
						$followUserCount      = WorkFollowUser::find()->where(['corp_id' => $user['corp_id'], 'user_id' => $user['id'], 'status' => 1])->exists();
						$userData['disabled'] = false;
						if (!$followUserCount) {
							$userData['disabled'] = true;
							$userData['title']    = $user['name'] . '（无权限）';
						}
						if ($user['is_del'] == 1) {
							$userData['disabled'] = true;
							$userData['title']    = $user['name'] . '（已删除）';
						}
					}
					if ($userDisabled == 1 && $form == 0) {
						$userData['disabled'] = true;
					}
					$userData['key']         = strval($user['id'] . '-' . $department_id);
					$userData['id']          = $user['id'];
					$userData['scopedSlots'] = ['title' => 'custom'];
					$userData['avatar']      = $user['avatar'];
					$userData["isLeaf"]      = true;
					if ($disabled == 1) {
						//对欢迎语单独判断
						if (empty($welcome_user_ids_edit)) {
							if (in_array($user['id'], $welcome_user_ids)) {
								$userData['disabled'] = true;//禁用
							}
						} else {
							if (in_array($user['id'], $welcome_user_ids) && !in_array($user['id'], $welcome_user_ids_edit)) {
								$userData['disabled'] = true;//禁用
							}
						}
					}
					if (!empty($user_param['is_audit_edit'])) {
						if (!empty($user_param['audit_edit_ids'])) {
							if (in_array($user['id'], $user_param['audit_edit_ids'])) {
								$userData['disabled'] = true;//禁用
							}
						}
					}
					$userData['departmentNameAll'] = $userData["departmentName"] = '';
					/**获取完整的成员部门名称*/
					if ($departName) {
						$department = explode(",", $user["department"]);
						if (!empty($department)) {
							$Dname = WorkDepartment::find()->where(["corp_id" => $corp_id, "is_del" => 0])->andWhere(["in", "department_id", $department])->select("name")->asArray()->all();
							if (!empty($Dname)) {
								$userData["departmentName"] = implode("；", array_column($Dname, "name"));
								foreach ($department as $kk => $vv) {
									if (!empty($vv)) {
										if ($vv == 1) {
											$userData['departmentNameAll'] .= $Dname[$kk]["name"] . '<br/>';
											continue;
										}
										$parentId = \Yii::$app->db->createCommand("SELECT getParentList(" . $vv . "," . $corp_id . ") as department;")->queryOne();
										if (!empty($parentId)) {
											$parentId       = explode(",", $parentId["department"]);
											$departmentName = WorkDepartment::find()->where(["in", "department_id", $parentId])->andWhere(["corp_id" => $corp_id, "is_del" => 0])->orderBy("parentid asc")->asArray()->all();
											if (!empty($departmentName)) {
												$departmentName                = array_column($departmentName, "name");
												$str                           = implode("/", $departmentName);
												$userData['departmentNameAll'] .= $str . "/" . $Dname[$kk]["name"] . '<br/>';
											}
										}
									}
								}
								$userData['departmentNameAll'] = mb_substr($userData['departmentNameAll'], 0, strlen($userData["departmentNameAll"]) - 5);
								if (empty($userData['departmentNameAll'])) {
									$userData['departmentNameAll'] = $userData["departmentName"];
								}
							}
						}
					}
					if (in_array($userData["id"], $TempUserDisabled)) {
						$userData['disabled'] = true;//禁用
					}
					array_push($result, $userData);
					unset($userData);
				}


			}

			return $result;
		}

		/**
		 * 获取所有成员
		 *
		 * @param $corp_id
		 * @param $user_param
		 * @param $from_chat
		 *
		 * @return array
		 *
		 */
		public static function getAllUsers ($corp_id, $user_param, $from_chat, $user = [])
		{
			$result                = [];
			$user_ids              = [];
			$is_del                = isset($user_param['is_del']) ? $user_param['is_del'] : 0;
			$from_channel          = isset($user_param['from_channel']) ? $user_param['from_channel'] : 0;
			$disabled              = isset($user_param['disabled']) ? $user_param['disabled'] : 0;
			$welcome_user_ids      = isset($user_param['welcome_user_ids']) ? $user_param['welcome_user_ids'] : [];
			$welcome_user_ids_edit = isset($user_param['welcome_user_ids_edit']) ? $user_param['welcome_user_ids_edit'] : [];

			if ($from_channel == 1) {
				//$workFollwer = WorkFollowUser::find()->andWhere(['status' => 1, 'corp_id' => $corp_id])->asArray()->all();
				$workFollower = WorkFollowUser::find()->alias('f');;
				$workFollowerCount = $workFollower->leftJoin('{{%work_user}} w', 'w.id = f.user_id')->andWhere(['f.corp_id' => $corp_id])->andWhere(['!=', 'w.status', 4])->count();
				if ($workFollowerCount == 0) {
					return $result;
				}
			}
			if (!empty($user)) {
				$users = WorkUser::find()->where(['is_del' => $is_del])->andWhere(["in", "id", $user]);
			} else {
				$users = WorkUser::find()->where(['corp_id' => $corp_id, 'is_del' => $is_del]);
			}

			if (isset($user_param["is_external"]) && !empty($user_param["is_external"])) {
				$users = $users->andWhere(["is_external" => 1]);
			}
			if ($from_channel == 1) {
				$users = $users->andWhere(['status' => 1]);
			}
			if ($from_chat == 1) {
				$ownerId = WorkGroupSending::sendChat($corp_id);
				\Yii::error($ownerId, '$ownerId');
				if (!empty($ownerId)) {
					$users = $users->andWhere(['id' => $ownerId]);
				}
			}
			if (!empty($user_param['audit_user_ids'])) {
				$users = $users->andWhere(['id' => $user_param['audit_user_ids']]);
			}
			$users = $users->select('id,corp_id,is_del,userid,name,avatar,department')->asArray()->all();
			if (!empty($users)) {
				foreach ($users as $user) {
					$userData          = [];
					$userData['title'] = $user['name'];
					if ($from_channel == 1) {
						$followUserCount      = WorkFollowUser::find()->where(['corp_id' => $user['corp_id'], 'user_id' => $user['id'], 'status' => 1])->count();
						$userData['disabled'] = false;
						if ($followUserCount == 0) {
							$userData['disabled'] = true;
							$userData['title']    = $user['name'] . '（无权限）';
						}
						if ($user['is_del'] == 1) {
							$userData['disabled'] = true;
							$userData['title']    = $user['name'] . '（已删除）';
						}
					}
					$userData['id']          = $user['id'];
					$userData['scopedSlots'] = ['title' => 'custom'];
					$userData['avatar']      = $user['avatar'];
					$userData['department']  = explode(',', $user['department']);
					if ($disabled == 1) {
						//对欢迎语单独判断
						if (empty($welcome_user_ids_edit)) {
							if (in_array($user['id'], $welcome_user_ids)) {
								$userData['disabled'] = true;//禁用
							}
						} else {
							if (in_array($user['id'], $welcome_user_ids) && !in_array($user['id'], $welcome_user_ids_edit)) {
								$userData['disabled'] = true;//禁用
							}
						}
					}
					if (!empty($user_param['is_audit_edit'])) {
						if (!empty($user_param['audit_edit_ids'])) {
							if (in_array($user['id'], $user_param['audit_edit_ids'])) {
								$userData['disabled'] = true;//禁用
							}
						}
					}

					array_push($result, $userData);
				}
			}

			return $result;
		}

		/**
		 * 获取所有的子部门
		 *
		 * @param int   $parentId  父级id
		 * @param int   $get_users 0 不获取成员 1 获取成员
		 * @param int   $corp_id   企业唯一标志
		 * @param array $children
		 * @param array $user_param
		 * @param int   $from_chat
		 * @param int   $fromType
		 *
		 * @return mixed
		 */
		public static function getSubDepartment ($parentId, $get_users, $corp_id, $children, $user_param, $from_chat, $fromType = 0)
		{
			$departments = static::find()->where(['parentid' => $parentId, 'corp_id' => $corp_id, 'is_del' => 0])->orderBy(['order' => SORT_DESC]);
			if (!empty($user_param['audit_depart_ids'])) {
				$departments = $departments->andWhere(['department_id' => $user_param['audit_depart_ids']]);
			}
			$departments = $departments->select(['department_id', 'name'])->asArray()->all();
			if (!empty($departments)) {
				foreach ($departments as $depart) {
					$data                  = [];
					$data['key']           = strval($depart['department_id']);
					$data['department_id'] = strval($depart['department_id']);
					$data['id']            = strval($depart['department_id']);
					$data['title']         = $depart['name'];
					$data['scopedSlots']   = ['title' => 'title'];
					$children1             = static::getSubDepartment($depart['department_id'], $get_users, $corp_id, [], $user_param, $from_chat, $fromType);
					if ($fromType == 1 && $get_users == 1) {
						$result    = static::getUsers($depart['department_id'], $corp_id, $user_param, $from_chat);
						$children1 = array_merge($result, $children1);
					}
					$data['children'] = $children1;
					unset($children1);
					array_push($children, $data);
					unset($data);
				}
			}

			unset($departments);

			return $children;
		}

		/**
		 * 获取所有的子部门
		 *
		 * @param $parentId
		 * @param $corp_id
		 * @param $data
		 *
		 * @return mixed
		 *
		 */
		public static function getSubDepart ($parentId, $corp_id, $data)
		{
			$departments = static::find()->where(['parentid' => $parentId, 'corp_id' => $corp_id, 'is_del' => 0])->all();
			if (!empty($departments)) {
				/** @var WorkDepartment $depart */
				foreach ($departments as $depart) {
					array_push($data, $depart->department_id);
					$data = static::getSubDepart($depart->department_id, $depart->corp_id, $data);
				}
			}

			return $data;
		}

		/**
		 * 获取所有的父级部门
		 *
		 * @param $parentId
		 * @param $corp_id
		 * @param $data
		 *
		 * @return mixed
		 *
		 */
		public static function getParentDepart ($parentId, $corp_id, $data)
		{
			$departments = static::find()->where(['department_id' => $parentId, 'corp_id' => $corp_id, 'is_del' => 0])->all();
			if (!empty($departments)) {
				/** @var WorkDepartment $depart */
				foreach ($departments as $depart) {
					array_push($data, $depart->department_id);
					$data = static::getParentDepart($depart->parentid, $depart->corp_id, $data);
				}
			}

			return $data;
		}

		/**
		 * 获取所有子部门的员工
		 *
		 * @param $department
		 * @param $corpId
		 * @param $is_leader_in_dept
		 *
		 * @return array
		 */
		public static function getDepartId ($department, $corpId, $is_leader_in_dept)
		{
			$userId = [];
			if (!empty($department)) {
				$depart     = explode(',', $department);
				$leadDepart = explode(',', $is_leader_in_dept);
				$workDepart = static::getSubDepart($depart, $corpId, []);
				$data       = array_unique($workDepart);
				if (!empty($data)) {
					foreach ($data as $v) {
						$user = WorkUser::find()->where("find_in_set ('" . $v . "',department)")->andWhere(['corp_id' => $corpId])->select('id')->asArray()->all();
						if (!empty($user)) {
							foreach ($user as $val) {
								array_push($userId, $val['id']);
							}
						}
					}
				}
				if (!empty($depart) && !empty($leadDepart)) {
					foreach ($depart as $k => $v) {
						foreach ($leadDepart as $kk => $val) {
							if ($k == $kk) {
								if ($val == 1) {
									$user = WorkUser::find()->where("find_in_set ('" . $v . "',department)")->andWhere(['corp_id' => $corpId])->select('id')->asArray()->all();
									if (!empty($user)) {
										foreach ($user as $va) {
											array_push($userId, $va['id']);
										}
									}
								}
							}
						}
					}
				}
			}
			$userId = array_unique($userId);

			return $userId;
		}

		/**
		 * 根据部门id获取成员
		 *
		 * @param int $departId 部门id
		 * @param int $corp_id  企业唯一标志
		 *
		 * @return array
		 *
		 */
//		public static function getUsersByDepartId ($departId, $corp_id)
//		{
//			$result      = [];
//			$departments = static::findOne($departId);
//			$departs     = static::find()->andWhere(['corp_id' => $corp_id, 'parentid' => $departments->department_id])->all();
//			if (!empty($departs)) {
//				foreach ($departs as $dep) {
//					$result = static::getUsers($dep->department_id, $corp_id);
//					static::getUsersByDepartId($dep->department_id, $corp_id);
//				}
//			}
//
//			return $result;
//		}

		/**
		 * 根据成员id获取部门名称
		 *
		 * @param string $departId 当corpId=0时代表成员id，否则代表部门id
		 * @param int    $corpId
		 *
		 * @return string
		 */
		public static function getDepartNameByUserId ($departId, $corpId = 0)
		{
			$departName = '';
			if (empty($corpId)) {
				$userId = $departId;
				if (!empty($userId)) {
					$workUser   = WorkUser::findOne($userId);
					$departId   = $workUser->department;
					$department = WorkDepartment::find()->andWhere(['in', 'department_id', explode(",", $departId)])->andWhere(['corp_id' => $workUser->corp_id])->asArray()->all();
					if (!empty($department)) {
						$name       = array_column($department, 'name');
						$departName = implode('/', $name);
					}
				}
			} else {
				if (!empty($departId)) {
					$department = self::find()->where(['corp_id' => $corpId])->andWhere(['department_id' => explode(",", $departId)])->select('name')->asArray()->all();
					if (!empty($department)) {
						$name       = array_column($department, 'name');
						$departName = implode('/', $name);
					}
				}
			}

			return $departName;
		}

		/**
		 * 重组部门员工列表
		 *
		 * @param $detail
		 * @param $data
		 *
		 * @return array
		 */
		public static function getUserListsSubMember ($detail, $data, $sub_id, $corp_id)
		{
			$department  = empty($detail['department']) ? [] : json_decode($detail['department']);
			$user_key    = empty($detail['user_key']) ? [] : json_decode($detail['user_key']);
			$checkedD    = empty($detail['checked_list']) ? [] : json_decode($detail['checked_list']);
			$checkedData = [];
			if (!empty($checkedD)) {
				$checkedD = array_column($checkedD, "user_key");
				foreach ($checkedD as $value) {
					$tmp = explode('-', $value);
					array_push($checkedData, $tmp[1]);
				}
			}
			$sub_user_id = WorkUser::find()->alias("s")
				->leftJoin("{{%sub_user}} as w", "s.mobile = w.account")
				->where(["w.sub_id" => $sub_id, "s.corp_id" => $corp_id, "w.type" => 0])
				->andWhere(["!=", "s.status", 4])
				->select("s.id")->asArray()->all();
			$userKeyALL  = [];
			if (!empty($sub_user_id)) {
				$sub_user_id    = array_column($sub_user_id, "id");
				$userKeyALL     = array_merge($user_key, $sub_user_id);
				$TempDepartment = self::getTopNextUserDepartment($userKeyALL, $corp_id);
			} else {
				$sub_user_id = [];
			}
			if (!empty($department)) {
				$TempData = [];
				foreach ($department as $value) {
					$temp = self::getParentDepart($value, $corp_id, $TempData);
					array_push($TempData, ...$temp);
				}
				$department = self::getDepartmentChildren($department, $corp_id);
				foreach ($department as $item) {
					$workUser   = WorkUser::find()->where("FIND_IN_SET(" . $item . ",department) ")->andWhere(["!=", "status", 4])->andWhere(["corp_id" => $corp_id])->select("id")->asArray()->all();
					$workUser   = array_column($workUser, 'id');
					$userKeyALL = array_merge($userKeyALL, $workUser);
				}
				$department = array_unique(array_merge($department, $TempData));
			}
			if (!empty($TempDepartment)) {
				$department = array_unique(array_merge($department, $TempDepartment));
			}

			//所有人id(包含部门下的)，部门id,   子账户人员id,   选中成员id,    选中成员部门id
			return [$userKeyALL, $department, $sub_user_id, $user_key, $checkedData];
		}

		public static function getDepartmentChildren ($array, $corp_id)
		{
			$data = [];
			foreach ($array as $item) {
				$tmp  = static::getSubDepart($item, $corp_id, $data);
				$data = array_merge($data, $tmp);
			}
			$data = array_merge($array, $data);

			return $data;
		}

		public static function getChildrenDepartment (&$array, $department, $user_key, &$tmp)
		{
			foreach ($array as $key => $item) {
				if (!isset($item['children']) && !in_array($item['id'], $user_key)) {
					unset($array[$key]);
//					if(isset($item['children']) && empty($item['children'])){
//						$tmp = true;
//					}
					continue;
				}
				if (in_array($item['id'], $department)) {
					return;
				}
				if (!empty($item['children'])) {
					self::getChildrenDepartment($array[$key]['children'], $department, $user_key, $tmp);
				}
			}
		}


//		public static function getSelectedUser($crop_id,$user_key,$departments)
//		{
////			if(!empty($departments)){
//				$array1 = [];
//				$departmentData = self::find()->where(["corp_id"=>$crop_id,"is_del"=>0])->asArray()->all();
//				$data = self::getWorkDepartment($departmentData,$departments);
//				return $data;
////			}
//		}
//
//
//		public static function getWorkDepartment($array,$departments)
//		{
//			$tree = [];
//			$newData = [];
//			//循环重新排列
//			foreach ($array as $datum) {
//				$newData[$datum['id']] = $datum;
//			}
//			foreach ($newData as $key => $datum) {
//				if ($datum['parentid'] > 0) {
//					$newData[$datum['parentid']]['children'][] = &$newData[$key];
//				} else {
//					$datum['key'] = 1;
//					$tree[] = &$newData[$datum['id']];
//				}
//			}
//			return $tree;
//		}
		public static function FormatData (&$data, &$data1)
		{
			/**sym 刪除選擇部門但是查询需要回写*/
			if (isset($data)) {
				foreach ($data as &$valueA) {
					foreach ($valueA as &$cc) {
						if (isset($valueA["time"])) {
							foreach ($valueA["time"] as &$vv) {
								if (isset($vv["userList"]) && is_array($vv["userList"])) {
									$TempA = [];
									foreach ($vv["userList"] as $key => $item) {
										if (strpos($item["id"], 'd') === false) {
											array_push($TempA, $item);
										}
									}
									$vv["userList"] = $TempA;
								}
							}
						}
					}
				}
			}
			unset($valueA, $cc, $vv);
			$TempData = $data1;
			if (isset($TempData[0])) {
				foreach ($TempData[0] as $key => $value) {
					foreach ($value as &$vv) {
						if (isset($vv["userList"]) && is_array($vv["userList"])) {
							$Temp = [];
							foreach ($vv["userList"] as $cc) {
								if (strpos($cc["id"], 'd') === false) {
									array_push($Temp, $cc);
								}
							}
							$vv["userList"] = $Temp;
						}
					}
					$TempData[0][$key] = $value;
				}
			}
			unset($vv);
			$data1 = $TempData;

		}

		public static function ActivityDataFormat (&$userList, $corpId, $department, $depart = true)
		{
			foreach ($userList as $kk => &$list) {
				$list['id']     = (string) $list['id'];
				$list['is_del'] = 0;
				if (isset($list["user_key"])) {
					$list["key"] = $list["user_key"];
				}
				$list["scopedSlots"] = ['title' => 'custom'];
				if (strpos($list["id"], 'd') !== false) {
					$list["scopedSlots"] = ["title" => "title"];
				}
				$name = isset($list["title"]) ? $list["title"] : (isset($list["name"]) ? $list["name"] : '');
				if (!isset($list["title"])) {
					$list["title"]  = $name;
					$list["isLeaf"] = true;
				}
				$followUser = WorkFollowUser::findOne(['corp_id' => $corpId, 'user_id' => $list['id'], 'status' => 1]);
				$workUser   = WorkUser::findOne($list['id']);
				$name       = !empty($workUser) ? $workUser->name : "";
				if (empty($followUser)) {
					if (strpos($list["id"], 'd') === false && !empty($workUser)) {
						$str          = ($workUser->is_del == 1) ? '（已删除）' : '';
						$str          = ($workUser->status == 5) ? '（已退出）' : $str;
						$list['name'] = $list['title'] = $name . $str;
					}
					if (strpos($list["id"], 'd') === false && empty($followUser)) {
						$list['name'] = $list['title'] = $name . '（无权限）';
					}
				}
				if (!empty($followUser)) {
					if (strpos($list["id"], 'd') === false) {
						$list['name'] = $list['title'] = $name;
					}
				}

			}

			if (!empty($department)) {
				if (!is_array($department)) {
					$department = json_decode($department);
				}
				if ($depart) {
					$department = WorkDepartment::find()->where(["in", "id", $department])->select("id,department_id as key,parentid,name as title,department_id")->asArray()->all();
				} else {
					$department = WorkDepartment::find()->where(["in", "department_id", $department])->andWhere(["corp_id" => $corpId])->select("id,department_id as key,parentid,name as title,department_id")->asArray()->all();
				}
				foreach ($department as $item) {
					$item["ids"]         = $item["id"];
					$item["id"]          = "d-" . $item["department_id"];
					$item["scopedSlots"] = ["title" => "title"];
					array_push($userList, $item);
				}
			}
			$userList = array_values($userList);
		}

		/**
		 * @param int|string $corp_id     企业微信id
		 * @param int|string $agent_id    应用id
		 * @param int|string $is_del      是否删除
		 * @param int|string $is_external 是否具有外部联系人权限
		 *                                获取应用下面的范围
		 *
		 * @return array
		 */
		public static function GiveAgentIdReturnDepartmentOrUser ($corp_id, $agent_id, $is_del = 0, $is_external = 0)
		{
			$AgentDepartmentOld = $UserDepartment = $AgentUserIds = $AgentDepartment = [];
			/**应用可见范围成员*/
			if (!empty($agent_id)) {
				$agentInfo = WorkCorpAgent::findOne($agent_id);
				if (!empty($agentInfo->allow_party) || !empty($agentInfo->allow_user)) {
					$AgentDepartment = !empty($agentInfo->allow_party) ? explode(',', $agentInfo->allow_party) : [];
					$AgentUserIds    = !empty($agentInfo->allow_user) ? explode(',', $agentInfo->allow_user) : [];
					if (!empty($AgentUserIds)) {
						$users          = WorkUser::find()->andWhere(['corp_id' => $corp_id])->andWhere(['in', 'userid', $AgentUserIds])->select('id')->all();
						$AgentUserIds   = array_column($users, "id");
						$UserDepartment = self::getTopNextUserDepartment($AgentUserIds, $corp_id);
						if (in_array(1, $UserDepartment)) {
							sort($UserDepartment);
							unset($UserDepartment[0]);
						}
					}
					if (in_array(1, $AgentDepartment)) {
						self::GiveDepartmentReturnUserArray([], $corp_id, $AgentUserIds, true, $is_del, $is_external, [], true);
						$AgentDepartment = self::GiveDepartmentReturnChildren([1], $corp_id);
					} else {
						if (!empty($AgentDepartment)) {
							$AgentDepartment = self::GiveDepartmentReturnChildren($AgentDepartment, $corp_id);
							self::GiveDepartmentReturnUserArray($AgentDepartment, $corp_id, $AgentUserIds, true, $is_del, $is_external, [], false);
						}
					}
					$AgentDepartmentOld = $AgentDepartment;
					$AgentDepartment    = array_merge($UserDepartment, $AgentDepartment);
				}
			}

			return [$AgentDepartment, $AgentUserIds, $AgentDepartmentOld];
		}

		/**
		 * @param int|string $corp_id     企业微信id
		 * @param int|string $sub_id      子账户id
		 * @param int|string $is_del      是否删除
		 * @param int|string $is_external 是否外部联系人权限
		 * @param bool       $is_all      获取完整部门
		 *                                获取子账户下面的范围
		 *
		 * @return array
		 */
		public static function GiveSubIdReturnDepartmentOrUser ($corp_id, $sub_id, $is_del = 0, $is_external = 0, $is_all = true)
		{
			$subUser          = [];
			$subDepartment    = [];
			$subDepartmentOld = [];
			$all              = true;
			/**子账户限定*/
			if (!empty($sub_id)) {
				$detail      = AuthoritySubUserDetail::checkSubUser($sub_id, $corp_id);
				$subWorkUser = SubUser::findOne(["sub_id" => $sub_id]);
				if ($detail["type_all"] == 1) {
					$all = false;
				} else {
					if ($detail["type_all"] == 2) {
						$subDepartmentOld = $subDepartment = $subUser = [0];
						if (!empty($subWorkUser)) {
							$subWorkUser = WorkUser::findOne(["corp_id" => $corp_id, "status" => 1, "is_del" => 0, "mobile" => $subWorkUser->account]);
							if (!empty($subWorkUser) && !in_array($subWorkUser->id, $subUser)) {
								$subUser[]      = $subWorkUser->id;
								$TempDepartment = self::getTopNextUserDepartment($subUser, $corp_id);
								if (in_array(1, $TempDepartment)) {
									sort($TempDepartment);
									unset($TempDepartment[0]);
								}
								$subDepartment = $TempDepartment;
							}
						}
					} else {
						$department = empty($detail['department']) ? [] : json_decode($detail['department']);
						$user_key   = empty($detail['user_key']) ? [] : json_decode($detail['user_key']);
						if (!in_array(1, $department)) {
							$newData          = [];
							$parentDepartment = [];
							foreach ($department as $vv) {
								$temp = \Yii::$app->db->createCommand("SELECT getParentList(" . $vv . "," . $corp_id . ") as department;")->queryOne();
								if (!empty($temp) && !empty($temp["department"])) {
									$parentId = explode(",", $temp["department"]);
									if (in_array(1, $department)) {
										$key = array_search(1, $parentId);
										if ($key !== false) {
											unset($parentId[$key]);
										}
									}
									array_push($parentDepartment, ...$parentId);
								}
							}
							$parentDepartment = array_unique($parentDepartment);
							$subDepartmentOld = $subDepartment = self::GiveDepartmentReturnChildren($department, $corp_id, $newData);
							$subDepartmentOld = array_merge($subDepartmentOld, $parentDepartment);
							$user_Temp        = [];
							self::GiveDepartmentReturnUserArray($subDepartment, $corp_id, $user_Temp, true, $is_del, $is_external);
							$user_key = array_unique(array_merge($user_Temp, $user_key));
							if (!empty($subWorkUser)) {
								$subWorkUser = WorkUser::findOne(["corp_id" => $corp_id, "status" => 1, "is_del" => 0, "mobile" => $subWorkUser->account]);
								if (!empty($subWorkUser) && !in_array($subWorkUser->id, $subUser)) {
									$user_key[] = $subWorkUser->id;
								}
							}
							$subUser = $user_key;
							if ($is_all) {
								$TempDepartment = self::getTopNextUserDepartment($subUser, $corp_id, $newData);
								if (in_array(1, $TempDepartment)) {
									sort($TempDepartment);
									unset($TempDepartment[0]);
								}
								$subDepartment = array_unique(array_merge($subDepartment, $TempDepartment));
							}
						} else {
							$all = false;
						}
					}
				}
			}

			return [$subUser, $subDepartment, $all, $subDepartmentOld];

		}

		/**
		 * @param       $corp_id
		 * @param       $data
		 * @param array $user_param 参数
		 * @param int   $from_chat  格式化部门
		 *
		 * @return array
		 */
		public static function FormattingData ($corp_id, $data, $user_param, $from_chat)
		{

			$is_del             = isset($user_param["is_del"]) ? $user_param["is_del"] : 0;
			$agentid            = isset($user_param["agentid"]) ? $user_param["agentid"] : 0;
			$subScope           = isset($user_param["subScope"]) ? $user_param["subScope"] : 0;
			$disabledPart       = isset($user_param["disabledPart"]) ? $user_param["disabledPart"] : [];
			$welcome_department = isset($user_param["welcome_department"]) ? $user_param["welcome_department"] : [];
			$disabledPart       = $disabledPart + $welcome_department;
			[$parentId, $uid, $is_external, $sub_id1, $from_channel] = $data;
			$AgentDepartmentOld = $AgentDepartment = $AgentUserIds = $subUser = $subDepartment = $subDepartmentOld = [];
			$sub_id             = 0;
			$all                = true;
			if (!empty($agentid)) {
				[$AgentDepartment, $AgentUserIds, $AgentDepartmentOld] = self::GiveAgentIdReturnDepartmentOrUser($corp_id, $agentid, $is_del, $is_external);
				if ($subScope == 1 && !empty($sub_id1)) {
					$sub_id = $sub_id1;
				} else {
					$subScope = 0;
				}
			}
			if ($subScope == 1) {
				[$subUser, $subDepartment, $all, $subDepartmentOld] = self::GiveSubIdReturnDepartmentOrUser($corp_id, $sub_id1, $is_del, $is_external);
				$sub_id = $sub_id1;
				/** 范围限定包含应用范围限定**/
				if (!empty($agentid) && $all) {
					$subDepartment = array_intersect($subDepartment, $AgentDepartment);
					$subUser       = array_intersect($subUser, $AgentUserIds);
				}
				/** 范围限定包含应用范围限定**/
				if (!empty($agentid) && $all === false) {
					$subDepartment = empty($AgentDepartment) ? [0] : $AgentDepartment;
					$subUser       = empty($AgentUserIds) ? [0] : $AgentUserIds;
				}
			} else {
				/** 应用范围限定**/
				if (!empty($agentid)) {
					$subDepartment = $AgentDepartment;
					$subUser       = $AgentUserIds;
				}
			}
//			$TempU = [];
//
//			$DepartmentCount = self::GiveDepartmentReturnUserArray([], $corp_id, $TempU, true, $is_del, $is_external, $user_param["audit_user_ids"], false, true, $subUser);
			/**获取部门*/
			$departmentLists = WorkDepartment::find()->where(["corp_id" => $corp_id, "is_del" => 0]);
			$departmentLists = $departmentLists->andWhere(["parentid" => $parentId]);
			$departmentLists = $departmentLists->select("id,department_id as key,parentid,name as title,department_id")
				->asArray()->all();
			if ($from_chat == 1 || $from_chat == 7) {
				$is_external = 1;
			}
			if ($from_chat == 8) {
				$ownerId = WorkGroupSending::sendChat($corp_id);
				if (!empty($ownerId) && !empty($subUser)) {
					$subUser = array_intersect($ownerId, $subUser);
				} else {
					$subUser = $ownerId;
				}
			}
			/**查询所有员工*/
			if (!empty($parentId)) {
				$workUserData          = self::getUsers($parentId, $corp_id, $user_param, $from_chat, $subUser);
				$departmentChildrenIds = array_column($departmentLists, "department_id");
				$TempU                 = [];
//				Yii::error($departmentChildrenIds, __CLASS__ . ':' . __FUNCTION__ . '-$departmentChildrenIds');
				$departmentChildrenCount = self::GiveDepartmentReturnUserArray($departmentChildrenIds, $corp_id, $TempU, true, $is_del, $is_external, $user_param["audit_user_ids"], false, true, $subUser);
//				Yii::error($departmentChildrenCount, __CLASS__ . ':' . __FUNCTION__ . '-$departmentChildrenCount');
				[$departmentChildrenDepartmentCount, $departmentChildrenDepartmentAll] = self::GiveDepartmentReturnChildResult($corp_id, $departmentChildrenIds, $subDepartmentOld);
				foreach ($departmentLists as $key => &$list) {
					$list["isLeaf"] = false;
					$list["ids"]    = $list["id"];
					if (!empty($agentid) && !in_array($list["department_id"], $AgentDepartment)) {
						$list["disabled"] = true;
					}
					if (!empty($agentid) && !in_array($list["department_id"], $AgentDepartmentOld)) {
						$list["disabled"] = true;
					}
					$list["disabled"] = false;
					$list["titleAll"] = '';
					if (!empty($sub_id) && $all) {
						/**子账户限定*/
						if (!in_array($list["department_id"], $subDepartment)) {
							$list["isLeaf"]   = true;
							$list["disabled"] = true;
						}
						/**范围中不存在部门不允许点击*/
						if (!in_array($list["department_id"], $subDepartmentOld)) {
							$list["disabled"] = true;
						}
						if (in_array($list["department_id"], $subDepartmentOld)) {
							$list["isLeaf"] = false;
						}
					}
					$workUser       = 0;
					$departmentNext = 0;
					if (isset($departmentChildrenCount[$list["department_id"]])) {
						$workUser = $departmentChildrenCount[$list["department_id"]];
					}
					if (isset($departmentChildrenDepartmentAll[$list["department_id"]])) {
						$departmentNext = $departmentChildrenDepartmentAll[$list["department_id"]];
					}
					if (($workUser == 0 && count($departmentNext) <= 1) && $from_chat != 9) {
						$list["isLeaf"]   = true;
						$list["disabled"] = true;
					}
					$list["id"] = "d-" . $list["department_id"];
					if (in_array($list["department_id"], $disabledPart) && !empty($disabledPart)) {
						$list["disabled"] = true;
					}
					$list["scopedSlots"] = ["title" => "title"];
					if ($user_param["departmentDisabled"] == 1) {
						$list["disabled"] = true;
					}
					if (!empty($workUserData)) {
						$workUserData[] = $list;
					}
				}
				$departmentLists = array_values($departmentLists);
				if (!empty($workUserData)) {
					$departmentLists = $workUserData;
				}
			} else {
				foreach ($departmentLists as $key => &$list) {
					$list["isLeaf"] = false;
					$list["ids"]    = $list["id"];

					if (!empty($agentid) && !in_array($list["department_id"], $AgentDepartment)) {
						$list["disabled"] = true;
					}
					if (!empty($agentid) && !in_array($list["department_id"], $AgentDepartmentOld)) {
						$list["disabled"] = true;
					}
					if (!empty($sub_id) && $all) {
						/**子账户限定*/
						$workUser = self::getUsers($list["department_id"], $corp_id, $user_param, $from_chat, $subUser);
						if (!in_array($list["department_id"], $subDepartmentOld)) {
							$list["disabled"] = true;
						}
					} else {
						$workUser = self::getUsers($list["department_id"], $corp_id, $user_param, $from_chat, $subUser);
					}
					$list["scopedSlots"] = ["title" => "title"];
					$list["id"]          = "d-" . $list["department_id"];
					$list["children"]    = $workUser;
					$list["titleAll"]    = '';
					/**部门下的所有人*/
//					$list["userCount"]  = self::getUsers($list["department_id"], $corp_id, $user_param, $from_chat, $subUser, '', true);
					$departmentChildren = WorkDepartment::find()->where(["corp_id" => $corp_id, "parentid" => $list["department_id"], "is_del" => 0]);
					$departmentChildren = $departmentChildren->select("id,department_id as key,parentid,name as title,department_id")
						->asArray()->all();
					if (empty($workUser) && empty($departmentChildren)) {
						$list["isLeaf"] = true;
					}
					if (in_array($list["department_id"], $disabledPart) && !empty($disabledPart)) {
						$list["disabled"] = true;
					}
					$TempU                 = [];
					$departmentChildrenIds = array_column($departmentChildren, "department_id");
					[$departmentChildrenDepartmentCount, $departmentChildrenDepartmentAll] = self::GiveDepartmentReturnChildResult($corp_id, $departmentChildrenIds, $subDepartmentOld);
					$departmentChildrenCount = self::GiveDepartmentReturnUserArray($departmentChildrenIds, $corp_id, $TempU, true, $is_del, $is_external, $user_param["audit_user_ids"], false, true, $subUser);
					foreach ($departmentChildren as $key2 => $record) {
						$record["titleAll"] = '';
						$record["isLeaf"]   = $record["disabled"] = false;
						if (!empty($agentid) && !in_array($record["department_id"], $AgentDepartment)) {
							continue;
						}
						if (!empty($agentid) && !in_array($record["department_id"], $AgentDepartmentOld)) {
							$record["disabled"] = true;
						}
						if (!empty($sub_id) && $all) {
							/**子账户限定*/
							if (!in_array($record["department_id"], $subDepartment)) {
								$record["isLeaf"]   = true;
								$record["disabled"] = true;
							}
							if (!in_array($record["department_id"], $subDepartmentOld)) {
								$record["disabled"] = true;
							}
							if (in_array($record["department_id"], $subDepartmentOld)) {
								$record["isLeaf"] = false;
							}
						}
						$workUser       = 0;
						$departmentNext = 0;
						if (isset($departmentChildrenCount[$record["department_id"]])) {
							$workUser = $departmentChildrenCount[$record["department_id"]];
						}
						if (isset($departmentChildrenDepartmentAll[$record["department_id"]])) {
							$departmentNext = $departmentChildrenDepartmentAll[$record["department_id"]];
						}
						if ($workUser == 0 && count($departmentNext) <= 1 && $from_chat != 9) {
							$record["isLeaf"]   = true;
							$record["disabled"] = true;

						}
						if ($user_param["departmentDisabled"] == 1) {
							$record["disabled"] = true;
						}
						if (in_array($record["department_id"], $disabledPart) && !empty($disabledPart)) {
							$record["disabled"] = true;
						}
						$record["ids"]         = $record["id"];
						$record["id"]          = "d-" . $record["department_id"];
						$record["scopedSlots"] = ["title" => "title"];
						$list["children"][]    = $record;
					}
					if ($user_param["departmentDisabled"] == 1) {
						$list["disabled"] = true;
					}
				}
			}

			return array_values($departmentLists);
		}

		/**
		 * @param int|string $corp_id         企业id
		 * @param int        $department_id   部门id
		 * @param array      $AgentDepartment 应用可见范围部门
		 *                                    获取下面是否存在部门
		 *
		 * @return bool
		 */
		public static function GiveDepartmentReturnBool ($corp_id, $department_id, $AgentDepartment)
		{
			$departmentChildren = WorkDepartment::find()->where(["corp_id" => $corp_id, "parentid" => $department_id, "is_del" => 0]);
			if (!empty($AgentDepartment)) {
				$departmentChildren = $departmentChildren->andWhere(["in", "department_id", $AgentDepartment]);
			}

			return $departmentChildren->select("id,department_id as key,parentid,name as title,department_id")->exists();
		}

		public static function GiveDepartmentReturnChildResult ($corp_id, $department, $AgentDepartment)
		{
			if (!empty($AgentDepartment) && !in_array(1, $AgentDepartment)) {
				$department = array_intersect($department, $AgentDepartment);
			}
			$departmentChildrenAll = WorkDepartment::find()->where(["corp_id" => $corp_id, "is_del" => 0]);
			$result2               = $departmentChildrenAll->select("parentid,department_id")->orderBy("parentid asc")->asArray()->all();
			$Temp                  = [];
			$returnData1           = [];
			//返回部门下的所有子部门是一个二维数组，父部门作为键，子部门数组列表
			$returnData = [];
			if ($result2) {
				foreach ($department as $value) {
					$returnData[$value] = [$value];
				}
				foreach ($result2 as $kk => $vv) {
					if (!empty($vv['parentid']) && isset($returnData[$vv['parentid']])) {
						$returnData[$vv['parentid']][] = $vv["department_id"];
					}
					if (!empty($vv['parentid'])) {
						$Temp[$vv["department_id"]] = $vv['parentid'];
						$key                        = isset($Temp[$vv['parentid']]) ? $Temp[$vv['parentid']] : false;
						if ($key !== false && isset($Temp[$key]) && $key != 1) {
							$Temp[$vv["department_id"]] = $key;
						}
						if ($key !== false && isset($Temp[$key]) && isset($returnData[$key]) && in_array($key, $returnData[$key])) {
							$returnData[$key][] = $vv["department_id"];
						}
					}
				}
			}

			return [$returnData1, $returnData];
		}

		/**
		 * @param array      $userIdsData
		 * @param int|string $corp_id
		 * @param array      $newData
		 *
		 * @return array
		 * @throws \yii\db\Exception
		 */
		public static function getTopNextUserDepartment ($userIdsData, $corp_id, $newData = [])
		{
			$workUser   = WorkUser::find()->where(['and', ['corp_id' => $corp_id], ["in", "id", $userIdsData]])->select("department")->asArray()->all();
			$Department = [];
			foreach ($workUser as $item) {
				$A = explode(",", $item["department"]);
				array_push($Department, ...$A);
			}
			if (!empty($newData)) {
				$Department = self::ReturnUserDepartment($Department, $newData);
			} else {
				self::GiveDepartmentReturnChildren([], $corp_id, $newData);
				$Department = self::ReturnUserDepartment($Department, $newData);
			}

			return $Department;
//			foreach ($Department as $key => $value) {
//				if (!empty($value)) {
//					$temp = Yii::$app->db->createCommand("SELECT getParentList($value,$corp_id) as department;")->queryOne();
//					if (!empty($temp)) {
//						$temp = explode(",", $temp["department"]);
//						array_push($data, ...$temp);
//					}
//				} else {
//					unset($Department[$key]);
//				}
//			}
//			$Department = array_unique(array_merge($data, $Department));
//			if (in_array(1, $Department)) {
//				$key = array_search(1, $Department);
//				unset($Department[$key]);
//			}
//
//			return array_values($Department);
		}

		/**
		 * @param array $data 部门
		 *                    格式化部门
		 *
		 * @return array
		 */
		public static function getDepartmentData ($data)
		{
			$newData = [];
			foreach ($data as $v) {
				$newData[$v['department_id']] = $v;
			}
			$returnData = [];
			foreach ($newData as $kk => &$vv) {
				$temp_name = '';
				if (isset($newData[$vv['parentid']])) {
					$temp_name  = $newData[$vv['parentid']]['name'] . '--' . $vv['name'];
					$vv['name'] = $temp_name;

				}
				$temp         = [];
				$temp["name"] = $vv['name'];
				$temp["id"]   = $vv['department_id'];
				$returnData[] = $temp;
			}

			return $returnData;
		}

		/**
		 * @param array $user_id
		 * @param bool  $ids
		 *
		 * @return array
		 * @remark 分离部门和员工id
		 */
		public static function GiveUserIdsReturnDepartmentAndUserIds ($user_id, $ids = true)
		{
			$A = [];//部门
			$B = [];//成员
			if (is_array($user_id)) {
				foreach ($user_id as $value) {
					if (is_array($value)) {
						$TempValue = $value["id"];
					} else {
						$TempValue = $value;
					}
					if (strpos($TempValue, 'd') !== false) {
						$T = explode("-", $TempValue);
						if (isset($T[1])) {
							$A[] = $T[1];
						}
					} else {
						if ($ids) {
							if (is_array($value)) {
								$B[] = $value["id"];
							} else {
								$B[] = $value;
							}
						} else {
							$B[] = $value;
						}
					}
				}
			}

			return [
				"department" => $A,
				"user"       => $B
			];
		}

		/**
		 * @param array $user
		 *
		 * @return array
		 * @remark 删除部门只留员工数据
		 */
		public static function FormatDataUserArray ($user)
		{
			if (is_array($user)) {
				foreach ($user as $key => $value) {
					if (is_array($value)) {
						$value = $value["id"];
					}
					if (strpos($value, 'd') !== false) {
						unset($user[$key]);
					}
				}
			}

			return array_values($user);
		}

		/**
		 * @param       $departmentData
		 * @param array $parentDepartmentData
		 * @param array $allowDepartmentData
		 *
		 * @return bool
		 */
		public static function getAllowDepartmentData ($departmentData, $parentDepartmentData = [], &$allowDepartmentData = [])
		{
			if (!empty($departmentData) && !empty($parentDepartmentData)) {
				$newParentDepartmentData = [];
				foreach ($departmentData as $key => $department) {
					if (in_array($department['parentid'], $parentDepartmentData)) {
						if (!in_array($department['department_id'], $allowDepartmentData)) {
							array_push($newParentDepartmentData, $department['department_id']);
							array_push($allowDepartmentData, $department['department_id']);

							unset($departmentData[$key]);
						}
					}
				}
				self::getAllowDepartmentData($departmentData, $newParentDepartmentData, $allowDepartmentData);
			}

			return true;
		}

		/**
		 * @param array      $Department 部门
		 * @param int|string $corp_id    企业微信
		 * @param array      $newData    企业微信
		 *
		 * @remark 获取部门下的所有子部门
		 * @return array
		 */
		public static function GiveDepartmentReturnChildren ($Department, $corp_id, &$newData = [])
		{

			if (empty($newData)) {
				$TempDepartment = self::find()->where(["corp_id" => $corp_id, "is_del" => 0])->select("department_id,parentid")->orderBy("parentid asc")->asArray()->all();
				foreach ($TempDepartment as $v) {
					$newData[$v['department_id']] = $v;
				}
			}
			if (!empty($Department)) {
				if (is_array($Department)) {
					$returnData = $Department;
				} else {
					$returnData = [$Department];
				}
				if (in_array(1, $Department)) {
					return array_keys($newData);
				}

				self::getAllowDepartmentData($newData, $returnData, $returnData);

				return $returnData;
			}

			return [];
		}

		/**
		 * Title: ReturnUserDepartment
		 * User: sym
		 * Date: 2020/12/19
		 * Time: 15:19
		 *
		 * @param $userDepart
		 * @param $newData
		 *
		 * @return mixed
		 * @remark 返回所有员工的上级部门
		 */
		public static function ReturnUserDepartment ($userDepart, $newData)
		{
			foreach ($newData as $kk => $vv) {
				if (isset($newData[$vv['department_id']]) && in_array($vv['department_id'], $userDepart)) {
					$userDepart[] = $vv["department_id"];
				}
			}
			$userDepart = array_unique($userDepart);
//			if (in_array(1, $userDepart)) {
//				sort($userDepart);
//				unset($userDepart[0]);
//			}

			return $userDepart;
		}

		/**
		 * @param int   $corp_id     企业微信id
		 * @param array $department  部门id
		 * @param array $user_id     已有成员id
		 * @param int   $is_external 是否具有外部联系人权限
		 * @param bool  $giveArray   返回时id数组还是数组
		 * @param int   $is_del      是否删除
		 * @param array $audit       开启回话存档人员
		 * @param int   $sub         子账户
		 * @param int   $agentid     应用
		 * @param bool  $sub_list    子账户列表
		 */
		public static function GiveDepartmentReturnUserData ($corp_id, $department = [], $user_id = [], $is_external = 0, $giveArray = false, $is_del = 0, $audit = [], $sub = 0, $agentid = 0, $sub_list = false)
		{
			$AgentDepartmentOld = $AgentDepartment = $AgentUserIds = $subUser = $subDepartment = [];

			if (!empty($agentid)) {
				[$AgentDepartment, $AgentUserIds, $AgentDepartmentOld] = self::GiveAgentIdReturnDepartmentOrUser($corp_id, $agentid, $is_del, $is_external);
			}
			if (!empty($sub)) {
				[$subUser, $subDepartment, $condition] = self::GiveSubIdReturnDepartmentOrUser($corp_id, $sub, $is_del, $is_external);
				if (count($subDepartment) == 1 && in_array(0, $subDepartment)) {
					$subDepartment = [];
				}
				if ($condition === false) {
					$subDepartment = [1];
				}
				if (empty($department) && empty($user_id) && $condition) {
					$user_id = $subUser;
				}
				if (!empty($user_id) && $condition) {
					$user_id = array_intersect($subUser, $user_id);;
				}
				if (in_array(1, $department)) {
					$department = $subDepartment;
					$user_id    = $subUser;
				} else {
					if (!empty($department) && $condition) {
						$department = array_intersect($department, $subDepartment);
					}
				}
				if ($sub_list) {
					$department = $subDepartment;
					$user_id    = $subUser;
				}
				/** 范围限定包含应用范围限定**/
				if (!empty($agentid)) {
					$department = array_intersect($department, $AgentDepartmentOld);
					$user_id    = array_intersect($user_id, $AgentUserIds);
				}
			} else {
				/** 应用范围限定**/
				if (!empty($agentid)) {
					if (!empty($department)) {
						$department = array_intersect($department, $AgentDepartmentOld);
					} else {
						$department = $AgentDepartmentOld;
					}
					if (!empty($department)) {
						$user_id = array_intersect($user_id, $AgentUserIds);
					} else {
						$user_id = $AgentUserIds;
					}
				}
			}

			if (!empty($department)) {
				if (!in_array(1, $department)) {
					$department = self::GiveDepartmentReturnChildren($department, $corp_id);
				}

				if (!empty($department)) {
					$department = array_unique($department);
					if (in_array(1, $department)) {
						self::GiveDepartmentReturnUserArray($department, $corp_id, $user_id, $giveArray, $is_del, $is_external, $audit, true, false, $subUser);
					} else {
						self::GiveDepartmentReturnUserArray($department, $corp_id, $user_id, $giveArray, $is_del, $is_external, $audit, false, false, $subUser);
					}
				}
			}
			if ($giveArray) {
				return array_unique($user_id);
			}

			return $user_id;

		}

		/**
		 * @param array      $department      部门
		 * @param int|string $corp_id         企业id
		 * @param array      $user_id         员工数据
		 * @param bool       $giveArray       是否返回id数组
		 * @param int        $is_del          是否返回id数组
		 * @param int        $is_external     是否返回id数组
		 * @param array      $audit           回话存档
		 * @param bool       $parent          是否是根部门
		 * @param bool       $departmentCount 所有员工数据
		 * @param array      $subUser         范围
		 *
		 * @remark  获取对应是的员工id或员工数组
		 */
		public static function GiveDepartmentReturnUserArray ($department, $corp_id, &$user_id, $giveArray, $is_del, $is_external = 0, $audit = [], $parent = false, $departmentCount = false, $subUser = [])
		{
			$workUser = WorkUser::find()->where(["corp_id" => $corp_id])
				->andWhere(["corp_id" => $corp_id]);
			if ($is_del == 1) {
				$workUser = $workUser->andWhere(["is_del" => $is_del]);
			} else {
				$workUser = $workUser->andWhere(['status' => 1]);
				$workUser = $workUser->andWhere(['is_del' => 0]);
			}
			if (!empty($is_external)) {
				$workUser = $workUser->andWhere(["is_external" => $is_external]);
			}
			if (!empty($audit)) {
				$workUser = $workUser->andWhere(['id' => $audit]);
			}
			$workUser = $workUser->select("id,name as title,department as key")
				->asArray()->all();
			if ($parent) {
				if ($giveArray) {
					$workUser = array_column($workUser, "id");
					$user_id  = array_unique(array_merge($workUser, $user_id));
				} else {
					foreach ($workUser as $record) {
						$TempArray = array_column($user_id, "id");
						if (!empty($record["key"]) && !in_array($record["id"], $TempArray)) {
							$TempDepartment        = explode(",", $record["key"]);
							$record["key"]         = $TempDepartment[0] . "-" . $record["id"];
							$record['scopedSlots'] = ['title' => 'custom'];
							$user_id[]             = $record;
						}
					}
				}

			} else {
				$TempDepartmentUserCount = [];
				foreach ($workUser as $record) {
					if (!empty($subUser) && !in_array($record["id"], $subUser)) {
						continue;
					}
					if (!empty($record["key"])) {
						$A = explode(",", $record["key"]);
						if ($departmentCount) {
							if (count($A) > 1) {
								foreach ($A as $B) {
									if (isset($TempDepartmentUserCount[$B])) {
										++$TempDepartmentUserCount[$B];
									} else {
										$TempDepartmentUserCount[$B] = 1;
									}
								}
							} else {
								if (isset($TempDepartmentUserCount[$A[0]])) {
									++$TempDepartmentUserCount[$A[0]];
								} else {
									$TempDepartmentUserCount[$A[0]] = 1;
								}
							}
						} else {
							/**查看员工是否在部门中*/
							if (!empty(array_intersect($A, $department))) {
								if ($giveArray) {
									$user_id[] = $record["id"];
								} else {
									$TempArray = array_column($user_id, "id");
									if (!in_array($record["id"], $TempArray)) {
										$TempDepartment        = explode(",", $record["key"]);
										$record["key"]         = $TempDepartment[0] . "-" . $record["id"];
										$record['scopedSlots'] = ['title' => 'custom'];
										$user_id[]             = $record;
									}
								}
							}
						}
					}
				}

				return $TempDepartmentUserCount;
			}
		}

		/**
		 * @param array|string $userData 选择成员数据
		 */
		public static function GiveUserDataReturnPart ($userData)
		{
			$party = [];
			if (!is_array($userData)) {
				$userData = json_decode($userData, true);
			}
			foreach ($userData as $value) {
				if (strpos($value["id"], 'd') !== false) {
					$party[] = $value["key"];
				}
			}

			return $party;
		}

	}
