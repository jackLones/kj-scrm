<?php


namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Imagine\Exception\RuntimeException;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\Query;

/**
 * This is the model class for table "{{%shop_customer}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $user_id 员⼯的ID
 * @property int $external_id 企微客户主表ID pig_work_external_contact
 * @property int $sea_customer_id ⾮企微客户主表ID pig_public_sea_customer
 * @property string $union_id 企微客户union_id
 * @property string $phone ⼿机号（可能不能唯⼀） 可能存在刚开始没有⼿机号的情况，允许为空
 * @property int $level_id 等级ID（跟着对应的数据变动更新） 关联等级表ID
 * @property int $rfm_id RFM 平级ID（跟着对应的数据变动更新） 关联RFM 表
 * @property string $name 顾客昵称 （以⼊库第⼀条为准，后期可以修改）
 * @property string $true_name 顾客真实姓名-⼿动编辑，属于顾客管理后期可以自定义的名称)
 * @property string $last_interactive_time 最后互动时间
 * @property int $interactive_count 总互动次数
 * @property float $frequency_msg 会话频率
 * @property float $recency_msg 会话近度
 * @property string $last_consumption_time 最后消费时间
 * @property int $consumption_count 总消费次数
 * @property float $frequency_shopping 消费频率
 * @property float $recency_shopping 消费近度
 * @property float $amount 消费金额
 * @property int $is_del 是否删除0正常1删除
 * @property string $add_time 入库时间
 * @property string $update_time 更新时间
 */
class ShopCustomer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'external_id', 'sea_customer_id', 'level_id', 'rfm_id', 'interactive_count', 'consumption_count', 'is_del'], 'integer'],
            [['last_interactive_time', 'last_consumption_time', 'add_time', 'update_time'], 'safe'],
            [['frequency_msg', 'frequency_shopping', 'recency_msg', 'recency_shopping', 'amount'], 'number'],
            [['phone'], 'string', 'max' => 11],
            [['name'], 'string'],
            [['true_name', 'union_id'], 'string', 'max' => 100],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => Yii::t('app', 'ID'),
            'corp_id'               => Yii::t('app', '授权的企业ID'),
            'user_id'               => Yii::t('app', '员⼯的ID'),
            'external_id'           => Yii::t('app', '企微客户主表ID pig_work_external_contact'),
            'sea_customer_id'       => Yii::t('app', '⾮企微客户主表ID pig_public_sea_customer'),
            'union_id'              => Yii::t('app', '企微客户union_id'),
            'phone'                 => Yii::t('app', '⼿机号（可能不能唯⼀） 可能存在刚开始没有⼿机号的情况，允许为空'),
            'level_id'              => Yii::t('app', '等级ID（跟着对应的数据变动更新） 关联等级表ID'),
            'rfm_id'                => Yii::t('app', 'RFM 平级ID（跟着对应的数据变动更新） 关联RFM 表'),
            'name'                  => Yii::t('app', '顾客昵称 （以⼊库第⼀条为准，后期可以修改）'),
            'true_name'             => Yii::t('app', '顾客真实姓名-⼿动编辑，属于顾客管理后期可以自定义的名称)'),
            'last_interactive_time' => Yii::t('app', '最后互动时间'),
            'interactive_count'     => Yii::t('app', '总互动次数'),
            'frequency_msg'         => Yii::t('app', '会话频率'),
            'recency_msg'           => Yii::t('app', '会话近度'),
            'last_consumption_time' => Yii::t('app', '最后消费时间'),
            'consumption_count'     => Yii::t('app', '总消费次数'),
            'frequency_shopping'    => Yii::t('app', '消费频率'),
            'recency_shopping'      => Yii::t('app', '消费近度'),
            'amount'                => Yii::t('app', '消费金额'),
            'is_del'                => Yii::t('app', '是否删除'),
            'add_time'              => Yii::t('app', '入库时间'),
            'update_time'           => Yii::t('app', '更新时间'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(ShopCustLevelSet::className(), ['id' => 'level_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRfm()
    {
        return $this->hasOne(ShopCustomerRfmDefault::className(), ['id' => 'rfm_id']);
    }


    //员工等级数据
    public static function getLevelDetail($corpId)
    {
        $cacheKey = 'shop_customer_' . $corpId . "_get_level_detail";
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId) {
            $customerLevel = self::find()->where(['corp_id' => $corpId, 'is_del' => 0])->select('level_id,count(id) as num')->groupBy(['level_id'])->asArray()->all();
            $customerNum   = self::find()->where(['corp_id' => $corpId, 'is_del' => 0])->select('count(id) as num')->asArray()->one();
            $levelNum      = [];
            if (!empty($customerLevel)) {
                foreach ($customerLevel as $v) {
                    $levelNum[$v['level_id']] = $v['num'];
                }
            }
            return ['level_num' => $levelNum, 'customer_num' => $customerNum['num']];
        }, null, new TagDependency(['tags' => ['shop_customer_' . $corpId, 'shop_customer', 'shop_customer_level_setting_' . $corpId]]));
    }

    //员工rfm参考数据
    public static function getRfmData($corpId, $consumptionDataOpen, $msgAuditOpen)
    {
        $cacheKey = 'shop_customer_' . $corpId . '_get_rfm_data';
        if (!($customerList = \Yii::$app->cache->get($cacheKey))) {
            $customerList = self::find()->where(['corp_id' => $corpId, 'is_del' => 0])
                ->select('amount,frequency_shopping,recency_shopping,frequency_msg,recency_msg,consumption_count')
                ->asArray()->all();
            \Yii::$app->cache->set($cacheKey, $customerList, 86400, new TagDependency(['tags' => ['shop_customer_' . $corpId, 'shop_customer']]));
        }
        $amount = $frequencyShopping = $recencyShopping = $frequencyMsg = $recencyMsg = [];
        if (!empty($customerList)) {
            foreach ($customerList as $v) {
                $amount[]            = $v['consumption_count'] > 0 ? round($v['amount'] / $v['consumption_count'], 2) : 0;
                $frequencyShopping[] = $v['frequency_shopping'];
                $recencyShopping[]   = $v['recency_shopping'];
                $frequencyMsg[]      = $v['frequency_msg'];
                $recencyMsg[]        = $v['recency_msg'];
            }
        }
        //总人数
        $result['customer_num'] = count($customerList);
        //消费参考值
        if ($consumptionDataOpen == ShopCustomerRfmSetting::CONSUMPTION_DATA_OPEN) {
            $result['amount']             = self::getMiddle($amount);
            $result['frequency_shopping'] = self::getMiddle($frequencyShopping);
            $result['recency_shopping']   = self::getMiddle($recencyShopping);
        } else {
            $result['frequency_shopping'] = $result['recency_shopping'] = $result['amount'] = 0;
        }
        //会话参考值
        if ($msgAuditOpen == ShopCustomerRfmSetting::MSG_OPEN) {
            $result['frequency_msg'] = self::getMiddle($frequencyMsg);
            $result['recency_msg']   = self::getMiddle($recencyMsg);
        } else {
            $result['frequency_msg'] = $result['recency_msg'] = 0;
        }
        return $result;
    }

    //等级设置页面-等级移动-批量更新用户等级
    public static function updateCustomerLevel($corpId, $operatorId, $customerWhere, $levelId)
    {
        $field   = ['level_id' => $levelId];
        $oldUser = ShopCustomer::find()->where($customerWhere)->select('level_id,id,name')->asArray()->all();
        if (empty($oldUser)) {
            throw new InvalidDataException('暂无待修改用户！');
        }
        if ($levelId > 0 && !ShopCustLevelSet::findOne($levelId)) {
            throw new InvalidDataException('不存在该等级或已删除！');
        }
        $re = self::updateAll($field, $customerWhere);
        if (!$re) {
            throw new InvalidDataException('用户更新失败！');
        }
        //删除缓存
        TagDependency::invalidate(\Yii::$app->cache, 'shop_customer_' . $corpId);
        $cusMgs = [];
        foreach ($oldUser as $value) {
            $cusMgs[$value['id']] = $value;
        }
        //记录等级更新日志
        ShopCustomerLevelLog::addLevelLog($corpId, $operatorId, $cusMgs, $levelId);
        return $re;
    }

    //计算中位数
    public static function getMiddle($data)
    {
        if (empty($data)) return 0;
        sort($data);
        if (count($data) % 2 == 1) {
            return $data[(count($data) - 1) / 2];
        } else {
            return round(($data[(count($data)) / 2] + $data[(count($data) - 1) / 2]) / 2, 2);
        }
    }

    //清理企业员工数据到顾客表
    public static function clearWorkUser($corpId = 0, $id = 0)
    {
        if ($id > 0) {
            $where = ['fu.id' => $id];
            $users = WorkExternalContactFollowUser::find()->alias('fu')
                ->leftJoin('{{%work_external_contact}} wc', 'fu.external_userid=wc.id')
                ->select(['fu.external_userid'])
                ->where($where)
                ->orderBy('wc.id')
                ->asArray()
                ->all();
            self::clearWorkUserItem($users);
            return true;
        } else if ($corpId > 0) {
            //查找最近一次更新记录
            $last_time = ShopTaskRecord::getRecord($corpId, ShopTaskRecord::TYPE_WORK_USER);
            if ($last_time === 0) {
                $where = ['wc.corp_id' => $corpId];
            } else {
                $last_time  = date('Y-m-d 00:00:00', strtotime($last_time));
                $whereOne = ['or', ['>', 'fu.update_at', $last_time], ['>', 'wc.update_time', $last_time]];
                $where    = ['and', $whereOne, ['wc.corp_id' => $corpId]];
            }
            $query = (new Query())
                ->select(['fu.external_userid'])
                ->from('{{%work_external_contact_follow_user}} as fu')
                ->leftJoin('{{%work_external_contact}} wc', 'fu.external_userid=wc.id')
                ->where($where)
                ->orderBy('wc.id');

            foreach ($query->batch() as $qk => $users) {
                //var_dump('清理企业员工数据到顾客表'.$corpId.'数据:'.($qk*100 + count($users)) );
                self::clearWorkUserItem($users);
                unset($users);
            }
            //插入最新任务执行记录
            ShopTaskRecord::addRecord($corpId, ShopTaskRecord::TYPE_WORK_USER);
            unset($where);
            return true;
        } else {
            return false;
        }


    }

    private static function clearWorkUserItem($followUser)
    {
        if (empty($followUser)) {
            return false;
        }
        $userId = [];
        foreach ($followUser as $fv) {
            $userId[$fv['external_userid']] = $fv['external_userid'];
        }
        unset($followUser);

        $user = WorkExternalContact::find()
            ->select('id,corp_id,name,unionid')
            ->where(['id' => $userId])
            ->with(['workExternalContactFollowUsers' => function (\yii\db\ActiveQuery $query) {
                $query->select('external_userid,user_id,remark_mobiles,createtime,store_id')
                    ->orderBy('createtime asc');
            }])
            ->asArray()
            ->all();
        unset($userId);
        foreach ($user as $v) {
            //从分表中获取用户手机号码 多个不同号码则均丢弃
            if (!empty($v['workExternalContactFollowUsers'])) {
                $corpId        = isset($v['corp_id']) ? $v['corp_id'] : 0;
                $where         = [
                    'is_del'      => 0,
                    'corp_id'     => $corpId,
                    'external_id' => $v['id'],
                ];
                $customer      = [
                    'corp_id'         => $corpId,
                    'external_id'     => $v['id'],
                    'sea_customer_id' => 0,
                    'name'            => $v['name'],
                    'union_id'        => !empty($v['unionid']) && $v['unionid'] != 'null' ? $v['unionid'] : ''
                ];
                $remarkMobiles = $remarkMobilesUser = [];
                $remarkMobile  = '';
                foreach ($v['workExternalContactFollowUsers'] as $wv) {
                    $customer['add_time'] = !empty($customer['add_time']) ? $customer['add_time'] : date('Y-m-d H:i:s', $wv['createtime']);
                    if (!empty($wv['remark_mobiles']) && strlen($wv['remark_mobiles']) == 11) {
                        $remarkMobile                             = $wv['remark_mobiles'];
                        $remarkMobiles[$wv['remark_mobiles']]     = $wv['remark_mobiles'];
                        $remarkMobilesUser[$wv['remark_mobiles']] = !empty($remarkMobilesUser[$wv['remark_mobiles']]) ? $remarkMobilesUser[$wv['remark_mobiles']] : $wv['user_id'];
                    }
                }

                //查询该企微用户是否存在对应顾客
                $oldWorkCustomer = ShopCustomer::findOne($where);

                //查询该手机号码是否存在对应顾客
                $phoneOldCus = [];
                if (!empty($remarkMobile)) {
                    $phoneOldCus = ShopCustomer::findOne(['is_del' => 0, 'corp_id' => $corpId, 'phone' => $remarkMobile]);
                }

                //企微用户仅存在一条联系号码 并且该号码不存在顾客表中 并且若该企微用户已经存在顾客则对应顾客不存在其他号码
                if (count($remarkMobiles) == 1 && empty($oldWorkCustomer['phone']) && empty($phoneOldCus['external_id'])) {
                    $customer['user_id'] = $remarkMobilesUser[$remarkMobile];
                    $customer['phone']   = $remarkMobile;
                }


                //添加顾客
                $cusId = ShopCustomer::addCustomer($where, $customer);

                //添加企微用户顾客时
                //检查 此电话号码 是否存在非企业用户顾客 如果存在 则删除非企业顾客信息 移动该顾客所有订单到企微用户下
                $oldCusId = 0;
                if (!empty($phoneOldCus) && empty($phoneOldCus['external_id']) && !empty($customer['phone'])) {
                    $oldCusId = $phoneOldCus['id'];
                }

                //根据unionid查询
                if (!empty($oldCusId) && $oldCusId != $cusId) {
                    //删除原号码对应的非企微客户的顾客信息
                    ShopCustomer::updateAll(['is_del' => 1], ['id' => $oldCusId]);
                    //修改原号码对应的非企微客户的顾客的订单信息
                    ShopCustomerOrder::updateAll(['cus_id' => $cusId], ['corp_id' => $corpId, 'cus_id' => $oldCusId]);
                }

                //顾客绑定导购
                ShopCustomerGuideRelation::addGuideScanCode($corpId, $cusId, $v['workExternalContactFollowUsers']);
            }
        }
        unset($user);
    }

    //清理非企业员工信息到顾客表
    public static function clearSeaUser($corpId = 0, $id = 0)
    {
        //非企业员工
        if ($id > 0) {
            $where = ['id' => $id];
            $users = PublicSeaCustomer::find()->select('id,name,phone,add_time,corp_id')->where($where)->asArray()->all();
            self::clearSeaUserItem($users);
            unset($where);
            return true;
        } else if ($corpId > 0) {
            $last_time = ShopTaskRecord::getRecord($corpId, ShopTaskRecord::TYPE_SEA_USER);
            if ($last_time === 0) {
                $where = ['corp_id' => $corpId];
            } else {
                $last_time  = date('Y-m-d 00:00:00', strtotime($last_time));
                $whereOne = ['or', ['>', 'update_time', $last_time], ['>', 'add_time', $last_time]];
                $where    = ['and', $whereOne, ['corp_id' => $corpId]];
            }
            $query = (new Query())
                ->select('id,name,phone,add_time,corp_id')
                ->from('{{%public_sea_customer}}')
                ->where($where)
                ->andWhere(['!=', 'phone', ''])
                ->andWhere(['type' => 0])
                ->orderBy('id');

            foreach ($query->batch() as $qk => $users) {
                //var_dump('清理非企业员工数据到顾客表'.$corpId.'数据:'.($qk*100 + count($users)) );
                self::clearSeaUserItem($users);
                unset($users);
            }
            //插入最新更新记录
            ShopTaskRecord::addRecord($corpId, ShopTaskRecord::TYPE_SEA_USER);

            unset($where);
            return true;
        } else {
            return false;
        }


    }

    public static function clearSeaUserItem($seaUser)
    {

        //非企业员工
        if (empty($seaUser)) {
            return false;
        }
        foreach ($seaUser as $sv) {
            $corpId = $sv['corp_id'];
            $where  = ['is_del' => 0, 'corp_id' => $corpId, 'phone' => $sv['phone']];
            $user   = ShopCustomer::find()->where($where)->count();
            if (empty($user)) {
                $customer = [
                    'corp_id'         => $corpId,
                    'external_id'     => 0,
                    'sea_customer_id' => $sv['id'],
                    'name'            => rawurlencode($sv['name']),
                    'phone'           => $sv['phone'],
                    'add_time'        => date('Y-m-d H:i:s', $sv['add_time'])
                ];
                ShopCustomer::addCustomer($where, $customer);
            }
        }
        TagDependency::invalidate(\Yii::$app->cache, 'shop_customer');
    }

    //计算rfm等级数据
    public static function taskRfm($where = ['is_del' => 0])
    {
        $query = (new Query())
            ->select('id,corp_id,external_id,add_time')
            ->from('{{%shop_customer}}')
            ->where($where)
            ->orderBy('corp_id asc');

        foreach ($query->batch() as $qk => $users) {
            //var_dump('计算企业'.$where['corp_id'].'的顾客rfm等级数据:'.($qk*100 + count($users)) );
            self::taskRfmItem($users);
            unset($users);
        }
    }

    private static function taskRfmItem($customers)
    {

        if (empty($customers)) {
            return true;
        }
        foreach ($customers as $cv) {

            $corpId           = $customer['corp_id'] = $cv['corp_id'];
            $rfmList[$corpId] = !empty($rfmList[$corpId]) ? $rfmList[$corpId] : ShopCustomerRfmSetting::getData($corpId);
            $rfm              = $rfmList[$corpId];

            if (empty($rfm)) continue;

            //用户创建时间
            $addCustomerTime = strtotime($cv['add_time']);
            $rfmWhere        = [];
            $isGetRfm        = 0;//是否需要计算rfm等级 基准数据均有配置时计算
            //是否开启会话存档 开启则 获取会话频率 次数 近度
            if ($rfm['msg_audit_open'] == ShopCustomerRfmSetting::MSG_OPEN && $cv['external_id'] > 0) {

                $msg = WorkMsgAuditInfo::find()
                    ->select('external_id,to_external_id,msgtime,from_type,to_type')
                    ->where(['to_type' => 2, 'to_external_id' => $cv['external_id']])
                    ->orWhere(['from_type' => 2, 'external_id' => $cv['external_id']])
                    ->orderBy('msgtime desc')->asArray()->all();

                if (!empty($msg)) {
                    $msgLength           = count($msg) - 1;
                    $msgAllowTime        = !empty($rfm['msg_allow_time']) ? $rfm['msg_allow_time'] : 0;
                    $lastInteractiveTime = 0;//最近一次会话时间
                    $interactiveCount    = 0;//主动会话总次数
                    foreach ($msg as $k => $v) {
                        if ($v['to_type'] == 2) {//客户发送消息
                            $lastInteractiveTime = !empty($lastInteractiveTime) ? $lastInteractiveTime : $v['msgtime'];
                            if ($k == $msgLength) {
                                $interactiveCount += 1;
                            } else if ($msg[$k + 1]['to_type'] == 1) {
                                $interactiveCount += (($v['msgtime'] - $msg[$k + 1]['msgtime']) / 1000 / 60 > $msgAllowTime ? 1 : 0);
                            }
                        }
                    }
                    //变化时间格式
                    $lastInteractiveTime               = intval($lastInteractiveTime / 1000);
                    $customer['last_interactive_time'] = date('Y-m-d H:i:s', $lastInteractiveTime);
                    $customer['interactive_count']     = $interactiveCount;
                    $customer['frequency_msg']         = round($interactiveCount / ((time() - $addCustomerTime) / 3600 / 24), 2);
                    $customer['recency_msg']           = round((time() - $lastInteractiveTime) / 3600 / 24, 2);
                    if ($rfm['frequency_type'] == ShopCustomerRfmSetting::FREQUENCY_MSG) {
                        $rfmWhere['frequency'] = $customer['frequency_msg'] > $rfm['frequency_value'] ? 1 : 0;
                    }
                    if ($rfm['recency_type'] == ShopCustomerRfmSetting::RECENCY_MSG) {
                        $rfmWhere['recency'] = $customer['recency_msg'] < $rfm['recency_value'] ? 1 : 0;
                    }
                    $isGetRfm = ($rfm['frequency_value'] > 0 && $rfm['recency_value']) > 0 ? 1 : 0;
                }
            }

            //是否开启消费数据
            if ($rfm['consumption_data_open'] == ShopCustomerRfmSetting::CONSUMPTION_DATA_OPEN) {

                $order = ShopCustomerOrder::find()
                    ->select('corp_id,cus_id,sum(payment_amount) as amount,count(id) as num,max(pay_time) as last_time,min(pay_time) as start_time')
                    ->where(['corp_id' => $corpId, 'cus_id' => $cv['id']])
                    ->andWhere(['>', 'pay_time', 0])
                    ->groupBy('cus_id')
                    ->asArray()->one();

                if (!empty($order)) {
                    $customer['last_consumption_time'] = $order['last_time'];
                    $customer['amount']                = $order['amount'];
                    $customer['consumption_count']     = $order['num'];

                    $startTime                      = date("Y-m-d", strtotime($order['start_time']));
                    $endTime                        = date("Y-m-d", time());
                    $allDay                         = ((strtotime($endTime) - strtotime($startTime)) / 3600 / 24);
                    $allDay                         = $allDay < 1 ? 1 : $allDay;
                    $customer['frequency_shopping'] = round($order['num'] / $allDay, 2);
                    $customer['recency_shopping']   = round((time() - strtotime($order['last_time'])) / 3600 / 24, 2);
                    //频率
                    if ($rfm['frequency_type'] == ShopCustomerRfmSetting::FREQUENCY_MONEY) {
                        $rfmWhere['frequency'] = $customer['frequency_shopping'] > $rfm['frequency_value'] ? 1 : 0;
                    }
                    //近度 
                    if ($rfm['recency_type'] == ShopCustomerRfmSetting::RECENCY_MONEY) {
                        $rfmWhere['recency'] = ($customer['recency_shopping'] < $rfm['recency_value']) ? 1 : 0;
                    }
                    //消费额度 = 消费金额 / 消费次数
                    $monetary             = $order['num'] > 0 ? round($customer['amount'] / $order['num'], 2) : 0;
                    $rfmWhere['monetary'] = $monetary > $rfm['monetary_value'] ? 1 : 0;
                }
                $isGetRfm = ($rfm['frequency_value'] > 0 && $rfm['recency_value']) > 0 && $rfm['monetary_value'] > 0 ? 1 : 0;
            }

            //计算rfm等级
            if (isset($rfmWhere['recency']) && isset($rfmWhere['frequency']) && $isGetRfm) {
                $rfmWhere['type']   = $rfm['consumption_data_open'];
                $customerModel      = ShopCustomerRfmDefault::findOne($rfmWhere);
                $customer['rfm_id'] = !empty($customerModel) ? $customerModel->id : 0;
            }


            //更新信息
            if (!empty($customer)) {
                ShopCustomer::addCustomer(['id' => $cv['id']], $customer);
                unset($customer);
            }
        }
    }

    //根据union_id 或 phone 检测是否存在用户
    public static function checkCustomer($order)
    {
        $phone = !empty($order['buy_phone']) ? $order['buy_phone'] : $order['receiver_phone'];
        //存在union_id情况
        if (!empty($order['union_id'])) {
            $unionUser = self::findOne(['is_del' => 0, 'corp_id' => $order['corp_id'], 'union_id' => $order['union_id']]);
            if (!empty($unionUser)) {
                return $unionUser['id'];
            }
        }
        if (!empty($phone)) {
            if (!preg_match("/^((13[0-9])|(14[0-9])|(15([0-9]))|(16([0-9]))|(17([0-9]))|(18[0-9])|(19[0-9]))\d{8}$/", $phone)) {
                return false;
            }
            $oldCustomer = self::findOne(['is_del' => 0, 'corp_id' => $order['corp_id'], 'phone' => $phone]);
            if (!empty($oldCustomer) && $cusId = $oldCustomer->id) {
                return $cusId;
            }
            //公海客户信息
            $sea = [
                'corp_id' => intval($order['corp_id']),
                'phone'   => $phone,
                'name'    => ($phone == $order['buy_phone'] ? $order['buy_name'] : $order['receiver_name']),
            ];
            $userCorpRelationModel = UserCorpRelation::findOne(['corp_id' => $order['corp_id']]);
            if (isset($userCorpRelationModel) && !empty($userCorpRelationModel)) {
                $sea['uid'] = $userCorpRelationModel->uid;
            } else {
                return false;
            }
            //获取来源
            $customFieldModel       = CustomField::findOne(['key' => 'offline_source']);
            $fieldId                = (isset($customFieldModel) && !empty($customFieldModel)) ? $customFieldModel->id : 0;
            $customFieldOptionModel = CustomFieldOption::findOne(['fieldid' => $fieldId, 'match' => '第三方订单导入']);
            if (isset($customFieldOptionModel) && !empty($customFieldOptionModel)) {
                $sea['field_option_id'] = $customFieldOptionModel->id;
            }
            //插入公海表
            $tempInfo = PublicSeaCustomer::find()->where(['uid' => $sea['uid'], 'corp_id' => $sea['corp_id'], 'phone' => $phone])->asArray()->one();
            //公海已存在该号码
            if ($tempInfo) {
                $seaCustomerId = $tempInfo['id'];
                //如果存在非企业用户但无顾客信息 新建顾客
                ShopCustomer::clearSeaUser(0, $seaCustomerId);
            }
            //公海不存在该号码
            else {
                $sea['name'] = $sea['name'] ?: '暂无';
                //插入公海数据 其中包含了插入顾客记录逻辑
                $seaCustomerId = PublicSeaCustomer::setData($sea);
            }

            //返回顾客id
            $customer = ShopCustomer::findOne(['is_del' => 0, 'corp_id' => intval($order['corp_id']), 'sea_customer_id' =>$seaCustomerId]);
            if ($customer) {
                return $customer->id;
            }
            return false;
        }
    }


    //添加顾客数据
    public static function addCustomer($where, $data)
    {
        $customerModel = ShopCustomer::find()->where($where)->one();
        $oldAttributes = !empty($customerModel) ? clone $customerModel : null;
        $oldRfmId      = !empty($oldAttributes) ? $oldAttributes->rfm_id : 0;
        $customerModel = !empty($oldAttributes) ? $customerModel : new ShopCustomer();
        foreach ($data as $k => $v) {
            if (empty($v)) {
                unset($data[$k]);
            }
        }
        $customerModel->setAttributes($data);
        if (!$customerModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($customerModel));
        }
        !empty($oldAttributes) ? $customerModel->update() : $customerModel->save();
        if (!empty($data['rfm_id']) && $oldRfmId != $data['rfm_id']) {
            $rfmName = ShopCustomerRfmAlias::getData($data['corp_id'], 1);
            $rfmLog  = [
                'corp_id'         => $data['corp_id'],
                'cus_id'          => $customerModel->id,
                'rfm_id'          => $customerModel->rfm_id,
                'rfm_name'        => isset($rfmName[$customerModel->rfm_id]) ? $rfmName[$customerModel->rfm_id] : '',
                'before_rfm_id'   => $oldRfmId,
                'before_rfm_name' => isset($rfmName[$oldRfmId]) ? $rfmName[$oldRfmId] : '暂无',
            ];
            $logId   = ShopCustomerRfmLog::addLog($rfmLog);
            $cusLog  = [
                'corp_id'     => $data['corp_id'],
                'cus_id'      => $customerModel->id,
                'table_name'  => self::tableName(),
                'log_id'      => $logId,
                'title'       => '评级',
                'description' => '【' . $rfmLog['before_rfm_name'] . '】变成【' . $rfmLog['rfm_name'] . '】'
            ];
            ShopCustomerChangeLog::addLog($cusLog);
        }

        return $customerModel->id;
    }


}