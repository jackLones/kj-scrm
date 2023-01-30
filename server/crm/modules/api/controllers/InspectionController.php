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
use app\modules\api\components\WorkBaseController;
use yii\data\Pagination;
use yii\web\MethodNotAllowedHttpException;

class InspectionController extends WorkBaseController
{
    /**
     * 获取质检人
     */
    public function actionQualityInspector()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $agent_id  = \Yii::$app->request->post('agent_id', 0);
        $corpid    = \Yii::$app->request->post('corp_id', 0);
        $is_external = \Yii::$app->request->post('is_external', 0);
        $workCorp = WorkCorp::findOne(['corpid' => $corpid]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        [$AgentDepartment, $AgentUserIds, $AgentDepartmentOld] = WorkDepartment::GiveAgentIdReturnDepartmentOrUser($workCorp->id, $agent_id, 0, $is_external);
        $workUser = WorkUser::find()
            ->select(['id', 'corp_id', 'name', 'department'])
            ->where(['id' => $AgentUserIds])
            ->asArray()
            ->all();

        if($is_external) {//查询是否开启会话
            $workUser = array_filter($workUser, function ($v) {
                return !empty(WorkMsgAuditUser::find()
                    ->where(['user_id' => $v['id']])
                    ->andWhere(['status' => 1])
                    ->asArray()
                    ->one());
            });
        }
        return $workUser;
    }

    /**
     * 获取质检提醒
     */
    public function actionInspectorRemindAdd()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $id       = \Yii::$app->request->post('id', 0);
        $corpid  = \Yii::$app->request->post('corp_id', 0);
        $user     = \Yii::$app->request->post('user', '');
        $report   = \Yii::$app->request->post('report', '');
        $quality  = \Yii::$app->request->post('quality', '');
        $agent_id = \Yii::$app->request->post('agent_id', 0);
        $is_cycle = \Yii::$app->request->post('is_cycle', 0);
        $workCorp = WorkCorp::findOne(['corpid' => $corpid]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        $corp_id = $workCorp->id;
        if(empty($user) || empty($report) || empty($quality)) {
            throw new InvalidParameterException('参数错误！');
        }
        $qualityId    = array_column($quality, 'id');
        $quality_id   = implode(',', $qualityId);
        $reportId     = array_column($report, 'id');
        $report_id    = implode(',', $reportId);
        $reportName   = array_column($report, 'title');
        $report_name  = implode(',', $reportName);
        if($id) { //修改
            InspectionRemind::updateAll(['quality_id' => $quality_id, 'is_cycle' => $is_cycle, 'quality_json' => json_encode($quality),
                'report_json' => json_encode($report), 'report_name' => $report_name, 'report_id' => $report_id],['id' => $id]);
        } else {
            $user_id      = $user['id'];
            $InspectionRemind = new InspectionRemind();
            $InspectionRemind->corp_id = $corp_id;
            $InspectionRemind->user_id = $user_id;
            $InspectionRemind->report_id = $report_id;
            $InspectionRemind->report_name = $report_name;
            $InspectionRemind->quality_id = $quality_id;
            $InspectionRemind->quality_json = json_encode($quality);
            $InspectionRemind->report_json = json_encode($report);
            $InspectionRemind->agent_id = $agent_id;
            $InspectionRemind->is_cycle = $is_cycle;
            if(!$InspectionRemind->save()) {
                throw new InvalidParameterException('添加失败');
            }
        }

        return;
    }

    /**
     * 质检提醒列表
     */
    public function actionInspectorRemindList()
    {
        if (\Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $user    = \Yii::$app->request->get('user', '');
        $status  = \Yii::$app->request->get('status', '');
        $corpid  = \Yii::$app->request->get('corp_id', 0);
        $pageSize  = \Yii::$app->request->get('pageSize', 10);
        $workCorp = WorkCorp::findOne(['corpid' => $corpid]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        //判断违规原因是否存在，不存在，添加默认数据
        $classify = InspectionViolationClassify::find()
            ->where(['corp_id' => $workCorp->id])
            ->one();
        if(empty($classify)) {
            InspectionViolationClassify::getClassifyAdd($workCorp->id);
        }
        $query = InspectionRemind::find()
            ->select(['id', 'user_id', 'report_name', 'quality_id', 'is_cycle', 'status', 'create_time'])
            ->where(['corp_id' => $workCorp->id])
            ->andWhere(['is_delete' => 0])
            ->andFilterWhere(['status' => $status]);
        if(!empty($user)) {
            $query->andWhere("FIND_IN_SET( '$user', report_name )");
        }

        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('create_time')
            ->asArray()
            ->all();
        foreach ($data as $key => $val) {
            $data[$key]['user_name'] = WorkUser::findOne($val['user_id'])['name'];
            $id = explode(',', $val['quality_id']);
            $quality_name = '';
            if(!empty($id)) {
                $WorkUser = WorkUser::find()->select('name')->where(['id' => $id])->asArray()->all();
                $quality_name = array_column($WorkUser, 'name');
//                $quality_name = implode(',', $quality_name);
//                if(count($id) > 5) {
//                    $quality_name.= '等';
//                }
            }
            $data[$key]['quality_name'] = $quality_name;
            $data[$key]['report_name'] = explode(',', $val['report_name']);
        }

        return ['data' => $data, 'total_count' => (int)$pagination->totalCount, 'page_count' => (int)$pagination->pageCount];
    }

    public function actionRemindInfo()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $remind_id    = \Yii::$app->request->post('remind_id');
        return InspectionRemind::findOne($remind_id);
    }

    /**
     * 批量操作
     */
    public function actionBatchOperation()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $id = \Yii::$app->request->post('id', 0);
        $type = \Yii::$app->request->post('type', 0);//1开启 2关闭 3移除
        if(empty($type) || empty($id)) {
            throw new InvalidDataException('参数不正确');
        }

        $id = explode(',', $id);
        if($type == 1) {
            InspectionRemind::updateAll(['status' => 1], ['id' => $id]);
        } else if($type == 2) {
            InspectionRemind::updateAll(['status' => 0], ['id' => $id]);
        } else if($type == 3) {
            InspectionRemind::updateAll(['is_delete' => 1], ['id' => $id]);
        } else {
            throw new InvalidDataException('参数不正确');
        }
        return;
    }

    /**
     * 违规原因列表
     */
    public function actionInspectionViolationClassifyList()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $corpid = \Yii::$app->request->post('corp_id', 0);
        $workCorp = WorkCorp::findOne(['corpid' => $corpid]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        $list = InspectionViolationClassify::find()
            ->where(['corp_id' => $workCorp->id])
            ->andWhere(['is_delete' => 0])
            ->asArray()
            ->all();

        return $list;
    }

    /**
     * 违规原因批量操作
     */
    public function actionInspectionViolationClassifyAdd()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $id = \Yii::$app->request->post('id', 0);
        $is_del = \Yii::$app->request->post('is_del', 0);
        $corpid = \Yii::$app->request->post('corp_id', 0);
        $content = \Yii::$app->request->post('content', '');
        $workCorp = WorkCorp::findOne(['corpid' => $corpid]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        if($is_del != 0 && $id == 0) {
            if(mb_strlen($content) > 20) {
                throw new InvalidDataException('内容超出限制');
            }
        }
        if($id) {//修改或删除
            if($is_del == 1) {//删除
                InspectionViolationClassify::updateAll(['is_delete' => 1], ['id' => $id]);
            } else {//修改
                InspectionViolationClassify::updateAll(['content' => $content], ['id' => $id]);
            }
        } else {
            $Classify = new InspectionViolationClassify();
            $Classify->corp_id = $workCorp->id;
            $Classify->content = $content;
            $Classify->save();
        }

        return;
    }

    /**
     * 违规质检
     */
    public function actionInspectionViolationAdd()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $corp_id    = \Yii::$app->request->post('corp_id');
        $id         = \Yii::$app->request->post('id');
        $msg_id     = \Yii::$app->request->post('msg_id', 0);
        $quality_id = \Yii::$app->request->post('quality_id');
        $to_user_id = \Yii::$app->request->post('to_user_id', 0);
        $roomid     = \Yii::$app->request->post('roomid');
        $content    = \Yii::$app->request->post('content', '');
        $content_classify    = \Yii::$app->request->post('content_classify', '');
        $status     = \Yii::$app->request->post('status', 0);
        $is_del     = \Yii::$app->request->post('is_del', 0);

        $workCorp = WorkCorp::findOne(['corpid' => $corp_id]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        if($status == 0) {
            if(empty($content) && empty($msg_id)) {
                throw new InvalidDataException('参数不正确');
            }
        }
        if (!isset($this->subUser->sub_id)) {
            throw new InvalidDataException('非质检人员不能质检');
        }
        $workUser = WorkUser::find()->where(['corp_id' => $workCorp->id])->andWhere(['mobile' => $this->subUser->account])->asArray()->one();
        if(empty($workUser)) {
            throw new InvalidDataException('非质检人员不能质检');
        }
        if($status) {
            $msg_type =  empty($roomid) ? 0 : 1;
            if($msg_type == 0) {
                $where = ['quality_id' => $quality_id, 'to_user_id' => $to_user_id];
            } else {
                $where = ['quality_id' => $quality_id, 'roomid' => $roomid];
            }
            InspectionViolation::updateAll(['status' => 1], $where);
            return;
        }
        if($id) {//id存在，修改
            $InspectionViolation = InspectionViolation::findOne($id);
            if($is_del) {
                $InspectionViolation->is_delete = $is_del;
            } else {
                $InspectionViolation->content = $content;
                $InspectionViolation->content_classify = $content_classify;
            }
            $InspectionViolation->save();
            return;
        }
        $InspectionViolation = new InspectionViolation();
        $InspectionViolation->corp_id = $workCorp->id;
        $InspectionViolation->user_id = $workUser['id'];
        $InspectionViolation->quality_id = $quality_id;
        $InspectionViolation->work_msg_audit_info_id = empty($msg_id) ? 0 : $msg_id;
        $InspectionViolation->to_user_id = $to_user_id;
        $InspectionViolation->roomid = $roomid;
        $InspectionViolation->content = empty($content) ? '' : $content;
        $InspectionViolation->content_classify = empty($content_classify) ? '' : $content_classify;
        $InspectionViolation->msg_type = empty($roomid) ? 0 : 1;
        $InspectionViolation->status = $status;
        $InspectionViolation->save();
        return;
    }

    /**
     * 获取没有提交的内容
     */
    public function actionInspectionViolationList()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $quality_id = \Yii::$app->request->post('quality_id');
        $to_user_id = \Yii::$app->request->post('to_user_id');
        $roomid     = \Yii::$app->request->post('roomid');
        $corp_id    = \Yii::$app->request->post('corp_id');
        $workCorp = WorkCorp::findOne(['corpid' => $corp_id]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        if (!isset($this->subUser->sub_id)) {
            throw new InvalidDataException('非质检人员不能质检');
        }
        $workUser = WorkUser::find()->where(['corp_id' => $workCorp->id])->andWhere(['mobile' => $this->subUser->account])->asArray()->one();
        if(empty($workUser)) {
            throw new InvalidDataException('非质检人员不能质检');
        }
        $user_id = $workUser['id'];
        $list = InspectionViolation::find()
            ->where(['quality_id' => $quality_id])
            ->andWhere(['user_id' => $user_id])
            ->andWhere(['status' => 0])
            ->andWhere(['is_delete' => 0])
            ->andFilterWhere(['to_user_id' => $to_user_id])
            ->andFilterWhere(['roomid' => $roomid])
            ->asArray()
            ->all();
        if($list) {
            foreach ($list as $key => $val) {
                if(empty($val['work_msg_audit_info_id'])) {
                    continue;
                }
                $msgContent = WorkMsgAuditInfo::find()->where(['id' => $val['work_msg_audit_info_id']])->one();
                $content = $msgContent->dumpData(true, true, true);
                $list[$key]['msg_content'] = $content;
            }
        }

        return $list;
    }

    /**
     * 质检记录
     */
    public function actionInspectionList()
    {
        if (\Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $corp_id = \Yii::$app->request->get('corp_id');
        $start_date = \Yii::$app->request->get('start_date');
        $end_date = \Yii::$app->request->get('end_date');
        $name = \Yii::$app->request->get('name');
        $pageSize = \Yii::$app->request->get('pageSize');

        $workCorp = WorkCorp::findOne(['corpid' => $corp_id]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        $query = InspectionViolation::find()
            ->alias('a')
            ->select(['b.id user_id', 'b.avatar user_avatar', 'b.name user_name', 'c.id quality_id', 'c.avatar quality_avatar',
                'c.name quality_name', 'a.work_msg_audit_info_id', 'a.content', 'a.create_time'])
            ->leftJoin(WorkUser::tableName(). ' b', 'b.id = a.user_id')
            ->leftJoin(WorkUser::tableName(). ' c', 'c.id = a.quality_id')
            ->where(['a.corp_id' => $workCorp->id])
            ->andWhere(['a.is_delete' => 0])
            ->andWhere('a.work_msg_audit_info_id > 0')
            ->andFilterWhere(['between', 'a.create_time', $start_date, $end_date.' 23:59:59'])
            ->andFilterWhere([
                'or',
                ['like', 'b.name', $name],
                ['like', 'c.name', $name]
            ]);

        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('create_time')
            ->asArray()
            ->all();
        if($list) {
            foreach ($list as $key => $val) {
                $msgContent = WorkMsgAuditInfo::find()->where(['id' => $val['work_msg_audit_info_id']])->one();
                $content = $msgContent->dumpData(false, false, true);
                $content['msgtime'] = date('Y-m-d H:i', $content['msgtime']/1000);
                $list[$key]['msg_content'] = $content;
                $list[$key]['create_time'] = date('Y-m-d H:i', strtotime($val['create_time']));
                $list[$key]['user_department_name'] = WorkDepartment::getDepartNameByUserId($val['user_id']);
                $list[$key]['quality_department_name'] = WorkDepartment::getDepartNameByUserId($val['quality_id']);
            }
        }

        return ['data' => $list, 'total_count' => (int)$pagination->totalCount, 'page_count' => (int)$pagination->pageCount];
    }

    /**
     * 判断是否弹框 h5端
     */
    public function actionIsViolation()
    {
        if (\Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException('请求方式不允许！');
        }
        $corp_id = \Yii::$app->request->post('corp_id');
        $roomid = \Yii::$app->request->post('roomid');
        $quality_id = \Yii::$app->request->post('quality_id');
        $to_user_id = \Yii::$app->request->post('to_user_id');

        $workCorp = WorkCorp::findOne(['corpid' => $corp_id]);
        if (empty($workCorp)) {
            throw new InvalidDataException('参数不正确');
        }
        $violation = InspectionViolation::find()
            ->where(['corp_id' => $workCorp->id])
            ->andWhere(['user_id' => $quality_id])
            ->andWhere(['status' => 0])
            ->andWhere(['is_delete' => 0])
            ->andFilterWhere(['to_user_id' => $to_user_id])
            ->andFilterWhere(['roomid' => $roomid])
            ->one();
        $status = 0;
        if($violation) {
            $status = 1;
        }
        $inspection = InspectionViolation::find()
            ->where(['corp_id' => $workCorp->id])
            ->andWhere(['user_id' => $quality_id])
            ->andWhere(['status' => 1])
            ->andWhere(['is_delete' => 0])
            ->andFilterWhere(['to_user_id' => $to_user_id])
            ->andFilterWhere(['roomid' => $roomid])
            ->one();
        if($inspection) {
            $status = 1;
        }

        return ['status' => $status];
    }


}