<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%reply_info}}".
	 *
	 * @property int       $id
	 * @property int       $kw_id         关键词ID
	 * @property int       $menu_keyword_id 菜单关键词ID
	 * @property int       $rp_id         自动回复ID
	 * @property int       $scene_id      参数二维码ID
	 * @property int       $type          回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）
	 * @property string    $content       对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID
	 * @property int       $material_id   素材库ID
	 * @property int       $attachment_id 附件ID
	 * @property string    $title         图文消息的标题
	 * @property string    $digest        图文消息的摘要
	 * @property string    $author        图文消息的作者
	 * @property int       $show_cover    是否显示封面，0为不显示，1为显示
	 * @property string    $cover_url     封面图片的URL
	 * @property string    $content_url   正文的URL
	 * @property string    $source_url    原文的URL，若置空则无查看原文入口
	 * @property int       $status        是否开启，0代表未开启，1代表开启
	 * @property string    $create_time   创建时间
	 * @property string    $is_sync       是否同步文件柜
	 * @property string    $is_use        是否是自定义图文
	 * @property string    $attach_id     同步文件柜的id
     * @property string $appid 小程序的appid
     * @property string $pagepath 小程序的页面路径
	 *
	 * @property Keyword   $kw
	 * @property Material  $material
	 * @property AutoReply $rp
	 * @property Scene     $scene
	 */
	class ReplyInfo extends \yii\db\ActiveRecord
	{
		const TEXT_REPLY = 1;
		const IMG_REPLY = 2;
		const VOICE_REPLY = 3;
		const VIDEO_REPLY = 4;
		const NEWS_REPLY = 5;

		const HIDE_COVER_PIC = 0;
		const SHOW_COVER_PIC = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%reply_info}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['kw_id', 'menu_keyword_id', 'rp_id', 'scene_id', 'type', 'material_id', 'show_cover', 'status'], 'integer'],
				[['type'], 'required'],
				[['content', 'cover_url', 'content_url', 'source_url'], 'string'],
				[['create_time'], 'safe'],
				[['title'], 'string', 'max' => 64],
                [['digest', 'appid', 'pagepath'], 'string', 'max' => 255],
				[['author'], 'string', 'max' => 16],
				[['kw_id'], 'exist', 'skipOnError' => true, 'targetClass' => Keyword::className(), 'targetAttribute' => ['kw_id' => 'id']],
				[['menu_keyword_id'], 'exist', 'skipOnError' => true, 'targetClass' => WechatMenusKeywordRelation::class, 'targetAttribute' => ['menu_keyword_id' => 'id']],
				[['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Material::className(), 'targetAttribute' => ['material_id' => 'id']],
				[['rp_id'], 'exist', 'skipOnError' => true, 'targetClass' => AutoReply::className(), 'targetAttribute' => ['rp_id' => 'id']],
				[['scene_id'], 'exist', 'skipOnError' => true, 'targetClass' => Scene::className(), 'targetAttribute' => ['scene_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'kw_id'         => Yii::t('app', '关键词ID'),
				'menu_keyword_id'=> Yii::t('app', '菜单关键词ID'),
				'rp_id'         => Yii::t('app', '自动回复ID'),
				'scene_id'      => Yii::t('app', '参数二维码ID'),
				'type'          => Yii::t('app', '回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）'),
				'content'       => Yii::t('app', '对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID'),
				'material_id'   => Yii::t('app', '素材库ID'),
				'attachment_id' => Yii::t('app', '附件ID'),
				'title'         => Yii::t('app', '图文消息的标题'),
				'digest'        => Yii::t('app', '图文消息的摘要'),
				'author'        => Yii::t('app', '图文消息的作者'),
				'show_cover'    => Yii::t('app', '是否显示封面，0为不显示，1为显示'),
				'cover_url'     => Yii::t('app', '封面图片的URL'),
				'content_url'   => Yii::t('app', '正文的URL'),
				'source_url'    => Yii::t('app', '原文的URL，若置空则无查看原文入口'),
				'status'        => Yii::t('app', '是否开启，0代表未开启，1代表开启'),
				'create_time'   => Yii::t('app', '创建时间'),
				'is_sync'       => Yii::t('app', '是否同步文件柜'),
				'is_use'        => Yii::t('app', '是否是自定义图文'),
				'attach_id'     => Yii::t('app', '同步文件柜的id'),
                'appid'         => Yii::t('app', '小程序的appid'),
                'pagepath'      => Yii::t('app', '小程序的页面路径'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getKw ()
		{
			return $this->hasOne(Keyword::className(), ['id' => 'kw_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMaterial ()
		{
			return $this->hasOne(Material::className(), ['id' => 'material_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getRp ()
		{
			return $this->hasOne(AutoReply::className(), ['id' => 'rp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getScene ()
		{
			return $this->hasOne(Scene::className(), ['id' => 'scene_id']);
		}
	}
