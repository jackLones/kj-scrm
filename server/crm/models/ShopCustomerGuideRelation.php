<?php


namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\Expression;
use function foo\func;

/**
 * This is the model class for table "{{%shop_customer_guide_relation}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $cus_id 顾客id
 * @property int $guide_id 导购的用户ID
 * @property int $store_id 门店id
 * @property string $guide_name 导购名
 * @property int $status 状态：0 解除关系，1正常
 * @property int $source_type 1扫码形式2消费时候添加的3订单导⼊添加的4⼿动改变0默认
 * @property string $add_time 入库时间
 * @property string $update_time 更新时间
 */
class ShopCustomerGuideRelation extends \yii\db\ActiveRecord
{
    /**
     * @var 0 解除关系
     */
    const DELETE_RELATION = 0;
    /**
     * @var 1 绑定关系
     */
    const ADD_RELATION = 1;
    /**
     * @var  1 扫码时添加
     */
    const ADD_TYPE_SCAN = 1;
    /**
     * @var  2 消费时添加
     */
    const ADD_TYPE_SHOPPING = 2;
    /**
     * @var  3 订单导入时添加
     */
    const ADD_TYPE_IMPORT = 3;
    /**
     * @var  4 手动添加
     */
    const ADD_TYPE_PEOPLE = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_guide_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'cus_id', 'guide_id', 'store_id', 'status', 'source_type'], 'integer'],
            [['add_time', 'update_time'], 'safe'],
            [['guide_id', 'cus_id', 'corp_id'], 'required'],
            [['guide_name'], 'string', 'max' => 100],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'corp_id'     => Yii::t('app', '授权的企业ID'),
            'cus_id'      => Yii::t('app', '顾客id'),
            'guide_id'    => Yii::t('app', '导购的用户ID'),
            'store_id'    => Yii::t('app', '门店id'),
            'guide_name'  => Yii::t('app', '导购名'),
            'status'      => Yii::t('app', '状态：0 解除关系，1正常'),
            'source_type' => Yii::t('app', '1扫码形式2消费时候添加的3订单导⼊添加的4⼿动改变0默认'),
            'add_time'    => Yii::t('app', '入库时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(AuthStore::className(), ['id' => 'store_id']);
    }

    /**
     * @param $where
     * @param $field
     * @return array
     */
    public static function getData($where, $field)
    {
        $cacheKey = 'shop_customer_guide_relation' . json_encode($where) . '_' . json_encode($field);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($where, $field) {
            $guideModel = self::find()
                ->alias('gr')
                ->select($field . ",store_id,guide_name");

            if (isset($where['store_id']) && !empty($where['store_id'])) {
                $guideModel = $guideModel->with(['store' => function ($query) {
                    $query->select('group_id,id,shop_name')->with(['group' => function ($query) {
                        $query->select('id,name,parent_ids');
                    }]);
                }]);
            }
            $guides = $guideModel->where($where)->orderBy(['id' => 'desc'])->asArray()->all();
            if (!empty($guides)) {

                foreach ($guides as $k => &$gv) {
                    if (isset($gv['store']['group']) && !empty($gv['store']['group'])) {
                        $parentIds = $gv['store']['group']['parent_ids'];
                        if (!empty($parentIds)) {
                            $groupIds         = explode(',', $parentIds);
                            $allGroup         = AuthStoreGroup::find()->select('name')->where(['id' => $groupIds])->asArray()->all();
                            $groupName        = array_column($allGroup, 'name');
                            $gv['group_name'] = implode('-', $groupName) . '-' . $gv['store']['shop_name'];
                        } else {
                            $gv['group_name'] = $gv['store']['group']['name'] . '-' . $gv['store']['shop_name'];
                        }

                        if (!empty($gv['group_name'])) {
                            $gv['guide_name'] .= '(' . $gv['group_name'] . ')';
                        }
                    }
                    unset($gv['store']);
                }
            }
            return $guides;
        }, null, new TagDependency(['tags' => 'shop_customer_guide_relation']));
    }

    /*
     * 扫码时是否添加导购
     * */
    public static function addGuideScanCode($corpId, $cusId, $data)
    {
        //查看是否配置
        $configObject = ShopGuideAttribution::findOne(['corp_id' => $corpId]);
        if (empty($configObject)) {
            \Yii::error(date('Y-m-d H:s:i') . ':ShopCustomerGuideRelation.php->addGuideScanCode 未配置扫码添加联系人时导购添加方式', 'ShopCustomerGuideRelation');
            return false;
        }

        //根据配置项 筛选需要添加导购关系的吗，门店-员工-顾客 数据
        $result = [];
        switch ($configObject->priority) {
            case ShopGuideAttribution::PRIORITY_ONE://每个⻔店⾸个添加的企微好友
                $resultOne = $resultTwo = [];
                foreach ($data as $v) {
                    $v['store_id'] = $v['store_id'] ?: 0;
                    if ($v['store_id']) {
                        $resultOne[$v['store_id']] = empty($resultOne[$v['store_id']]) ? $v : $resultOne[$v['store_id']];
                    } else {
                        $resultTwo[] = $v;
                    }
                    $result = array_merge($resultOne, $resultTwo);
                }
                break;
            case ShopGuideAttribution::PRIORITY_TWO://⾸个添加的企微好友成为导购
                $result[] = $data[0];
                break;
            case ShopGuideAttribution::PRIORITY_THREE://所有的添加的企微好友都成为导购
                $result = $data;
                break;
            default:
                \Yii::error(date('Y-m-d H:s:i') . ':ShopCustomerGuideRelation.php->addGuideScanCode 暂无此种扫码添加联系人时导购添加方式', 'ShopCustomerGuideRelation');
                break;
        }

        //插入导购关系
        foreach ($result as $v) {
            $storeId = isset($v['store_id']) && !empty($v['store_id']) ? $v['store_id'] : 0;
            $guideId = $v['user_id'];
            //检查员工和门店关系是否存在
            self::checkStoreGuideRelation($corpId,$cusId,$guideId,$storeId,ShopCustomerGuideRelation::ADD_TYPE_SCAN);
        }
    }

    /**
     * 判断导购门店是否存在关系 ： 门店存在时->存在关系则添加导购  门店不存在时->不存在关系则添加导购
     * @param $corpId 企业id
     * @param $cusId 顾客id
     * @param $guideId 导购id
     * @param $storeId 门店id
     * @param int $sourceType 来源
     */
    public static function checkStoreGuideRelation($corpId, $cusId, $guideId, $storeId, $sourceType = 0)
    {
        //订单有门店 并且 员工属于此门店员工 则建立导购关系
        if (!empty($storeId)) {
            $storeUser = AuthStoreUser::find()->where(['status' => 1, 'store_id' => $storeId, 'user_id' => $guideId])
                ->select('id')->asArray()->all();
            if (!empty($storeUser)) {
                ShopCustomerGuideRelation::addGuideRelation($corpId, $cusId, $guideId, $storeId, $sourceType);
            }
        } //订单无门店 且 员工无门店归属 则建立导购关系
        else {
            $storeUser = AuthStoreUser::find()->where(['status' => 1, 'user_id' => $guideId])
                ->select('id')->asArray()->all();
            if (empty($storeUser)) {
                ShopCustomerGuideRelation::addGuideRelation($corpId, $cusId, $guideId, $storeId, $sourceType);
            }
        }
    }

    /*
     * 订单推送或者扫码时 顾客添加导购
     * */
    public static function addGuideRelation($corpId, $customerId, $guideId, $storeId = 0, $sourceType = 0)
    {
        $oldWhere    = ['status' => 1, 'corp_id' => $corpId, 'cus_id' => $customerId, 'guide_id' => $guideId, 'store_id' => $storeId];
        $oldRelation = self::findOne($oldWhere);//查询导购信息
        //不存在则添加导购关系
        if (empty($oldRelation)) {
            $guideModel = WorkUser::findOne($guideId);
            $guideName  = !empty($guideModel) ? $guideModel->name : '';
            //添加导购
            $relation = [
                'cus_id'      => $customerId,
                'corp_id'     => $corpId,
                'guide_id'    => $guideId,
                'store_id'    => $storeId,
                'guide_name'  => $guideName,
                'source_type' => $sourceType
            ];
            self::addRelation($relation);

            //导购添加日志
            $guideLog   = [
                'cus_id'      => $customerId,
                'corp_id'     => $corpId,
                'guide_id'    => $guideId,
                'store_id'    => $storeId,
                'operator_id' => 0,
                'type'        => ShopCustomerGuideChangeLog::ADD_TYPE
            ];
            $guideLogId = ShopCustomerGuideChangeLog::addLog($guideLog);

            $storeName = ShopCustomerOrder::getStoreName($storeId);
            $storeName = !empty($storeName) ? '(' . $storeName . ')' : '';
            //顾客信息变更日志
            $setAttributesAll = [
                'corp_id'     => $corpId, 'cus_id' => $customerId, 'table_name' => self::tableName(),
                'log_id'      => $guideLogId, 'title' => '导购',
                'description' => '【' . $guideName . $storeName . '】成为顾客的导购'
            ];
            ShopCustomerChangeLog::addLog($setAttributesAll);
        }
    }

    /*
     *
     * 更新用户导购关系
     * */
    public static function updateRelation($corpId, $operatorId, $customerId, $guideId, $storeId, $store_guide_name)
    {
        //导购_门店 导购唯一性
        $guideStore = [];
        if (!empty($storeId)) {
            foreach ($storeId as $k => $v) {
                $guideStore[] = (isset($guideId[$k]) ? $guideId[$k] : '0') . '_' . $v;
            }
        }


        //查询顾客原导购
        $gWhere      = ['status' => 1, 'corp_id' => $corpId, 'cus_id' => $customerId];
        $gFields     = 'id,cus_id,guide_id,guide_name,store_id';
        $oldRelation = ShopCustomerGuideRelation::getData($gWhere, $gFields);//查询导购信息
        $oldGuide    = $oldGuideIds = [];
        foreach ($oldRelation as $item) {
            $oldGuide[$item['cus_id']][]    = ['id' => $item['id'], 'guide_id' => $item['guide_id'], 'guide_name' => $item['guide_name'], 'store_id' => $item['store_id']];
            $oldGuideIds[$item['cus_id']][] = $item['guide_id'] . '_' . $item['store_id'];
        }

        //添加导购
        foreach ($customerId as $cid) {
            //删除导购
            if (!empty($oldGuide) && !empty($oldGuide[$cid])) {
                foreach ($oldGuide[$cid] as $gk => $gv) {
                    if (!in_array($gv['guide_id'] . '_' . $gv['store_id'], $guideStore)) {
                        //修改数据
                        $relation         = ShopCustomerGuideRelation::findOne($gv['id']);
                        $relation->status = ShopCustomerGuideRelation::DELETE_RELATION;
                        $relation->update();

                        //导购日志
                        $guideLog   = [
                            'cus_id'      => $cid,
                            'corp_id'     => $corpId,
                            'guide_id'    => $gv['guide_id'],
                            'store_id'    => $gv['store_id'],
                            'operator_id' => $operatorId,
                            'type'        => ShopCustomerGuideChangeLog::DELETE_TYPE
                        ];
                        $guideLogId = ShopCustomerGuideChangeLog::addLog($guideLog);

                        //总日志
                        $setAttributesAll = [
                            'corp_id'     => $corpId, 'cus_id' => $cid, 'table_name' => self::tableName(),
                            'log_id'      => $guideLogId, 'title' => '导购',
                            'description' => '【' . $gv['guide_name'] . '】与顾客的导购关系解除'
                        ];
                        ShopCustomerChangeLog::addLog($setAttributesAll);
                    }
                }
            }

            //新增导购
            if (!empty($guideId)) {
                //查询新增导购昵称
                $guide     = WorkUser::find()->where(['id' => $guideId])->select('id,name')->asArray()->all();
                $guideName = [];
                foreach ($guide as $item) {
                    $guideName[$item['id']] = $item['name'];
                }
                if (empty($guideName) || count($guideName) != count($guideId)) {
                    throw new InvalidDataException('导购信息错误！');
                }
                foreach ($guideId as $k => $gid) {

                    $curStoreId = isset($storeId[$k]) && !empty($storeId[$k]) ? $storeId[$k] : 0;

                    if (empty($oldGuideIds[$cid]) || !in_array($gid . '_' . $curStoreId, $oldGuideIds[$cid])) {

                        //插入数据
                        $relation = [
                            'cus_id'      => $cid,
                            'corp_id'     => $corpId,
                            'guide_id'    => $gid,
                            'store_id'    => $curStoreId,
                            'guide_name'  => $guideName[$gid],
                            'source_type' => ShopCustomerGuideRelation::ADD_TYPE_PEOPLE
                        ];

                        $result = self::addRelation($relation);
                        if ($result['type'] == 'update') {
                            // continue;
                        }
                        //导购日志
                        $guideLog   = [
                            'cus_id'      => $cid,
                            'corp_id'     => $corpId,
                            'guide_id'    => $gid,
                            'store_id'    => $curStoreId,
                            'operator_id' => $operatorId,
                            'type'        => ShopCustomerGuideChangeLog::ADD_TYPE
                        ];
                        $guideLogId = ShopCustomerGuideChangeLog::addLog($guideLog);
                        $storeName  = '';
                        if ($curStoreId) {
                            $storeName = ShopCustomerOrder::getStoreName($curStoreId);
                            $storeName = !empty($storeName) ? '(' . $storeName . ')' : '';
                        }
                        //总日志
                        $setAttributesAll = [
                            'corp_id'     => $corpId, 'cus_id' => $cid, 'table_name' => self::tableName(),
                            'log_id'      => $guideLogId, 'title' => '导购',
                            'description' => '【' . $guideName[$gid] . $storeName . '】成为顾客的导购'
                        ];
                        ShopCustomerChangeLog::addLog($setAttributesAll);
                    }
                }
            }
        }

        //清除缓存
        TagDependency::invalidate(\Yii::$app->cache, 'shop_customer_guide_relation');
        return true;
    }

    /*
     * 添加顾客导购关系
     * */
    public static function addRelation($data)
    {
        $where = [
            'corp_id' => $data['corp_id'], 'cus_id' => $data['cus_id'], 'guide_id' => $data['guide_id'], 'store_id' => $data['store_id']
        ];
        //清除缓存
        TagDependency::invalidate(\Yii::$app->cache, 'shop_customer_guide_relation');
        if ($relation = ShopCustomerGuideRelation::findOne($where)) {
            $relation->status = ShopCustomerGuideRelation::ADD_RELATION;
            $id               = $relation->update();
            $result           = ['id' => $id, 'type' => 'update'];
        } else {
            $relation = new ShopCustomerGuideRelation();
            $relation->setAttributes($data);
            if (!$relation->validate()) {
                throw new InvalidDataException(SUtils::modelError($relation));
            }
            $id     = $relation->save();
            $result = ['id' => $id, 'type' => 'insert'];
        }
        return $result;
    }

    /*
     * 导购角色
     * */
    public static function getRole($corpId)
    {
        //TODO::导购角色查询
        return [
//            ['id' => 0, 'name' => '暂无'],
//            ['id' => 1, 'name' => '店长'],
//            ['id' => 2, 'name' => '副店长'],
//            ['id' => 3, 'name' => '销售'],
//            ['id' => 4, 'name' => '财务'],
        ];
    }


    /**
     * 导购顾客总数和本月新增
     * @param $corpId
     * @param $guideId
     * @return int[]
     */
    public static function getCustomerMsg($corpId, $guideId)
    {
        $relationModel = new ShopCustomerGuideRelation();
        $customer      = $relationModel->find()
            ->select(new Expression("DATE_FORMAT(`add_time`, '%Y-%m') as cur_time,count(id) as num"))
            ->where(['corp_id' => $corpId, 'guide_id' => $guideId, 'status' => 1])
            ->groupBy(new Expression("DATE_FORMAT(`add_time`, '%Y-%m')"))
            ->asArray()
            ->all();
        $data          = ['num' => 0, 'new' => 0];
        foreach ($customer as $v) {
            $data['num'] += $v['num'];
            if ($v['cur_time'] == date('Y-m', time())) {
                $data['new'] = (int)$v['num'];
            }
        }
        return $data;
    }


}