<?php

	namespace app\models;

	use app\util\MsgUtil;
	use Yii;
	use app\components\InvalidParameterException;
	use app\components\InvalidDataException;
	use callmez\wechat\sdk\Wechat;
	use app\util\WxConstUtil;

	/**
	 * This is the model class for table "{{%scene}}".
	 *
	 * @property int                $id           场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
	 * @property int                $author_id    公众号ID
	 * @property string             $title        二维码标题
	 * @property string             $action_name  二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值
	 * @property string             $scene_id     二维码场景值ID，临时二维码时从100001开始的整型，永久二维码时最大值为100000（目前参数只支持1--100000）
	 * @property string             $scene_str    场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
	 * @property string             $scene_expire 该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
	 * @property string             $ticket       获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
	 * @property string             $url          二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
	 * @property int                $status       是否启用，1：启用、0：不启用
	 * @property int                $push_type    推送方式，1：随机推送一条、2：全部推送
	 * @property string             $create_time  创建时间
	 * @property string             $reply_sort   消息回复的排序，多个时用逗号分割
	 * @property string             $local_path   二维码图片本地地址
	 *
	 * @property Fans[]             $fans
	 * @property ReplyInfo[]        $replyInfos
	 * @property WxAuthorize        $author
	 * @property SceneTags[]        $sceneTags
	 * @property SceneUserDetails[] $SceneUserDetails
	 */
	class Scene extends \yii\db\ActiveRecord
	{
		const ACTION_QR_SCENE = 'QR_SCENE';
		const ACTION_QR_STR_SCENE = 'QR_STR_SCENE';
		const ACTION_QR_LIMIT_SCENE = 'QR_LIMIT_SCENE';
		const ACTION_QR_LIMIT_STR_SCENE = 'QR_LIMIT_STR_SCENE';

		const SCENE_CLOSE = 0;
		const SCENE_OPEN = 1;

		const RAND_PUSH = 1;
		const ALL_PUSH = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%scene}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'status', 'push_type'], 'integer'],
				[['action_name'], 'required'],
				[['url'], 'string'],
				[['create_time'], 'safe'],
				[['title'], 'string', 'max' => 20],
				[['action_name'], 'string', 'max' => 32],
				[['scene_str'], 'string', 'max' => 64],
				[['scene_expire'], 'string', 'max' => 16],
				[['ticket'], 'string', 'max' => 128],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', '场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）'),
				'author_id'    => Yii::t('app', '公众号ID'),
				'title'        => Yii::t('app', '二维码标题'),
				'action_name'  => Yii::t('app', '二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值'),
				'scene_id'     => Yii::t('app', '二维码场景值ID，临时二维码时从100001开始的整型，永久二维码时最大值为100000（目前参数只支持1--100000）'),
				'scene_str'    => Yii::t('app', '场景值ID（字符串形式的ID），字符串类型，长度限制为1到64'),
				'scene_expire' => Yii::t('app', '该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。'),
				'ticket'       => Yii::t('app', '获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。'),
				'url'          => Yii::t('app', '二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片'),
				'status'       => Yii::t('app', '是否启用，1：启用、0：不启用'),
				'push_type'    => Yii::t('app', '推送方式，1：随机推送一条、2：全部推送'),
				'create_time'  => Yii::t('app', '创建时间'),
				'reply_sort'   => Yii::t('app', '消息回复的排序，多个时用逗号分割'),
				'local_path'   => Yii::t('app', '二维码图片本地地址'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasMany(Fans::className(), ['qr_scene' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getReplyInfos ()
		{
			return $this->hasMany(ReplyInfo::className(), ['scene_id' => 'id']);
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
		public function getSceneTags ()
		{
			return $this->hasMany(SceneTags::className(), ['scene_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSceneUserDetails ()
		{
			return $this->hasMany(SceneUserDetail::className(), ['scene_id' => 'id']);
		}

		//参数二维码发送
		public static function sceneSend ($data)
		{
			if (empty($data['author_id']) || empty($data['openid']) || empty($data['eventKey'])) {
				return '';
			}
			\Yii::error($data, 'eventKey');
			$author_id = $data['author_id'];
			//关注和扫描时eventKey值是不一样的，关注时会带上qrscene_，扫描时则不带
			if ($data['event'] == WxConstUtil::WX_SUBSCRIBE_EVENT) {
				$eventKeyArr = explode('_', $data['eventKey']);
				$scene_id    = $eventKeyArr[1];
			} elseif ($data['event'] == WxConstUtil::WX_SCAN_EVENT) {
				$scene_id = $data['eventKey'];
			}

			//判断此临时二维码是来源于模板消息预览
			TemplatePushMsg::sendPreviewMessage($scene_id, $author_id, $data['openid']);
			$scene_id = intval($scene_id);
			if (empty($scene_id)) {
				return '';
			}
			$scene = static::findOne(['author_id' => $author_id, 'scene_id' => $scene_id]);
			if (empty($scene)) {
				return '';
			}

			$fans = Fans::findOne(['author_id' => $author_id, 'openid' => $data['openid']]);
			if (empty($fans)) {
				return '';
			}

			$time         = !empty($data['time']) ? $data['time'] : time();
			$toUserName   = $data['openid'];
			$fromUserName = $data['user_name'];

			$wxAuthorize = WxAuthorize::getTokenInfo($scene->author->authorizer_appid, false, true);
			if (empty($wxAuthorize)) {
				return '';
			}
			//获取规则粉丝标签 关注时给用户打标签
			if ($data['event'] == WxConstUtil::WX_SUBSCRIBE_EVENT && !empty($scene->sceneTags)) {
				$tagIds = array_column($scene->sceneTags, 'tag_id');
				try {
					Tags::giveUserTags($scene->author->authorizer_appid, $author_id, $tagIds, [$data['openid']], 0, 0, $scene->id);
				} catch (InvalidDataException $e) {
					\Yii::error('openid: ' . $data['openid'] . ', error_msg: ' . $e->getMessage());
				}
			}
			$site_url = \Yii::$app->params['site_url'];
			//推送内容
			if (!empty($scene->status)) {
				$pattenArr = [
					'nickname' => $fans->nickname
				];
				if (empty($scene->reply_sort)) {
					return '';
				}
				$replyInfo = ReplyInfo::find()->where('id in(' . $scene->reply_sort . ')')->orderBy(["FIELD(id," . $scene->reply_sort . ")" => true])->asArray()->all();
				$replyList = [];
				foreach ($replyInfo as $rv) {
					if ($rv['type'] == 5) {
						if (!isset($tempId)) {
							$tempId                     = $rv['id'];
							$replyList[$tempId]['type'] = 5;
						}
						$from = 0;//1来自导入
						if (empty($rv['is_use']) && !empty($rv['attachment_id'])) {
							$attachment = Attachment::findOne($rv['attachment_id']);
							if (empty($attachment->status)) {
								break;
							} else {
								$from = 1;
							}
						}
						if (!empty($rv['material_id']) && empty($rv['title'])) {
							$material = Material::findOne(['id' => $rv['material_id']]);
							$article  = Article::find()->alias('a');
							$article  = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
							$article  = $article->where(['a.id' => $material->article_sort])->select('a.title,a.digest,a.content_source_url,m.local_path')->asArray()->one();
							$pic_url  = '';
							if (!empty($article['local_path'])) {
								$pic_url = $site_url . $article['local_path'];
							}
							$title              = $article['title'];
							$digest             = $article['digest'];
							$content_source_url = $article['content_source_url'];
							if ($from == 1) {
								$title              = $attachment->file_name;
								$digest             = $attachment->content;
								$content_source_url = $attachment->jump_url;
								$pic_url            = $site_url . $attachment->local_path;
							}
							$replyList[$tempId]['content'][] = ['type' => $rv['type'], 'title' => $title, 'digest' => $digest, 'content_url' => $content_source_url, 'pic_url' => $pic_url];
						} else {
							$title   = static::pregReplaceCallback($rv['title'], $pattenArr);
							$pic_url = '';
							if (!empty($rv['cover_url'])) {
								$pic_url = $site_url . $rv['cover_url'];
							}
							$digest      = $rv['digest'];
							$content_url = $rv['content_url'];
							if ($from == 1) {
								$title       = $attachment->file_name;
								$digest      = $attachment->content;
								$content_url = $attachment->jump_url;
								$pic_url     = $site_url . $attachment->local_path;
							}
							$replyList[$tempId]['content'][] = ['type' => $rv['type'], 'title' => $title, 'digest' => $digest, 'content_url' => $content_url, 'pic_url' => $pic_url];
						}
					} elseif ($rv['type'] == 1) {
						$content              = static::pregReplaceCallback(rawurldecode($rv['content']), $pattenArr);
						$replyList[$rv['id']] = ['type' => $rv['type'], 'content' => $content];
					} else {
						$attachment = Attachment::findOne($rv['attachment_id']);
						$material   = Material::getMaterial(['author_id' => $scene->author_id, 'attachment_id' => $rv['attachment_id'], 'file_type' => $attachment->file_type]);
						MsgUtil::checkNeedReload($material);
						$title   = !empty($material->file_name) ? $material->file_name : '';
						$digest  = '';//先不加
						$pic_url = '';
						if (!empty($material->local_path)) {
							$pic_url = $site_url . $material->local_path;
						}
						$replyList[$rv['id']] = ['type' => $rv['type'], 'content' => $material->media_id, 'attachment_id' => $attachment->id, 'material_id' => $rv['material_id'], 'pic_url' => $pic_url, 'title' => $title, 'digest' => $digest];
					}
				}
				if ($scene->push_type == 1) {
					$xmlData = [];
					$typeArr = [];
					foreach ($replyList as $rv) {
						if ($rv['type'] == 1) {
							$typeArr[] = 'text';
							$xmlData[] = "<xml>
										  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
										  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
										  <CreateTime>" . $time . "</CreateTime>
										  <MsgType><![CDATA[text]]></MsgType>
										  <Content><![CDATA[" . $rv['content'] . "]]></Content>
										</xml>";
						} elseif ($rv['type'] == 2) {
							$typeArr[] = 'image';
							$xmlData[] = "<xml>
										  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
										  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
										  <CreateTime>" . $time . "</CreateTime>
										  <MsgType><![CDATA[image]]></MsgType>
										  <Image>
										    <MediaId><![CDATA[" . $rv['content'] . "]]></MediaId>
										  </Image>
										</xml>";
						} elseif ($rv['type'] == 3) {
							$typeArr[] = 'voice';
							$xmlData[] = "<xml>
										  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
										  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
										  <CreateTime>" . $time . "</CreateTime>
										  <MsgType><![CDATA[voice]]></MsgType>
										  <Voice>
										    <MediaId><![CDATA[" . $rv['content'] . "]]></MediaId>
										  </Voice>
										</xml>";
						} elseif ($rv['type'] == 4) {
							$typeArr[] = 'video';
							$xmlData[] = "<xml>
										  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
										  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
										  <CreateTime>" . $time . "</CreateTime>
										  <MsgType><![CDATA[video]]></MsgType>
										  <Video>
										    <MediaId><![CDATA[" . $rv['content'] . "]]></MediaId>
										    <Title><![CDATA[" . $rv['title'] . "]]></Title>
										    <Description><![CDATA[" . $rv['digest'] . "]]></Description>
										  </Video>
										</xml>";
						} elseif ($rv['type'] == 5) {
							$typeArr[]    = 'news';
							$articles     = '';
							$articleCount = 0;
							foreach ($rv['content'] as $rvv) {
								if ($articleCount > 8) {
									continue;
								}
								$articles .= "<item>
										      <Title><![CDATA[" . $rvv['title'] . "]]></Title>
										      <Description><![CDATA[" . $rvv['digest'] . "]]></Description>
										      <PicUrl><![CDATA[" . $rvv['pic_url'] . "]]></PicUrl>
										      <Url><![CDATA[" . $rvv['content_url'] . "]]></Url>
										    </item>";
								$articleCount++;
							}
							$xmlData[] = "<xml>
										  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
										  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
										  <CreateTime>" . $time . "</CreateTime>
										  <MsgType><![CDATA[news]]></MsgType>
										  <ArticleCount>" . $articleCount . "</ArticleCount>
										  <Articles>" . $articles . "</Articles>
										</xml>";
						}
					}
					$count  = count($xmlData);
					$random = rand(0, $count - 1);
					//shuffle($xmlData);
					$type = $typeArr[$random];

					return ['push_type' => 1, 'replyMsg' => $xmlData[$random], 'title' => $scene->title, 'type' => $type];
				} elseif ($scene->push_type == 2) {
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $scene->author->authorizer_appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					foreach ($replyList as $rv) {
						if ($rv['type'] == 1) {
							$result = $wechat->sendText($toUserName, $rv['content']);
							if ($result['errcode'] == 0) {
								FansTimeLine::create($fans->id, 'text', time(), 0, 2, $scene->title);
							}
						} elseif ($rv['type'] == 2) {
							$result = $wechat->sendImage($toUserName, $rv['content']);
							if ($result['errcode'] == 0) {
								FansTimeLine::create($fans->id, 'image', time(), 0, 2, $scene->title);
							}
						} elseif ($rv['type'] == 3) {
							$result = $wechat->sendVoice($toUserName, $rv['content']);
							if ($result['errcode'] == 0) {
								FansTimeLine::create($fans->id, 'voice', time(), 0, 2, $scene->title);
							}
						} elseif ($rv['type'] == 4) {
							$title      = $rv['title'];
							$attachment = Attachment::findOne($rv['attachment_id']);
							if (!empty($attachment->file_name)) {
								$title = $attachment->file_name;
							}
							$result = $wechat->sendVideo($toUserName, $rv['content'], '', $title, $rv['digest']);
							if ($result['errcode'] == 0) {
								FansTimeLine::create($fans->id, 'video', time(), 0, 2, $scene->title);
							}
						} elseif ($rv['type'] == 5) {
							//如果全部推送中有图文消息，则用被动回复用户消息
							$articles     = '';
							$articleCount = 0;
							foreach ($rv['content'] as $rvv) {
								if ($articleCount > 8) {
									continue;
								}
								$articles .= "<item>
										      <Title><![CDATA[" . $rvv['title'] . "]]></Title>
										      <Description><![CDATA[" . $rvv['digest'] . "]]></Description>
										      <PicUrl><![CDATA[" . $rvv['pic_url'] . "]]></PicUrl>
										      <Url><![CDATA[" . $rvv['content_url'] . "]]></Url>
										    </item>";
								$articleCount++;
							}
							$xmlData = "<xml>
									  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
									  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
									  <CreateTime>" . $time . "</CreateTime>
									  <MsgType><![CDATA[news]]></MsgType>
									  <ArticleCount>" . $articleCount . "</ArticleCount>
									  <Articles>" . $articles . "</Articles>
									</xml>";
						}
					}
					if (!empty($xmlData)) {
						return ['push_type' => 1, 'replyMsg' => $xmlData, 'title' => $scene->title, 'type' => 'news'];
					} else {
						return ['push_type' => 2, 'replyMsg' => ''];
					}
				}
			}

			return '';
		}

		/**
		 * @name         正则批量替换变量
		 * @info 可匹配｛｝[] 符号变量
		 *
		 * @param string $string 带有变量的字符串   “[y]年[d]月”
		 * @param array  $array  变量数组键名一定要和字符串中的变量名的一样    ['y'=>1970,'d'=>'2']
		 *
		 * @return mixed|string
		 */
		public static function pregReplaceCallback ($string = '', $array = [])
		{
			$GLOBALS['pregReplaceCallbackArr'] = $array;
			$_patten                           = '/(\{|\[)([\w]+)(\]|\})/';
			if (preg_match($_patten, $string, $preg_match)) {
				$string = preg_replace_callback(
					$_patten,
					function ($matches) {
						return $GLOBALS['pregReplaceCallbackArr'][$matches[2]];
					},
					$string
				);
			}

			return $string;
		}

		//unicode解码方法
		public static function unicodeDecode ($unicode_str)
		{
			$unicode_str = rawurldecode($unicode_str);
			$json        = '{"str":"' . $unicode_str . '"}';
			$arr         = json_decode($json, true);
			if (empty($arr)) {
				return '';
			}

			return $arr['str'];
		}

		//检查数据
		public static function checkData ($msgData)
		{
			$msg = '';
			foreach ($msgData as $mv) {
				if ($mv['type'] == 5) {
					if (empty($mv['newsList'])) {
						$msg = '请填写图文消息';
						break;
					}
					foreach ($mv['newsList'] as $nv) {
						if (!empty($nv['is_use'])) {
							if (empty($nv['title'])) {
								$msg = '请填写标题';
								break 2;
							}
							if (empty($nv['cover_url'])) {
								$msg = '请选择图片封面';
								break 2;
							}
							if (empty($nv['content_url'])) {
								$msg = '请填写跳转链接';
								break 2;
							} else {
								$content_url = strtolower($nv['content_url']);
								$pattern     = '/(http|https)(.)*([a-z0-9\-\.\_])+/i';
								if (!preg_match($pattern, $content_url)) {
									$msg = '跳转链接格式不正确';
									break 2;
								}
							}
							if (!empty($nv['is_sync'])) {
								if (empty($nv['group_id'])) {
									$msg = '请选择分组';
									break 2;
								}
							}
						} else {
							if (empty($nv['material_id'])) {
								$msg = '请选择素材';
								break 2;
							} else {
								$attachment = Attachment::findOne($nv['material_id']);
								if (empty($attachment) || $attachment->status == 0) {
									$msg = '素材已被删除，请重新编辑后再发送';
									break 2;
								}
							}
						}
					}
				} elseif ($mv['type'] == 1) {
					if (empty($mv['content'])) {
						$msg = '请填写文字消息';
						break;
					}
				} else {
					if (empty($mv['material_id'])) {
						$msg = '请选择素材';
						break;
					} else {
						$attachment = Attachment::findOne($mv['material_id']);
						if (empty($attachment) || $attachment->status == 0) {
							$msg = '素材已被删除，请重新编辑后再发送';
							break;
						}
					}
				}
			}

			return $msg;
		}
	}
