<?php
	/**
	 * Title: OpenSubUserJob
	 * User: sym
	 * Date: 2021/3/30
	 * Time: 17:09
	 *
	 * @remark 更新外部联系人权限，把未启用的子账户打开（如果子账户剩余数量足够）
	 */

	namespace app\queue;

	use app\models\SubUser;
	use app\models\SubUserAuthority;
	use app\models\User;
	use app\models\UserAuthorRelation;
	use app\models\UserCorpRelation;
	use app\models\WorkUser;
	use app\models\WxAuthorize;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class OpenSubUserJob extends BaseObject implements JobInterface
	{
		public $corp_id;
		public $uid;

		public function execute ($queue)
		{

			$subNum = SubUser::find()->where(["status" => 1, "uid" => $this->uid])->count();
			$user   = User::findOne($this->uid);
			$num    = $user->sub_num - $subNum;
			\Yii::error([$num, $user->sub_num, $subNum], 'error-data');
			if ($num <= 0 && !empty($user->sub_num)) {
				return;
			}
			$userData    = WorkUser::find()->alias("a")
				->leftJoin("{{%sub_user}} as b", "a.mobile = b.account")
				->leftJoin("{{%sub_user_profile}} as c", "b.sub_id = c.sub_user_id")
				->where(["a.corp_id" => $this->corp_id, "b.uid" => $this->uid, "a.status" => 1, "a.is_external" => 1, "b.status" => 0])
				->select("a.is_external,b.sub_id,b.account,c.name,c.sex,c.department,c.position")->orderBy(["b.create_time" => SORT_DESC])->asArray()->all();
			$defaultAuth = SubUserAuthority::getDisabledParams(); //外部联系
			if (count($userData) > $num && !empty($user->sub_num)) {
				$userData = array_slice($userData, 0, $num);
			}
			foreach ($userData as $datum) {
				try {
					$tempData2 = ["type" => 1, "list" => ["wx" => [], "min" => []]];
					$tempWx    = UserAuthorRelation::find()->alias("a")
						->leftJoin("{{%wx_authorize}} as b", "a.author_id = b.author_id")
						->leftJoin("{{%wx_authorize_info}} as c", "a.author_id = c.author_id");
					$tempWx    = $tempWx->where(["!=", "b.authorizer_type", WxAuthorize::AUTH_TYPE_UNAUTH]);
					if (!empty($datum["sub_id"])) {
						$tempWx = $tempWx->leftJoin("{{%sub_user}} as d", "a.uid = d.uid");
						$tempWx = $tempWx->andWhere(["d.sub_id" => $datum["sub_id"]]);
					}
					$tempWx = $tempWx->select("a.author_id,b.auth_type,c.user_name")->asArray()->all();
					foreach ($tempWx as $record) {
						$wxTemp         = [];
						$wxTemp["id"]   = $record["author_id"];
						$wxTemp["list"] = [];
						if ($record["auth_type"] == 1) {
							$tempData2["list"]["wx"][] = $wxTemp;
						} else {
							$tempData2["list"]["min"][] = $wxTemp;
						}
					}
					$authority_ids[] = $tempData2;
					$data2           = UserCorpRelation::find()->alias("a")
						->leftJoin("{{%sub_user}} as b", "a.uid = b.uid")
						->leftJoin("{{%work_corp}} as d", "a.corp_id = d.id")
						->where(["b.sub_id" => $datum["sub_id"], "d.corp_type" => "verified"])
						->select("a.corp_id,b.account,d.id")
						->orderBy("a.corp_id asc")->asArray()->all();
					$tempData        = ["type" => 2, "list" => []];
					foreach ($data2 as $values) {
//						$workUser     = WorkUser::find()->where(["corp_id" => $values["corp_id"], "mobile" => $values["account"], "is_external" => 1])->exists();
						$temp["list"] = $defaultAuth;
//						if ($workUser) {
//							$temp["list"] = $defaultAuth;
//						}
						$temp["id"]         = $values['corp_id'];
						$tempData["list"][] = $temp;
					}
					$authority_ids[]       = $tempData;
					$authority_ids[]       = ["type" => 3, "list" => [["id" => 0, "list" => []]]];
					$authority_ids[]       = [];
					$data['uid']           = $this->uid;
					$data['name']          = $datum["name"];
					$data['account']       = $datum["account"];
					$data['sex']           = $datum["sex"];
					$data['department']    = $datum["position"];
					$data['position']      = '';
					$data['status']        = 1;
					$data['password']      = 'm123456';
					$data['sub_id']        = $datum["sub_id"];
					$data['authority_ids'] = $authority_ids;
					$data['type']          = 0;
					$data['company_name']  = '';
					$data['local_path']    = '';
					$data['source']        = 0;
					\Yii::error($datum["sub_id"], 'sub_id');

					if (empty($source) && !empty($datum["position"]) && $datum["position"] == "创建人") {
						continue;
					}
					SubUser::add($data);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'error-data');
					\Yii::error($e->getFile(), 'error-data');
					\Yii::error($e->getLine(), 'error-data');
					continue;
				}
			}

		}
	}