<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_moment_setting}}".
	 *
	 * @property int           $id
	 * @property int           $corp_id        企业ID
	 * @property int           $status         状态：0、关闭；1：开启
	 * @property int           $banner_type    朋友圈背景图样式：1、统一；2、可以自定义
	 * @property string        $banner_info    背景图设置，最多5个
	 * @property int           $can_goods      是否可以点赞：0、关闭；1、开启
	 * @property int           $can_reply      是否可以评论：0、关闭；1、开启
	 * @property string        $external_name  属性名称： 需要先确保在管理端有创建该属性，否则会忽略
	 * @property string        $external_title 网页的展示标题
	 * @property string        $create_time    创建时间
	 * @property string        $heard_img      默认头像
	 * @property int           $is_heard       0不允许修改1允许
	 * @property string        $description    个性签名
	 * @property int           $is_description 是否个性签名
	 * @property int           $agent_id       应用id
	 * @property int           $is_context     员工是否允许发表内容0不允许1允许
	 * @property int           $is_audit       员工发表内容是否审核0不允许1允许
	 * @property int           $is_synchro     是否同步官方朋友圈 0否 1是
	 * @property int           $is_synchro_all 是否同步之前全部朋友圈数据 0否 1是
	 *
	 * @property WorkCorpAgent $agent
	 * @property WorkCorp      $corp
	 */
	class WorkMomentSetting extends \yii\db\ActiveRecord
	{
		//是否允许发表内容
		const IS_CONTEXT_FALSE = 0;
		const IS_CONTEXT_TRUE = 1;
		//是发表内容是否审核
		const IS_AUDIT_FALSE = 0;
		const IS_AUDIT_TRUE = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moment_setting}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'status', 'banner_type', 'can_goods', 'can_reply', 'is_heard', 'is_description', 'agent_id', 'is_context', 'is_audit', 'is_synchro', 'is_synchro_all'], 'integer'],
				[['banner_info'], 'string'],
				[['create_time'], 'safe'],
				[['external_name', 'external_title'], 'string', 'max' => 16],
				[['heard_img', 'description'], 'string', 'max' => 255],
				[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'corp_id'        => Yii::t('app', '企业ID'),
				'status'         => Yii::t('app', '状态：0、关闭；1：开启'),
				'banner_type'    => Yii::t('app', '朋友圈背景图样式：1、统一；2、可以自定义'),
				'banner_info'    => Yii::t('app', '背景图设置，最多5个'),
				'can_goods'      => Yii::t('app', '是否可以点赞：0、关闭；1、开启'),
				'can_reply'      => Yii::t('app', '是否可以评论：0、关闭；1、开启'),
				'external_name'  => Yii::t('app', '属性名称： 需要先确保在管理端有创建该属性，否则会忽略'),
				'external_title' => Yii::t('app', '网页的展示标题'),
				'create_time'    => Yii::t('app', '创建时间'),
				'heard_img'      => Yii::t('app', '默认头像'),
				'is_heard'       => Yii::t('app', '0不允许修改1允许'),
				'description'    => Yii::t('app', '个性签名'),
				'is_description' => Yii::t('app', '是否个性签名'),
				'agent_id'       => Yii::t('app', '应用id'),
				'is_context'     => Yii::t('app', '员工是否允许发表内容0不允许1允许'),
				'is_audit'       => Yii::t('app', '员工发表内容是否审核0不允许1允许'),
				'is_synchro'     => Yii::t('app', '是否同步官方朋友圈 0否 1是'),
				'is_synchro_all' => Yii::t('app', '是否同步之前全部朋友圈数据 0否 1是'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}
	}