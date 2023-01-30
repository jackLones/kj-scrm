<?php
	/**
	 * Create by PhpStorm
	 * title:日思夜想注册推送
	 * User: fulu
	 * Date: 2020/09/25
	 */

	namespace app\controllers;

	use app\components\InvalidDataException;
	use app\controllers\common\BaseController;
	use app\models\AttachmentGroup;
	use app\models\User;
	use app\util\WebhookUtil;
	use app\util\WorkUtils;
	use yii\filters\Cors;

	class KuaijingPushController extends BaseController
	{
		public $enableCsrfValidation = false;

		/**
		 * {@inheritDoc}
		 *
		 * @return array
		 */
		public function behaviors ()
		{
			$behaviors = parent::behaviors();

			$behaviors['cors'] = [
				'class' => Cors::className(),
				'cors'  => [
					'Origin' => ['www.51lick.com'],
				]
			];

			return $behaviors;
		}

		/**
		 * @throws \Throwable
		 * @throws \app\components\InvalidParameterException
		 * @throws \yii\db\StaleObjectException
		 */
		public function actionIndex ()
		{
			header("Access-Control-Allow-Origin:*");

			/*$httpRequestBody = file_get_contents('php://input');
			$postData        = json_decode($httpRequestBody, true);
			$name    = isset($postData['name']) ? $postData['name'] : '';
			$phone   = isset($postData['phone']) ? $postData['phone'] : '';
			$company = isset($postData['company']) ? $postData['company'] : '';*/

			$name       = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
			$phone      = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : '';
			$company    = isset($_REQUEST['company']) ? $_REQUEST['company'] : '';
			$type       = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			$sourceName = isset($_REQUEST['source_name']) ? $_REQUEST['source_name'] : '';

			if (empty($name)) {
				$this->dexit(['code' => 100, 'msg' => "姓名不能为空"]);
			}
			if (empty($phone)) {
				$this->dexit(['code' => 100, 'msg' => "手机号不能为空"]);
			} elseif (!preg_match("/^((13[0-9])|(14[0-9])|(15([0-9]))|(16([0-9]))|(17([0-9]))|(18[0-9])|(19[0-9]))\d{8}$/", $phone)) {
				$this->dexit(['code' => 100, 'msg' => "请输入正确的手机号"]);
			}

			//保存
			try {
				$profileData = [
					'company_name' => $company,
					'nick_name'    => $name,
				];
				$userInfo    = User::create($phone, 'm123456', 2, $profileData);
				//设置默认分组
				AttachmentGroup::setNotGroup($userInfo->uid);

				WorkUtils::sendOpportunities($userInfo, $type, $name, 1, $sourceName);

				$this->dexit(["code" => 0, "msg" => "success"]);
			} catch (InvalidDataException $e) {
				$this->dexit(['code' => 100, 'msg' => $e->getMessage()]);
			}
		}
	}