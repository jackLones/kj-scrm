<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use yii\debug\panels\EventPanel;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%work_contact_way_date_welcome}}".
 *
 * @property int                                $id
 * @property int                                $way_id      渠道活码ID
 * @property int                                $type        1周2日期
 * @property string                             $start_date  开始日期
 * @property string                             $end_date    结束日期
 * @property string                             $day         周几
 * @property int                                $create_time 创建时间
 *
 * @property WorkContactWay                     $way
 * @property WorkContactWayDateWelcomeContent[] $workContactWayDateWelcomeContents
 */
class WorkContactWayDateWelcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_contact_way_date_welcome}}';
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
			'type'        => '1周2日期',
			'start_date'  => '开始日期',
			'end_date'    => '结束日期',
			'day'         => '周几',
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
     * @return \yii\db\ActiveQuery
     */
    public function getWorkContactWayDateWelcomeContents()
    {
        return $this->hasMany(WorkContactWayDateWelcomeContent::className(), ['date_id' => 'id']);
    }

	/**
	 * @param $data
	 * @param $wayId
	 * @param $type
	 * @param $corpId
	 * @param $uid
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException
	 */
	public static function add ($data, $wayId, $type, $corpId, $uid)
	{
		$dateWel = static::find()->where(['way_id' => $wayId, 'type' => $type])->all();
		if (!empty($dateWel)) {
			/** @var WorkContactWayDateWelcome $wel */
			$date_ids = [];
			foreach ($dateWel as $wel) {
				$date_ids[] = $wel->id;
				RadarLink::updateAll(['status'=>0],['and',['associat_type'=>1,'associat_id'=>$wayId],['like','associat_param','content_'.$wel->id]]);
				//RadarLink::deleteAll(['and',['associat_type'=>1,'associat_id'=>$wayId],['like','associat_param','content_'.$wel->id]]);
			}
			if (!empty($date_ids)) {
				WorkContactWayDateWelcomeContent::deleteAll(['date_id'=>$date_ids]);
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
					$welcome              = new self();
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
				WorkContactWayDateWelcomeContent::add($val['time'], $welcome->id, $corpId, $uid,$wayId);
			} else {
				throw new InvalidDataException('选择周期不能为空');
			}
		}

	}
}
