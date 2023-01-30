<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_third_order_set}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property string $shop_name 店铺名称
 * @property string $shop_api_key 对接key，全表唯一
 * @property string $shop_api_secret 对接密钥
 * @property string $order_pull_url 第三方的订单拉取地址
 * @property string $third_api_key 第三方对接key
 * @property string $third_api_secret 第三方对接密钥
 * @property int $status 默认0未上线 1已上线，已上线才可以生效
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopThirdOrderSet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_third_order_set}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'status'], 'integer'],
            [['shop_name', 'shop_api_key','shop_api_secret'], 'required','message' => '{attribute}不能为空!'],
            ['shop_name', 'unique', 'targetAttribute' => ['corp_id', 'shop_name'], 'message' => '店铺名称不能重复!'],
            ['shop_api_key', 'unique', 'message' => '店铺key不能重复请刷新页面重新生成!'],
            [['add_time', 'update_time'], 'safe'],
            [['shop_name', 'shop_api_key', 'third_api_key'], 'string', 'max' => 100],
            [['order_pull_url','shop_api_secret','third_api_secret'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => Yii::t('app', 'ID'),
            'corp_id'          => Yii::t('app', '授权的企业ID'),
            'shop_name'        => Yii::t('app', '店铺名称'),
            'shop_api_key'     => Yii::t('app', '对接key，全表唯一'),
            'shop_api_secret'  => Yii::t('app', '对接密钥'),
            'order_pull_url'   => Yii::t('app', '第三方的订单拉取地址'),
            'third_api_key'    => Yii::t('app', '第三方对接key'),
            'third_api_secret' => Yii::t('app', '第三方对接密钥'),
            'status'           => Yii::t('app', '默认0未上线 1已上线，已上线才可以生效'),
            'add_time'         => Yii::t('app', '添加时间'),
            'update_time'      => Yii::t('app', '更新时间'),
        ];
    }

    public static function getData($corp_id, $field = '')
    {

        $cacheKey = 'shop_third_order_set_' . $corp_id . '_' . json_encode($field);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corp_id,$field) {
            $setting = self::find()->where(['corp_id' => $corp_id])->asArray()->one();
            if (!empty($field) && !empty($setting)) {
                return $setting[$field];
            }
            return $setting;
        }, null, new TagDependency(['tags' => 'shop_third_order_set_' . $corp_id]));


    }


    public static function updateConfig($corp_id, $operation_id, $data)
    {

        $attributionModel = isset($data['id']) ? self::findOne(['id' => $data['id']]) : new ShopThirdOrderSet();
        $oldAttributes    = !empty($attributionModel['id']) ? clone $attributionModel : null;
        if ( isset($data['id']) && $data['id'] > 0 && empty($oldAttributes)) {
            throw new InvalidDataException('该配置信息不存在！');
        }
        $attributionModel->setAttributes($data);
        if (!$attributionModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($attributionModel));
        }
        $re = !empty($oldAttributes) ? $attributionModel->update() : $attributionModel->save();
        if ($re) {
            //清除缓存
            TagDependency::invalidate(\Yii::$app->cache, 'shop_third_order_set_' . $corp_id);
            //记录操作日志
            foreach ($data as $k => $v) {
                if (empty($oldAttributes) || $oldAttributes->$k != $v) {
                    $log = [
                        'corp_id'        => $corp_id,
                        'table_name'     => self::tableName(),
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

    /**
     * 加密解密的方法
     * @param $key 加密key
     * @param $string 加密字符串
     * @param $decrypt 0 加密 1解密
     * @return string
     */
    public static function encryptDecrypt($key, $string, $decrypt)
    {
        if ($decrypt) {
            $txt = self::passport_key(base64_decode(urldecode($string)), $key);
            $tmp = '';
            for ($i = 0; $i < strlen($txt); $i++) {
                $md5 = $txt[$i];
                $tmp .= $txt[++$i] ^ $md5;
            }
            return $tmp;
        } else {
            srand((double)microtime() * 1000000);
            $encryptKey = md5(rand(0, 32000));
            $ctr         = 0;
            $tmp         = '';
            for ($i = 0; $i < strlen($string); $i++) {
                $ctr = $ctr == strlen($encryptKey) ? 0 : $ctr;
                $tmp .= $encryptKey[$ctr] . ($string[$i] ^ $encryptKey[$ctr++]);
            }
            return urlencode(base64_encode(self::passport_key($tmp, $key)));
        }
    }


    public static function passport_key($txt, $encryptKey)
    {
        $encryptKey = md5($encryptKey);
        $ctr         = 0;
        $tmp         = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encryptKey) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encryptKey[$ctr++];
        }
        return $tmp;
    }
}
