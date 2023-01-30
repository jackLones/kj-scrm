<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_dialout_config".
 *
 * @property int $id
 * @property int $corp_id 企业微信ID
 * @property int $uid 用户ID
 * @property string $exten_money 坐席价格（含月租） 元/月/个
 * @property string $phone_money 花费价格  元/分钟
 * @property string $balance 账户余额
 * @property string $remark
 * @property string $business_license_url 营业执照
 * @property string $number_attribute 客户属性
 * @property string $customer_words_art 客户话术
 * @property string $acknowledgement_url 承诺函
 * @property string $corporate_identity_card_positive_url 身份证正面照片
 * @property string $corporate_identity_card_reverse_url 身份证反面照片
 * @property string $operator_identity_card_positive_url 经办人身份证正面照片
 * @property string $operator_identity_card_reverse_url 经办人身份证反面照片
 * @property int $status 状态
 * @property string $refuse_reason 拒绝原因
 * @property string $create_time 创建时间
 * @property string $monthly_money 月租
 *
 * @property User $u
 */
class DialoutConfig extends \yii\db\ActiveRecord
{
    const STATUS_AUDIT = 2;
    const STATUS_SUCCESS = 1;
    const STATUS_REFUSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialout_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'uid', 'business_license_url', 'number_attribute', 'customer_words_art', 'corporate_identity_card_positive_url', 'corporate_identity_card_reverse_url'], 'required'],
            [['corp_id', 'uid', 'status'], 'integer'],
            [['exten_money', 'phone_money', 'balance', 'monthly_money'], 'number'],
            [['remark'], 'string'],
            [['create_time'], 'safe'],
            [['business_license_url', 'acknowledgement_url', 'corporate_identity_card_positive_url', 'corporate_identity_card_reverse_url', 'operator_identity_card_positive_url', 'operator_identity_card_reverse_url'], 'string', 'max' => 255],
            [['number_attribute', 'refuse_reason'], 'string', 'max' => 800],
            [['customer_words_art'], 'string', 'max' => 2000],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
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
            'uid' => Yii::t('app', '用户ID'),
            'exten_money' => Yii::t('app', '坐席价格（含月租） 元/月/个'),
            'phone_money' => Yii::t('app', '花费价格  元/分钟'),
            'balance' => Yii::t('app', '账户余额'),
            'remark' => Yii::t('app', 'Remark'),
            'business_license_url' => Yii::t('app', '营业执照'),
            'number_attribute' => Yii::t('app', '号码属性'),
            'customer_words_art' => Yii::t('app', '客户话术'),
            'acknowledgement_url' => Yii::t('app', '承诺函'),
            'corporate_identity_card_positive_url' => Yii::t('app', '身份证正面照片'),
            'corporate_identity_card_reverse_url' => Yii::t('app', '身份证反面照片'),
            'operator_identity_card_positive_url' => Yii::t('app', '经办人身份证正面照片'),
            'operator_identity_card_reverse_url' => Yii::t('app', '经办人身份证反面照片'),
            'status' => Yii::t('app', '状态'),
            'refuse_reason' => Yii::t('app', '拒绝原因'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }

    public function getWorkCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['uid' => 'uid']);
    }
}
