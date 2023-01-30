<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%wait_project_remind}}".
 *
 * @property int         $id
 * @property int         $project_id  待办项目ID
 * @property int         $type        1 预计结束时间前 2 项目超时
 * @property int         $days        天数
 * @property int         $create_time 创建时间
 *
 * @property WaitProject $project
 */
class WaitProjectRemind extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName ()
	{
		return '{{%wait_project_remind}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules ()
	{
		return [
			[['project_id', 'type', 'days', 'create_time'], 'integer'],
			[['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => WaitProject::className(), 'targetAttribute' => ['project_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels ()
	{
		return [
			'id'          => 'ID',
			'project_id'  => '待办项目ID',
			'type'        => '1 预计结束时间前 2 项目超时',
			'days'        => '天数',
			'create_time' => '创建时间',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProject ()
	{
		return $this->hasOne(WaitProject::className(), ['id' => 'project_id']);
	}

	public function dumpData ()
	{
		return [
			'type' => $this->type,
			'days' => $this->days
		];
	}

	/**
	 * @param $data
	 * @param $projectId
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 */
	public static function add ($data, $projectId)
	{
		static::deleteAll(['project_id' => $projectId]);
		foreach ($data as $val) {
			$remind              = new WaitProjectRemind();
			$remind->create_time = time();
			$remind->project_id  = $projectId;
			$remind->type        = $val['type'];
			$remind->days        = !empty($val['days']) ? $val['days'] : 0;
			if (!$remind->validate() || !$remind->save()) {
				throw new InvalidDataException('创建失败：' . SUtils::modelError($remind));
			}
		}

		return true;
	}

	/**
	 * @param $id
	 *
	 * @return array
	 *
	 */
	public static function getData ($id)
	{
		$data   = [];
		$remind = self::find()->where(['project_id' => $id])->all();
		if (!empty($remind)) {
			/** @var WaitProjectRemind $val */
			foreach ($remind as $val) {
				array_push($data, $val->dumpData());
			}
		}

		return $data;
	}


}
