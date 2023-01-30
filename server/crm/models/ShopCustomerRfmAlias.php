<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Imagine\Exception\RuntimeException;
use Matrix\Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_customer_rfm_alias}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $rfm_id rfm_default的id
 * @property string $rfm_name ⾃定义名称
 * @property string $add_time 入库时间
 * @property string $update_time 更新时间
 */
class ShopCustomerRfmAlias extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_rfm_alias}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'rfm_id'], 'integer'],
            [['add_time', 'update_time'], 'safe'],
            [['rfm_name'], 'string', 'max' => 100],
            ['rfm_name', 'unique', 'targetAttribute' => ['corp_id', 'rfm_name'], 'message' => 'rfm别名称不能重复!'],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']]
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
            'rfm_id'      => Yii::t('app', 'rfm_default的id'),
            'rfm_name'    => Yii::t('app', '⾃定义名称'),
            'add_time'    => Yii::t('app', '入库时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }


    /**
     * @param $corpId
     * @param int $dataType 0原数组 1转换后rfm_id作为键值的数组
     * @return array
     */
    public static function getData($corpId,$dataType=0)
    {
        $type     = ShopCustomerRfmSetting::getData($corpId, 'consumption_data_open');//消费数据是否开启
        $type     = $type ?: 0;
        $cacheKey = 'shop_customer_rfm_'.$corpId.'_'.$dataType;
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($type,$corpId,$dataType) {
            $rfmAll = ShopCustomerRfmDefault::find()->where(['type' => $type])
                ->with(['alias' => function (\yii\db\ActiveQuery $query)use($corpId){
                    $query->where(['corp_id'=>$corpId]);
                }])->orderBy(['id' => 'desc'])->asArray()->all();

            $rfmName = [];
            if (!empty($rfmAll)) {
                foreach ($rfmAll as $rk => $rv) {
                    $rfmName[$rv['id']] = !empty($rv['alias']) && !empty($rv['alias']['rfm_name']) ? $rv['alias']['rfm_name'] : $rv['default_name'];
                    $rfmAll[$rk]['default_name'] = $rfmName[$rv['id']];
                }
            }
            if(!empty($dataType)){
                $result  = $rfmName;
            }else{
                $result  = $rfmAll;
            }
            return $result;
        }, null, new TagDependency(['tags' =>['shop_customer_rfm_'.$corpId,'shop_customer_rfm'] ]));


    }

    /**
     * @param $corpId
     * @return array
     */
    public static function getAllData($corpId){
        $cacheKey = 'shop_customer_rfm_'.$corpId.'_get_all_data';
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId) {
            return ShopCustomerRfmDefault::find()
                ->with(['alias' => function (\yii\db\ActiveQuery $query)use($corpId) {
                    $query->where(['corp_id'=>$corpId]);
                }])->orderBy(['id' => 'desc'])->asArray()->all();
        }, null, new TagDependency(['tags' => 'shop_customer_rfm_'.$corpId]));
    }

    /**
     * @param $corpId
     * @param $operatorUid
     * @param $data
     * @return bool|int
     * @throws InvalidDataException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function AddAlias($corpId, $operatorUid, $data)
    {
        $where['corp_id'] = $data['corp_id'] = $log['corp_id'] = $corpId;
        $where['rfm_id']  = $data['rfm_id'];
        if ($aliasModel = ShopCustomerRfmAlias::findOne($where)) {
            $log['old_value']     = (string)$aliasModel->rfm_name;
            $aliasModel->rfm_name = $data['rfm_name'];
            $result               = $aliasModel->update();

            $log['new_value']      = $data['rfm_name'];
            $log['fields_name']    = 'rfm_name';
            $log['remarks']        = 'update';
            $log['primary_key_id'] = $aliasModel->id;
        } else {

            $aliasModel = new ShopCustomerRfmAlias();
            $aliasModel->setAttributes($data);
            if (!$aliasModel->validate()) {
                throw new InvalidDataException(SUtils::modelError($aliasModel));
            }
            $result = $aliasModel->save();

            $log['fields_name'] = 'id';
            $log['old_value']   = '0';
            $log['new_value']   = $aliasModel->id;
            $log['remarks']     = 'insert';
            $log['new_value']   = $log['primary_key_id'] = $aliasModel->id;
        }

        //记录操作日志
        if ($result) {
            $log['table_name']   = self::tableName();
            $log['operator_uid'] = $operatorUid;
            ShopOperationLog::addLog($log);
            //清除缓存
            TagDependency::invalidate(\Yii::$app->cache, 'shop_customer_rfm_'.$corpId);
        }
        return $result;


    }
}
