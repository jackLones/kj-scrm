<?php
/**
 * Create by PhpStorm
 * User: wangpan
 * Date: 2019/11/14
 * Time: 09:35
 */

namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\components\NotAllowException;
use app\models\Article;
use app\models\Attachment;
use app\models\Keyword;
use app\models\UserAuthorRelation;
use app\models\WechatMenus;
use app\models\WorkPublicActivity;
use app\models\WxAuthorize;
use app\modules\api\components\AuthBaseController;
use app\models\Material;
use app\models\ReplyInfo;
use app\queue\KeywordJob;
use app\util\SUtils;
use app\util\DateUtil;
use callmez\wechat\sdk\Wechat;
use yii\data\Pagination;

class KeywordController extends AuthBaseController
{
    /**
     * 添加规则
     */
    public function actionAdd()
    {
        if (\Yii::$app->request->isPost) {
            if (empty($this->wxAuthorInfo)) {
                throw new InvalidParameterException('参数不正确！');
            }
            $id = \Yii::$app->request->post('id');
            $reply_mode = \Yii::$app->request->post('reply_mode');//1 全部回复 2 随机回复其中一条
            $equal_keyword = \Yii::$app->request->post('equal_keyword');
            $contain_keyword = \Yii::$app->request->post('contain_keyword');
            $title = \Yii::$app->request->post('title');
            $uid = \Yii::$app->request->post('uid');
            $msgData = \Yii::$app->request->post('list');
            $authorInfo = $this->wxAuthorInfo->author;
            if (empty($title)) {
                throw new InvalidParameterException('名称不能为空！');
            }
            if (empty($msgData)) {
                throw new InvalidParameterException('推送内容不能为空！');
            }
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $reply_info_id = [];
                if ($id) {
                    $reply_info = ReplyInfo::find()->andWhere(['kw_id' => $id])->asArray()->all();
                    if (!empty($reply_info)) {
                        foreach ($reply_info as $info) {
                            array_push($reply_info_id, $info['id']);
                        }
                    }
                    $Keyword = Keyword::findOne($id);
                    $Keyword->rule_name = $title;
                    $Keyword->reply_mode = $reply_mode;
                    $Keyword->equal_keyword = implode(',', $equal_keyword);
                    $Keyword->contain_keyword = implode(',', $contain_keyword);
                    $Keyword->create_time = DateUtil::getCurrentTime();
                    $Keyword->save();
                } else {
                    $keyword = Keyword::find()->where(['rule_name' => $title])->andWhere(['is_del' => 0])->andWhere(['author_id' => $authorInfo->author_id])->one();
                    if ($keyword) {
                        throw new InvalidParameterException('规则名称重复！');
                    }
                    $Keyword = new Keyword();
                    $Keyword->author_id = $authorInfo->author_id;
                    $Keyword->rule_name = $title;
                    $Keyword->reply_mode = $reply_mode;
                    $Keyword->status = 1;
                    $Keyword->equal_keyword = implode(',', $equal_keyword);
                    $Keyword->contain_keyword = implode(',', $contain_keyword);
                    $Keyword->create_time = DateUtil::getCurrentTime();
                    $Keyword->save();
                }
                $i = 0;
                foreach ($msgData as $mv) {
                    if ($mv['type'] == 5) {
                        foreach ($mv['newsList'] as $nv) {
                            $reply = new ReplyInfo();
                            $reply->kw_id = $Keyword->id;
                            $reply->type = $mv['type'];
                            $reply->status = 1;//默认开启
                            $reply->create_time = DateUtil::getCurrentTime();
                            if (empty($nv['is_use'])) {
                                //改版 采用文件柜
                                $attachment = Attachment::findOne(['id' => $nv['material_id'], 'status' => 1]);
                                if (empty($attachment)) {
                                    throw new InvalidDataException('内容' . ($i + 1) . '图文素材不存在');
                                }
                                $reply->attachment_id = $attachment->id;
                                if (!empty($attachment->material_id) && $attachment->material->author_id == $authorInfo->author_id && !empty($attachment->material->status)) {
                                    $reply->content = $attachment->material->media_id;
                                    $reply->material_id = $attachment->material->id;
                                } else {
                                    $material = Material::getMaterial(['author_id' => $authorInfo->author_id, 'attachment_id' => $attachment->id, 'file_type' => $attachment->file_type]);
                                    if (!empty($material)) {
                                        $reply->content = $material->media_id;
                                        $reply->material_id = $material->id;
                                    } else {
                                        $reply->title = $attachment->file_name;
                                        $reply->digest = $attachment->content;
                                        $reply->cover_url = $attachment->local_path;
                                        $reply->content_url = $attachment->jump_url;
                                    }
                                }

                            } else {
                                $reply->title = $nv['title'];
                                $reply->digest = $nv['digest'];
                                $site_url = \Yii::$app->params['site_url'];
                                $cover_url = str_replace($site_url, '', $nv['cover_url']);
                                $reply->cover_url = $cover_url;
                                $reply->content_url = $nv['content_url'];
                                //$reply->material_id = !empty($nv['material_id']) ? $nv['material_id'] : '';
                                $reply->attachment_id = !empty($nv['material_id']) ? $nv['material_id'] : '';
                                $reply->is_use = 1;
                                //是否同步自定义图文
                                if (!empty($nv['is_sync'])) {
                                    $reply->is_sync = 1;

                                    $userRelation = UserAuthorRelation::findOne(['author_id' => $authorInfo->author_id]);
                                    if (!empty($nv['attach_id'])) {
                                        $attachment = Attachment::findOne($nv['attach_id']);
                                        $attachment->file_name = $nv['title'];
                                        $attachment->content = $nv['digest'];
                                        $attachment->local_path = $cover_url;
                                        $attachment->jump_url = $nv['content_url'];
                                        if (!empty($attachment->dirtyAttributes)) {
                                            $attachment = new Attachment();
                                            $attachment->uid = $userRelation->uid;
                                            $attachment->file_type = 4;
                                            $attachment->create_time = DateUtil::getCurrentTime();
                                        }
                                        $attachment->group_id = $nv['group_id'];
                                    } else {
                                        $attachment = new Attachment();
                                        $attachment->uid = $userRelation->uid;
                                        $attachment->file_type = 4;
                                        $attachment->create_time = DateUtil::getCurrentTime();
                                    }
                                    $attachment->group_id = $nv['group_id'];
                                    $attachment->file_name = $nv['title'];
                                    $attachment->content = $nv['digest'];
                                    $attachment->local_path = $cover_url;
                                    $attachment->jump_url = $nv['content_url'];
                                    if (!$attachment->validate() || !$attachment->save()) {
                                        throw new InvalidDataException(SUtils::modelError($attachment));
                                    }
                                    $reply->attach_id = $attachment->id;
                                }
                            }
                            if (!$reply->save()) {
                                throw new InvalidDataException(SUtils::modelError($reply));
                            }
                        }
                    } else {
                        $reply = new ReplyInfo();
                        $reply->kw_id = $Keyword->id;
                        $reply->type = $mv['type'];
                        $reply->status = 1;//默认开启
                        $reply->create_time = DateUtil::getCurrentTime();
                        if ($mv['type'] == 1) {
                            $reply->content = rawurlencode($mv['content']);
                        } elseif ($mv['type'] == 2 || $mv['type'] == 3 || $mv['type'] == 4 || $mv['type'] == 6) {
                            //改版 采用文件柜
                            $attachment = Attachment::findOne(['id' => $mv['material_id'], 'status' => 1]);
                            if (empty($attachment)) {
                                if ($mv['type'] == 2) {
                                    $msg = '图片';
                                } elseif ($mv['type'] == 3) {
                                    $msg = '音频';
                                } elseif ($mv['type'] == 4) {
                                    $msg = '视频';
                                } elseif ($mv['type'] == 6) {
                                    $msg = '小程序';
                                }
                                throw new InvalidDataException('内容' . ($i + 1) . $msg . '素材不存在');
                            }
                            if ($mv['type'] == 6) {
                                if($mv['is_sync']) { //是否同步自定义图文
                                    $site_url = \Yii::$app->params['site_url'];
                                    $appId     = trim($mv['appid']);
                                    $appPath   = trim($mv['pagepath']);
                                    $title     = trim($mv['title']);
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
                                    $atta->uid  = $uid;
                                    $atta->group_id  = $mv['group_id'];
                                    $atta->file_type  = 7;
                                    $atta->file_name  = $title;
                                    $atta->local_path = str_replace($site_url, '', $pic_url);
                                    $atta->s_local_path = str_replace($site_url, '', $pic_url);
                                    $atta->appId      = $appId;
                                    $atta->appPath    = $appPath;
                                    $atta->attach_id  = $attach_id;
                                    $atta->save();
                                    $reply->attach_id = $atta->id;
                                    $reply->is_sync = 1;
                                }
                                if ($mv['is_user'] == 0) {//导入
                                    $reply->title = $attachment->file_name;
                                    $reply->appid = $attachment->appId;
                                    $reply->pagepath = $attachment->appPath;
                                } else {//新建
                                    $reply->title = trim($mv['title']);
                                    $reply->appid = trim($mv['appid']);
                                    $reply->pagepath = trim($mv['pagepath']);
                                }
                                $reply->is_use = $mv['is_user'];
                            }
                            $reply->attachment_id = $attachment->id;
                            if (!empty($attachment->material_id) && $attachment->material->author_id == $authorInfo->author_id && !empty($attachment->material->status)) {
                                $reply->content = $attachment->material->media_id;
                                $reply->material_id = $attachment->material->id;
                            } else {
                                //$material = Material::findOne(['author_id'=>$authorInfo->author_id,'attachment_id' => $attachment->id,'status'=>1]);
                                $material = Material::getMaterial(['author_id' => $authorInfo->author_id, 'attachment_id' => $attachment->id, 'file_type' => $attachment->file_type]);
                                if (!empty($material)) {
                                    $reply->content = $material->media_id;
                                    $reply->material_id = $material->id;
                                }else{
                                    $material = (new WechatMenus())->uploadMedia($attachment);
                                    $reply->content = $material->media_id;
                                    $reply->material_id = $material->id;
                                }
                            }
                        }
                        if (!$reply->save()) {
                            throw new InvalidDataException(SUtils::modelError($reply));
                        }
                    }
                    $i++;
                }
                $transaction->commit();
                if (!empty($reply_info_id)) {
                    ReplyInfo::deleteAll(['id' => $reply_info_id]);
                }
            } catch (InvalidDataException $e) {
                $transaction->rollBack();
                throw new InvalidDataException($e->getMessage());
            }
        }
        return;
    }

    /**
     * 规则列表
     */
    public function actionList()
    {
        if (\Yii::$app->request->isGet) {
            throw new InvalidDataException("请求方式出错");
        }
        $name = \Yii::$app->request->post('name');
        $page = \Yii::$app->request->post('page'); //分页
        $pageSize = \Yii::$app->request->post('pageSize'); //页数
        $page = !empty($page) ? $page : 1;
        $pageSize = !empty($pageSize) ? $pageSize : 10;
        $offset = ($page - 1) * $pageSize;

        $authorInfo = $this->wxAuthorInfo->author;
        $Keyword = Keyword::find()->where(['is_del' => 0])
            ->andWhere(['author_id' => $authorInfo->author_id])
            ->andFilterWhere(['like', 'rule_name', $name]);
        $count = $Keyword->count();

        $list = $Keyword->limit($pageSize)->offset($offset)->orderBy('id desc')->asArray()->all();

        foreach ($list as $key => $val) {
            $list[$key]['equal_keyword'] = empty($val['equal_keyword']) ? [] : explode(',', $val['equal_keyword']);
            $list[$key]['contain_keyword'] = empty($val['contain_keyword']) ? [] : explode(',', $val['contain_keyword']);
            $info = ReplyInfo::find()->select('type')->where(['kw_id' => $val['id']])->asArray()->all();
            $info_type = array_column($info, 'type');
            $info_type = array_unique($info_type);
            $type_text = '';
            foreach ($info_type as $k => $v) {
                if ($v == 1) {
                    $type_text .= '文本|';
                } else if ($v == 2) {
                    $type_text .= '图片|';
                } else if ($v == 3) {
                    $type_text .= '语音|';
                } else if ($v == 4) {
                    $type_text .= '视频|';
                } else if ($v == 5) {
                    $type_text .= '图文|';
                } else if ($v == 6) {
                    $type_text .= '小程序|';
                }
            }
            $type_text = substr($type_text, 0, -1);
            $list[$key]['type_text'] = $type_text;
        }

        return ['data' => $list, 'count' => $count,];
    }

    /**
     * 删除
     */
    public function actionDelete()
    {
        if (\Yii::$app->request->isGet) {
            throw new InvalidDataException("请求方式出错");
        }
        $id = \Yii::$app->request->post('id');
        $Keyword = Keyword::findOne($id);
        $Keyword->is_del = 1;
        $Keyword->save();

        return;
    }

    /**
     * 规则状态
     */
    public function actionKeywordStatus()
    {
        if (\Yii::$app->request->isGet) {
            throw new InvalidDataException("请求方式出错");
        }

        $id = \Yii::$app->request->post('id');
        $status = \Yii::$app->request->post('status');
        if (!in_array($status, [0, 1])) {
            throw new InvalidDataException("参数错误");
        }
        $Keyword = Keyword::findOne($id);
        $Keyword->status = $status;
        $Keyword->save();

        return;
    }

    /**
     * 详情
     */
    public function actionReplyInfo()
    {
        if (\Yii::$app->request->isPost) {
            $id = \Yii::$app->request->post('id');
            if (empty($id)) {
                throw new InvalidDataException('参数不正确');
            }
            $result = [];
            try {
                $interact = Keyword::findOne($id);
                $result['title'] = $interact->rule_name;
                $result['reply_mode'] = $interact->reply_mode;

                $result['equal_keyword'] = empty($interact->equal_keyword) ? [] : explode(',', $interact->equal_keyword);
                $result['contain_keyword'] = empty($interact->contain_keyword) ? [] : explode(',', $interact->contain_keyword);
                $content = [];

                $replyInfo = ReplyInfo::find()->andWhere(['kw_id' => $id])->asArray()->all();
                $replyList = [];
                $typeNum = 0;
                $sketchId = 0;
                foreach ($replyInfo as $rv) {
                    if ($rv['type'] == 5) {
                        if (!empty($rv['material_id']) && empty($rv['title'])) {
                            $material = Material::findOne(['id' => $rv['material_id']]);
                            $article = Article::find()->alias('a');
                            $article = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
                            $article = $article->where(['a.id' => $material->article_sort])->select('a.title,a.digest,a.content_source_url,m.local_path')->asArray()->one();

                            if (strpos($article['local_path'], 'http') === false) {
                                $article['local_path'] = \Yii::$app->params['site_url'] . $article['local_path'];
                            }

                            $replyList[$rv['id']]['sketchList'][] = ['id' => $sketchId, 'addType' => 0, 'inputTitle' => $article['title'], 'digest' => $article['digest'], 'content_source_url' => $article['content_source_url'], 'material_id' => $rv['attachment_id'], 'local_path' => ['img' => $article['local_path'], 'audio' => '']];
                            $replyList[$rv['id']]['typeValue'] = (int)$rv['type'];
                        } else {
                            if (strpos($rv['cover_url'], 'http') === false) {
                                $rv['cover_url'] = \Yii::$app->params['site_url'] . $rv['cover_url'];
                            }
                            $material_id = !empty($rv['attachment_id']) ? $rv['attachment_id'] : 0;
                            $group_id = "";
                            $attach_id = "";
                            if (empty($rv['is_use'])) {
                                $addType = 0;
                                $is_sync = 0;
                            } else {
                                $addType = 1;
                                $is_sync = (int)$rv['is_sync'];
                                if (!empty($is_sync)) {
                                    $attach_id = $rv['attach_id'];
                                    $attachment = Attachment::findOne($rv['attach_id']);
                                    $group_id = (string)$attachment->group_id;
                                }
                            }
                            $replyList[$rv['id']]['sketchList'][] = ['id' => $sketchId, 'addType' => $addType, 'inputTitle' => $rv['title'], 'digest' => $rv['digest'], 'content_source_url' => $rv['content_url'], 'material_id' => $material_id, 'local_path' => ['img' => $rv['cover_url'], 'audio' => ''], 'is_sync' => $is_sync, 'group_id' => $group_id, 'attach_id' => $attach_id];
                            $replyList[$rv['id']]['typeValue'] = (int)$rv['type'];
                        }
                        $sketchId++;
                    } elseif ($rv['type'] == 1) {
                        $replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int)$rv['type'], 'file_name' => '', 'material_id' => 0, 'local_path' => ['img' => '', 'audio' => ''], 'sketchList' => [], 'textAreaValueHeader' => rawurldecode($rv['content'])];
                        $typeNum++;
                    } elseif ($rv['type'] == 6) {
                        if (!empty($rv['attachment_id'])) {
                            $temp = Attachment::findOne(['id' => $rv['attachment_id']]);
                            $cover_url = !empty($temp->local_path) ? $temp->local_path : '';
                            if (strpos($cover_url, 'http') === false) {
                                $cover_url = \Yii::$app->params['site_url'] . $cover_url;
                            }
                        } else {
                            $cover_url = '';
                        }
                        $group_id = "";
                        $attach_id = "";
                        if (empty($rv['is_use'])) {
                            $is_sync = 0;
                        } else {
                            $is_sync = (int)$rv['is_sync'];
                            if (!empty($is_sync)) {
                                $attach_id = $rv['attach_id'];
                                $attachment = Attachment::findOne($rv['attach_id']);
                                $group_id = (string)$attachment->group_id;
                            }
                        }
                        $replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int)$rv['type'], 'is_user' => $rv['is_use'], 'is_sync' => $is_sync, 'group_id' => $group_id, 'attach_id' => $attach_id, 'appid' => $rv['appid'], 'pagepath' => $rv['pagepath'], 'file_name' => $rv['title'], 'material_id' => $rv['attachment_id'], 'local_path' => $cover_url, 'sketchList' => [], 'textAreaValueHeader' => ''];
                        $typeNum++;
                    } else {
                        if (!empty($rv['attachment_id'])) {
                            $temp = Attachment::findOne(['id' => $rv['attachment_id']]);
                            $cover_url = !empty($temp->local_path) ? $temp->local_path : '';
                            if (strpos($cover_url, 'http') === false) {
                                $cover_url = \Yii::$app->params['site_url'] . $cover_url;
                            }
                            $file_name = $temp->file_name;
                        } else {
                            $cover_url = '';
                            $file_name = '';
                        }

                        if ($rv['type'] == 2) {
                            $local_path = ['img' => $cover_url, 'audio' => ''];
                        } else {
                            $local_path = ['img' => '', 'audio' => $cover_url];
                        }
                        $replyList[$rv['id']] = ['id' => $typeNum, 'typeValue' => (int)$rv['type'], 'file_name' => $file_name, 'material_id' => $rv['attachment_id'], 'local_path' => $local_path, 'sketchList' => [], 'textAreaValueHeader' => ''];
                        $typeNum++;
                    }
                }
                $replyList = array_values($replyList);
                $content['replyList'] = $replyList;

                $result['content'] = $content;
                return $result;
            } catch (InvalidDataException $e) {
                throw new InvalidDataException($e->getMessage());
            }
        } else {
            throw new NotAllowException("请求方式不正确");
        }

    }
}