<?php

namespace app\models;

use app\util\WorkUtils;
use dovechen\yii2\weWork\src\dataStructure\TextcardMesssageContent;
use dovechen\yii2\weWork\src\dataStructure\Message;
use Yii;

/**
 * This is the model class for table "pig_inspection_remind".
 *
 * @property int $id
 * @property int $corp_id 企业ID
 * @property int $user_id 质检人id
 * @property string $report_id 汇报对象id 用逗号隔开
 * @property string $report_name 汇报对象名称 用逗号隔开
 * @property string $report_json 汇报对象json
 * @property string $quality_id 质检对象id 用逗号隔开
 * @property string $quality_json 质检对象json
 * @property int $agent_id 应用id
 * @property int $is_cycle 周期 0每天 1每周
 * @property int $status 推送是否开启 0关闭 1开启
 * @property string $create_time 创建时间
 * @property int $is_delete 是否删除 0否 1是
 */
class InspectionRemind extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%inspection_remind}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'agent_id', 'is_cycle', 'status', 'is_delete'], 'integer'],
            [['report_id', 'report_name', 'report_json', 'quality_id', 'quality_json'], 'required'],
            [['report_id', 'report_name', 'report_json', 'quality_id', 'quality_json'], 'string'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'user_id'     => Yii::t('app', '质检人id'),
            'report_id'   => Yii::t('app', '汇报对象id 用逗号隔开'),
            'report_name' => Yii::t('app', '汇报对象名称 用逗号隔开'),
            'report_json' => Yii::t('app', '汇报对象json'),
            'quality_id'  => Yii::t('app', '质检对象id 用逗号隔开'),
            'quality_json'=> Yii::t('app', '质检对象json'),
            'agent_id'    => Yii::t('app', '应用id'),
            'is_cycle'    => Yii::t('app', '周期 0每天 1每周'),
            'status'      => Yii::t('app', '推送是否开启 0关闭 1开启'),
            'create_time' => Yii::t('app', '创建时间'),
            'is_delete'   => Yii::t('app', '是否删除 0否 1是'),
        ];
    }

    /*
     * 获取质检对象
     */
    public function quality($user_id)
    {
        $remind = InspectionRemind::find()->select('quality_id')->where(['user_id' => $user_id])->andWhere(['is_delete' => 0])->asArray()->all();
        $arr = [];
        foreach ($remind as $key => $val) {
            $arr = array_merge($arr, explode(',' ,$val['quality_id']));
        }
        $arr = array_unique($arr);
        return $arr;
    }

    /**
     * 获取质检数据下的质检对象该段时间产生的有效会话数
     */
    public function getConversation($remind_id, $date = '', $quality_id = [])
    {
        $InspectionRemind = InspectionRemind::findOne($remind_id);
        if(empty($quality_id)) {
            //查询被质检人员数据
            $quality_id = explode(',', $InspectionRemind->quality_id);
        }
        $quality_count= count($quality_id);//被质检人数
        //判断推送周期
        [$start_date, $end_date, $start_date_time, $end_date_time] = $this->getDate($InspectionRemind->is_cycle, $date);
        $external_mag_count = $this->getConversationCount($quality_id, $InspectionRemind->user_id, $start_date, $end_date);
        $chat_mag_count     = $this->getConversationCount($quality_id, $InspectionRemind->user_id, $start_date, $end_date, 0);
        $conversation_count = $this->getConversationViolation($InspectionRemind->user_id, $quality_id, $start_date, $end_date);
        $violation_count    = $this->getViolationCount($InspectionRemind->user_id, $quality_id, $start_date_time, $end_date_time);

        return [$quality_count, $external_mag_count, $chat_mag_count, $conversation_count, $violation_count];
    }

    public function getDate($is_cycle = 0, $date)
    {
        if($is_cycle == 0) {//每天推送
            $start_date = date("Y-m-d", strtotime("-1 day", strtotime($date)));
            $end_date = $start_date.' 23:59:59';
        } else {
            $start_date = date("Y-m-d", strtotime("-7 day", strtotime($date)));
            $end = date("Y-m-d", strtotime("-1 day", strtotime($date)));
            $end_date = $end.' 23:59:59';
        }
        $start_date_time    = strtotime($start_date)*1000;
        $end_date_time      = strtotime($end_date)*1000;
        return [$start_date, $end_date, $start_date_time, $end_date_time];
    }

    /**
     * 获取质检客户于群聊
     */
    public function getConversationCount($quality_id, $user_id, $start_date, $end_date, $to_type = 1)
    {
        $que = InspectionViolation::find()
            ->select('id')
            ->where(['quality_id' => $quality_id])
            ->andWhere(['between', 'create_time', $start_date, $end_date])
            ->andWhere(['user_id' => $user_id])
            ->andWhere(['is_delete' => 0]);
        if($to_type == 1) {
            $que->andWhere(['msg_type' => 0])
                ->groupBy('to_user_id');
        } else {
            $que->andWhere(['msg_type' => 1])
                ->groupBy('roomid');
        }
        $external_mag = $que->asArray()
            ->all();
        return count($external_mag);
    }

    /**
     * 会话不合规条数
     */
    public function getConversationViolation($user_id ,$quality_id, $start_date, $end_date)
    {
        return InspectionViolation::find()
            ->where(['user_id' => $user_id])
            ->andWhere(['quality_id' => $quality_id])
            ->andWhere('work_msg_audit_info_id > 0')
            ->andWhere(['is_delete' => 0])
            ->andWhere(['between', 'create_time', $start_date, $end_date])
            ->count();
    }

    /**
     * 质检会话数
     */
    public function getViolationCount($user_id, $quality_id, $start_date, $end_date)
    {
        $violation = WorkMsgAuditInfo::find()
            ->select('to_external_id,roomid')
            ->where(['from_type' => 1])
            ->andWhere(['to_type' => [0, 2]])
            ->andWhere(['user_id' => 21])
            ->andWhere(['msgtype' => 'text'])
            ->andWhere(['between', 'msgtime', $start_date, $end_date])
            ->groupBy('to_external_id,roomid')
            ->count();

        return $violation;
    }

    /**
     * 获取质检对象
     */
    public function getQualityName($quality_id)
    {
        $woriUser = WorkUser::find()
            ->select(['id', 'name'])
            ->where(['id' => $quality_id])
            ->limit(5)
            ->asArray()
            ->all();

        return array_column($woriUser, 'name');
    }

    /**
     * 敏感词监控
     */
    public function getLimitWorkCount($quality_id, $is_cycle, $date, $corp_id)
    {
        [$start_date, $end_date, $start_date_time, $end_date_time] = $this->getDate($is_cycle, $date);

        return LimitWordMsg::find()
            ->alias('a')
            ->leftJoin(WorkMsgAuditInfo::tableName().' b', 'b.id = a.audit_info_id')
            ->where(['b.user_id' => $quality_id])
            ->andWhere(['action' => 'send'])
            ->andWhere(['from_type' => 1])
            ->andWhere(['a.corp_id' => $corp_id])
            ->andWhere(['between', 'msgtime', $start_date_time, $end_date_time])
            ->count();
    }

    /**
     * 质检汇报
     */
    public function getTestingReport()
    {
        $da = date("w");
        if($da == 1) {//周一
            $is_cycle = [0, 1];
        } else {
            $is_cycle = 0;
        }
        $remind = InspectionRemind::find()
            ->where(['is_delete' => 0])
            ->andWhere(['is_cycle' => $is_cycle])
            ->andWhere(['status' => 1])
            ->asArray()
            ->all();
        $date = date('Y-m-d');
        foreach ($remind as $key => $val) {
            try {
                //质检对象
                $quality_id    = explode(',', $val['quality_id']);
                $quality_count = count($quality_id);//被质检人数
                if($quality_count == 0) {
                    continue;
                }
                $WorkUser = WorkUser::findOne($val['user_id']);
                $quality_name     = $this->getQualityName($quality_id);
                $arr              = $this->getConversation($val['id'], $date);
                $limit_work_count = $this->getLimitWorkCount($quality_id, $val['is_cycle'], $date, $val['corp_id']);
                $MessageContent   = $this->getMessageContent($WorkUser->name, $val['is_cycle'], 1, $val['id'], $quality_name, $arr, $limit_work_count);
                $reportUser       = WorkUser::find()->where(['id' => $val['report_id']])->asArray()->all();
                $touser           = array_column($reportUser, 'userid');
                $this->getSend($MessageContent, $touser, $val['corp_id'], $val['agent_id']);
            } catch (\Exception $e) {
                \Yii::error($e->getMessage(), '$TestingReport');
            }
        }
        return true;
    }

    /**
     * 质检结果反馈
     */
    public function getQualitySned()
    {
        $da = date("w");
        if($da == 1) {//周一
            $is_cycle = [0, 1];
        } else {
            $is_cycle = 0;
        }
        $remind = InspectionRemind::find()
            ->where(['is_delete' => 0])
            ->andWhere(['is_cycle' => $is_cycle])
            ->andWhere(['status' => 1])
            ->asArray()
            ->all();
        foreach ($remind as $key => $val) {
            try {
                $quality_id    = explode(',', $val['quality_id']);
                $quality_count = count($quality_id);//被质检人数
                if($quality_count == 0) {
                    continue;
                }
                $WorkUser = WorkUser::findOne($val['user_id']);
                $MessageContent = $this->getMessageContent($WorkUser->name, $val['is_cycle'], 0, $val['id']);
                [$start_date, $end_date, $start_date_time, $end_date_time] = $this->getDate($val['is_cycle'], date('Y-m-d'));
                $violation_quality = InspectionViolation::find()
                    ->select(['quality_id'])
                    ->where(['user_id' => $val['user_id']])
                    ->andWhere(['is_delete' => 0])
                    ->andWhere(['status' => 1])
                    ->andWhere(['quality_id' => $quality_id])
                    ->andWhere(['between', 'create_time', $start_date, $end_date])
                    ->groupBy('quality_id')
                    ->asArray()
                    ->all();
                if(empty($violation_quality)) {
                    continue;
                }
                $send_quality_id = array_column($violation_quality, 'quality_id');
                $reportUser     = WorkUser::find()->where(['id' => $send_quality_id])->asArray()->all();
                $touser         = array_column($reportUser, 'userid');
                $this->getSend($MessageContent, $touser, $val['corp_id'], $val['agent_id']);
            } catch (\Exception $e) {
                \Yii::error($e->getMessage(), '$qualitySned');
            }
        }

        return true;
    }

    /**
     * 获取内容格式
     */
    public function getMessageContent($user_name, $is_cycle, $type = 1, $remind_id, $quality_name = [], $arr = [], $limit_work_count = 0)
    {
        [$start_date, $end_date, $start_date_time, $end_date_time] = $this->getDate($is_cycle, date('Y-m-d'));
        if($is_cycle == 0) {
            $time = $start_date;
        } else {
            $time = $start_date.'~'.$end_date;
        }
        $remind = InspectionRemind::findOne($remind_id);
        $corp = WorkCorp::findOne($remind->corp_id);
        $corpid = $corp->corpid;
        $agentId = $remind->agent_id;
        if($type == 1) {
            $quality_name = implode(',', $quality_name);
            if($arr['quality_count'] > 5) {
                $quality_name = $quality_name.'等'.$arr[0].'位成员';
            }
            $messageContent = [
                'title'       => $user_name.'的质检汇报',
                'description' => "<div class=\"gray\">". date("Y年m月d日") ."</div>
            \r\n质检周期：{$time}\r\n质检对象：{$quality_name}，共计质检{$arr[1]}个客户、{$arr[2]}个群聊\r\n敏感词监控：共计自动触发{$limit_work_count}条\r\n内容质检结果：存在不合规会话{$arr[3]}条，未审核会话{$arr[4]}个。",
                'url'         => \Yii::$app->params["web_url"].'/h5/pages/quality/violation?remind_id='.$remind_id.'&date='.date('Y-m-d').'&corpid='.$corpid.'&agentId='.$agentId,
            ];
        } else {
            $messageContent = [
                'title'       => '会话质检结果反馈',
                'description' => "<div class=\"gray\">". date("Y年m月d日") ."</div>
            \r\n质检人：{$user_name}\r\n质检周期：{$time}",
                'url'         => \Yii::$app->params["web_url"].'/h5/pages/quality/inspection?remind_id='.$remind_id.'&date='.date('Y-m-d').'&corpid='.$corpid.'&agentId='.$agentId,
            ];
        }
        return $messageContent;
    }

    public function getSend($messageContent, $touser, $corp_id, $agentid)
    {
        $workApi = WorkUtils::getAgentApi($corp_id, $agentid);

        $agent = WorkCorpAgent::findOne($agentid);
        $messageContent = TextcardMesssageContent::parseFromArray($messageContent);
        $message        = [
            'touser'                   => $touser,
            'toparty'                  => [],
            'totag'                    => [],
            'agentid'                  => $agent->agentid,
            'messageContent'           => $messageContent,
            'duplicate_check_interval' => 10,
        ];

        $message = Message::pareFromArray($message);
        $workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);

        return true;
    }
}
