<?php
/**
 * Create by PhpStorm
 * User: dovechen
 * Date: 2019/10/14
 * Time: 16:43
 */

namespace app\queue;

use app\models\Fans;
use app\models\ReplyInfo;
use app\models\WxAuthorize;
use app\util\MsgUtil;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class KeywordJob extends BaseObject implements JobInterface
{

    public $activity;
    public $fromUserName;

    public function execute($queue)
    {
        if(isset($this->activity['menu'])){
            $rely = ReplyInfo::find()->where(['menu_keyword_id' => $this->activity['id']])->asArray()->all();
        }else{
            $rely = ReplyInfo::find()->where(['kw_id' => $this->activity['id']])->asArray()->all();
        }
        if(empty($rely)) {
            return true;
        }
        $wxConfig = WxAuthorize::find()->where(['author_id' => $this->activity['author_id']])->select("authorizer_appid")->asArray()->one();
        if($this->activity['reply_mode'] == 1) {//全部回复
            foreach ($rely as $key => $val) {
                if($val['type'] == 1) {
                    $msgType = 1;
                    $content = rawurldecode($val['content']);
                    if (strpos($content, '{nickname}') !== false) {
                        $fans = Fans::find()->where(['openid' => $this->fromUserName])->one();
                        if($fans) {
                            $nickname = $fans->nickname;
                            $content  = str_replace("{nickname}", $nickname, $content);
                        } else {
                            $content  = str_replace("{nickname}", '', $content);
                        }
                    }
                    $msgContent = ['text' => $content];
                } else if($val['type'] == 2) {//图片
                    $msgType = 2;
                    $msgContent = ['media_id' => $val['material_id']];
                } else if($val['type'] == 3) {//语音
                    $msgType = 3;
                    $msgContent = ['media_id' => $val['material_id']];
                } else if($val['type'] == 4) {//视频
                    $msgType = 4;
                    $msgContent = ['media_id' => $val['material_id']];
                } else if($val['type'] == 5) {//图文
                    $msgType = 5;
                    $msgContent = [
                        'media_id' => $val['material_id'],
                        'title' => $val['title'],
                        'description' => $val['digest'],
                        'url' => $val['content_url'],
                        'pic_url' => \Yii::$app->params['site_url'].$val['cover_url'],
                    ];
                } else if($val['type'] == 6) {//小程序
                    $msgType = 10;
                    $msgContent = [
                        'media_id' => $val['material_id'],
                        'title' => $val['title'],
                        'appid' => $val['appid'],
                        'pagepath' => $val['pagepath'],
                    ];
                } else {
                    break;
                }
                MsgUtil::send($wxConfig["authorizer_appid"], $this->fromUserName, $msgType, $msgContent, false);
            }
        } else {//随机回复
            $oen_rely = $rely[array_rand($rely)];
            if($oen_rely['type'] == 1) {
                $msgType = 1;
                $content = rawurldecode($oen_rely['content']);
                if (strpos($content, '{nickname}') !== false) {
                    $fans = Fans::find()->where(['openid' => $this->fromUserName])->one();
                    if($fans) {
                        $nickname = $fans->nickname;
                        $content  = str_replace("{nickname}", $nickname, $content);
                    } else {
                        $content  = str_replace("{nickname}", '', $content);
                    }
                }
                $msgContent = ['text' => $content];
            } else if($oen_rely['type'] == 2) {//图片
                $msgType = 2;
                $msgContent = ['media_id' => $oen_rely['material_id']];
            } else if($oen_rely['type'] == 3) {//语音
                $msgType = 3;
                $msgContent = ['media_id' => $oen_rely['material_id']];
            } else if($oen_rely['type'] == 4) {//视频
                $msgType = 4;
                $msgContent = ['media_id' => $oen_rely['material_id']];
            } else if($oen_rely['type'] == 5) {//图文
                $msgType = 5;
                $msgContent = [
                    'media_id' => $oen_rely['material_id'],
                    'title' => $oen_rely['title'],
                    'description' => $oen_rely['digest'],
                    'url' => $oen_rely['content_url'],
                    'pic_url' => \Yii::$app->params['site_url'].$oen_rely['cover_url'],
                ];
            } else if($oen_rely['type'] == 6) {//小程序
                $msgType = 10;
                $msgContent = [
                    'media_id' => $oen_rely['material_id'],
                    'title' => $oen_rely['title'],
                    'appid' => $oen_rely['appid'],
                    'pagepath' => $oen_rely['pagepath'],
                ];
            } else {
                return true;
            }
            MsgUtil::send($wxConfig["authorizer_appid"], $this->fromUserName, $msgType, $msgContent, false);
        }
    }
}