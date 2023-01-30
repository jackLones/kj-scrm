<?php

	namespace app\models;

	use Yii;
    use yii\base\Model;

    /**
	 * @property int    $id
	 * @property string $appid       微信公众号appid
	 * @property string $menu        微信公众号菜单
	 * @property string $type        菜单类型，1：普通菜单、2：个性化菜单
	 */
	class WechatMenusForm extends Model
	{
        public $id;
        public $wx_id;
        public $menu;
        public $menu_name;
        public $type;
        public $matchrule;
        public $menuid;

        /**
         * @return array|array[]
         */
		public function rules()
		{
            return [
                [['id','!menuid'],'safe','on' => ['ordinary','personalized']],
                [['menu', 'type'], 'required','message'=>'{attribute}不能为空', 'on' => ['ordinary', 'personalized']],
                [['wx_id'], 'required','message'=>'{attribute}不能为空', 'on' => ['ordinary', 'personalized', 'delPersonalizedMenu','list']],
                ['type', 'in', 'range' => [WechatMenus::ORDINARY_MENU, WechatMenus::PERSONALIZED_MENU],'message'=>'{attribute}值错误'],
                [['matchrule','menu_name'],'required','message'=>'{attribute}不能为空', 'on' => ['personalized']],
                ['id','required','message'=>'{attribute}不能为空', 'on' => ['delPersonalizedMenu']]
            ];
		}

        /**
         * @return array|array[]|\string[][]
         */
        public function scenarios()
        {
            return [
                'ordinary'      => ['id','wx_id', 'menu', 'type'],
                'personalized'  => ['id','wx_id', 'menu', 'menu_name','type','matchrule'],
                'list'          => ['wx_id'],
                'delPersonalizedMenu' => ['id','wx_id'],
            ];
        }
        /**
         * @return array
         */
		public function attributeLabels()
		{
			return [
				'id'        =>  Yii::t('app', 'ID'),
				'wx_id'     =>  Yii::t('app', '微信公众号'),
				'menu'      =>  Yii::t('app', '微信公众号菜单'),
				'menu_name' =>  Yii::t('app', '微信公众号菜单名称'),
				'menuid'    =>  Yii::t('app', '微信公众号个性化菜单ID'),
				'type'      =>  Yii::t('app', '菜单类型'),
				'matchrule' =>  Yii::t('app', '菜单匹配规则'),
			];
		}
	}
