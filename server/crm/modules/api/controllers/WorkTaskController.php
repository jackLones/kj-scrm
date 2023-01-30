<?php
	/**
	 * Create by PhpStorm
	 * User: beenlee
	 * Date: 2020/12/07
	 * Time: 01:14
	 */

	namespace app\modules\api\controllers;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\models\AuthoritySubUserDetail;
	use app\models\CustomField;
	use app\models\SubUser;
	use app\models\WorkCorp;
	use app\models\WorkDepartment;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkSop;
	use app\models\WorkTag;
	use app\models\WorkTagGroup;
	use app\models\WorkTaskTag;
	use app\models\WorkUser;
	use app\modules\api\components\WorkBaseController;
	use app\queue\BatchChangeTagsJob;
	use app\queue\ChangeRuleAndChangeTagsJob;
	use app\queue\CreateRuleAndChangeTagsJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use dovechen\yii2\weWork\Work;
	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\web\MethodNotAllowedHttpException;

	class WorkTaskController extends WorkBaseController
	{

		/**
		 * showdoc
		 *
		 * @catalog          数据接口/api/work-task/
		 * @title            任务标签列表
		 * @description      任务标签列表
		 * @method   post
		 * @url  http://{host_name}/api/work-task/task-tags
		 *
		 * @param corp_id 必选 string 企业的唯一ID
		 * @param task_id 可选 string 任务ID
		 * @param type 必选 int 0都显示，1仅任务标签数据，2仅最近使用
		 *
		 * @return           {"error":0,"data":{"taskTags":[{"id":1,"task_tag_id":1,"tag_id":1522,"tagname":"跟人沾边的事儿你是一点不干","condition":{"aaa":"111"},"create_time":"2020-12-18 17:08:40","update_time":"2020-12-18 17:17:10"}],"usingLatest":[{"id":1,"task_tag_id":1,"tagname":"跟人沾边的事儿你是一点不干"},{"id":1,"task_tag_id":1,"tagname":"跟人沾边的事儿你是一点不干"}]}}
		 *
		 * @return_param     error int 状态码
		 * @return_param     data array 结果数据
		 * @return_param     taskTags array 任务标签数据
		 * @return_param     id int 任务标签id
		 * @return_param     task_tag_id int 任务标签id
		 * @return_param     tag_id  int 标签id
		 * @return_param     tagname string 标签名称
		 * @return_param     condition array 筛选条件
		 * @return_param     usingLatest array 最近使用(参数参考taskTags)
		 *
		 * @remark           Create by PhpStorm. User: beenlee. Date: 2020-12-18 16:12
		 * @number           0
		 *
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionTaskTags ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			if (empty($this->corp)) {
				throw new MethodNotAllowedHttpException('参数不正确！');
			}
			$corp_id = $this->corp->id;
			$type    = \Yii::$app->request->post('type', 0);
			$task_id = \Yii::$app->request->post('task_id', 0);

			$taskTagsInfo = [];
			if (in_array($type, [0, 1], true)) {
				$taskTagsData = WorkTaskTag::findAll(['corp_id' => $corp_id]);
				if ($taskTagsData) {
					foreach ($taskTagsData as $taskTags) {
						$taskTagsInfo[] = $taskTags->dumpData();
					}
				}
			}

			$usingLatest = [];

			return [
				'taskTags'    => $taskTagsInfo,
				'usingLatest' => $usingLatest
			];
		}

		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/work-task/
		 * @title           任务标签添加修改
		 * @description     任务标签添加修改
		 * @method   post
		 * @url  http://{host_name}/api/work-task/task-tag-add
		 *
		 * @param corp_id 必选 string 企业的唯一ID
		 * @param task_tag_id 可选 任务标签ID
		 * @param name 必选 string 标签名
		 * @param condition 必选 array 筛选条件
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: beenlee. Date: 2020-12-18 16:13
		 * @number          0
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\Exception
		 * @throws \yii\web\MethodNotAllowedHttpException
		 */
		public function actionTaskTagAdd ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$task_tag_id = \Yii::$app->request->post('task_tag_id', 0);
			$name        = \Yii::$app->request->post('name');
			$condition   = \Yii::$app->request->post('condition');
			$type        = 0;

			if (empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			if ($this->corp->corp_type !== 'verified') {
				throw new InvalidParameterException('当前企业号未认证！');
			}

			if ($task_tag_id <= 0) {
				if (empty($name)) {
					throw new InvalidParameterException('标签名称不能为空！');
				}

				$len = mb_strlen($name, "utf-8");
				if ($len > 15) {
					throw new InvalidParameterException('名称不能超过15个字');
				}

				$tagName = WorkTag::find()->andWhere(['tagname' => $name, 'is_del' => 0, 'type' => $type, 'corp_id' => $this->corp['id']])->one();
				if (!empty($tagName)) {
					throw new InvalidParameterException('标签名称不能重复');
				}
			}

			$group_id = 0;
			$tag_id   = 0;
			if ($task_tag_id > 0) {
				$taskTagInfo = WorkTaskTag::findOne(['id' => $task_tag_id]);
				if ($taskTagInfo) {
					$group_id = $taskTagInfo->tag->group_id;
					$tag_id   = $taskTagInfo->tag_id;
				}
			}

			$transaction = \Yii::$app->db->beginTransaction();
			try {
				if ($task_tag_id <= 0) {
					if (empty($group_id)) {
						//判断并创建任务标签分组 $group_id
						$tagGroupInfo = WorkTagGroup::findOne(['type' => 0, 'corp_id' => $this->corp->id, 'group_name' => "任务分组"]);
						if ($tagGroupInfo === NULL) {
							$group_id = WorkTagGroup::add(0, $this->corp->id, '任务分组', 0, [$name], true);
						} else {
							$group_id = $tagGroupInfo->id;
						}
					}

					if ($tag_id <= 0) {
						WorkTag::add($tag_id, $this->corp['id'], [$name], $type, $group_id);
						if (empty($tag_id)) {
							$newTagData = WorkTag::find()->andWhere(['tagname' => $name, 'is_del' => 0, 'type' => 0, 'corp_id' => $this->corp['id']])->one();
							if (!$newTagData) {
								$transaction->rollBack();
								throw new InvalidParameterException('新建标签数据错误！');
							}

							$tag_id = $newTagData->id;
						}
					}
				}

				$is_add = 0;
				if (!isset($taskTagInfo)) {
					$taskTagInfo              = new WorkTaskTag();
					$taskTagInfo->corp_id     = $this->corp->id;
					$taskTagInfo->tag_id      = $tag_id;
					$taskTagInfo->create_time = DateUtil::getCurrentTime();
					$taskTagInfo->tagname     = $name;
					$is_add                   = 1;
				}

				$taskTagInfo->condition   = !empty($condition) ? json_encode($condition, JSON_UNESCAPED_UNICODE) : '';
				$taskTagInfo->update_time = DateUtil::getCurrentTime();

				if (!$taskTagInfo->validate() || !$taskTagInfo->save()) {
					$transaction->rollBack();
					throw new InvalidDataException(SUtils::modelError($taskTagInfo));
				}
				$transaction->commit();

				if ($is_add > 0) {
					//beenlee 创建规则标签  队列给符合用户打标签
					$dateJob             = [];
					$dateJob['type']     = 0;
					$dateJob['corp_id']  = $this->corp->id;
					$dateJob['uid']      = $this->user->uid;
					$dateJob['param_id'] = $taskTagInfo->id;
					\Yii::$app->queue->push(new CreateRuleAndChangeTagsJob($dateJob));
				} else {
					//beenlee 修改规则标签  队列给符合用户打标签
					$dateJob             = [];
					$dateJob['type']     = 1;
					$dateJob['corp_id']  = $this->corp->id;
					$dateJob['uid']      = $this->user->uid;
					$dateJob['param_id'] = $taskTagInfo->id;
					\Yii::$app->queue->push(new ChangeRuleAndChangeTagsJob($dateJob));
				}

			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/work-task/
		 * @title           任务标签添加删除
		 * @description     任务标签添加删除
		 * @method   post
		 * @url  http://{host_name}/api/work-task/task-tag-del
		 *
		 * @param corp_id 必选 string 企业的唯一ID
		 * @param task_tag_id 可选 任务标签ID
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: beenlee. Date: 2020-12-20 16:13
		 * @number          0
		 *
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\web\MethodNotAllowedHttpException
		 */
		public function actionTaskTagDel ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$task_tag_id = \Yii::$app->request->post('task_tag_id', 0);
			if (empty($task_tag_id) || empty($this->corp)) {
				throw new InvalidDataException('缺少必要参数！');
			}
			$corp_id         = $this->corp->id;
			$workTaskTagInfo = WorkTaskTag::findOne($task_tag_id);
			if ($workTaskTagInfo === NULL) {
				throw new InvalidDataException('此标签已不存在！');
			}
			//已被使用不允许删除
			/*$hasRelations = WorkTaskTagRelations::findOne(['work_tag_id' => $task_tag_id, 'corp_id' => $corp_id, 'status' => 1]);
			if ($hasRelations) {
				throw new InvalidDataException('此标签不可删除！');
			}*/
			$hasRelations = WorkSop::findOne(['corp_id' => $corp_id, 'type' => 2, 'is_all' => 0, 'task_id' => $task_tag_id, 'is_del' => 0]);
			if ($hasRelations) {
				throw new InvalidDataException('此标签不可删除！');
			}

			$workTag = WorkTag::findOne($workTaskTagInfo['tag_id']);
			if ($workTag) {
				$workTag->is_del = 1;
				$workTag->save();
				//同步到企业微信
				if (!empty($workTag->tagid)) {
					WorkTag::deleteTag($workTag->corp_id, $workTag->type, $workTag->tagid);
				}
			}

			//beenlee 删除规则 进入队列取消对应用户标签
			$dateJob             = [];
			$dateJob['type']     = 3;
			$dateJob['corp_id']  = $this->corp->id;
			$dateJob['uid']      = $this->user->uid;
			$dateJob['param_id'] = $task_tag_id;
			\Yii::$app->queue->push(new ChangeRuleAndChangeTagsJob($dateJob));

			//WorkTaskTagRelations::deleteAll(['work_tag_id' => $task_tag_id, 'corp_id' => $corp_id]);
			WorkTaskTag::deleteAll(['id' => $task_tag_id]);

			return true;
		}

		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/work-task/
		 * @title           系统任务客户筛选
		 * @description     系统任务客户筛选
		 * @method   post
		 * @url  http://{host_name}/api/work-task/task-tag-members
		 *
		 * @param corp_id 必选 string 企业唯一标志
		 * @param suite_id 必选 int 应用id
		 * @param isMasterAccount 必选 int 帐户类型：1主账户2子账户3员工
		 * @param uid 必选 int 主账户id
		 * @param sub_id 可选 int 子账户id 子账户登录时必填
		 * @param user_ids 可选 array 成员id
		 * @param sex 可选 string 性别-1全部1男2女3未知
		 * @param work 可选 string 行业
		 * @param location 可选 string 区域-省-市
		 * @param follow_id 可选 int 跟进状态id
		 * @param tag_ids 可选 string 标签值（多标签用,分开）
		 * @param sign_id 可选 int 绑定客户
		 * @param is_fans 可选 int 是否是粉丝1是2否
		 *
		 * @return          {"error":0,"data":{"info":[{"key":"4114"}],"count":1,"real_num":0}}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: beenlee. Date: 2020/12/22 17:06
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 * @throws \yii\db\Exception|\app\components\InvalidDataException
		 */
		public function actionTaskTagMembers ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$uid             = \Yii::$app->request->post('uid', 0);
			$sub_id          = \Yii::$app->request->post('sub_id', 0);
			$isMasterAccount = \Yii::$app->request->post('isMasterAccount', 1);
			if (empty($this->corp) || empty($uid)) {
				throw new InvalidDataException('缺少必要参数！');
			}
			$corp_id = $this->corp->id;

			$name        = \Yii::$app->request->post('name', '');
			$phone       = \Yii::$app->request->post('phone', '');
			$name        = trim($name);
			$phone       = trim($phone);
			$group_id    = \Yii::$app->request->post('group_id', '');
			$tag_type    = \Yii::$app->request->post('tag_type', 1);
			$belong_id   = \Yii::$app->request->post('belong_id', 0);
			$start_time  = \Yii::$app->request->post('start_time');
			$end_time    = \Yii::$app->request->post('end_time');
			$correctness = \Yii::$app->request->post('correctness', 2);//1全部 2条件
			$update_time = \Yii::$app->request->post('update_time');
			$follow_num1 = \Yii::$app->request->post('follow_num1');
			$follow_num2 = \Yii::$app->request->post('follow_num2');
			$chat_time   = \Yii::$app->request->post('chat_time');
			$chat_id     = \Yii::$app->request->post('chat_id');
			$is_moment   = \Yii::$app->request->post('is_moment', 0);

			$user_ids = \Yii::$app->request->post('user_ids');
			$sex      = \Yii::$app->request->post('sex', '-1');
			$work     = \Yii::$app->request->post('work', '');
			$location = \Yii::$app->request->post('location', NULL);
			if ($location && is_array($location) && count($location) === 2) {
				[$province, $city] = $location;
				if ($city === '全部') {
					$city = '';
				}
			} else {
				$province = '';
				$city     = '';
			}
			$sign_id   = \Yii::$app->request->post('signId', NULL);
			$follow_id = \Yii::$app->request->post('follow_status', '-1');
			$is_fans   = \Yii::$app->request->post('isPublic');
			$tag_ids   = \Yii::$app->request->post('tag_arr', []);

			$fieldData = [];
			$postDate  = \Yii::$app->request->post();
			if (!empty($postDate) && is_array($postDate)) {
				$postKey   = array_keys($postDate);
				$fieldInfo = CustomField::findAll($postKey);
				if ($fieldInfo) {
					foreach ($fieldInfo as $field) {
						if ($field['key'] === 'work') {
							$work = $postDate[$field['id']];
						} else /*if (is_array($postDate[$field['id']])) {
							foreach ($postDate[$field['id']] as $fieldDate) {
								$fieldData[] = [
									'field' => $field['id'],
									'match' => $fieldDate,
								];
							}
						} else */ {
							if (!empty($postDate[$field['id']])) {
								$fieldData[] = [    
									'field' => $field['id'],
									'match' => $postDate[$field['id']],
								];
							}
						}
					}
				}
			}

			if (!empty($user_ids)) {
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 1, true);
				$user_ids = empty($user_ids) ? [0] : $user_ids;
			}

			$data['corp_id']         = $this->corp['id'];
			$data['isMasterAccount'] = $isMasterAccount;
			$data['sub_id']          = $sub_id;
			$data['name']            = $name;
			$data['phone']           = $phone;
			$data['sex']             = $sex;
			$data['work']            = $work;
			$data['province']        = $province;
			$data['city']            = $city;
			$data['follow_status']   = -1;
			$data['follow_id']       = $follow_id;
			$data['fieldData']       = $fieldData;
			$data['tag_ids']         = implode(',', $tag_ids);
			$data['group_id']        = $group_id;
			$data['tag_type']        = $tag_type;
			$data['start_time']      = $start_time;
			$data['end_time']        = $end_time;
			$data['correctness']     = $correctness;
			$data['update_time']     = $update_time;
			$data['follow_num1']     = $follow_num1;
			$data['follow_num2']     = $follow_num2;
			$data['chat_time']       = $chat_time;
			$data['sign_id']         = $sign_id;
			$data['chat_id']         = $chat_id;
			$data['user_ids']        = $user_ids;
			$data['belong_id']       = $belong_id;
			$data['is_fans']         = $is_fans;
			$data['uid']             = $this->user->uid;
			$data['is_moment']       = $is_moment;
			$data['is_follow_full']  = 1;

			$info     = WorkExternalContactFollowUser::getData($data);
			$result   = $info['result'];
			$real_num = count(array_unique(array_column($result, 'key')));

			return [
				'info'     => $result,
				'count'    => count($result),
				'real_num' => $real_num,
			];
		}

	}