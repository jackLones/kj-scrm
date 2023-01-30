<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\DateUtil;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_msg_keyword_attachment}}".
 *
 * @property int $id
 * @property int $corp_id 企业ID
 * @property string $keywords 关键词（逗号分隔）
 * @property int $type 推送类型1不限制2用户标签
 * @property string $attachment_ids 内容引擎id集合
 * @property int $is_del 是否删除1是0否
 * @property int $add_time 添加时间
 *
 * @property WorkCorp $corp
 */
class WorkMsgKeywordAttachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_msg_keyword_attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'type', 'is_del', 'add_time'], 'integer'],
            [['keywords'], 'string', 'max' => 5000],
            [['attachment_ids'], 'string', 'max' => 2000],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'             => Yii::t('app', 'ID'),
			'corp_id'        => Yii::t('app', '企业ID'),
			'keywords'       => Yii::t('app', '关键词（逗号分隔）'),
			'type'           => Yii::t('app', '推送类型1不限制2用户标签'),
			'attachment_ids' => Yii::t('app', '内容引擎id集合'),
			'is_del'         => Yii::t('app', '是否删除1是0否'),
			'add_time'       => Yii::t('app', '添加时间'),
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

	/**
	 * 设置智能推荐
	 */
	public static function setKeyword ($corp_id, $data)
	{
		//设置的关键词
		$keywordAdd = [];
		foreach ($data['keyword'] as $vv) {
			if (in_array($vv, $keywordAdd)) {
				throw new InvalidDataException('关键词‘' . $vv . '’重复，请修改后重试！');
			} else {
				array_push($keywordAdd, $vv);
			}
		}
		//已设置的关键词
		$keywordId = $data['id'];
		$keyWordData = static::find()->where(['corp_id' => $corp_id, 'is_del' => 0]);
		if ($keywordId){
			$keyWordData = $keyWordData->andWhere(['!=', 'id', $keywordId]);
		}
		$keyWordData = $keyWordData->all();
		$keywordNow  = [];
		foreach ($keyWordData as $v) {
			$keywordArr = explode(',', $v->keywords);
			foreach ($keywordArr as $vv) {
				array_push($keywordNow, $vv);
			}
		}
		//关键词是否重复
		$repeatKeyword = array_intersect($keywordAdd, $keywordNow);

		if (!empty($repeatKeyword)) {
			throw new InvalidDataException('关键词‘' . implode(',', $repeatKeyword) . '’已存在，请修改后重试！');
		}

		//设置关键词及内容
		$transaction = \Yii::$app->db->beginTransaction();

		try {
			$time         = time();
			$keywordType  = $data['type'];
			$userRelation = UserCorpRelation::findOne(['corp_id' => $corp_id]);

			if ($keywordId) {
				$keyword = static::findOne($keywordId);
			} else {
				$keyword           = new WorkMsgKeywordAttachment();
				$keyword->corp_id  = $corp_id;
				$keyword->add_time = $time;
			}

			/*$attachment = [];
			foreach ($v['attachment'] as $vv) {
				$attachmentD         = [];
				$attachmentD['id']   = $vv['id'];
				$attachmentD['type'] = $vv['type'];
				$attachment[]        = $attachmentD;
			}*/

			$keyword->keywords = implode(',', $keywordAdd);
			$keyword->type     = $keywordType;
			//$keyword->attachment_ids = json_encode($attachment);
			$keyword->is_del = 0;

			if (!$keyword->save()) {
				throw new InvalidDataException(SUtils::modelError($keyword));
			}
			//关键词关联标签表置为无效
			if ($keywordId && $keywordType == 2) {
				WorkMsgKeywordTag::updateAll(['is_del' => 1], ['keyword_id' => $keywordId, 'is_del' => 0]);
			}
			//内容置为删除
			if ($keywordId) {
				WorkMsgKeywordAttachmentInfo::deleteAll(['keyword_id' => $keywordId]);
			}
			//设置数据
			foreach ($data['list'] as $listKey => $listData) {
				$keywordTagId = 0;
				if ($keywordType == 2) {
					//关键词标签表
					if (empty($listData['tag_ids'])) {
						throw new InvalidDataException('内容‘' . ($listKey + 1) . '’请选择标签！');
					}

					if ($listData['keyword_tag_id']) {
						$keywordTag = WorkMsgKeywordTag::findOne($listData['keyword_tag_id']);
					} else {
						$keywordTag           = new WorkMsgKeywordTag();
						$keywordTag->corp_id  = $corp_id;
						$keywordTag->add_time = $time;
					}
					$keywordTag->keyword_id = $keyword->id;
					$keywordTag->tags       = implode(',', $listData['tag_ids']);
					$keywordTag->is_del     = 0;
					//$keywordTag->attachment_ids = '';

					if (!$keywordTag->save()) {
						throw new InvalidDataException(SUtils::modelError($keywordTag));
					}
					$keywordTagId = $keywordTag->id;
				}

				//内容
				$i = 0;
				foreach ($listData['msgData'] as $mv) {
					if ($mv['type'] == 5) {
						foreach ($mv['newsList'] as $nv) {
							$attachmentInfo                 = new WorkMsgKeywordAttachmentInfo();
							$attachmentInfo->keyword_id     = $keyword->id;
							$attachmentInfo->keyword_tag_id = $keywordTagId;
							$attachmentInfo->type           = $mv['type'];
							$attachmentInfo->status         = 1;//默认开启
							$attachmentInfo->create_time    = DateUtil::getCurrentTime();
							if (empty($nv['is_use'])) {
								$attachment = Attachment::findOne(['id' => $nv['material_id'], 'status' => 1]);
								if (empty($attachment)) {
									if ($keywordType == 1) {
										throw new InvalidDataException('素材' . ($i + 1) . '图文素材不可用');
									} else {
										throw new InvalidDataException('内容' . ($listKey + 1) . '素材' . ($i + 1) . '图文素材不可用');
									}
								}
								$attachmentInfo->attachment_id = $attachment->id;
								$attachmentInfo->title         = $attachment->file_name;
								$attachmentInfo->digest        = $attachment->content;
								$attachmentInfo->cover_url     = $attachment->local_path;
								$attachmentInfo->content_url   = $attachment->jump_url;
								/*if (!empty($attachment->material_id) && $attachment->workMaterials->corp_id == $corp_id && !empty($attachment->workMaterials->status)) {
									$attachmentInfo->content     = $attachment->workMaterials->media_id;
									$attachmentInfo->material_id = $attachment->workMaterials->id;
								} else {
									$material = WorkMaterial::findOne(['corp_id' => $corp_id, 'attachment_id' => $attachment->id, 'status' => 1]);
									//$material = Material::getMaterial(['author_id' => $authorInfo->author_id, 'attachment_id' => $attachment->id, 'file_type' => $attachment->file_type]);
									if (!empty($material)) {
										$attachmentInfo->content     = $material->media_id;
										$attachmentInfo->material_id = $material->id;
									} else {
										$attachmentInfo->title       = $attachment->file_name;
										$attachmentInfo->digest      = $attachment->content;
										$attachmentInfo->cover_url   = $attachment->local_path;
										$attachmentInfo->content_url = $attachment->jump_url;
									}
								}*/

							} else {
								$attachmentInfo->title         = $nv['title'];
								$attachmentInfo->digest        = $nv['digest'];
								$site_url                      = \Yii::$app->params['site_url'];
								$cover_url                     = str_replace($site_url, '', $nv['cover_url']);
								$attachmentInfo->cover_url     = $cover_url;
								$attachmentInfo->content_url   = $nv['content_url'];
								$attachmentInfo->attachment_id = !empty($nv['material_id']) ? $nv['material_id'] : '';
								$attachmentInfo->is_use        = 1;
								//是否同步自定义图文
								if (!empty($nv['is_sync'])) {
									$attachmentInfo->is_sync = 1;

									if (!empty($nv['attach_id'])) {
										$attachment             = Attachment::findOne($nv['attach_id']);
										$attachment->file_name  = $nv['title'];
										$attachment->content    = $nv['digest'];
										$attachment->local_path = $cover_url;
										$attachment->jump_url   = $nv['content_url'];
										if (!empty($attachment->dirtyAttributes)) {
											$attachment              = new Attachment();
											$attachment->uid         = $userRelation->uid;
											$attachment->file_type   = 4;
											$attachment->create_time = DateUtil::getCurrentTime();
										}
										$attachment->group_id = $nv['group_id'];
									} else {
										$attachment              = new Attachment();
										$attachment->uid         = $userRelation->uid;
										$attachment->file_type   = 4;
										$attachment->create_time = DateUtil::getCurrentTime();
									}
									$attachment->group_id   = $nv['group_id'];
									$attachment->file_name  = $nv['title'];
									$attachment->content    = $nv['digest'];
									$attachment->local_path = $cover_url;
									$attachment->jump_url   = $nv['content_url'];
									if (!$attachment->validate() || !$attachment->save()) {
										throw new InvalidDataException(SUtils::modelError($attachment));
									}
									$attachmentInfo->attach_id = $attachment->id;
								}
							}
							if (!$attachmentInfo->save()) {
								throw new InvalidDataException(SUtils::modelError($attachmentInfo));
							}
							//素材类型保存!!!!!
						}
					} else {
						$attachmentInfo                 = new WorkMsgKeywordAttachmentInfo();
						$attachmentInfo->keyword_id     = $keyword->id;
						$attachmentInfo->keyword_tag_id = $keywordTagId;
						$attachmentInfo->type           = $mv['type'];
						$attachmentInfo->status         = 1;//默认开启
						$attachmentInfo->create_time    = DateUtil::getCurrentTime();

						if ($mv['type'] == 1) {
							$attachmentInfo->content       = rawurlencode($mv['content']);
							$attachmentInfo->attachment_id = $mv['material_id'];
						} elseif (in_array($mv['type'], [2, 3, 4, 5, 6, 7])) {
							//改版 采用文件柜
							$attachment = Attachment::findOne(['id' => $mv['material_id'], 'status' => 1]);
							if (empty($attachment)) {
								if ($mv['type'] == 2) {
									$msg = '图片';
								} elseif ($mv['type'] == 3) {
									$msg = '音频';
								} elseif ($mv['type'] == 4) {
									$msg = '视频';
								} elseif ($mv['type'] == 5) {
									$msg = '文件';
								} elseif ($mv['type'] == 6) {
									$msg = '小程序';
								}
								if ($keywordType == 1) {
									throw new InvalidDataException('素材' . ($i + 1) . $msg . '素材不存在');
								} else {
									throw new InvalidDataException('内容' . ($listKey + 1) . '素材' . ($i + 1) . $msg . '素材不存在');
								}
							}
							if ($mv['type'] == 6) {
								if ($mv['is_sync']) {
									$site_url = \Yii::$app->params['site_url'];
									$appId    = trim($mv['appid']);
									$appPath  = trim($mv['pagepath']);
									$title    = trim($mv['title']);
//                                    $pic_url   = trim($mv['pic_url']);
									$attach_id = $mv['material_id'];
									if (empty($appId)) {
										throw new InvalidDataException('请填写小程序appid！');
									}
									if (empty($appPath)) {
										throw new InvalidDataException('请填写小程序路径！');
									}
									if (empty($title)) {
										throw new InvalidDataException('请填写卡面标题！');
									} elseif (mb_strlen($title, 'utf-8') > 20) {
										throw new InvalidDataException('卡面标题不能超过20个字符！');
									}
									if (empty($mv['material_id'])) {
										throw new InvalidDataException('请选择卡面图片！');
									}
									$pic_url = '';
									if (!empty($attach_id)) {
										$attach = Attachment::findOne($attach_id);
										if (!empty($attach) && !empty($attach->s_local_path)) {
											$pic_url = $attach->s_local_path;
										}
									}
									if(empty($mv['attach_id'])) {
										$atta = new Attachment();
										$atta->create_time = DateUtil::getCurrentTime();
									} else {
										$atta = Attachment::findOne($mv['attach_id']);
										$atta->update_time = DateUtil::getCurrentTime();
									}
									$atta->uid          = $userRelation->uid;
									$atta->group_id     = $mv['group_id'];
									$atta->file_type    = 7;
									$atta->file_name    = $title;
									$atta->local_path   = str_replace($site_url, '', $pic_url);
									$atta->s_local_path = str_replace($site_url, '', $pic_url);
									$atta->appId        = $appId;
									$atta->appPath      = $appPath;
									$atta->attach_id    = $attach_id;
									$atta->save();
									$attachmentInfo->attach_id = $atta->id;
									$attachmentInfo->is_sync   = 1;
								}
								if ($mv['is_user'] == 0) {//导入
									$attachmentInfo->title    = $attachment->file_name;
									$attachmentInfo->appid    = $attachment->appId;
									$attachmentInfo->pagepath = $attachment->appPath;
								} else {//新建
									$attachmentInfo->title    = trim($mv['title']);
									$attachmentInfo->appid    = trim($mv['appid']);
									$attachmentInfo->pagepath = trim($mv['pagepath']);
								}
								$attachmentInfo->is_use = $mv['is_user'];
							}
							$attachmentInfo->attachment_id = $attachment->id;
							if (!empty($attachment->material_id) && $attachment->workMaterials->corp_id == $corp_id && !empty($attachment->workMaterials->status)) {
								$attachmentInfo->content     = $attachment->workMaterials->media_id;
								$attachmentInfo->material_id = $attachment->workMaterials->id;
							} else {
								$material = WorkMaterial::findOne(['corp_id' => $corp_id, 'attachment_id' => $attachment->id, 'status' => 1]);
								if (!empty($material)) {
									$attachmentInfo->content     = $material->media_id;
									$attachmentInfo->material_id = $material->id;
								}
							}
						}
						if (!$attachmentInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($attachmentInfo));
						}
					}
					$i++;
				}
			}

			$transaction->commit();
		} catch (InvalidDataException $e) {
			$transaction->rollBack();
			throw new InvalidDataException($e->getMessage());
		}

		return true;
	}
}
