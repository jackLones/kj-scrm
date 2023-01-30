<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\DateUtil;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_contact_way_redpacket_date_user}}".
 *
 * @property int $id
 * @property int $date_id 红包活动日期表ID
 * @property string $time 具体时间
 * @property string $user_key 用户选择的key值
 * @property string $department 部门id
 * @property string $create_time 创建时间
 *
 * @property WorkContactWayRedpacketDate $date
 */
class WorkContactWayRedpacketDateUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket_date_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_id'], 'required'],
            [['date_id'], 'integer'],
            [['create_time'], 'safe'],
            [['time'], 'string', 'max' => 32],
            [['department'], 'string', 'max' => 255],
            [['date_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayRedpacketDate::className(), 'targetAttribute' => ['date_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_id' => Yii::t('app', '红包活动日期表ID'),
            'time' => Yii::t('app', '具体时间'),
            'user_key' => Yii::t('app', '用户选择的key值'),
            'department' => Yii::t('app', '部门id'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDate()
    {
        return $this->hasOne(WorkContactWayRedpacketDate::className(), ['id' => 'date_id']);
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

	/**
	 * @param $data
	 * @param $id
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 */
	public static function setData ($data, $id)
	{
		$newDate = [];
		try {
			static::deleteAll(['date_id' => $id]);
			foreach ($data as $key => $val) {
				$start_time = $val['start_time'];
				$end_time   = $val['end_time'];
				$department = isset($val['party']) && !empty($val['party']) ? $val['party'] : '';
				array_push($newDate, $start_time . '-' . $end_time);
				$dateUser              = new WorkContactWayRedpacketDateUser();
				$dateUser->create_time = DateUtil::getCurrentTime();
				$dateUser->date_id     = $id;
				$dateUser->time        = $start_time . '-' . $end_time;
				$dateUser->department  = !empty($department) ? json_encode($department) : '';
				$userList              = $val['userList'];
				if (is_array($val['userList'])) {
					$uList = $val['userList'];
					foreach ($uList as $k => $v) {
						if (isset($v['is_del']) && $v['is_del'] == 1) {
							unset($uList[$k]);
						}
					}
					$userList = json_encode($uList);
				}
				$dateUser->user_key = !empty($userList) ? $userList : '';
				if (!$dateUser->validate() || !$dateUser->save()) {
					throw new InvalidDataException(SUtils::modelError($dateUser));
				}
			}
		} catch (\Exception $e) {
			$message = $e->getMessage();
			\Yii::error($message, '$message-2');
			throw new InvalidDataException($message);
		}

		return true;
	}
}
