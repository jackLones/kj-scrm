<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%fans_behavior}}".
	 *
	 * @property int         $id
	 * @property int         $author_id   公众号ID
	 * @property int         $fans_id     粉丝ID
	 * @property int         $fans_event  粉丝行为，1：关注（subscribe）、2：取消关注（unsubscribe）
	 * @property string      $year        年
	 * @property string      $month       月
	 * @property string      $day         日
	 * @property int         $hour        时
	 * @property int         $minute      分
	 * @property int         $second      秒
	 * @property int         $time        发生时间
	 * @property string      $create_time 创建时间
	 * @property int         $scene_id    参数二维码id
	 *
	 * @property WxAuthorize $author
	 * @property Fans        $fans
	 */
	class FansBehavior extends \yii\db\ActiveRecord
	{
		const FANS_SUBSCRIBE = 1;
		const FANS_UNSUBSCRIBE = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans_behavior}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id'], 'required'],
				[['author_id', 'fans_id', 'fans_event', 'hour', 'minute', 'second', 'time'], 'integer'],
				[['create_time'], 'safe'],
				[['year'], 'string', 'max' => 4],
				[['month', 'day'], 'string', 'max' => 2],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
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
				'fans_id'     => Yii::t('app', '粉丝ID'),
				'fans_event'  => Yii::t('app', '粉丝行为，1：关注（subscribe）、2：取消关注（unsubscribe）'),
				'year'        => Yii::t('app', '年'),
				'month'       => Yii::t('app', '月'),
				'day'         => Yii::t('app', '日'),
				'hour'        => Yii::t('app', '时'),
				'minute'      => Yii::t('app', '分'),
				'second'      => Yii::t('app', '秒'),
				'time'        => Yii::t('app', '发生时间'),
				'create_time' => Yii::t('app', '创建时间'),
				'scene_id'    => Yii::t('app', '参数二维码id'),
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
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}

		/**
		 * @param integer $fansId
		 * @param integer $time
		 * @param int     $event
		 * @param integer $sceneId
		 *
		 * @return bool
		 * @throws InvalidDataException
		 */
		public static function create ($fansId, $time, $event = self::FANS_SUBSCRIBE, $sceneId = 0)
		{
			$fansBehavior = static::findOne(['fans_id' => $fansId, 'time' => $time]);

			if (empty($fansBehavior)) {
				$fansBehavior              = new FansBehavior();
				$fansBehavior->create_time = DateUtil::getCurrentTime();
			}

			$fansInfo = Fans::findOne($fansId);

			$fansBehavior->author_id  = $fansInfo->author_id;
			$fansBehavior->fans_id    = $fansId;
			$fansBehavior->fans_event = $event;
			$fansBehavior->year       = DateUtil::getYear($time);
			$fansBehavior->month      = DateUtil::getMonth($time);
			$fansBehavior->day        = DateUtil::getDay($time);
			$fansBehavior->hour       = DateUtil::getHour($time);
			$fansBehavior->minute     = DateUtil::getMinute($time);
			$fansBehavior->second     = DateUtil::getSecond($time);
			$fansBehavior->time       = $time;
			$fansBehavior->scene_id   = $sceneId;

			if ($fansBehavior->dirtyAttributes) {
				if (!$fansBehavior->validate() || !$fansBehavior->save()) {
					throw new InvalidDataException(SUtils::modelError($fansBehavior));
				}
			}

			return true;
		}
	}
