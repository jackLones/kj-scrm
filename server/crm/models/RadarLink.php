<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%radar_link}}".
	 *
	 * @property int                  $id
	 * @property int                  $associat_type        关联类型，0：内容引擎（附件表）、1：渠道活码、2：欢迎语、3：群欢迎语
	 * @property int                  $associat_id          关联id
	 * @property string               $associat_param       关联参数
	 * @property string               $title                标题
	 * @property int                  $dynamic_notification 是否启用动态通知，0：不启用、1：启用
	 * @property int                  $radar_tag_open       是否启用标签，0：不启用、1：启用
	 * @property string               $tag_ids              给客户打的标签
	 * @property int                  $open_times           打开次数
	 * @property string               $content              内容
	 * @property string               $created_at           创建时间
	 * @property string               $updated_at           更新时间
	 * @property int                  $status               状态，1：可用、0：不可用
	 *
	 * @property RadarLinkStatistic[] $radarLinkStatistics
	 */
	class RadarLink extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%radar_link}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['associat_type', 'associat_id', 'dynamic_notification', 'radar_tag_open', 'open_times', 'status'], 'integer'],
				[['tag_ids', 'content'], 'string'],
				[['created_at', 'updated_at'], 'required'],
				[['created_at', 'updated_at'], 'safe'],
				[['associat_param'], 'string', 'max' => 255],
				[['title'], 'string', 'max' => 64],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                   => Yii::t('app', 'ID'),
				'associat_type'        => Yii::t('app', '关联类型，0：内容引擎（附件表）、1：渠道活码、2：欢迎语、3：群欢迎语'),
				'associat_id'          => Yii::t('app', '关联id'),
				'associat_param'       => Yii::t('app', '关联参数'),
				'title'                => Yii::t('app', '标题'),
				'dynamic_notification' => Yii::t('app', '是否启用动态通知，0：不启用、1：启用'),
				'radar_tag_open'       => Yii::t('app', '是否启用标签，0：不启用、1：启用'),
				'tag_ids'              => Yii::t('app', '给客户打的标签'),
				'open_times'           => Yii::t('app', '打开次数'),
				'content'              => Yii::t('app', '内容'),
				'created_at'           => Yii::t('app', '创建时间'),
				'updated_at'           => Yii::t('app', '更新时间'),
				'status'               => Yii::t('app', '状态，1：可用、0：不可用'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getRadarLinkStatistics ()
		{
			return $this->hasMany(RadarLinkStatistic::className(), ['radar_link_id' => 'id']);
		}

		/**
		 * @param int    $associat_type        // 关联类型，0：内容引擎（附件表）、1：渠道活码、2：欢迎语、3：群欢迎语
		 * @param int    $associat_id          // 关联id
		 * @param int    $dynamic_notification // 是否启用动态通知，0：不启用、1：启用
		 * @param int    $radar_tag_open       // 是否启用标签，0：不启用、1：启用
		 * @param string $tag_ids              // 给客户打的标签
		 * @param int    $radar_open           // 雷达链接状态，0：不启用、1：启用
		 * @param string $title                // 标题
		 * @param null   $associat_param       // 关联参数
		 * @param null   $content
		 *
		 * @return bool
		 * @throws \app\components\InvalidDataException
		 */
		public static function addRadarLink ($associat_type, $associat_id, $dynamic_notification = 0, $radar_tag_open = 0, $tag_ids = '', $radar_open = 0, $title = '', $associat_param = NULL, $content = NULL)
		{
			$radar_link = static::findOne(['associat_id' => $associat_id, 'associat_type' => $associat_type, 'associat_param' => $associat_param]);
			if ($radar_link) {
				$radar_link->updated_at = DateUtil::getCurrentTime();
			} else {
				$radar_link                = new self();
				$radar_link->created_at    = DateUtil::getCurrentTime();
				$radar_link->updated_at    = DateUtil::getCurrentTime();
				$radar_link->associat_type = $associat_type;
				$radar_link->associat_id   = $associat_id;
			}

			$radar_link->associat_param = $associat_param;

			if ($content !== NULL && is_array($content)) {
				$radar_link->content = rawurlencode(json_encode($content, JSON_UNESCAPED_UNICODE));
			}

			$length = mb_strlen($title, 'utf-8');
			if ($length > 64) {
				$radar_link->title = mb_substr($title, 0, 64, 'utf-8');
			} else {
				$radar_link->title = $title;
			}
			$radar_link->dynamic_notification = $dynamic_notification;
			$radar_link->radar_tag_open       = $radar_tag_open;
			$radar_link->tag_ids              = (string) $tag_ids;
			$radar_link->status               = $radar_open;
			\Yii::error($radar_link, 'addRadarLink:radar_link');
			if (!$radar_link->validate() || !$radar_link->save()) {
				throw new InvalidDataException(SUtils::modelError($radar_link));
			}

			return true;
		}

		/**
		 * 历史内容引擎统一加上雷达(一次性执行）
		 * File: models/RadarLink.php
		 * Class: RadarLink
		 * Function: allAttachmentAddRadar
		 *
		 * Author: BeenLee
		 * Time: 2021/3/30 9:13 上午
		 *
		 */
		public static function allAttachmentAddRadar ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$allAttachment = Attachment::find()->where(['status' => 1])->andWhere(['in', 'file_type', [1, 3, 4, 5]])->all();
			if ($allAttachment !== NULL) {
				foreach ($allAttachment as $attachment) {
					$radarInfo = self::findOne(['associat_type' => 0, 'associat_id' => $attachment->id]);
					if ($radarInfo === NULL) {
						try {
							self::addRadarLink(0, $attachment->id, 1, 0, '', 1, $attachment->file_name);
						} catch (InvalidDataException $e) {
							\Yii::error($e->getMessage(), 'allAttachmentAddRadar:addRadarLink');
						}
					}
				}
			}
		}
	}
