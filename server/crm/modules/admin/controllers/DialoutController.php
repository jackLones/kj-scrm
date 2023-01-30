<?php

	namespace app\modules\admin\controllers;


	use app\components\InvalidParameterException;
    use app\models\DialoutAgent;
    use app\models\DialoutConfig;
    use app\models\DialoutKey;
    use app\models\DialoutOrder;
    use app\modules\admin\components\BaseController;
	use app\util\SUtils;
	use app\components\InvalidDataException;
    use app\models\AdminConfig;
    use yii\helpers\Json;

    class DialoutController extends BaseController
	{
		public $enableCsrfValidation = false;

        //设置价钱信息
        public function actionSetConfig()
        {
            $dialoutConfigId     = \Yii::$app->request->post('id',0);

            $dialoutConfig = DialoutConfig::find()->where(['id' => $dialoutConfigId])->one();

            if(empty($dialoutConfig)){
                return Json::encode(["error" => 1, "data" => "请先开通外呼"],JSON_UNESCAPED_UNICODE);
            }

            $corpId = $dialoutConfig->corp_id;

            $cre_time = date("Y-m-d H:i:s");
            $exten_money     = \Yii::$app->request->post('exten_money');
            $phone_money     = \Yii::$app->request->post('phone_money');
            //$monthly_money     = \Yii::$app->request->post('monthly_money', 0);

            if (!$corpId) {
                return Json::encode(["error" => 1, "data" => "缺少参数"],JSON_UNESCAPED_UNICODE);
            }

            $exten_money = floatval($exten_money);

            $phone_money = floatval($phone_money);

            //$monthly_money = floatval($monthly_money);

            if (!$this->checkMoneyFormat($exten_money)) {
                return Json::encode(["error" => 1, "data" => "坐席费用格式不正确"],JSON_UNESCAPED_UNICODE);
            }

//            if (!$this->checkMoneyFormat($monthly_money)) {
//                return Json::encode(["error" => 1, "data" => "月租格式不正确"],JSON_UNESCAPED_UNICODE);
//            }

            if (!$this->checkMoneyFormat($phone_money)) {
                return Json::encode(["error" => 1, "data" => "话费价格格式不正确"],JSON_UNESCAPED_UNICODE);
            }

            $dialoutConfig->exten_money = $exten_money;
            $dialoutConfig->phone_money = $phone_money;
            $dialoutConfig->remark = $dialoutConfig->remark . "$cre_time:修改坐席价格为$exten_money;话费价格为$phone_money|";
            $res = $dialoutConfig->save();

            if ($res) {
                return Json::encode(["error" => 0], JSON_UNESCAPED_UNICODE);
            }else{
                return Json::encode(["error" => 1, "data" => "设置错误"], JSON_UNESCAPED_UNICODE);
            }

        }

		//话费充值
        public function actionRecharge()
        {
            $dialoutConfigId     = \Yii::$app->request->post('id',0);

            $dialoutConfig = DialoutConfig::find()->where(['id' => $dialoutConfigId])->one();

            if(empty($dialoutConfig)){
                return Json::encode(["error" => 1, "data" => "请先开通外呼"],JSON_UNESCAPED_UNICODE);
            }

            $corpId = $dialoutConfig->corp_id;

            $cre_time = date("Y-m-d H:i:s");
            $money     = \Yii::$app->request->post('money');

            $money = floatval($money);

            $checkFormat = $this->checkMoneyFormat($money);

            if (!$checkFormat) {
                return Json::encode(["error" => 1, "data" => "格式不正确"],JSON_UNESCAPED_UNICODE);
            }

            if (empty($dialoutConfig->phone_money)) {
                return Json::encode(["error" => 1, "data" => "请设置话费价格"],JSON_UNESCAPED_UNICODE);
            }

            $min = $money/$dialoutConfig->phone_money;

            if ($min > (int)$min) {
                return Json::encode(["error" => 1, "data" => "请保持充值金额数能够整除话费价格"],JSON_UNESCAPED_UNICODE);
            }

            $keyInfo = DialoutKey::findOne(['api_type'=>'7moor']);

            if (!$keyInfo) {
                return Json::encode(["error" => 1, "data" => "请联系管理员分配KEY"],JSON_UNESCAPED_UNICODE);
            }

            $min = (int)$min;

            $orderModel = new DialoutOrder;

            $orderModel->corp_id = $corpId;
            $orderModel->exten = 0;
            $orderModel->type = 1;
            $orderModel->money = $money;
            $orderModel->status = 2;
            $orderModel->create_time = $cre_time;
            $resOrder = $orderModel->save();

            $url = \Yii::$app->params['dialout_url'] . '/index.php?r=webcall/api/telephone-recharge';
            $postData = [
                'api_key'=>$keyInfo->api_key,
                'custom_key'=>$corpId,
                'minute'=>$min,
                'custom_data'=>$orderModel->id,
            ];

            $res = SUtils::postUrl($url, $postData);

            return Json::encode(["error" => 0], JSON_UNESCAPED_UNICODE);
        }

		//开通坐席
        public function actionAddAgent ()
        {
            $dialoutConfigId     = \Yii::$app->request->post('id',0);

            $dialoutConfig = DialoutConfig::find()->where(['id' => $dialoutConfigId])->one();

            if(empty($dialoutConfig)){
                return Json::encode(["error" => 1, "data" => "请先开通外呼"],JSON_UNESCAPED_UNICODE);
            }

            $cre_time = date("Y-m-d H:i:s");

            $corpId     = $dialoutConfig->corp_id;
            $duration     = \Yii::$app->request->post('duration', 0);
            $addNum     = \Yii::$app->request->post('add_num',0);

            if (!$corpId) {
                return Json::encode(["error" => 1, "data" => "缺少参数"],JSON_UNESCAPED_UNICODE);
            }

            $duration = (int)$duration;
            if ($duration <= 0) {
                return Json::encode(["error" => 1, "data" => "开通时长不正确"],JSON_UNESCAPED_UNICODE);
            }

            $duration = $duration * 12;

            $keyInfo = DialoutKey::findOne(['api_type'=>'7moor']);
            if (!$keyInfo) {
                return Json::encode(["error" => 1, "data" => "请联系管理员分配KEY"],JSON_UNESCAPED_UNICODE);
            }

            //判断基本设置
            if (empty($dialoutConfig->exten_money)) {
                return Json::encode(["error" => 1, "data" => "请设置坐席价格"],JSON_UNESCAPED_UNICODE);
            }

//            if (!isset($dialoutConfig[0]['monthly_money'])) {
//                return Json::encode(["error" => 1, "data" => "请设置月租"],JSON_UNESCAPED_UNICODE);
//            }

            $extenMoney = $dialoutConfig->exten_money;
//            $monthlyMoney = $dialoutConfig[0]['monthly_money'];

            $maxAgentId = DialoutAgent::find()->select(['max(exten) agent_id'])->asArray()->all();
            $maxAgentId = $maxAgentId[0]['agent_id'] ?? 1000;

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $i = 1;
                while($i <= $addNum) {
                    $exten = $maxAgentId+$i;
                    $orderModel = new DialoutOrder;

                    $orderModel->corp_id = $corpId;
                    $orderModel->exten = $exten;
                    $orderModel->type = 3;
                    $orderModel->money = $extenMoney*$duration;
                    $orderModel->status = 0;
                    $orderModel->create_time = $cre_time;
                    $resOrder = $orderModel->save();

                    //月租
                    $orderModelMonthly = new DialoutOrder;

                    $orderModelMonthly->corp_id = $corpId;
                    $orderModelMonthly->exten = $exten;
                    $orderModelMonthly->type = 6; //月租充值
                    $orderModelMonthly->money = $extenMoney*$duration;
                    $orderModelMonthly->status = 0;
                    $orderModelMonthly->create_time = $cre_time;
                    $resOrderMonthly = $orderModelMonthly->save();

                    $model = new DialoutAgent();
                    $model->corp_id = $corpId;
                    $model->exten = $exten;
                    $model->create_time = $cre_time;
                    $resModel = $model->save();

                    if (!$resOrder || !$resModel || !$resOrderMonthly) {
                        return Json::encode(["error" => 1, "data" => "开通错误"],JSON_UNESCAPED_UNICODE);
                    }

                    $url = \Yii::$app->params['dialout_url'] . '/index.php?r=webcall/api/moor7-listence-renewal';
                    $postData = [
                        'api_key'=>$keyInfo->api_key,
                        'exten'=>$exten,
                        'custom_key'=>$corpId,
                        'duration'=>$duration,
                        'custom_data'=>$orderModel->id . '_' . $orderModelMonthly->id,
                    ];

                    $res = SUtils::postUrl($url, $postData);

                    if($res['code'] != '200'){
                        return Json::encode(["error" => 1, "data" => $res['message']],JSON_UNESCAPED_UNICODE);
                    }

                    $i++;

                }
                $transaction->commit();
            }catch (InvalidDataException $e) {
                $transaction->rollBack();
                return Json::encode(["error" => 1, "data" => "系统开通错误"],JSON_UNESCAPED_UNICODE);
            }

            return Json::encode(["error" => 0], JSON_UNESCAPED_UNICODE);

        }

        //坐席续费
        public function actionExtenRenew ()
        {
            $dialoutConfigId     = \Yii::$app->request->post('id',0);

            $dialoutConfig = DialoutConfig::find()->where(['id' => $dialoutConfigId])->one();

            if(empty($dialoutConfig)){
                return Json::encode(["error" => 1, "data" => "请先开通外呼"],JSON_UNESCAPED_UNICODE);
            }

            $cre_time = date("Y-m-d H:i:s");

            $corpId     = $dialoutConfig->corp_id;
            $duration     = \Yii::$app->request->post('duration', 0);
            $exten  = \Yii::$app->request->post('exten',0);

            if (!$corpId) {
                return Json::encode(["error" => 1, "data" => "缺少参数"],JSON_UNESCAPED_UNICODE);
            }

            if (!$exten) {
                return Json::encode(["error" => 1, "data" => "缺少坐席工号"],JSON_UNESCAPED_UNICODE);
            }

            $duration = (int)$duration;
            if ($duration <= 0) {
                return Json::encode(["error" => 1, "data" => "开通时长不正确"],JSON_UNESCAPED_UNICODE);
            }

            $keyInfo = DialoutKey::findOne(['api_type'=>'7moor']);
            if (!$keyInfo) {
                return Json::encode(["error" => 1, "data" => "请联系管理员分配KEY"],JSON_UNESCAPED_UNICODE);
            }

            //判断基本设置
            if (empty($dialoutConfig->exten_money)) {
                return Json::encode(["error" => 1, "data" => "请设置坐席价格"],JSON_UNESCAPED_UNICODE);
            }

            $extenMoney = $dialoutConfig->exten_money;

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $orderModel = new DialoutOrder;

                $orderModel->corp_id = $corpId;
                $orderModel->exten = $exten;
                $orderModel->type = 3;
                $orderModel->money = $extenMoney*$duration;
                $orderModel->status = 0;
                $orderModel->create_time = $cre_time;
                $resOrder = $orderModel->save();

                //月租
                $orderModelMonthly = new DialoutOrder;

                $orderModelMonthly->corp_id = $corpId;
                $orderModelMonthly->exten = $exten;
                $orderModelMonthly->type = 6; //月租充值
                $orderModelMonthly->money = $extenMoney*$duration;
                $orderModelMonthly->status = 0;
                $orderModelMonthly->create_time = $cre_time;
                $resOrderMonthly = $orderModelMonthly->save();

                $url = \Yii::$app->params['dialout_url'] . '/index.php?r=webcall/api/moor7-listence-renewal';
                $postData = [
                    'api_key'=>$keyInfo->api_key,
                    'exten'=>$exten,
                    'custom_key'=>$corpId,
                    'duration'=>$duration,
                    'custom_data'=>$orderModel->id . "_" . $orderModelMonthly->id,
                ];
                SUtils::postUrl($url, $postData);
                $transaction->commit();
            }catch (InvalidDataException $e) {
                $transaction->rollBack();
                return Json::encode(["error" => 1, "data" => "系统开通错误"],JSON_UNESCAPED_UNICODE);
            }

            return Json::encode(["error" => 0], JSON_UNESCAPED_UNICODE);

        }

        //检查金额格式,最多2位小数
        public function checkMoneyFormat($money)
        {
            $money = floatval($money);

            if ($money <= 0) return false;
            if (! is_numeric($money)) return false;

            $arr = explode('.', $money);

            if (!empty($arr[1]) && strlen($arr[1]) > 2) {
                return false;
            }

            return true;
        }

        public function actionAgents()
        {
            $dialoutConfigId = \Yii::$app->request->get('id');

            if(empty($dialoutConfigId)){
                throw new InvalidDataException('ID cannot be blank.');
            }

            $dialoutAgents = DialoutAgent::find()->alias('da')
                ->rightJoin('{{%dialout_config}} dc', 'dc.corp_id=da.corp_id')
                ->where(['dc.id' => $dialoutConfigId])
                ->all();

            return Json::encode(["error" => 0, 'data' => $dialoutAgents], JSON_UNESCAPED_UNICODE);
        }
	}
