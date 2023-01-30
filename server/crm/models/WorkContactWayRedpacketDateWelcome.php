<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%work_contact_way_redpacket_date_welcome}}".
 *
 * @property int $id
 * @property int $way_id 红包活动渠道活码ID
 * @property int $type 1周2日期
 * @property string $start_date 开始日期
 * @property string $end_date 结束日期
 * @property string $day 周几
 * @property int $create_time 创建时间
 *
 * @property WorkContactWayRedpacket $way
 * @property WorkContactWayRedpacketDateWelcomeContent[] $workContactWayRedpacketDateWelcomeContents
 */
class WorkContactWayRedpacketDateWelcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_redpacket_date_welcome}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['way_id', 'type', 'create_time'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['day'], 'string', 'max' => 255],
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
            'type' => Yii::t('app', '1周2日期'),
            'start_date' => Yii::t('app', '开始日期'),
            'end_date' => Yii::t('app', '结束日期'),
            'day' => Yii::t('app', '周几'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getWorkContactWayRedpacketDateWelcomeContents()
    {
        return $this->hasMany(WorkContactWayRedpacketDateWelcomeContent::className(), ['date_id' => 'id']);
    }

	/**
	 * @param $data
	 * @param $wayId
	 * @param $type
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException
	 */
	public static function add ($data, $wayId, $type)
	{
		$dateWel = static::find()->where(['way_id' => $wayId, 'type' => $type])->all();
		if (!empty($dateWel)) {
			/** @var WorkContactWayDateWelcome $wel */
			foreach ($dateWel as $wel) {
				WorkContactWayRedpacketDateWelcomeContent::deleteAll(['date_id' => $wel->id]);
			}
		}
		static::deleteAll(['way_id' => $wayId, 'type' => $type]);
		foreach ($data as $val) {
			if (!empty($val['date'])) {
				if ($type == 2) {
					$welcome = self::find()->where(['way_id' => $wayId, 'type' => $type, 'start_date' => $val['date'][0], 'end_date' => $val['date'][1]])->one();
				} else {
					$welcome = self::find()->where(['way_id' => $wayId, 'type' => $type, 'day' => Json::encode($val['date'])])->one();
				}
				if (empty($welcome)) {
					$welcome              = new WorkContactWayRedpacketDateWelcome();
					$welcome->create_time = time();
				}
				$welcome->way_id = $wayId;
				$welcome->type   = $type;
				if ($type == 2) {
					$welcome->start_date = $val['date'][0];
					$welcome->end_date   = $val['date'][1];
				} else {
					$welcome->day = Json::encode($val['date']);
				}
				if (!$welcome->validate() || !$welcome->save()) {
					throw new InvalidDataException($welcome . SUtils::modelError($welcome));
				}
				WorkContactWayRedpacketDateWelcomeContent::add($val['time'], $welcome->id);
			} else {
				throw new InvalidDataException('选择周期不能为空');
			}
		}

	}
}
