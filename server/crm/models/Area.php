<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%area}}".
	 *
	 * @property int    $id
	 * @property int    $parent_id   父级ID
	 * @property int    $sid         原始的ID
	 * @property string $name        城市名称
	 * @property string $full_name   城市全称
	 * @property string $pinyin      城市名称拼音
	 * @property string $lng         经度
	 * @property string $lat         纬度
	 * @property int    $level       级别
	 * @property string $create_time 创建时间
	 */
	class Area extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%area}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['parent_id', 'sid', 'lng', 'lat', 'level'], 'required'],
				[['parent_id', 'sid', 'level'], 'integer'],
				[['lng', 'lat'], 'number'],
				[['create_time'], 'safe'],
				[['name'], 'string', 'max' => 32],
				[['full_name', 'pinyin'], 'string', 'max' => 64],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'parent_id'   => Yii::t('app', '父级ID'),
				'sid'         => Yii::t('app', '原始的ID'),
				'name'        => Yii::t('app', '城市名称'),
				'full_name'   => Yii::t('app', '城市全称'),
				'pinyin'      => Yii::t('app', '城市名称拼音'),
				'lng'         => Yii::t('app', '经度'),
				'lat'         => Yii::t('app', '纬度'),
				'level'       => Yii::t('app', '级别'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @param bool $withChildren
		 *
		 * @return array
		 */
		public function dumpData ($withChildren = false)
		{
			$result = [
				'id'        => $this->id,
				'sid'       => $this->sid,
				'name'      => $this->name,
				'full_name' => $this->full_name,
				'pinyin'    => $this->pinyin,
				'lng'       => $this->lng,
				'lat'       => $this->lat,
			];

			if ($withChildren) {
				$result['children'] = [];
			}

			return $result;
		}

		/**
		 * @param $areaId
		 *
		 * @return array
		 */
		public static function getChildArea ($areaId)
		{
			$data = [];

			$children = static::findAll(['parent_id' => $areaId]);

			if (!empty($children)) {
				foreach ($children as $area) {
					array_push($data, $area->dumpData(true));
				}
			}

			return $data;
		}

		/**
		 * @param $areaId
		 *
		 * @return array
		 */
		public static function getChildrenArea ($areaId)
		{
			$children = static::getChildArea($areaId);

			if (!empty($children)) {
				foreach ($children as $key => $area) {
					$children[$key]['children'] = static::getChildrenArea($area['id']);
				}
			}

			return $children;
		}

		/**
		 * 获取联动地域值
		 *
		 * @param int $leafId Id
		 * @param int $level  层级 1/2/3
		 *
		 * @return array ['province'=>[],'city'=>[],'district'=>[]]
		 */
		public function getDistrict ($leafId = 0, $level = 3)
		{
			$res = ['province' => [], 'city' => [], 'district' => []];
			switch ($level) {
				case 1 :
					$res['province'] = static::find()->where(['parent_id' => 0])->asArray()->all();
					break;
				case 2 :
					$leafInfo = static::findOne(['id' => $leafId]);
					if ($leafInfo->parent_id == 0) {
						$res['city'] = static::find()->where(['id' => $leafId, 'level' => 2])->asArray()->all();
					} else {
						$res['city'] = static::find()->where(['parent_id' => $leafInfo->parent_id, 'level' => 2])->asArray()->all();
					}
					$res['province'] = static::find()->where(['parent_id' => 0])->asArray()->all();
					break;
				case 3 :
				default :
					$leafInfo = static::findOne(['id' => $leafId]);

					$res['district'] = static::find()->where(['parent_id' => $leafInfo->parent_id])->asArray()->all();

					$NextInfo = static::findOne(['id' => $leafInfo->parent_id]);
					if ($NextInfo->parent_id == 0) {
						$res['city'] = static::find()->where(['id' => $leafInfo->parent_id, 'level' => 2])->asArray()->all();
					} else {
						$res['city'] = static::find()->where(['parent_id' => $NextInfo->parent_id, 'level' => 2])->asArray()->all();
					}
					$res['province'] = static::find()->where(['parent_id' => 0])->asArray()->all();
			}

			return $res;
		}
	}
