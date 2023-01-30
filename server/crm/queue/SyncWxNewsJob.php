<?php

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\Article;
	use app\models\Attachment;
	use app\models\AttachmentGroup;
	use app\models\Material;
	use app\models\MaterialPullTime;
	use app\models\UserAuthorRelation;
	use app\util\DateUtil;
	use app\util\MaterialDownload;
	use app\util\SUtils;
	use callmez\wechat\sdk\Wechat;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWxNewsJob extends BaseObject implements JobInterface
	{
		public $author_id;
		public $wxAuthInfo;
		public $wxAuthorize;
		public $material_type;
		public $group_id = 0;
		public $sub_id;

		public function execute ($queue)
		{
			$wxAuthInfo  = $this->wxAuthInfo;
			$wxAuthorize = $this->wxAuthorize;
			$group_id    = $this->group_id;

			$wechat = \Yii::createObject([
				'class'          => Wechat::className(),
				'appId'          => $wxAuthInfo->authorizer_appid,
				'appSecret'      => $wxAuthorize['config']->appSecret,
				'token'          => $wxAuthorize['config']->token,
				'componentAppId' => $wxAuthorize['config']->appid,
			]);

			$userAuthorRelation = UserAuthorRelation::findOne(['author_id' => $this->author_id]);
			$typeArr            = [
				1 => 'news',
				2 => 'image',
				3 => 'voice',
				4 => 'video',
			];
			$type               = $typeArr[$this->material_type];
			$i                  = 0;
			$materialIds        = [];
			//查询分组
			if (empty($group_id)) {
				$notGroup = AttachmentGroup::findOne(['uid' => $userAuthorRelation->uid, 'is_not_group' => 1]);
				if (empty($notGroup)) {
					$group               = new AttachmentGroup();
					$group->uid          = $userAuthorRelation->uid;
					$group->title        = '未分组';
					$group->sort         = 1;
					$group->is_not_group = 1;
					$group->create_time  = DateUtil::getCurrentTime();
					if ($group->validate() && $group->save()) {
						Attachment::updateAll(['group_id' => $group->id], ['uid' => $userAuthorRelation->uid, 'status' => 1, 'group_id' => NULL]);
					}
				}
				$group_id = $notGroup->id;
			}
			try {
				while (true) {
					$postData = [
						'type'   => $type,
						'offset' => $i * 20,
						'count'  => 20,
					];
					$res      = $wechat->getMaterialList($postData);
					if (!empty($res) && !empty($res['total_count'])) {
						$itemList = $res['item'];
						foreach ($itemList as $item) {
							if ($type == 'news') {
								$material = Material::findOne(['media_id' => $item['media_id']]);
								if (empty($material)) {
									$material                = new Material();
									$material->author_id     = $this->author_id;
									$material->media_id      = $item['media_id'];
									$material->type          = 1;
									$material->material_type = $this->material_type;
									$material->create_time   = DateUtil::getCurrentTime();
								} else {
									if ($material->update_time >= $item['update_time']) {
										array_push($materialIds, $material->id);
										continue;
									} else {
										$material->update_time = $item['update_time'];
									}
								}
								$material->status = 1;
								if (!$material->validate() || !$material->save()) {
									throw new InvalidDataException(SUtils::modelError($material));
								}
								$articleIds = [];
								$remove     = 0;
								$artIdArr   = Article::find()->where(['material_id' => $material->id])->select('id')->asArray()->all();
								if (!empty($artIdArr)) {
									$artIdArr = array_column($artIdArr, 'id');
								}
								foreach ($item['content']['news_item'] as $key => $new) {
									if (empty($new['thumb_media_id'])) {
										if (empty($new['thumb_url'])) {
											//$remove = 1;
											continue;
										}
									}

									$itemData       = [
										'author_id'       => $this->author_id,
										'media_id'        => $new['thumb_media_id'],
										'material_type'   => 2,
										'name'            => '',
										'url'             => $new['thumb_url'],
										'uid'             => $userAuthorRelation->uid,
										'sub_id'          => $this->sub_id,
										'create_time'     => $item['content']['create_time'],
										'group_id'        => $group_id,
										'parent_media_id' => $item['media_id'],
										'key'             => $key,
										'new'             => $new,
									];
									$thumb_media_id = $this->addItem($itemData, $wechat);
									if (empty($thumb_media_id)) {
										continue;
									}
									if (isset($artIdArr[$key]) && !empty($artIdArr[$key])) {
										$article = Article::findOne($artIdArr[$key]);
										if (empty($article)) {
											$article              = new Article();
											$article->create_time = date('Y-m-d H:i:s', $item['content']['create_time']);
										}
									} else {
										$article              = new Article();
										$article->create_time = date('Y-m-d H:i:s', $item['content']['create_time']);
									}
									$article->thumb_media_id        = $thumb_media_id;
									$article->material_id           = $material->id;
									$article->title                 = $new['title'];
									$article->author                = $new['author'];
									$article->digest                = $new['digest'];
									$article->show_cover_pic        = $new['show_cover_pic'];
									$article->wx_content            = $new['content'];
									$content                        = Material::changeVideoUrl($new['content'], $userAuthorRelation->uid);
									$article->content               = $content;
									$article->content_source_url    = $new['url'];
									$article->need_open_comment     = $new['need_open_comment'];
									$article->only_fans_can_comment = $new['only_fans_can_comment'];
									if (!$article->validate() || !$article->save()) {
										throw new InvalidDataException(SUtils::modelError($article));
									}
									array_push($articleIds, $article->id);
								}
								//多余的删除
								Article::deleteAll(['and', ['material_id' => $material->id], ['not in', 'id', $articleIds]]);
								if (!empty($remove)) {
									continue;
								}

								if (empty($articleIds)) {
									$material->attachment_id = NULL;
									$material->save();

									Attachment::deleteAll(['material_id' => $material->id]);
									$material->delete();

									continue;
								}
								$material->article_sort = implode(',', $articleIds);
								$material->news_type    = 1;
								if (count($articleIds) > 1) {
									$material->news_type = 2;
								}
								if (!$material->validate() || !$material->save()) {
									throw new InvalidDataException(SUtils::modelError($material));
								}
								//图文做附件关联
								$attachment = Attachment::findOne(['material_id' => $material->id]);
								if (empty($attachment)) {
									$attachment              = new Attachment();
									$attachment->uid         = $userAuthorRelation->uid;
									$attachment->sub_id      = $this->sub_id;
									$attachment->group_id    = $group_id;
									$attachment->file_type   = 4;
									$attachment->source      = 1;
									$attachment->create_time = DateUtil::getCurrentTime();
									$attachment->material_id = $material->id;
									if (count($articleIds) == 1) {
										$article = Article::find()->alias('a');
										$article = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
										$artInfo = $article->where(['a.id' => $articleIds[0]])->select('a.title,a.digest,m.local_path,m.s_local_path,m.attachment_id,a.content_source_url')->asArray()->one();
										if (!empty($artInfo)) {
											$attachment->file_name    = $artInfo['title'];
											$attachment->content      = $artInfo['digest'];
											$attachment->local_path   = $artInfo['local_path'];
											$attachment->s_local_path = $artInfo['s_local_path'];
											$attachment->attach_id    = intval($artInfo['attachment_id']);
											$attachment->jump_url     = $artInfo['content_source_url'];
										}
									}
									if (!$attachment->validate() || !$attachment->save()) {
										throw new InvalidDataException(SUtils::modelError($attachment));
									}
								} elseif (count($articleIds) == 1) {
									$article = Article::find()->alias('a');
									$article = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
									$artInfo = $article->where(['a.id' => $articleIds[0]])->select('a.title,a.digest,m.local_path,m.s_local_path,m.attachment_id,a.content_source_url')->asArray()->one();
									if (!empty($artInfo)) {
										$attachment->file_name    = $artInfo['title'];
										$attachment->content      = $artInfo['digest'];
										$attachment->local_path   = $artInfo['local_path'];
										$attachment->s_local_path = $artInfo['s_local_path'];
										$attachment->attach_id    = intval($artInfo['attachment_id']);
										$attachment->jump_url     = $artInfo['content_source_url'];
										if (!$attachment->validate() || !$attachment->save()) {
											throw new InvalidDataException(SUtils::modelError($attachment));
										}
									}
								} elseif (count($articleIds) > 1) {
									$attachment->file_name    = '';
									$attachment->local_path   = '';
									$attachment->s_local_path = '';
									$attachment->jump_url     = '';
									$attachment->content      = '';
									$attachment->text_content = '';
									if (!$attachment->validate() || !$attachment->save()) {
										throw new InvalidDataException(SUtils::modelError($attachment));
									}
								}
								if (!empty($attachment->id)) {
									$material->attachment_id = $attachment->id;
									if (!$material->validate() || !$material->save()) {
										throw new InvalidDataException(SUtils::modelError($material));
									}
								}
								array_push($materialIds, $material->id);
							} else {
								if ($type == 'image' && empty($item['url'])) {
									continue;
								}
								$item['author_id']     = $this->author_id;
								$item['material_type'] = $this->material_type;
								$item['uid']           = $userAuthorRelation->uid;
								$item['sub_id']        = $this->sub_id;
								$item['create_time']   = $item['update_time'];//拉取是只有修改时间
								$item['group_id']      = $group_id;

								$materialId = $this->addItem($item, $wechat);
								if (!empty($materialId)) {
									array_push($materialIds, $materialId);
								}
							}
						}
						if ($res['item_count'] < 20) {
							break;
						}
					} else {
						break;
					}
					$i++;
				}
				//更新素材库状态
				if (!empty($materialIds)) {
					Material::updateAll(['status' => 0], ['and', ['author_id' => $this->author_id, 'material_type' => $this->material_type, 'type' => 1], ['not in', 'id', $materialIds]]);
					Material::updateAll(['status' => 1], ['id' => $materialIds]);
					//修改图文关联的附件状态
					if ($this->material_type == 1) {
						$userRelation = UserAuthorRelation::findOne(['author_id' => $this->author_id]);
						Attachment::updateAll(['status' => 0], ['and', ['uid' => $userRelation->uid, 'file_type' => 4], ['>', 'material_id', 0], ['not in', 'material_id', $materialIds]]);
						Attachment::updateAll(['status' => 1], ['uid' => $userRelation->uid, 'file_type' => 4, 'material_id' => $materialIds]);
					}
				}

				//添加素材拉取记录
				$materialPullTime = MaterialPullTime::findOne(['author_id' => $this->author_id, 'material_type' => $this->material_type]);
				if (empty($materialPullTime)) {
					$materialPullTime                = new MaterialPullTime();
					$materialPullTime->author_id     = $this->author_id;
					$materialPullTime->material_type = $this->material_type;
				}
				$materialPullTime->pull_time = DateUtil::getCurrentTime();
				if (!$materialPullTime->validate() || !$materialPullTime->save()) {
					throw new InvalidDataException(SUtils::modelError($materialPullTime));
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . 'errorMsg');
			} catch (\Throwable $e) {
				\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . 'errorMsg');
			}
		}

		//微信拉取添加图片素材
		private function addItem ($item, $wechat = '')
		{
			if (!empty($item['media_id'])) {
				$material = Material::findOne(['media_id' => $item['media_id']]);
			}
			$uid = $item['uid'];
			if (empty($material)) {
				$material                = new Material();
				$material->author_id     = $item['author_id'];
				$material->media_id      = $item['media_id'];
				$material->type          = 1;
				$material->created_at    = (string) $item['create_time'];
				$material->create_time   = DateUtil::getCurrentTime();
				$material->material_type = $item['material_type'];
				$material->file_name     = $item['name'];
				if ($item['material_type'] == 3) {
					$file_type = 2;
					$url       = $wechat->getMaterial($item['media_id'], 1);
					//$imgData   = Material::getVoice($url, $item['media_id']);
					$imgData = MaterialDownload::download($url, MaterialDownload::VOICE_TYPE, MaterialDownload::VOICE_PATH . '/' . $uid, '', ['media_id' => $item['media_id']]);
					if (empty($imgData['error'])) {
						$imgData['content_type'] = $imgData['local_data']['content_type'];
						$duArr                   = explode(':', $imgData['local_data']['media_duration']);
						$hour                    = !empty($duArr[0]) ? intval($duArr[0]) : 0;
						$minute                  = !empty($duArr[1]) ? intval($duArr[1]) : 0;
						$second                  = !empty($duArr[2]) ? intval($duArr[2]) : 0;
						$imgData['duration']     = $hour * 3600 + $minute * 60 + $second;
					}
					$wx_url = '';
				} elseif ($item['material_type'] == 4) {
					$file_type              = 3;
					$result                 = $wechat->getMaterial($item['media_id']);
					$material->title        = $item['name'];
					$material->introduction = $result['description'];
					$material->wx_url       = $result['down_url'];
					$wx_url                 = $result['down_url'];
					$imgData                = Material::getVideo($result['down_url'], 'videos/' . $uid);
				} else {
					if (!empty($item['new']['content'])) {
						$material->wx_url = $item['url'];
						$wx_url           = $item['url'];
						//$imgData          = Material::getImage($item['url']);
						$imgData = MaterialDownload::download($item['url'], MaterialDownload::IMAGE_TYPE, MaterialDownload::IMAGE_PATH . '/' . $uid, '', ['is_thumb' => 1]);
						if (empty($imgData['error'])) {
							$imgData['width']        = $imgData['local_data']['width'];
							$imgData['height']       = $imgData['local_data']['height'];
							$imgData['content_type'] = $imgData['local_data']['mime'];
							if (empty($item['media_id']) && !empty($wechat) && !empty($item['new'])) {
								$item['new']['parent_media_id'] = $item['parent_media_id'];
								$item['new']['key']             = $item['key'];
								$thumb_media_id                 = Material::updateWxMaterial($wechat, $imgData['local_path'], $item['new']);
								if (empty($thumb_media_id)) {
									return '';
								}
								$material->media_id = $thumb_media_id;
							}
						}
						$file_type = 1;
					} else {
						return '';
					}
				}
				if (empty($imgData['error'])) {
					$material->local_path   = $imgData['local_path'];
					$material->file_length  = $imgData['file_length'];
					$material->content_type = $imgData['content_type'];
					//添加附件表
					$attachment                    = new Attachment();
					$attachment->uid               = $item['uid'];
					$attachment->sub_id            = $item['sub_id'];
					$attachment->file_type         = $file_type;
					$attachment->file_name         = !empty($item['name']) ? $item['name'] : $imgData['file_name'];
					$attachment->file_content_type = $imgData['content_type'];
					$attachment->file_length       = $imgData['file_length'];
					if ($file_type != 2) {
						$attachment->file_width  = $imgData['width'];
						$attachment->file_height = $imgData['height'];
						$material->media_width   = $imgData['width'];
						$material->media_height  = $imgData['height'];
					}
					if (isset($imgData['duration'])) {
						$material->media_duration  = $imgData['duration'];
						$attachment->file_duration = $imgData['duration'];
					}
					if (isset($imgData['s_local_path'])) {
						$attachment->s_local_path = $imgData['s_local_path'];
						$material->s_local_path   = $imgData['s_local_path'];
					}
					$attachment->local_path  = $imgData['local_path'];
					$attachment->wx_url      = $wx_url;
					$attachment->create_time = DateUtil::getCurrentTime();
					$attachment->source      = 1;
					$attachment->group_id    = $item['group_id'];
					//$attachment->is_temp     = !empty($item['new']) ? 1 : 0;//公版不加这个,我乐定制的
					if (!$attachment->validate() || !$attachment->save()) {
						throw new InvalidDataException(SUtils::modelError($attachment));
					}
					$material->attachment_id = $attachment->id;
				} else {
					return '';
				}
			} else {
				$material->file_name = $item['name'];
				if (!empty($material->attachment_id)) {
					$attachment            = Attachment::findOne($material->attachment_id);
					$attachment->file_name = $item['name'];
					if ($attachment->file_type == 1) {
						if ($attachment->wx_url != $item['url']) {
							$imgData = MaterialDownload::download($item['url'], MaterialDownload::IMAGE_TYPE, MaterialDownload::IMAGE_PATH . '/' . $uid, '', ['is_thumb' => 1]);
							if (empty($imgData['error'])) {
								$attachment->file_width  = $imgData['local_data']['width'];
								$attachment->file_height = $imgData['local_data']['height'];
								$attachment->file_length = $imgData['file_length'];
								$attachment->local_path  = $imgData['local_path'];
								$attachment->wx_url      = $item['url'];

								$material->media_width  = $imgData['local_data']['width'];
								$material->media_height = $imgData['local_data']['height'];
								$material->file_length  = $imgData['file_length'];
								$material->local_path   = $imgData['local_path'];
								$material->wx_url       = $item['url'];
								if (isset($imgData['s_local_path'])) {
									$attachment->s_local_path = $imgData['s_local_path'];
									$material->s_local_path   = $imgData['s_local_path'];
								}
								$material->update();
							}
						}
						//$attachment->is_temp = !empty($item['new']) ? 1 : 0;//公版不加这个,我乐定制的
					}
					$attachment->update();
				}
			}
			if (!$material->validate() || !$material->save()) {
				throw new InvalidDataException(SUtils::modelError($material));
			}

			return $material->id;
		}
	}