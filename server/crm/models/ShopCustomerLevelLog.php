<?php


namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_customer_level_log}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $cus_id 顾客id
 * @property int $operator_id 操作人ID
 * @property int $before_level_id 之前的等级
 * @property int $level_id 等级ID
 * @property string $name 等级名称
 * @property string $before_name 之前等级名称
 * @property string $add_time 入库时间
 */


class ShopCustomerLevelLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName ()
    {
        return '{{%shop_customer_level_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules ()
    {
        return [
            [['id','corp_id','cus_id','operator_id','before_level_id','level_id'], 'integer'],
            [['before_name','name'], 'string', 'max' => 100],
            [['add_time'], 'safe'],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels ()
    {
        return [
            'id'              => Yii::t('app', 'ID'),
            'corp_id'         => Yii::t('app', '授权的企业ID'),
            'cus_id'          => Yii::t('app', '顾客id'),
            'operator_id'     => Yii::t('app', '操作人ID'),
            'before_level_id' => Yii::t('app', '之前的等级'),
            'level_id'        => Yii::t('app', '等级ID'),
            'name'            => Yii::t('app', '等级名称'),
            'before_name'     => Yii::t('app', '之前等级名称'),
            'add_time'        => Yii::t('app', '入库时间')
        ];
    }

    /**
     * 自动添加时间戳，序列化参数
     * @param $corpId
     * @param $operatorId
     * @param $cusMgs array $key 顾客id $value 顾客等级id-level_id,名称-name等信息
     * @param $levelId int 新等级id
     * @return void
     */
    public static function addLevelLog($corpId,$operatorId,$cusMgs,$levelId){

        $level = ShopCustLevelSet::getData($corpId);
        $levelMsg = [];
        foreach ($level as $k=>$v){
            $levelMsg[$v['id']] = $v;
        }
        if(is_array($cusMgs)){
            $levelLogModel=new ShopCustomerLevelLog();
            $allLog = new ShopCustomerChangeLog();
            foreach ($cusMgs as $cusId=>$beforeValue){
                $beforeLevelId = $beforeValue['level_id'];
                $newLevelName = !empty($levelMsg[$levelId]['title']) ? $levelMsg[$levelId]['title'] : '无等级';
                $beforeLevelName = !empty($levelMsg[$beforeLevelId]['title']) ? $levelMsg[$beforeLevelId]['title'] : '无等级';
                $levelLogModel->isNewRecord=true;
                $setAttributes = [];
                $setAttributes['corp_id']         = $corpId;
                $setAttributes['operator_id']     = $operatorId;
                $setAttributes['cus_id']          = $cusId;
                $setAttributes['before_level_id'] = $beforeLevelId;
                $setAttributes['before_name']     = $beforeLevelName;
                $setAttributes['level_id']        = $levelId;
                $setAttributes['name']            = $newLevelName;
                $levelLogModel->setAttributes($setAttributes);
                if ( !$levelLogModel->validate() ) {
                    throw new InvalidDataException(SUtils::modelError($levelLogModel));
                }
                $levelLogModel->save();
                unset($setAttributes);

                $allLog->isNewRecord = true;
                $setAttributesAll = [];
                $setAttributesAll['corp_id']     = $corpId;
                $setAttributesAll['cus_id']      = $cusId;
                $setAttributesAll['table_name']  = self::tableName();
                $setAttributesAll['log_id']      = (string)$levelLogModel->id;
                $setAttributesAll['title']       = '等级';
                $setAttributesAll['description'] = '【' . $beforeLevelName . '】变成【' . $newLevelName . '】';
                $allLog->setAttributes($setAttributesAll);
                if ( !$allLog->validate() ) {
                    throw new InvalidDataException(SUtils::modelError($allLog));
                }
                unset($setAttributesAll);
                $allLog->save();
                $allLog->id=0;
                $levelLogModel->id=0;
            }
        }
    }
}