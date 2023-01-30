<?php
	/**
	 * Create by PhpStorm
	 * title: 智能推荐功能
	 * User: fulu
	 * Date: 2020/12/07
	 */

	namespace app\modules\api\controllers;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\models\Article;
	use app\models\Attachment;
	use app\models\AttachmentGroup;
	use app\models\RadarLink;
	use app\models\WorkMaterial;
	use app\models\WorkMsgAudit;
	use app\models\WorkMsgAuditInfo;
	use app\models\WorkMsgAuditUser;
	use app\models\WorkMsgKeywordAttachment;
	use app\models\WorkMsgKeywordAttachmentInfo;
	use app\models\WorkMsgKeywordTag;
	use app\models\WorkMsgKeywordUser;
	use app\models\WorkTag;
	use app\models\WorkUser;
	use app\models\WorkDepartment;
	use app\modules\api\components\WorkBaseController;
	use app\util\StringUtil;
	use app\util\SUtils;
	use yii\db\Expression;
	use yii\web\MethodNotAllowedHttpException;

	class WorkMsgKeywordAttachmentController extends WorkBaseController
	{
		/**
		 * @inheritDoc
		 *
		 * @param \yii\base\Action $action
		 *
		 * @return bool
		 *
		 * @throws \app\components\InvalidParameterException
		 * @throws \yii\web\BadRequestHttpException
		 */
		public function beforeAction ($action)
		{
			return parent::beforeAction($action);
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           推荐规则列表
		 * @description     推荐规则列表
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-list
		 *
		 * @param corp_id    必选 string 企业唯一标志
		 * @param keyword    可选 string 关键词
		 * @param page       可选 int 页码
		 * @param page_size  可选 int 每页数据量，默认15
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 数据条数
		 * @return_param    list array 数据列表
		 * @return_param    list.id int 规则id
		 * @return_param    list.keywords string 关键词
		 * @return_param    list.type int 推送类型1不限制2用户标签
		 * @return_param    list.attachment_num array 素材分类数量
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$keyword  = \Yii::$app->request->post('keyword', '');
			$page     = \Yii::$app->request->post('page', 1);
			$pageSize = \Yii::$app->request->post('page_size', 15);
			$keyword  = trim($keyword);

			if (empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$offset = ($page - 1) * $pageSize;

			$keywordData = WorkMsgKeywordAttachment::find()->andWhere(['corp_id' => $this->corp['id'], 'is_del' => 0]);

			if (!empty($keyword) || $keyword === '0') {
				$keywordData = $keywordData->andWhere(['like', 'keywords', $keyword]);
			}

			$count = $keywordData->count();

			$keywordData = $keywordData->limit($pageSize)->offset($offset);
			$keywordData = $keywordData->select('`id`,`keywords`,`attachment_ids`,`type`')->orderBy(['id' => SORT_DESC])->asArray()->all();

			$result = [];
			foreach ($keywordData as $key => $val) {
				$keywordD             = [];
				$keywordD['id']       = $val['id'];
				$keywordD['keywords'] = explode(',', $val['keywords']);
				$keywordD['type']     = $val['type'];
				//标签及内容
				$attachment_num = [];
				if ($val['type'] == 2) {
					$keywordTag = WorkMsgKeywordTag::find()->where(['keyword_id' => $val['id'], 'is_del' => 0])->asArray()->all();
					foreach ($keywordTag as $tag) {
						$attachmentData = [];
						$tags           = explode(',', $tag['tags']);
						$tag_ids        = [];
						$tag_name       = [];
						$workTag        = WorkTag::find()->where(['id' => $tags, 'is_del' => 0])->asArray()->all();
						foreach ($workTag as $t) {
							$tag_ids[]  = $t['id'];
							$tag_name[] = $t['tagname'];
						}
						$attachmentData['tag_num']  = count($tag_ids);
						$attachmentData['tag_name'] = $tag_name;

						$attachmentInfo = WorkMsgKeywordAttachmentInfo::find()->where(['keyword_id' => $val['id'], 'keyword_tag_id' => $tag['id'], 'status' => 1])->select('type')->asArray()->all();
						foreach ($attachmentInfo as $attach) {
							if (isset($attachmentData[$attach['type']])) {
								$attachmentData[$attach['type']] += 1;
							} else {
								$attachmentData[$attach['type']] = 1;
							}
						}

						$attachment_num[] = $attachmentData;
					}
				} else {
					$attachmentData            = [];
					$attachmentData['tag_num'] = 0;
					$attachmentInfo            = WorkMsgKeywordAttachmentInfo::find()->where(['keyword_id' => $val['id'], 'keyword_tag_id' => 0, 'status' => 1])->select('type')->asArray()->all();
					foreach ($attachmentInfo as $attach) {
						if (isset($attachmentData[$attach['type']])) {
							$attachmentData[$attach['type']] += 1;
						} else {
							$attachmentData[$attach['type']] = 1;
						}
					}
					$attachment_num[] = $attachmentData;
				}
				$keywordD['attachment_num'] = $attachment_num;

				$result[] = $keywordD;
			}

			return [
				'count' => $count,
				'list'  => $result
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           设置智能推荐规则
		 * @description     设置智能推荐规则
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-set
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param id                 可选 int 规则id(修改时)
		 * @param keyword            必选 array 关键词集合
		 * @param type               必选 int 推送类型1不限制2用户标签
		 * @param list               必选 array 数据
		 * @param list.keyword_tag_id  可选 int 关键词关联标签id(修改时)
		 * @param list.tag_ids         可选 array 标签id集合
		 * @param list.msgData         必选 array 内容数据
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordSet ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}

			$id      = \Yii::$app->request->post('id', 0);
			$keyword = \Yii::$app->request->post('keyword', []);
			$type    = \Yii::$app->request->post('type');
			$list    = \Yii::$app->request->post('list', []);

			if (empty($this->corp) || empty($list)) {
				throw new InvalidParameterException('缺少必要参数！');
			}
			if (empty($keyword)) {
				throw new InvalidParameterException('关键词不能为空！');
			}
			if (!in_array($type, [1, 2])) {
				throw new InvalidParameterException('推送类型数据错误！');
			}

			$data            = [];
			$data['id']      = $id;
			$data['keyword'] = $keyword;
			$data['type']    = $type;
			$data['list']    = $list;

			WorkMsgKeywordAttachment::setKeyword($this->corp->id, $data);

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           智能推荐规则删除
		 * @description     智能推荐规则删除
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-del
		 *
		 * @param id             必选 int 规则id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordDel ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}

			$id = \Yii::$app->request->post('id');

			if (empty($id)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			$keyword = WorkMsgKeywordAttachment::findOne($id);
			if (empty($keyword)) {
				throw new InvalidParameterException('规则参数错误！');
			}

			$keyword->is_del = 1;

			if (!$keyword->save()) {
				throw new InvalidParameterException(SUtils::modelError($keyword));
			}

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           推荐规则详情
		 * @description     推荐规则详情
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-detail
		 *
		 * @param corp_id    必选 string 企业唯一标志
		 * @param id         必选 string 规则id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    data.id int 规则id
		 * @return_param    data.keyword string 关键词
		 * @return_param    data.type int 推送类型1不限制2用户标签
		 * @return_param    data.attachmentData array 素材信息
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordDetail ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$id = \Yii::$app->request->post('id');

			if (empty($this->corp) || empty($id)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			$keyword = WorkMsgKeywordAttachment::findOne($id);

			$result            = [];
			$result['id']      = $keyword->id;
			$result['keyword'] = explode(',', $keyword->keywords);
			$result['type']    = $keyword->type;

			//内容
			$attachmentData = [];
			$keywordTagD    = [0];//默认无标签限制
			$keywordTagIdD  = [];
			if ($keyword->type == 2) {
				$keywordTag  = WorkMsgKeywordTag::find()->where(['keyword_id' => $keyword->id, 'is_del' => 0])->all();
				$keywordTagD = [];
				foreach ($keywordTag as $tag) {
					$keywordTagD[]           = $tag->id;
					$keywordTagIdD[$tag->id] = explode(',', $tag->tags);
				}
			}
			foreach ($keywordTagD as $keywordTagId) {
				$attachmentInfo = WorkMsgKeywordAttachmentInfo::find()->where(['keyword_id' => $keyword->id, 'keyword_tag_id' => $keywordTagId, 'status' => 1])->asArray()->all();
				$replyList      = [];
				$typeNum        = 0;
				foreach ($attachmentInfo as $rv) {
					if ($rv['type'] == 5) {
						if (strpos($rv['cover_url'], 'http') === false) {
							$rv['cover_url'] = \Yii::$app->params['site_url'] . $rv['cover_url'];
						}
						$material_id = !empty($rv['attachment_id']) ? $rv['attachment_id'] : 0;
						$group_id    = "";
						$attach_id   = "";
						if (empty($rv['is_use'])) {
							$addType = 0;
							$is_sync = 0;
						} else {
							$addType = 1;
							$is_sync = (int) $rv['is_sync'];
							if (!empty($is_sync)) {
								$attach_id  = $rv['attach_id'];
								$attachment = Attachment::findOne($rv['attach_id']);
								$group_id   = (string) $attachment->group_id;
							}
						}
						//雷达图
						$temp          = Attachment::findOne(['id' => $rv['attachment_id']]);
						$radarInfo     = RadarLink::findOne(['associat_type' => 0, 'associat_id' => $rv['attachment_id']]);
						$qy_local_path = '';
						if ($temp->file_type == 4 || (isset($radarInfo['status']) && $radarInfo['status'] > 0)) {
							if ($temp->file_type == 1) {
								$qy_local_path = '/static/image/image.png';
							} elseif ($temp->file_type == 2) {
								$qy_local_path = '/static/image/audio.png';
							} elseif ($temp->file_type == 3) {
								$qy_local_path = '/static/image/video.png';
							} elseif ($temp->file_type == 5) {
								$extension = Attachment::getExtension($temp->file_content_type, $temp->file_name);
								if (!empty($extension)) {
									$qy_local_path = '/static/image/' . $extension . '.png';
								} else {
									$qy_local_path = '/static/image/file.png';
								}
							} else {
								$qy_local_path = $temp->local_path;
							}
							$qy_local_path = !empty($temp->qy_local_path) ? $temp->qy_local_path : $qy_local_path;
						}
						$qy_local_path = !empty($qy_local_path) ? \Yii::$app->params['site_url'] . $qy_local_path : $rv['cover_url'];

						$replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int) $rv['type'], 'addType' => $addType, 'inputTitle' => $rv['title'], 'digest' => $rv['digest'], 'content_source_url' => $rv['content_url'], 'material_id' => $material_id, 'local_path' => ['img' => $qy_local_path, 'audio' => ''], 'is_sync' => $is_sync, 'group_id' => $group_id, 'attach_id' => $attach_id];
					} elseif ($rv['type'] == 1) {
						$replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int) $rv['type'], 'file_name' => '', 'material_id' => $rv['attachment_id'], 'is_user' => $rv['is_use'], 'local_path' => ['img' => '', 'audio' => ''], 'sketchList' => [], 'textAreaValueHeader' => rawurldecode($rv['content'])];
						$typeNum++;
					} elseif ($rv['type'] == 6) {
						if (!empty($rv['attachment_id'])) {
							$temp      = Attachment::findOne(['id' => $rv['attachment_id']]);
							$cover_url = !empty($temp->local_path) ? $temp->local_path : '';
							if (strpos($cover_url, 'http') === false) {
								$cover_url = \Yii::$app->params['site_url'] . $cover_url;
							}
						} else {
							$cover_url = '';
						}
						$group_id  = "";
						$attach_id = "";
						if (empty($rv['is_use'])) {
							$is_sync = 0;
						} else {
							$is_sync = (int) $rv['is_sync'];
							if (!empty($is_sync) && $rv['attach_id']) {
								$attach_id  = $rv['attach_id'];
								$attachment = Attachment::findOne($rv['attach_id']);
								$group_id   = (string)$attachment->group_id;
							}
						}
						$replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int) $rv['type'], 'is_user' => $rv['is_use'], 'is_sync' => $is_sync, 'group_id' => $group_id, 'attach_id' => $attach_id, 'appid' => $rv['appid'], 'pagepath' => $rv['pagepath'], 'file_name' => $rv['title'], 'material_id' => $rv['attachment_id'], 'local_path' => $cover_url, 'sketchList' => [], 'textAreaValueHeader' => ''];
						$typeNum++;
					} else {
						$extension = '';
						if (!empty($rv['attachment_id'])) {
							$temp      = Attachment::findOne(['id' => $rv['attachment_id']]);
							$cover_url = !empty($temp->local_path) ? $temp->local_path : '';
							if (strpos($cover_url, 'http') === false) {
								$cover_url = \Yii::$app->params['site_url'] . $cover_url;
							}
							$file_name = $temp->file_name;

							if ($temp->file_type == 5) {
								$extension = Attachment::getExtension($temp->file_content_type, $temp->file_name);
							}
						} else {
							$cover_url = '';
							$file_name = '';
						}

						if ($rv['type'] == 2) {
							$local_path = ['img' => $cover_url, 'audio' => ''];
						} else {
							$local_path = ['img' => '', 'audio' => $cover_url];
						}
						$replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int) $rv['type'], 'file_name' => $file_name, 'material_id' => $rv['attachment_id'], 'local_path' => $local_path, 'sketchList' => [], 'textAreaValueHeader' => '', 'extension' => $extension];
						$typeNum++;
					}
				}

				$attachmentD                   = [];
				$attachmentD['keyword_tag_id'] = $keywordTagId;
				$tags                          = isset($keywordTagIdD[$keywordTagId]) ? $keywordTagIdD[$keywordTagId] : [];
				$tag_ids                       = [];
				$tag_name                      = [];
				if ($tags) {
					$workTag = WorkTag::find()->where(['id' => $tags, 'is_del' => 0])->asArray()->all();
					foreach ($workTag as $t) {
						$tag_ids[]  = $t['id'];
						$tag_name[] = $t['tagname'];
					}
				}
				$attachmentD['tag_ids']     = $tag_ids;
				$attachmentD['tag_name']    = $tag_name;
				$attachmentD['attach_list'] = array_values($replyList);
				$attachmentData[]           = $attachmentD;
			}

			$result['attachmentData'] = $attachmentData;

			return $result;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           推荐成员列表
		 * @description     推荐成员列表
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-user-list
		 *
		 * @param corp_id    必选 string 企业唯一标志
		 * @param status     可选 int 状态：1开启2关闭
		 * @param user_ids   可选 array 员工id集合
		 * @param party      可选 array 部门id集合
		 * @param page       可选 int 页码
		 * @param page_size  可选 int 每页数据量，默认15
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 数据条数
		 * @return_param    all_user array 全部成员规则id
		 * @return_param    list array 数据列表
		 * @return_param    list.id int 成员规则id
		 * @return_param    list.keyword_status int 状态：1开启2关闭
		 * @return_param    list.user_name string 员工姓名
		 * @return_param    list.avatar string 员工头像
		 * @return_param    list.gender int 员工性别
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordUserList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$status   = \Yii::$app->request->post('status', 0);
			$user_ids = \Yii::$app->request->post('user_ids', []);
			$party    = \Yii::$app->request->post('party', []);
			$page     = \Yii::$app->request->post('page', 1);
			$pageSize = \Yii::$app->request->post('page_size', 15);

			if (empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$audit = WorkMsgAudit::findOne(['corp_id' => $this->corp->id, 'status' => 1]);

			if (empty($audit)) {
				return [
					'count'    => 0,
					'all_user' => [],
					'list'     => []
				];
			}

			$offset = ($page - 1) * $pageSize;

			$keywordUser = WorkMsgAuditUser::find()->where(['audit_id' => $audit->id, 'status' => 1]);

			if ($status) {
				$keywordUser = $keywordUser->andWhere(['keyword_status' => $status]);
			} else {
				$keywordUser = $keywordUser->andWhere(['keyword_status' => [1, 2]]);
			}

			if ($party) {
				$departmentUser = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $party, [], 0, true);
				$user_ids       = array_merge($user_ids, $departmentUser);
			}
			if ($user_ids) {
				$user_ids    = array_unique($user_ids);
				$keywordUser = $keywordUser->andWhere(['user_id' => $user_ids]);
			}

			$countData = $keywordUser->all();
			$count     = count($countData);

			$all_user = [];
			foreach ($countData as $k => $v) {
				array_push($all_user, $v->id);
			}

			$keywordUser = $keywordUser->limit($pageSize)->offset($offset)->asArray()->all();

			$result = [];
			foreach ($keywordUser as $val) {
				$keywordD                   = [];
				$keywordD['id']             = $val['id'];
				$keywordD['user_id']        = $val['user_id'];
				$keywordD['keyword_status'] = $val['keyword_status'];
				$workUser                   = WorkUser::findOne($val['user_id']);
				$departName                 = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
				$keywordD['user_name']      = $workUser->name . '--' . $departName;
				$keywordD['avatar']         = $workUser->avatar;
				$keywordD['gender']         = $workUser->gender;

				$result[] = $keywordD;
			}

			return [
				'count'    => $count,
				'all_user' => $all_user,
				'list'     => $result
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           设置推荐成员
		 * @description     设置推荐成员
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-user-set
		 *
		 * @param corp_id             必选 string 企业唯一标志
		 * @param user_ids            必选 array 成员id集合
		 * @param status              必选 int 状态：1开启2关闭
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordUserSet ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}

			$party    = \Yii::$app->request->post('party', []);
			$user_ids = \Yii::$app->request->post('user_ids');
			$status   = \Yii::$app->request->post('status');

			if ($party) {
				$audit          = WorkMsgAudit::findOne(['corp_id' => $this->corp->id, 'status' => 1]);
				$keywordUser    = WorkMsgAuditUser::find()->where(['audit_id' => $audit->id, 'status' => 1])->asArray()->all();
				$auditUser      = array_column($keywordUser, 'user_id');
				$departmentUser = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $party, [], 0, true, 0, $auditUser);
				$user_ids       = array_merge($user_ids, $departmentUser);
			}
			if ($user_ids) {
				$user_ids = array_unique($user_ids);
			}

			if (empty($this->corp) || empty($status)) {
				throw new InvalidParameterException('缺少必要参数！');
			}
			if (empty($user_ids)){
				throw new InvalidParameterException('没有可用成员！');
			}
			if (!in_array($status, [1, 2])) {
				throw new InvalidParameterException('状态参数错误！');
			}

			$audit = WorkMsgAudit::findOne(['corp_id' => $this->corp->id, 'status' => 1]);

			if (empty($audit)) {
				throw new InvalidParameterException('会话存档未开启！');
			}

			WorkMsgAuditUser::updateAll(['keyword_status' => $status], ['audit_id' => $audit->id, 'status' => 1, 'user_id' => $user_ids]);

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           设置推荐成员状态
		 * @description     设置推荐成员状态
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-user-status-set
		 *
		 * @param corp_id             必选 string 企业唯一标志
		 * @param rule_ids            必选 array 成员规则id集合
		 * @param status              必选 int 状态：0删除1开启2关闭
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordUserStatusSet ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}

			$rule_ids = \Yii::$app->request->post('rule_ids');
			$status   = \Yii::$app->request->post('status');

			if (empty($this->corp) || empty($rule_ids)) {
				throw new InvalidParameterException('缺少必要参数！');
			}
			if (!in_array($status, [0, 1, 2])) {
				throw new InvalidParameterException('状态参数错误！');
			}

			WorkMsgAuditUser::updateAll(['keyword_status' => $status], ['id' => $rule_ids]);

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           H5客户关键词列表
		 * @description     H5客户关键词列表
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/custom-keyword-list
		 *
		 * @param corp_id             必选 string 企业唯一标志
		 * @param external_userid     必选 string 客户userid
		 * @param userid              必选 string 员工userid
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    list array 数据列表
		 * @return_param    list.keyword_id int 数据id
		 * @return_param    list.keyword string 关键词
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionCustomKeywordList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$external_userid = \Yii::$app->request->post('external_userid', '');
			$userid          = \Yii::$app->request->post('userid', '');

			if (empty($this->corp) || empty($external_userid) || empty($userid)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			$audit = WorkMsgAudit::findOne(['corp_id' => $this->corp->id, 'status' => 1]);

			if (empty($audit)) {
				return ['isPermission' => 0, 'list' => []];
			}

			$auditUser = WorkMsgAuditUser::findOne(['audit_id' => $audit->id, 'userid' => $userid, 'status' => 1, 'keyword_status' => 1]);

			if (empty($auditUser)) {
				return ['isPermission' => 0, 'list' => []];
			}

			$stime       = time() - WorkMsgKeywordUser::KEYWORD_TIME;
			$keywordUser = WorkMsgKeywordUser::find()->alias('u');
			$keywordUser = $keywordUser->leftJoin('{{%work_msg_keyword_attachment}} a', 'u.keyword_id = a.id');
			$keywordUser = $keywordUser->where(['u.external_userid' => $external_userid, 'u.userid' => $userid, 'a.is_del' => 0])->andWhere(['>', 'u.time', $stime]);
			$keywordUser = $keywordUser->select('u.id keyword_user_id, u.keyword_id, u.keyword')->groupBy('u.keyword_id')->orderBy(['u.time' => SORT_DESC])->asArray()->all();
			if (empty($keywordUser)) {
				$keywordOne = WorkMsgKeywordUser::find()->where(['external_userid' => $external_userid, 'userid' => $userid])->select('id keyword_user_id, keyword_id, keyword')->orderBy(['id' => SORT_DESC])->asArray()->one();
				if ($keywordOne) {
					$keywordAttachment = WorkMsgKeywordAttachment::findOne(['id' => $keywordOne['keyword_id'], 'is_del' => 0]);
					if ($keywordAttachment) {
						$keywordUser[] = $keywordOne;
					}
				}
			}

			$keywordUser = !empty($keywordUser) ? $keywordUser : [];

			return [
				'isPermission' => 1,
				'list'         => $keywordUser
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-msg-keyword-attachment/
		 * @title           关键词素材列表
		 * @description     关键词素材列表
		 * @method   post
		 * @url  http://{host_name}/api/work-msg-keyword-attachment/keyword-attachment-list
		 *
		 * @param corp_id                 必选 string 企业唯一标志
		 * @param userid                  必选 string 员工userid
		 * @param keyword_user_id         必选 array 员工关键词id集合
		 * @param file_type               可选 int 素材类型
		 * @param page                    可选 int 页码
		 * @param pageSize                可选 int 每页数据量，默认15
		 *
		 * @return       {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    attachment_type array 附件类型集合
		 * @return_param    file_type string 附件类型
		 * @return_param    count string 数量
		 * @return_param    attachment array 数据
		 * @return_param    attachment.id string 附件id
		 * @return_param    attachment.group_id string 分组id
		 * @return_param    attachment.file_type string 附件类型
		 * @return_param    attachment.file_name string 附件名称
		 * @return_param    attachment.file_width string 附件宽度
		 * @return_param    attachment.file_height string 附件高度
		 * @return_param    attachment.local_path string 附件地址
		 * @return_param    attachment.jump_url string 跳转地址
		 * @return_param    attachment.content string 附件文本内容
		 *
		 * @remark        Create by PhpStorm. User: fulu. Date: 2020/12/07
		 * @number       0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionKeywordAttachmentList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}

			$keyword_user_id = \Yii::$app->request->post('keyword_user_id');
			$file_type       = \Yii::$app->request->post('file_type', '');//素材类型
			$userid          = \Yii::$app->request->post('userid');//企业成员userid
			$page            = \Yii::$app->request->post('page', 1);
			$pageSize        = \Yii::$app->request->post('pageSize', 10);

			if (empty($this->corp) || empty($keyword_user_id) || empty($userid)) {
				throw new InvalidDataException('参数不正确');
			}

			/*$corpId   = $this->corp->id;
			$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidDataException('参数不正确');
			}*/
			$keywordUser = WorkMsgKeywordUser::find()->where(['id' => $keyword_user_id])->asArray()->all();
			if (empty($keywordUser)) {
				throw new InvalidDataException('员工关键词参数不正确');
			}
			$keywordWhere = '';
			foreach ($keywordUser as $v) {
				if (!empty($v['keyword_tag_id'])) {
					$whereStr = '(keyword_id=' . $v['keyword_id'] . ' and keyword_tag_id in (' . $v['keyword_tag_id'] . '))';
				} else {
					$whereStr = '(keyword_id=' . $v['keyword_id'] . ' and keyword_tag_id=' . $v['keyword_tag_id'] . ')';
				}

				$keywordWhere .= empty($keywordWhere) ? $whereStr : ' or ' . $whereStr;
			}

			//todo 类型转换
			//1：文本、2：图片、3：语音、4：视频、5：图文、6：小程序、7：文件  WorkMsgKeywordAttachmentInfo
			//1：图片、2：音频、3：视频、4：图文、5：文件、6：文本、7：小程序  attachment
			$type = [1 => 6, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 7, 7 => 5];

			//关键词内容分类
			$attachmentInfo  = WorkMsgKeywordAttachmentInfo::find()->where(['status' => 1])->andWhere($keywordWhere)->andWhere(['>', 'attachment_id', 0])->select('type')->groupBy('type')->asArray()->all();
			if (empty($attachmentInfo)) {
				throw new InvalidDataException('无推荐内容！');
			}
			$attachment_type = [];
			foreach ($attachmentInfo as $v){
				$attachment_type[] = $type[$v['type']];
			}

			//内容类型
			$file_type = !empty($file_type) ? $file_type : $attachment_type[0];

			$attachmentType = 0;
			foreach ($type as $k=>$v){
				if ($v == $file_type){
					$attachmentType = $k;
					break;
				}
			}
			if (empty($attachmentType)){
				throw new InvalidDataException('内容类型数据不正确');
			}

			//内容数据
			$attachmentInfo = WorkMsgKeywordAttachmentInfo::find()->where(['status' => 1, 'type' => $attachmentType])->andWhere($keywordWhere);
			$attachAll      = $attachmentInfo->asArray()->all();
			$attachmentIds  = [];
			foreach ($attachAll as $v) {
				if (!empty($v['attachment_id'])) {
					$attachmentIds[] = $v['attachment_id'];
				}
			}

			$attachment = Attachment::find()->alias('a');
			$attachment = $attachment->where(['a.id' => $attachmentIds, 'a.status' => 1, 'a.is_temp' => 0]);
			if ($file_type == 1) {
				$attachment = $attachment->andWhere(['a.file_type' => $file_type, 'a.file_content_type' => ['image/jpeg', 'image/png']]);
			} elseif ($file_type == 3) {
				$attachment = $attachment->andWhere(['a.file_type' => $file_type])->andWhere(['<=', 'a.file_length', 10485760]);
			} elseif ($file_type == 4) {
				$attachment = $attachment->andWhere(['a.file_type' => $file_type])->andWhere(['!=', 'a.file_name', '']);
			} elseif ($file_type == 5) {
				$attachment = $attachment->andWhere(['a.file_type' => $file_type])->andWhere(['<=', 'a.file_length', 20971520]);
			} else {
				$attachment = $attachment->andWhere(['a.file_type' => $file_type]);
			}
			$select     = new Expression("a.id,a.id as `key`,a.group_id,a.file_type,a.file_name,a.file_content_type,a.file_width,a.file_height,a.local_path,a.s_local_path,a.jump_url,a.text_content as `content`");
			$attachment = $attachment->select($select)->orderBy('a.id desc');

			$count = $attachment->count();
			$keys  = [];
			if (in_array($file_type, [1, 3, 4, 5])) {
				//文件柜获取所有的key
				$idList = $attachment->all();
				if (!empty($idList)) {
					foreach ($idList as $idInfo) {
						array_push($keys, (string) $idInfo['id']);
					}
				}
			}
			//分页
			$offset     = ($page - 1) * $pageSize;
			$attachData = $attachment->limit($pageSize)->offset($offset)->asArray()->all();
			foreach ($attachData as $key => $attach) {
				if ($attach['file_type'] == 6) {
					$attachData[$key]['content'] = rawurldecode($attach['content']);
				}
				if ($attach['file_type'] == 5) {
					$attachData[$key]['extension'] = Attachment::getExtension($attach['file_content_type'], $attach['file_name']);
				} else {
					$attachData[$key]['extension'] = '';
				}

				$attachData[$key]['first_group'] = [];
				if (!empty($attach['group_id'])) {
					$attachData[$key]['first_group'] = AttachmentGroup::getFirstGroup($attach['group_id']);
				}

				//todo beenlee 雷达链接状态
				$radarInfo = RadarLink::findOne(['associat_type' => 0, 'associat_id' => $attach['id']]);
				if ($radarInfo) {
					$attachData[$key]['radar_status'] = $radarInfo->status;
				} else {
					$attachData[$key]['radar_status'] = 0;
				}
			}

			/*$attachmentIds     = [];
			$notAttachmentData = [];//未同步素材
			foreach ($attachAll as $v) {
				if (!empty($v['attachment_id'])) {
					$attachmentIds[] = $v['attachment_id'];
				} else {
					$notAttachmentD                 = [];
					$notAttachmentD['id']           = $v['id'] . '-s';
					$notAttachmentD['key']          = $v['id'] . '-s';
					$notAttachmentD['file_type']    = $file_type;
					$notAttachmentD['local_path']   = $v['cover_url'];
					$notAttachmentD['jump_url']     = $v['content_url'];
					$notAttachmentD['content']      = $v['type'] == 1 ? rawurldecode($v['content']) : $v['content'];
					$notAttachmentD['file_name']    = $v['type'] == 1 ? mb_substr($notAttachmentD['content'], 0, 10, 'utf-8') : $v['title'];
					$notAttachmentD['first_group']  = [];
					$notAttachmentD['radar_status'] = 0;

					$notAttachmentData[] = $notAttachmentD;
				}
			}

			//内容引擎素材
			$attachmentData = [];
			$select         = new Expression("a.id,a.id as `key`,a.group_id,a.file_type,a.file_name,a.file_content_type,a.file_width,a.file_height,a.local_path,a.s_local_path,a.jump_url,a.text_content as `content`");
			if ($attachmentIds) {
				$attachment = Attachment::find()->alias('a');
				$attachment = $attachment->where(['a.id' => $attachmentIds, 'a.status' => 1, 'a.is_temp' => 0]);
				if ($file_type == 1) {
					$attachment = $attachment->andWhere(['a.file_type' => $file_type, 'a.file_content_type' => ['image/jpeg', 'image/png']]);
				} elseif ($file_type == 3) {
					$attachment = $attachment->andWhere(['a.file_type' => $file_type])->andWhere(['<=', 'a.file_length', 10485760]);
				} elseif ($file_type == 4) {
					$attachment = $attachment->andWhere(['a.file_type' => $file_type])->andWhere(['!=', 'a.file_name', '']);
				} elseif ($file_type == 5) {
					$attachment = $attachment->andWhere(['a.file_type' => $file_type])->andWhere(['<=', 'a.file_length', 20971520]);
				} else {
					$attachment = $attachment->andWhere(['a.file_type' => $file_type]);
				}
				$attachmentData = $attachment->select($select)->orderBy('a.id desc')->asArray()->all();

				foreach ($attachmentData as $key => $attach) {
					if ($attach['file_type'] == 6) {
						$attachmentData[$key]['content'] = rawurldecode($attach['content']);
					}
					if ($attach['file_type'] == 5) {
						$attachmentData[$key]['extension'] = Attachment::getExtension($attach['file_content_type'], $attach['file_name']);
					} else {
						$attachmentData[$key]['extension'] = '';
					}

					$attachmentData[$key]['first_group'] = [];
					if (!empty($attach['group_id'])) {
						$attachmentData[$key]['first_group'] = AttachmentGroup::getFirstGroup($attach['group_id']);
					}
					//todo beenlee 雷达链接状态
					$radarInfo = RadarLink::findOne(['associat_type' => 0, 'associat_id' => $attach['id']]);
					if ($radarInfo) {
						$attachmentData[$key]['radar_status'] = $radarInfo->status;
					} else {
						$attachmentData[$key]['radar_status'] = 0;
					}
				}
			}

			$attachData = [];
			foreach ($attachmentData as $v) {
				$attachData[] = $v;
			}
			foreach ($notAttachmentData as $v) {
				$attachData[] = $v;
			}

			$count = count($attachData);

			$keys = [];
			if (in_array($file_type, [1, 3, 4, 5])) {
				foreach ($attachData as $idInfo) {
					array_push($keys, (string) $idInfo['id']);
				}
			}*/

			return [
				'attachment_type' => $attachment_type,
				'file_type'       => $file_type,
				'keys'            => $keys,
				'is_have'         => [],
				'count'           => $count,
				'attachment'      => $attachData,
			];
		}


		public function actionKeywordTest ()
		{
			$content = \Yii::$app->request->get('content');

			if (empty($content)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			$auditInfo = new WorkMsgAuditInfo();
			$auditInfo->audit_id = 2;
			$auditInfo->content = rawurlencode($content);
			$auditInfo->external_id = '1996';
			$auditInfo->to_user_id = '182';
			$auditInfo->from = 'wmiWVTDwAALjXswWLs1Q3eJJ18pBm2qA';
			$auditInfo->tolist = '93d12f2b83a0a6796c421d42795d3422';
			$auditInfo->id = 152188;

			WorkMsgKeywordUser::creat($auditInfo);

			return true;
		}

	}