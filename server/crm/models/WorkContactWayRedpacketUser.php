<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_contact_way_redpacket_user}}".
 *
 * @property int $id
 * @property int $config_id 红包活动渠道活码表id
 * @property int $user_id 成员ID
 *
 * @property WorkContactWayRedpacket $config
 * @property WorkUser $user
 */
class WorkContactWayRedpacketUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['config_id', 'user_id'], 'integer'],
            [['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayRedpacket::className(), 'targetAttribute' => ['config_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('app', '成员ID'),
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
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

	/**
	 * @param $configId
	 * @param $userId
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 */
	public static function setData ($configId, $userId)
	{
		$wayUser = static::findOne(['config_id' => $configId, 'user_id' => $userId]);

		if (empty($wayUser)) {
			$wayUser = new WorkContactWayRedpacketUser();
		}

		$wayUser->config_id = $configId;
		$wayUser->user_id   = $userId;

		if ($wayUser->dirtyAttributes) {
			if (!$wayUser->validate() || !$wayUser->save()) {
				throw new InvalidDataException(SUtils::modelError($wayUser));
			}
		}

		return $wayUser->id;
	}
}
