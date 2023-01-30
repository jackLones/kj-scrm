<?php

namespace app\models;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\util\SUtils;
use Elphin\IcoFileLoader\IcoFileService;
use Yii;

/**
 * This is the model class for table "{{%work_sop_time}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $sop_id 规则id
 * @property int $time_type 提醒时间分类，1：x时x分后、2：x天后时间
 * @property string $time_one 时间一
 * @property string $time_two 时间二
 * @property int $is_del 是否删除1是0否
 * @property int $create_time 创建时间
 *
 * @property WorkCorp $corp
 * @property WorkSop $sop
 */
class WorkSopTime extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_sop_time}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'sop_id', 'time_type', 'is_del', 'create_time'], 'integer'],
            [['time_one', 'time_two'], 'string', 'max' => 32],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['sop_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkSop::className(), 'targetAttribute' => ['sop_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'          => Yii::t('app', 'ID'),
			'corp_id'     => Yii::t('app', '授权的企业ID'),
			'sop_id'      => Yii::t('app', '规则id'),
			'time_type'   => Yii::t('app', '提醒时间分类，1：x时x分后、2：x天后时间'),
			'time_one'    => Yii::t('app', '时间一'),
			'time_two'    => Yii::t('app', '时间二'),
			'is_del'      => Yii::t('app', '是否删除1是0否'),
			'create_time' => Yii::t('app', '创建时间'),
		];
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
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSop()
    {
        return $this->hasOne(WorkSop::className(), ['id' => 'sop_id']);
    }

	/**
	 * @param $data
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function setSopTime ($data)
	{
		$sop_time_id = $data['sop_time_id'];

		if ($sop_time_id) {
			$sopTime = static::findOne($sop_time_id);
			if (empty($sopTime)) {
				throw new InvalidDataException('SOP规则时间数据错误');
			}
		} else {
			$sopTime              = new WorkSopTime();
			$sopTime->corp_id     = $data['corp_id'];
			$sopTime->sop_id      = $data['sop_id'];
			$sopTime->create_time = time();
		}
		$sopTime->time_type = $data['time_type'];
		$sopTime->time_one  = $data['time_one'];
		$sopTime->time_two  = $data['time_two'];
		$sopTime->is_del    = 0;

		if (!$sopTime->validate() || !$sopTime->save()) {
			throw new InvalidDataException(SUtils::modelError($sopTime));
		}

		$contentData = $data['contentData'];

		if ($sop_time_id) {
			WorkSopTimeContent::updateAll(['status' => 0], ['sop_time_id' => $sop_time_id, 'status' => 1]);
		}

		foreach ($contentData as $content) {
			$sopContent = [];
			if (isset($content['sop_content_id']) && $content['sop_content_id'] > 0) {
				$sopContent = WorkSopTimeContent::findOne($content['sop_content_id']);
			}
			if (empty($sopContent)) {
				$sopContent              = new WorkSopTimeContent();
				$sopContent->sop_time_id = $sopTime->id;
				$sopContent->create_time = time();
			}
			$sopContent->type    = $content['file_type'];
			//beenlee 同步更新至内容引擎
			self::getAttachmentContent($content,$data['sop_id']);

			$sopContent->content = json_encode($content);
			$sopContent->status  = 1;

			$sopContent->save();
		}

		return $sopTime->id;
	}

	/**
	 * @param $sop_id
	 * @param $subUser 子帐号可见员工
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function getSopTime ($sop_id, $subUser)
	{
		$workSopTime = static::find()->where(['sop_id' => $sop_id, 'is_del' => 0])->all();

		$timeData = [];
		foreach ($workSopTime as $time) {
			$timeD                 = [];
			$timeD['sop_time_id']  = $time->id;
			$timeD['time_type']    = $time->time_type;
			$timeD['time_one']     = $time->time_one;
			$timeD['time_two']     = $time->time_two;
			if (!empty($subUser)){
				$whereData = ['sop_time_id' => $time->id, 'status' => 1, 'user_id' => $subUser];
			}else{
				$whereData = ['sop_time_id' => $time->id, 'status' => 1];
			}
			$timeD['over_num']     = WorkSopMsgSending::find()->where($whereData)->andWhere(['is_over' => 1])->count();
			$timeD['not_over_num'] = WorkSopMsgSending::find()->where($whereData)->andWhere(['is_over' => 0])->count();

			$sopContent  = WorkSopTimeContent::find()->where(['sop_time_id' => $time->id, 'status' => 1])->all();
			$contentData = [];
			foreach ($sopContent as $content) {
				$contentD                   = json_decode($content->content, true);
				$contentD['sop_content_id'] = $content->id;

				$contentData[] = $contentD;
			}
			$timeD['contentData'] = $contentData;

			$timeData[] = $timeD;
		}

		return $timeData;
	}

	public static function getAttachmentContent (&$content, $sop_id)
	{
		$workSop = WorkSop::findOne(['id' => $sop_id]);
		if ($content['material_sync'] == 1) {
			$is_temp = 0;
		} else {
			$is_temp = 1;
		}
		switch ($content['file_type']) {
			case 1:
				if (!empty($content['uploadImg']) && !isset($content['uploadImg'][0]['id'])) {
					$local_path = $content['uploadImg'][0]['local_path'];
				}
				break;
			case 3:
				if (!empty($content['uploadVideo']) && !isset($content['uploadVideo']['id'])) {
					$local_path = $content['uploadVideo']['local_path'];
				}
				break;
			case 4:
				if (!empty($content['uploadText']) && !isset($content['uploadText']['id'])) {
					$local_path = $content['uploadText']['url'];
				}

				break;
		}

		if (isset($local_path) && !empty($local_path)) {
			$extension = pathinfo($local_path, PATHINFO_EXTENSION);
			if ($extension == 'ico') {
				$loader = new IcoFileService;
				$im     = $loader->extractIcon(\Yii::getAlias('@app') . $local_path, 80, 80);

				//beenlee $im is a GD image resource, so we could, for example, save this as a PNG
				$local_path = str_ireplace('.ico', '.png', $local_path);
				imagepng($im, \Yii::getAlias('@app') . $local_path);
			}

			//beenlee 同步至内容引擎 导入微信素材库
			$imgDate = [
				'uid'             => $workSop->uid,
				'sub_id'          => $workSop->sub_id,
				'isMasterAccount' => ($workSop->uid == $workSop->sub_id) ? 1 : 2,
				'file_type'       => $content['file_type'],
				'group_id'        => 0,
				'local_path'      => $local_path,
				'is_temp'         => $is_temp,
			];

			if ($content['file_type'] == 4) {
				$imgDate['title']    = $content['uploadText']['title'];
				$imgDate['content']  = $content['uploadText']['description'];
				$imgDate['jump_url'] = $content['uploadText']['link'];
				$imgDate['pic_url']  = $local_path;
			}

			try {
				$tmp_id = Attachment::syncAttachment($imgDate);
				if ($tmp_id) {
					switch ($content['file_type']) {
						case 1:
							$content['uploadImg']['id'] = $tmp_id;
							break;
						case 3:
							$content['uploadVideo']['id'] = $tmp_id;
							break;
						case 4:
							$content['uploadText']['id'] = $tmp_id;
					}
				}
			} catch (\Exception $e) {
				throw new InvalidParameterException($e->getMessage());
			}
		}
	}
}
