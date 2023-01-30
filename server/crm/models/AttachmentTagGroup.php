<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%attachment_tag_group}}".
 *
 * @property int $id
 * @property int $pid 上级分组id
 * @property int $uid 账户id
 * @property int $corp_id 企业微信id
 * @property string $name 分组名称
 * @property int $status 状态
 * @property int $sort 排序
 * @property string $parent_ids 上级分组
 * @property string $create_time 创建时间
 * @property string $update_time 修改时间
 *
 * @property WorkCorp $corp
 */
class AttachmentTagGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attachment_tag_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', '上级分组id'),
            'uid' => Yii::t('app', '账户id'),
            'corp_id' => Yii::t('app', '企业微信id'),
            'name' => Yii::t('app', '分组名称'),
            'status' => Yii::t('app', '状态'),
            'sort' => Yii::t('app', '排序'),
            'parent_ids' => Yii::t('app', '上级分组'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

	public static function getDb ()
	{
		return Yii::$app->get('mdb');
	}

	/***
	 * Title: CreatNoGroup
	 *
	 * @param $uid
	 *
	 * @return array|\yii\db\ActiveRecord|null
	 * @remark 初始化未分组
	 */
	public static function CreatNoGroup ($uid)
	{
		$exists = self::find()->where(["uid" => $uid, "status" => 1, "name" => "未分组"])->one();
		if (empty($exists)) {
			if (!Yii::$app->cache->get("uid" . $uid)) {
				Yii::$app->cache->set("uid" . $uid, "1", 5);
				$group          = new self();
				$group->name    = "未分组";
				$group->pid     = 0;
				$group->corp_id = null;
				$group->uid     = $uid;
				$group->sort    = 1;
				$group->status  = 1;
				$group->save();
				$group->parent_ids = $group->id;
				$group->save();

				return $group;
			}
		}

		return $exists;
	}

	/**
	 * Title: FormattingData
	 *
	 * @param $uid
	 * @param $corp_id
	 * @param $parent_id
	 * @param $tag
	 * @param $choose
	 *
	 * @return array
	 * @remark 获取格式化的内容标签组数据
	 */
	public static function FormattingData ($uid, $parent_id = 0, $tag = 0, $choose = 0)
	{
		self::CreatNoGroup($uid);
		$count     = 0;
		$parent_id = empty($parent_id) ? 0 : $parent_id;
		/**获取$parent_id下的分组**/
		$GroupLists = self::find()->where(["uid" => $uid, "pid" => $parent_id, "status" => 1])
			->select("name as title,id,id as key,pid,parent_ids")->orderBy(["sort" => SORT_ASC])->asArray()->all();
		/**获取所有内容标签**/
		$StoreLists = WorkTag::find()->where(["corp_id" => $uid, "is_del" => 0, 'type' => 3])->select("id,id as key,group_id,tagname as title")->asArray()->all();

		$StoreUserAll = $StoreNewData = [];
		/*if (!empty($tag)) {
			$select         = new Expression("count(*) as cc,a.tag_id");
			$StoreListsUser = WorkTagAttachment::find()->alias("a")
				->leftJoin("{{%work_tag}} as b", "a.tag_id = b.id")
				->where(["a.corp_id" => $corp_id, "a.status" => 1])
				->select($select)->groupBy(["a.tag_id"])->asArray()->all();
			$StoreUserAll   = array_column($StoreListsUser, "cc", "tag_id");
		}*/

		$GroupListsT     = array_column($GroupLists, "id");
		$GroupListsTemp2 = self::GivePidReturnId($uid, $GroupListsT);
		$returnData      = [];
		foreach ($StoreLists as $storeList) {
			$storeList["scopedSlots"] = ["title" => "custom"];
			$storeList["isLeaf"]      = true;
			$storeList["tag"]         = true;
			if (!empty($choose)) {
				$storeList["disabled"] = false;
			}
			/*$storeList["count"] = 0;
			if (isset($StoreUserAll[$storeList["id"]])) {
				$storeList["count"] = (int)$StoreUserAll[$storeList["id"]];
			}*/
			$storeList["id"]                        = $storeList["key"] = $storeList["id"] . "-s";
			$StoreNewData[$storeList["group_id"]][] = $storeList;
		}
		if (isset($StoreNewData[$parent_id]) && !empty($tag)) {
			$returnData = $StoreNewData[$parent_id];
		}
		foreach ($GroupLists as $k => $groupList) {
			$groupList["count"]       = 0;
			$groupList["tag"]         = false;
			$groupList["pid_all"]     = !empty($groupList["parent_ids"]) ? explode(",", $groupList["parent_ids"]) : [];
			$groupList["scopedSlots"] = ["title" => "custom"];
			/**查询当点分组下所有的子分组**/
			if (isset($GroupListsTemp2[$groupList["id"]])) {
				foreach ($GroupListsTemp2[$groupList["id"]] as $cc) {
					if (isset($StoreNewData[$cc])) {
						/*if (!empty($tag)) {
							$sum                = array_sum(array_column($StoreNewData[$cc], "count"));
							$groupList["count"] += $sum;
						} else {
							$groupList["count"] +=  (int)count($StoreNewData[$cc]);
						}*/
						$groupList["count"] +=  (int)count($StoreNewData[$cc]);
					}
				}
			}
			if (!empty($choose)) {
				$groupList["disabled"] = true;
			}
			$is_leaf             = isset($GroupListsTemp2[$groupList["id"]]) ? $GroupListsTemp2[$groupList["id"]] : [];
			$groupList["isLeaf"] = (count($is_leaf) > 1) ? false : true;
			if (isset($StoreNewData[$groupList["id"]]) && !empty($tag)) {
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
	 * @param $GroupLists
	 *
	 * @return array
	 * @remark 批量获取下级
	 */
	public static function GivePidReturnId ($uid, $GroupLists)
	{
		$GroupListsTemp2 = [];
		/**分组键值翻转**/
		foreach ($GroupLists as $vv) {
			$GroupListsTemp2[$vv] = [$vv];
		}
		/**获取所有的分组**/
		$GroupListALL = self::find()->where(["uid" => $uid, "status" => 1])->orderBy(["pid" => SORT_DESC])->asArray()->all();
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
	 *
	 * @param       $uid
	 * @param       $GroupLists
	 * @param array $GroupListALLTemp
	 *
	 * @return array
	 * @remark 批量获取上级
	 */
	public static function GiveIdReturnParentId ($uid, $GroupLists, &$GroupListALLTemp = [])
	{
		$GroupListsTemp = [];
		if (empty($GroupListALLTemp)) {
			/**获取所有的分组**/
			$GroupListALLTemp = self::find()->where(["uid" => $uid, "status" => 1])->orderBy(["pid" => SORT_DESC])->asArray()->all();
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

	/**
	 * 内容标签及内容分组标签
	 *
	 * @param array $tagIds
	 * @param int   $uid
	 *
	 * @return array
	 */
	public static function getTagsAndGroupTags ($tagIds, $uid = 0)
	{
		$tag_ids = [];//内容标签id
		$tagIds  = !empty($tagIds) && !is_array($tagIds) ? explode(',', $tagIds) : $tagIds;
		if (is_array($tagIds)) {
			$tagGroupIds = [];
			foreach ($tagIds as $value) {
				if (strpos($value, '-s') !== false) {
					$T = explode("-", $value);
					if (isset($T[0])) {
						$tag_ids[] = $T[0];
					}
				} else {
					$tagGroupIds[] = $value;
				}
			}
			//标签组
			if ($tagGroupIds) {
				$childGroupIds = self::GivePidReturnId($uid, $tagGroupIds);
				$groupIds      = [];
				foreach ($childGroupIds as $groupId) {
					$groupIds = array_merge($groupIds, $groupId);
				}
				$workTag     = WorkTag::find()->where(['type' => 3, 'is_del' => 0, 'group_id' => $groupIds])->select('id')->asArray()->all();
				foreach ($workTag as $v) {
					$tag_ids[] = $v['id'];
				}
			}

			$tag_ids = array_unique($tag_ids);
		}
		$tag_ids = !empty($tag_ids) ? $tag_ids : 0;

		return $tag_ids;
	}

	/**
	 * 更新内容标签，根据corp_id转为uid
	 */
	public static function uptAttachmentTag ()
	{
		try {
			$tagAttachment = WorkTag::find()->where(['type' => 3, 'is_del' => 0])->groupBy('corp_id')->all();

			foreach ($tagAttachment as $v) {
				$userInfo = UserCorpRelation::findOne(['corp_id' => $v->corp_id]);

				WorkTag::updateAll(['corp_id' => $userInfo->uid], ['corp_id' => $v->corp_id, 'type' => 3, 'is_del' => 0]);
			}
		} catch (\Exception $e) {
			\Yii::error($e->getMessage(), 'uptAttachmentTag-getMessage');
		}

		return true;
	}

}
