<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use app\util\WebsocketUtil;
use Yii;

/**
 * This is the model class for table "{{%work_msg_keyword_user}}".
 *
 * @property int $id
 * @property int $external_id 外部联系人ID
 * @property int $user_id 成员ID
 * @property string $external_userid 外部联系人的userid
 * @property string $userid 成员的userid
 * @property string $keyword 关键词
 * @property int $keyword_id 推荐规则ID
 * @property string $keyword_tag_id 推荐规则关联标签表ID
 * @property int $audit_info_id 会话内容ID
 * @property int $time 时间
 *
 * @property WorkMsgAuditInfo $auditInfo
 * @property WorkExternalContact $external
 * @property WorkMsgKeywordAttachment $keyword0
 * @property WorkUser $user
 */
class WorkMsgKeywordUser extends \yii\db\ActiveRecord
{
	const KEYWORD_TIME = '3600';//关键词展示1小时内的

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_msg_keyword_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_id', 'user_id', 'keyword_id', 'audit_info_id', 'time'], 'integer'],
            [['external_userid', 'userid'], 'string', 'max' => 64],
            [['keyword', 'keyword_tag_id'], 'string', 'max' => 500],
            [['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
            [['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
            [['keyword_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgKeywordAttachment::className(), 'targetAttribute' => ['keyword_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
	    return [
		    'id'              => Yii::t('app', 'ID'),
		    'external_id'     => Yii::t('app', '外部联系人ID'),
		    'user_id'         => Yii::t('app', '成员ID'),
		    'external_userid' => Yii::t('app', '外部联系人的userid'),
		    'userid'          => Yii::t('app', '成员的userid'),
		    'keyword'         => Yii::t('app', '关键词'),
		    'keyword_id'      => Yii::t('app', '推荐规则ID'),
		    'keyword_tag_id'  => Yii::t('app', '推荐规则关联标签表ID'),
		    'audit_info_id'   => Yii::t('app', '会话内容ID'),
		    'time'            => Yii::t('app', '时间'),
	    ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditInfo()
    {
        return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeyword0()
    {
        return $this->hasOne(WorkMsgKeywordAttachment::className(), ['id' => 'keyword_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

	/**
	 * 文本会话关键词匹配
	 *
	 * @param $auditInfo
	 * @param $content
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function creat ($auditInfo, $content)
	{
		$audit   = WorkMsgAudit::findOne($auditInfo->audit_id);
		$corp_id = $audit->corp_id;
		//设置的关键词
		$keyWordData  = WorkMsgKeywordAttachment::find()->where(['corp_id' => $corp_id, 'is_del' => 0])->all();
		$keywordD     = [];
		$keywordTypeD = [];
		foreach ($keyWordData as $v) {
			$keywordArr           = explode(',', $v->keywords);
			$keywordD[$v->id]     = $keywordArr;
			$keywordTypeD[$v->id] = $v->type;
		}

		//是否有适配的关键词
		$keywordNum = 0;
		$stime      = time() - static::KEYWORD_TIME;
		if (!empty($content) && !empty($keywordD)) {
			//用户标签
			$followUserTag = [];
			$followUser    = WorkExternalContactFollowUser::findOne(['external_userid' => $auditInfo->external_id, 'user_id' => $auditInfo->to_user_id]);
			if ($followUser) {
				$contactTag    = WorkTagFollowUser::find()->alias('w');
				$contactTag    = $contactTag->leftJoin('{{%work_tag}} t', '`t`.`id` = `w`.`tag_id`')->andWhere(['w.follow_user_id' => $followUser->id, 'w.status' => 1, 't.is_del' => 0, 'w.corp_id' => $corp_id])->select('`t`.`id`')->asArray()->all();
				$followUserTag = array_column($contactTag, 'id');
			}

			foreach ($keywordD as $keywordId => $keywordArr) {
				$keywordType = $keywordTypeD[$keywordId];
				//$keywordTagD = [0];//默认无标签限制
				$keywordTagD = 0;//默认无标签限制
				if ($keywordType == 2) {
					if (empty($followUserTag)) {
						continue;
					}
					//标签匹配
					$keywordTag  = WorkMsgKeywordTag::find()->where(['keyword_id' => $keywordId, 'is_del' => 0])->all();
					$keywordTagD = [];
					foreach ($keywordTag as $tag) {
						$tagArr = explode(',', $tag->tags);
						if (!empty(array_intersect($followUserTag, $tagArr))) {
							$keywordTagD[] = $tag->id;
						}
					}
					if (empty($keywordTagD)) {
						continue;
					}
					\Yii::error($keywordTagD, '$keywordTagD');
				}

				foreach ($keywordArr as $keyword) {
					if (strpos($content, $keyword) !== false) {
						/*foreach ($keywordTagD as $keywordTagId) {
							$isAdd       = 0;
							$keywordUser = static::find()->where(['external_id' => $auditInfo->external_id, 'user_id' => $auditInfo->to_user_id, 'keyword_id' => $keywordId, 'keyword_tag_id' => $keywordTagId])->andWhere(['>', 'time', $stime])->one();
							if (empty($keywordUser)) {
								$isAdd                        = 1;
								$keywordUser                  = new WorkMsgKeywordUser();
								$keywordUser->external_id     = $auditInfo->external_id;
								$keywordUser->user_id         = $auditInfo->to_user_id;
								$keywordUser->external_userid = $auditInfo->from;
								$keywordUser->userid          = $auditInfo->tolist;
							}
							$keywordUser->keyword        = $keyword;
							$keywordUser->keyword_id     = $keywordId;
							$keywordUser->keyword_tag_id = $keywordTagId;
							$keywordUser->audit_info_id  = $auditInfo->id;
							$keywordUser->time           = time();

							if ($keywordUser->save() && $isAdd == 1) {
								$keywordNum++;
							}
						}*/
						$keywordTagId = !empty($keywordTagD) ? implode(',', $keywordTagD) : 0;
						$isAdd        = 0;
						$keywordUser  = static::find()->where(['external_id' => $auditInfo->external_id, 'user_id' => $auditInfo->to_user_id, 'keyword_id' => $keywordId])->andWhere(['>', 'time', $stime])->one();
						if (empty($keywordUser)) {
							$isAdd                        = 1;
							$keywordUser                  = new WorkMsgKeywordUser();
							$keywordUser->external_id     = $auditInfo->external_id;
							$keywordUser->user_id         = $auditInfo->to_user_id;
							$keywordUser->external_userid = $auditInfo->from;
							$keywordUser->userid          = $auditInfo->tolist;
						}
						$keywordUser->keyword        = $keyword;
						$keywordUser->keyword_id     = $keywordId;
						$keywordUser->keyword_tag_id = (string)$keywordTagId;
						$keywordUser->audit_info_id  = $auditInfo->id;
						$keywordUser->time           = time();

						if ($keywordUser->save()) {
							if ($isAdd == 1){
								$keywordNum++;
							}
						}else{
							\Yii::error(SUtils::modelError($keywordUser), 'keywordUser-modelError');
						}

						break;//一个规则多个关键词只算一条
					}
				}
			}

			if ($keywordNum) {
				$workUser = WorkUser::findOne($auditInfo->to_user_id);

				$socket_type = SUtils::KEYWORD_WEBSOCKET_TYPE;
				\Yii::$app->websocket->send([
					'channel' => 'web-message',
					'to'      => $workUser->openid,
					'type'    => $socket_type,
					'info'    => [
						'userid'          => $auditInfo->tolist,
						'external_userid' => $auditInfo->from,
						'type'            => $socket_type,
						'has_keyword'     => true,
						'keyword_num'     => $keywordNum,
					]
				]);
			}
		}

		return true;
	}
}
