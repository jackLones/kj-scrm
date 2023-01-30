<?php

namespace app\models;

use app\util\WorkUtils;
use dovechen\yii2\weWork\src\dataStructure\EContactGetTransferResult;
use Yii;
use app\util\SUtils;

/**
 * This is the model class for table "{{%work_dismiss_user_detail}}".
 *
 * @property int $id
 * @property int $corp_id 企业微信ID
 * @property int $user_id 企业成员ID
 * @property int $external_userid 外部联系人ID
 * @property int $chat_id 群ID
 * @property int $status 0待分配1已分配2客户拒绝3接替成员客户达到上限4分配中5未知
 * @property int $allocate_user_id 已分配成员
 * @property int $allocate_time 分配时间
 * @property int $create_time 创建时间
 *
 * @property WorkChat $chat
 * @property WorkCorp $corp
 * @property WorkExternalContact $externalUser
 * @property WorkUser $user
 */
class WorkDismissUserDetail extends \yii\db\ActiveRecord
{
	const IS_ASSIGN = 1; //已分配
	const NO_ASSIGN = 0; //未分配

	/**
	 * {@inheritdoc}
	 */
	public static function tableName ()
	{
		return '{{%work_dismiss_user_detail}}';
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'external_userid', 'chat_id', 'status', 'allocate_user_id', 'allocate_time', 'create_time'], 'integer'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'               => Yii::t('app', 'ID'),
			'corp_id'          => Yii::t('app', '企业微信ID'),
			'user_id'          => Yii::t('app', '企业成员ID'),
			'external_userid'  => Yii::t('app', '外部联系人ID'),
			'chat_id'          => Yii::t('app', '群ID'),
			'status'           => Yii::t('app', '0待分配1已分配2客户拒绝3接替成员客户达到上限4分配中5未知'),
			'allocate_user_id' => Yii::t('app', '已分配成员'),
			'allocate_time'    => Yii::t('app', '分配时间'),
			'create_time'      => Yii::t('app', '创建时间'),
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExternalUser()
    {
        return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

	/**
	 * @param $data
	 * @param $type
	 *
	 * @return bool
	 *
	 */
	public static function add ($data, $type)
	{
		if ($type == 1) {
			$userDetail = WorkDismissUserDetail::findOne(['corp_id' => $data['corp_id'], 'user_id' => $data['user_id'], 'external_userid' => $data['external_userid']]);
		} else {
			$userDetail = WorkDismissUserDetail::findOne(['corp_id' => $data['corp_id'], 'user_id' => $data['user_id'], 'chat_id' => $data['chat_id']]);
		}
		if (empty($userDetail)) {
			$userDetail = new WorkDismissUserDetail();
		}
		$userDetail->corp_id         = $data['corp_id'];
		$userDetail->user_id         = $data['user_id'];
		$userDetail->external_userid = isset($data['external_userid']) ? $data['external_userid'] : NULL;
		$userDetail->chat_id         = isset($data['chat_id']) ? $data['chat_id'] : NULL;
		$userDetail->allocate_time   = isset($data['allocate_time']) ? $data['allocate_time'] : 0;
		$userDetail->create_time     = time();
		if (!$userDetail->validate() || !$userDetail->save()) {
			\Yii::error(SUtils::modelError($userDetail), 'dismiss');
		}

		return true;
	}

	/**
	 * 拉取企业微信接替结果数据
	 *
	 * @param $corpId
	 *
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \app\components\InvalidDataException
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function syncData ($corpId)
	{
		$disUserDetail = WorkDismissUserDetail::find()->where(['status' => 0, 'corp_id' => $corpId])->andWhere(['!=','external_userid',''])->all();
		if (!empty($disUserDetail)) {
			/** @var WorkDismissUserDetail $detail */
			foreach ($disUserDetail as $detail) {
				$workApi    = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				$assignList = $workApi->ECGetUnAssignedList();
				$workUser   = WorkUser::findOne($detail->user_id);
				$contact    = WorkExternalContact::findOne($detail->external_userid);
				$flag       = 0;
				if (!empty($workUser) && !empty($contact)) {
					foreach ($assignList as $list) {
						if ($list['handover_userid'] == $workUser->userid && $list['external_userid'] == $contact->external_userid) {
							//还未分配
							$flag = 1;
						}
					}
					if (empty($flag)) {
						//分配中
						if (!empty($workUser->dimission_time)) {
							if (time() > ($workUser->dimission_time + 24 * 3600)) {
								$followUser = WorkExternalContactFollowUser::find()->where(['!=', 'user_id', $detail->user_id])->andWhere(['external_userid' => $detail->external_userid])->one();
								if (!empty($followUser)) {
									$detail->allocate_user_id = $followUser->user_id;
								}
								$detail->status = 5;
								$detail->save();
							} else {
								$detail->status = 4;
								$detail->save();
							}
						} else {
							$detail->status = 4;
							$detail->save();
						}
					}
				}

			}
		}
	}



}
