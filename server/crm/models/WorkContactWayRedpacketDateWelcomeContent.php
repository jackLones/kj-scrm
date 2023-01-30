<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_contact_way_redpacket_date_welcome_content}}".
	 *
	 * @property int                                $id
	 * @property int                                $date_id     红包活动渠道活码欢迎语日期表ID
	 * @property string                             $content     欢迎语内容
	 * @property string                             $welcome     欢迎语给前端用的
	 * @property string                             $start_time  开始时刻
	 * @property string                             $end_time    结束时刻
	 * @property int                                $create_time 创建时间
	 *
	 * @property WorkContactWayRedpacketDateWelcome $date
	 */
	class WorkContactWayRedpacketDateWelcomeContent extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_redpacket_date_welcome_content}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['date_id', 'create_time'], 'integer'],
				[['content', 'welcome'], 'string'],
				[['start_time', 'end_time'], 'string', 'max' => 32],
				[['date_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWayRedpacketDateWelcome::className(), 'targetAttribute' => ['date_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'date_id'     => Yii::t('app', '红包活动渠道活码欢迎语日期表ID'),
				'content'     => Yii::t('app', '欢迎语内容'),
				'welcome'     => Yii::t('app', '欢迎语给前端用的'),
				'start_time'  => Yii::t('app', '开始时刻'),
				'end_time'    => Yii::t('app', '结束时刻'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDate ()
		{
			return $this->hasOne(WorkContactWayRedpacketDateWelcome::className(), ['id' => 'date_id']);
		}

		/**
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->welcome = rawurlencode(rawurldecode($this->welcome));
			$this->content = rawurlencode(rawurldecode($this->content));

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->welcome)) {
				$this->welcome = rawurldecode($this->welcome);
			}
			if (!empty($this->content)) {
				$this->content = rawurldecode($this->content);
			}

			parent::afterFind(); // TODO: Change the autogenerated stub
		}

		/**
		 * @param $id
		 *
		 * @return array
		 *
		 */
		public static function getData ($id)
		{
			$timeDate   = [];
			$welContent = self::find()->where(['date_id' => $id])->all();
			if (!empty($welContent)) {
				/**@var $val WorkContactWayRedpacketDateWelcomeContent**/
				foreach ($welContent as $k => $val) {
					$timeDate[$k]['start_time']       = $val->start_time;
					$timeDate[$k]['end_time']         = $val->end_time;
					$content                          = !empty($val->welcome) ? Json::decode($val->welcome, true) : [];
					$contentNew                       = !empty($val->content) ? Json::decode($val->content, true) : [];
					$welcome_content['add_type']      = 0;
					$welcome_content['material_sync'] = isset($content['materialSync']) ? $content['materialSync'] : '';
					$welcome_content['groupId']       = isset($content['groupId']) ? $content['groupId'] : 0;
					$welcome_content['attachment_id'] = isset($content['attachment_id']) ? $content['attachment_id'] : 0;
					$welcome_content['text_content']  = '';
					$contentData                      = WorkWelcome::getContentData($contentNew);
					$welcome_content                  = WorkWelcome::getWelcomeData($content, $contentNew, $contentData);
					$welcome_content['attachment_id'] = !empty($welcome_content['attachment_id']) ? $welcome_content['attachment_id'] : 0;
					$timeDate[$k]['content']          = $welcome_content;
				}
			}

			return $timeDate;
		}

		/**
		 * @param $dateNow
		 * @param $dateId
		 *
		 * @return array
		 *
		 */
		public static function getContent ($dateNow, $dateId)
		{
			$content    = [];
			$welContent = WorkContactWayRedpacketDateWelcomeContent::find()->where(['date_id' => $dateId])->all();
			if (!empty($welContent)) {
				/**@var $con WorkContactWayRedpacketDateWelcomeContent**/
				foreach ($welContent as $k => $con) {
					$timeNow   = time();
					$timeStart = strtotime($dateNow . ' ' . $con->start_time);
					if ($con->end_time == '00:00') {
						$con->end_time = '23:59:59';
					}
					$timeEnd = strtotime($dateNow . ' ' . $con->end_time);
					if ($timeStart <= $timeNow && $timeNow <= $timeEnd) {
						$content = Json::decode($con->content, true);
					}
				}
			}

			return $content;
		}

		/**
		 * @param $data
		 * @param $id
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 */
		public static function add ($data, $id)
		{
			if (!empty($data)) {
				foreach ($data as $val) {
					$content = self::find()->where(['date_id' => $id, 'start_time' => $val['start_time'], 'end_time' => $val['end_time']])->one();
					\Yii::error($content, '$content');
					if (empty($content)) {
						$content              = new WorkContactWayRedpacketDateWelcomeContent();
						$content->create_time = time();
					}
					$content->date_id    = $id;
					$content->start_time = $val['start_time'];
					$content->end_time   = $val['end_time'];
					$content->welcome    = Json::encode($val['content']);
					$con                 = WorkWelcome::getContent($val['content']);
					$content->content    = Json::encode($con);
					if (!$content->validate() || !$content->save()) {
						\Yii::error(SUtils::modelError($content), 'message');
						throw new InvalidDataException($content . SUtils::modelError($content));
					}

				}
			}
		}
	}
