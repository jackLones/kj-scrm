<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_tag}}".
	 *
	 * @property int               $id
	 * @property int               $corp_id             授权的企业ID
	 * @property int               $follow_user_id      外部联系人对应的ID
	 * @property int               $tag_id              授权的企业的标签ID
	 * @property int               $status              0不显示1显示
	 * @property int               $add_time            添加时间
	 * @property int               $update_time         修改时间
	 * @property string            $is_sync             是否已同步：0否、1是
	 *
	 * @property WorkCorp          $corp
	 * @property PublicSeaCustomer $sea
	 */
	class PublicSeaTag extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_tag}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id','tag_id', 'status', 'add_time', 'update_time'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['follow_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => PublicSeaContactFollowUser::className(), 'targetAttribute' => ['follow_user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'corp_id'        => Yii::t('app', '授权的企业ID'),
				'follow_user_id' => Yii::t('app', '外部联系人对应的ID'),
				'tag_id'         => Yii::t('app', '授权的企业的标签ID'),
				'status'         => Yii::t('app', '0不显示1显示'),
				'add_time'       => Yii::t('app', '添加时间'),
				'update_time'    => Yii::t('app', '修改时间'),
				'is_sync'        => Yii::t('app', '是否已同步：0否、1是'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFollowUser ()
		{
			return $this->hasOne(PublicSeaContactFollowUser::className(), ['id' => 'follow_user_id']);
		}

		//打标签
		public static function addUserTag (array $user_ids, array $tag_ids, $otherData = [])
		{
			$fail    = 0;
			$t_count = count($tag_ids);
			//标签信息
			$tag_data = WorkTag::find()->where(['in', 'id', $tag_ids])->asArray()->all();
			$tagD     = [];
			foreach ($tag_data as $k => $v) {
				$tagD[$v['id']] = $v;
			}
			$time      = time();
			$work_corp = WorkTag::findOne($tag_ids[0]);
			foreach ($user_ids as $user_id) {
				$remark      = '';
				foreach ($tag_ids as $tag_id) {
					if ($t_count > 9999) {
						throw new InvalidDataException('客户标签数量不能超过9999个！');
					}
					$tagFollow = PublicSeaTag::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id, 'status' => 1]);
					if (empty($tagFollow)) {
						$tagFollow = PublicSeaTag::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
						if (empty($tagFollow)) {
							$tagFollow           = new PublicSeaTag();
							$tagFollow->add_time = $time;
						}
						$tagFollow->corp_id        = $work_corp->corp_id;
						$tagFollow->follow_user_id = $user_id;
						$tagFollow->tag_id         = $tag_id;
						$tagFollow->status         = 1;
						$tagFollow->update_time    = $time;
						if (!$tagFollow->validate() || !$tagFollow->save()) {
							$fail++;
							continue;
						}
						$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
						$remark .= '【' . $work_tag['tagname'] . '】、';
					}
				}
				if (!empty($remark)) {
					$followUser = PublicSeaContactFollowUser::findOne($user_id);
					if (!empty($followUser)) {
						$relatedId = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
						PublicSeaTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $followUser->user_id, 'sea_id' => $followUser->sea_id, 'event' => 'add_tag', 'related_id' => $relatedId, 'remark' => rtrim($remark, '、')]);
					}
				}
			}

			return $fail;
		}

		//移出标签
		public static function removeUserTag (array $user_ids, array $tag_ids, $otherData = [])
		{
			$fail = 0;
			//标签信息
			$tag_data = WorkTag::find()->where(['in', 'id', $tag_ids])->asArray()->all();
			$tagD     = [];
			foreach ($tag_data as $k => $v) {
				$tagD[$v['id']] = $v;
			}
			$time      = time();
			foreach ($user_ids as $user_id) {
				$remark = '';
				foreach ($tag_ids as $tag_id) {
					$tagFollow = PublicSeaTag::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
					$work_tag  = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
					if (!empty($work_tag['tagid'])) {
						if (!empty($tagFollow) && $tagFollow->status == 1) {
							$remark .= '【' . $work_tag['tagname'] . '】、';
						}
					}
					if (!empty($tagFollow)) {
						$tagFollow->status = 0;
						$tagFollow->update_time = $time;
						$tagFollow->save();
					}
				}
				if (!empty($remark)) {
					$followUser = PublicSeaContactFollowUser::findOne($user_id);
					if (!empty($followUser)) {
						$relatedId = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
						PublicSeaTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $followUser->user_id, 'sea_id' => $followUser->sea_id, 'event' => 'del_tag', 'related_id' => $relatedId, 'remark' => rtrim($remark, '、')]);
					}
				}
			}
			return $fail;
		}
	}
