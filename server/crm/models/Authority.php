<?php

	namespace app\models;

	use Yii;
	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;

	/**
	 * This is the model class for table "{{%authority}}".
	 *
	 * @property int    $id
	 * @property int    $level       权限等级
	 * @property int    $pid         父级id
	 * @property string $name        权限名称
	 * @property string $route       权限相关路由
	 * @property string $description 权限简介
	 * @property int    $status      状态0未删除1已删除
	 * @property int    $sort        排序
	 * @property string $create_time 创建时间
	 * @property string $update_time 更新时间
	 */
	class Authority extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%authority}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['level', 'status', 'pid'], 'integer'],
				[['create_time', 'update_time'], 'safe'],
				[['name', 'route'], 'string', 'max' => 50],
				[['description'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'level'       => '权限等级',
				'pid'         => '父级id',
				'name'        => '权限名称',
				'route'       => '权限相关路由',
				'description' => '权限简介',
				'status'      => '状态0未删除1已删除',
				'create_time' => '创建时间',
				'update_time' => '更新时间',
			];
		}

		public static function getCommon ()
		{
			return ['subAccountDad', 'filingCabinet', 'sms', 'integration', 'customerAttribute'];
		}

		public static function getSubCommon ()
		{
			$ids    = [];
			$common = static::getCommon();
			foreach ($common as $comm) {
				$auto = Authority::findOne(['route' => $comm, 'status' => 0]);
				if (!empty($auto)) {
					$sub = Authority::findOne(['pid' => $auto->id, 'status' => 0]);
					if (!empty($sub)) {
						array_push($ids, $auto->id);
					}
				}
			}
			$common = array_merge(['account-wx', 'account-mini', 'work-wechat'],$common);
			$id  = static::getAuto($common);
			$ids = array_merge($ids, $id);

			return array_unique($ids);
		}

		public static function getAuto ($route)
		{
			$id        = [];
			$subIdsAll = [];
			$autoAll   = Authority::find()->where(['status' => 0])->andWhere(["in", "route", $route])->all();
			if (!empty($autoAll)) {
				foreach ($autoAll as $auto) {
					$sub = Authority::find()->where(['pid' => $auto->id, 'status' => 0])->asArray()->all();
					if (!empty($sub)) {
						$subIds    = array_column($sub, "id");
						$subIdsAll = array_merge($subIds, $subIdsAll);
						$subUser   = Authority::find()->where(["in", 'pid', $subIds, 'status' => 0])->asArray()->all();
						if (!empty($subUser)) {
							$subUserIds = array_column($subUser, "id");
							array_push($id, ...$subUserIds);
						}
					}
				}
				static::getSubAuto($subIdsAll, $id);
			}

			return array_unique($id);
//			$id   = [];
//			$auto = Authority::findOne(['route' => $route, 'status' => 0]);
//			if (!empty($auto)) {
//				$sub = Authority::find()->where(['pid' => $auto->id, 'status' => 0])->asArray()->all();
//				if (!empty($sub)) {
//					$subIds  = array_column($sub, "id");
//					$subUser = Authority::find()->where(["in", 'pid', $subIds, 'status' => 0])->asArray()->all();
//					if (!empty($subUser)) {
//						$subUserIds = array_column($subUser, "id");
//						array_push($id, ...$subUserIds);
//					}
//					static::getSubAuto($subIds, $id);
//				}
//			}
		}

		/**
		 * @param $parentId
		 * @param $id
		 *
		 */
		public static function getSubAuto ($parentId, &$id)
		{
			$result2    = Authority::find()->where(["status"=>0])->asArray()->orderBy("pid asc")->all();
			$id = $parentId;
			foreach ($result2 as $kk => $vv) {
				if (!empty($vv['pid']) && in_array($vv['pid'],$id)) {
					array_push($id, $vv["id"]);
				}
			}

//			return $returnData;
//			$subUser = Authority::findOne(['pid' => $parentId, 'status' => 0]);
//
//			if (!empty($subUser)) {
//				array_push($id, $parentId);
//				static::getSubAuto($subUser->id, $id);
//			}
		}

		public static function setAuthority ($data)
		{
			$LookId      = trim($data['LookId']);
			$name        = trim($data['name']);
			$level       = trim($data['level']);
			$route       = trim($data['route']);
			$sort        = trim($data['sort']);
			$description = trim($data['description']);
			$AuthorityId = isset($data['AuthorityId']) ? trim($data['AuthorityId']) : 0;

			if (!$name || !$level || !$route) {
				throw new InvalidDataException('数据填写不全');
			}
			$actionInfo = static::find()->where(['route' => $route, 'status' => 0]);
			if (!empty($AuthorityId)) {
				$actionInfo = $actionInfo->andWhere(['<>', 'id', $AuthorityId]);
			}
			$actionInfo = $actionInfo->one();
			if (!empty($actionInfo)) {
				throw new InvalidDataException('权限路由已经存在');
			}
			if (!empty($AuthorityId)) {
				$model              = static::findOne($AuthorityId);
				$model->update_time = DateUtil::getCurrentTime();
			} else {
				$model              = new Authority();
				$model->create_time = DateUtil::getCurrentTime();
				if (empty($LookId)) {
					$model->pid = 0;
				} else {
					$model->pid = $LookId;
				}
			}
			$model->name        = $name;
			$model->level       = $level;
			$model->route       = $route;
			$model->description = $description;
			$model->sort        = !empty($sort) ? $sort : 0;
			if (!$model->save()) {
				throw new InvalidDataException(SUtils::modelError($model));
			}

			return ['error' => 0, 'msg' => ''];
		}

		/**
		 * @param $id
		 * @param $ids
		 * @param $disabled
		 * @param $menuAuthority
		 * @param $route
		 * @param $flag
		 * @param $menuAuthorityNew
		 * @param array $newData 组合完成后数组
		 * @param bool $recursion 是否递归
		 * @param $AuthIds
		 * @param $route1
		 *
		 * @return array
		 *
		 */
		public static function getSubAuthority ($id, $ids = [], $disabled = 0, $menuAuthority, $route = '', $flag = 0, $menuAuthorityNew, $newData = [], $recursion = false, $AuthIds = [], $route1 = '')
		{
			$flag++;
			$result          = [];
			$i               = 0;
			$commonAuthority = static::getCommon();
			if (!empty($id)) {
				if ($recursion) {
					if (isset($newData[$id])) {
						$subAuthority = $newData[$id];
					} else {
						$subAuthority = [];
					}
				} else {
					$subAuthority    = static::find()->andWhere(['status' => 0, 'pid' => $id])->orderBy(['sort' => SORT_ASC])->all();
					$subAuthorityAll = static::find()->andWhere(['status' => 0])->orderBy(['sort' => SORT_ASC])->all();
					foreach ($subAuthorityAll as $record) {
						if (isset($newData[$record["pid"]])) {
							$newData[$record["pid"]][] = $record;
						} else {
							$newData[$record["pid"]][] = $record;
						}
					}
				}

				if (!empty($subAuthority)) {
					/**
					 * @var key       $key
					 * @var Authority $auto
					 */
					foreach ($subAuthority as $key => $auto) {
						if (!empty($ids) && !in_array($auto->id, $ids)) {
							continue;
						}
						$menu     = [];
						$subRoute = '';
						if ($route == 'wx-account' || $route == 'account-wx') {
							$menu     = $menuAuthority;
							$subRoute = $auto->route;
						}
						if ($route == 'work-wechat' && !empty($menuAuthority) && !in_array($auto->route, $menuAuthority)) {
							continue;
						}
						if (($route == 'work-wechat' || $route1 == 'work-wechat') && !empty($AuthIds) && !in_array($auto->id, $AuthIds)) {
							continue;
						}
						if (($route == 'account-wx' || $route == 'account-mini') && !empty($menuAuthority) && !in_array($auto->route, $menuAuthority)) {
							continue;
						}
						if ($auto->level == 3 && !empty($menuAuthorityNew) && !in_array($auto->route, $menuAuthorityNew)) {
							$isTrue = true;
							$auth1  = Authority::findOne(['id' => $auto->pid, 'status' => 0]);
							if (!empty($auth1)) {
								$auth2             = Authority::findOne(['id' => $auth1->pid, 'status' => 0]);
								$commonAuthority[] = 'work-wechat';
								$commonAuthority[] = 'account-wx';
								if (in_array($auth2->route, $commonAuthority)) {
									$isTrue = false;
								}
							}
							if ($isTrue) {
								continue;
							}
						}

						if (!in_array($auto->route, $menuAuthorityNew) && in_array($route, $commonAuthority) && $route != 'subAccount' && $route != 'integration') {
							continue;
						}
						$children               = static::getSubAuthority($auto->id, $ids, $disabled, $menu, $subRoute, $flag, $menuAuthority, $newData, true, $AuthIds, $route);
						$result[$i]['key']      = $auto->id;
						$result[$i]['id']       = $auto->id;
						$result[$i]['title']    = $auto->name;
						$result[$i]['route']    = $auto->route;
						$result[$i]['children'] = $children;
						if ($disabled == 1) {
							$result[$i]['disabled'] = true;
						}
						$i++;
					}
				}
			}

			return $result;

		}

		/**
		 * @param $sub_id
		 * @param $wx_id
		 * @param $type 0公众号1企业微信
		 *
		 * @return array
		 *
		 */
		public static function getAuthority ($sub_id, $wx_id, $type)
		{
			$routes = [];
			if (!empty($sub_id)) {
				if (empty($type)) {
					$type = 1;
				} elseif ($type == 1) {
					$type = 2;
				}
				if (empty($wx_id)) {
					$type = 3;
				}
				$subUser = SubUserAuthority::findOne(['sub_user_id' => $sub_id, 'is_mini' => 1]);
				if (!empty($subUser) && $type == 3) {
					$type = 1;
				}
				$sub_auto = SubUserAuthority::find()->andWhere(['sub_user_id' => $sub_id, 'type' => [$type, 3]])->andWhere(['or', ['in', 'wx_id', [$wx_id, 0]], ['is_mini' => 1]]);
				$sub_auto = $sub_auto->all();
				if (!empty($sub_auto)) {
					foreach ($sub_auto as $auto) {
						if (!empty($auto->authority_ids)) {
							$authority_ids = explode(',', $auto->authority_ids);
							$authority_ids = SubUserAuthority::getNowAuthority($authority_ids);
							$author        = Authority::find()->andWhere(['in', 'id', $authority_ids])->andWhere(['status' => 0])->asArray()->all();
							$routes_list   = array_column($author, 'route');
							if (!empty($routes_list)) {
								foreach ($routes_list as $ro) {
									array_push($routes, $ro);
								}
							}
						}
					}
				}
			}
			return $routes;
		}

	}
