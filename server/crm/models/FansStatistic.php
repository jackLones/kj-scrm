<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%fans_statistic}}".
	 *
	 * @property int         $id
	 * @property int         $author_id    公众号ID
	 * @property int         $new          新增粉丝数
	 * @property int         $unsubscribe  取关粉丝数
	 * @property int         $net_increase 净增粉丝数
	 * @property int         $active       活跃粉丝数
	 * @property int         $total        总粉丝数
	 * @property string      $data_time    数据统计日期
	 * @property string      $create_time  创建日期
	 *
	 * @property WxAuthorize $author
	 */
	class FansStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'new', 'unsubscribe', 'net_increase', 'active', 'total'], 'integer'],
				[['data_time', 'create_time'], 'safe'],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'author_id'    => Yii::t('app', '公众号ID'),
				'new'          => Yii::t('app', '新增粉丝数'),
				'unsubscribe'  => Yii::t('app', '取关粉丝数'),
				'net_increase' => Yii::t('app', '净增粉丝数'),
				'active'       => Yii::t('app', '活跃粉丝数'),
				'total'        => Yii::t('app', '总粉丝数'),
				'data_time'    => Yii::t('app', '数据统计日期'),
				'create_time'  => Yii::t('app', '创建日期'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		public static function getFansNum ($author_id, $type, $date)
		{
			$new      = $cancel = $new_incre = $per = 0;
			$fansData = static::find()->andWhere(['author_id' => $author_id, 'type' => $type, 'data_time' => $date])->one();
			if (!empty($fansData)) {
				$new       = $fansData->new;
				$cancel    = $fansData->unsubscribe;
				$new_incre = $fansData->net_increase;
				$per       = $fansData->cancel_per;
			}
			$result['new']       = $new;
			$result['cancel']    = $cancel;
			$result['new_incre'] = $new_incre;
			$result['per']       = $per;

			return $result;
		}
	}
