<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_contact_way_user_limit}}".
 *
 * @property int            $id
 * @property int            $way_id      渠道活码ID
 * @property int            $user_id     成员ID
 * @property string         $name        员工名称
 * @property int            $limit       每天添加的上限
 * @property int            $create_time 创建时间
 *
 * @property WorkUser       $user
 * @property WorkContactWay $way
 */
class WorkContactWayUserLimit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_user_limit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['way_id', 'user_id', 'limit', 'create_time'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWay::className(), 'targetAttribute' => ['way_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'          => 'ID',
			'way_id'      => '渠道活码ID',
			'user_id'     => '成员ID',
			'name'        => '员工名称',
			'limit'       => '每天添加的上限',
			'create_time' => '创建时间',
		];
	}

	public function dumpData ()
	{
		$result = [
			'id'      => $this->id,
			'user_id' => $this->user_id,
			'name'    => $this->name,
			'limit'   => $this->limit,
		];

		return $result;
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWay()
    {
        return $this->hasOne(WorkContactWay::className(), ['id' => 'way_id']);
    }

	/**
	 * @param $data
	 * @param $wayId
	 *
	 * @throws InvalidDataException
	 */
	public static function add ($data, $wayId)
	{
		static::deleteAll(['way_id' => $wayId]);
		foreach ($data as $val) {
			$userLimit              = new WorkContactWayUserLimit();
			$userLimit->way_id      = $wayId;
			$userLimit->user_id     = $val['user_id'];
			$userLimit->name        = $val['name'];
			$userLimit->limit       = !empty($val['limit']) ? $val['limit'] : 0;
			$userLimit->create_time = time();
			if (!$userLimit->validate() || !$userLimit->save()) {
				throw new InvalidDataException($userLimit . SUtils::modelError($userLimit));
			}
		}
	}

	/**
	 * @param $userDateLimit
	 * @param $wayId
	 *
	 * @return bool
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function deleteLimit ($userDateLimit, $wayId)
	{
		if (!empty($userDateLimit)) {
			$userLimit = WorkContactWayUserLimit::find()->where(['way_id' => $wayId])->all();
			if (!empty($userLimit)) {
				/** @var WorkContactWayUserLimit $li */
				foreach ($userLimit as $li) {
					if (!in_array($li->user_id, $userDateLimit)) {
						$li->delete();
					}
				}
			}
		} else {
			WorkContactWayUserLimit::deleteAll(['way_id' => $wayId]);
		}

		return true;
	}

	/**
	 * @param $userIdLimit
	 * @param $userDateLimit
	 * @param $wayId
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 */
	public static function addData ($userIdLimit, $userDateLimit, $wayId)
	{
		if (!empty($userIdLimit)) {
			foreach ($userIdLimit as $limit) {
				Yii::error($userIdLimit,'$userIdLimit');
				if (!in_array($limit, $userDateLimit)) {
					$workUser = WorkUser::findOne($limit);
					$name     = '';
					if (!empty($workUser)) {
						$name = $workUser->name;
					}
					$userLimit              = new WorkContactWayUserLimit();
					$userLimit->way_id      = $wayId;
					$userLimit->user_id     = $limit;
					$userLimit->name        = $name;
					$userLimit->limit       = 100;
					$userLimit->create_time = time();
					if (!$userLimit->validate() || !$userLimit->save()) {
						throw new InvalidDataException(SUtils::modelError($userLimit));
					}
				}
			}
		}

		return true;
	}

}
