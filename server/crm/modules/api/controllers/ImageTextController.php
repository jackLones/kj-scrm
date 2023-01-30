<?php
	/**
	 * H5图文接口
	 * User: fulu
	 * Date: 2020/02/29
	 * Time: 17:17
	 */

	namespace app\modules\api\controllers;

	use app\models\Attachment;
	use app\models\AttachmentStatistic;
	use app\models\ExternalTimeLine;
	use app\models\Fans;
	use app\models\Material;
	use app\models\Article;
	use app\components\InvalidDataException;
	use app\models\RadarLink;
	use app\models\User;
	use app\models\UserAuthorRelation;
	use app\models\WorkChat;
	use app\models\WorkChatInfo;
	use app\models\WorkContactWay;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkTag;
	use app\models\WorkWelcome;
	use app\modules\api\components\BaseController;
	use app\util\DateUtil;
	use app\util\SUtils;
	use yii\web\MethodNotAllowedHttpException;

	class ImageTextController extends BaseController
	{
		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/image-text/
		 * @title           获取图文预览内容
		 * @description     获取图文预览内容及图文详情接口
		 * @method   post
		 * @url  http://{host_name}/api/image-text/image-text-preview-info
		 *
		 * @param num  可选 int 缓存键值（预览必选）
		 * @param attach_id  可选 int 附件表id（单图文详情）
		 * @param article_id  可选 int 图文表id（多图文详情）
		 * @param user  可选 int|string 访问者
		 * @param user_type  可选 int 访问者类型：2、外部联系人；3、位置类型（默认2）
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    title string 图文标题
		 * @return_param    author string 作者
		 * @return_param    pic_url string 图片封面
		 * @return_param    image_text string 编辑器图文
		 * @return_param    statistic_id int 统计ID
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-02-28
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 */
		public function actionImageTextPreviewInfo ()
		{
			\Yii::error(\Yii::$app->request->post(), 'actionImageTextPreviewInfo');
			$num        = \Yii::$app->request->post('num');
			$attach_id  = \Yii::$app->request->post('attach_id');
			$article_id = \Yii::$app->request->post('article_id');
			$chat_id    = \Yii::$app->request->post('chat_id');
			$user       = \Yii::$app->request->post('user');
			$userType   = \Yii::$app->request->post('user_type', AttachmentStatistic::EXTERNAL_USER);
			$agent_id   = \Yii::$app->request->post('agent_id', 0);
			$num        = (int) $num;
			$attach_id  = (int) $attach_id;
			$article_id = (int) $article_id;
			if (empty($num) && empty($attach_id) && empty($article_id)) {
				throw new InvalidDataException('缺少必要参数！');
			}
			$aesConfig = \Yii::$app->get('aes');
			if ($aesConfig === NULL) {
				$aesConfig = ['key' => '123456'];
			}

			$site_url    = \Yii::$app->params['site_url'];
			$previewData = [];
			if (!empty($num)) {
				//获取缓存预览内容
				$cacheKey    = 'image_text_' . $num;
				$previewData = \Yii::$app->cache->get($cacheKey);

				if (empty($previewData)) {
					throw new InvalidDataException('二维码已过期，请重新生成二维码！');
				}
				if ($previewData['show_cover_pic']) {
					$previewData['image_text'] = '<p style="text-align: center;"><img src="' . $site_url . $previewData['pic_url'] . '"/></p>' . $previewData['image_text'];
				}
			} elseif (!empty($attach_id)) {
				//单图文详情
				$info = Attachment::findOne($attach_id);
				if ($info === NULL) {
					throw new InvalidDataException('没有找到该图文详情');
				}
				$previewData['uid']     = $info->uid;
				$previewData['title']   = $info->file_name;
				$previewData['author']  = $info->author;
				$previewData['pic_url'] = $info->local_path;
				$previewData['create_time'] = date("Y-m-d H:i", strtotime($info->create_time));
				if ($info->show_cover_pic) {
					$info->image_text = '<p style="text-align: center;"><img src="' . $site_url . $info->local_path . '"/></p>' . $info->image_text;
				}
				$previewData['image_text'] = $info->image_text;
				$statisticData             = [];
				$userId                    = 0;
				$openid                    = '';
				if (!empty($user)) {
					if ($userType == AttachmentStatistic::EXTERNAL_USER) {
						$externalContact = WorkExternalContact::findOne(['external_userid' => $user]);
						if ($externalContact !== NULL && $externalContact->corp->userCorpRelations[0]->uid == $info->uid) {
							$userId                   = $externalContact->id;
							$openid                   = $externalContact->openid;
							$statisticData['corp_id'] = $externalContact->corp->id;
						}
						if (!empty($chat_id) && $externalContact !== NULL) {
							$chatInfo = WorkChatInfo::findOne(['chat_id' => $chat_id, 'external_id' => $externalContact->id, 'type' => 2, 'status' => 1]);
							if ($chatInfo !== NULL) {
								$statisticData['chat_id'] = $chat_id;
							}
						}
					} elseif ($userType == AttachmentStatistic::PUBLIC_USER) {
						$openid = $userId = $user;
					}
				}

				//todo beenlee 内容裂变层级
				$attach_code = \Yii::$app->request->post('attach_code', '');//加密参数
				if (!empty($attach_code)) {
					$attach_code = \Yii::$app->getSecurity()->decryptByPassword(base64_decode(urldecode($attach_code)), $aesConfig->key);
					$attach_code = json_decode($attach_code, true);
					if (!empty($attach_code) && is_array($attach_code)) {
						$work_user_id = isset($attach_code['work_user_id']) ? $attach_code['work_user_id'] : 0;//发送员工 user_id
						$uid          = isset($attach_code['uid']) ? $attach_code['uid'] : 0;//用户id
					}
				}
				$radar_status = 0;
				//beenlee 雷达链接状态
				$radarInfo = RadarLink::findOne(['associat_type' => 0, 'associat_id' => $attach_id]);
				if ($radarInfo) {
					$radar_status = $radarInfo->status;
				}

				if ($radar_status > 0 && !empty($user)) {
					//beenlee  雷达链接记录，非雷达链接不记录
					if (!empty($userId) && ($info['file_type'] == 4 && ($info['is_editor'] == 1 || $info['material_id'] > 0))) {
						$previewData['statistic_id'] = AttachmentStatistic::create($attach_id, $userId, $statisticData, AttachmentStatistic::ATTACHMENT_OPEN, $userType);
					}

					if (!empty($userId) && $userType == AttachmentStatistic::EXTERNAL_USER && $radarInfo['radar_tag_open'] > 0 && !empty($radarInfo['tag_ids'])) {
						if (!isset($work_user_id) || empty($work_user_id)) {
							$work_user_id = \Yii::$app->request->post('work_user_id', 0);
						}

						if ($work_user_id > 0) {
							$followUser = WorkExternalContactFollowUser::findOne(['external_userid' => $userId, 'user_id' => $work_user_id]);
							if ($followUser) {
								$attachmentType = ['1' => '图片', '2' => '音频', '3' => '视频', '4' => '图文', '5' => '文件', '6' => '文本', '7' => '小程序'];
								//beenlee 打标签
								//$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $userId, 'user_id' => $info->uid])->asArray()->all();
								$otherData = ['type' => 'radar_tag', 'msg' => ' ' . $attachmentType[$info->file_type] . '【' . $info->file_name . '】'];
								WorkTag::addUserTag(2, [$followUser->id], explode(',', $radarInfo['tag_ids']), $otherData);
							}
						}
					}
				}
			} elseif (!empty($article_id)) {
				//多图文某一图文详情
				$info                   = Article::findOne($article_id);
				$previewData['title']   = $info->title;
				$previewData['author']  = $info->author;
				$materialCover          = Material::findOne(['id' => $info->thumb_media_id]);
				$userAuthor             = UserAuthorRelation::findOne(['author_id' => $materialCover->author_id]);
				$previewData['uid']     = !empty($userAuthor) ? $userAuthor->uid : 0;
				$previewData['pic_url'] = $materialCover->local_path;
				if ($info->show_cover_pic) {
					$info->content = '<p style="text-align: center;"><img src="' . $site_url . $materialCover->local_path . '"/></p>' . $info->content;
				}
				$previewData['create_time'] = date("Y-m-d H:i", strtotime($info->create_time));
				$previewData['image_text'] = $info->content;
			}

			$access_token = '';
			if ($previewData['uid'] > 0) {
				$user         = User::findOne($previewData['uid']);
				$user_type    = User::USER_TYPE;
				$access_token = base64_encode($user_type . '-' . $user->access_token);
			}
			$previewData['access_token'] = $access_token;

			//分享地址 上下级关联关系
			$attach_data   = [
				'uid'          => isset($uid) ? $uid : 0,
				'user_id'      => isset($work_user_id) ? $work_user_id : 0,
				'work_user_id' => isset($work_user_id) ? $work_user_id : 0
			];
			$encryptedData = urlencode(urlencode(base64_encode(\Yii::$app->getSecurity()->encryptByPassword(json_encode($attach_data, JSON_UNESCAPED_UNICODE), $aesConfig->key))));
			$link          = \Yii::$app->params['web_url'] . "/h5/pages/preview/index?attach_id=" . $attach_id;
			if (!empty($agent_id)) {
				$link .= '&agent_id=' . $agent_id;
			}
			if (!empty($chat_id)) {
				$link .= '&chat_id=' . $chat_id;
			}
			$link                     .= '&attach_code=' . $encryptedData;
			$previewData['share_url'] = $link;

			return $previewData;
		}

		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/image-text/
		 * @title           H5图文详情
		 * @description     H5图文详情（不用）
		 * @method   post
		 * @url  http://{host_name}/api/image-text/image-text-detail
		 *
		 * @param attachment_id 必选 int 附件id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    group_id  string 分组id
		 * @return_param    attachment_id  int 附件id
		 * @return_param    data array 结果数据
		 * @return_param    data.title string 图文标题
		 * @return_param    data.content string 图文描述
		 * @return_param    data.image_text string 编辑器图文
		 * @return_param    data.author string 作者
		 * @return_param    data.show_cover_pic int 是否显示封面1是0否
		 * @return_param    data.attach_id int 封面图片id
		 * @return_param    data.pic_url string 图片封面
		 * @return_param    data.jump_url string 跳转链接
		 * @return_param    data.article_id int 图文表id
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-03-02
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionImageTextDetail ()
		{
			$attachment_id = \Yii::$app->request->post('attachment_id');
			if (empty($attachment_id)) {
				$attachment_id = \Yii::$app->request->get('attachment_id');
			}
			if (empty($attachment_id)) {
				throw new InvalidDataException('缺少必要参数！');
			}
			$attachment = Attachment::findOne($attachment_id);
			$msgData    = [];
			if ($attachment->material_id) {
				$materialInfo = Material::find()->select('id,local_path,article_sort')->where(['id' => $attachment->material_id])->asArray()->one();
				if (!empty($materialInfo['article_sort'])) {
					$article = Article::find()->alias('a');
					$article = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
					$artList = $article->where('a.id in(' . $materialInfo['article_sort'] . ')')->orderBy(["FIELD(a.id," . $materialInfo['article_sort'] . ")" => true])->select('a.*,m.local_path,m.attachment_id')->asArray()->all();
					foreach ($artList as $v) {
						$data                   = [];
						$data['title']          = $v['title'];
						$data['content']        = $v['digest'];
						$data['image_text']     = $v['content'];
						$data['author']         = $v['author'];
						$data['show_cover_pic'] = $v['show_cover_pic'] ? true : false;
						$data['attach_id']      = $v['attachment_id'];
						$data['pic_url']        = $v['local_path'];
						$data['jump_url']       = $v['content_source_url'];
						$data['article_id']     = $v['id'];

						$msgData[] = $data;
					}
				}
			} else {
				$data                   = [];
				$data['title']          = $attachment->file_name;
				$data['content']        = $attachment->content;
				$data['image_text']     = $attachment->image_text;
				$data['author']         = $attachment->author;
				$data['show_cover_pic'] = $attachment->show_cover_pic ? true : false;
				$data['attach_id']      = $attachment->attach_id;
				$data['pic_url']        = $attachment->local_path;
				$data['jump_url']       = $attachment->jump_url;
				$data['article_id']     = '';

				$msgData[] = $data;
			}

			return ['attachment_id' => $attachment->id, 'group_id' => $attachment->group_id, 'data' => $msgData];
		}

		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/image-text/
		 * @title           素材打开
		 * @description     素材打开时间
		 * @method   post
		 * @url  http://{host_name}/api/image-text/get-statistic-id
		 *
		 * @param attach_id  可选 int 附件表id
		 * @param user  可选 int|string 访问者
		 * @param uid  可选 用户 id
		 * @param user_type  可选 int 访问者类型：2、外部联系人；3、位置类型（默认2）
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    statistic_id int 统计ID
		 *
		 * @remark          Create by PhpStorm. User: xingchangyu. Date: 2020-07-29 14:07
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 * @throws \app\components\InvalidParameterException
		 */
		public function actionGetStatisticId ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}

			\Yii::error(\Yii::$app->request->post(), 'actionGetStatisticId');
			$attach_id      = \Yii::$app->request->post('attach_id');
			$uid            = \Yii::$app->request->post('uid', 0);
			$work_user_id   = \Yii::$app->request->post('work_user_id', 0);
			$associat_type  = \Yii::$app->request->post('associat_type', 0);
			$associat_id    = \Yii::$app->request->post('associat_id', 0);
			$associat_param = \Yii::$app->request->post('associat_param', 0);
			//todo beenlee 内容裂变层级
			$attach_code = \Yii::$app->request->post('attach_code', '');//加密参数
			if (!empty($attach_code)) {
				$aesConfig = \Yii::$app->get('aes');
				if ($aesConfig === NULL) {
					$aesConfig = ['key' => '123456'];
				}
				$attach_code = \Yii::$app->getSecurity()->decryptByPassword(base64_decode(urldecode($attach_code)), $aesConfig->key);
				$attach_code = json_decode($attach_code, true);
				if (!empty($attach_code) && is_array($attach_code)) {
					$uid            = isset($attach_code['uid']) ? $attach_code['uid'] : 0;
					$work_user_id   = isset($attach_code['work_user_id']) ? $attach_code['work_user_id'] : 0;
					$associat_type  = isset($attach_code['associat_type']) ? $attach_code['associat_type'] : 0;
					$associat_id    = isset($attach_code['associat_id']) ? $attach_code['associat_id'] : 0;
					$associat_param = isset($attach_code['associat_param']) ? $attach_code['associat_param'] : 0;
				}
			}
			$statisticId  = 0;
			$radar_status = 0;
			$idArr        = explode(',', $attach_id);
			if (is_array($idArr) && count($idArr) == 1) {
				$tmpId    = $idArr[0];
				$tmpIdArr = explode('_', $tmpId);
			}
			//beenlee 雷达链接状态
			if (isset($tmpIdArr) && is_array($tmpIdArr) && count($tmpIdArr) > 1) {
				switch ($tmpIdArr[0]) {
					case "radar":
						$radarInfo = RadarLink::findOne($tmpIdArr['1']);
						$type      = 'radar';
						break;
					default:
						$radarInfo = NULL;
						$type      = 'other';
				}
			} else {
				$type      = 'attachment';
				$radarInfo = RadarLink::findOne(['associat_type' => 0, 'associat_id' => $attach_id]);
				if ($radarInfo === NULL) {
					if ($associat_id > 0) {
						if (empty($associat_param)) {
							$associat_param = NULL;
						}
						$radarInfo = RadarLink::findOne(['associat_type' => $associat_type, 'associat_id' => $associat_id, 'associat_param' => $associat_param]);
					}
				}
			}

			if ($radarInfo !== NULL) {
				$radar_status = $radarInfo->status;
			}

			if ($radar_status > 0) {
				$user     = \Yii::$app->request->post('user');
				$userType = \Yii::$app->request->post('user_type', AttachmentStatistic::EXTERNAL_USER);
				$chat_id  = \Yii::$app->request->post('chat_id', '');
				if ($type == 'attachment') {
					$info           = Attachment::findOne($attach_id);
					$attachmentType = ['1' => '图片', '2' => '音频', '3' => '视频', '4' => '图文', '5' => '文件', '6' => '文本', '7' => '小程序'];
					$radar_title    = ' ' . $attachmentType[$info->file_type] . ' 【' . $info->file_name . '】';
					//$radar_title = '【' . $info->file_name . '】';
				} else {
					$info = NULL;
				}

				$userId        = 0;
				$statisticData = [];
				if (!empty($user)) {
					if ($userType == AttachmentStatistic::EXTERNAL_USER) {
						$externalContact = WorkExternalContact::findOne(['external_userid' => $user]);

						if ($externalContact !== NULL && ($type == 'radar' || ($info !== NULL && $externalContact->corp->userCorpRelations[0]->uid == $info['uid']))) {
							$userId                   = $externalContact->id;
							$statisticData['corp_id'] = $externalContact->corp->id;
						}

						if (!empty($chat_id) && $externalContact !== NULL) {
							$chatInfo = WorkChatInfo::findOne(['chat_id' => $chat_id, 'external_id' => $externalContact->id, 'type' => 2, 'status' => 1]);
							if ($chatInfo !== NULL) {
								$statisticData['chat_id'] = $chat_id;
							}
						}
					} elseif ($userType == AttachmentStatistic::PUBLIC_USER) {
						$userId = $user;
					}
				}

				if (!empty($userId) && ($type == 'attachment' || $radarInfo['associat_type'] == 0)) {
					if ($radarInfo['dynamic_notification'] > 0 && $info !== NULL) {
						if ($info['file_type'] == 4 && ($info['is_editor'] == 1 || $info['material_id'] > 0)) {
							$statisticId = 0;
						} else {
							//beenlee 雷达链接记录，非雷达链接不记录
							$statisticId = AttachmentStatistic::create($attach_id, $userId, $statisticData, AttachmentStatistic::ATTACHMENT_OPEN, $userType);
							if (!(in_array($info['file_type'], [1, 3], false) || ($info['file_type'] == 5 && in_array($info['file_content_type'], ['text/plain', 'application/pdf'])))) {
								$statisticId = 0;
							}
						}
					} else {
						$statisticId = 0;
					}
				} elseif (!empty($userId) && $type == 'radar' && $work_user_id > 0) {
					//非内容引擎记录客户行为轨迹 雷达链接记录，非雷达链接不记录
					if ($userType == AttachmentStatistic::EXTERNAL_USER && $radarInfo['dynamic_notification'] > 0) {
						if ($radarInfo['associat_type'] == 2) {
							$event = 'work_welcome';
						} else if ($radarInfo['associat_type'] == 1) {
							$event = 'work_contact_way';
						} else {
							$event = '';
						}

						if (!empty($event)) {
							ExternalTimeLine::addExternalTimeLine(['external_id' => $userId, 'user_id' => $work_user_id, 'event' => $event, 'event_id' => $radarInfo['associat_id']]);
						}
					}
				}

				if (!empty($userId) && $radarInfo['radar_tag_open'] > 0 && !empty($radarInfo['tag_ids'])) {
					if ($work_user_id > 0) {
						$followUser = WorkExternalContactFollowUser::findOne(['external_userid' => $userId, 'user_id' => $work_user_id]);
						if ($followUser) {
							if (empty($radar_title) && $radarInfo['associat_id'] > 0) {
								if ($radarInfo['associat_type'] == 0) {
									$info = Attachment::findOne($radarInfo['associat_id']);
									if ($info !== NULL) {
										$attachmentType = ['1' => '图片', '2' => '音频', '3' => '视频', '4' => '图文', '5' => '文件', '6' => '文本', '7' => '小程序'];
										$radar_title    = ' ' . $attachmentType[$info->file_type] . ' 【' . $info->file_name . '】';
									}
								} elseif ($radarInfo['associat_type'] == 1) {
									$radar_title = '【欢迎语】';
								} elseif ($radarInfo['associat_type'] == 2) {
									$info = WorkContactWay::findOne($radarInfo['associat_id']);
									if ($info !== NULL) {
										$radar_title = ' 渠道活码【' . $info->title . '】';
									}
								} else {
									$radar_title = '【' . $radarInfo['title'] . '】';
								}
							}
							//beenlee 打标签
							$otherData = ['type' => 'radar_tag', 'msg' => $radar_title];
							WorkTag::addUserTag(2, [$followUser->id], explode(',', $radarInfo['tag_ids']), $otherData);
						}
					}
				}
			}

			return ['statistic_id' => $statisticId];
		}

		/**
		 * showdoc
		 *
		 * @catalog         数据接口/api/image-text/
		 * @title           素材离开
		 * @description     离开素材时间更新
		 * @method   POST
		 * @url  http://{host_name}/api/image-text/leave
		 *
		 * @param statistic_id 必选 int 统计ID
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: dovechen. Date: 2020/4/16 12:09
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionLeave ()
		{
			if (\Yii::$app->request->isPost) {
				$statisticId = \Yii::$app->request->post('statistic_id');
				if (empty($statisticId)) {
					throw new InvalidDataException('缺少必要参数！');
				}

				$attachmentStatistic = AttachmentStatistic::findOne($statisticId);
				if (empty($attachmentStatistic)) {
					throw new InvalidDataException('参数不正确');
				}

				return $attachmentStatistic->setLeaveTime();
			} else {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
		}
	}