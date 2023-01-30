<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/11
	 * Time: 11:57
	 */

	namespace app\controllers;

	use app\controllers\common\BaseController;
	use yii\filters\Cors;
	use yii\helpers\FileHelper;
	use yii\web\Response;
	use yii\web\ServerErrorHttpException;

	class UploadController extends BaseController
	{
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
					'Origin' => ['*', 'pscrm.51lick.com', 'pscrm-mob.51lick.com'],
				]
			];

			return $behaviors;
		}

		/**
		 * @param $fileFullPath
		 *
		 * @return \yii\console\Response|Response
		 *
		 * @throws ServerErrorHttpException
		 * @throws \yii\base\InvalidConfigException
		 */
		private function getResponse ($fileFullPath)
		{
			$mimeType = FileHelper::getMimeType($fileFullPath);

			if ($mimeType == 'text/plain') {
				$fileContent = file_get_contents($fileFullPath);
				$charset     = mb_detect_encoding($fileContent, ["ASCII", "UTF-8", "GB2312", "GBK", "BIG5"]);
				$mimeType    .= ';charset=' . $charset;
			}

			$fileSize = @filesize($fileFullPath);

			$response = \Yii::$app->response;
			$response->headers->set('Content-Type', $mimeType);
			$response->headers->set('Content-Length', $fileSize);

			$start      = 0;
			$readLength = $fileSize;

			// 兼容苹果手机的视频请求
			$canStream = true;
			$httpRange = \Yii::$app->request->getHeaders()->get('Range');
			if (!empty($httpRange)) {
				$response->headers->set('Accept-Ranges', 'bytes');

				$rangInfoString = explode('=', $httpRange)[1];
				[$start, $end] = explode('-', $rangInfoString);

				if (!empty($end)) {
					$canStream  = false;
					$readLength = $end - $start + 1;
					$response->headers->set('Content-Length', $readLength);
				}

				$response->headers->set('Content-Range', 'bytes ' . $rangInfoString . '/' . $fileSize);
			}

			$response->format = Response::FORMAT_RAW;
			if ($canStream) {
				$response->stream = fopen($fileFullPath, 'r');
			} else {
				$response->content = file_get_contents($fileFullPath, false, NULL, $start, $readLength);
			}

			return $response;
		}

		/**
		 * @param string $fileName
		 *
		 * @throws ServerErrorHttpException
		 * @throws \yii\base\InvalidConfigException
		 */
		public function actionIndex ($fileName = '')
		{
			if (empty($fileName)) {
				$fileFullPath = \Yii::getAlias('@upload') . '/problem.jpeg';
			} else {
				$fileFullPath = \Yii::getAlias('@upload') . '/' . $fileName;
			}

			if (!file_exists($fileFullPath)) {
				$fileFullPath = \Yii::getAlias('@upload') . '/problem.jpeg';
			}

			$response = $this->getResponse($fileFullPath);

			return $response->send();
		}

		/**
		 * @param string $fileName
		 *
		 * @throws ServerErrorHttpException
		 * @throws \yii\base\InvalidConfigException
		 */
		public function actionStatic ($fileName = '')
		{
			if (empty($fileName)) {
				$fileFullPath = \Yii::getAlias('@static') . '/problem.jpeg';
			} else {
				$fileFullPath = \Yii::getAlias('@static') . '/' . $fileName;
			}

			if (!file_exists($fileFullPath)) {
				$fileFullPath = \Yii::getAlias('@static') . '/problem.jpeg';
			}

			$response = $this->getResponse($fileFullPath);

			return $response->send();
		}

		/**
		 * @param string $fileName
		 *
		 * @return \yii\console\Response|Response
		 */
		public function actionPem ($fileName = '')
		{
			if (empty($fileName)) {
				$fileFullPath = \Yii::getAlias('@upload') . '/problem.jpeg';
			} else {
				$fileFullPath = \Yii::getAlias('@pem') . '/' . $fileName;
			}

			if (!file_exists($fileFullPath)) {
				$fileFullPath = \Yii::getAlias('@upload') . '/problem.jpeg';
			}

			return \Yii::$app->response->sendFile($fileFullPath);
		}
	}