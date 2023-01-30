<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%work_import_customer_detail}}".
 *
 * @property int $id
 * @property int $corp_id 企业微信id
 * @property int $import_id 导入表id
 * @property int $user_id 成员ID
 * @property string $phone 手机号
 * @property string $nickname 微信昵称
 * @property string $name 姓名
 * @property int $sex 性别 1男2女0未知
 * @property string $area 区域
 * @property string $des 备注
 * @property string $distribution_records 分配员工记录
 * @property int $time 创建时间
 * @property int $is_add 是否添加 1是 0否 2待分配
 * @property int $add_time 修改时间
 * @property int $external_follow_id 成员客户表ID
 */
class WorkImportCustomerDetail extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_import_customer_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'phone'], 'required'],
            [['corp_id', 'import_id', 'user_id', 'sex', 'time', 'is_add', 'add_time', 'external_follow_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['distribution_records'], 'string'],
            [['nickname', 'name', 'area', 'des'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
	    return [
		    'id'                 => 'ID',
		    'corp_id'            => 'Corp ID',
		    'import_id'          => 'Import ID',
		    'user_id'            => 'User ID',
		    'phone'              => 'Phone',
		    'nickname'           => 'Nickname',
		    'name'               => 'Name',
		    'sex'                => 'Sex',
		    'area'               => 'Area',
		    'des'                => 'Des',
		    'time'               => 'Time',
		    'is_add'             => 'Is Add',
		    'add_time'           => 'Add Time',
		    'external_follow_id' => 'External Follow ID',
		    'distribution_records' => '客户分配记录',
	    ];
    }

	/**
	 *
	 * @return object|\yii\db\Connection|null
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getDb ()
	{
		return Yii::$app->get('mdb');
	}

	//导入客户
	public static function setCustomer ($data)
	{
		$corp_id   = $data['corp_id'];
		$import_id = $data['import_id'];
		$user_id   = $data['user_id'];
		$phone     = trim($data['phone']);

		if (empty($corp_id) || empty($import_id) || empty($user_id) || empty($phone)) {
			return 'skipPhone';
		}
		if (!empty($phone)) {
			if (!preg_match("/^((13[0-9])|(14[0-9])|(15([0-9]))|(16([0-9]))|(17([0-9]))|(18[0-9])|(19[0-9]))\d{8}$/", $phone)) {
				return 'skipPhone';
			}
		}
		//是否导入过
		$hasImport = static::findOne(['corp_id' => $corp_id, 'phone' => $phone]);
		if (!empty($hasImport)) {
			return 'skip';
		}
		//是否已添加客户
		$hasExternalFollow = WorkExternalContactFollowUser::findOne(['user_id' => $user_id, 'remark_mobiles' => $phone, 'del_type' => 0]);
		if (!empty($hasExternalFollow)) {
			return 'skip';
		}
        $distributionRecords = [['add_time'=>date('Y-m-d H:i:s'),'user_id' => $user_id]];
		$importDetail            = new WorkImportCustomerDetail();
		$importDetail->corp_id   = $corp_id;
		$importDetail->import_id = $import_id;
		$importDetail->user_id   = $user_id;
		$importDetail->phone     = $phone;
		$importDetail->nickname  = !empty($data['nickname']) ? $data['nickname'] : '';
		$importDetail->name      = !empty($data['name']) ? $data['name'] : '';
		$importDetail->area      = !empty($data['area']) ? $data['area'] : '';
		$importDetail->des       = !empty($data['des']) ? mb_substr($data['des'], 0, 10, 'utf-8') : '';
		$importDetail->distribution_records = json_encode($distributionRecords);

		if ($data['sex'] == '男') {
			$sex = 1;
		} elseif ($data['sex'] == '女') {
			$sex = 2;
		} else {
			$sex = 0;
		}
		$importDetail->sex  = $sex;
		$importDetail->time = time();

		if (!$importDetail->validate() || !$importDetail->save()) {
			return 'skip';
		}

		return 'insert';
	}

    /**
     * 二次分配导入的客户
     * @param array $data
     * @return bool
     */
	public static function distributionCustomers(array $data)
    {
        $corp_id   = $data['corp_id'];
        $user_id   = $data['user_id'];
        $customer_id = $data['customer_id'];

        $customer = self::findOne(['id' => $customer_id, 'corp_id' => $corp_id]);
        if(empty($customer) || $customer->is_add == 1 ) return false;//已添加的客户不可以二次分配
        //是否导入过
//        $hasImport = static::findOne(['corp_id' => $corp_id, 'phone' => $customer->phone, 'user_id' => $user_id]);
//        if($hasImport) return 'exist';//已经分配的不需要重新分配了
        //是否已添加客户
//        $hasExternalFollow = WorkExternalContactFollowUser::findOne(['user_id' => $customer->user_id, 'remark_mobiles' => $customer->phone, 'del_type' => 0]);
//        if (!empty($hasExternalFollow)) return 'exist';

        $customer->user_id   = $user_id;
        $distributionRecords = $customer->distribution_records ? json_decode($customer->distribution_records,true) : [];
        array_push($distributionRecords,['add_time'=>date('Y-m-d H:i:s'),'user_id' => $user_id]);
        $customer->distribution_records = json_encode($distributionRecords);
        $customer->add_time = time();
        $customer->is_add = 0;//添加状态重置为待添加

        return $customer->save();
    }
}
