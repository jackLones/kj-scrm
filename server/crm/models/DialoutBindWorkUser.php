<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_dialout_bind_work_user".
 *
 * @property string $id
 * @property string $corp_id 授权的企业ID
 * @property string $user_id 员工id
 * @property string $exten 坐席id
 * @property string $status 状态；1，启用；2禁用
 * @property string $create_time 创建日期
 */
class DialoutBindWorkUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialout_bind_work_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'exten', 'status'], 'integer'],
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
            'corp_id' => Yii::t('app', '授权的企业ID'),
            'user_id' => Yii::t('app', '员工id'),
            'exten' => Yii::t('app', '坐席id'),
            'status' => Yii::t('app', '状态'),
            'create_time' => Yii::t('app', '创建日期'),
        ];
    }

    //坐席当前被哪个员工使用
    public static function getUsingUser($exten, $corpId)
    {
        $userInfo = static::find()
            ->select(['a.*','b.name'])
            ->alias('a')
            ->innerJoin(WorkUser::tableName() . ' b', 'a.user_id=b.id')
            ->where(['a.exten'=>$exten,'a.corp_id'=>$corpId,'a.status'=>1])
            ->asArray()
            ->all();

        return $userInfo ? $userInfo[0] : [];
    }

    //检查是否是正在使用的员工
    public static function checkUsing($corpId, $userIds){
        $alreadBind = DialoutBindWorkUser::find()
            ->alias('a')
            ->where(['a.corp_id'=>$corpId])
            ->andWhere(['a.user_id'=>$userIds,'a.status'=>1])
            ->count();

        if ($alreadBind) {
            return true;
        }
        return false;
    }

    //判断主账号或者子账号是否绑定了坐席
    public static function isBindExten($corpId, $uid, $subId) {
        $exten = '';
        if ($subId) {
            $user = SubUser::find()
                ->alias('a')
                ->select(['b.id'])
                ->innerJoin(WorkUser::tableName() . ' b', 'a.account=b.mobile')
                ->where(['b.corp_id'=>$corpId,'a.sub_id'=>$subId])
                ->asArray()
                ->all();
        }else{
            $user = User::find()
                ->alias('a')
                ->select(['b.id'])
                ->innerJoin(WorkUser::tableName() . ' b', 'a.account=b.mobile')
                ->where(['b.corp_id'=>$corpId,'a.uid'=>$uid])
                ->asArray()
                ->all();
        }
        if (!empty($user[0]['id'])) {
            $user_id = $user[0]['id'];
            $extenInfo = static::find()
                ->select(['a.exten'])
                ->alias('a')
                ->innerJoin(DialoutAgent::tableName() . ' b', 'a.exten=b.exten')
                ->where(['a.user_id'=>$user_id,'a.status'=>1, 'b.status'=>1, 'a.corp_id'=>$corpId, 'b.corp_id'=>$corpId])
                ->andWhere(['>=', 'expire', date("Y-m-d H:i:s")])
                ->asArray()
                ->all();

            if (!empty($extenInfo[0]['exten'])) {
                $exten = $user_id . "_" . $extenInfo[0]['exten'];
            }
        }

        return $exten;
    }
}
