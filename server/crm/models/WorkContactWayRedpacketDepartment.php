<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_contact_way_redpacket_department}}".
 *
 * @property int $id
 * @property int $config_id 红包活动渠道活码表id
 * @property int $department_id 部门ID
 *
 * @property WorkContactWayRedpacket $config
 * @property WorkDepartment $department
 */
class WorkContactWayRedpacketDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket_department}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['config_id', 'department_id'], 'integer'],
            [['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayRedpacket::className(), 'targetAttribute' => ['config_id' => 'id']],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkDepartment::className(), 'targetAttribute' => ['department_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'config_id' => Yii::t('app', '红包活动渠道活码表id'),
            'department_id' => Yii::t('app', '部门ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasOne(WorkContactWayRedpacket::className(), ['id' => 'config_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(WorkDepartment::className(), ['id' => 'department_id']);
    }

	/**
	 * @param $configId
	 * @param $partyId
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 */
	public static function setData ($configId, $partyId)
	{
		$wayDepartment = static::findOne(['config_id' => $configId, 'department_id' => $partyId]);

		if (empty($wayDepartment)) {
			$wayDepartment = new WorkContactWayRedpacketDepartment();
		}

		$wayDepartment->config_id     = $configId;
		$wayDepartment->department_id = $partyId;

		if ($wayDepartment->dirtyAttributes) {
			if (!$wayDepartment->validate() || !$wayDepartment->save()) {
				throw new InvalidDataException(SUtils::modelError($wayDepartment));
			}
		}

		return $wayDepartment->id;
	}
}
