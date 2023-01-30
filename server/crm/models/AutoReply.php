<?php

	namespace app\models;

	use phpDocumentor\Reflection\Types\Static_;
	use Yii;
	use app\util\WxConstUtil;
	use callmez\wechat\sdk\Wechat;

	/**
	 * This is the model class for table "{{%auto_reply}}".
	 *
	 * @property int         $id
	 * @property int         $author_id         公众号ID
	 * @property int         $replay_type       自动回复分类，1：关注后自动回复、2：消息自动回复
	 * @property int         $status            是否开启，0代表未开启，1代表开启
	 * @property string      $create_time       创建时间
	 * @property string      $push_type         推送方式，1：随机推送一条、2：全部推送
	 * @property string      $time_json         时间段json格式
	 *
	 * @property WxAuthorize $author
	 * @property ReplyInfo[] $replyInfos
	 */
	class AutoReply extends \yii\db\ActiveRecord
	{
		const SUBSCRIBE_REPLY = 1;
		const MEG_AUTO_REPLY = 2;

		const REPLAY_CLOSE = 0;
		const REPLAY_OPEN = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%auto_reply}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'replay_type', 'status'], 'integer'],
				[['create_time'], 'safe'],
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
				'replay_type' => Yii::t('app', '自动回复分类，1：关注后自动回复、2：消息自动回复'),
				'status'      => Yii::t('app', '是否开启，0代表未开启，1代表开启'),
				'create_time' => Yii::t('app', '创建时间'),
				'push_type'   => Yii::t('app', '推送方式，1：随机推送一条、2：全部推送'),
				'time_json'   => Yii::t('app', '时间段json格式'),
			];
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
		public function getReplyInfos ()
		{
			return $this->hasMany(ReplyInfo::className(), ['rp_id' => 'id']);
		}

		//关注时自动回复
		public static function sendMessage ($data)
		{
			Yii::error($data, 'autoReply');
			$author_id   = $data['author_id'];
			$eventKeyArr = explode('_', $data['eventKey']);
			$scene_id    = $eventKeyArr[1];
			if (!empty($scene_id)) {
				$scene = Scene::find()->where(['author_id' => $author_id, 'scene_id' => $scene_id, 'status' => 1])->one();
				if (!empty($scene)) {
					Yii::error($scene->id, 'autoReply-1');

					return '';
				}
			}
			$time         = !empty($data['time']) ? $data['time'] : time();
			$toUserName   = $data['openid'];
			$fromUserName = $data['user_name'];
			$fans         = Fans::find()->where(['author_id' => $author_id, 'openid' => $data['openid']])->one();
			$auto         = static::find()->where(['author_id' => $author_id, 'replay_type' => 1])->one();
			if (!empty($auto)) {
				$wxAuthorize = WxAuthorize::getTokenInfo($auto->author->authorizer_appid, false, true);
			}
			$host = \Yii::$app->params['site_url'];
			if (!empty($auto->status)) {
				$pattenArr = [
					'nickname' => $fans->nickname
				];
				$replyInfo = $auto->replyInfos;
				foreach ($replyInfo as $rv) {
					if ($rv['type'] != 1) {
						$temp    = Material::findOne(['id' => $rv['material_id']]);
						$pic_url = '';
						if (!empty($temp->local_path)) {
							$pic_url = $host . $temp->local_path;
						}
					}
					if ($rv['type'] == 5) {
						if (!isset($tempId)) {
							$tempId                     = $rv['id'];
							$replyList[$tempId]['type'] = 5;
						}
						$title                           = Scene::pregReplaceCallback($rv['title'], $pattenArr);
						$replyList[$tempId]['content'][] = ['type' => $rv['type'], 'title' => $title, 'digest' => $rv['digest'], 'content_url' => $rv['content_url'], 'content' => $rv['content'], 'material_id' => $rv['material_id'], 'pic_url' => $pic_url];
					} elseif ($rv['type'] == 1) {
						$content              = Scene::pregReplaceCallback($rv['content'], $pattenArr);
						$replyList[$rv['id']] = ['type' => $rv['type'], 'content' => $content];
					} else {
						$title                = !empty($temp->title) ? $temp->title : '';
						$digest               = !empty($temp->introduction) ? $temp->introduction : '';
						$replyList[$rv['id']] = ['type' => $rv['type'], 'content' => $rv['content'], 'material_id' => $rv['material_id'], 'pic_url' => $pic_url, 'title' => $title, 'digest' => $digest];
					}
				}

				if ($auto->push_type == 1) {
					$xmlData = [];
					foreach ($replyList as $rv) {
						if ($rv['type'] == 1) {
							$xmlData[]
								= "<xml>
  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
  <CreateTime>" . $time . "</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[" . $rv['content'] . "]]></Content>
</xml>";
						} elseif ($rv['type'] == 2) {
							$xmlData[]
								= "<xml>
  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
  <CreateTime>" . $time . "</CreateTime>
  <MsgType><![CDATA[image]]></MsgType>
  <Image>
    <MediaId><![CDATA[" . $rv['content'] . "]]></MediaId>
  </Image>
</xml>";
						} elseif ($rv['type'] == 3) {
							$xmlData[]
								= "<xml>
  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
  <CreateTime>" . $time . "</CreateTime>
  <MsgType><![CDATA[voice]]></MsgType>
  <Voice>
    <MediaId><![CDATA[" . $rv['content'] . "]]></MediaId>
  </Voice>
</xml>";
						} elseif ($rv['type'] == 4) {
							$xmlData[]
								= "<xml>
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
							$articles     = '';
							$articleCount = count($rv);
							foreach ($rv['content'] as $rvv) {
								$articles
									.= "<item>
      <Title><![CDATA[" . $rvv['title'] . "]]></Title>
      <Description><![CDATA[" . $rvv['digest'] . "]]></Description>
      <PicUrl><![CDATA[" . $rvv['pic_url'] . "]]></PicUrl>
      <Url><![CDATA[" . $rvv['content_url'] . "]]></Url>
    </item>";
							}
							$xmlData[]
								= "<xml>
  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
  <CreateTime>" . $time . "</CreateTime>
  <MsgType><![CDATA[news]]></MsgType>
  <ArticleCount>" . $articleCount . "</ArticleCount>
  <Articles>" . $articles . "</Articles>
</xml>";
						}
					}
					shuffle($xmlData);

					return ['push_type' => 1, 'replyMsg' => $xmlData[0]];
				} elseif ($auto->push_type == 2) {
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $auto->author->authorizer_appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);

					foreach ($replyList as $rv) {
						if ($rv['type'] == 1) {
							$wechat->sendText($toUserName, $rv['content']);
						} elseif ($rv['type'] == 2) {
							$wechat->sendImage($toUserName, $rv['content']);
						} elseif ($rv['type'] == 3) {
							$wechat->sendVoice($toUserName, $rv['content']);
						} elseif ($rv['type'] == 4) {
							$wechat->sendVideo($toUserName, $rv['content'], $rv['content'], $rv['title'], $rv['digest']);
						} elseif ($rv['type'] == 5) {
							$articles = [];
							//发送客服图文消息时，只能发送一条
							foreach ($rv['content'] as $rvv) {
								$articles[] = [
									"title"       => $rvv['title'],
									"description" => $rvv['digest'],
									"url"         => $rvv['content_url'],
									"picurl"      => $rvv['pic_url']
								];
								break;
							}
							$wechat->sendNews($toUserName, $articles);
						}
					}

					return ['push_type' => 2, 'replyMsg' => ''];
				}

			}

			return '';
		}
	}
