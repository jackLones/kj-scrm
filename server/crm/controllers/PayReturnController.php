<?php

	namespace app\controllers;

	use app\controllers\common\BaseController;
	use app\models\MessageOrder;

	class PayReturnController extends BaseController
	{
		public $enableCsrfValidation = false;

		public function actionIndex ()
		{

			libxml_disable_entity_loader(true);
			if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
				$xml = file_get_contents('php://input');
			} else {
				$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
			}
			\Yii::error($xml, 'xml');

			$arrayData = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
			\Yii::error($arrayData, 'arrayData');
			if(!empty($arrayData)){
				$messageOrder = MessageOrder::findOne(['order_id'=>$arrayData['out_trade_no']]);
				if(!empty($messageOrder)){
					if ($arrayData['return_code'] == 'SUCCESS' && $arrayData['result_code'] == 'SUCCESS') {
						if ($messageOrder['ispay'] == 0) {
							MessageOrder::paySuccess($arrayData);
						}
						echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
						exit();
					}
				}
			}
			echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
			exit();
		}
	}