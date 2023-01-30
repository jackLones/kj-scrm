<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%menu}}".
	 *
	 * @property int    $id
	 * @property int    $parent_id   父级ID
	 * @property string $title       菜单名称
	 * @property string $icon        图标样式
	 * @property string $key         菜单标识
	 * @property string $link        菜单地址
	 * @property int    $level       菜单等级
	 * @property int    $sort        排序
	 * @property int    $is_new      是否为新菜单，0：否、1：是
	 * @property int    $is_hot      是否为热门菜单。0：否、1：是
	 * @property int    $status      菜单状态：0：隐藏、1：显示
	 * @property string $create_time 创建时间
	 * @property string $comefrom    菜单归属：0公众号、1企业微信
	 *
	 * @property Menu   $parent
	 * @property Menu[] $menus
	 */
	class Menu extends \yii\db\ActiveRecord
	{
		const NOT_NEW_MENU = 0;
		const IS_NEW_MENU = 1;

		const NOT_HOT_MENU = 0;
		const IS_HOT_MENU = 1;

		const HIDE_MENU = 0;
		const SHOW_MENU = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%menu}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['parent_id', 'level', 'sort', 'is_new', 'is_hot', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['title'], 'string', 'max' => 32],
				[['icon', 'key'], 'string', 'max' => 128],
				[['link'], 'string', 'max' => 255],
				[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
				'title'       => Yii::t('app', '菜单名称'),
				'icon'        => Yii::t('app', '图标样式'),
				'key'         => Yii::t('app', '菜单标识'),
				'link'        => Yii::t('app', '菜单地址'),
				'level'       => Yii::t('app', '菜单等级'),
				'sort'        => Yii::t('app', '排序'),
				'is_new'      => Yii::t('app', '是否为新菜单，0：否、1：是'),
				'is_hot'      => Yii::t('app', '是否为热门菜单。0：否、1：是'),
				'status'      => Yii::t('app', '菜单状态：0：隐藏、1：显示'),
				'create_time' => Yii::t('app', '创建时间'),
				'comefrom'    => Yii::t('app', '菜单归属：0公众号、1企业微信'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getParent ()
		{
			return $this->hasOne(Menu::className(), ['id' => 'parent_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMenus ()
		{
			return $this->hasMany(Menu::className(), ['parent_id' => 'id'])->orderBy('sort ASC');
		}

		/**
		 * @param bool $withParent
		 *
		 * @return array
		 */
		public function dumpData ($withParent = false)
		{
			$data = [
				'id'       => $this->id,
				'title'    => $this->title,
				'icon'     => $this->icon,
				'key'      => $this->key,
				'link'     => $this->link,
				'level'    => $this->level,
				'sort'     => $this->sort,
				'is_new'   => $this->is_new,
				'is_hot'   => $this->is_hot,
				'status'   => $this->status,
				'comefrom' => $this->comefrom,
			];

			if ($withParent) {
				$data['parent_id'] = $this->parent_id;
			}

			return $data;
		}

		/**
		 * 根据父级ID获取子菜单的列表
		 *
		 * @param      $parentId
		 * @param      $sub_id
		 * @param      $routes
		 * @param bool $withSubMenu
		 * @param bool $is_all //true取全部状态，false取开启状态
		 * @param int  $type   0、公众号、1企业微信
		 *
		 * @return array
		 */
		public static function getSubMenuList ($parentId, $withSubMenu = true, $is_all = false, $sub_id, $routes, $type)
		{
			$menuList = [];

			$parentMenu = static::findOne($parentId);

			if (!empty($parentMenu->menus)) {
				foreach ($parentMenu->menus as $subMenu) {
					if (!$is_all && ($subMenu->status != 1)) {
						continue;
					}
					if (!empty($sub_id) && !in_array($subMenu->key, $routes)) {
						continue;
					}
					$weChatMenu = ['redFission', 'raffle', 'fission'];
					if ($type == 0 && ($subMenu->key == 'filingCabinetStatistics' || in_array($subMenu->key, $weChatMenu))) {
						//公众号不返回内容统计
						continue;
					}
					$subMenuData = $subMenu->dumpData();
					if ($withSubMenu) {
						$subMenuData['children'] = static::getSubMenuList($subMenu->id, true, false, $sub_id, $routes, $type);
					}
					array_push($menuList, $subMenuData);
				}
			}

			return $menuList;
		}

		/**
		 * 根据菜单等级，获取菜单列表
		 *
		 * @param int  $level       // 菜单等级
		 * @param int  $type        // 0 公众号 1 企业微信 2 全部 3 公共部分 4公众号不包含公共部分 5企业微信不包含公共部分
		 * @param int  $sub_id      // 子账户id
		 * @param int  $wx_id       // 公众号或企业微信id
		 * @param bool $withSubMenu //是否包含子菜单
		 * @param bool $is_all      //true取全部状态，false取开启状态
		 *
		 * @return array
		 */
		public static function getMenuList ($level = 1, $withSubMenu = true, $is_all = false, $type = 0, $sub_id = 0, $wx_id = 0)
		{
			$menuList = [];
			switch ($type) {
				case 5:
					$where = ['level' => $level, 'comefrom' => 1];
					break;
				case 4:
					$where = ['level' => $level, 'comefrom' => 0];
					break;
				case 3:
					$where = ['level' => $level, 'comefrom' => 2];
					break;
				case 1:
					$where = ['level' => $level, 'comefrom' => [$type, 2]];
					break;
				case 0:
					$where = ['level' => $level, 'comefrom' => [$type, 2]];
					break;
				default:
					$where = ['level' => $level];
					break;
			}

			/*if ($type != 2) {
				$where = ['level' => $level, 'comefrom' => [$type, 2]];
			} else {
				$where = ['level' => $level];
			}*/
			if (!$is_all) {
				$where['status'] = static::SHOW_MENU;
			}
			$topMenuData = static::find()->where($where)->orderBy(['sort' => SORT_ASC])->all();
			$routes      = [];
			if (!empty($sub_id)) {
				$routes = Authority::getAuthority($sub_id, $wx_id, $type);
			}
			if (in_array('miniMsg', $routes)) {
				array_push($routes, 'fansMsg');
			}
			if (!empty($topMenuData)) {
				foreach ($topMenuData as $menuData) {
					if (empty($sub_id)) {
						if ($type == 1 && $menuData->key == 'filingCabinet') {
							continue;
						}
					} else {
						if($type == 1){
							$flag = false;
							$filingNew = Authority::findOne(['status' => 0, 'route' => 'filingCabinetStatisticsNew']);
							if (!empty($filingNew)) {
								$subAuth = SubUserAuthority::find()->where(['sub_user_id' => $sub_id, 'type' => 2])->andWhere(['!=', 'authority_ids', ''])->all();
								if (!empty($subAuth)) {
									/** @var SubUserAuthority $au */
									foreach ($subAuth as $au) {
										$ids = explode(',',$au->authority_ids);
										if(in_array($filingNew->id,$ids) && $au->wx_id == $wx_id){
											$flag = true;
										}
									}
								}
							}
							if($flag && $menuData->key == 'filingCabinet'){
								continue;
							}
						}
					}

					if (!empty($sub_id) && !in_array($menuData->key, $routes)) {
						Yii::error($menuData->key,'$menuDatakey');
						continue;
					}
					$menuInfo = $menuData->dumpData();

					if ($withSubMenu) {
						$menuInfo['children'] = static::getSubMenuList($menuData->id, $withSubMenu, $is_all, $sub_id, $routes, $type);
						if ($type == 1 && $menuData->key == 'filingCabinetNew') {
							$filing      = static::findOne(['key' => 'filingCabinet']);
							$menuInfoNew = static::getSubMenuList($filing->id, $withSubMenu, $is_all, $sub_id, $routes, $type);
							foreach ($menuInfoNew as $k=>$new){
								if($new['key'] == 'filingCabinetStatistics'){
									unset($menuInfoNew[$k]);
								}
							}
							$menuInfo['children'] = array_merge($menuInfoNew,$menuInfo['children']);
						}
					}

					array_push($menuList, $menuInfo);
				}
			}

			return $menuList;
		}

		/**
		 * 添加或者更新菜单
		 *
		 * @param array $data 菜单数据
		 *
		 * @return array
		 */
		public static function setMenu ($data)
		{
			$menuId = intval($data['id']);
			$type   = $data['type'];
			$sort   = intval($data['sort']);
			if (empty($data['title'])) {
				throw new InvalidDataException('菜单名称必填');
			}
			if (empty($data['key'])) {
				throw new InvalidDataException('菜单标识必填');
			}
			$transaction = \Yii::$app->db->beginTransaction();
			try {
				$keyInfo = static::find()->where(['key' => $data['key']]);
				if ($type == 'add') {
					$menu            = new Menu();
					$menu->parent_id = $menuId;
					$menu->sort      = $sort + 1;
					$menu->level     = 2;
					$menu->status    = 1;
				} elseif ($type == 'edit') {
					$menu    = static::findOne($menuId);
					$keyInfo = $keyInfo->andWhere(['<>', 'id', $menuId]);
				} else {
					throw new InvalidDataException('请求类型不正确');
				}
				//查询标识是否重复
				$keyInfo = $keyInfo->one();
				if (!empty($keyInfo)) {
					throw new InvalidDataException('菜单标识已经存在');
				}

				$menu->title    = $data['title'];
				$menu->icon     = $data['icon'];
				$menu->key      = $data['key'];
				$menu->link     = $data['link'];
				$menu->is_new   = $data['is_new'];
				$menu->is_hot   = $data['is_hot'];
				$menu->comefrom = $data['comefrom'];
				if (!$menu->save()) {
					throw new InvalidDataException(SUtils::modelError($menu));
				}
				$newId = $menu->id;
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return ['error' => 0, 'msg' => '', 'id' => $newId, 'title' => $data['title']];
		}
	}
