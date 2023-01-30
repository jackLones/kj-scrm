<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use callmez\wechat\sdk\Wechat;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%tags}}".
	 *
	 * @property int         $id
	 * @property int         $author_id   公众号ID
	 * @property int         $tag_id      标签ID
	 * @property string      $name        标签名
	 * @property int         $count       此标签下粉丝数
	 * @property int         $wx_fans_num 微信后台标签粉丝数
	 * @property string      $create_time 创建时间
	 *
	 * @property FansTags[]  $fansTags
	 * @property SceneTags[] $sceneTags
	 * @property WxAuthorize $author
	 */
	class Tags extends \yii\db\ActiveRecord
	{
		const NO_TAGS = 0;
		const MAX_OPENID_NUM = 50;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%tags}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'tag_id', 'count', 'wx_fans_num'], 'integer'],
				[['create_time'], 'safe'],
				[['name'], 'string', 'max' => 64],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'author_id'   => Yii::t('app', '公众号ID'),
				'tag_id'      => Yii::t('app', '标签ID'),
				'name'        => Yii::t('app', '标签名'),
				'count'       => Yii::t('app', '此标签下粉丝数'),
				'wx_fans_num' => Yii::t('app', '微信后台标签粉丝数'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansTags ()
		{
			return $this->hasMany(FansTags::className(), ['tags_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSceneTags ()
		{
			return $this->hasMany(SceneTags::className(), ['tag_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return array
		 */
		public function dumpData ()
		{
			return [
				'tag_id' => $this->id,
				'name'   => $this->name,
				'count'  => $this->count,
			];
		}

		/**
		 * 同步微信标签
		 *
		 * @param string $appid
		 *
		 * @return bool
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function syncTagsFromWx ($appid)
		{
			if (!empty($appid)) {
				$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);

				if (!empty($wxAuthorize)) {
					$wechat          = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);
					$wxAuthorizeData = WxAuthorize::findOne(['authorizer_appid' => $appid]);
					//删除系统中存在 微信不存在的标签
					$tagsList = $wechat->getTags();
					$wxtagIds = array_column($tagsList, 'id');
					$tagIds   = static::find()->where(['author_id' => $wxAuthorizeData->author_id])->asArray()->all();
					$tagIdNew = array_column($tagIds, 'tag_id');
					$diff_ids = array_diff($tagIdNew, $wxtagIds);
					if (!empty($diff_ids)) {
						static::deleteTags($diff_ids, $wxAuthorizeData->author_id);
						Tags::deleteAll(['author_id' => $wxAuthorizeData->author_id, 'tag_id' => $diff_ids]);
					}
					if (!empty($tagsList)) {
						foreach ($tagsList as $tagInfo) {
							$tagModel = static::findOne(['author_id' => $wxAuthorizeData->author_id, 'tag_id' => $tagInfo['id']]);

							if (empty($tagModel)) {
								$tagModel              = new Tags();
								$tagModel->author_id   = $wxAuthorizeData->author_id;
								$tagModel->create_time = DateUtil::getCurrentTime();
							}

							$tagModel->setAttributes($tagInfo);
							$tagModel->tag_id      = $tagInfo['id'];
							$tagModel->wx_fans_num = $tagInfo['count'];
							$wxInfo                = WxAuthorizeInfo::find()->where(['author_id' => $wxAuthorizeData->author_id])->one();
							$wxInfo->last_tag_time = DateUtil::getCurrentTime();
							$wxInfo->save();
							if ($tagModel->dirtyAttributes) {
								if (!$tagModel->validate() || !$tagModel->save()) {
									throw new InvalidDataException(SUtils::modelError($tagModel));
								}
							}
						}

						return true;
					}
				}
			}

			return false;
		}

		//删除标签
		private static function deleteTags ($diff_ids, $author_id)
		{
			foreach ($diff_ids as $v) {
				$tagName = Tags::find()->andWhere(['tag_id' => $v, 'author_id' => $author_id])->one();
				SceneTags::deleteAll(['tag_id' => $tagName->id]);
				$fansTags   = FansTags::find(['tags_id' => $tagName->id])->asArray()->all();
				$openid_new = [];
				if (!empty($fansTags)) {
					foreach ($fansTags as $v) {
						$fans = Fans::find()->andWhere(['id' => $v['fans_id']])->one();
						array_push($openid_new, $fans->openid);
					}
					static::deleteFansTag($openid_new, $tagName->id, $tagName->tag_id, $tagName->name);
				}
			}
		}

		/**
		 * 创建标签
		 *
		 * @param string $appId
		 * @param int    $authorId
		 * @param array  $tag_name 标签名称（数组格式）
		 *
		 * @return bool
		 * @throws InvalidDataException
		 * @throws NotAllowException
		 * @throws \app\components\InvalidParameterException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function createTag ($appId, $authorId, array $tag_name)
		{
			if (!empty($appId) && !empty($tag_name)) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					if (!is_array($tag_name)) {
						throw new InvalidParameterException('标签名称必须为数组格式！');
					}
					//获取微信api相关配置
					$wechat = Tags::returnWechat($appId);
					foreach ($tag_name as $value) {
						$len = mb_strlen($value, "utf-8");
						if ($len > 6) {
							throw new InvalidDataException("每个标签最多为6个字");
						}
					}
					$tagName = static::find()->where(['name' => $tag_name, 'author_id' => $authorId])->all();
					if (!empty($tagName)) {
						throw new InvalidParameterException('标签名称存在重复！');
					}
					$count = static::find()->where(['author_id' => $authorId])->count();
//					if($count>=100 || ($count+count($tag_name))>10 ){
//						throw new InvalidParameterException('每个公众号最多添加10个标签！');
//					}
					foreach ($tag_name as $v) {
						$tag = $wechat->createTag($v);
						if (is_array($tag)) {
							$tags              = new Tags();
							$tags->author_id   = $authorId;
							$tags->tag_id      = $tag['id'];
							$tags->name        = $tag['name'];
							$tags->count       = 0;
							$tags->create_time = DateUtil::getCurrentTime();
							if (!$tags->validate() || !$tags->save()) {
								throw new InvalidDataException(SUtils::modelError($tags));
							}
						} else {
							throw new InvalidDataException($tag);
						}
					}
					$transaction->commit();
				} catch (\Exception $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}

				return true;
			} else {
				throw new InvalidParameterException('参数不能为空！');
			}
		}

		/**
		 * 删除标签
		 *
		 * @param $appId
		 * @param $tagId 标签Id pig_tags表的id
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 */
		public static function deleteTag ($appId, $tagId, $author_id)
		{
			if (!empty($tagId) && !empty($appId)) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					$tags    = Tags::findOne($tagId);
					$wxTagId = $tags->tag_id;
					if (empty($wxTagId)) {
						throw new InvalidParameterException('参数不正确！');
					}
					//获取微信api相关配置
					$wechat = Tags::returnWechat($appId);
					$res    = $wechat->deleteTag($wxTagId);
					if ($res == 'ok') {
						FansTags::deleteAll(['tags_id' => $tags->id]);
						SceneTags::deleteAll(['tag_id' => $tags->id]);
						$fansTags = Fans::find()->where(['author_id' => $author_id])->andWhere(['!=', 'tagid_list', '[]'])->select('id,tagid_list')->asArray()->all();
						if (!empty($fansTags)) {
							foreach ($fansTags as $v) {
								$tagid_list = Json::decode($v['tagid_list'], true);
								$fans       = Fans::findOne(['id' => $v['id']]);
								if (in_array($wxTagId, $tagid_list)) {
									$key = array_search($wxTagId, $tagid_list);
									unset($tagid_list[$key]);
									$tagid_new        = array_values($tagid_list);
									$fans->tagid_list = Json::encode($tagid_new, JSON_UNESCAPED_UNICODE);
									$fans->save();
								}
							}
						}
						$tags->delete();
					} else {
						throw new InvalidDataException($res);
					}
					$transaction->commit();
				} catch (\Exception $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}

				return true;
			} else {
				throw new InvalidParameterException('参数不能为空！');
			}
		}

		/**
		 * 更新标签
		 *
		 * @param $appId
		 * @param $tagId   pig_tags表的id
		 * @param $tagName 新的标签名
		 *
		 * @return bool
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function updateTags ($appId, $tagId, $tagName)
		{
			if (!empty($tagId) && !empty($appId) && !empty($tagName)) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					$tags    = Tags::findOne($tagId);
					$wxTagId = $tags->tag_id;
					if (empty($wxTagId)) {
						throw new InvalidParameterException('参数不正确！');
					}

					$len = mb_strlen($tagName, "utf-8");
					if ($len > 6) {
						throw new InvalidDataException("每个标签最多为6个字");
					}
					//获取微信api相关配置
					$wechat = Tags::returnWechat($appId);
					$res    = $wechat->updateTag($wxTagId, $tagName);
					if ($res == 'ok') {
						$tags->name = $tagName;
						$tags->save();
					} else {
						throw new InvalidDataException($res);
					}
					$transaction->commit();
				} catch (\Exception $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}

				return true;
			} else {
				throw new InvalidParameterException('参数不能为空！');
			}
		}

		/**
		 * 获取公众号下的所有标签
		 *
		 * @param $appId
		 *
		 * @return bool|mixed
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getWxTagsList ($appId)
		{
			if (empty($appId)) {
				throw new InvalidParameterException('参数不正确！');
			}
			$wechat = Tags::returnWechat($appId);
			$result = $wechat->getTags();

			return $result;
		}

		/**
		 * 获取标签下的粉丝列表
		 *
		 * @param        $appId
		 * @param        $tagId
		 * @param string $next_openid
		 *
		 * @return bool|mixed
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getOpenIdByTag ($appId, $tagId, $next_openid = "")
		{
			if (empty($appId) || empty($tagId)) {
				throw new InvalidParameterException('参数不正确！');
			}
			$tag    = Tags::findOne(['id' => $tagId]);
			$wechat = Tags::returnWechat($appId);
			$result = $wechat->getUserByTag($tag->tag_id, $next_openid);

			return $result;
		}

		/**
		 * 获取用户的标签列表
		 *
		 * @param $appId
		 * @param $openId
		 *
		 * @return mixed
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getTagByOpenId ($appId, $openId)
		{
			if (empty($appId) || empty($openId)) {
				throw new InvalidParameterException('参数不正确！');
			}
			try {
				$wechat = Tags::returnWechat($appId);
				$result = $wechat->getUserTagsList($openId);
				$data   = [];
				if ($result) {
					$tags = Tags::find()->where(['tag_id' => $result])->asArray()->all();
					$data = array_column($tags, 'name');
				}

				return $data;
			} catch (\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}

		}

		/**
		 * 批量打标签
		 *
		 * @param       $appId
		 * @param       $author_id
		 * @param array $tagIds
		 * @param array $openids
		 * @param int   $type
		 * @param int   $count
		 * @param int   $scene_id
		 *
		 * @return bool|int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function giveUserTags ($appId, $author_id, array $tagIds, array $openids, $type = 0, $count = 0, $scene_id = 0)
		{
			$cc = $count;
			if (empty($appId) || empty($tagIds) || empty($openids)) {
				throw new InvalidParameterException('参数不正确！');
			}
			if (!is_array($tagIds) || !is_array($openids)) {
				throw new InvalidParameterException('标签id和openid必须为数组格式！');
			}

			$wechat = Tags::returnWechat($appId);
			//当标签不在微信后台时则默认新建一个
			$tagsInfo = Tags::find()->where(['author_id' => $author_id, 'id' => $tagIds])->select('tag_id')->asArray()->all();
			$tagsId   = array_column($tagsInfo, 'tag_id');
			$tagsList = $wechat->getTags();
			$wxTagsId = array_column($tagsList, 'id');
			foreach ($tagsId as $id) {
				if (!in_array($id, $wxTagsId)) {
					$tagName    = Tags::find()->where(['tag_id' => $id, 'author_id' => $author_id])->one();
					$fansTags   = FansTags::find()->where(['tags_id' => $tagName->id])->asArray()->all();
					$openid_new = [];
					if (!empty($fansTags)) {
						foreach ($fansTags as $v) {
							$fans = Fans::find()->where(['id' => $v['fans_id']])->one();
							array_push($openid_new, $fans->openid);
						}
					}
					$tag             = $wechat->createTag($tagName->name);
					$tagName->tag_id = $tag['id'];
					$tagName->save();
					if (!empty($openid_new)) {
						static::createNewFansTag($tagName->id, $openid_new, $author_id, $wechat, $scene_id);
					}
				}
			}

			foreach ($tagIds as $tagId) {
				$tag = Tags::findOne(['id' => $tagId]);
				if (empty($tag->tag_id)) {
					throw new InvalidParameterException('参数不正确！');
				}
				//打标签每次最多只能传50个openid
				if (count($openids) > static::MAX_OPENID_NUM) {
					$openidData = array_chunk($openids, static::MAX_OPENID_NUM, true);
					$length     = count($openidData);
					for ($i = 0; $i < $length; $i++) {
						$data = $openidData[$i];
						//遍历每50个openid里的值
						if ($type == 0) {
							if (!empty($data)) {
								$openids = FansTags::getNewOpenids($tagId, $data, 1);
								$res     = $wechat->memberBatchTag($tag->tag_id, $openids);
								if ($res['errcode'] == 0) {
									Tags::createFansTag($author_id, $openids, $tagId, $tag->tag_id, $tag->name, 0, $scene_id);
								} else {
									$count = $count - count($openids);
									\Yii::error($count, 'count1');
									\Yii::error($openids, '$openids1');
									\Yii::error($res, 'res-1');
								}
							}

						} else {
							//移除标签 memberUnBatchTag
							if (!empty($data)) {
								$openids = FansTags::getNewOpenids($tagId, $data, 2);
								$res     = $wechat->memberBatchTag($tag->tag_id, $openids);
								if ($res['errcode'] == 0) {
									Tags::deleteFansTag($openids, $tagId, $tag->tag_id, $tag->name);
								} else {
									$count = $count - count($openids);
									\Yii::error($count, 'count2');
									\Yii::error($openids, '$openids2');
									\Yii::error($res, 'res-2');
								}
							}

						}

					}
				} else {
					if ($type == 0) {
						if (!empty($openids)) {
							$openids1 = FansTags::getNewOpenids($tagId, $openids, 1);
							Yii::error($openids1, 'openids-1');
							$res = $wechat->memberBatchTag($tag->tag_id, $openids1);
							Yii::error($res, 'res-1');
							if ($res['errcode'] == 0) {
								//插入粉丝标签表
								Tags::createFansTag($author_id, $openids1, $tagId, $tag->tag_id, $tag->name, 0, $scene_id);
							} else {
								$count = $count - count($openids1);
								\Yii::error($count, 'count3');
								\Yii::error($openids1, '$openids3');
								\Yii::error($res, 'res-3');
							}
						}
					} else {
						//移除标签 memberUnBatchTag
						if (!empty($openids)) {
							$openids1 = FansTags::getNewOpenids($tagId, $openids, 2);
							$res      = $wechat->memberBatchTag($tag->tag_id, $openids1);
							if ($res['errcode'] == 0) {
								Tags::deleteFansTag($openids1, $tagId, $tag->tag_id, $tag->name);
							} else {
								$count = $count - count($openids1);
								\Yii::error($count, 'count4');
								\Yii::error($openids1, '$openids4');
								\Yii::error($res, 'res-4');
							}
						}
					}
				}
			}

			if ($cc > 0) {
				return $count;
			} else {
				return true;
			}


		}

		//给微信后台默认新建的标签  如果之前的粉丝有则要更新
		public static function createNewFansTag ($tagId, $openids, $author_id, $wechat, $scene_id = 0)
		{
			$tag = Tags::findOne(['id' => $tagId]);
			if (empty($tag->tag_id)) {
				throw new InvalidParameterException('参数不正确！');
			}
			//打标签每次最多只能传50个openid
			if (count($openids) > static::MAX_OPENID_NUM) {
				$openidData = array_chunk($openids, static::MAX_OPENID_NUM, true);
				$length     = count($openidData);
				for ($i = 0; $i < $length; $i++) {
					$data = $openidData[$i];
					//遍历每50个openid里的值
					if (!empty($data)) {
						$res = $wechat->memberBatchTag($tag->tag_id, $openids);
						if ($res['errcode'] == 0) {
							Tags::createFansTag($author_id, $openids, $tagId, $tag->tag_id, $tag->name, 1, $scene_id);
						}
					}
				}
			} else {
				if (!empty($openids)) {
					$res = $wechat->memberBatchTag($tag->tag_id, $openids);
					if ($res['errcode'] == 0) {
						//插入粉丝标签表
						Tags::createFansTag($author_id, $openids, $tagId, $tag->tag_id, $tag->name, 1, $scene_id);
					}
				}
			}

			return true;
		}

		/**
		 * 插入粉丝标签
		 *
		 * @param     $author_id
		 * @param     $openids
		 * @param     $tagId
		 * @param     $wx_tagId
		 * @param     $tagName
		 * @param int $type
		 * @param int $scene_id
		 *
		 * @throws InvalidDataException
		 */
		public static function createFansTag ($author_id, $openids, $tagId, $wx_tagId, $tagName, $type = 0, $scene_id = 0)
		{
			if (!empty($openids)) {
				foreach ($openids as $v) {
					$transaction = Yii::$app->db->beginTransaction();
					try {

						$fansData   = Fans::findOne(['author_id' => $author_id, 'openid' => $v]);
						$tagid_list = Json::decode($fansData->tagid_list, true);
						array_push($tagid_list, $wx_tagId);
						$fansData->tagid_list = Json::encode($tagid_list, JSON_UNESCAPED_UNICODE);
						$fansData->save();

						$fansTag = FansTags::findOne(['fans_id' => $fansData->id, 'tags_id' => $tagId]);
						if (empty($fansTag)) {
							$fansTag              = new FansTags();
							$fansTag->fans_id     = $fansData->id;
							$fansTag->tags_id     = $tagId;
							$fansTag->create_time = DateUtil::getCurrentTime();
							if (!$fansTag->validate() || !$fansTag->save()) {
								throw new InvalidDataException(SUtils::modelError($fansTag));
							}
						}

						if ($type == 0) {
							//插入粉丝轨迹
							FansTimeLine::create($fansData->id, 'give_tag', time(), $scene_id, 0, $tagName);
						}

						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						\Yii::error($e->getMessage(), 'createFansTag');
					}
				}
			}
		}

		/**
		 * 获取微信后台粉丝数
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getFansCountByTagId ()
		{
			$tagsData = Tags::find()->all();

			if (!empty($tagsData)) {
				foreach ($tagsData as $tag) {
					$wechat      = Tags::returnWechat($tag->author->authorizer_appid);
					$tagsResult  = $wechat->getUserByTag($tag->tag_id);
					$wxFansCount = $tagsResult['count'] ?: 0;

					$tag->wx_fans_num = $wxFansCount;
					if (!empty($tag->dirtyAttributes)) {
						if ($tag->validate()) {
							$tag->save();
						}
					}
				}
			}
		}

		/**
		 * 删除粉丝标签
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/10/23 13:20
		 * @number          0
		 *
		 */
		public static function deleteFansTag ($openids, $tagId, $wx_tagId, $tagsName)
		{
			if (!empty($openids)) {
				foreach ($openids as $v) {
					try {
						$transaction = Yii::$app->db->beginTransaction();
						$fans        = Fans::findOne(['openid' => $v]);
						$fansTags    = FansTags::findOne(['tags_id' => $tagId, 'fans_id' => $fans->id]);
						if ($fansTags) {
							FansTags::deleteAll(['id' => $fansTags->id]);
							$fansTags->delete();
						}
						$tagid_list = Json::decode($fans->tagid_list, true);
						if (in_array($wx_tagId, $tagid_list)) {
							$key = array_search($wx_tagId, $tagid_list);
							unset($tagid_list[$key]);
							$tagid_new        = array_values($tagid_list);
							$fans->tagid_list = Json::encode($tagid_new, JSON_UNESCAPED_UNICODE);
							$fans->save();
						}
						//插入粉丝轨迹
						if (!empty($tagsName)) {
							FansTimeLine::create($fans->id, 'remove_tag', time(), 0, 0, $tagsName);
						}
						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						\Yii::error($e->getMessage(), 'deleteFansTag');
					}

				}
			}

		}

		/**
		 * 微信API公共配置
		 *
		 * @param $appid
		 *
		 * @return Wechat
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function returnWechat ($appid)
		{
			$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);
			if (!empty($wxAuthorize)) {
				$wechat = \Yii::createObject([
					'class'          => Wechat::className(),
					'appId'          => $appid,
					'appSecret'      => $wxAuthorize['config']->appSecret,
					'token'          => $wxAuthorize['config']->token,
					'componentAppId' => $wxAuthorize['config']->appid,
				]);
			}

			return $wechat;
		}
	}
