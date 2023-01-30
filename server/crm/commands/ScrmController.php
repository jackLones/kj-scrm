<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/3/27
	 * Time: 19:14
	 */

	namespace app\commands;

	use app\models\WorkExternalContact;
	use app\models\WorkUser;
	use yii\console\Controller;
	use yii\console\ExitCode;

	class ScrmController extends Controller
	{
		/**
		 * This command get & save work user an external contact no have openid.
		 * @return int
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public function actionOpenid()
		{
			echo "============= start =============\n";
			echo "WorkUser begin:\n";
			$workUsers = WorkUser::find()->where(['openid' => NULL])->orderBy(['corp_id' => 'asc', 'id' => 'desc'])->all();
			if (!empty($workUsers)) {
				$count = count($workUsers);
				$i     = 0;
				/** @var WorkUser $workUser */
				foreach ($workUsers as $workUser) {
					$i++;
					echo "$i/$count begin: $workUser->userid\n";
					$workUser->getUserOpenid();
				}
			}

			echo "================================\n";

			echo "WorkExternalContact begin:\n";
			$workExternalUsers = WorkExternalContact::find()->where(['openid' => NULL])->orderBy(['corp_id' => 'asc', 'id' => 'desc'])->all();
			if (!empty($workExternalUsers)) {
				$count = count($workExternalUsers);
				$i     = 0;
				/** @var WorkExternalContact $workExternalUser */
				foreach ($workExternalUsers as $workExternalUser) {
					$i++;
					echo "$i/$count begin: $workExternalUser->external_userid\n";
					$workExternalUser->getExternalOpenid();
				}
			}
			echo "============= end ==============\n";

			return ExitCode::OK;
		}

	}