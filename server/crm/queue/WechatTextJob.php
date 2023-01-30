<?php
/**
 * Create by PhpStorm
 * User: dovechen
 * Date: 2019/10/14
 * Time: 16:43
 */

namespace app\queue;

use app\models\Keyword;
use app\models\WorkPublicActivity;
use app\models\WxAuthorizeInfo;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class WechatTextJob extends BaseObject implements JobInterface
{

    public $fromUserName;
    public $toUserName;
    public $content;

    public function execute($queue)
    {
        //关键词回复
        $wxAuthorInfo = WxAuthorizeInfo::findOne(['user_name' => $this->toUserName]);
        $keyword = Keyword::find()->where(['is_del' => 0])
            ->andWhere(['author_id' => $wxAuthorInfo->author_id])
            ->andWhere(['status' => 1])
            ->andWhere("(contain_keyword like '%{$this->content}%' or FIND_IN_SET('{$this->content}', equal_keyword))")
            ->orderBy('id desc')
            ->asArray()
            ->one();
        if(!empty($keyword)) {
            \Yii::$app->queue->push(new KeywordJob([
                'activity'     => $keyword,
                'fromUserName' => $this->fromUserName
            ]));
        }
        $activity = WorkPublicActivity::find()->alias("a")
            ->leftJoin("{{%wx_authorize_info}} as b","a.public_id = b.author_id")
            ->where(["a.keyword" => $this->content,"a.is_over"=>1,"b.user_name"=>$this->toUserName])
            ->andWhere("a.start_time < UNIX_TIMESTAMP() and a.end_time > UNIX_TIMESTAMP() and a.type in (1,3) ")
            ->select("a.id")
            ->asArray()->one();
        if (!empty($activity)) {
            \Yii::$app->queue->push(new TaskTreasureJob([
                'activity'     => $activity,
                'fromUserName' => $this->fromUserName,
                'type'         => false,
            ]));
        }
        return true;
    }
}