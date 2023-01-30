<?php

	namespace app\controllers;

	use app\controllers\common\BaseController;
	use app\models\MessagePush;

	class SmsReturnController extends BaseController
	{
		public $enableCsrfValidation = false;

		//短信回执
		public function actionSend ()
		{
			$postData = $_POST;
			\Yii::error($postData, 'SendData');
			//短信发送回执处理
			MessagePush::sendReturn($postData);
			echo 'success';
		}

		//上行回复推送
		public function actionUp ()
		{
			$postData = $_POST;
			\Yii::error($postData, 'UpData');
		}

		//模版审核推送
		public function actionVerify ()
		{
			$postData = $_POST;
			\Yii::error($postData, 'VerifyData');
		}

	}