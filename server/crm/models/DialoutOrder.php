<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_dialout_order".
 *
 * @property int $id
 * @property int $corp_id
 * @property int $exten 坐席号
 * @property int $type 1:花费充值；2：话费消耗；3：坐席充值；4：开通坐席；5：续费坐席
 * @property string $money 出账/进账 金额
 * @property int $status 1:已到账；2：未到账
 * @property string $create_time
 */
class DialoutOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialout_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'type', 'money', 'status'], 'required'],
            [['corp_id', 'exten', 'type', 'status'], 'integer'],
            [['money'], 'number'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', 'Corp ID'),
            'exten' => Yii::t('app', '坐席号'),
            'type' => Yii::t('app', '1:花费充值；2：话费消耗；3：坐席充值；4：开通坐席；5：续费坐席'),
            'money' => Yii::t('app', '出账/进账 金额'),
            'status' => Yii::t('app', '1:已到账；2：未到账'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }
}
