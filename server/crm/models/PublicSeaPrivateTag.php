<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_private_tag}}".
	 *
	 * @property int  $id
	 * @property int  $uid       账户ID
	 * @property int  $sub_id    子账户ID
	 * @property int  $corp_id    授权的企业ID
	 * @property int  $sea_id    公海池客户ID
	 * @property int  $tag_id    授权的企业的标签ID
	 * @property int  $status    0不显示1显示
	 * @property int  $add_time  添加时间
	 *
	 * @property User $u
	 */
	class PublicSeaPrivateTag extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_private_tag}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sub_id', 'sea_id', 'tag_id', 'status', 'add_time'], 'integer'],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'        => Yii::t('app', 'ID'),
				'uid'       => Yii::t('app', '账户ID'),
				'sub_id'    => Yii::t('app', '子账户ID'),
				'corp_id'    => Yii::t('app', '授权的企业ID'),
				'sea_id'    => Yii::t('app', '公海池客户ID'),
				'tag_id'    => Yii::t('app', '授权的企业的标签ID'),
				'status'    => Yii::t('app', '0不显示1显示'),
				'add_time'  => Yii::t('app', '添加时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		//添加数据
		public static function setData ($tagIds, $otherData)
		{
			$uid      = !empty($otherData['uid']) ? $otherData['uid'] : '';
			$subId    = !empty($otherData['sub_id']) ? $otherData['sub_id'] : '';
			$corpId    = !empty($otherData['corp_id']) ? $otherData['corp_id'] : '';
			$seaId    = !empty($otherData['sea_id']) ? $otherData['sea_id'] : '';
			$addTime  = time();
			foreach ($tagIds as $tagId) {
				$privateTag = PublicSeaPrivateTag::findOne(['uid' => $uid, 'sea_id' => $seaId, 'tag_id' => $tagId]);
				if (empty($privateTag)) {
					$privateTag            = new PublicSeaPrivateTag();
					$privateTag->uid       = $uid;
					$privateTag->sub_id    = $subId;
					$privateTag->corp_id   = $corpId;
					$privateTag->sea_id    = $seaId;
					$privateTag->tag_id    = $tagId;
					$privateTag->add_time  = $addTime;
					if (!$privateTag->validate() || !$privateTag->save()){
						continue;
					}
				}
			}

			return true;
		}

		//根据公海池客户id获取标签
		public static function getTagBySeaId ($corpId, $seaId)
		{
			$tagData = PublicSeaPrivateTag::find()->alias('pt');
			$tagData = $tagData->leftJoin('{{%work_tag}} wt', 'pt.tag_id=wt.id');
			$tagData = $tagData->where(['pt.corp_id' => $corpId, 'pt.sea_id' => $seaId, 'pt.status' => 1, 'wt.is_del' => 0]);
			$tagData = $tagData->select('wt.id,wt.tagname')->asArray()->all();

			$tagName = [];
			foreach ($tagData as $k => $tag) {
				$workTagD          = [];
				$workTagD['tid']   = (int) $tag['id'];
				$workTagD['tname'] = $tag['tagname'];
				$tagName[]         = $workTagD;
			}

			return $tagName;
		}
	}
