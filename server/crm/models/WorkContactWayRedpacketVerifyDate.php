<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_contact_way_redpacket_verify_date}}".
 *
 * @property int $id
 * @property int $way_id 红包活动渠道活码ID
 * @property string $start_time 开始时间
 * @property string $end_time 结束时间
 * @property int $create_time 创建时间
 *
 * @property WorkContactWayRedpacket $way
 */
class WorkContactWayRedpacketVerifyDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket_verify_date}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['way_id', 'create_time'], 'integer'],
            [['start_time', 'end_time'], 'string', 'max' => 30],
            [['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayRedpacket::className(), 'targetAttribute' => ['way_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'way_id' => Yii::t('app', '红包活动渠道活码ID'),
            'start_time' => Yii::t('app', '开始时间'),
            'end_time' => Yii::t('app', '结束时间'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWay()
    {
        return $this->hasOne(WorkContactWayRedpacket::className(), ['id' => 'way_id']);
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
			$date              = new WorkContactWayRedpacketVerifyDate();
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
