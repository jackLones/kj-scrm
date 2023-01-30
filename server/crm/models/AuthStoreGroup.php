<?php

	namespace app\models;

	use Yii;
	use yii\db\Exception;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%auth_store_group}}".
	 *
	 * @property int         $id
	 * @property int         $pid         上级分组id
	 * @property int         $uid         账户id
	 * @property int         $corp_id     企业微信id
	 * @property string      $name        分组名称
	 * @property int         $status      状态
	 * @property int         $sort        排序
	 * @property string      $parent_ids  上级分组
	 * @property string      $create_time 创建时间
	 * @property string      $update_time 修改时间
	 *
	 * @property AuthStore[] $authStores
	 * @property WorkCorp    $corp
	 */
	class AuthStoreGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%auth_store_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['pid', 'uid', 'corp_id', 'status', 'sort'], 'integer'],
				[['parent_ids'], 'string'],
				[['create_time', 'update_time'], 'safe'],
				[['name'], 'string', 'max' => 60],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'pid'         => Yii::t('app', '上级分组id'),
				'uid'         => Yii::t('app', '账户id'),
				'corp_id'     => Yii::t('app', '企业微信id'),
				'name'        => Yii::t('app', '分组名称'),
				'status'      => Yii::t('app', '状态'),
				'sort'        => Yii::t('app', '排序'),
				'parent_ids'  => Yii::t('app', '上级分组'),
				'create_time' => Yii::t('app', '创建时间'),
				'update_time' => Yii::t('app', '修改时间'),
			];
		}

		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthStores ()
		{
			return $this->hasMany(AuthStore::className(), ['group_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/***
		 * Title: CreatNoGroup
		 * User: sym
		 * Date: 2021/2/1
		 * Time: 11:07
		 *
		 * @param $uid
		 * @param $corp_id
		 *
		 * @return AuthStoreGroup|array|\yii\db\ActiveRecord|null
		 * @remark 初始化未分组
		 */
		public static function CreatNoGroup ($uid, $corp_id)
		{
			$exists = self::find()->where(["corp_id" => $corp_id, "status" => 1, "name" => "未分组"])->one();
			if (empty($exists)) {
				if (!Yii::$app->cache->get("corp_id" . $corp_id)) {
					Yii::$app->cache->set("corp_id" . $corp_id, "1", 5);
					$group          = new self();
					$group->name    = "未分组";
					$group->pid     = 0;
					$group->corp_id = $corp_id;
					$group->uid     = $uid;
					$group->sort    = 1;
					$group->status  = 1;
					$group->save();
					self::updateAll(["parent_ids" => $group->id], ["id" => $group->id]);

					return $group;
				}
			}

			return $exists;
		}

		/**
		 * Title: FormattingData
		 * User: sym
		 * Date: 2021/1/18
		 * Time: 17:40
		 *
		 * @param $uid
		 * @param $corp_id
		 * @param $parent_id
		 * @param $store
		 * @param $choose
		 * @param $StoreStatus
		 *
		 * @return array
		 * @remark 获取格式化的门店数据
		 */
		public static function FormattingData ($uid, $corp_id, $parent_id = 0, $store = 0, $choose = 0, $StoreStatus = 0)
		{

//			self::CreatNoGroup($uid, $corp_id);
			$count     = 0;
			$parent_id = empty($parent_id) ? 0 : $parent_id;
			/**获取$parent_id下的分组**/
			$GroupLists = self::find()->where(["uid" => $uid, "corp_id" => $corp_id, "pid" => $parent_id, "status" => 1])
				->select("name as title,id,id as key,pid,parent_ids")->orderBy(["sort" => SORT_ASC])->asArray()->all();
			/**获取所有门店**/
			$StoreLists   = AuthStore::find()->where(["uid" => $uid, "corp_id" => $corp_id, "is_del" => 0])->select("status,shop_name as title,id,id as key,group_id,id")->asArray()->all();
			$StoreUserAll = $StoreNewData = [];
			if (!empty($store)) {
				$select         = new Expression("count(*) as cc,a.store_id");
				$StoreListsUser = AuthStoreUser::find()->alias("a")
					->leftJoin("{{%auth_store}} as b", "a.store_id = b.id")
					->where(["b.corp_id" => $corp_id, "a.status" => 1])
					->select($select)->groupBy(["a.store_id"])->asArray()->all();
				$StoreUserAll   = array_column($StoreListsUser, "cc", "store_id");
			}
			$GroupListsT     = array_column($GroupLists, "id");
			$GroupListsTemp2 = self::GivePidReturnId($uid, $corp_id, $GroupListsT);
			$returnData      = [];
			foreach ($StoreLists as $storeList) {
				$storeList["scopedSlots"] = ["title" => "custom"];
				$storeList["isLeaf"]      = true;
				$storeList["store"]       = true;
				if (!empty($choose)) {
					$storeList["disabled"] = false;
				}
				if (!empty($StoreStatus) && $storeList["status"] == 0) {
					$storeList["disabled"] = true;
					$storeList["title"]    .= "(门店已关闭)";
				}
				$storeList["count"] = 0;
				if (isset($StoreUserAll[$storeList["id"]])) {
					$storeList["count"] = (int) $StoreUserAll[$storeList["id"]];
				}
				$storeList["id"]                        = $storeList["key"] = $storeList["id"] . "-s";
				$StoreNewData[$storeList["group_id"]][] = $storeList;
			}
			if (isset($StoreNewData[$parent_id]) && !empty($store)) {
				$returnData = $StoreNewData[$parent_id];
			}
			foreach ($GroupLists as $k => $groupList) {
				$groupList["count"]       = 0;
				$groupList["store"]       = false;
				$groupList["pid_all"]     = !empty($groupList["parent_ids"]) ? explode(",", $groupList["parent_ids"]) : [];
				$groupList["scopedSlots"] = ["title" => "custom"];
				/**查询当点分组下所有的子分组**/
				if (isset($GroupListsTemp2[$groupList["id"]])) {
					foreach ($GroupListsTemp2[$groupList["id"]] as $cc) {
						if (isset($StoreNewData[$cc])) {
							if (!empty($store)) {
								if($store == 1){
									$sum                = array_sum(array_column($StoreNewData[$cc], "count"));
								}else{
									$sum                = (int) count($StoreNewData[$cc]);
								}
								$groupList["count"] += $sum;
							} else {
								$groupList["count"] += (int) count($StoreNewData[$cc]);
							}
						}
					}

				}
				if (!empty($choose)) {
					$groupList["disabled"] = true;
				}
				$is_leaf             = isset($GroupListsTemp2[$groupList["id"]]) ? $GroupListsTemp2[$groupList["id"]] : [];
				$groupList["isLeaf"] = (count($is_leaf) > 1) ? false : true;
				if (isset($StoreNewData[$groupList["id"]]) && !empty($store)) {
					$groupList["isLeaf"] = false;
				}
				$count        += $groupList["count"];
				$returnData[] = $groupList;
			}

			return [
				"data"  => $returnData,
				"count" => $count
			];
		}

		/***
		 * Title: GivePidReturnId
		 * User: sym
		 * Date: 2021/2/1
		 * Time: 11:06
		 *
		 * @param $uid
		 * @param $corp_id
		 * @param $GroupLists
		 *
		 * @return array
		 * @remark 批量获取下级
		 */
		public static function GivePidReturnId ($uid, $corp_id, $GroupLists)
		{
			$GroupListsTemp2 = [];
			/**分组键值翻转**/
			foreach ($GroupLists as $vv) {
				$GroupListsTemp2[$vv] = [$vv];
			}
			/**获取所有的分组**/
			$GroupListALL = self::find()->where(["uid" => $uid, "corp_id" => $corp_id, "status" => 1])->orderBy(["pid" => SORT_DESC])->asArray()->all();
			/**当前分组id作key子分组作值（array）**/
			$Temp  = array_column($GroupListALL, "pid", "id");
			$Temp2 = [];
			foreach ($GroupListALL as $vv) {
				$Temp2[$vv["pid"]][] = $vv["id"];
			}
			foreach ($GroupListALL as $vv) {
				if (!empty($vv['pid'])) {
					$keyT    = $vv['id'];
					$tempIds = [$vv['id']];
					while ($keyT) {
						$tempIds[] = $keyT;
						if (isset($Temp2[$keyT])) {
							$tempIds = array_merge($tempIds, $Temp2[$keyT]);
						}
						if (isset($Temp[$keyT])) {
							if ($Temp[$keyT] == 0) {
								$keyT = false;
							} else {
								$keyT = $Temp[$keyT];
								if (isset($GroupListsTemp2[$keyT])) {
									$GroupListsTemp2[$keyT] = array_merge($GroupListsTemp2[$keyT], $tempIds);
									$GroupListsTemp2[$keyT] = array_values(array_unique($GroupListsTemp2[$keyT]));
									$keyT                   = false;
								}
							}
						} else {
							$keyT = false;
						}
					}
				}
			}

			return $GroupListsTemp2;
		}

		/***
		 * Title: GiveIdReturnParentId
		 * User: sym
		 * Date: 2021/2/1
		 * Time: 11:07
		 *
		 * @param       $uid
		 * @param       $corp_id
		 * @param       $GroupLists
		 * @param array $GroupListALLTemp
		 *
		 * @return array
		 * @remark 批量获取上级
		 */
		public static function GiveIdReturnParentId ($uid, $corp_id, $GroupLists, &$GroupListALLTemp = [])
		{
			$GroupListsTemp = [];
			if (empty($GroupListALLTemp)) {
				/**获取所有的分组**/
				$GroupListALLTemp = self::find()->where(["uid" => $uid, "corp_id" => $corp_id, "status" => 1])->orderBy(["pid" => SORT_DESC])->asArray()->all();
			}
			$temp = array_column($GroupListALLTemp, "pid", "id");
			foreach ($GroupLists as $vv) {
				$GroupListsTemp[$vv] = [$vv];
				$kvv                 = $vv;
				/**当前id查询上级分组作值（array）**/
				while ($kvv) {
					if (isset($temp[$kvv])) {
						if ($temp[$kvv] == 0) {
							$kvv = false;
						} else {
							$GroupListsTemp[$vv][] = $temp[$kvv];
							$kvv                   = $temp[$kvv];
						}
					} else {
						$kvv = false;
					}
				}
			}

			return $GroupListsTemp;
		}

	}
