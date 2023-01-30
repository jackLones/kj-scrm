<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\WorkTagFollowUserJob;
	use dovechen\yii2\weWork\Work;
	use Yii;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\Tag;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactTag;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactTagGroup;

	/**
	 * This is the model class for table "{{%work_tag}}".
	 *
	 * @property int                 $id
	 * @property int                 $corp_id   授权的企业ID
	 * @property int                 $tagid     标签id，非负整型，指定此参数时新增的标签会生成对应的标签id，不指定时则以目前最大的id自增
	 * @property string              $tagname   标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名
	 * @property int                 $is_del    0：未删除；1：已删除
	 * @property int                 $type      类型0 外部联系人 1 员工 2客户群 3内容标签
	 * @property int                 $group_id  分组id
	 *
	 * @property WorkCorp            $corp
	 * @property WorkTagDepartment[] $workTagDepartments
	 * @property WorkTagUser[]       $workTagUsers
	 */
	class WorkTag extends \yii\db\ActiveRecord
	{
		const UPDATE_TAG = 'update_tag';

		const NORMAL_TAG = 0;
		const DEL_TAG = 1;

		const CORP_TAG = 1;
		const PERSONAL_TAG = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'is_del', 'type', 'group_id'], 'integer'],
				[['tagname', 'tagid'], 'string', 'max' => 32],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'       => Yii::t('app', 'ID'),
				'corp_id'  => Yii::t('app', '授权的企业ID'),
				'tagid'    => Yii::t('app', '标签id，非负整型，指定此参数时新增的标签会生成对应的标签id，不指定时则以目前最大的id自增'),
				'tagname'  => Yii::t('app', '标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名'),
				'is_del'   => Yii::t('app', '0：未删除；1：已删除'),
				'type'     => Yii::t('app', '类型0 外部联系人 1 员工'),
				'group_id' => Yii::t('app', '分组id'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagDepartments ()
		{
			return $this->hasMany(WorkTagDepartment::className(), ['tag_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagUsers ()
		{
			return $this->hasMany(WorkTagUser::className(), ['tag_id' => 'id']);
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @param $tagId
		 * @param $relationInfo
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function changeTagRelation ($tagId, $relationInfo)
		{
			if (empty($relationInfo) || !is_array($relationInfo)) {
				throw new InvalidDataException('参数不正确！');
			}

			$workTag = static::findOne($tagId);

			if (empty($workTag)) {
				throw new InvalidDataException('参数不正确！');
			}

			if (!empty($relationInfo['adduseritems'])) {
				$relationInfo['adduseritems'] = explode(',', $relationInfo['adduseritems']);
				foreach ($relationInfo['adduseritems'] as $userId) {
					$userInfo = WorkUser::findOne(['corp_id' => $workTag->corp_id, 'userid' => $userId]);
					if (!empty($userInfo)) {
						WorkTagUser::setTagUser($tagId, $userInfo->id);
					}
				}
			}

			if (!empty($relationInfo['deluseritems'])) {
				$relationInfo['deluseritems'] = explode(',', $relationInfo['deluseritems']);
				foreach ($relationInfo['deluseritems'] as $userId) {
					$userInfo = WorkUser::findOne(['corp_id' => $workTag->corp_id, 'userid' => $userId]);
					if (!empty($userInfo)) {
						WorkTagUser::deleteAll(['tag_id' => $tagId, 'user_id' => $userInfo->id]);
					}
				}
			}

			if (!empty($relationInfo['addpartyitems'])) {
				$relationInfo['addpartyitems'] = explode(',', $relationInfo['addpartyitems']);
				foreach ($relationInfo['addpartyitems'] as $departmentId) {
					$departmentInfo = WorkDepartment::findOne(['corp_id' => $workTag->corp_id, 'department_id' => $departmentId]);
					if (!empty($departmentInfo)) {
						WorkTagDepartment::setTagDepartment($tagId, $departmentInfo->id);
					}
				}
			}

			if (!empty($relationInfo['delpartyitems'])) {
				$relationInfo['delpartyitems'] = explode(',', $relationInfo['delpartyitems']);
				foreach ($relationInfo['delpartyitems'] as $departmentId) {
					$departmentInfo = WorkDepartment::findOne(['corp_id' => $workTag->corp_id, 'department_id' => $departmentId]);
					if (!empty($departmentInfo)) {
						WorkTagDepartment::deleteAll(['tag_id' => $tagId, 'department_id' => $departmentInfo->id]);
					}
				}
			}

			return true;
		}

		/**
		 *  给员工/客户/客户群/内容引擎添加标签
		 *
		 * @param int   $s_type   1 通讯录 2 外部联系人 3 客户群 5内容引擎
		 * @param array $user_ids
		 * @param array $tag_ids
		 * @param array $isRecord 0 记录轨迹 1不记录
		 * @param array $TagSaveIds 返回保存的id值
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function addUserTag ($s_type, array $user_ids, array $tag_ids, $otherData = [], $isRecord = 0 , &$TagSaveIds = [])
		{
			$fail        = 0;
			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				$t_count = count($tag_ids);
				//static::verifyCount($user_ids, $s_type, $t_count);
				//标签信息
				$tag_data = WorkTag::find()->where(['in', 'id', $tag_ids])->andWhere(["is_del" => 0])->asArray()->all();
				$tagD     = [];
				foreach ($tag_data as $k => $v) {
					$tagD[$v['id']] = $v;
				}
				$work_corp = WorkTag::find()->andWhere(['in', 'id', $tag_ids])->orderBy('corp_id desc')->One();
				$workApi   = '';
				$time      = time();
				foreach ($user_ids as $user_id) {
					$remark     = '';
					$workAddTag = [];
					foreach ($tag_ids as $tag_id) {
						if (empty($tag_id)){
							continue;
						}
						if ($s_type == 1) {
							if ($t_count > 3000) {
								throw new InvalidDataException('成员标签数量不能超过3000个！');
							}
							$workApi  = WorkUtils::getWorkApi($work_corp->corp_id);
							$workUser = WorkTagUser::find()->where(['tag_id' => $tag_id, 'user_id' => $user_id])->one();
							if (empty($workUser)) {
								$work_tag_user          = new WorkTagUser();
								$work_tag_user->user_id = $user_id;
								$work_tag_user->tag_id  = $tag_id;
								if (!$work_tag_user->validate() || !$work_tag_user->save()) {
									$fail++;
									$TagSaveIds[] = $work_tag_user->id;
									continue;
								}
								$work_tag  = WorkTag::findOne($tag_id);
								$work_user = WorkUser::findOne($user_id);
								if (!empty($work_tag->tagid)) {
									$result = $workApi->tagAddTagUsers(intval($work_tag->tagid), [$work_user->userid]);
									//\Yii::error($result, '$result-0');
								}
							}
						} elseif ($s_type == 3){
							if ($t_count > 9999) {
								throw new InvalidDataException('群标签数量不能超过9999个！');
							}
							$tagChat = WorkTagChat::find()->where(['tag_id' => $tag_id, 'chat_id' => $user_id, 'status' => 1])->one();
							if (empty($tagChat)) {
								$tagChat = WorkTagChat::findOne(['tag_id' => $tag_id, 'chat_id' => $user_id]);
								if (empty($tagChat)) {
									$tagChat = new WorkTagChat();
								}
								$tagChat->corp_id  = $work_corp->corp_id;
								$tagChat->tag_id   = $tag_id;
								$tagChat->chat_id  = $user_id;
								$tagChat->add_time = date('Y-m-d H:i:s');
								$tagChat->status   = 1;
								if (!$tagChat->validate() || !$tagChat->save()) {
									$fail++;
									continue;
								}
								$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
								$remark   .= '【' . $work_tag['tagname'] . '】、';
							}
						} elseif ($s_type == 5){
							if ($t_count > 9999) {
								throw new InvalidDataException('标签数量不能超过9999个！');
							}
							$tagAttachment = WorkTagAttachment::find()->where(['tag_id' => $tag_id, 'attachment_id' => $user_id])->one();
							if (empty($tagAttachment)) {
								$tagAttachment = new WorkTagAttachment();
								$tagAttachment->corp_id        = $work_corp->corp_id;
								$tagAttachment->tag_id         = $tag_id;
								$tagAttachment->attachment_id = $user_id;
								$tagAttachment->add_time = date('Y-m-d H:i:s');
							}
							$tagAttachment->status         = 1;
							if (!$tagAttachment->validate() || !$tagAttachment->save()) {
								$fail++;
								continue;
							}
						} else {
							if ($t_count > 9999) {
								throw new InvalidDataException('客户标签数量不能超过9999个！');
							}
							$tagFollow = WorkTagFollowUser::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id, 'status' => 1]);
							if (empty($tagFollow)) {
								$tagFollow = WorkTagFollowUser::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
								if (empty($tagFollow)) {
									$tagFollow = new WorkTagFollowUser();
									$tagFollow->add_time = $time;
								}
								$tagFollow->follow_user_id = $user_id;
								$tagFollow->tag_id         = $tag_id;
								$tagFollow->corp_id        = $work_corp->corp_id;
								$tagFollow->status         = 1;
								$tagFollow->update_time    = $time;
								if (!$tagFollow->validate() || !$tagFollow->save()) {
									$fail++;
									$TagSaveIds[] = $tagFollow->id;
									continue;
								}
								$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
								if (!empty($work_tag['tagid'])) {
									array_push($workAddTag, $work_tag['tagid']);
								}

								if (isset($work_tag['tagname']) && !empty($work_tag['tagname'])) {
									$remark .= '【' . $work_tag['tagname'] . '】、';
								}

							}

//							$workContact = WorkTagContact::find()->andWhere(['tag_id' => $tag_id, 'contact_id' => $user_id])->one();
//							if (empty($workContact)) {
//								$work_tag_contact             = new WorkTagContact();
//								$work_tag_contact->contact_id = $user_id;
//								$work_tag_contact->tag_id     = $tag_id;
//								if (!$work_tag_contact->validate() || !$work_tag_contact->save()) {
//									$fail++;
//								}
//								//$work_tag = WorkTag::findOne($tag_id);
//								$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
//								if (!empty($work_tag['tagid'])) {
//									array_push($workAddTag, $work_tag['tagid']);
//								}
//
//								$remark   .= '【' . $work_tag['tagname'] . '】、';
//							}

						}

					}

					if ($s_type == 2 && !empty($workAddTag)) {
						try {
							$workApi = WorkUtils::getWorkApi($work_corp->corp_id, WorkUtils::EXTERNAL_API);
						} catch (\Exception $e) {
							\Yii::error($e->getMessage(), 'addUserTag-getMessage');
						}

						if ($workApi instanceof Work) {
							$exMarkData = WorkExternalContactFollowUser::find()->alias('fu')
								->select('`fu`.`userid` as userId, `ec`.`external_userid` as externalUserId')
								->leftJoin('{{%work_external_contact}} as ec', '`ec`.`id` = `fu`.`external_userid`')
								->where(['fu.id' => $user_id])->asArray()->one();
							\Yii::error($exMarkData,'exMarkData');
							if (!empty($exMarkData)) {
								if ($workApi && !empty($exMarkData['userId']) && !empty($exMarkData['externalUserId'])) {
									try {
										\Yii::error($workAddTag, '$workAddTag');
										$res = $workApi->ECMarkTag($exMarkData['userId'], $exMarkData['externalUserId'], $workAddTag);
										\Yii::error($res, '$res');
									} catch (\Exception $e) {
										Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":ECMarkTag");
									}
								}
							}

//							$exMarkData = WorkExternalContactFollowUser::find()->alias('fu')
//								->select('`wu`.`userid` as userId, `ec`.`external_userid` as externalUserId')
//								->leftJoin('{{%work_user}} as wu', '`wu`.`id` = `fu`.`user_id`')
//								->leftJoin('{{%work_external_contact}} as ec', '`ec`.`id` = `fu`.`external_userid`')
//								->where(['fu.external_userid' => $user_id])
//								->andWhere(['wu.status' => 1])->asArray()->all();
//
//							if (!empty($exMarkData)) {
//								foreach ($exMarkData as $ecMarkInfo) {
//									if ($workApi && !empty($ecMarkInfo['userId']) && !empty($ecMarkInfo['externalUserId'])) {
//										try {
//											$workApi->ECMarkTag($ecMarkInfo['userId'], $ecMarkInfo['externalUserId'], $workAddTag);
//										} catch (\Exception $e) {
//											Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":ECMarkTag");
//										}
//									}
//								}
//							}

						} else {
							\Yii::error('未取到值', 'workApi');
						}
					}

					//记录客户轨迹
					if ($s_type == 2 && !empty($remark) && empty($isRecord)) {
						$event  = 'add_tag';
						$remark = rtrim($remark, '、');
						if (isset($otherData['type'])) {
							switch ($otherData['type']) {
								case 'fission'://营销引流
									$event = 'fission_add_tag';
									if (!empty($otherData['msg'])) {
										$remark = '客户通过扫' . $otherData['msg'] . '自动打上标签' . $remark;
									}
									break;
								case 'activity'://任务宝
									$event = 'activity_add_tag';
									if (!empty($otherData['msg'])) {
										$remark = '客户通过扫' . $otherData['msg'] . '自动打上标签' . $remark;
									}
									break;
								case 'chat_tag'://聊天打标签
									$event = 'chat_tag';
									if (!empty($otherData['msg'])) {
										$remark = $otherData['msg'] . $remark;
									}
									break;
								case WorkContactWayRedpacket::REDPACKET_WAY://红包拉新
									$event = 'way_redpacket_tag';
									if (!empty($otherData['msg'])) {
										$remark = '客户通过扫' . $otherData['msg'] . '自动打上标签' . $remark;
									}
									break;
								case 'auto_rule_tag':
									$event  = 'auto_rule_tag';
									$remark = '符合自动化打标签规则条件，给客户自动打上标签' . $remark;
									break;
								case 'radar_tag':
									// todo beenlee 打标签
									$event = 'radar_tag';
									if (!empty($otherData['msg'])) {
										$remark = '客户打开带有雷达标识的 '.$otherData['msg']. '自动打上客户标签' . $remark;
									}
									break;
							}
						}
						$followUser = WorkExternalContactFollowUser::findOne($user_id);
						if (!empty($followUser)) {
							$relatedId = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
							ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $followUser->user_id, 'external_id' => $followUser->external_userid, 'event' => $event, 'related_id' => $relatedId, 'remark' => rtrim($remark, '、')]);
						}

					}

					//记录客户群轨迹
					if ($s_type == 3 && !empty($remark) && empty($isRecord)) {
						$userCorp          = UserCorpRelation::findOne(['corp_id' => $work_corp->corp_id]);
						$addData           = ['uid' => $userCorp->uid, 'event' => 'chat_track', 'event_id' => 4, 'related_id' => $user_id];
						$addRemark         = '系统打标签';
						$remark            = rtrim($remark, '、');
						$addData['remark'] = $addRemark . $remark;
						ExternalTimeLine::addExternalTimeLine($addData);
					}
				}
				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				$message = $e->getMessage();
				if (strpos($message, '84061') !== false) {
					$message = '不存在外部联系人的关系';
				}
				if (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				}
				if (strpos($message, '81011') !== false) {
					$message = '无权限操作标签';
				}
				if (strpos($message, '48002') !== false) {
					$message = 'API接口无权限调用';
				}
				if (strpos($message, '40068') !== false) {
					$message = '不合法的标签ID';
				}
				throw new InvalidDataException($message);
			}

			return $fail;
		}

		/**
		 *  给群客户添加标签
		 *
		 * @param int   $chat_id
		 * @param array $external_ids
		 * @param array $tag_ids
		 * @param array $otherData
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function addChatUserTag ($chat_id, array $external_ids, array $tag_ids, $otherData = [])
		{
			$fail        = 0;
			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				$t_count = count($tag_ids);
				if ($t_count > 9999) {
					throw new InvalidDataException('客户标签数量不能超过9999个！');
				}
				//标签信息
				$tag_data = WorkTag::find()->where(['in', 'id', $tag_ids])->andWhere(["is_del" => 0])->asArray()->all();
				$tagD     = [];
				foreach ($tag_data as $k => $v) {
					$tagD[$v['id']] = $v;
				}
				$work_corp = WorkTag::findOne($tag_ids[0]);
				$workApi   = '';
				try {
					$workApi = WorkUtils::getWorkApi($work_corp->corp_id, WorkUtils::EXTERNAL_API);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'addUserTag-getMessage');
				}
				foreach ($external_ids as $external_id) {
					$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $external_id, 'del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
					$followUser = $followUser->select('`id`, `external_userid`, `user_id`')->asArray()->all();
					if (empty($followUser)) {
						$fail++;
						continue;
					}
					$failStatus = 0;
					foreach ($followUser as $val) {
						$remark     = '';
						$workAddTag = [];
						$user_id    = $val['id'];
						foreach ($tag_ids as $tag_id) {
							$tagFollow = WorkTagFollowUser::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id, 'status' => 1]);
							if (empty($tagFollow)) {
								$tagFollow = WorkTagFollowUser::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
								if (empty($tagFollow)) {
									$tagFollow = new WorkTagFollowUser();
								}
								$tagFollow->follow_user_id = $user_id;
								$tagFollow->tag_id         = $tag_id;
								$tagFollow->corp_id        = $work_corp->corp_id;
								$tagFollow->status         = 1;
								if (!$tagFollow->validate() || !$tagFollow->save()) {
									$failStatus = 1;
									continue;
								}
								$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
								if (!empty($work_tag['tagid'])) {
									array_push($workAddTag, $work_tag['tagid']);
								}
								$remark .= '【' . $work_tag['tagname'] . '】、';
							}
						}

						if (!empty($workAddTag)) {
							if ($workApi instanceof Work) {
								$exMarkData = WorkExternalContactFollowUser::find()->alias('fu')
									->select('`fu`.`userid` as userId, `ec`.`external_userid` as externalUserId')
									->leftJoin('{{%work_external_contact}} as ec', '`ec`.`id` = `fu`.`external_userid`')
									->where(['fu.id' => $user_id])->asArray()->one();

								if (!empty($exMarkData)) {
									if ($workApi && !empty($exMarkData['userId']) && !empty($exMarkData['externalUserId'])) {
										try {
											\Yii::error($workAddTag, '$workAddTag');
											$res = $workApi->ECMarkTag($exMarkData['userId'], $exMarkData['externalUserId'], $workAddTag);
											\Yii::error($res, '$res');
										} catch (\Exception $e) {
											Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":ECMarkTag");
										}
									}
								}

							}
						}

						//记录客户轨迹
						if (!empty($remark)) {
							$relatedId = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
							ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $val['user_id'], 'external_id' => $val['external_userid'], 'event' => 'chat_add_tag', 'event_id' => $chat_id, 'related_id' => $relatedId, 'remark' => rtrim($remark, '、')]);
						}
					}

					if ($failStatus == 1) {
						$fail++;
					}
				}
				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				$message = $e->getMessage();
				if (strpos($message, '84061') !== false) {
					$message = '不存在外部联系人的关系';
				}
				if (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				}
				if (strpos($message, '81011') !== false) {
					$message = '无权限操作标签';
				}
				if (strpos($message, '48002') !== false) {
					$message = 'API接口无权限调用';
				}
				if (strpos($message, '40068') !== false) {
					$message = '不合法的标签ID';
				}
				throw new InvalidDataException($message);
			}

			return $fail;
		}

		/**
		 * @param $user_ids
		 * @param $s_type
		 * @param $t_count
		 *
		 *
		 * @throws InvalidDataException
		 */
		private static function verifyCount ($user_ids, $s_type, $t_count)
		{
			if (!empty($user_ids)) {
				foreach ($user_ids as $user_id) {
					if ($s_type == 1) {
						$count = WorkTagUser::find()->andWhere(['user_id' => $user_id])->count();
						if ($t_count + $count > 9999) {
							throw new InvalidDataException('成员标签数量不能超过9999个！');
						}
					} else {
						$count = WorkTagContact::find()->andWhere(['contact_id' => $user_id])->count();
						if ($t_count + $count > 9999) {
							throw new InvalidDataException('客户标签数量不能超过9999个！');
						}
					}
				}
			}
		}

		/**
		 * 移除成员/客户/客户群/内容引擎标签
		 *
		 * @param int   $s_type 1 通讯录 2 外部联系人 3 客户群 5内容引擎
		 * @param array $user_ids
		 * @param array $tag_ids
		 *
		 * @return boolean
		 *
		 */
		public static function removeUserTag ($s_type, array $user_ids, array $tag_ids, $otherData = [])
		{
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$fail = 0;
				//标签信息
				$tag_data = WorkTag::find()->where(['in', 'id', $tag_ids])->asArray()->all();
				$tagD     = [];
				foreach ($tag_data as $k => $v) {
					$tagD[$v['id']] = $v;
				}

				$work_corp     = WorkTag::findOne($tag_ids[0]);
				$workApi       = '';
				$time          = time();
				$workRemoveTag = [];
				foreach ($user_ids as $user_id) {
					$remark = '';
					foreach ($tag_ids as $tag_id) {
						if ($s_type == 1) {
							$workApi  = WorkUtils::getWorkApi($work_corp->corp_id);
							$workUser = WorkTagUser::find()->andWhere(['tag_id' => $tag_id, 'user_id' => $user_id])->one();
							if (!empty($workUser)) {
								WorkTagUser::deleteAll(['id' => $workUser->id]);
							}
							try {
								$work_tag  = WorkTag::findOne($tag_id);
								$work_user = WorkUser::findOne($user_id);
								if (!empty($work_tag->tagid)) {
									$result = $workApi->tagDelTagUsers(intval($work_tag->tagid), [$work_user->userid]);
									//\Yii::error($result, '$result-0');
								}
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'removeUserTag-getMessage');
							}
						} elseif ($s_type == 3){
							$tagChat  = WorkTagChat::findOne(['tag_id' => $tag_id, 'chat_id' => $user_id]);
							$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
							if (!empty($tagChat) && $tagChat->status == 1) {
								$remark          .= '【' . $work_tag['tagname'] . '】、';
								$tagChat->status = 0;
								$tagChat->save();
							}
						} elseif ($s_type == 5){
							$tagAttachment = WorkTagAttachment::findOne(['tag_id' => $tag_id, 'attachment_id' => $user_id]);
							if (!empty($tagAttachment) && $tagAttachment->status == 1) {
								$tagAttachment->status = 0;
								$tagAttachment->save();
							}
						} else {

							$tagFollow = WorkTagFollowUser::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id]);

							$workApi = '';
//							$workContact = WorkTagContact::find()->andWhere(['tag_id' => $tag_id, 'contact_id' => $user_id])->one();
//							if (!empty($workContact)) {
//								WorkTagContact::deleteAll(['id' => $workContact->id]);
//							}

							//$work_tag = WorkTag::findOne($tag_id);
							$work_tag = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
							if (!empty($work_tag['tagid'])) {
								array_push($workRemoveTag, $work_tag['tagid']);
								if (!empty($tagFollow) && $tagFollow->status == 1) {
									$remark .= '【' . $work_tag['tagname'] . '】、';
								}
							}

							if (!empty($tagFollow)) {
								//WorkTagFollowUser::deleteAll(['id' => $tagFollow->id]);
								$tagFollow->update_time = $time;
								$tagFollow->status = 0;
								$tagFollow->save();
								WorkUserTagExternal::updateAll(['status' => 0], ['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
								//WorkTagChat::updateAll(['status' => 0], ['tag_id' => $tag_id]);
							}


						}

					}

					if ($s_type == 2 && !empty($workRemoveTag)) {
						try {
							$workApi = WorkUtils::getWorkApi($work_corp->corp_id, WorkUtils::EXTERNAL_API);
						} catch (\Exception $e) {
							\Yii::error($e->getMessage(), 'addUserTag-getMessage');
						}

						if ($workApi instanceof Work) {

							$exMarkData = WorkExternalContactFollowUser::find()->alias('fu')
								->select('`fu`.`userid` as userId, `ec`.`external_userid` as externalUserId')
								->leftJoin('{{%work_external_contact}} as ec', '`ec`.`id` = `fu`.`external_userid`')
								->where(['fu.id' => $user_id])->asArray()->one();
							if (!empty($exMarkData)) {
								if ($workApi && !empty($exMarkData['userId']) && !empty($exMarkData['externalUserId'])) {
									try {
										\Yii::error($workRemoveTag, '$workRemoveTag');
										$res = $workApi->ECMarkTag($exMarkData['userId'], $exMarkData['externalUserId'], [], $workRemoveTag);
										\Yii::error($res, '$res1');
									} catch (\Exception $e) {
										Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":ECMarkTag");
									}
								}
							}

//							$exMarkData = WorkExternalContactFollowUser::find()->alias('fu')
//								->select('`wu`.`userid` as userId, `ec`.`external_userid` as externalUserId')
//								->leftJoin('{{%work_user}} as wu', '`wu`.`id` = `fu`.`user_id`')
//								->leftJoin('{{%work_external_contact}} as ec', '`ec`.`id` = `fu`.`external_userid`')
//								->where(['fu.external_userid' => $user_id])
//								->andWhere(['wu.status' => 1])->asArray()->all();
//
//							if (!empty($exMarkData)) {
//								foreach ($exMarkData as $ecMarkInfo) {
//									if ($workApi && !empty($ecMarkInfo['userId']) && !empty($ecMarkInfo['externalUserId'])) {
//										try {
//											$workApi->ECMarkTag($ecMarkInfo['userId'], $ecMarkInfo['externalUserId'], [], $workRemoveTag);
//										} catch (\Exception $e) {
//											Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":ECMarkTag");
//										}
//									}
//								}
//							}

						}
					}
					//记录客户轨迹
					if ($s_type == 2) {
						$followUser = WorkExternalContactFollowUser::findOne($user_id);
						if (!empty($followUser) && !empty($remark)) {
							$relatedId = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
							ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $followUser->user_id, 'external_id' => $followUser->external_userid, 'event' => 'del_tag', 'related_id' => $relatedId, 'remark' => rtrim($remark, '、')]);
						}
					}

					//记录客户群轨迹
					if ($s_type == 3 && !empty($remark)) {
						$userCorp          = UserCorpRelation::findOne(['corp_id' => $work_corp->corp_id]);
						$addData           = ['uid' => $userCorp->uid, 'event' => 'chat_track', 'event_id' => 5, 'related_id' => $user_id];
						$addRemark         = '系统移除标签';
						$remark            = rtrim($remark, '、');
						$addData['remark'] = $addRemark . $remark;
						ExternalTimeLine::addExternalTimeLine($addData);
					}
				}
				$transaction->commit();

				return $fail;
			} catch (\Exception $e) {
				$transaction->rollBack();
				\Yii::error($e->getMessage(), 'workRemoveUserTag');
			}
		}

		/**
		 * 移除群客户标签
		 *
		 * @param int   $chat_id
		 * @param array $external_ids
		 * @param array $tag_ids
		 *
		 * @return boolean
		 *
		 */
		public static function removeChatUserTag ($chat_id, array $external_ids, array $tag_ids, $otherData = [])
		{
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$fail = 0;
				//标签信息
				$tag_data = WorkTag::find()->where(['in', 'id', $tag_ids])->asArray()->all();
				$tagD     = [];
				foreach ($tag_data as $k => $v) {
					$tagD[$v['id']] = $v;
				}

				$work_corp = WorkTag::findOne($tag_ids[0]);
				$workApi   = '';
				try {
					$workApi = WorkUtils::getWorkApi($work_corp->corp_id, WorkUtils::EXTERNAL_API);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'addUserTag-getMessage');
				}

				foreach ($external_ids as $external_id) {
					$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $external_id, 'del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
					$followUser = $followUser->select('`id`, `external_userid`, `user_id`')->asArray()->all();
					if (empty($followUser)) {
						$fail++;
						continue;
					}
					foreach ($followUser as $val) {
						$remark        = '';
						$workRemoveTag = [];
						$user_id       = $val['id'];

						foreach ($tag_ids as $tag_id) {
							$tagFollow = WorkTagFollowUser::findOne(['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
							$work_tag  = isset($tagD[$tag_id]) ? $tagD[$tag_id] : [];
							if (!empty($work_tag['tagid'])) {
								array_push($workRemoveTag, $work_tag['tagid']);
								if (!empty($tagFollow) && $tagFollow->status == 1) {
									$remark .= '【' . $work_tag['tagname'] . '】、';
								}
							}

							if (!empty($tagFollow)) {
								$tagFollow->status = 0;
								$tagFollow->save();
								WorkUserTagExternal::updateAll(['status' => 0], ['tag_id' => $tag_id, 'follow_user_id' => $user_id]);
							}

						}

						if (!empty($workRemoveTag)) {
							if ($workApi instanceof Work) {
								$exMarkData = WorkExternalContactFollowUser::find()->alias('fu')
									->select('`fu`.`userid` as userId, `ec`.`external_userid` as externalUserId')
									->leftJoin('{{%work_external_contact}} as ec', '`ec`.`id` = `fu`.`external_userid`')
									->where(['fu.id' => $user_id])->asArray()->one();
								if (!empty($exMarkData)) {
									if ($workApi && !empty($exMarkData['userId']) && !empty($exMarkData['externalUserId'])) {
										try {
											\Yii::error($workRemoveTag, '$workRemoveTag');
											$res = $workApi->ECMarkTag($exMarkData['userId'], $exMarkData['externalUserId'], [], $workRemoveTag);
											\Yii::error($res, '$res1');
										} catch (\Exception $e) {
											Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":ECMarkTag");
										}
									}
								}

							}
						}
						//记录客户轨迹
						if (!empty($remark)) {
							$relatedId = isset($otherData['user_id']) && !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
							ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $val['user_id'], 'external_id' => $val['external_userid'], 'event' => 'chat_del_tag', 'event_id' => $chat_id, 'related_id' => $relatedId, 'remark' => rtrim($remark, '、')]);
						}
					}

				}
				$transaction->commit();

				return $fail;
			} catch (\Exception $e) {
				$transaction->rollBack();
				\Yii::error($e->getMessage(), 'workRemoveUserTag');
			}
		}

		/**
		 * @param int $id
		 * @param     $corp_id
		 * @param     $tagName
		 * @param     $type
		 * @param int $group_id
		 *
		 * @return bool
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\Exception
		 */
		public static function add ($id = 0, $corp_id, $tagName, $type, $group_id = 0)
		{
			if (empty($id)) {
				foreach ($tagName as $name) {
					static::createTag($corp_id, $name, '', $type, $group_id);
				}
			} else {
				static::updateTag($id, $tagName, $group_id, $type);
			}

		}

		/**
		 * @param $type
		 * @param $corp_id
		 * @param $name
		 * @param $group_id
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		private function createWorkTag ($type, $corp_id, $name, $group_id)
		{
			if ($type == 1) {
				$workApi = WorkUtils::getWorkApi($corp_id);
				//企业标签
				$tagName = Tag::parseFromArray(['tagname' => $name]);
				$workApi->tagCreate($tagName, $tagId);
				$this->tagid = strval($tagId);
			} elseif ($type == 0) {
				$workApi = '';
				try {
					$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'createTag-getMessage');
				}
				if ($workApi instanceof Work) {
					//客户标签
					$work_tag_group = WorkTagGroup::findOne($group_id);
					if (!empty($work_tag_group) && !empty($work_tag_group->group_name)) {
						$group_name               = $work_tag_group->group_name;
						$etcTag['group_name']     = strval($group_name);
						$tag                      = [
							'name' => $name,
						];
						$etcTag['tag']            = [$tag];
						$ECTagGroup               = ExternalContactTagGroup::parseFromArray($etcTag);
						$data                     = $workApi->ECAddCorpTag($ECTagGroup);
						$tag_data                 = SUtils::Object2Array($data);
						$this->tagid              = $tag_data['tag'][0]['id'];
						$work_tag_group->group_id = $tag_data['group_id'];
						$work_tag_group->save();
					}
				}
			}
		}

		/**
		 * 创建标签
		 *
		 * @param        $corp_id
		 * @param        $name
		 * @param string $tagId
		 * @param        $type
		 * @param        $group_id
		 *
		 * @return bool
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\Exception
		 */
		public static function createTag ($corp_id, $name, $tagId = '', $type, $group_id)
		{
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$workTag           = new WorkTag();
				$workTag->corp_id  = $corp_id;
				$workTag->tagname  = $name;
				$workTag->tagid    = $tagId;
				$workTag->type     = $type;
				$workTag->group_id = $group_id;

				if (!$workTag->validate() || !$workTag->save()) {
					throw new InvalidDataException(SUtils::modelError($workTag));
				}

				if ($type == 0 || $type == 1){
					try {
						$workTag->createWorkTag($type, $corp_id, $name, $group_id);
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), 'createTag');
					}

					if (!$workTag->validate() || !$workTag->save()) {
						throw new InvalidDataException(SUtils::modelError($workTag));
					}
				}

				$transaction->commit();

			} catch (\Exception $e) {
				$transaction->rollBack();
				$message = $e->getMessage();
				if (strpos($message, '40071') !== false) {
					$message = '标签【' . $name . '】已在企业微信后台创建，请点击同步标签即可';
				}
				if (strpos($message, '48002') !== false) {
					$message = '标签API接口无权限调用';
				}
				if (strpos($message, '40068') !== false) {
					$message = '不合法的标签ID';
				}
				\Yii::error($e->getMessage(), 'createTag');
				throw new InvalidDataException($message);
			}

		}

		/**
		 * @param $id
		 * @param $tagName
		 * @param $group_id
		 * @param $type
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\Exception
		 */
		public static function updateTag ($id, $tagName, $group_id, $type)
		{
			$transaction = Yii::$app->mdb->beginTransaction();
			try {
				$workTag           = static::findOne($id);
				$oldTagName        = $workTag->tagname;
				$oldTagGroupId     = $workTag->group_id;
				$workTag->group_id = $group_id;
				$workTag->tagname  = $tagName;
				if (!$workTag->validate() || !$workTag->save()) {
					throw new InvalidDataException(SUtils::modelError($workTag));
				}
				$tagFollowId = [];
				try {
					if ($type == 0 || $type == 1){
						if ($oldTagName != $tagName || ($type ==0 && ($oldTagGroupId != $group_id))) {
							$corp_id = $workTag->corp_id;
							//同步到企业微信
							if ($workTag->tagid) {
								if ($type == 1) {
									//企业标签
									$workApi = WorkUtils::getWorkApi($corp_id);
									$tagData = Tag::parseFromArray(['tagid' => intval($workTag->tagid), 'tagname' => $tagName]);
									$workApi->tagUpdate($tagData);
								} else {
									$workApi = '';
									try {
										$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
									} catch (\Exception $e) {
										\Yii::error($e->getMessage(), 'createTag-getMessage');
									}
									//移动分组
									if ($oldTagGroupId != $group_id) {
										$flag    = false;
										if (!empty($workTag->tagid)) {
											$drop_tag = $workApi->ECDelCorpTag([$workTag->tagid]);
											if ($drop_tag['errcode'] == 0) {
												$flag = true;
											}
										} else {
											$flag = true;
										}
										if ($flag) {
											$newGroup = WorkTagGroup::findOne($group_id);
											$etcTag['group_name'] = strval($newGroup->group_name);
											$tag_new              = [
												'name' => $workTag->tagname,
											];
											$etcTag['tag']        = [$tag_new];
											$ECTagGroup           = ExternalContactTagGroup::parseFromArray($etcTag);
											$data                 = $workApi->ECAddCorpTag($ECTagGroup);
											$tag_data             = SUtils::Object2Array($data);
											$workTag->tagid      = $tag_data['tag'][0]['id'];
											$workTag->save();

											$followUserIds = WorkTagFollowUser::find()->where(['tag_id' => $workTag->id, 'status' => 1])->asArray()->all();
											if (!empty($followUserIds)) {
												foreach ($followUserIds as $userId) {
													array_push($tagFollowId, $userId['id']);
												}
											}
											if (!empty($tagFollowId)) {
												\Yii::$app->queue->push(new WorkTagFollowUserJob(
													['followIds' => $tagFollowId]
												));
											}
										}
									} else {//只修改客户标签
										if ($workApi instanceof Work) {
											$tagData = ExternalContactTag::parseFromArray(['id' => $workTag->tagid, 'name' => $tagName]);
											$workApi->ECEditCorpTag($tagData);
										}
									}
								}
							} else {
								$workTag->createWorkTag($type, $corp_id, $tagName, $group_id);
								$workTag->save();
							}
						} elseif (empty($workTag->tagid)) {
							$workTag->createWorkTag($type, $workTag->corp_id, $tagName, $group_id);
							$workTag->save();
						}
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'updateTag');
					throw new InvalidDataException($e->getMessage());
				}

				$transaction->commit();

				return true;
			} catch (\Exception $e) {
				$transaction->rollBack();
				$message = $e->getMessage();
				if (strpos($message, '40071') !== false) {
					$message = '标签【' . $tagName . '】在企业微信后台已存在，请重新修改';
				}
				if (strpos($message, '40068') !== false) {
					$message = '不合法的标签ID';
				}
				if (strpos($message, '81011') !== false) {
					$message = '无权限操作标签';
				}
				\Yii::error($e->getMessage(), 'updateTag');
				throw new InvalidDataException($message);
			}

		}

		/**
		 * 删除标签同步至企业微信
		 *
		 * @param int $corp_id
		 * @param int $type
		 * @param int $tagId
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 */
		public static function deleteTag ($corp_id, $type, $tagId)
		{
			try {
				//同步到企业微信
				if ($type == 1) {
					//企业标签
					$workApi = WorkUtils::getWorkApi($corp_id);
					$workApi->tagDelete(intval($tagId));
				} elseif ($type == 0) {
					//客户标签
					try {
						$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
						if ($workApi instanceof Work) {
							$workApi->ECDelCorpTag([$tagId]);
						}
					} catch (\Exception $e) {
						throw new InvalidDataException($e->getMessage());
					}
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '40068') !== false) {
					$message = '不合法的标签ID';
				}
				if (strpos($message, '81011') !== false) {
					$message = '无权限操作标签';
				}
				\Yii::error($e->getMessage(), 'deleteTag');
				throw new InvalidDataException($message);
			}

			return true;
		}

		/**
		 * 同步企业标签
		 *
		 * @param $corpId
		 * @param $group_id
		 *
		 * @return bool
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function syncWorkTag ($corpId, $group_id)
		{
			try {
				$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$data     = $workApi->tagList();
					$tag_data = SUtils::Object2Array($data);
					$tagids   = array_column($tag_data, 'tagid');
					if (!empty($tag_data)) {
						$tag_info = WorkTag::find()->andWhere(['type' => 1, 'corp_id' => $corpId, 'is_del' => 0])->andWhere(['<>', 'tagid', ''])->all();
						if (!empty($tag_info)) {
							foreach ($tag_info as $info) {
								if (!in_array($info->tagid, $tagids)) {
									$info->is_del = 1;
									$info->save();
								}
							}
						}
						if (empty($group_id)) {
							$tagGroupInfo = WorkTagGroup::findOne(['type' => 1, 'corp_id' => $corpId, 'group_name' => "未分组"]);
							if (empty($tagGroupInfo)) {
								$group_id = WorkTagGroup::add(0, $corpId, '未分组', 1);
							} else {
								$group_id = $tagGroupInfo->id;
							}
						}
						foreach ($tag_data as $v) {
							//查看当前标签名其他分组可有，如果已存在则不再同步
							$workTagIsExist = WorkTag::findOne(['corp_id' => $corpId, 'is_del' => 0, 'type' => 1, 'tagname' => $v['tagname'], 'tagid' => $v['tagid']]);
							if (empty($workTagIsExist)) {
								$workTag = WorkTag::findOne(['corp_id' => $corpId, 'is_del' => 0, 'type' => 1, 'tagname' => $v['tagname']]);
								if (!empty($workTag)) {
									$workTag->tagid = strval($v['tagid']);
									$workTag->save();
								} else {
									$work_tag = WorkTag::findOne(['corp_id' => $corpId, 'is_del' => 0, 'type' => 1, 'tagid' => $v['tagid']]);
									if (!empty($work_tag)) {
										$work_tag->tagname = $v['tagname'];
										$work_tag->is_del  = 0;
									} else {
										$work_tag           = new WorkTag();
										$work_tag->corp_id  = $corpId;
										$work_tag->tagid    = strval($v['tagid']);
										$work_tag->tagname  = $v['tagname'];
										$work_tag->type     = 1;
										$work_tag->group_id = $group_id;
									}
									$work_tag->save();
								}
							}
						}
					}
				} else {
					throw new InvalidDataException("该企业尚未绑定应用");
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'syncWorkTag');
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		/**同步标签成员
		 *
		 * @param $corpId
		 *
		 * @throws InvalidDataException
		 */
		public static function getWorkTagUser ($corpId)
		{
			try {
				$work_tag = WorkTag::find()->andWhere(['is_del' => 0, 'type' => 1, 'corp_id' => $corpId])->andWhere((['<>', 'tagid', '']))->all();
				$workApi  = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				if (!empty($work_tag)) {
					foreach ($work_tag as $tag) {
						if (!empty($workApi)) {
							$data     = $workApi->tagGet(intval($tag->tagid));
							$tag_data = SUtils::Object2Array($data);
							if (!empty($tag_data)) {
								WorkTagUser::deleteAll(['tag_id' => $tag->id]);
								$userlist = $tag_data['userlist'];
								if (!empty($userlist)) {
									foreach ($userlist as $list) {
										$user = WorkUser::findOne(['userid' => $list['userid'], 'corp_id' => $corpId]);
										if (!empty($user)) {
											$tag_user = WorkTagUser::findOne(['tag_id' => $tag->id, 'user_id' => $user->id]);
											if (empty($tag_user)) {
												$user_tag          = new WorkTagUser();
												$user_tag->tag_id  = $tag->id;
												$user_tag->user_id = $user->id;
												$user_tag->save();
											}
										}

									}
								}
							}
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'getWorkTagUser');
				throw new InvalidDataException($e->getMessage());
			}

		}

		/**
		 * 同步客户标签
		 *
		 * @param $corpId
		 *
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function syncWorkTagExternal ($corpId)
		{
			try {
				$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$tagData  = $workApi->ECGetCorpTagList();
					$tag_data = SUtils::Object2Array($tagData);
					$groupId  = [];
					if (!empty($tag_data)) {
						foreach ($tag_data as $v) {
							$tag = $v['tag'];
							array_push($groupId, $v['group_id']);
							$tag_group = WorkTagGroup::findOne(['corp_id' => $corpId, 'group_id' => $v['group_id'], 'type' => 0]);
							if (empty($tag_group)) {
								$tag_group = WorkTagGroup::find()->where(['corp_id' => $corpId, 'group_name' => $v['group_name'], 'type' => 0])->andWhere('group_id IS NULL')->one();
								if (empty($tag_group)) {
									$tag_group          = new WorkTagGroup();
									$tag_group->corp_id = $corpId;
									$tag_group->type    = 0;
								}

								$tag_group->group_id = $v['group_id'];
							}

							$tag_group->group_name = $v['group_name'];
							$tag_group->save();

							$group_id = $tag_group->id;

							if (!empty($tag)) {
								$tagInfos = WorkTag::find()->where(['type' => 0, 'corp_id' => $corpId, 'is_del' => 0, 'group_id' => $group_id])->all();
								if (!empty($tagInfos)) {
									/** @var WorkTag $tagInfo */
									foreach ($tagInfos as $tagInfo) {
										$tagInfo->is_del = 1;
										$tagInfo->save();
									}
								}

								foreach ($tag as $tt) {
									//查看当前标签名其他分组可有，如果已存在则不再同步
									$workTagIsExist = WorkTag::findOne(['corp_id' => $corpId, 'type' => 0, 'tagid' => $tt['id']]);
									if (empty($workTagIsExist)) {
										$workTagIsExist = WorkTag::findOne(['corp_id' => $corpId, 'type' => 0, 'tagname' => $tt['name'], 'tagid' => '']);
										if (empty($workTagIsExist)) {
											$workTagIsExist          = new WorkTag();
											$workTagIsExist->corp_id = $corpId;
											$workTagIsExist->type    = 0;
										}

										$workTagIsExist->tagid = strval($tt['id']);
									}

									$workTagIsExist->tagname  = $tt['name'];
									$workTagIsExist->group_id = $group_id;
									$workTagIsExist->is_del   = 0;
									$workTagIsExist->save();
								}
							}
						}
					}

					$tagGroup = WorkTagGroup::find()->where(['corp_id' => $corpId, 'type' => 0])->andWhere('group_id IS NOT NULL')->all();
					if (!empty($tagGroup) && !empty($groupId)) {
						/** @var WorkTagGroup $group */
						foreach ($tagGroup as $group) {
							if (!in_array($group->group_id, $groupId)) {
								WorkTag::updateAll(["is_del" => 1], ["group_id" => $group->group_id, "corp_id" => $group->corp_id]);
								$group->delete();
							}
						}
					}
				} else {
					throw new InvalidDataException("参数错误");
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'syncWorkTagExternal');
				$message = $e->getMessage();
				if (strpos($message, '40013') !== false) {
					$message = 'corpid无效';
				}
				throw new InvalidDataException($message);
			}

		}

		/**
		 * 获取企业标签及客户标签并同步
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getWorkTagSync ()
		{
			//企业微信号
			$work_corp = WorkCorp::find()->select('id,corpid')->where('corpid != \'\' AND corp_type != \'\'')->asArray()->all();
			foreach ($work_corp as $k => $v) {
				$corpId  = $v['id'];
				$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					/*********************************** 企业标签 *********************************/
					$tag_group = WorkTagGroup::findOne(['corp_id' => $corpId, 'group_name' => '未分组']);
					static::syncWorkTag($corpId, $tag_group->id);

					/*********************************** 客户标签 *********************************/
					static::syncWorkTagExternal($corpId);

					/*********************************** 同步标签成员 *********************************/
					static::getWorkTagUser($corpId);
				}

				$workCorp                         = WorkCorp::findOne($corpId);
				$workCorp->last_tag_time          = time();
				$workCorp->last_customer_tag_time = time();
				$workCorp->save();
			}

			return true;
		}

	}
