<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use callmez\wechat\sdk\Wechat;
	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Json;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%fans}}".
	 *
	 * @property int                 $id
	 * @property int                 $author_id          公众号ID
	 * @property int                 $subscribe          用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
	 * @property string              $openid             用户的标识，对当前公众号唯一
	 * @property string              $nickname           用户的昵称
	 * @property int                 $sex                用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
	 * @property string              $city               用户所在城市
	 * @property string              $country            用户所在国家
	 * @property string              $province           用户所在省份
	 * @property string              $language           用户的语言，简体中文为zh_CN
	 * @property string              $headerimg          用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
	 * @property string              $subscribe_time     用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
	 * @property string              $unsubscribe_time   用户取消关注时间，为时间戳。如果用户曾多次取消关注，则取最后取消关注时间
	 * @property string              $unionid            只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
	 * @property string              $remark             公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
	 * @property string              $groupid            用户所在的分组ID（兼容旧的用户分组接口）
	 * @property string              $tagid_list         用户被打上的标签ID列表
	 * @property string              $subscribe_scene    返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENEPROFILE LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他
	 * @property int                 $qr_scene           二维码扫码场景（开发者自定义）
	 * @property string              $qr_scene_str       二维码扫码场景描述（开发者自定义）
	 * @property string              $last_time          最后活跃时间
	 * @property int                 $follow_status      跟进状态
	 * @property int                 $follow_id          跟进id
	 * @property string              $create_time
	 * @property int                 $external_userid    外部联系人ID
	 * @property int                 $activity_id        任务宝id
	 *
	 * @property Follow              $follow
	 * @property WorkExternalContact $externalUser
	 * @property WxAuthorize         $author
	 * @property Scene               $qrScene
	 * @property FansBehavior[]      $fansBehaviors
	 * @property FansMsg[]           $fansMsgs
	 * @property FansMsgMaterial[]   $fansMsgMaterials
	 * @property FansTags[]          $fansTags
	 * @property FansTimeLine[]      $fansTimeLines
	 * @property KfPushPreview[]     $kfPushPreviews
	 * @property Location[]          $locations
	 * @property MiniUser[]          $miniUsers
	 * @property TemplatePushInfo[]  $templatePushInfos
	 */
	class Fans extends \yii\db\ActiveRecord
	{
		const USER_UNSUBSCRIBE = 0;
		const USER_SUBSCRIBE = 1;

		const SEX_UNKNOW = 0;
		const SEX_MALE = 1;
		const SEX_FEMALE = 2;

		const MAX_USER_LIST = 10000;
		const MAX_GET_USER_INFO = 100;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'subscribe'], 'required'],
				[['author_id', 'subscribe', 'activity_id', 'sex', 'qr_scene', 'groupid', 'subscribe_time', 'unsubscribe_time', 'follow_status', 'external_userid'], 'integer'],
				[['create_time'], 'safe'],
				[['openid', 'unionid', 'tagid_list'], 'string', 'max' => 80],
				[['nickname'], 'string', 'max' => 128],
				[['city', 'country', 'province'], 'string', 'max' => 32],
				[['language'], 'string', 'max' => 16],
				[['headerimg', 'remark', 'qr_scene_str'], 'string', 'max' => 255],
				[['subscribe_scene'], 'string', 'max' => 64],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['qr_scene'], 'exist', 'skipOnError' => true, 'targetClass' => Scene::className(), 'targetAttribute' => ['qr_scene' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'author_id'        => Yii::t('app', '公众号ID'),
				'subscribe'        => Yii::t('app', '用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。'),
				'openid'           => Yii::t('app', '用户的标识，对当前公众号唯一'),
				'nickname'         => Yii::t('app', '用户的昵称'),
				'sex'              => Yii::t('app', '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知'),
				'city'             => Yii::t('app', '用户所在城市'),
				'country'          => Yii::t('app', '用户所在国家'),
				'province'         => Yii::t('app', '用户所在省份'),
				'language'         => Yii::t('app', '用户的语言，简体中文为zh_CN'),
				'headerimg'        => Yii::t('app', '用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。'),
				'subscribe_time'   => Yii::t('app', '用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间'),
				'unsubscribe_time' => Yii::t('app', '用户取消关注时间，为时间戳。如果用户曾多次取消关注，则取最后取消关注时间'),
				'unionid'          => Yii::t('app', '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。'),
				'remark'           => Yii::t('app', '公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注'),
				'groupid'          => Yii::t('app', '用户所在的分组ID（兼容旧的用户分组接口）'),
				'tagid_list'       => Yii::t('app', '用户被打上的标签ID列表'),
				'subscribe_scene'  => Yii::t('app', '返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENEPROFILE LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他'),
				'qr_scene'         => Yii::t('app', '二维码扫码场景（开发者自定义）'),
				'qr_scene_str'     => Yii::t('app', '二维码扫码场景描述（开发者自定义）'),
				'last_time'        => Yii::t('app', '最后活跃时间'),
				'follow_status'    => Yii::t('app', '跟进状态'),
				'external_userid'  => Yii::t('app', '外部联系人ID'),
				'activity_id'      => Yii::t('app', '任务宝id'),
				'create_time'      => Yii::t('app', 'Create Time'),
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
		public function getFollow ()
		{
			return $this->hasOne(Follow::className(), ['id' => 'follow_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternalUser ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getQrScene ()
		{
			return $this->hasOne(Scene::className(), ['id' => 'qr_scene']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansBehaviors ()
		{
			return $this->hasMany(FansBehavior::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansMsgs ()
		{
			return $this->hasMany(FansMsg::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansMsgMaterials ()
		{
			return $this->hasMany(FansMsgMaterial::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansTags ()
		{
			return $this->hasMany(FansTags::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansTimeLines ()
		{
			return $this->hasMany(FansTimeLine::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getKfPushPreviews ()
		{
			return $this->hasMany(KfPushPreview::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLocations ()
		{
			return $this->hasMany(Location::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMiniUsers ()
		{
			return $this->hasMany(MiniUser::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplatePushInfos ()
		{
			return $this->hasMany(TemplatePushInfo::className(), ['fans_id' => 'id']);
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			return [
				'fans_id'         => $this->id,
				'openid'          => $this->openid,
				'nickname'        => $this->nickname,
				'remark'          => $this->remark,
				'sex'             => $this->sex,
				'country'         => $this->country,
				'province'        => $this->province,
				'city'            => $this->city,
				'language'        => $this->language,
				'headerimg'       => $this->headerimg,
				'subscribe_time'  => $this->subscribe_time,
				'last_time'       => $this->last_time ?: $this->subscribe_time,
				'follow_status'   => $this->follow_status,
				'follow_id'       => $this->follow_id,
				'unionid'         => $this->unionid,
				'subscribe_scene' => $this->subscribe_scene,
			];
		}

		/**
		 * @return array
		 */
		public function dumpMinData ()
		{
			return [
				'fans_id'   => $this->id,
				'openid'    => $this->openid,
				'nickname'  => $this->nickname,
				'remark'    => $this->remark,
				'headerimg' => $this->headerimg,
			];
		}

		/**
		 * 获取关注渠道列表
		 *
		 * @param null $scene
		 *
		 * @return array|mixed
		 */
		public static function getSubscribeScene ($scene = NULL)
		{
			$result = [
				'ADD_SCENE_SEARCH'               => '公众号搜索',
				'ADD_SCENE_ACCOUNT_MIGRATION'    => '公众号迁移',
				'ADD_SCENE_PROFILE_CARD'         => '名片分享',
				'ADD_SCENE_QR_CODE'              => '扫描二维码',
				'ADD_SCENE_PROFILE_LINK'         => '图文页内名称点击',
				'ADD_SCENE_PROFILE_ITEM'         => '图文页右上角菜单',
				'ADD_SCENE_PAID'                 => '支付后关注',
				'ADD_SCENE_WECHAT_ADVERTISEMENT' => '微信广告',
				'ADD_SCENE_OTHERS'               => '其他',
			];

			if (!empty($scene) && !empty($result[$scene])) {
				$result = $result[$scene];
			}

			return $result;
		}

		/**
		 * 获取粉丝 openid 列表
		 *
		 * @param string $appid
		 * @param string $nextOpenid
		 *
		 * @return array
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getFansList ($appid, $nextOpenid = NULL)
		{
			$fansList = [];
			if (!empty($appid)) {
				$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

				if (!empty($wxAuthorize)) {
					$wechat   = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);
					$fansList = $wechat->getMemberList($nextOpenid);
				}
			}

			return $fansList;
		}

		/**
		 * 获取粉丝信息
		 *
		 * @param string       $appid
		 * @param string|array $openid
		 * @param string       $lang
		 *
		 * @return array
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getFansInfo ($appid, $openid, $lang = 'zh_CN')
		{
			$fansInfo = [];

			if (!empty($appid) && !empty($openid)) {
				$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

				if (!empty($wxAuthorize)) {
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					if (is_array($openid)) {
						$openidData[] = $openid;
						if (count($openid) > static::MAX_GET_USER_INFO) {
							$openidData = array_chunk($openid, static::MAX_GET_USER_INFO, true);
						}

						$length = count($openidData);
						for ($i = 0; $i < $length; $i++) {
							$userList = array_values(array_map(function ($openid, $lang = 'zh_CN') {
								return [
									'openid' => $openid,
									'lang'   => $lang,
								];
							}, $openidData[$i]));

							$fansListInfo = $wechat->getMemberInfoBatchGet($userList);
							if (!empty($fansListInfo)) {
								$fansInfo = ArrayHelper::merge($fansInfo, $fansListInfo['user_info_list']);
							}
						}
					} else {
						$fansInfo[] = $wechat->getMemberInfo($openid, $lang);
					}
				}
			}

			return $fansInfo;
		}

		/**
		 * @param int    $authorId
		 * @param string $openid
		 * @param null   $userInfo
		 * @param int    $type 1 粉丝同步
		 *
		 * @return int
		 * @throws InvalidDataException
		 * @throws \yii\db\Exception
		 */
		public static function create ($authorId, $openid, $userInfo = NULL, $type = 0)
		{
			$fansId  = 0;
			$sceneId = 0;
			if (!empty($authorId) && !empty($openid)) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					$fans = static::findOne(['author_id' => $authorId, 'openid' => $openid]);

					if (empty($fans)) {
						$fans              = new Fans();
						$fans->author_id   = $authorId;
						$fans->openid      = $openid;
						$fans->subscribe   = isset($userInfo['subscribe']) ? $userInfo['subscribe'] : static::USER_SUBSCRIBE;
						$fans->create_time = DateUtil::getCurrentTime();
					}

					if (!empty($userInfo)) {
						if (isset($fans->id) && !empty($fans->id)) {
							//同步高级属性性别
							$customField = CustomFieldValue::findOne(['type' => 2, 'fieldid' => 3, 'cid' => $fans->id]);
							if (empty($customField)) {
								if (isset($userInfo['sex'])) {
									if ($userInfo['sex'] == 1) {
										$sex = '男';
									} elseif ($userInfo['sex'] == 2) {
										$sex = '女';
									} else {
										$sex = '未知';
									}
									$customField          = new CustomFieldValue();
									$customField->type    = 2;
									$customField->cid     = $fans->id;
									$customField->fieldid = 3;
									$customField->value   = $sex;
									$customField->time    = time();

									if (!$customField->validate() || !$customField->save()) {
										throw new InvalidDataException(SUtils::modelError($customField));
									}
								}
							} else {
								unset($userInfo['sex']);
							}
						}

						$fans->setAttributes($userInfo);
						if (!empty($fans->qr_scene)) {
							$scene = Scene::findOne(['author_id' => $authorId, 'scene_id' => $fans->qr_scene]);
						}
						if (empty($scene)) {
							unset($fans->qr_scene);
							$fans->qr_scene = NULL;
							$sceneId        = 0;
						} else {
							$fans->qr_scene = $scene->id;
							$sceneId        = $scene->id;
						}

						if (!empty($userInfo['headimgurl'])) {
							$fans->headerimg = $userInfo['headimgurl'];
						}

						$fans->tagid_list = Json::encode($userInfo['tagid_list'], JSON_UNESCAPED_UNICODE);
					}
					if (!empty($fans->qr_scene_str)) {
						//由于临时二维码携带参数过多 先去掉
						unset($fans->qr_scene_str);
					}

					if (empty($fans->follow_id)) {
						$follow_id = Follow::getFollowIdByAuthorId($authorId);
						if (!empty($follow_id)) {
							$fans->follow_id = $follow_id;
						}
					}

					if (!empty($fans->dirtyAttributes)) {
						if ($fans->validate() && $fans->save()) {
							$fansId = $fans->id;

							if (!empty($fans->tagid_list)) {
								// 更新粉丝标签
								$tagsData = Json::decode($fans->tagid_list);
								if (!empty($tagsData)) {
									foreach ($tagsData as $tagsId) {
										$tagsInfo = Tags::findOne(['author_id' => $authorId, 'tag_id' => $tagsId]);
										if (!empty($tagsInfo)) {
											FansTags::create($fansId, $tagsInfo->id);
										}
									}
								}
							}

							if (!empty($fans->subscribe_time)) {
								if (!empty($sceneId)) {
									//更新二维码用户记录
									SceneUserDetail::create($sceneId, $fansId, 1);
								} else {
									$sceneId = 0;
								}
								// 更新粉丝行为
								FansBehavior::create($fansId, $fans->subscribe_time, FansBehavior::FANS_SUBSCRIBE, $sceneId);

							}
						} else {
							throw new InvalidDataException(SUtils::modelError($fans));
						}
					} else {
						$fansId = $fans->id;
					}

					$transaction->commit();
					// 更新粉丝时间线
					if (!empty($fans->subscribe_time)) {
						FansTimeLine::create($fansId, FansTimeLine::SUBSCRIBE_EVENT, $fans->subscribe_time, $sceneId, 0, '', $type);
					}
					//更新企业微信客户是否关注
					if (!empty($fans->unionid)) {
						WorkExternalContact::updateAll(['is_fans' => $fans->subscribe], ['unionid' => $fans->unionid]);
					}
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}
			}

			return $fansId;
		}

		/**
		 * 取消关注修改
		 *
		 * @param int    $authorId
		 * @param string $openid
		 * @param null   $userInfo
		 *
		 * @return int
		 * @throws InvalidDataException
		 * @throws \yii\db\Exception
		 */
		public static function unSubscribe ($authorId, $openid, $createTime)
		{
			$fansId = 0;
			if (!empty($authorId) && !empty($openid)) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					$fans = static::findOne(['author_id' => $authorId, 'openid' => $openid]);
					if (!empty($fans)) {
						$fans->subscribe        = 0;
						$fans->unsubscribe_time = $createTime;
						$fans->tagid_list       = '[]';
						if (!$fans->validate() || !$fans->save()) {
							throw new InvalidDataException(SUtils::modelError($fans));
						}
						$time   = time();
						$fansId = $fans->id;
						if (!empty($fans->qr_scene)) {
							$scene = Scene::findOne(['author_id' => $authorId, 'id' => $fans->qr_scene]);
						}
						$sceneId = 0;
						if (!empty($scene)) {
							$sceneId = $scene->id;
							//删除用户扫码记录
							SceneUserDetail::deleteRecord($sceneId, $fansId);
						}
						//任务宝取关删除
						WorkPublicActivityFansUser::ActivityDnfollowDel($fans->id, $authorId);
						// 更新粉丝行为
						FansBehavior::create($fansId, $time, FansBehavior::FANS_UNSUBSCRIBE, $sceneId);

						//删除未推送的队列
						InteractReply::removeUnPushDetail($fans->openid);

						//删除对应的粉丝标签
						FansTags::deleteAll(['fans_id' => $fansId]);
						$transaction->commit();
						//更新粉丝时间线
						FansTimeLine::create($fansId, FansTimeLine::UNSUBSCRIBE_EVENT, $time, $sceneId);
						//客户粉丝对应关系
						$contact = WorkExternalContact::findOne(['unionid' => $fans->unionid]);
						if (!empty($contact)) {
							$contact->is_fans = 0;
							$contact->save();
						}
					}
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}
			}

			return $fansId;
		}

		/**
		 * 扫码
		 *
		 * @param int    $authorId
		 * @param string $openid
		 * @param string $eventKey
		 *
		 * @return int
		 * @throws InvalidDataException
		 * @throws \yii\db\Exception
		 */
		public static function scanQrCode ($authorId, $openid, $eventKey)
		{
			$fansId = 0;
			$title  = '';
			if (!empty($authorId) && !empty($openid)) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					$fans = static::findOne(['author_id' => $authorId, 'openid' => $openid]);
					if (!empty($fans)) {
						$sceneId = 0;
						$time    = time();
						$fansId  = $fans->id;
						if (!empty($eventKey)) {
							$templateMsg = explode('-', $eventKey);
							if (isset($templateMsg[0]) && $templateMsg[0] == 'template') {
								return $fansId;
							}
							$scene  = Scene::findOne(['author_id' => $authorId, 'scene_id' => $eventKey]);
							$is_new = 0;
							if (!empty($scene)) {
								$sceneId = $scene->id;
								if ($fans->qr_scene == $scene->id) {
									$is_new = 1;
								}
								$title = $scene->title;
								//更新二维码用户记录
								SceneUserDetail::create($sceneId, $fansId, $is_new);
							}
							$activityMsg = explode("_", $eventKey);
							if (count($activityMsg) > 6) {
								array_shift($activityMsg);
							}
							if ($activityMsg[0] == WorkPublicActivity::STATE_NAME) {
								$activity = WorkPublicActivity::findOne($activityMsg[1]);
								$title    = "裂变引流-" . $activity->activity_name;
							}
						}
						// 更新粉丝时间线
						FansTimeLine::create($fansId, FansTimeLine::SCAN_EVENT, $time, $sceneId, 0, $title);
						$transaction->commit();
					}
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}
			}

			return $fansId;
		}

		/**
		 * 获取活跃的粉丝列表（48小时内有互动）
		 *
		 * @param $authId
		 *
		 * @return array
		 */
		public static function getActiveFans ($authId)
		{
			$fansList = [];

			$timeLinePreTime = 48 * 60 * 60;
			$msgPreTime      = 7 * 24 * 60 * 60;

			$fansData = Fans::find()->alias('f');
			$fansData = $fansData->select(['f.*', 'max(fm.create_time) as msg_time']);
			$fansData = $fansData->rightJoin('{{%fans_msg}} fm', '`fm`.`fans_id` = `f`.`id`');
			$fansData = $fansData->rightJoin('{{%fans_time_line}} ftl', '`ftl`.`fans_id` = `f`.`id`');
			$fansData = $fansData->where(['f.author_id' => $authId]);
			$fansData = $fansData->andWhere(['>=', 'fm.create_time', DateUtil::getPreviousSecondsTime($msgPreTime)]);
			$fansData = $fansData->andWhere(['>=', 'ftl.event_time', DateUtil::getPreviousSecondsTime($timeLinePreTime)]);
			$fansData = $fansData->groupBy('f.id')->orderBy(['msg_time' => SORT_DESC])->all();

			if (!empty($fansData)) {
				foreach ($fansData as $fansInfo) {
					$fansListInfo                 = $fansInfo->dumpData();
					$fansListInfo['last_content'] = FansMsg::getMsgList($fansInfo->id, 0, 1, true);
					array_push($fansList, $fansListInfo);
				}
			}

			return $fansList;
		}

		/**
		 * 粉丝数据导出
		 *
		 * @param array $data
		 *
		 * @return array
		 *
		 */
		public static function getExportData ($data)
		{
			$info = [];
			$i    = 0;
			foreach ($data as $fans) {
				$fansTags = $fans->fansTags;
				$tagsInfo = [];
				if (!empty($fansTags)) {
					foreach ($fansTags as $fansTag) {
						array_push($tagsInfo, $fansTag->tags->dumpData());
					}
				}
				$tag_name             = array_column($tagsInfo, 'name');
				$info[$i]['nickname'] = $fans->nickname;
				$info[$i]['openid']   = $fans->openid;
				if ($fans->sex == 1) {
					$info[$i]['sex'] = '男';
				} elseif ($fans->sex == 2) {
					$info[$i]['sex'] = '女';
				} else {
					$info[$i]['sex'] = '未知';
				}
				$info[$i]['subscribe_day']       = DateUtil::getDiffDay($fans->subscribe_time) . '天';
				$info[$i]['interact_nums']       = FansTimeLine::find()->where(['fans_id' => $fans->id])
						->andWhere(['>=', 'event_time', date("Y-m-d H:i:s", $fans->subscribe_time)])
						->andWhere(['source' => 0])
						->andWhere(['event' => [FansTimeLine::SCAN_EVENT, FansTimeLine::CLICK_EVENT, FansTimeLine::VIEW_EVENT, FansTimeLine::SEND_TEXT, FansTimeLine::SEND_IMAGE, FansTimeLine::SEND_VOICE, FansTimeLine::SEND_VIDEO, FansTimeLine::SEND_SHORTVIDEO, FansTimeLine::SEND_LOCATION, FansTimeLine::SEND_LINK]])
						->count() . '次';
				$info[$i]['subscribe_time_str']  = DateUtil::getFormattedTime($fans->subscribe_time);
				$info[$i]['subscribe_scene_str'] = Fans::getSubscribeScene($fans->subscribe_scene);
				$info[$i]['last_time_str']       = !empty($fans->last_time) ? DateUtil::getFormattedTime($fans->last_time) : DateUtil::getFormattedTime($fans->subscribe_time);
				$info[$i]['tags_info']           = !empty($tag_name) ? implode(',', $tag_name) : '';
				$i++;
			}

			return $info;
		}

		/**
		 * 首页指标总览
		 *
		 * @param int $author_id 具体的公众号
		 *
		 * @return array
		 *
		 */
		public static function getIndexFansData ($author_id)
		{
			$start1                = date('Y-m-d', strtotime('-1 day'));
			$start2                = date('Y-m-d', strtotime('-2 day'));
			$fans_static1          = FansStatistic::find()->andWhere(['author_id' => $author_id, 'type' => 1, 'data_time' => $start1])->one();
			$fans_static2          = FansStatistic::find()->andWhere(['author_id' => $author_id, 'type' => 1, 'data_time' => $start2])->one();
			$yesterDayNewCount     = 0;//昨日新增粉丝数
			$lastDayNewCount       = 0;//前日新增粉丝数
			$yesterDayCount        = 0; //昨日总粉丝
			$lastDayCount          = 0; //前日总粉丝
			$yesterActiveCount     = 0; //昨日活跃粉丝数
			$lastActiveCount       = 0; //前日活跃粉丝数
			$yesterCancelFansCount = 0; //昨日取关粉丝数
			$lastCancelFansCount   = 0; //前日取关粉丝数
			$yesterIncrease        = 0; //昨日净增粉丝数
			$lastIncrease          = 0; //前日净增粉丝数
			if (!empty($fans_static1)) {
				$yesterDayNewCount     = $fans_static1->new;
				$yesterDayCount        = $fans_static1->total;
				$yesterActiveCount     = $fans_static1->active;
				$yesterCancelFansCount = $fans_static1->unsubscribe;
				$yesterIncrease        = $fans_static1->net_increase;
			}
			if (!empty($fans_static2) && !empty($fans_static1)) {
				$lastDayNewCount     = $fans_static2->new;
				$lastDayCount        = $fans_static2->total;
				$lastActiveCount     = $fans_static2->active;
				$lastCancelFansCount = $fans_static2->unsubscribe;
				$lastIncrease        = $fans_static2->net_increase;
			}

			//昨日新增
			$one = [];
			if ($yesterDayNewCount >= $lastDayNewCount) {
				$one['status'] = 1; //上升
			} else {
				$one['status'] = 0; //下降
			}
			$num = 0;
			if ($lastDayNewCount > 0) {
				$num = round(abs($yesterDayNewCount - $lastDayNewCount) / $lastDayNewCount, 3);
				$num = $num * 100;
			} else {
				$num = '100';
			}
			if ($yesterDayNewCount == 0 && $lastDayNewCount == 0) {
				$num = 0;
			}
			$num          = sprintf("%.1f", $num);
			$one['count'] = $yesterDayNewCount;
			$one['per']   = $num . '%';

			//昨日取关
			$two = [];
			if ($yesterCancelFansCount >= $lastCancelFansCount) {
				$two['status'] = 1; //上升
			} else {
				$two['status'] = 0; //下降
			}
			$num = 0;
			if ($lastCancelFansCount > 0) {
				$num = round(abs($yesterCancelFansCount - $lastCancelFansCount) / $lastCancelFansCount, 3);
				$num = $num * 100;
			} else {
				$num = '100';
			}
			if ($yesterCancelFansCount == 0 && $lastCancelFansCount == 0) {
				$num = 0;
			}
			$num          = sprintf("%.1f", $num);
			$two['count'] = $yesterCancelFansCount;
			$two['per']   = $num . '%';

			//昨日净增
			$three = [];
			if ($yesterIncrease >= $lastIncrease) {
				$three['status'] = 1; //上升
			} else {
				$three['status'] = 0; //下降
			}
			$num = 0;
			if ($lastIncrease > 0) {
				$num = round(abs($yesterIncrease - $lastIncrease) / $lastIncrease, 3);
				$num = $num * 100;
			} else {
				$num = '100';
			}
			if ($yesterIncrease == 0 && $lastIncrease == 0) {
				$num = 0;
			}
			$num            = sprintf("%.1f", $num);
			$three['count'] = $yesterIncrease;
			$three['per']   = $num . '%';

			//昨日活跃
			$four = [];
			if ($yesterActiveCount >= $lastActiveCount) {
				$four['status'] = 1; //上升
			} else {
				$four['status'] = 0; //下降
			}
			$num = 0;
			if ($lastActiveCount > 0) {
				$num = round(abs($yesterActiveCount - $lastActiveCount) / $lastActiveCount, 3);
				$num = $num * 100;
			} else {
				$num = '100';
			}
			if ($yesterActiveCount == 0 && $lastActiveCount == 0) {
				$num = 0;
			}
			$num           = sprintf("%.1f", $num);
			$four['count'] = $yesterActiveCount;
			$four['per']   = $num . '%';

			//昨日总粉丝
			$five = [];
			if ($yesterDayCount >= $lastDayCount) {
				$five['status'] = 1; //上升
			} else {
				$five['status'] = 0; //下降
			}
			$num = 0;
			if ($lastDayCount > 0) {
				$num = round(abs($yesterDayCount - $lastDayCount) / $lastDayCount, 3);
				$num = $num * 100;
			} else {
				$num = '100';
			}
			if ($yesterDayCount == 0 && $lastDayCount == 0) {
				$num = 0;
			}
			$num           = sprintf("%.1f", $num);
			$five['count'] = $yesterDayCount;
			$five['per']   = $num . '%';

			$result['one']   = $one;
			$result['two']   = $two;
			$result['three'] = $three;
			$result['four']  = $four;
			$result['five']  = $five;

			return $result;
		}

		//获取活跃粉丝数
		public static function getActiveFansNum ($author_id, $s_date, $e_date)
		{
			$fans = Fans::find()->alias('f');
			$fans = $fans->leftJoin('{{%fans_time_line}} ft', '`f`.`id` = `ft`.`fans_id`');
			$fans = $fans->andWhere(['f.author_id' => $author_id, 'ft.source' => 0]);
			$fans = $fans->andWhere(['ft.event' => ['subscribe', 'scan', 'click', 'view', 'text', 'image', 'voice', 'video', 'shortvideo', 'link', 'location']]);
			$fans = $fans->select('ft.id')->andFilterWhere(['between', 'ft.event_time', $s_date, $e_date])->groupBy('ft.fans_id');
			$fans = $fans->asArray()->all();
			$fans = count($fans);

			return $fans;
		}

		//$type 1 新增 2 取关
		public static function getFansCount ($type, $author_id, $date1, $date2)
		{
			if ($type == 1) {
				$event = 'subscribe';
			} else {
				$event = 'unsubscribe';
			}
			$select1 = new Expression('count(ft.id) cc,ft.fans_id');
			$fans1   = Fans::find()->alias('f');
			$fans1   = $fans1->leftJoin('{{%fans_time_line}} ft', '`f`.`id` = `ft`.`fans_id`');
			$fans1   = $fans1->andWhere(['f.author_id' => $author_id, 'ft.event' => $event]);
			$fans1   = $fans1->select($select1)->andFilterWhere(['between', 'ft.event_time', $date1, $date2])->groupBy('ft.fans_id');
			$count   = $fans1->count();

			return $count;
		}

		/**
		 * 粉丝修改备注
		 *
		 * @param $fans_id
		 * @param $remark
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function modifyFansRemark ($fans_id, $remark)
		{
			if (!empty($fans_id)) {
				$fans = static::findOne(['id' => $fans_id]);
				if (!empty($fans)) {
					$wxAuthorize = WxAuthorize::getTokenInfo($fans->author->authorizer_appid, false, true);
					$wechat      = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $fans->author->authorizer_appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					$result = $wechat->updateMemberRemark($fans->openid, $remark);

					if ($result) {
						if (empty($fans->remark)) {
							$fansTimeLineEvent = FansTimeLine::ADD_REMARK_EVENT;
						} else {
							if (!empty($remark)) {
								$fansTimeLineEvent = FansTimeLine::MODIFY_REMARK_EVENT;
							} else {
								$fansTimeLineEvent = FansTimeLine::REMOVE_REMARK_EVENT;
							}
						}
						FansTimeLine::create($fans_id, $fansTimeLineEvent, time(), 0, 0, $remark);

						$fans->remark = $remark;
						if (!$fans->validate() || !$fans->save()) {
							throw new InvalidDataException(SUtils::modelError($fans));
						}
					} else {
						throw new InvalidDataException('修改失败！');
					}
				}
			}

			return true;
		}

		/**
		 * 修改粉丝属性
		 *
		 * $uid
		 * $fans_id
		 * $fieldData
		 */
		public static function modifyFansField ($uid, $fans_id, $fieldData)
		{
			if (!empty($fans_id)) {
				$time     = time();
				$uptField = '';
				foreach ($fieldData as $k => $v) {
					$fieldid = intval($v['fieldid']);
					//$value   = !empty($v['value']) ? trim($v['value'], ',') : '';
					$value = is_array($v['value']) ? $v['value'] : trim($v['value']);
					if (empty($fieldid)) {
						throw new InvalidDataException('客户高级属性数据错误！');
					}

					$fieldValue = CustomFieldValue::findOne(['cid' => $fans_id, 'type' => 2, 'fieldid' => $fieldid]);
					if (empty($fieldValue)) {
						if (empty($value)) {
							continue;
						}
						$fieldValue          = new CustomFieldValue();
						$fieldValue->uid     = $uid;
						$fieldValue->type    = 2;
						$fieldValue->cid     = $fans_id;
						$fieldValue->fieldid = $fieldid;
					} else {
						if ($value == $fieldValue->value) {
							continue;
						}
					}
					if ($v['type'] == 8) {
						$imgVal = json_decode($fieldValue->value, true);
						if ($imgVal == $value) {
							continue;
						}
						$value = json_encode($value);
					}
					$fieldValue->value = $value;
					$fieldValue->time  = $time;

					if ($v['key'] == 'phone') {
						if (!preg_match("/^1[34578]{1}\d{9}$/", $value)) {
							throw new InvalidDataException('手机号格式不正确！');
						}
					} elseif ($v['key'] == 'email') {
						if (!preg_match("/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/", $value)) {
							throw new InvalidDataException('邮箱格式不正确！');
						}
					}

					if (!$fieldValue->save()) {
						throw new InvalidDataException(SUtils::modelError($fieldValue));
					}

					//修改粉丝性别
					if ($v['key'] == 'sex') {
						if ($fieldValue->value == '男') {
							$sex = 1;
						} elseif ($fieldValue->value == '女') {
							$sex = 2;
						} else {
							$sex = 0;
						}
						$fans      = Fans::findOne($fans_id);
						$fans->sex = $sex;
						if (!$fans->save()) {
							throw new InvalidDataException(SUtils::modelError($fans));
						}
					}

					$uptField .= $fieldid . ',';
				}

				//记录客户轨迹
				if (!empty($uptField)) {
					$customField = CustomField::find()->where('id IN (' . trim($uptField, ',') . ')')->select('`title`')->asArray()->all();
					$remark      = '';
					foreach ($customField as $v) {
						$remark .= $v['title'] . '、';
					}
					$fansTimeLineEvent = FansTimeLine::MODIFY_FIELD_EVENT;
					FansTimeLine::create($fans_id, $fansTimeLineEvent, $time, 0, 4, trim($remark, '、'));
				}
			}

			return true;
		}

		public static function getFansCountByType ($type, $author_id, $date1, $date2)
		{
			if ($type == 1) {
				$source  = 'sex';
				$sexData = [];
			} else {
				$source     = 'subscribe_scene';
				$sourceData = [];
			}
			$date1    = strtotime($date1);
			$date2    = strtotime($date2);
			$select   = new Expression('count(id) as cc,' . $source);
			$fansData = Fans::find()->andWhere(['author_id' => $author_id, 'subscribe' => 1]);
			$fansData = $fansData->andWhere(['between', 'subscribe_time', $date1, $date2]);
			$fansData = $fansData->select($select)->groupBy($source);
			$fansData = $fansData->asArray()->all();
			if ($type == 1) {
				if (!empty($fansData)) {
					foreach ($fansData as $data) {
						if ($data['sex'] == 0) {
							$sexData['unknown'] = intval($data['cc']);
						} elseif ($data['sex'] == 1) {
							$sexData['male'] = intval($data['cc']);
						} elseif ($data['sex'] == 2) {
							$sexData['female'] = intval($data['cc']);
						}
					}
				}

				return $sexData;
			} else {
				if (!empty($fansData)) {
					foreach ($fansData as $data) {
						switch ($data['subscribe_scene']) {
							case "ADD_SCENE_SEARCH":
								$sourceData['one'] = intval($data['cc']);
								break;
							case "ADD_SCENE_ACCOUNT_MIGRATION":
								$sourceData['two'] = intval($data['cc']);
								break;
							case "ADD_SCENE_PROFILE_CARD":
								$sourceData['three'] = intval($data['cc']);
								break;
							case "ADD_SCENE_QR_CODE":
								$sourceData['four'] = intval($data['cc']);
								break;
							case "ADD_SCENE_PROFILE_LINK":
								$sourceData['five'] = intval($data['cc']);
								break;
							case "ADD_SCENE_PROFILE_ITEM":
								$sourceData['six'] = intval($data['cc']);
								break;
							case "ADD_SCENE_PAID":
								$sourceData['seven'] = intval($data['cc']);
								break;
							case "ADD_SCENE_OTHERS":
								$sourceData['eight'] = intval($data['cc']);
								break;
						}
					}
				}

				return $sourceData;
			}
		}

		//按照每天 每周 每月 统计粉丝数据
		public static function fans_statistic ($type)
		{
			try {
				\Yii::error($type, '$type');
				if ($type == 1) {
					$start_date = date('Y-m-d', strtotime('-1 day'));
					$end_date   = $start_date . ' 23:59:59';
					$data_time  = $start_date;
				} elseif ($type == 2) {
					$start_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y')));
					$end_date   = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y")));
					$data_time  = $start_date;
				} elseif ($type == 3) {
					$data_time1 = date('Y-m', strtotime('-1 month '));
					$start_date = $data_time1 . '-01';
					$end_date   = date('Y-m-t 23:59:59', strtotime('-1 month'));
					$date       = explode('-', $start_date);
					$data_time  = $start_date;
				}
				$s_date  = strtotime($start_date . ' 23:59:59');
				$s_date1 = date('Y-m-d H:i:s', $s_date - 48 * 3600); //48小时内
				$s_date2 = date('Y-m-d H:i:s', $s_date - 7 * 24 * 3600); //7天内
				$s_date3 = date('Y-m-d H:i:s', $s_date - 15 * 24 * 3600); //15天内
				$s_date  = $start_date . ' 23:59:59';
				$wxAuth  = WxAuthorize::find()->andWhere(['<>', 'authorizer_type', 'unauthorized'])->select('author_id')->all();
				\Yii::error($start_date, '$start_date');
				\Yii::error($end_date, '$end_date');
				\Yii::error($data_time, '$data_time');
				if (!empty($wxAuth)) {
					foreach ($wxAuth as $auth) {
						$male = $female = $unknown = $add_scene_search = $add_scene_account_migration = $add_scene_profile_card = $add_scene_profile_item = $add_scene_qr_code = $add_scene_profile_link = $add_scene_paid = $add_scene_others = 0;
						\Yii::error($auth->author_id, 'author_id');
						$fans_statistic = FansStatistic::findOne(['data_time' => $data_time, 'type' => $type, 'author_id' => $auth->author_id]);
						if (empty($fans_statistic)) {
							$fans_statistic = new FansStatistic();
						}
						//获取总粉丝数
						$total = Fans::find()->andWhere(['subscribe' => 1, 'author_id' => $auth->author_id])->count();
						\Yii::error($total, '$total');
						//新增粉丝数
						$new = static::getFansCount(1, $auth->author_id, $start_date, $end_date);
						//取关粉丝数
						$unsubscribe = static::getFansCount(2, $auth->author_id, $start_date, $end_date);
						//净增粉丝数
						$net_increase = $new - $unsubscribe;
						//活跃粉丝数
						$active = static::getActiveFansNum($auth->author_id, $start_date, $end_date);
						//取关率 取关粉丝数/（取关粉丝数+总粉丝数）
						$cancel_per = '0.0%';
						if ($total > 0) {
							$cancel_per = DateUtil::getPer($unsubscribe, $unsubscribe + $total);
						}
						//48小时活跃粉丝数
						$active_48h = static::getActiveFansNum($auth->author_id, $s_date1, $s_date);
						//7天活跃粉丝数
						$active_7d = static::getActiveFansNum($auth->author_id, $s_date2, $s_date);
						//15天活跃粉丝数
						$active_15d = static::getActiveFansNum($auth->author_id, $s_date3, $s_date);
						//获取性别
						$sexData = static::getFansCountByType(1, $auth->author_id, $start_date, $end_date);
						if (!empty($sexData)) {
							$male    = !empty($sexData['male']) ? $sexData['male'] : 0;
							$female  = !empty($sexData['female']) ? $sexData['female'] : 0;
							$unknown = !empty($sexData['unknown']) ? $sexData['unknown'] : 0;
						}
						//获取来源
						$sourceData = static::getFansCountByType(2, $auth->author_id, $start_date, $end_date);
						if (!empty($sourceData)) {
							$add_scene_search            = !empty($sourceData['one']) ? $sourceData['one'] : 0;
							$add_scene_account_migration = !empty($sourceData['two']) ? $sourceData['two'] : 0;
							$add_scene_profile_card      = !empty($sourceData['three']) ? $sourceData['three'] : 0;
							$add_scene_qr_code           = !empty($sourceData['four']) ? $sourceData['four'] : 0;
							$add_scene_profile_link      = !empty($sourceData['five']) ? $sourceData['five'] : 0;
							$add_scene_profile_item      = !empty($sourceData['six']) ? $sourceData['six'] : 0;
							$add_scene_paid              = !empty($sourceData['seven']) ? $sourceData['seven'] : 0;
							$add_scene_others            = !empty($sourceData['eight']) ? $sourceData['eight'] : 0;
						}
						//活跃比率
						$active_per                                  = DateUtil::getPer($active, $total);
						$fans_statistic->author_id                   = $auth->author_id;
						$fans_statistic->new                         = $new;
						$fans_statistic->unsubscribe                 = $unsubscribe;
						$fans_statistic->net_increase                = $net_increase;
						$fans_statistic->active                      = $active;
						$fans_statistic->total                       = $total;
						$fans_statistic->data_time                   = $data_time;
						$fans_statistic->create_time                 = DateUtil::getCurrentTime();
						$fans_statistic->cancel_per                  = $cancel_per;
						$fans_statistic->active_48h                  = $active_48h;
						$fans_statistic->active_7d                   = $active_7d;
						$fans_statistic->active_15d                  = $active_15d;
						$fans_statistic->active_per                  = $active_per;
						$fans_statistic->type                        = $type;
						$fans_statistic->male                        = $male;
						$fans_statistic->female                      = $female;
						$fans_statistic->unknown                     = $unknown;
						$fans_statistic->add_scene_search            = $add_scene_search;
						$fans_statistic->add_scene_account_migration = $add_scene_account_migration;
						$fans_statistic->add_scene_profile_card      = $add_scene_profile_card;
						$fans_statistic->add_scene_qr_code           = $add_scene_qr_code;
						$fans_statistic->add_scene_profile_link      = $add_scene_profile_link;
						$fans_statistic->add_scene_profile_item      = $add_scene_profile_item;
						$fans_statistic->add_scene_paid              = $add_scene_paid;
						$fans_statistic->add_scene_others            = $add_scene_others;
						if (!$fans_statistic->save()) {
							\Yii::error(SUtils::modelError($fans_statistic), 'fans_statistic_error');
						} else {
							\Yii::error($fans_statistic->id, 'fans_statistic');
						}

					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'fans_statistic');
			}

		}

	}
