<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_guide_attribution}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property string $role 角色字id字符串
 * @property int $mode_type 导购管理模式:1顾客对应多导购 2导购锁定+有效期
 * @property int $priority 扫码添加时:扫码添加条件优先级:1每个⻔店⾸个添加的企微好友则以⻔店第⼀个添加的⼈为这个顾客的导购;2⾸个添加的企微好友成为导购;3所有的添加的企微好友都成为导购;针对的是模式 mode_type = 1
 * @property int $is_consumption 顾客消费时（订单所属⻔店的企微好友）0不处理 1 订单尝试关联员⼯且⾃动添加该员⼯为导购;针对的是模式 mode_type = 1
 * @property int $in_page_lock 进⼊⻚⾯锁定的天数 针对的是模式 mode_type = 2
 * @property int $add_friend_lock 添加好友锁定天数 针对的是模式 mode_type = 2
 * @property int $consumption_amount_lock 消费锁定天数 针对的是模式 mode_type=2
 * @property int $performance_related 业绩关联设置 0 默认 不关联 1 关联员⼯，没有则不处理 2 优先给关联员⼯，没有则归属⻔店⾸个员⼯
 * @property string $add_time 入库时间
 * @property string $update_time 更新时间
 */
class ShopGuideAttribution extends \yii\db\ActiveRecord
{
    //0 不关联关联员⼯
    const RELATION_NO = 0;
    //1 关联员⼯，没有则不处理
    const RELATION_ONE = 1;
    //2 优先给关联员⼯，没有则归属⻔店⾸个员⼯'
    const RELATION_TWO = 2;

    //不处理
    const IS_CONSUMPTION_CLOSE = 0;
    //订单里若可关联员工则自动增加此员工为导购
    const IS_CONSUMPTION_OPEN = 1;

    //每个⻔店⾸个添加的企微好友则以⻔店第⼀个添加的⼈为这个顾客的导购
    const PRIORITY_ONE = 1;
    //⾸个添加的企微好友成为导购
    const PRIORITY_TWO = 2;
    //所有的添加的企微好友都成为导购
    const PRIORITY_THREE = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_guide_attribution}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'mode_type', 'priority', 'is_consumption', 'in_page_lock', 'add_friend_lock', 'consumption_amount_lock', 'performance_related'], 'integer'],
            [['role'], 'string', 'max' => 200],
            [['add_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                      => Yii::t('app', 'ID'),
            'corp_id'                 => Yii::t('app', '授权的企业ID'),
            'role'                    => Yii::t('app', '导购所属角色类型'),
            'mode_type'               => Yii::t('app', '导购管理模式:1顾客对应多导购 2导购锁定+有效期'),
            'priority'                => Yii::t('app', '扫码添加时:扫码添加条件优先级:1每个⻔店⾸个添加的企微好友则以⻔店第⼀个添加的⼈为这个顾客的导购;2⾸个添加的企微好友成为导购;3所有的添加的企微好友都成为导购;针对的是模式 mode_type = 1'),
            'is_consumption'          => Yii::t('app', '顾客消费时（订单所属⻔店的企微好友）0不处理 1 订单尝试关联员⼯且⾃动添加该员⼯为导购;针对的是模式 mode_type = 1'),
            'in_page_lock'            => Yii::t('app', '进⼊⻚⾯锁定的天数 针对的是模式 mode_type = 2'),
            'add_friend_lock'         => Yii::t('app', '添加好友锁定天数 针对的是模式 mode_type = 2'),
            'consumption_amount_lock' => Yii::t('app', '消费锁定天数 针对的是模式 mode_type=2'),
            'performance_related'     => Yii::t('app', '业绩关联设置 0 默认 不关联 1 关联员⼯，没有则不处理 2 优先给关联员⼯，没有则归属⻔店⾸个员⼯'),
            'add_time'                => Yii::t('app', '入库时间'),
            'update_time'             => Yii::t('app', '更新时间'),
        ];
    }


    public static function getData($corp_id, $field = '')
    {

        $cacheKey = 'shop_guide_attribution_' . $corp_id . '_' . json_encode($field);
        $setting  = \Yii::$app->cache->getOrSet($cacheKey, function () use ($corp_id) {
            return self::findOne(['corp_id' => $corp_id]);
        }, null, new TagDependency(['tags' => 'shop_guide_attribution_'.$corp_id]));
        if (!empty($field)) {
            return $setting->$field;
        }
        return $setting;
    }

    public static function updateConfig($corp_id, $operation_id, $data)
    {

        $attributionModel = self::findOne(['corp_id' => $corp_id]);
        $oldAttributes    = !empty($attributionModel) ? clone $attributionModel : null;

        $attributionModel = !empty($oldAttributes) ? $attributionModel : new ShopGuideAttribution();
        $data['corp_id']  = $corp_id;
        $attributionModel->setAttributes($data);
        if (!$attributionModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($attributionModel));
        }
        $re = !empty($oldAttributes) ? $attributionModel->update() : $attributionModel->save();

        if ($re) {
            //清除缓存
            TagDependency::invalidate(\Yii::$app->cache, 'shop_guide_attribution_' . $corp_id);
            //记录操作日志
            foreach ($data as $k => $v) {

                if (empty($oldAttributes) || $oldAttributes->$k != $v) {
                    $log = [
                        'corp_id'        => $corp_id,
                        'table_namedie'  => self::tableName(),
                        'fields_name'    => $k,
                        'primary_key_id' => $attributionModel->id,
                        'old_value'      => empty($oldAttributes) ? '' : $oldAttributes->$k,
                        'new_value'      => $v,
                        'operator_uid'   => $operation_id,
                        'remarks'        => empty($oldAttributes) ? 'insert' : 'update',
                    ];
                    ShopOperationLog::addLog($log);
                }
            }
        }
        return ['result' => $re];
    }
}
