<?php


namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_customer_rfm_setting}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $consumption_data_open 消费数据是否开启0:未开启1:已开启
 * @property int $msg_audit_open 会话存档数据是否开启0:未开启1:已开启
 * @property int $msg_allow_time 会话排除时间msg_allow_time分钟
 * @property int $frequency_type 1:会话频率2:消费频率
 * @property float $frequency_value 频率值
 * @property int $recency_type 1:会话近度2:消费近度
 * @property float $recency_value 近度值
 * @property float $monetary_value 消费额度
 * @property string $add_time 入库时间
 * @property string $update_time 更新时间
 */
class ShopCustomerRfmSetting extends \yii\db\ActiveRecord
{

    /**
     * @var int 消费数据开启
     */
    const CONSUMPTION_DATA_OPEN = 1;//消费数据开启
    /**
     * @var int 消费数据关闭
     */
    const CONSUMPTION_DATA_CLOSE = 0;
    /**
     * @var int 会话存档开启
     */
    const MSG_OPEN = 1;//会话存档开启
    /**
     * @var int 会话存档关闭
     */
    const MSG_CLOSE = 0;
    /**
     * @var int 1 采用会话频率
     */
    const FREQUENCY_MSG = 1;
    /**
     * @var int 2 采用消费频率
     */
    const FREQUENCY_MONEY = 2;
    /**
     * @var int 采用会话近度
     */
    const RECENCY_MSG = 1;
    /**
     * @var int 2 采用消费近度
     */
    const RECENCY_MONEY = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_rfm_setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'consumption_data_open', 'msg_audit_open', 'msg_allow_time', 'frequency_type', 'recency_type'], 'integer'],
            [['msg_audit_open'], 'required'],
            [['frequency_value', 'recency_value', 'monetary_value'], 'number'],
            [['add_time', 'update_time'], 'safe'],
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
            'consumption_data_open' => Yii::t('app', '消费数据是否开启0未开启1已开启'),
            'msg_audit_open'        => Yii::t('app', '会话数据是否开启0未开启1已开启'),
            'msg_allow_time'        => Yii::t('app', '会话排除时间_分钟'),
            'frequency_type'        => Yii::t('app', '频率类型1会话频率2消费频率'),
            'frequency_value'       => Yii::t('app', '频率值'),
            'recency_type'          => Yii::t('app', '进度类型1会话近度2消费近度'),
            'recency_value'         => Yii::t('app', '近度值'),
            'monetary_value'        => Yii::t('app', '消费额度'),
            'add_time'              => Yii::t('app', '入库时间'),
            'update_time'           => Yii::t('app', '更新时间')
        ];
    }


    public static function getData($corp_id, $field = '')
    {
        $cacheKey = 'shop_customer_rfm_setting_' . $corp_id;
        $setting  = \Yii::$app->cache->getOrSet($cacheKey, function () use ($corp_id) {
            $re = self::find()->where(['corp_id' => $corp_id])->asArray()->one();
            if (!empty($re)) {
                $re['recency_type']          = intval($re['recency_type']);
                $re['frequency_type']        = intval($re['frequency_type']);
                $re['consumption_data_open'] = intval($re['consumption_data_open']);
                $re['msg_audit_open']        = intval($re['msg_audit_open']);
            }
            return $re;
        },null,new TagDependency(['tags'=>['shop_customer_rfm_setting_' . $corp_id,'shop_customer_rfm_setting']]));

        if (!empty($field)) {
            return $setting[$field];
        }
        return $setting;
    }

    public static function updateSetting($corp_id, $operation_id, $data)
    {

        $settingModel  = ShopCustomerRfmSetting::findOne(['corp_id' => $corp_id]);
        $oldAttributes = !empty($settingModel) ? clone $settingModel : null;

        $settingModel    = !empty($oldAttributes) ? $settingModel : new ShopCustomerRfmSetting();
        $data['corp_id'] = $corp_id;
        $settingModel->setAttributes($data);
        if (!$settingModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($settingModel));
        }
        $re = !empty($oldAttributes) ? $settingModel->update() : $settingModel->save();

        if ($re) {
            //清除缓存
            TagDependency::invalidate(\Yii::$app->cache, 'shop_customer_rfm_setting_' . $corp_id);
            //记录操作日志
            foreach ($data as $k => $v) {
                if (empty($oldAttributes) || ($oldAttributes->$k != $v)) {
                    $log = [
                        'corp_id'        => $corp_id,
                        'table_name'     => self::tableName(),
                        'fields_name'    => $k,
                        'primary_key_id' => $settingModel->id,
                        'old_value'      => !empty($oldAttributes) ? $oldAttributes->$k : '',
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