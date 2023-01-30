<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;
	use app\util\DateUtil;

	/**
	 * This is the model class for table "{{%package_menu}}".
	 *
	 * @property int     $id
	 * @property int     $package_id  套餐id
	 * @property int     $menu_id     菜单id
	 * @property string  $use_limit   功能限制使用数量
	 * @property int     $status      状态，1：启用、0：不启用
	 * @property string  $update_time 修改时间
	 * @property string  $create_time 创建时间
	 *
	 * @property Menu    $menu
	 * @property Package $package
	 */
	class PackageMenu extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%package_menu}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['package_id', 'menu_id', 'status'], 'integer'],
				[['use_limit'], 'string', 'max' => 255],
				[['update_time', 'create_time'], 'safe'],
				[['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['menu_id' => 'id']],
				[['package_id'], 'exist', 'skipOnError' => true, 'targetClass' => Package::className(), 'targetAttribute' => ['package_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'package_id'  => Yii::t('app', '套餐id'),
				'menu_id'     => Yii::t('app', '菜单id'),
				'use_limit'   => Yii::t('app', '功能限制使用数量'),
				'status'      => Yii::t('app', '状态，1：启用、0：不启用'),
				'update_time' => Yii::t('app', '修改时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMenu ()
		{
			return $this->hasOne(Menu::className(), ['id' => 'menu_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPackage ()
		{
			return $this->hasOne(Package::className(), ['id' => 'package_id']);
		}

		//套餐菜单关联详情
		public static function dumpData ($packageId)
		{
			$result = [
				'menu_id' => [],
				'status'  => [],
			];

			$packageAuthorities = static::find()->where(['package_id' => $packageId])->all();

			if (!empty($packageAuthorities)) {
				foreach ($packageAuthorities as $packageAuthority) {
					array_push($result['menu_id'], $packageAuthority['menu_id']);
					array_push($result['status'], $packageAuthority['status']);
					$result['limit'][$packageAuthority['menu_id']] = !empty($packageAuthority['use_limit']) ? $packageAuthority['use_limit'] : '';
				}
			}

			return $result;
		}

		//设置套餐菜单关联
		public static function setAuthority ($packageId, $packageAuthority, $menuLimit = [])
		{
			static::updateAll(['status' => 0], ['package_id' => $packageId]);
			foreach ($packageAuthority as $authority) {
				$packageMenu = static::findOne(['package_id' => $packageId, 'menu_id' => $authority]);
				if (empty($packageMenu)) {
					$packageMenu              = new PackageMenu();
					$packageMenu->package_id  = $packageId;
					$packageMenu->menu_id     = $authority;
					$packageMenu->status      = 1;
					$packageMenu->create_time = DateUtil::getCurrentTime();
				} else {
					$packageMenu->status      = 1;
					$packageMenu->update_time = DateUtil::getCurrentTime();
				}
				$packageMenu->use_limit = isset($menuLimit[$authority]) ? $menuLimit[$authority] : '';

				if (!$packageMenu->save()) {
					throw new InvalidDataException(SUtils::modelError($packageMenu));
				}
			}

			return true;
		}
	}
