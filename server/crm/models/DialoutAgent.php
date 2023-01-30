<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_dialout_agent".
 *
 * @property string $id
 * @property string $corp_id 授权的企业ID
 * @property string $exten 坐席id
 * @property string $small_phone 小号
 * @property string $start_time 坐席可用开始时间
 * @property string $expire 坐席到期时间
 * @property string $enable 是否开通，1已开通；0未开通
 * @property string $status 是否可用，1：可用，2：不可用
 * @property string $state 状态，1登出/置闲；2登录/置忙
 * @property string $state_change_time 状态改变时间
 * @property string $last_use_user 最后一个使用该坐席的员工
 * @property string $create_time 创建日期
 */
class DialoutAgent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialout_agent}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'exten', 'status'], 'integer'],
            [['expire', 'create_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '授权的企业ID'),
            'exten' => Yii::t('app', '坐席id'),
            'small_phone' => Yii::t('app', '小号'),
            'start_time' => Yii::t('app', '坐席可用开始时间'),
            'expire' => Yii::t('app', '坐席到期时间'),
            'enable' => Yii::t('app', '是否开通，1已开通；0未开通'),
            'status' => Yii::t('app', '是否可用，1：可用，2：不可用'),
            'state' => Yii::t('app', '状态，1登出/置闲；2登录/置忙'),
            'state_change_time' => Yii::t('app', '状态改变时间'),
            'last_use_user' => Yii::t('app', '最后一个使用该坐席的员工'),
            'create_time' => Yii::t('app', '创建日期'),
        ];
    }

    //判断坐席是否可用
    public static function checkUsableByExten($exten, $corpId = null)
    {
        $date = date("Y-m-d H:i:s");
        $count = static::find()
            ->where(['exten'=>$exten,'status'=>1])
            ->andWhere(['>=', 'expire', $date])
            ->andFilterWhere(['corp_id'=>$corpId])
            ->count();
        return $count > 0;
    }

    //坐席是否已经被分配
    public static function chackIsdistribute($exten, $corpId = null){
        $count = DialoutBindWorkUser::find()
            ->where(['exten'=>$exten])
            ->andFilterWhere(['corp_id'=>$corpId])
            ->count();
        return $count>0;
    }

    //检查坐席是否处于忙碌状态
    public static function cheakState($exten, $corpId = null)
    {
        $result = [
            'state'=>1,
            'user_name'=>'',
        ];
        $data = static::find()
            ->where(['exten'=>$exten,'status'=>1])
            ->andWhere(['>=', 'expire', date("Y-m-d H:i:s")])
            ->andFilterWhere(['corp_id'=>$corpId])
            ->asArray()->all();

        if (!empty($data[0]) && $data[0]['state'] == 2) {
            $result['state'] = 2;
            $userInfo = DialoutBindWorkUser::getUsingUser($exten, $corpId);
            $result['user_name'] = $userInfo['name'] ?? '';
        }
        return $result;
    }



}
