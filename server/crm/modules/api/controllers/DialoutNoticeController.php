<?php

	namespace app\modules\api\controllers;


    use app\components\InvalidDataException;
    use app\components\InvalidParameterException;
    use app\models\DialoutAgent;
    use app\models\DialoutBindWorkUser;
    use app\models\DialoutConfig;
    use app\models\DialoutOrder;
    use app\models\DialoutRecord;
    use app\models\ExternalTimeLine;
    use app\models\PublicSeaContactFollowRecord;
    use app\models\PublicSeaTimeLine;
    use app\models\SubUser;
    use app\models\UserCorpRelation;
    use app\models\WorkExternalContactFollowRecord;
    use app\models\WorkUser;
    use app\modules\api\components\AuthBaseController;
    use app\modules\api\components\BaseController;
    use app\modules\api\components\WorkBaseController;
    use app\util\SUtils;
    use yii\web\Request;


    class DialoutNoticeController extends BaseController
	{
		public function actionIndex ()
		{
		    if (!static::checkData()) {
		        return false;
            }
            \Yii::error(print_r(\Yii::$app->request->post(), true), '==============================================');
            $cre_time = date("Y-m-d H:i:s");
		    $noticeType = \Yii::$app->request->post('CallState');

            switch ($noticeType) {
                case 'Hangup':
                case 'Unlink':
                    $callSheetID = \Yii::$app->request->post('CallSheetID','');
                    $callNo = \Yii::$app->request->post('CallNo','');
                    $calledNo = \Yii::$app->request->post('CalledNo','');
                    $RealCalled = \Yii::$app->request->post('RealCalled','');
                    $ring = \Yii::$app->request->post('Ring');
                    $ringingDate = \Yii::$app->request->post('RingingDate');
                    $begin = \Yii::$app->request->post('Begin');
                    $end = \Yii::$app->request->post('End');
                    $exten = \Yii::$app->request->post('Exten');
                    $state = \Yii::$app->request->post('State');
                    $recordFile = \Yii::$app->request->post('RecordFile','');
                    $fileServer = \Yii::$app->request->post('FileServer');
                    $province = \Yii::$app->request->post('Province');
                    $district = \Yii::$app->request->post('District');
                    $hangupPart = \Yii::$app->request->post('HangupPart');
                    $actionID = \Yii::$app->request->post('ActionID');

                    \Yii::$app->cache->set($actionID, '2', 60*60*24);

                    $data = DialoutAgent::find()->where(['exten'=>$exten])->all();

                    //把正在通话的状态修改掉
                    if (!empty($data[0])) {
                        $data = $data[0];
                        $data->state = 1;
                        $data->state_change_time = $cre_time;
                        if (!$data->save()) {
                            \Yii::error("正在通话的状态修改失败", 'Dialout->Unlink->error');
                            \Yii::error(SUtils::modelError($data), 'Dialout->Unlink->rrror111111');
                        }
                    }else{
                        \Yii::error("没有查找到对应的坐席", 'Dialout->Unlink->error');
                        return;
                    }

                    $corpId = $data->corp_id;

                    $stateText = ['dealing'=>1,'notDeal'=>2,'leak'=>3,'queueLeak'=>4,'blackList'=>5,'voicemail'=>6,'limit'=>7];
                    $state = $stateText[$state] ?? 0;

                    $ring = $ring ? strtotime($ring) : 0;
                    $ringing = $ringingDate ? strtotime($ringingDate) : 0;
                    $begin = $begin ? strtotime($begin) : 0;
                    $end = $end ? strtotime($end) : 0;
                    $money = 0;

                    if (!$ringingDate) continue;

                    if ($begin && $end) {
                        $dialoutConfig = DialoutConfig::find()->where(['corp_id'=>$corpId])->asArray()->all();

                        if (!empty($dialoutConfig[0]['phone_money'])) {
                            $money = ceil( ($end-$begin)/60 ) * $dialoutConfig[0]['phone_money'];  //话费消耗
                        }
                    }



                    $actionIDs = explode('_', $actionID);
                    $user_id = $actionIDs[0] ?? 0;
                    $external_userid = $actionIDs[1] ?? 0;
                    $custom_type = $actionIDs[2] ?? -1;

                    if (!$user_id || !$external_userid || $custom_type == -1) {
                        \Yii::error("自定义数据不正确", 'Dialout->Ring->error');
                        return;
                    }

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $recordModel = new DialoutRecord;
                        $recordModel->corp_id = $corpId;
                        $recordModel->user_id = $user_id;
                        $recordModel->external_userid = $external_userid;
                        $recordModel->exten = $exten;
                        $recordModel->call_no = $callNo;
                        $recordModel->small_phone = $calledNo;
                        $recordModel->called_no = $calledNo;
                        $recordModel->real_called = $RealCalled;
                        $recordModel->call_sheet_id = $callSheetID;
                        $recordModel->ring = $ring;
                        $recordModel->ringing = $ringing;
                        $recordModel->begin = $begin;
                        $recordModel->end = $end;
                        $recordModel->money = $money;
                        $recordModel->state = $state;
                        $recordModel->record_file = $recordFile;
                        $recordModel->file_server = $fileServer;
                        $recordModel->province = $province;
                        $recordModel->district = $district;
                        $recordModel->hangup_part = $hangupPart;
                        $recordModel->custom_type = $custom_type;
                        $recordModel->create_time = $cre_time;

                        if (!$recordModel->save()) {
                            \Yii::error(SUtils::modelError($recordModel), 'Dialout->Unlink->rrror111111');
                            return;
                        }

                        if ($money) {
                            //加一条花费订单
                            $orderModel = new DialoutOrder();
                            $orderModel->corp_id = $corpId;
                            $orderModel->exten = $user_id;
                            $orderModel->type = 2;
                            $orderModel->money = -$money;
                            $orderModel->status = 1;
                            $orderModel->create_time = $cre_time;
                            $orderModel->save();

                            //扣除相应的余额
                            $dialoutConfig = DialoutConfig::find()->where(['corp_id'=>$corpId])->all();
                            if (!empty($dialoutConfig[0])){
                                $dialoutConfig = $dialoutConfig[0];
                                $dialoutConfig->balance = $dialoutConfig->balance - $money;
                                $dialoutConfig->save();
                            }
                        }

                        $uid = UserCorpRelation::find(['uid'])->where(['corp_id'=>$corpId])->asArray()->all();
                        if (!empty($uid[0]['uid'])) {
                            $uid = $uid[0]['uid'];
                            $account = WorkUser::findOne($user_id);
                            if ($account) {
                                $sub_id = 0;
                                $is_master = 0;
                                $account = $account->mobile;
                                $subUserInfo = SubUser::find()->where(['uid'=>$uid,'account'=>$account])->asArray()->all();
                                if (!empty($subUserInfo[0])) {
                                    if ($subUserInfo[0]['type'] == 0) {
                                        $sub_id = $subUserInfo[0]['sub_id'];
                                        $is_master = 1;
                                    }
                                }else{
                                    $is_master = 1;
                                }

                                if ($custom_type == 1) {
                                    $followRecord = new PublicSeaContactFollowRecord();
                                    $followRecord->sea_id = $external_userid;
                                    $followRecord->add_time = time();
                                }else{
                                    $followRecord = new WorkExternalContactFollowRecord();
                                    $followRecord->type = 1;
                                    $followRecord->external_id = $external_userid;
                                    $followRecord->time = time();
                                }
                                $followRecord->uid = $uid;
                                $followRecord->sub_id = $sub_id;
                                $followRecord->user_id = $user_id;
                                $followRecord->record = $recordModel->id . '';
                                $followRecord->status      = 1;
                                $followRecord->follow_id      = 0;
                                $followRecord->is_master = $is_master;
                                $followRecord->record_type = 1;
                                $res = $followRecord->save();

                                if ($res) {
                                    if ($custom_type == 1) {
                                        $count = PublicSeaContactFollowRecord::find()->where(['sea_id' => $external_userid, 'status' => 1, 'record_type' => 1, 'user_id' => $user_id])->count();
                                        PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sea_id' => $external_userid, 'user_id' => $user_id, 'sub_id' => $sub_id, 'event' => 'follow', 'event_id' => 0, 'related_id' => $followRecord->id, 'remark' => $count]);
                                    }else{
                                        $count = WorkExternalContactFollowRecord::find()->where(['external_id' => $external_userid, 'type' => 1, 'status' => 1, 'record_type' => 1, 'user_id' => $user_id])->count();//跟进次数
                                        ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'external_id' => $external_userid, 'user_id' => $user_id, 'sub_id' => $sub_id, 'event' => 'follow', 'event_id' => 0, 'related_id' => $followRecord->id, 'remark' => $count]);
                                    }
                                }
                            }
                        }
                        $transaction->commit();
                    }catch (InvalidDataException $e){
                        $transaction->rollBack();
                        \Yii::error("通话通知接收失败", 'Dialout->Hangup->error');
                    }

                    \Yii::$app->cache->set($actionID, '2', 60*60*24);
                    break;
                case 'Ring':
                    $exten = \Yii::$app->request->post('Exten');
                    $ring = \Yii::$app->request->post('Ring');
                    $ring = $ring ? strtotime($ring) : 0;
                    \Yii::error("$exten", 'Dialout->Ring');
                    $data = DialoutAgent::find()->where(['exten'=>$exten])->all();
                    if (!empty($data[0]) && $ring) {
                        $data = $data[0];
                        $data->state = 2;
                        $data->state_change_time = $data->state_change_time ?: 0;
                        if ($ring > $data->state_change_time) {
                            $data->state_change_time = $ring;
                            $data->save();
                        }
                    }else{
                        \Yii::error("没有查找到对应的坐席", 'Dialout->Ring->error');
                    }
                    break;
                case 'Link':
                    $ActionID = \Yii::$app->request->post('ActionID');
                    \Yii::error("ActionID", 'Dialout->Link');
                    \Yii::$app->cache->set($ActionID, '1', 60*60*24);
                    break;
                case 'Ringing':
                    $ActionID = \Yii::$app->request->post('ActionID');
                    \Yii::error("ActionID", 'Dialout->Ringing');
                    \Yii::$app->cache->set($ActionID, '3', 60*60*24);
                    break;
            }
		}

        public function actionOpenExten ()
        {
            \Yii::error(print_r(\Yii::$app->request->post(), true), '==============================================');
            $cre_time = date("Y-m-d H:i:s");

            $corp_id = \Yii::$app->request->post('corp_id');
            $exten = \Yii::$app->request->post('exten');
            $small_phone = \Yii::$app->request->post('small_phone');
            $duration = \Yii::$app->request->post('duration');
            $custom_data = \Yii::$app->request->post('custom_data');

            \Yii::error("$corp_id|$exten|$small_phone|$duration|$custom_data", 'Dialout->openExten');

            $duration = intval($duration);

            if (!$duration) return;

            $data = DialoutAgent::find()->where(['corp_id'=>$corp_id, 'exten'=>$exten])->all();

            if (!empty($data[0])){
                $data = $data[0];
                $transaction = \Yii::$app->db->beginTransaction();
                try{
                    $is_first = true;
                    //首次开通
                    if ($data->enable == 0) {
                        $expire = date("Y-m-d 23:59:59",strtotime("+$duration month"));
                        $data->small_phone = $small_phone;
                        $data->start_time = $cre_time;
                        $data->expire = $expire;
                        $data->enable = 1;
                        $data->status = 1;
                    }else{
                        //续费
                        $expire = date("Y-m-d 23:59:59",strtotime("+$duration month", strtotime($data->expire)));
                        $data->expire = $expire;
                        $is_first = false;
                    }
                    $data->save();

                    //修改和添加订单
                    if ($custom_data) {
                        $customData = explode('_', $custom_data);
                        if (count($customData) != 2) {
                            \Yii::error("自定义数据返回不正确", 'Dialout->openExten->error');
                            return;
                        }
                        $custom_data = $customData[0];
                        $dialoutOrder = DialoutOrder::findOne($custom_data);
                        if ($dialoutOrder) {
                            $dialoutOrder->status = 1;
                            $dialoutOrder->save();

                            $newOrder = new DialoutOrder();
                            $newOrder->corp_id = $dialoutOrder->corp_id;
                            $newOrder->exten = $dialoutOrder->exten;
                            $newOrder->type = $is_first ? 4 : 5;
                            $newOrder->money = -abs($dialoutOrder->money);
                            $newOrder->status = 1;
                            $newOrder->create_time = $cre_time;
                            $newOrder->save();
                        }

                        $custom_data_monthly = $customData[1];
                        $dialoutOrderMonthly = DialoutOrder::findOne($custom_data_monthly);
                        if ($dialoutOrder) {
                            $dialoutOrderMonthly->status = 1;
                            $dialoutOrderMonthly->save();

                            $newOrder = new DialoutOrder();
                            $newOrder->corp_id = $dialoutOrderMonthly->corp_id;
                            $newOrder->exten = $dialoutOrderMonthly->exten;
                            $newOrder->type = 7; //月租消耗
                            $newOrder->money = -abs($dialoutOrderMonthly->money);
                            $newOrder->status = 1;
                            $newOrder->create_time = $cre_time;
                            $newOrder->save();
                        }

                    }
                    $transaction->commit();
                }catch (InvalidDataException $e){
                    $transaction->rollBack();
                }
            }else{
                \Yii::error("没有查找到对应的坐席", 'Dialout->openExten->error');
            }


        }

        public function actionRecharge()
        {
            \Yii::error(print_r(\Yii::$app->request->post(), true), '======Recharge');

            $corp_id = \Yii::$app->request->post('corp_id');
            $custom_data = \Yii::$app->request->post('custom_data');

            if ($custom_data) {
                $dialoutOrder = DialoutOrder::findOne($custom_data);
                if (!$dialoutOrder) {
                    \Yii::error("order不存在", 'Dialout->Recharge->error');
                    return;
                }

                $dialoutConfig = DialoutConfig::findOne(['corp_id'=>$corp_id]);
                if (!$dialoutConfig) {
                    \Yii::error("配置表不存在", 'Dialout->Recharge->error');
                    return;
                }

                $transaction = \Yii::$app->db->beginTransaction();
                try{
                    $dialoutOrder->status = 1;
                    $dialoutConfig->balance = $dialoutConfig->balance + abs($dialoutOrder->money);
                    $dialoutOrder->save();
                    $dialoutConfig->save();
                    $transaction->commit();
                }catch (InvalidDataException $e){
                    $transaction->rollBack();
                }
            }

        }

        public function actionMaterialAuditNotify()
        {
            $corp_id = \Yii::$app->request->post('corp_id');
            $status = \Yii::$app->request->post('status');

            $dialoutConfig = DialoutConfig::find()
                ->where(['corp_id' => $corp_id, 'status' => DialoutConfig::STATUS_AUDIT])
                ->one();

            $dialoutConfig->status = $status;
            if($dialoutConfig->status == DialoutConfig::STATUS_REFUSE)  $dialoutConfig->refuse_reason = \Yii::$app->request->post('refuse_reason', '');

            $dialoutConfig->save();
        }

        private static function checkData(){
            $actionID = \Yii::$app->request->post('ActionID');
            $actionIDs = explode('_', $actionID);
            $user_id = $actionIDs[0] ?? 0;
            $external_userid = $actionIDs[1] ?? 0;
            $custom_type = $actionIDs[2] ?? -1;

            if (!$user_id || !$external_userid || $custom_type == -1) {
                \Yii::error("自定义数据不正确", 'Dialout->checkData->error');
                return false;
            }
            return true;
        }
	}
