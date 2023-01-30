<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_protect}}".
	 *
	 * @property int  $id
	 * @property int  $uid                账户ID
	 * @property int  $sub_id             子账户ID
	 * @property int  $corp_id            授权的企业ID
	 * @property int  $user_id            操作的成员ID
	 * @property int  $is_master          状态：0、主账户添加，1、子账户添加
	 * @property int  $type               客户类型：0非企微客户1企微客户
	 * @property int  $external_id        外部联系人ID
	 * @property int  $follow_user_id     外部联系人添加信息表id
	 *
	 * @property User $u
	 */
	class PublicSeaProtect extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_protect}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sub_id', 'is_master', 'type', 'external_id', 'user_id'], 'integer'],
				[['external_id'], 'required'],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'uid'            => Yii::t('app', '账户ID'),
				'sub_id'         => Yii::t('app', '子账户ID'),
				'corp_id'        => Yii::t('app', '授权的企业ID'),
				'user_id'        => Yii::t('app', '操作的成员ID'),
				'is_master'      => Yii::t('app', '状态：0、主账户添加，1、子账户添加'),
				'type'           => Yii::t('app', '客户类型：0非企微客户1企微客户'),
				'external_id'    => Yii::t('app', '外部联系人ID'),
				'follow_user_id' => Yii::t('app', '外部联系人添加信息表id'),
			];
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		//设置客户保护
		public static function protect ($followIds, $otherData)
		{
			$isMaster = !empty($otherData['is_master']) ? $otherData['is_master'] : 0;
			$uid      = !empty($otherData['uid']) ? $otherData['uid'] : 0;
			$subId    = !empty($otherData['sub_id']) ? $otherData['sub_id'] : 0;
			$corpId   = !empty($otherData['corp_id']) ? $otherData['corp_id'] : 0;
			$userId   = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
			$userName = !empty($otherData['user_name']) ? '【' . $otherData['user_name'] . '】' : '';
			$type     = !empty($otherData['type']) ? $otherData['type'] : 0;

			//子账号限制
			if ($isMaster == 1) {
				//是否有员工
				if (empty($userId)) {
					throw new InvalidDataException('当前帐号无关联的成员！');
				}
				$reClaim = PublicSeaReclaimSet::getClaimRule($corpId, $userId);
				if (empty($reClaim) || empty($reClaim->is_protect)) {
					throw new InvalidDataException('因未设置客户保护数量，故无法设置保护对象！');
				}
				$protectNum  = $reClaim->protect_num;
				$customCount = PublicSeaProtect::find()->where(['corp_id' => $corpId, 'sub_id' => $subId])->count();
				if ($customCount >= $protectNum) {
					throw new InvalidDataException('永不进入公海池客户数已达上限，无法再设置！');
				}
				$restNum = $protectNum - $customCount;
			} else {
				$subId   = 0;
				$restNum = 0;
			}

			//总选择数量
			$count     = count($followIds);
			$success   = $fail = 0;
			$errorData = [];
			if ($type == 0) {
				$followUser = PublicSeaContactFollowUser::find()->where(['id' => $followIds])->orderBy(['add_time' => SORT_DESC])->all();
				/**@var PublicSeaContactFollowUser $followInfo * */
				foreach ($followUser as $followInfo) {
					if ($followInfo->is_protect == 1) {
						$fail++;
						array_push($errorData, '客户已设置过保护');
						continue;
					}
					if ($isMaster == 1 && empty($restNum)) {
						$fail++;
						array_push($errorData, '客户保护数量已达上限');
						continue;
					}
					$protect                 = new PublicSeaProtect();
					$protect->uid            = $uid;
					$protect->sub_id         = $subId;
					$protect->corp_id        = $corpId;
					$protect->is_master      = $isMaster;
					$protect->type           = 0;
					$protect->external_id    = $followInfo->sea_id;
					$protect->user_id        = $userId;
					$protect->follow_user_id = $followInfo->id;
					if (!$protect->validate() || !$protect->save()) {
						$fail++;
						continue;
					}
					$followInfo->is_protect = 1;
					$followInfo->update();
					$restNum--;
					$success++;

					//轨迹
					$belongName = '';//原归属
					if (!empty($followInfo->user_id)) {
						$belongWorkUser = WorkUser::findOne($followInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '（归属于' . $belongWorkUser->name . '）';
						}
					}
					$remark = $userName . '将该客户' . $belongName . '设为保护';
					PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $followInfo->sea_id, 'user_id' => $followInfo->user_id, 'event' => 'protect_custom', 'remark' => $remark]);
				}
			} else {
				$followUser = WorkExternalContactFollowUser::find()->where(['id' => $followIds])->all();

				//查询已设置的客户
				$protectList = PublicSeaProtect::find()->where(['uid' => $uid, 'type' => 1, 'corp_id' => $corpId])->all();
				$externalIds = [];
				if (!empty($protectList)) {
					$externalIds = array_column($protectList, 'external_id', 'user_id');
				}
				/**@var WorkExternalContactFollowUser $followInfo * */
				foreach ($followUser as $followInfo) {
					if ($followInfo->is_protect == 1) {
						$fail++;
						array_push($errorData, '客户已设置过保护');
						continue;
					}
					if ($followInfo->is_reclaim == 1) {
						$fail++;
						array_push($errorData, '客户已在公海池');
						continue;
					}

					if (in_array($followInfo->external_userid, $externalIds)) {
						$fail++;
						if ($count > 1) {
							array_push($errorData, '客户已被别人设置了保护，不能重复设置');
						} else {
							$searchUserId = array_search($followInfo->external_userid, $externalIds);
							if (!empty($searchUserId)) {
								$workUser    = WorkUser::findOne($searchUserId);
								$contactInfo = WorkExternalContact::findOne($followInfo->external_userid);
								if (!empty($workUser) && !empty($contactInfo)) {
									array_push($errorData, '【' . $contactInfo->name . '】已被【' . $workUser->name . '】保护，您已无法再对该客户进行保护。');
								}
							}
						}
						continue;
					}
					if ($isMaster == 1 && empty($restNum)) {
						$fail++;
						array_push($errorData, '客户保护数量已达上限');
						continue;
					}
					$protect                 = new PublicSeaProtect();
					$protect->uid            = $uid;
					$protect->sub_id         = $subId;
					$protect->corp_id        = $corpId;
					$protect->is_master      = $isMaster;
					$protect->type           = 1;
					$protect->external_id    = $followInfo->external_userid;
					$protect->user_id        = $userId;
					$protect->follow_user_id = $followInfo->id;
					if (!$protect->validate() || !$protect->save()) {
						$fail++;
						continue;
					}
					array_push($externalIds, $followInfo->external_userid);
					$followInfo->is_protect = 1;
					$followInfo->update();
					$restNum--;
					$success++;

					//轨迹
					$belongName = '';//原归属
					if (!empty($followInfo->user_id)) {
						$belongWorkUser = WorkUser::findOne($followInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '（归属于' . $belongWorkUser->name . '）';
						}
					}
					$remark = $userName . '将该客户' . $belongName . '设为保护';
					ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'external_id' => $followInfo->external_userid, 'user_id' => $followInfo->user_id, 'event' => 'protect_custom', 'remark' => $remark]);
				}
			}
			$errorData = array_unique($errorData);

			if ($count == 1) {
				if (!empty($errorData)) {
					throw new InvalidDataException($errorData[0]);
				} else {
					$textHtml = '操作成功';
				}
			} else {
				$textHtml = '设置' . $count . '位客户保护';
				if (!empty($success)) {
					$textHtml .= '，成功' . $success . '位';
				}
				if (!empty($fail)) {
					$textHtml .= '，失败' . $fail . '位';
				}
				if (!empty($errorData)) {
					$errorStr = implode('、', $errorData);
					$textHtml .= '，失败原因：' . $errorStr;
				}
				if ($count == $fail) {
					throw new InvalidDataException($textHtml);
				}
			}

			return $textHtml;
		}

		//取消客户保护
		public static function noProtect ($followIds, $otherData)
		{
			$isMaster = !empty($otherData['is_master']) ? $otherData['is_master'] : 0;
			$uid      = !empty($otherData['uid']) ? $otherData['uid'] : 0;
			$subId    = !empty($otherData['sub_id']) ? $otherData['sub_id'] : 0;
			$corpId   = !empty($otherData['corp_id']) ? $otherData['corp_id'] : 0;
			$userId   = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
			$userName = !empty($otherData['user_name']) ? '【' . $otherData['user_name'] . '】' : '';
			$type     = !empty($otherData['type']) ? $otherData['type'] : 0;

			//总选择数量
			$count     = count($followIds);
			$success   = $fail = 0;
			$errorData = [];
			if ($type == 0) {
				$followUser = PublicSeaContactFollowUser::find()->where(['id' => $followIds])->all();
				/**@var PublicSeaContactFollowUser $followInfo * */
				foreach ($followUser as $followInfo) {
					if ($followInfo->is_protect == 0) {
						$fail++;
						array_push($errorData, '客户未设置保护');
						continue;
					}
					if ($isMaster == 0) {
						$followInfo->is_protect = 0;
						$followInfo->update();
						PublicSeaProtect::deleteAll(['type' => 0, 'follow_user_id' => $followInfo->id]);
						$success++;
					} else {
						$protect = PublicSeaProtect::findOne(['uid' => $uid, 'type' => 0, 'follow_user_id' => $followInfo->id]);
						if (!empty($protect) && ($protect->sub_id == $subId || $followInfo->user_id == $userId)) {
							$followInfo->is_protect = 0;
							$followInfo->update();
							PublicSeaProtect::deleteAll(['type' => 0, 'follow_user_id' => $followInfo->id]);
							$success++;
						} else {
							$fail++;
							array_push($errorData, '客户已被其他员工保护，无法取消保护');
							continue;
						}
					}
					//轨迹
					$belongName = '';//原归属
					if (!empty($followInfo->user_id)) {
						$belongWorkUser = WorkUser::findOne($followInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '（归属于' . $belongWorkUser->name . '）';
						}
					}
					$remark = $userName . '将该客户' . $belongName . '取消保护';
					PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $followInfo->sea_id, 'user_id' => $followInfo->user_id, 'event' => 'no_protect_custom', 'remark' => $remark]);
				}
			} else {
				$followUser = WorkExternalContactFollowUser::find()->where(['id' => $followIds])->all();
				/**@var WorkExternalContactFollowUser $followInfo * */
				foreach ($followUser as $followInfo) {
					if ($followInfo->is_protect == 0) {
						$fail++;
						array_push($errorData, '客户未设置保护');
						continue;
					}
					if ($isMaster == 0) {
						$followInfo->is_protect = 0;
						$followInfo->update();
						PublicSeaProtect::deleteAll(['uid' => $uid, 'type' => 1, 'follow_user_id' => $followInfo->id]);
						$success++;
					} else {
						$protect = PublicSeaProtect::findOne(['uid' => $uid, 'type' => 1, 'follow_user_id' => $followInfo->id]);
						if (!empty($protect) && ($protect->sub_id == $subId || $followInfo->user_id == $userId)) {
							$followInfo->is_protect = 0;
							$followInfo->update();
							PublicSeaProtect::deleteAll(['uid' => $uid, 'type' => 1, 'follow_user_id' => $followInfo->id]);
							$success++;
						} else {
							$fail++;
							array_push($errorData, '客户已被其他员工保护，无法取消保护');
							continue;
						}
					}
					//轨迹
					$belongName = '';//原归属
					if (!empty($followInfo->user_id)) {
						$belongWorkUser = WorkUser::findOne($followInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '（归属于' . $belongWorkUser->name . '）';
						}
					}
					$remark = $userName . '将该客户' . $belongName . '取消保护';
					ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'external_id' => $followInfo->external_userid, 'user_id' => $followInfo->user_id, 'event' => 'no_protect_custom', 'remark' => $remark]);
				}
			}
			$errorData = array_unique($errorData);

			if ($count == 1) {
				if (!empty($errorData)) {
					throw new InvalidDataException($errorData[0]);
				} else {
					$textHtml = '操作成功';
				}
			} else {
				$textHtml = '设置' . $count . '位客户保护取消';
				if (!empty($success)) {
					$textHtml .= '，成功' . $success . '位';
				}
				if (!empty($fail)) {
					$textHtml .= '，失败' . $fail . '位';
				}
				if (!empty($errorData)) {
					$errorStr = implode('、', $errorData);
					$textHtml .= '，失败原因：' . $errorStr;
				}
				if ($count == $fail) {
					throw new InvalidDataException($textHtml);
				}
			}

			return $textHtml;
		}

		//删除保护数据
		public static function delProtect ($userId, $externalId)
		{
			$followUser = WorkExternalContactFollowUser::findOne(['user_id' => $userId, 'external_userid' => $externalId]);
			if (!empty($followUser)) {
				PublicSeaProtect::deleteAll(['type' => 1, 'follow_user_id' => $followUser->id]);
			}
		}

		//根据帐号获取保护数量
		public static function getProtectBySubId ($corpId, $subId)
		{
			$isShow    = 0;
			$isRest    = 0;
			$subUserId = 0;
			$subUser   = SubUser::findOne($subId);
			if (!empty($subUser) && !empty($subUser->account)) {
				$workUser = WorkUser::findOne(['corp_id' => $corpId, 'mobile' => $subUser->account, 'is_del' => 0]);
				if (!empty($workUser)) {
					$subUserId = $workUser->id;
					$reClaim   = PublicSeaReclaimSet::getClaimRule($corpId, $workUser->id);
					if (!empty($reClaim) && !empty($reClaim->is_protect)) {
						$isShow      = 1;
						$protectNum  = $reClaim->protect_num;
						$customCount = PublicSeaProtect::find()->where(['corp_id' => $corpId, 'sub_id' => $subId])->count();
						if ($customCount < $protectNum) {
							$isRest = 1;
						}
					}
				}
			}

			return ['is_show' => $isShow, 'is_rest' => $isRest, 'sub_user_id' => $subUserId];
		}

		//根据成员获取保护数量
		public static function getProtectByUserId ($corpId, $userId)
		{
			$isShow = 0;
			$isRest = 0;
			if (!empty($userId)) {
				$reClaim = PublicSeaReclaimSet::getClaimRule($corpId, $userId);
				if (!empty($reClaim) && !empty($reClaim->is_protect)) {
					$isShow      = 1;
					$protectNum  = $reClaim->protect_num;
					$customCount = PublicSeaProtect::find()->where(['corp_id' => $corpId, 'user_id' => $userId])->count();
					if ($customCount < $protectNum) {
						$isRest = 1;
					}
				}
			}

			return ['is_show' => $isShow, 'is_rest' => $isRest, 'sub_user_id' => $userId];
		}

		//根据FollowUserId获取已保护数据
		public static function getDataByFollowUserId ($corpId, $followUserIds, $type = 0)
		{
			$data        = [];
			$protectList = PublicSeaProtect::find()->alias("a")
				->leftJoin("{{%user}} as b", "a.uid = b.uid")
				->leftJoin("{{%sub_user}} as c", "a.sub_id = c.sub_id")
				->where(['a.corp_id' => $corpId, 'a.type' => $type, 'a.follow_user_id' => $followUserIds])
				->select("a.*,b.account as uaccount,c.account as saccount")
				->asArray()
				->all();
			if (!empty($protectList)) {
				$PuserIds    = array_column($protectList, "user_id");
				$workUserAll = WorkUser::find()->where(["and", ["corp_id" => $corpId], ["in", "id", $PuserIds]])->select("id,name")->asArray()->all();
				$UserData    = [];
				foreach ($workUserAll as $value) {
					$UserData[$value["id"]] = urldecode($value["name"]);
				}
				foreach ($protectList as $protect) {
					$name = '';
					if (isset($UserData[$protect["user_id"]])) {
						$name = $UserData[$protect["user_id"]];
					}
					if (empty($name)) {
						if (!empty($protect["uid"])) {
							$name = $protect["uaccount"];
						} elseif (!empty($protect["sub_id"])) {
							$name = $protect["saccount"];
						}
					}
					$data[$protect["follow_user_id"]] = ['sub_id' => $protect["sub_id"], 'name' => $name];
				}
			}

			return $data;
		}
	}
