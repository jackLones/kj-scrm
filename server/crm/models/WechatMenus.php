<?php

	namespace app\models;

	use app\components\InvalidDataException;
    use app\components\InvalidParameterException;
    use app\util\DateUtil;
    use app\util\StringUtil;
    use app\util\SUtils;
    use callmez\wechat\sdk\Wechat;
    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;
    use yii\db\Expression;
    use yii\helpers\ArrayHelper;

    /**
	 * This is the model class for table "{{%wechat_menus}}".
	 *
	 * @property int    $id
	 * @property string $appid       微信公众号appid
	 * @property string $menu_name   微信菜单名
	 * @property string $menuid      微信公众号个性化菜单微信id
	 * @property array|string $menu  微信公众号菜单
	 * @property array|string $matchrule  微信公众号菜单匹配规则
	 * @property string $type        菜单类型，1：普通菜单、2：个性化菜单
	 */
	class WechatMenus extends ActiveRecord
	{
	    const ORDINARY_MENU = 1;//1：普通菜单
	    const PERSONALIZED_MENU = 2;//2：个性化菜单

        /**
         * @var Wechat
         */
        public $wechat;

        public $wxAuthorInfo;
        /**
         * @return string
         */
		public static function tableName ()
		{
			return '{{%wechat_menus}}';
		}

        /**
         * @return array|array[]
         */
		public function rules ()
		{
			return [
			    [['id','type'],'integer'],
				[['appid'], 'string', 'max' => 32],
				[['menu_name'], 'string', 'max' => 100],
				[['menu'], 'string'],
				[['matchrule'], 'string'],
				[['menuid'], 'integer'],
			];
		}

        /**
         * @return array
         */
		public function attributeLabels ()
		{
            return [
                'id'        =>  Yii::t('app', 'ID'),
                'appid'     =>  Yii::t('app', '微信公众号'),
                'menu'      =>  Yii::t('app', '微信公众号菜单'),
                'menu_name' =>  Yii::t('app', '微信公众号菜单名称'),
                'menuid'    =>  Yii::t('app', '微信公众号个性化菜单ID'),
                'type'      =>  Yii::t('app', '菜单类型'),
                'matchrule' =>  Yii::t('app', '菜单匹配规则'),
            ];
		}

        public function behaviors(){
            return [
                [
                    'class'=>TimestampBehavior::class,
                    'attributes'=>[
                        ActiveRecord::EVENT_BEFORE_INSERT => ['create_time','update_time'],
                        ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                    ],
                    'value' => new Expression('NOW()'),
                ]
            ];
        }

        public function afterFind ()
        {
            !empty($this->menu) && $this->menu = json_decode($this->menu,true);
            !empty($this->matchrule) && $this->matchrule = json_decode($this->matchrule,true);

            parent::afterFind();
        }

        public function init()
        {
            if($this->appid){
                $wxAuthorize = WxAuthorize::getTokenInfo($this->appid, false, true);
                if(empty($wxAuthorize)) throw new InvalidParameterException('当前公众号未授权，请重新授权！',400188);
                $wechat = \Yii::createObject([
                    'class'     => Wechat::class,
                    'appId'     => $this->appid,
                    'appSecret' => $wxAuthorize['config']->appSecret,
                    'token'     => $wxAuthorize['config']->token,
                    'componentAppId' => $wxAuthorize['config']->appid,
                ]);
                $this->wechat = $wechat;
            }
            
            parent::init();
        }
        /**
         * @param array $data
         * @param string $field
         * @param $value
         * @return array
         */
        public static function menuFilterField(array &$data, array $field, $filter = true)
        {
            $menu = [];
            foreach($data as $key => &$val){
                if(!empty($val['sub_button'])){
                    unset($val['type']);
                }
                if(isset($val['type'])){
                    if($val['type'] == 'click'){
                        $val['key'] = (string)$val['timeid'];
                    }else if(in_array($val['type'],['miniprogram','view'])){
                        unset($val['key']);
                    }
                }
                if($filter){
                    foreach($val as $k => $v){
                        if(in_array($k, $field)){
                            $menu[$key][$k] = $v;
                        }
                    }
                }
                if(!empty($val['sub_button'])){
                    $menu[$key]['sub_button'] = self::menuFilterField($val['sub_button'], $field, $filter);
                }
            }
            return $filter ? $menu : $data;
        }

        /**
         * 公众号菜单
         * @return array
         */
        public function menusList()
        {
            $ordinaryMenu =  self::find()
               ->where(['appid'=>$this->appid,'type'=>self::ORDINARY_MENU])
               ->select('id,menu_name,menu')
               ->one();
            $personalizedMenu =  self::find()
               ->where(['appid'=>$this->appid,'type'=>self::PERSONALIZED_MENU])
               ->select('id,menu_name,menu,matchrule')
               ->all();

            return compact('ordinaryMenu','personalizedMenu');
        }

        /**
         * 添加公众号菜单
         * @return false|mixed
         * @throws InvalidParameterException
         */
        public function addMenus($confirmSave=0)
		{
            $originMenu = $this->menu;
            $this->menu = self::menuFilterField($originMenu,[],false);
		    $menu = self::menuFilterField($originMenu,['type','name','url','appid','pagepath','media_id','key']);
            if($this->type == self::ORDINARY_MENU){
                $this->matchrule = '';
                $this->menu_name = '通用菜单';
                $wechatMenus = self::findOne(['appid'=>$this->appid,'type'=>$this->type]) ?: $this;
            }else if($this->id){
                $wechatMenus = self::findOne($this->id) ?: $this;
            }else{
                $wechatMenus = $this;
            }
            $matchrule = '';
            if($this->type == self::PERSONALIZED_MENU){
                $matchField = array_flip(["tag_id", "sex", "country", "province", "city", "client_platform_type", "language"]);
                $matchrule  = array_filter(array_intersect_key($this->matchrule,$matchField));

                empty($matchrule) && SUtils::throwException(InvalidDataException::class,'个性化菜单匹配规则缺失');
                $wechatMenus->matchrule = json_encode($this->matchrule);
                $wechatMenus->menu_name = $this->menu_name;
            }
            $menuEvent = $this->menu;
            $wechatMenus->menu = json_encode($this->menu);
            $wechatMenus->save();
            ($msg = SUtils::modelError($wechatMenus)) && SUtils::throwException(InvalidDataException::class,$msg);
            if(!$confirmSave) return $wechatMenus->id;
            !Yii::$app->request->post('uid') && SUtils::throwException(InvalidDataException::class,'UID缺失');
            try{
                $result = Yii::$app->db->transaction(function ()use($menuEvent,$menu,$wechatMenus,$matchrule){

                    $menuKeyword = ['appid'=>$this->appid,'menu_id'=>$wechatMenus->id];
                    $eventIds = WechatMenusKeywordRelation::find()->where($menuKeyword)->select('id')->asArray()->all();
                    $keywordIds = array_column($eventIds,'id');
                    !empty($eventIds) && ReplyInfo::deleteAll(['in', 'menu_keyword_id', $keywordIds]) &&  WechatMenusKeywordRelation::deleteAll($menuKeyword);

                    $this->saveMenuClickEvent($menuEvent,$wechatMenus);//菜单点击事件
                    if($this->type == self::ORDINARY_MENU){
                        $result = $this->wechat->createMenu($menu);
                        !empty($result['errcode']) && SUtils::throwException(InvalidDataException::class, $this->wechat->errorCode[$result['errcode']]);
                        return $wechatMenus->id;
                    }
                    $result = $this->wechat->createPersonalizedMenu($menu,$matchrule);
                    !empty($result['errcode']) && SUtils::throwException(InvalidDataException::class, $this->wechat->errorCode[$result['errcode']]);
                    !empty($wechatMenus->menuid) && $this->wechat->deletePersonalizedMenu($wechatMenus->menuid);//清除线上个性化菜单
                    $wechatMenus->menuid = $result['menuid'];
                    $wechatMenus->save();

                    return $wechatMenus->id;
                });
            }catch (\Exception $e){
                if(strpos($e->getMessage(),'SQLSTATE') > -1){
                    SUtils::throwException(InvalidDataException::class,'保存失败，短时间内请勿多次请求');
                }else  SUtils::throwException(InvalidDataException::class,$e->getMessage());
            }
            return $result;
		}

        /**
         * 保存菜单点击事件
         * @param array $menuEvent
         */
        private function saveMenuClickEvent(array $menuEvent,$wechatMenus)
        {
            foreach ($menuEvent as $menu) {
                if(empty($menu['sub_button'])){
                    if($menu['type'] == 'click'){
                        $wechatMenusKey = new WechatMenusKeywordRelation();
                        $wechatMenusKey->appid = $this->appid;
                        $wechatMenusKey->menu_id = $wechatMenus->id;
                        $wechatMenusKey->keyword = $menu['key'];
                        $wechatMenusKey->save();
                        ($msg = SUtils::modelError($wechatMenusKey)) && SUtils::throwException(InvalidDataException::class,$msg);
                        $keyWordId = $wechatMenusKey->id;
                        $this->dumpReplyInfoData($keyWordId,$menu['menu_content']);
                    }
                }else{
                    $this->saveMenuClickEvent($menu['sub_button'],$wechatMenus);
                }
            }
        }

        private function dumpReplyInfoData($keyWordId, $event)
        {
            $authorInfo = WxAuthorize::findOne(['authorizer_appid' => $this->appid]);
            $i = 0;
            foreach ($event as $mv) {
                if ($mv['typeValue'] == 5) {
                    foreach ($mv['sketchList'] as $nv) {
                        $reply = new ReplyInfo();
                        $reply->menu_keyword_id = $keyWordId;
                        $reply->type = $mv['typeValue'];
                        $reply->status = 1;//默认开启
                        $reply->create_time = DateUtil::getCurrentTime();
                        if (empty($nv['addType'])) {
                            //改版 采用文件柜
                            $attachment = Attachment::findOne(['id' => $nv['material_id'], 'status' => 1]);
                            if (empty($attachment)) {
                                throw new InvalidDataException('内容' . ($i + 1) . '图文素材不存在');
                            }
                            $reply->attachment_id = $attachment->id;
                            if (!empty($attachment->material_id) && $attachment->material->author_id == $authorInfo->author_id && !empty($attachment->material->status)) {
                                $reply->content = $attachment->material->media_id;
                                $reply->material_id = $attachment->material->id;
                            } else {
                                $material = Material::getMaterial(['author_id' => $authorInfo->author_id, 'attachment_id' => $attachment->id, 'file_type' => $attachment->file_type]);
                                if (!empty($material)) {
                                    $reply->content = $material->media_id;
                                    $reply->material_id = $material->id;
                                } else {
                                    $reply->title = $attachment->file_name;
                                    $reply->digest = $attachment->content;
                                    $reply->cover_url = $attachment->local_path;
                                    $reply->content_url = $attachment->jump_url;
                                }
                            }
                        } else {
                            $reply->title = $nv['inputTitle'];
                            $reply->digest = $nv['digest'];
                            $site_url = \Yii::$app->params['site_url'];
                            $cover_url = str_replace($site_url, '', $nv['local_path']['img']);
                            $reply->cover_url = $cover_url;
                            $reply->content_url = $nv['content_source_url'];
                            $reply->attachment_id = !empty($nv['material_id']) ? $nv['material_id'] : '';
                            $reply->is_use = 1;
                            //是否同步自定义图文
                            if (!empty($nv['materialSync'])) {
                                $reply->is_sync = 1;

                                $userRelation = UserAuthorRelation::findOne(['author_id' => $authorInfo->author_id]);
                                if (!empty($nv['attach_id'])) {
                                    $attachment = Attachment::findOne($nv['attach_id']);
                                    $attachment->file_name = $nv['inputTitle'];
                                    $attachment->content = $nv['digest'];
                                    $attachment->local_path = $cover_url;
                                    $attachment->jump_url = $nv['content_source_url'];
                                    if (!empty($attachment->dirtyAttributes)) {
                                        $attachment = new Attachment();
                                        $attachment->uid = $userRelation->uid;
                                        $attachment->file_type = 4;
                                        $attachment->create_time = DateUtil::getCurrentTime();
                                    }
                                    $attachment->group_id = $nv['selectGroupId'];
                                } else {
                                    $attachment = new Attachment();
                                    $attachment->uid = $userRelation->uid;
                                    $attachment->file_type = 4;
                                    $attachment->create_time = DateUtil::getCurrentTime();
                                }
                                $attachment->group_id = $nv['selectGroupId'];
                                $attachment->file_name = $nv['inputTitle'];
                                $attachment->content = $nv['digest'];
                                $attachment->local_path = $cover_url;
                                $attachment->jump_url = $nv['content_source_url'];
                                if (!$attachment->validate() || !$attachment->save()) {
                                    throw new InvalidDataException(SUtils::modelError($attachment));
                                }
                                $reply->attach_id = $attachment->id;
                            }
                        }
                        if (!$reply->save()) {
                            throw new InvalidDataException(SUtils::modelError($reply));
                        }
                    }
                } else {
                    $reply = new ReplyInfo();
                    $reply->menu_keyword_id = $keyWordId;
                    $reply->type = $mv['typeValue'];
                    $reply->status = 1;//默认开启
                    $reply->create_time = DateUtil::getCurrentTime();
                    if ($mv['typeValue'] == 1) {
                        mb_strlen($mv['textContent']) > 650 && SUtils::throwException(InvalidDataException::class,'文本回复字数不可超过650个字符！');
                        $reply->content = rawurlencode($mv['textContent']);
                    } elseif ( in_array($mv['typeValue'],[2,3,4,6]) ) {
                        //改版 采用文件柜
                        $attachment = Attachment::findOne(['id' => $mv['material_id'], 'status' => 1]);
                        $typeValue = [2 => '图片', 3 => '音频', 4 => '视频', 6 => '小程序',];
                        if (empty($attachment)) {
                            SUtils::throwException(InvalidDataException::class,'内容' . ($i + 1) . $typeValue[$mv['typeValue']] . '素材不存在');
                        }
                        if ($mv['typeValue'] == 6) {
                            if($mv['materialSync']) { //是否同步自定义图文
                                $site_url = \Yii::$app->params['site_url'];
                                $appId     = trim($mv['appid']);
                                $appPath   = trim($mv['pageUrl']);
                                $title     = trim($mv['appletInputTitle']);
                                $attach_id = $mv['material_id'];

                                empty($appId)   &&  SUtils::throwException(InvalidDataException::class,'请填写小程序appid！');
                                empty($appPath) &&  SUtils::throwException(InvalidDataException::class,'请填写小程序路径！');
                                empty($title)   &&  SUtils::throwException(InvalidDataException::class,'请填写卡面标题！');
                                empty($title)   &&  SUtils::throwException(InvalidDataException::class,'请填写卡面标题！');
                                mb_strlen($title, 'utf-8') > 20 && SUtils::throwException(InvalidDataException::class,'卡面标题不能超过20个字符！');
                                empty($mv['material_id']) && SUtils::throwException(InvalidDataException::class,'请选择卡面图片！');

                                $pic_url = '';
                                if (!empty($attach_id)) {
                                    $attach = Attachment::findOne($attach_id);
                                    if (!empty($attach) && !empty($attach->s_local_path)) {
                                        $pic_url = $attach->s_local_path;
                                    }
                                }
                                $atta = new Attachment();
                                $atta->uid  = Yii::$app->request->post('uid');;
                                $atta->group_id     =   $mv['selectGroupId'];
                                $atta->file_type    =   7;
                                $atta->file_name    =   $title;
                                $atta->local_path   =   str_replace($site_url, '', $pic_url);
                                $atta->s_local_path =   str_replace($site_url, '', $pic_url);
                                $atta->appId        =   $appId;
                                $atta->appPath      =   $appPath;
                                $atta->attach_id    =   $attach_id;
                                $atta->create_time  =   DateUtil::getCurrentTime();
                                $atta->save();
                                $reply->attach_id = $atta->id;
                                $reply->is_sync = 1;
                            }
                            if ($mv['addType'] == 0) {//导入
                                $reply->title = $attachment->file_name;
                                $reply->appid = $attachment->appId;
                                $reply->pagepath = $attachment->appPath;
                            } else {//新建
                                $reply->title = trim($mv['appletInputTitle']);
                                $reply->appid = trim($mv['appid']);
                                $reply->pagepath = trim($mv['pageUrl']);
                                empty($reply->appid) && SUtils::throwException(InvalidDataException::class,'请填写小程序APPID！');
                                empty($reply->pagepath) && SUtils::throwException(InvalidDataException::class,'请填写小程序路径！');
                            }
                            $reply->is_use = $mv['addType'];
                        }
                        $reply->attachment_id = $attachment->id;
                        if (!empty($attachment->material_id) && $attachment->material->author_id == $authorInfo->author_id && !empty($attachment->material->status)) {
                            $reply->content = $attachment->material->media_id;
                            $reply->material_id = $attachment->material->id;
                        } else {
                            $material = Material::getMaterial(['author_id' => $authorInfo->author_id, 'attachment_id' => $attachment->id, 'file_type' => $attachment->file_type]);
                            if (!empty($material)) {
                                $reply->content = $material->media_id;
                                $reply->material_id = $material->id;
                            }else{
                                $material = $this->uploadMedia($attachment);
                                $reply->content = $material->media_id;
                                $reply->material_id = $material->id;
                            }
                            empty($reply->material_id) && SUtils::throwException(InvalidDataException::class,'内容' . ($i + 1) . $typeValue[$mv['typeValue']] . '素材不存在');
                        }
                    }
                    !$reply->save() && SUtils::throwException(InvalidDataException::class,SUtils::modelError($reply));
                }
                $i++;
            }
        }

        public function uploadMedia(Attachment $attachment,Material $material = null)
        {
            $this->wxAuthorInfo = WxAuthorize::findOne(['authorizer_appid' => $this->appid]);
            if (!in_array($attachment->file_type, [1, 2, 3])) {
                throw new InvalidDataException('此类型不支持上传！');
            }
            if ($attachment->file_type == 1) {//图片
                $material_type = 2;
                $fileType      = 'image';
            } elseif ($attachment->file_type == 2) {//音频
                if ($attachment->file_duration > '00:01:00') {
                    throw new InvalidDataException('音频的播放长度不能超过60s');
                }
                $material_type = 3;
                $fileType      = 'voice';
            } elseif ($attachment->file_type == 3) {//视频
                $material_type = 4;
                $fileType      = 'video';
            }
            $time        = time();
            $expire      = $time + 259200;//三天后时间戳
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $wxAuthorize = WxAuthorize::getTokenInfo($this->wxAuthorInfo->authorizer_appid, false, true);
                if (empty($wxAuthorize)) {
                    throw new InvalidDataException('获取token失败,请检查是否已授权');
                }
                $wechat  = \Yii::createObject([
                    'class'          => Wechat::class,
                    'appId'          => $this->wxAuthorInfo->authorizer_appid,
                    'appSecret'      => $wxAuthorize['config']->appSecret,
                    'token'          => $wxAuthorize['config']->token,
                    'componentAppId' => $wxAuthorize['config']->appid,
                ]);
                if(empty($material)){
                    $material                 = new Material();
                    $material->author_id      = $this->wxAuthorInfo->author_id;;
                    $material->type           = 0;
                    $material->material_type  = $material_type;
                    $material->file_name      = $attachment->file_name;
                    $material->media_width    = $attachment->file_width;
                    $material->media_height   = $attachment->file_height;
                    $material->media_duration = $attachment->file_duration;
                    $material->file_length    = $attachment->file_length;
                    $material->content_type   = $attachment->file_content_type;
                    $material->local_path     = $attachment->local_path;
                    $material->create_time    = DateUtil::getCurrentTime();
                }

                $material->expire         = (string) $expire;
                if ($attachment->file_type == 3) {
                    $material->title = $attachment->file_name;
                }
                $appPath  = \Yii::getAlias('@app');
                $filePath = $appPath . $material->local_path;
                $result   = $wechat->uploadMedia($filePath, $fileType);
                if (!empty($result['media_id'])) {
                    $material->media_id   = $result['media_id'];
                    $material->created_at = (string) $result['created_at'];
                } else {
                    if ($result['errcode'] == '45007') {
                        throw new InvalidDataException('音频的播放长度不能超过60s');
                    }
                    if ($result['errcode'] == '-1') {
                        throw new InvalidDataException('视频格式不对，请上传MP4格式的视频');
                    }

                    throw new InvalidDataException($result['errmsg']);
                }
                $material->attachment_id = $attachment->id;
                if (!$material->validate() || !$material->save()) {
                    throw new InvalidDataException(SUtils::modelError($material));
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw new InvalidDataException($e->getMessage());
            }
            return $material;
        }

        /**
         * 删除个性化菜单
         * @return mixed
         * @throws InvalidDataException
         */
		public function delPersonalizedMenu()
        {
            $wechatMenus = self::find()->where(['id'=>$this->id,'appid'=>$this->appid])->one();
            empty($wechatMenus) && SUtils::throwException(InvalidDataException::class,'该菜单不存在，请刷新页面后重试！');
            try{
                $result = Yii::$app->db->transaction(function ()use($wechatMenus){

                    $menuKeyword = ['appid'=>$this->appid,'menu_id'=>$wechatMenus->id];
                    $eventIds = WechatMenusKeywordRelation::find()->where($menuKeyword)->select('id')->asArray()->all();
                    $keywordIds = array_column($eventIds,'id');
                    !empty($eventIds) && ReplyInfo::deleteAll(['in', 'menu_keyword_id', $keywordIds]) &&  WechatMenusKeywordRelation::deleteAll($menuKeyword);

                    $menuid = $wechatMenus->menuid;
                    $wechatMenus->delete();

                    if($menuid){
                        $result = $this->wechat->deletePersonalizedMenu($menuid);
                        $result['errcode'] && SUtils::throwException(InvalidDataException::class,$result['errmsg']);
                    }
                    return true;
                });
            }catch (\Exception $e){
                SUtils::throwException(InvalidDataException::class,$e->getMessage());
            }

            return $result;
        }

        /**
         * 获取全部菜单
         * @return array
         * @throws \yii\base\Exception
         */
        public function wechatAllMenu()
        {
            return $this->wechat->getMenuList();
        }

        /**
         * 获取菜单
         * @return array
         * @throws \yii\base\Exception
         */
        public function wechatMenu()
        {
            return $this->wechat->getWxMenu();
        }

        /**
         * 同步公众号菜单到数据库
         * @return mixed
         * @throws \Throwable
         */
        public function syncWechatMenu()
        {
            self::findOne(['appid'=>$this->appid]) && SUtils::throwException(InvalidDataException::class,'当前数据库已有菜单数据，不可同步！');
            try{
                $result = Yii::$app->db->transaction(function (){

                    $this->initOriginWechatMenu(ArrayHelper::getValue($this->wechatMenu(), 'selfmenu_info.button'),self::ORDINARY_MENU);
                    $menusArray = ArrayHelper::getValue($this->wechatAllMenu(), 'conditionalmenu');
                    if(count($menusArray)){
                        foreach ($menusArray as $menu){
                            $this->initOriginWechatMenu($menu, self::PERSONALIZED_MENU);
                        }
                    }
                    return true;
                });
            }catch (\Exception $e){
                SUtils::throwException(InvalidDataException::class,$e->getMessage());
            }
            return $result;
        }

        private function initOriginWechatMenu(array $menus,$type)
        {
            $type == self::PERSONALIZED_MENU && ([$button,$matchrule,$menuid] = array_values($menus)) && $menus = $button;

            foreach ($menus as &$menu){
                if(empty($menu['sub_button']) && !in_array($menu['type'],['click','view','miniprogram'])){
                    $menu['type'] = 'click';
                }
                if(!empty($menu['sub_button'])){
                    isset($menu['sub_button']['list']) && $menu['sub_button'] = $menu['sub_button']['list'];
                    foreach ($menu['sub_button'] as &$sub_button){
                        if(!in_array($sub_button['type'],['click','view','miniprogram'])){
                            $sub_button['type'] = 'click';
                        }
                        $sub_button['timeid'] = StringUtil::uuid();
                    }
                }
                $menu['timeid'] = StringUtil::uuid();
            }
            $menus = self::menuFilterField($menus,['type','name','url','appid','pagepath','media_id','key']);
            $this->constructMenuData($menus);
            $wechatMenus = new WechatMenus();
            $wechatMenus->type = $type;
            $wechatMenus->appid = $this->appid;
            $wechatMenus->menu_name = $type == self::ORDINARY_MENU ? '通用菜单' : '个性化菜单';
            $wechatMenus->menu = json_encode($menus);
            $wechatMenus->matchrule = !empty($matchrule) ? json_encode($matchrule) : '';
            $wechatMenus->menuid = $menuid ?? '';
            $wechatMenus->save();
        }

        private function constructMenuData(array &$menus,$level = 'first')
        {
            $menuContent = [[
                'typeValue' => 1,
                "typeName" => [1 =>  "文字","图片", "音频", "视频","图文","小程序"],
                "closeShowModal3" => false,
                "disabled" => false,
                "file_name" =>  "",
                "material_id" => "",
                "local_path" => ["img"=> "","audio" => ""],
                "addType" => 1,
                "sketchList" => [
                    [
                        "addSketchVisible" => true,
                        "addType" => 1,
                        "inputTitle" => "",
                        "digest" => "",
                        "content_source_url" => "",
                        "material_id" => 0,
                        "local_path" => [
                            "audio"=> "",
                            "img" =>""
                        ],
                        "showTextContent" => false,
                        "isAdvance" => false,
                        "closeShowModal3" => false,
                        "materialSync" =>  0,
                        "selectGroupId" => ""
                    ]
                ],
                "appletUrl" => "",
                "appletInputTitle" => "",
                "appid" => "",
                "pageUrl" => "",
                "materialSync" => 0,
                "selectGroupId" => "",
                "textAreaValueHeader" => "",
                "textContent" => ""
            ]];
            foreach ($menus as $k=>&$menu){
                if(empty($menu['sub_button'])){
                    $menu = array_merge($menu,[
                        'timeid' => StringUtil::uuid(),
                        'leveltype' => $level,
                        'radiotype' => 'click'
                    ]);
                    $level == 'second' && $menu['activeMenu'] = 1;
                    if($menu['type'] == 'view'){
                        $menu['content_type'] = 2;
                        $menu['sub_button'] = [];
                    }else if($menu['type'] == 'miniprogram'){
                        $menu['content_type'] = 3;
                        $menu['sub_button'] = [];
                    }else{
                        $menu = array_merge($menu,[
                            'radiotype' => 'click',
                            'content_type' => 1,
                            'sub_button' => [],
                            'menu_content'=>$menuContent
                        ]);
                    }
                }else{
                    $menu = array_merge([
                        'timeid' => StringUtil::uuid(),
                        'leveltype' => $level,
                        'radiotype' => 'click',
                        'content_type' => 1,
                        'sub_button' => [],
                        'menu_content'=> $menuContent
                    ],$menu);
                    $this->constructMenuData($menu['sub_button'],'second');
                }
            }
        }
	}
