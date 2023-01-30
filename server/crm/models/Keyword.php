<?php

	namespace app\models;

	use app\util\MsgUtil;
    use Yii;

	/**
	 * This is the model class for table "{{%keyword}}".
	 *
	 * @property int         $id
	 * @property int         $author_id   公众号ID
	 * @property string      $rule_name   规则名称
	 * @property int         $reply_mode  回复模式，1：reply_all代表全部回复，2：random_one代表随机回复其中一条
	 * @property string      $keyword     关键词
	 * @property int         $match_mode  匹配模式，1：contain代表消息中含有该关键词即可，2：equal表示消息内容必须和关键词严格相同
	 * @property int         $status      是否开启，0代表未开启，1代表开启
	 * @property string      $create_time 创建时间
     * @property string $equal_keyword 全匹配关键词
     * @property string $contain_keyword 半匹配关键词
     * @property int $is_del 是否删除 0否 1是
	 *
	 * @property WxAuthorize $author
	 * @property ReplyInfo[] $replyInfos
	 */
	class Keyword extends \yii\db\ActiveRecord
	{
		const REPLAY_ALL = 1;
		const RANDOM_ONE = 2;

		const MATCH_CONTAIN = 1;
		const MATCH_EQUAL = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%keyword}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
            return [
                [['author_id', 'reply_mode', 'match_mode', 'status', 'is_del'], 'integer'],
                [['create_time'], 'safe'],
                [['rule_name'], 'string', 'max' => 64],
                [['keyword'], 'string', 'max' => 32],
                [['equal_keyword', 'contain_keyword'], 'string', 'max' => 255],
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
				'rule_name'   => Yii::t('app', '规则名称'),
				'reply_mode'  => Yii::t('app', '回复模式，1：reply_all代表全部回复，2：random_one代表随机回复其中一条'),
				'keyword'     => Yii::t('app', '关键词'),
				'match_mode'  => Yii::t('app', '匹配模式，1：contain代表消息中含有该关键词即可，2：equal表示消息内容必须和关键词严格相同'),
				'status'      => Yii::t('app', '是否开启，0代表未开启，1代表开启'),
				'create_time' => Yii::t('app', '创建时间'),
                'equal_keyword' => Yii::t('app', '全匹配关键词'),
                'contain_keyword' => Yii::t('app', '半匹配关键词'),
                'is_del' => Yii::t('app', '是否删除 0否 1是')
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
			return $this->hasMany(ReplyInfo::className(), ['kw_id' => 'id']);
		}
	}
