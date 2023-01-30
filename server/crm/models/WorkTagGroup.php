<?php

	namespace app\models;

	use app\queue\WorkTagFollowUserJob;
	use Yii;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactTag;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactTagGroup;
	use app\components\InvalidDataException;

	/**
	 * This is the model class for table "{{%work_tag_group}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id    授权的企业ID
	 * @property string   $group_name 标签分组名称，长度限制为32个字以内（汉字或英文字母），标签分组名不可与其他标签组重名
	 * @property string   $group_id   企业微信的分组id
	 * @property int      $type       类型0 客户管理 1 通讯录
	 * @property int      $sort       排序
	 *
	 * @property WorkCorp $corp
	 */
	class WorkTagGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'type', 'sort'], 'integer'],
				[['group_name', 'group_id'], 'string', 'max' => 32],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => 'ID',
				'corp_id'    => '授权的企业ID',
				'group_name' => '标签分组名称，长度限制为32个字以内（汉字或英文字母），标签分组名不可与其他标签组重名',
				'group_id'   => '企业微信的分组id',
				'type'       => '类型0 客户管理 1 通讯录',
				'sort'       => '排序',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		public function dumpData ()
		{
			$result = [
				'id'         => $this->id,
				'key'        => $this->id,
				'group_name' => $this->group_name,
			];

			return $result;
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
		 * @param int $id
		 * @param     $corp_id
		 * @param     $groupName
		 * @param int $type
		 *
		 * @return bool
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function add ($id = 0, $corp_id, $groupName, $type = 0, $tagName = [], $ignore = false)
		{
			if (empty($id)) {
				//客户管理
				if ($type == 0 || ($type == 2 && !empty($tagName))) {
					$workCorp = WorkCorp::findOne($corp_id);
					if (empty($workCorp)) {
						throw new InvalidDataException('参数不正确！');
					}
					if (!$ignore && $workCorp->corp_type != 'verified') {
						throw new InvalidDataException('当前企业号未认证！');
					}
					if (empty($tagName)) {
						throw new InvalidDataException('标签名称不能为空！');
					}
					if (count($tagName) != count(array_unique($tagName))) {
						throw new InvalidDataException('标签名称存在重复！');
					}
					foreach ($tagName as $v) {
						$len = mb_strlen($v, "utf-8");
						if ($len > 15) {
							throw new InvalidDataException('名称不能超过15个字！');
						}
					}
					$tagData = WorkTag::find()->andWhere(['tagname' => $tagName, 'is_del' => 0, 'type' => $type, 'corp_id' => $corp_id])->one();
					if (!empty($tagData)) {
						throw new InvalidDataException('标签名称不能重复！');
					}
				}

				$workGroup             = new WorkTagGroup();
				$workGroup->corp_id    = $corp_id;
				$workGroup->group_name = $groupName;
				$workGroup->type       = $type;
				if (!$workGroup->validate() || !$workGroup->save()) {
					throw new InvalidDataException(SUtils::modelError($workGroup));
				}

				if($type == 0 || ($type == 2 && !empty($tagName))){
					WorkTag::add(0, $corp_id, $tagName, $type, $workGroup->id);
				}
			} else {
				$workGroup             = static::findOne($id);
				$workGroup->group_name = $groupName;
				if (!$workGroup->validate() || !$workGroup->save()) {
					throw new InvalidDataException(SUtils::modelError($workGroup));
				}
				if ($type != 2){
					$tagFollowId = [];
					$group_id      = $id;
					$corp_id       = $workGroup->corp_id;
					$workApi       = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
					$workClassName = get_class($workApi);
					if ($workClassName == 'dovechen\yii2\weWork\Work') {
						$result = static::syncWxGroup($group_id);
						if ($result) {
							$work_tag = WorkTag::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0, 'type' => 0, 'group_id' => $group_id]);
							$work_tag = $work_tag->all();
							if (!empty($work_tag)) {
								if (!empty($workApi)) {
									/** @var WorkTag $tag */
									foreach ($work_tag as $tag) {
										$etcTag['group_name'] = strval($groupName);
										$tag_new              = [
											'name' => $tag->tagname,
										];
										$etcTag['tag']        = [$tag_new];
										$ECTagGroup           = ExternalContactTagGroup::parseFromArray($etcTag);
										$data                 = $workApi->ECAddCorpTag($ECTagGroup);
										$tag_data             = SUtils::Object2Array($data);
										\Yii::error($tag_data, '$tag_data');
										$tag->tagid          = $tag_data['tag'][0]['id'];
										$workGroup->group_id = $tag_data['group_id'];
										$workGroup->save();
										if (!$tag->validate() || !$tag->save()) {
											\Yii::error(SUtils::modelError($tag), 'modelError');
										}

										$followUserIds = WorkTagFollowUser::find()->where(['tag_id' => $tag->id, 'status' => 1])->asArray()->all();
										if (!empty($followUserIds)) {
											foreach ($followUserIds as $userId) {
												array_push($tagFollowId, $userId['id']);
											}
										}

									}
								}

							}
						}
					}

					if (!empty($tagFollowId)) {
						\Yii::$app->queue->push(new WorkTagFollowUserJob(
							['followIds' => $tagFollowId]
						));
					}
				}
			}

			return $workGroup->id;
		}

		/**
		 * @param $id
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function syncWxGroup ($id)
		{
			try {
				$workGroup = static::findOne($id);
				$corp_id   = $workGroup->corp_id;
				$work_tag  = WorkTag::find()->andWhere(['corp_id' => $corp_id, 'is_del' => 0, 'type' => 0, 'group_id' => $workGroup->id])->andWhere(['<>', 'tagid', '']);
				$work_tag  = $work_tag->asArray()->all();
				$tag_ids   = [];
				if (!empty($work_tag)) {
					$tag_ids = array_column($work_tag, 'tagid');
				}
				$workApi = '';
				try {
					$workApi = WorkUtils::getWorkApi($corp_id, WorkUtils::EXTERNAL_API);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'syncWxGroup-getMessage');
				}
				if (!empty($workApi) && !empty($tag_ids)) {
					$workApi->ECDelCorpTag($tag_ids);
				}

				return true;
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '48002') !== false) {
					$message = '标签API接口无权限调用';
				}
				\Yii::error($e->getMessage(), 'syncWxGroup');
				throw new InvalidDataException($message);
			}
		}

		/**
		 * @param $id
		 * @param $type
		 * @param $work_group
		 *
		 * @return bool
		 *
		 */
		public static function updateGroupId ($id, $type, $work_group)
		{
			$workTag = WorkTag::find()->andWhere(['group_id' => $id])->all();
			if (!empty($workTag)) {
				$tagFollowId = [];
				$group = WorkTagGroup::findOne(['group_name' => "未分组", 'type' => $type, 'corp_id' => $work_group->corp_id]);
				foreach ($workTag as $tag) {
					try {
						$tag->group_id = $group->id;
						$tag->save();

						if (empty($type)) {
							$workApi = '';
							try {
								$workApi       = WorkUtils::getWorkApi($group->corp_id, WorkUtils::EXTERNAL_API);
								$workClassName = get_class($workApi);
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'updateGroupId-getMessage');
							}
							if (!empty($workApi) && $workClassName == 'dovechen\yii2\weWork\Work') {
								$etcTag['group_name'] = strval($group->group_name);
								$tag_new              = [
									'name' => $tag->tagname,
								];
								$etcTag['tag']        = [$tag_new];
								$ECTagGroup           = ExternalContactTagGroup::parseFromArray($etcTag);
								$data                 = $workApi->ECAddCorpTag($ECTagGroup);
								$tag_data             = SUtils::Object2Array($data);
								$tag->tagid           = $tag_data['tag'][0]['id'];
								$group->group_id      = $tag_data['group_id'];
								$group->save();
								$tag->save();

								$followUserIds = WorkTagFollowUser::find()->where(['tag_id' => $tag->id, 'status' => 1])->asArray()->all();
								if (!empty($followUserIds)) {
									foreach ($followUserIds as $userId) {
										array_push($tagFollowId, $userId['id']);
									}
								}

							}

						}


					} catch (\Exception $e) {
						$message = $e->getMessage();
						if (strpos($message, '48002') !== false) {
							$message = '标签API接口无权限调用';
						}
						\Yii::error($e->getMessage(), 'updateGroupId');
						throw new InvalidDataException($message);
					}

				}
				if (!empty($tagFollowId)) {
					\Yii::$app->queue->push(new WorkTagFollowUserJob(
						['followIds' => $tagFollowId]
					));
				}
			}

			return true;
		}

		/**
		 * 获取分组标签数据
		 *
		 * @param $corp_id 企业唯一标志
		 * @param $type    0客户管理1通讯录
		 * @param $isNotAdd  是否不显示空分组，1是、0否
		 * @param $external_userid  外部联系人
		 *
		 * @return array
		 *
		 */
		public static function groupTagData ($corp_id, $type = 0, $isNotAdd = 0,$external_userid = '')
		{
			$groupData = [];
			$groupList = static::find()->where(['corp_id' => $corp_id, 'type' => $type])->orderBy('sort')->all();
			/** @var WorkTagGroup $group * */
			foreach ($groupList as $group) {
				$groupData[$group->id] = ['id' => $group->id, 'name' => $group->group_name, 'data' => []];
			}
			$tagList = WorkTagGroup::find()->alias('wg')->leftJoin('{{%work_tag}} as wt', '`wg`.`id` = `wt`.`group_id`')->where(['wt.corp_id' => $corp_id, 'wt.type' => $type, 'wt.is_del' => 0])->select('wt.id,wt.tagname,wt.group_id');
			$tagList = $tagList->asArray()->all();

			/** @var WorkTag $tag * */
			foreach ($tagList as $tag) {
				if (!empty($tag['group_id'])) {
					$groupData[$tag['group_id']]['data'][] = ['id' => $tag['id'], 'tagname' => $tag['tagname']];
				}
			}
			if (!empty($external_userid)) {
				$extTags = WorkTagFollowUser::find()->alias("a")
					->leftJoin("{{%work_external_contact_follow_user}} as b", "a.follow_user_id = b.id")
					->leftJoin("{{%work_external_contact}} as c", "b.external_userid = c.id")
					->leftJoin("{{%work_tag}} as d", "a.tag_id = d.id")
					->where(["a.corp_id" => $corp_id, "a.external_userid" => $external_userid, "c.is_del" => 0])
					->select("c.tagname,c.id")->asArray()->all();
			}
			if (empty($isNotAdd)) {
				$data = array_values($groupData);
			} else {
				$data = [];
				foreach ($groupData as $groupInfo) {
					if (!empty($groupInfo['data'])) {
						$data[] = $groupInfo;
					}
				}
			}
			return ["error"=>0,"data" => $data, "external_tags" => empty($extTags) ? '' : $extTags];
		}
	}
