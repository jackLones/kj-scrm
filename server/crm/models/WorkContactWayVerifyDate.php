<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_contact_way_verify_date}}".
 *
 * @property int            $id
 * @property int            $way_id      渠道活码ID
 * @property string         $start_time  开始时间
 * @property string         $end_time    结束时间
 * @property int            $create_time 创建时间
 *
 * @property WorkContactWay $way
 */
class WorkContactWayVerifyDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_verify_date}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['way_id', 'create_time'], 'integer'],
            [['start_time', 'end_time'], 'string', 'max' => 30],
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
			'start_time'  => '开始时间',
			'end_time'    => '结束时间',
			'create_time' => '创建时间',
		];
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
			$date              = new WorkContactWayVerifyDate();
			$date->way_id      = $wayId;
			$date->start_time  = $val['start_time'];
			$date->end_time    = $val['end_time'];
			$date->create_time = time();
			if (!$date->validate() || !$date->save()) {
				throw new InvalidDataException($date . SUtils::modelError($date));
			}
		}
	}

}
