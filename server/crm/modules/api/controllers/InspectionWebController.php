<?php
/**
 * Create by PhpStorm
 * User: dovechen
 * Date: 2020/9/5
 * Time: 16:37
 */

namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\models\InspectionRemind;
use app\models\WorkMsgAuditInfo;
use app\models\InspectionViolation;
use app\models\InspectionViolationClassify;
use app\models\WorkCorp;
use app\models\WorkDepartment;
use app\models\WorkExternalContact;
use app\models\WorkMsgAuditUser;
use app\models\WorkUser;
use app\modules\api\components\BaseController;
use yii\data\Pagination;
use yii\web\MethodNotAllowedHttpException;

class InspectionWebController extends BaseController
{
    /**
     * 质检汇报
     */
    public function actionReportList()
    {
        if (\Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $remind_id = \Yii::$app->request->get('remind_id', 0);
        $user_id   = \Yii::$app->request->get('user_id', 0);
        $date      = \Yii::$app->request->get('date');
        $pageSize  = \Yii::$app->request->get('pageSize', 10);
        $page      = \Yii::$app->request->get('page', 1);
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $remind = InspectionRemind::findOne($remind_id);
        if (empty($remind)) {
            throw new InvalidDataException('参数不正确');
        }
        $remind_user_id = $remind->user_id;
        //判断该用户是否拥有查看此条质检的权限
        $report_id = explode(',', $remind->report_id);
        if (!in_array($user_id, $report_id)) {
            return ["error" => 1, "msg" => "暂无权限"];
        }
        $data = [];
        [$start_date, $end_date, $start_date_time, $end_date_time] = $remind->getDate($remind->is_cycle, $date);
        if ($page == 1) {
            [$quality_count, $external_mag_count, $chat_mag_count, $conversation_count, $violation_count] = $remind->getConversation($remind_id, $date);
            $wrkUser = WorkUser::findOne($remind->user_id);
            if ($remind->is_cycle == 0) {
                $date_connt = $start_date;
            } else {
                $date_connt = $start_date . '-' . date('Y-m-d', strtotime($end_date));
            }
            $user = [
                'name' => $wrkUser->name,
                'department_name' => WorkDepartment::getDepartNameByUserId($remind->user_id),
                'start_date' => $date_connt
            ];
            $data['list'] = [
                'user' => $user,
                'quality_count' => $quality_count,
                'external_mag_count' => $external_mag_count,
                'chat_mag_count' => $chat_mag_count,
                'conversation_count' => $conversation_count,
                'violation_count' => $violation_count,
            ];
        }
        $quality_id = explode(',', $remind->quality_id);
        $users = InspectionViolation::find()
            ->select('quality_id')
            ->where(['quality_id' => $quality_id])
            ->andWhere(['user_id' => $remind_user_id])
            ->andWhere(['is_delete' => 0])
            ->andWhere('work_msg_audit_info_id > 0')
            ->andWhere(['between', 'create_time', $start_date, $end_date])
            ->groupBy('quality_id')
            ->asArray()
            ->all();
        $violation_user_id = array_column($users, 'quality_id');
        //查询被质检人员数据
        $query = WorkUser::find()
            ->select(['id', 'name', 'avatar'])
            ->with(['violation' => function ($que) use ($remind_user_id, $start_date, $end_date) {
                $que->select(['quality_id', 'count(*) count', 'msg_type'])
                    ->where(['user_id' => $remind_user_id])
                    ->andWhere(['is_delete' => 0])
                    ->andWhere('work_msg_audit_info_id > 0')
                    ->andWhere(['between', 'create_time', $start_date, $end_date])
                    ->groupBy('quality_id, msg_type');
            }])
            ->where(['id' => $violation_user_id]);

        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        foreach ($list as $key => $val) {
            if(count($val['violation']) ==  2) {
                continue;
            }
            foreach ($val['violation'] as $k => $v) {
                if($v['msg_type'] == 0) {
                    $val['violation'][] = [
                        'count' => 0,
                        'msg_type' => 1,
                    ];
                } else {
                    $val['violation'][] = [
                        'count' => 0,
                        'msg_type' => 0,
                    ];
                }
                $list[$key]['violation'] = $val['violation'];
            }
        }

        $data['page'] = [
            'data' => $list,
            'total_count' => (int)$pagination->totalCount,
            'page_count' => (int)$pagination->pageCount
        ];

        return $data;
    }

    /**
     * 违规记录详情
     */
    public function actionInspectionViolationLog()
    {
        if (\Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $remind_id  = \Yii::$app->request->get('remind_id');
        $quality_id = \Yii::$app->request->get('user_id');
        $date       = \Yii::$app->request->get('date');
        $pageSize   = \Yii::$app->request->get('pageSize', 10);
        $page       = \Yii::$app->request->get('page', 1);
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $remind = InspectionRemind::findOne($remind_id);
        if (empty($remind) || empty($quality_id)) {
            throw new InvalidDataException('参数不正确');
        }
        if ($page == 1) {
            [$quality_count, $external_mag_count, $chat_mag_count, $conversation_count, $violation_count] = $remind->getConversation($remind->id, $date, [$quality_id]);
            $data['count_list'] = [
                'agentId' => $remind->agent_id,
                'external_mag_count' => $external_mag_count,
                'chat_mag_count'     => $chat_mag_count,
                'conversation_count' => $conversation_count,
            ];
        }
        [$start_date, $end_date, $start_date_time, $end_date_time] = $remind->getDate($remind->is_cycle, $date);

        $query = InspectionViolation::find()
            ->where(['user_id' => $remind->user_id])
            ->andWhere(['quality_id' => $quality_id])
            ->andWhere(['between', 'create_time', $start_date, $end_date])
            ->andWhere(['is_delete' => 0]);

        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);
        $list = $query
            ->orderBy('work_msg_audit_info_id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        foreach ($list as $key => $val) {
            $msgContent = WorkMsgAuditInfo::find()->where(['id' => $val['work_msg_audit_info_id']])->one();
            $content = $msgContent->dumpData(true, false, true);
            $list[$key]['msg_content'] = $content;
        }

        $data['page'] = [
            'data' => $list,
            'total_count' => (int)$pagination->totalCount,
            'page_count' => (int)$pagination->pageCount
        ];
        return $data;
    }

    /**
     * 获取员工信息
     */
    public function actionUserInfo()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $user_id  = \Yii::$app->request->post('user_id');
        $user = WorkUser::find()->where(['id' => $user_id])->asArray()->one();
        if(empty($user)) {
            return [];
        }
        $user['department_name'] = WorkDepartment::getDepartNameByUserId($user['id']);

        return $user;
    }
}