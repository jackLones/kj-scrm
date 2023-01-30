<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%wait_project_follow}}".
 *
 * @property int    $id
 * @property int    $customer_task_id 客户任务ID
 * @property int    $task_id          任务ID
 * @property int    $external_userid  企微客户外部联系人
 * @property int    $sea_id           公海客户
 * @property int    $status           阶段状态ID
 * @property string $per              进度百分比
 * @property string $per_desc         进度说明
 * @property int    $create_time      创建时间
 */
class WaitProjectFollow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wait_project_follow}}';
    }

    /**
     * {@inheritdoc}
     */
	public function rules ()
	{
		return [
			[['customer_task_id', 'task_id', 'external_userid', 'sea_id', 'status', 'create_time'], 'integer'],
			[['per'], 'string', 'max' => 32],
			[['per_desc'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels ()
	{
		return [
			'id'               => 'ID',
			'customer_task_id' => '客户任务ID',
			'task_id'          => '客户任务ID',
			'external_userid'  => '企微客户外部联系人',
			'sea_id'           => '公海客户',
			'status'           => '阶段状态ID',
			'per'              => '进度百分比',
			'per_desc'         => '进度说明',
			'create_time'      => '创建时间',
		];
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 */
	public static function add ($data)
	{
		$waitCustom = WaitCustomerTask::findOne($data['id']);
		if (!empty($waitCustom)) {
			$waitStatus = WaitStatus::find()->where(['uid' => $data['uid'], 'is_del' => 0])->orderBy(['sort' => SORT_DESC])->one();
			if ($data['status'] == $waitStatus->id) {
				//说明已经执行到最后一个阶段
				if ($data['per'] != 100) {
					throw new InvalidDataException('当选择最后一个项目状态，请将项目进度填写100');
				}
				$waitCustom->finish_time = time();
				$waitCustom->is_finish   = 1;
				WaitUserRemind::deleteAll(['custom_id'=>$waitCustom->id,'task_id'=>$waitCustom->task_id]);
			}
			$waitCustom->status   = $data['status'];
			$waitCustom->per      = strval($data['per']);
			$waitCustom->per_desc = $data['per_desc'];
			$waitCustom->save();
		} else {
			throw new InvalidDataException('参数错误');
		}
		$follow                   = new WaitProjectFollow();
		$follow->create_time      = time();
		$follow->customer_task_id = $data['id'];
		$follow->task_id          = $data['task_id'];
		$follow->external_userid  = $data['external_userid'];
		$follow->sea_id           = $data['sea_id'];
		$follow->status           = $data['status'];
		$follow->per              = strval($data['per']);
		$follow->per_desc         = $data['per_desc'];
		if (!$follow->validate() || !$follow->save()) {
			throw new InvalidDataException('失败：' . SUtils::modelError($follow));
		}

		return true;
	}


}
