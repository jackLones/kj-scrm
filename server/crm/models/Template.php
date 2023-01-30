<?php

	namespace app\models;

	use Yii;
	use callmez\wechat\sdk\Wechat;
	use app\util\DateUtil;
	use app\models\WxAuthorize;

	/**
	 * This is the model class for table "{{%template}}".
	 *
	 * @property int               $id
	 * @property int               $author_id         公众号ID
	 * @property string            $template_id_short 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
	 * @property string            $template_id       模板ID
	 * @property string            $title             模板标题
	 * @property string            $primary_industry  模板所属行业的一级行业
	 * @property string            $deputy_industry   模板所属行业的二级行业
	 * @property string            $content           模板内容
	 * @property string            $example           模板示例
	 * @property string            $create_time       创建时间
	 *
	 * @property WxAuthorize       $author
	 * @property TemplatePushMsg[] $templatePushMsgs
	 */
	class Template extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%template}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id'], 'integer'],
				[['content', 'example'], 'string'],
				[['create_time'], 'safe'],
				[['template_id_short'], 'string', 'max' => 32],
				[['template_id', 'title', 'primary_industry', 'deputy_industry'], 'string', 'max' => 64],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                => Yii::t('app', 'ID'),
				'author_id'         => Yii::t('app', '公众号ID'),
				'template_id_short' => Yii::t('app', '模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式'),
				'template_id'       => Yii::t('app', '模板ID'),
				'title'             => Yii::t('app', '模板标题'),
				'primary_industry'  => Yii::t('app', '模板所属行业的一级行业'),
				'deputy_industry'   => Yii::t('app', '模板所属行业的二级行业'),
				'content'           => Yii::t('app', '模板内容'),
				'example'           => Yii::t('app', '模板示例'),
				'create_time'       => Yii::t('app', '创建时间'),
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
		public function getTemplatePushMsgs ()
		{
			return $this->hasMany(TemplatePushMsg::className(), ['template_id' => 'id']);
		}

		/**
		 * @param $appid
		 * @param $author_id
		 * 获取所有模板
		 *
		 * @return mixed
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getTemplate ($appid, $author_id)
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
			$result = $wechat->getTemplate();
			if ($result) {
				foreach ($result as $v) {
					$template = static::findOne(['author_id' => $author_id, 'template_id' => $v['template_id']]);
					if (empty($template)) {
						$tmp                   = new Template();
						$tmp->author_id        = $author_id;
						$tmp->template_id      = $v['template_id'];
						$tmp->title            = $v['title'];
						$tmp->primary_industry = $v['primary_industry'];
						$tmp->deputy_industry  = $v['deputy_industry'];
						$tmp->content          = $v['content'];
						$tmp->example          = $v['example'];
						$tmp->create_time      = DateUtil::getCurrentTime();
						$tmp->save();
					}
				}

				return true;
			} else {
				return false;
			}

		}

		//同步模板消息
		public static function sysncTemplate ($author_id)
		{
			$data = WxAuthorize::find()->where(['!=', 'authorizer_type', WxAuthorize::AUTH_TYPE_UNAUTH])->andWhere(['author_id' => $author_id])->one();
			Template::getTemplate($data->authorizer_appid, $author_id);

			return true;
		}

		//替换昵称
		public static function replaceTemplateData ($template_data, $nickname, $temp, $con)
		{
			try {
				foreach ($template_data as $key => $val) {
					$value = $val['value'];
					if (strpos($value, '{nickname}') !== false) {
						$value = str_replace("{nickname}", $nickname, $value);
					}
					$template_data[$key]['value'] = $value;
				}

				return $template_data;
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'message');
			}

		}

	}
