<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_operation_log}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property string $table_name 表名
 * @property string $fields_name 字段名
 * @property int $primary_key_id 对应主键id
 * @property string $old_value 旧值
 * @property string $new_value 新值
 * @property string $remarks 备注
 * @property int $operator_uid 操作⼈ID
 * @property string|null $add_time 操作时间
 */
class ShopOperationLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_operation_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'primary_key_id', 'operator_uid'], 'integer'],
            [['old_value', 'new_value'], 'string'],
            [['add_time'], 'safe'],
            [['table_name', 'fields_name', 'remarks'], 'string', 'max' => 100],
        ];
    }



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => Yii::t('app', 'ID'),
            'corp_id'        => Yii::t('app', '授权的企业ID'),
            'table_name'     => Yii::t('app', '表名'),
            'fields_name'    => Yii::t('app', '字段名'),
            'primary_key_id' => Yii::t('app', '对应主键id'),
            'old_value'      => Yii::t('app', '旧值'),
            'new_value'      => Yii::t('app', '新值'),
            'remarks'        => Yii::t('app', '备注'),
            'operator_uid'   => Yii::t('app', '操作⼈ID'),
            'add_time'       => Yii::t('app', '操作时间'),
        ];
    }

    public static function addLog($data){
        $data['new_value'] = (string)$data['new_value'];
        $data['old_value'] = (string)$data['old_value'];
        $logModel = new ShopOperationLog();
        $logModel->setAttributes($data);
        if (!$logModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($logModel));
        }
        return $logModel->save();
    }
}
