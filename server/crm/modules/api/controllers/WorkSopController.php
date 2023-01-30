<?php
	/**
	 * Create by PhpStorm
	 * title: 客户SOP运营
	 * User: fulu
	 * Date: 2021/01/05
	 */

	namespace app\modules\api\controllers;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\models\CustomFieldValue;
	use app\models\Follow;
	use app\models\SubUser;
	use app\models\SubUserProfile;
	use app\models\User;
	use app\models\UserProfile;
	use app\models\WorkChat;
	use app\models\WorkChatInfo;
	use app\models\WorkCorpAgent;
	use app\models\WorkDepartment;
	use app\models\WorkExternalContact;
	use app\models\WorkSop;
	use app\models\WorkSopMsgSending;
	use app\models\WorkTaskTag;
	use app\models\WorkUser;
	use app\models\WorkTagFollowUser;
	use app\modules\api\components\WorkBaseController;
	use app\queue\WorkSopMsgSendingJob;
	use app\util\SUtils;
	use Symfony\Component\CssSelector\Parser\Shortcut\EmptyStringParser;
	use yii\db\Expression;
	use yii\web\MethodNotAllowedHttpException;
	use moonland\phpexcel\Excel;

	class WorkSopController extends WorkBaseController
	{

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           设置SOP规则
		 * @description     设置SOP规则
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/add-sop
		 *
		 * @param corp_id                 必选 string 企业微信id
		 * @param isMasterAccount         必选 int 1主账户2子账户
		 * @param uid                     必选 int 用户ID
		 * @param sub_id                  必选 int 子账户ID
		 * @param sop_id                  可选 int SOP规则ID（修改时）
		 * @param is_chat                 必选 int 是否群SOP规则1是0否
		 * @param type                    可选 int 个人SOP类型：1新客培育、2客户生命周期
		 * @param title                   必选 string 规则名称
		 * @param user_ids                必选 array 规则成员集合（is_chat=0）/群id集合（is_chat=1）
		 * @param follow_id               可选 int 跟进状态id(type=2)
		 * @param is_all                  可选 int 是否全部客户1是0否(type=2)
		 * @param task_id                 可选 int 任务标签id(type=2)
		 * @param no_send_type            必选 int 不推送时间段1开启0关闭
		 * @param no_send_stime           可选 string 不推送时间段开始时间
		 * @param no_send_etime           可选 string 不推送时间段结束时间
		 * @param timeData                必选 array 规则时间及内容数据
		 * @param timeData.sop_time_id    可选 int 规则时间id（修改时）
		 * @param timeData.time_type      必选 int 提醒时间分类，1、x时x分后、2：x天后时间
		 * @param timeData.time_one       可选 string 时间一
		 * @param timeData.time_two       可选 string 时间二
		 * @param timeData.contentData    必选 array 内容数据
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/05
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionAddSop ()
		{
			if (empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$isMasterAccount = \Yii::$app->request->post("isMasterAccount");
			$sub_id          = \Yii::$app->request->post("sub_id", 0);
			$sop_id          = \Yii::$app->request->post("sop_id", 0);
			$is_chat         = \Yii::$app->request->post("is_chat", 0);
			$type            = \Yii::$app->request->post("type", 0);
			$title           = \Yii::$app->request->post("title");
			$user_ids        = \Yii::$app->request->post("user_ids", []);
			$title           = trim($title);

			if ($is_chat == 0 && !in_array($type, [1, 2])) {
				throw new InvalidParameterException('SOP类型错误！');
			}
			if (empty($title)) {
				throw new InvalidParameterException('规则名称不能为空！');
			}
			if (empty($user_ids)) {
				if ($is_chat == 0){
					throw new InvalidParameterException('执行人不能为空！');
				}else{
					throw new InvalidParameterException('客户群不能为空！');
				}
			}

			$workAgent = WorkCorpAgent::findOne(['corp_id' => $this->corp->id, 'is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT]);
			if (empty($workAgent)) {
				throw new InvalidParameterException('请前往“自建应用”设置自建应用，才可使用');
			}

			//群主集合
			$chat_ids = [];
			if ($is_chat == 1){
				$chat_ids = $user_ids;
				$workChat = WorkChat::find()->where(['id' => $user_ids])->select('owner_id')->all();
				$user_ids = [];
				foreach ($workChat as $v){
					array_push($user_ids, $v->owner_id);
				}
			}

			$data             = [];
			$data['uid']      = $this->user->uid;
			$data['sub_id']   = $isMasterAccount == 1 ? 0 : $sub_id;
			$data['corp_id']  = $this->corp->id;
			$data['sop_id']   = $sop_id;
			$data['is_chat']  = $is_chat;
			$data['type']     = $type;
			$data['title']    = $title;
			$data['user_ids'] = $user_ids;
			$data['chat_ids'] = $chat_ids;
			if ($data['sub_id']) {
				$subUser = SubUser::findOne(['sub_id' => $data['sub_id']]);
				if (!empty($subUser) && !empty($subUser->account)) {
					$workUser               = WorkUser::findOne(['corp_id' => $this->corp->id, 'mobile' => $subUser->account, 'is_del' => 0]);
					$data['create_user_id'] = !empty($workUser) ? $workUser->id : 0;
				}
			}else{
				$user = User::findOne(['uid' => $data['uid']]);
				if (!empty($user) && !empty($user->account)){
					$workUser               = WorkUser::findOne(['corp_id' => $this->corp->id, 'mobile' => $user->account, 'is_del' => 0]);
					$data['create_user_id'] = !empty($workUser) ? $workUser->id : 0;
				}
			}
			if ($type == 2) {
				$data['follow_id'] = \Yii::$app->request->post("follow_id", 0);
				$data['is_all']    = \Yii::$app->request->post("is_all", 1);
				$data['task_id']   = \Yii::$app->request->post("task_id", 0);

				if (empty($data['follow_id'])) {
					throw new InvalidParameterException('跟进状态不能为空！');
				}
				if (empty($data['is_all']) && $data['task_id'] == 0) {
					throw new InvalidParameterException('请设置目标客户！');
				}
			}

			$data['no_send_type']  = \Yii::$app->request->post("no_send_type", 1);
			$data['no_send_stime'] = \Yii::$app->request->post("no_send_stime", '');
			$data['no_send_etime'] = \Yii::$app->request->post("no_send_etime", '');

			if ($data['no_send_type'] == 1 && (empty($data['no_send_stime']) || empty($data['no_send_etime']))) {
				throw new InvalidParameterException('请补全不推送时间段！');
			}

			$data['timeData'] = \Yii::$app->request->post("timeData", []);

			if (empty($data['timeData'])) {
				throw new InvalidParameterException('请设置规则时间及内容数据！');
			}

			WorkSop::setSop($data);

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP规则已有员工
		 * @description     SOP规则已有员工
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-has-user
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param type      必选 string 1新客培育、2客户跟进
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    hasSopUser array 员工id集合
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/02/01
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopHasUser ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}
			$type = \Yii::$app->request->post("type");
			if (empty($this->corp) || empty($type)) {
				throw new InvalidParameterException('参数不正确！');
			}

			//已设置过SOP规则的员工
			$hasSopUserData = WorkSop::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 0, 'type' => $type, 'is_del' => 0])->asArray()->all();
			$hasSopUser     = [];
			foreach ($hasSopUserData as $v) {
				$hasUser    = explode(',', $v['user_ids']);
				$hasSopUser = array_merge($hasSopUser, $hasUser);
			}
			if ($hasSopUser) {
				$hasSopUser = array_unique($hasSopUser);
				//$now_sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
				$now_sub_id = 0;
				$Temp       = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($hasSopUser);
				$hasSopUser = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 0, true, 0, [], $now_sub_id);
			}

			$sopUser = [];
			foreach ($hasSopUser as $v){
				array_push($sopUser, $v);
			}

			return [
				'hasSopUser' => $sopUser,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP规则列表
		 * @description     SOP规则列表
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-list
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param is_chat   必选 int 是否群SOP规则1是0否
		 * @param type      可选 int 个人SOP类型：1新客培育、2客户生命周期
		 * @param follow_id 可选 int 跟进状态（type==2）
		 * @param title     可选 string 规则名称
		 * @param sub_id    可选 array 创建人员工集合
		 * @param user_ids  可选 array 执行人集合/群主集合
		 * @param chat_name 可选 string 群名称
		 * @param status    可选 int 状态:-1全部1开启0关闭
		 * @param stime     可选 string 开始时间
		 * @param etime     可选 string 结束时间
		 * @param page      可选 int 页码
		 * @param page_size 可选 int 页数
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 数据条数
		 * @return_param    list array 数据信息
		 * @return_param    list.sop_id int 规则id
		 * @return_param    list.title string 规则名称
		 * @return_param    list.creat_name string 创建人
		 * @return_param    list.user_name string 执行人
		 * @return_param    list.status int 状态1开启0关闭
		 * @return_param    list.can_edit int 是否可编辑1是0否
		 * @return_param    list.create_time string 创建时间
		 * @return_param    list.is_all int 是否全部客户1是0否
		 * @return_param    list.task_name string 标签名称
		 * @return_param    list.follow_name string 跟进状态
		 * @return_param    list.chat_data array 群列表数据（is_chat=1时）
		 * @return_param    list.chat_data.name string 群名称
		 * @return_param    list.chat_data.member_num int 群人数
		 * @return_param    list.chat_data.avatarData array 群头像
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/05
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}
			if (empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$is_chat   = \Yii::$app->request->post('is_chat', 0);
			$type      = \Yii::$app->request->post('type', 0);
			$title     = \Yii::$app->request->post('title', '');
			$follow_id = \Yii::$app->request->post('follow_id', 0);
			//$sub_id    = \Yii::$app->request->post('sub_id', '-1');
			$sub_id    = \Yii::$app->request->post('sub_id', []);
			$user_ids  = \Yii::$app->request->post('user_ids', []);
			$chat_name = \Yii::$app->request->post('chat_name', '');
			$status    = \Yii::$app->request->post('status', '-1');
			$stime     = \Yii::$app->request->post('stime', '');
			$etime     = \Yii::$app->request->post('etime', '');
			$page      = \Yii::$app->request->post('page') ?: 1;
			$pageSize  = \Yii::$app->request->post('page_size') ?: 15;

			$workSop = WorkSop::find()->where(['corp_id' => $this->corp->id, 'is_chat' => $is_chat, 'is_del' => 0]);

			if ($is_chat == 0){
				$workSop = $workSop->andWhere(['type' => $type]);
			}
			if ($type == 2 && !empty($follow_id)){
				$workSop = $workSop->andWhere(['follow_id' => $follow_id]);
			}
			if (!empty(trim($title))) {
				$workSop = $workSop->andWhere(['like', 'title', trim($title)]);
			}
			$now_sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
			if ($now_sub_id) {
				//子账户创建者及可见执行人
				[$subUser, $subDepartment, $all, $subDepartmentOld] = WorkDepartment::GiveSubIdReturnDepartmentOrUser($this->corp->id, $now_sub_id);
			}

			if (!empty($sub_id)) {
				$Temp            = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($sub_id);
				$create_user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 0, true);
				$create_user_ids = empty($create_user_ids) ? ['-1'] : $create_user_ids;
				$workSop         = $workSop->andWhere(['create_user_id' => $create_user_ids]);
			}
			//子帐号可见的执行人
			if ($now_sub_id) {
				if ($all == true){
					$subUserWhere = 'sub_id = ' . $now_sub_id;
					foreach ($subUser as $user_id_sub){
						$subUserWhere .= " or find_in_set ($user_id_sub,user_ids) ";
					}
					if ($is_chat == 0){
						if ($subUser){
							//子账户可见员工所属部门及父部门
							$workUserSub = WorkUser::find()->where(['id' => $subUser])->select('department')->all();
							foreach ($workUserSub as $wu){
								if ($wu->department) {
									$department = explode(',', $wu->department);
									foreach ($department as $dep) {
										$parentId = \Yii::$app->db->createCommand("SELECT getParentList(" . $dep . "," . $this->corp->id . ") as department;")->queryOne();
										if (!empty($parentId)) {
											$parentId       = explode(",", $parentId["department"]);
											$departmentData = WorkDepartment::find()->where(['department_id' => $parentId, "corp_id" => $this->corp->id, "is_del" => 0])->select('department_id')->asArray()->all();
											if (!empty($departmentData)) {
												foreach ($departmentData as $vv){
													$key       = 'd-' . $vv['department_id'];
													$subUserWhere .= " or find_in_set ('$key',user_ids)";
												}
											}
										}
									}
								}
							}
						}
						foreach ($subDepartment as $dep_id_sub){
							$dep_id_sub = 'd-' . $dep_id_sub;
							$subUserWhere .= " or find_in_set ('$dep_id_sub',user_ids) ";
						}
					}

					$workSop = $workSop->andWhere($subUserWhere);
				}
			}

			if(!empty($user_ids)){
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 0, true,0,[],$now_sub_id);
				$user_ids = empty($user_ids) ? [0] : $user_ids;
			}

			if ($is_chat == 0){
				if (!empty($user_ids)) {
					$userWhere = '';
					foreach ($user_ids as $k => $v) {
						if (!empty($v)){
							$workUser = WorkUser::findOne($v);
							if (empty($userWhere)) {
								$userWhere = "find_in_set ($workUser->id,user_ids) ";
							} else {
								$userWhere .= " or find_in_set ($workUser->id,user_ids) ";
							}
							if ($workUser->department) {
								$department = explode(',', $workUser->department);
								foreach ($department as $dep) {
									$key       = 'd-' . $dep;
									$userWhere .= " or find_in_set ('$key',user_ids)";
								}
							}
						}
					}
					if ($userWhere){
						$workSop = $workSop->andWhere($userWhere);
					}
				}
			}else{
				if (!empty($user_ids)){
					$userWhere = '';
					foreach ($user_ids as $k => $v){
						if (empty($userWhere)){
							$userWhere = "find_in_set ($v,user_ids) ";
						}else{
							$userWhere .= " or find_in_set ($v,user_ids)";
						}
					}
					$workSop = $workSop->andWhere($userWhere);
				}
				if (!empty(trim($chat_name))) {
					$userWhere = '';
					$workChat  = WorkChat::find()->where(['corp_id' => $this->corp->id])->andWhere(['!=', 'status', 4]);
					$workChat  = $workChat->andWhere(['like', 'name', trim($chat_name)]);
					$workChat  = $workChat->select('id')->all();
					if (!empty($workChat)) {
						foreach ($workChat as $k => $v) {
							if (empty($userWhere)) {
								$userWhere = "find_in_set ($v->id,chat_ids) ";
							} else {
								$userWhere .= " or find_in_set ($v->id,chat_ids)";
							}
						}
						$workSop = $workSop->andWhere($userWhere);
					} else {
						$workSop = $workSop->andWhere(['chat_ids' => 0]);
					}
				}
			}

			if ($status != '-1') {
				$workSop = $workSop->andWhere(['status' => $status]);
			}
			if ($stime && $etime) {
				$workSop = $workSop->andFilterWhere(['between', 'create_time', strtotime($stime), strtotime($etime)]);
			}

			$count   = $workSop->count();
			$offset  = ($page - 1) * $pageSize;
			$workSop = $workSop->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();

			$sopList = [];
			foreach ($workSop as $k => $v) {
				$sopData                = [];
				$sopData['sop_id']      = $v['id'];
				$sopData['title']       = $v['title'];
				$sopData['status']      = $v['status'];
				$sopData['create_time'] = $v['create_time'] ? date('Y-m-d H:i', $v['create_time']) : '--';
				$creat_name             = '总经理';
				if ($v['sub_id']) {
					$subInfo    = SubUserProfile::findOne(['sub_user_id' => $v['sub_id']]);
					if (!empty($subInfo)){
						$creat_name = !empty($subInfo->department) ? $subInfo->name . '（' . $subInfo->department . '）' : $subInfo->name;
					}
				}else{
					if ($v['create_user_id']){
						$workUser = WorkUser::findOne($v['create_user_id']);
						if (!empty($workUser)){
							$creat_name = $workUser->name;
						}
					}else{
						$userInfo = UserProfile::findOne(['uid' => $v['uid']]);
						if (!empty($userInfo) && !empty($userInfo->nick_name)){
							$creat_name = !empty($userInfo->department) ? $userInfo->nick_name . '（' . $userInfo->department . '）' : $userInfo->nick_name;
						}
					}
				}
				$sopData['creat_name'] = $creat_name;
				if ($is_chat == 0){
					$user_names = [];
					$user_ids   = explode(',', $v['user_ids']);
					$Temp       = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
					if ($Temp["user"]) {
						$workUser = WorkUser::find()->where(['id' => $Temp["user"]])->all();
						foreach ($workUser as $user) {
							if ($now_sub_id && $all == true && !in_array($user->id, $subUser)){
								continue;
							}
							$user_key                = [];
							$user_key['id']          = $user->id;
							$user_key['title']       = $user->name;
							$user_key['scopedSlots'] = ['title' => 'custom'];

							$user_names[] = $user_key;
						}
					}

					if ($now_sub_id && $all == true && $Temp["department"] && isset($workUserSub) && !empty($workUserSub)){
						//子账户可见员工所属部门及父部门
						foreach ($workUserSub as $wu){
							if ($wu->department) {
								$department = explode(',', $wu->department);
								foreach ($department as $dep) {
									$parentId = \Yii::$app->db->createCommand("SELECT getParentList(" . $dep . "," . $this->corp->id . ") as department;")->queryOne();
									if (!empty($parentId)) {
										$parentId       = explode(",", $parentId["department"]);
										$departmentData = WorkDepartment::find()->where(['department_id' => $parentId, "corp_id" => $this->corp->id, "is_del" => 0])->select('department_id,name')->asArray()->all();
										if (!empty($departmentData)) {
											foreach ($departmentData as $vv){
												if (in_array($vv['department_id'], $Temp["department"])){
													$user_key                = [];
													$user_key['id']          = 'd-' . $vv['department_id'];
													$user_key['ids']         = $vv['department_id'];
													$user_key['title']       = $vv['name'];
													$user_key['scopedSlots'] = ['title' => 'title'];

													$user_names[] = $user_key;

													$Temp["department"] = array_diff($Temp["department"], [$vv['department_id']]);
												}
											}
										}
									}
								}
							}
						}
					}

					if ($Temp["department"]) {
						$department = WorkDepartment::find()->where(['corp_id' => $this->corp->id,'department_id' => $Temp["department"]])->asArray()->all();
						foreach ($department as $dep) {
							if ($now_sub_id && $all == true && !in_array($dep['department_id'], $subDepartment)){
								continue;
							}
							$user_key                = [];
							$user_key['id']          = 'd-' . $dep['department_id'];
							$user_key['ids']         = $dep['department_id'];
							$user_key['title']       = $dep['name'];
							$user_key['scopedSlots'] = ['title' => 'title'];

							$user_names[] = $user_key;
						}
					}
					$sopData['user_name'] = $user_names;
				}else{
					$chatData = [];
					$chat_ids = explode(',', $v['chat_ids']);
					foreach ($chat_ids as $chat_id) {
						$workChat = WorkChat::findOne($chat_id);
						if ($now_sub_id && $all == true){
							if (!in_array($workChat->owner_id, $subUser)){
								continue;
							}
						}
						$chatD               = [];
						$chatD['name']       = WorkChat::getChatName($chat_id);
						$chatD['member_num'] = WorkChatInfo::find()->andWhere(['chat_id' => $chat_id, 'status' => 1])->count();
						$chatD['avatarData'] = WorkChat::getChatAvatar($chat_id);
						$workUser = WorkUser::findOne($workChat->owner_id);
						$chatD['ownerName'] = !empty($workUser) ? $workUser->name : '--';

						$chatData[] = $chatD;
					}
					$sopData['chat_data'] = $chatData;
				}
				$sopData['is_all'] = $v['is_all'];
				$task_name         = '';
				if ($v['task_id'] > 0) {
					$taskTag   = WorkTaskTag::findOne($v['task_id']);
					$task_name = !empty($taskTag) ? $taskTag->tagname : '';
				}
				$sopData['task_name'] = $task_name;
				$follow_name          = '';
				if ($v['follow_id'] > 0) {
					$follow      = Follow::findOne($v['follow_id']);
					$follow_name = !empty($follow) ? $follow->title : '';
				}
				$sopData['follow_name'] = $follow_name;
				$sopData['can_edit']    = $v['sub_id'] != $now_sub_id ? 0 : 1;

				array_push($sopList, $sopData);
			}

			return [
				'count' => $count,
				'list'  => $sopList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP规则详情
		 * @description     SOP规则详情
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-detail
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param sop_id    必选 int  SOP规则id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    sop_id int SOP规则id
		 * @return_param    is_chat int 是否群SOP规则1是0否
		 * @return_param    type int SOP类型：1新客培育、2客户生命周期
		 * @return_param    title string 规则名称
		 * @return_param    user_ids string 执行员工id
		 * @return_param    user_name array 执行员工数据
		 * @return_param    chat_data array 执行群数据
		 * @return_param    follow_id int 跟进状态id(type=2)
		 * @return_param    is_all int 是否全部客户1是0否(type=2)
		 * @return_param    task_id int 任务标签id(type=2)
		 * @return_param    task_name string 任务标签名称(type=2)
		 * @return_param    no_send_type int 不推送时间段1开启0关闭
		 * @return_param    no_send_stime string 不推送时间段开始时间
		 * @return_param    no_send_etime string 不推送时间段结束时间
		 * @return_param    timeData array 规则时间及内容数据
		 * @return_param    timeData.sop_time_id int 规则时间id
		 * @return_param    timeData.time_type int 提醒时间分类，1、x时x分后、2：x天后时间
		 * @return_param    timeData.time_one string 时间一
		 * @return_param    timeData.time_two string 时间二
		 * @return_param    timeData.over_num int 完成数量
		 * @return_param    timeData.not_over_num int 未完成数量
		 * @return_param    timeData.contentData array 内容数据
		 * @return_param    timeData.contentData.sop_content_id int 规则内容id
		 * @return_param    timeData.contentData.file_type int 内容类型：1图片、3视频、4图文
		 * @return_param    timeData.contentData.content array 内容数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/05
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopDetail ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}
			if (empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$sop_id = \Yii::$app->request->post('sop_id');

			if (empty($sop_id)) {
				throw new InvalidParameterException('SOP参数不正确！');
			}

			$now_sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
			$sopInfo    = WorkSop::getSop($sop_id, $now_sub_id);

			return [
				'sopInfo' => $sopInfo
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP规则状态变更
		 * @description     SOP规则状态变更
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-status-set
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param sop_id    必选 int  SOP规则id
		 * @param status    必选 int  状态：0关闭1开启2删除
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/05
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopStatusSet ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$sop_id = \Yii::$app->request->post('sop_id');
			$status = \Yii::$app->request->post('status');

			if (empty($sop_id) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}
			if (!in_array($status, [0, 1, 2])) {
				throw new InvalidParameterException('状态参数不正确！');
			}
			$workSop = WorkSop::findOne($sop_id);
			if (empty($workSop)) {
				throw new InvalidParameterException('规则参数错误');
			}

			if ($status == 2) {
				$workSop->is_del = 1;
			} else {
				$workSop->status = $status;
			}

			if (!$workSop->validate() || !$workSop->save()) {
				throw new InvalidParameterException(SUtils::modelError($workSop));
			}

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP消息列表
		 * @description     SOP消息列表
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-msg-list
		 *
		 * @param corp_id        必选 string 企业唯一标志
		 * @param sop_id         必选 int SOP规则id
		 * @param sop_time_id    可选 int SOP时间规则id
		 * @param is_over        必选 int  状态：0未完成1已完成
		 * @param name           可选 string 客户昵称
		 * @param user_ids       可选 array 执行人集合
		 * @param stime          可选 string 开始时间
		 * @param etime          可选 string 结束时间
		 * @param page           可选 int 页码
		 * @param page_size      可选 int 页数
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 数据条数
		 * @return_param    all_msg array 全部消息id集合
		 * @return_param    list array 数据信息
		 * @return_param    list.msg_id int 消息id
		 * @return_param    list.content array 内容数据
		 * @return_param    list.push_time string 推送时间
		 * @return_param    list.is_over int 是否完成1是0否
		 * @return_param    list.over_time string 完成时间
		 * @return_param    list.nickname string 客户昵称
		 * @return_param    list.avatar string 客户头像
		 * @return_param    list.user_name string 执行人
		 * @return_param    list.can_over int 是否可完成1是0否
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/07
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopMsgList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$sop_id      = \Yii::$app->request->post('sop_id');
			$sop_time_id = \Yii::$app->request->post('sop_time_id', 0);
			$is_over     = \Yii::$app->request->post('is_over', 0);
			$name        = \Yii::$app->request->post('name', '');
			$user_ids    = \Yii::$app->request->post('user_ids', []);
			$stime       = \Yii::$app->request->post('stime', '');
			$etime       = \Yii::$app->request->post('etime', '');
			$page        = \Yii::$app->request->post('page') ?: 1;
			$pageSize    = \Yii::$app->request->post('page_size') ?: 15;
			$name        = trim($name);

			if (empty($this->corp) || empty($sop_id)) {
				throw new InvalidParameterException('缺少必要参数！');
			}
			if (!in_array($is_over, [0, 1])) {
				throw new InvalidParameterException('状态参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->alias('m');
			$sopMsg = $sopMsg->leftJoin('{{%work_external_contact}} we', '`m`.`external_id` = `we`.`id`');
			$sopMsg = $sopMsg->andWhere(['m.sop_id' => $sop_id, 'm.status' => 1, 'm.is_over' => $is_over, 'm.is_del' => 0]);

			if ($sop_time_id){
				$sopMsg = $sopMsg->andWhere(['m.sop_time_id' => $sop_time_id]);
			}
			if (!empty($name)) {
				$sopMsg = $sopMsg->andWhere(['like', 'we.name_convert', $name]);
			}

			$now_sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
			$subUserId  = 0;
			if ($now_sub_id && !empty($this->subUser->account)) {
				$workUserSub = WorkUser::findOne(['corp_id' => $this->corp->id, 'mobile' => $this->subUser->account, 'is_del' => 0]);
				$subUserId   = !empty($workUserSub) ? $workUserSub->id : 0;
			}
			if (!empty($user_ids)) {
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 0, true, 0, [], $now_sub_id);
				$user_ids = empty($user_ids) ? [0] : $user_ids;
			} else {
				[$subUser, $subDepartment, $all, $subDepartmentOld] = WorkDepartment::GiveSubIdReturnDepartmentOrUser($this->corp->id, $now_sub_id);
				if ($all == true) {
					$user_ids = $subUser;
				}
			}
			if (!empty($user_ids)) {
				$sopMsg = $sopMsg->andWhere(['m.user_id' => $user_ids]);
			}
			if (!empty($stime) && !empty($etime)) {
				$sopMsg = $sopMsg->andFilterWhere(['between', 'm.push_time', strtotime($stime), strtotime($etime)]);
			}

			$countData = $sopMsg->select('m.`id`')->all();
			$count     = count($countData);

			$all_msg = [];
			foreach ($countData as $k => $v) {
				array_push($all_msg, $v->id);
			}

			$offset = ($page - 1) * $pageSize;
			$sopMsg = $sopMsg->limit($pageSize)->offset($offset);
			$sopMsg = $sopMsg->select('m.id,m.content,m.user_id,m.external_id,m.push_time,m.is_over,m.over_time,we.name_convert,we.avatar,we.gender,we.corp_name')->orderBy(['m.push_time' => SORT_DESC])->asArray()->all();

			$msgList = [];
			foreach ($sopMsg as $k => $v) {
				$msgData              = [];
				$msgData['msg_id']    = $v['id'];
				$msgData['content']   = $v['content'];
				$msgData['push_time'] = $v['push_time'] ? date('Y-m-d H:i', $v['push_time']) : '--';
				$msgData['is_over']   = $v['is_over'];
				$msgData['over_time'] = $v['over_time'] ? date('Y-m-d H:i', $v['over_time']) : '--';
				$msgData['nickname']  = $v['name_convert'];
				$msgData['avatar']    = $v['avatar'];
				$msgData['corp_name'] = $v['corp_name'];

				if ($v['gender'] == 1) {
					$gender = '男性';
				} elseif ($v['gender'] == 2) {
					$gender = '女性';
				} else {
					$gender = '未知';
				}
				$fieldSex = CustomFieldValue::findOne(['cid' => $v['external_id'], 'type' => 1, 'fieldid' => 3]);
				if ($fieldSex) {
					if ($fieldSex->value == '男') {
						$gender = '男性';
					} elseif ($fieldSex->value == '女') {
						$gender = '女性';
					} else {
						$gender = '未知';
					}
				}
				$msgData['gender'] = $gender;

				$user_name = '--';
				$workUser  = WorkUser::findOne($v['user_id']);
				if (!empty($workUser)) {
					$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
					$user_name  = $workUser->name . '（' . $departName . '）';
				}
				$msgData['user_name'] = $user_name;
				$msgData['can_over']  = $v['is_over'] == 0 && $v['user_id'] == $subUserId ? 1 : 0;

				array_push($msgList, $msgData);
			}

			return [
				'count'   => $count,
				'all_msg' => $all_msg,
				'list'    => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           群SOP消息列表
		 * @description     群SOP消息列表
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-chat-msg-list
		 *
		 * @param corp_id        必选 string 企业唯一标志
		 * @param sop_id         必选 int SOP规则id
		 * @param sop_time_id    可选 int SOP时间规则id
		 * @param is_over        必选 int  状态：0未完成1已完成
		 * @param name           可选 string 群名称
		 * @param user_ids       可选 array 执行人集合
		 * @param stime          可选 string 开始时间
		 * @param etime          可选 string 结束时间
		 * @param page           可选 int 页码
		 * @param page_size      可选 int 页数
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 数据条数
		 * @return_param    all_msg array 全部消息id集合
		 * @return_param    list array 数据信息
		 * @return_param    list.msg_id int 消息id
		 * @return_param    list.content array 内容数据
		 * @return_param    list.push_time string 推送时间
		 * @return_param    list.is_over int 是否完成1是0否
		 * @return_param    list.over_time string 完成时间
		 * @return_param    list.name string 群名称
		 * @return_param    list.member_num int 群人数
		 * @return_param    list.avatarData array 群头像
		 * @return_param    list.user_name string 执行人
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/21
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopChatMsgList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$sop_id      = \Yii::$app->request->post('sop_id');
			$sop_time_id = \Yii::$app->request->post('sop_time_id', 0);
			$is_over     = \Yii::$app->request->post('is_over', 0);
			$name        = \Yii::$app->request->post('name', '');
			$user_ids    = \Yii::$app->request->post('user_ids', []);
			$stime       = \Yii::$app->request->post('stime', '');
			$etime       = \Yii::$app->request->post('etime', '');
			$page        = \Yii::$app->request->post('page') ?: 1;
			$pageSize    = \Yii::$app->request->post('page_size') ?: 15;
			$name        = trim($name);

			if (empty($this->corp) || empty($sop_id)) {
				throw new InvalidParameterException('缺少必要参数！');
			}
			if (!in_array($is_over, [0, 1])) {
				throw new InvalidParameterException('状态参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->alias('m');
			$sopMsg = $sopMsg->leftJoin('{{%work_chat}} we', '`m`.`external_id` = `we`.`id`');
			$sopMsg = $sopMsg->leftJoin('{{%work_user}} wu', '`m`.`user_id` = `wu`.`id`');
			$sopMsg = $sopMsg->andWhere(['m.sop_id' => $sop_id, 'm.status' => 1, 'is_over' => $is_over]);

			if ($sop_time_id){
				$sopMsg = $sopMsg->andWhere(['m.sop_time_id' => $sop_time_id]);
			}
			if (!empty($name) || $name === '0') {
				$sopMsg = $sopMsg->andWhere("(we.name like '%$name%' or wu.name like '%$name%')");
			}

			$now_sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
			$subUserId  = 0;
			if ($now_sub_id && !empty($this->subUser->account)) {
				$workUserSub = WorkUser::findOne(['corp_id' => $this->corp->id, 'mobile' => $this->subUser->account, 'is_del' => 0]);
				$subUserId   = !empty($workUserSub) ? $workUserSub->id : 0;
			}
			if(!empty($user_ids)){
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 0, true,0,[],$now_sub_id);
				$user_ids = empty($user_ids) ? [0] : $user_ids;
			} else {
				[$subUser, $subDepartment, $all, $subDepartmentOld] = WorkDepartment::GiveSubIdReturnDepartmentOrUser($this->corp->id, $now_sub_id);
				if ($all == true) {
					$user_ids = $subUser;
				}
			}
			if (!empty($user_ids)) {
				$sopMsg = $sopMsg->andWhere(['m.user_id' => $user_ids]);
			}
			if (!empty($stime) && !empty($etime)) {
				$sopMsg = $sopMsg->andFilterWhere(['between', 'm.push_time', strtotime($stime), strtotime($etime)]);
			}

			$countData = $sopMsg->select('m.`id`')->all();
			$count     = count($countData);

			$all_msg = [];
			foreach ($countData as $k => $v) {
				array_push($all_msg, $v->id);
			}

			$offset = ($page - 1) * $pageSize;
			$sopMsg = $sopMsg->limit($pageSize)->offset($offset);
			$sopMsg = $sopMsg->select('m.id,m.content,m.user_id,m.external_id,m.push_time,m.is_over,m.over_time')->orderBy(['m.push_time' => SORT_DESC])->asArray()->all();

			$msgList = [];
			foreach ($sopMsg as $k => $v) {
				$msgData               = [];
				$msgData['msg_id']     = $v['id'];
				$msgData['content']    = $v['content'];
				$msgData['push_time']  = $v['push_time'] ? date('Y-m-d H:i', $v['push_time']) : '--';
				$msgData['is_over']    = $v['is_over'];
				$msgData['over_time']  = $v['over_time'] ? date('Y-m-d H:i', $v['over_time']) : '--';
				$msgData['name']       = WorkChat::getChatName($v['external_id']);
				$msgData['member_num'] = WorkChatInfo::find()->andWhere(['chat_id' => $v['external_id'], 'status' => 1])->count();
				$msgData['avatarData'] = WorkChat::getChatAvatar($v['external_id']);
				$user_name             = '--';
				$workUser              = WorkUser::findOne($v['user_id']);
				if (!empty($workUser)) {
					/*$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
					$user_name  = $workUser->name . '（' . $departName . '）';*/
					$user_name  = $workUser->name;
				}
				$msgData['ownerName'] = $user_name;
				$msgData['can_over']  = $v['is_over'] == 0 && $v['user_id'] == $subUserId ? 1 : 0;

				array_push($msgList, $msgData);
			}

			return [
				'count'   => $count,
				'all_msg' => $all_msg,
				'list'    => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP时间规则执行列表
		 * @description     SOP时间规则执行列表
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-time-user-list
		 *
		 * @param corp_id        必选 string 企业唯一标志
		 * @param sop_time_id    必选 int SOP时间规则id
		 * @param user_ids       可选 array 执行人集合
		 * @param page           可选 int 页码
		 * @param page_size      可选 int 页数
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 数据条数
		 * @return_param    list array 数据信息
		 * @return_param    list.sop_time_id int SOP时间规则id
		 * @return_param    list.over_num int 完成数量
		 * @return_param    list.not_over_num int 未完成数量
		 * @return_param    list.user_id int 员工id
		 * @return_param    list.user_name string 执行人
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/11
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopTimeUserList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$sop_time_id = \Yii::$app->request->post('sop_time_id');
			$user_ids    = \Yii::$app->request->post('user_ids', []);
			$page        = \Yii::$app->request->post('page') ?: 1;
			$pageSize    = \Yii::$app->request->post('page_size') ?: 15;

			if (empty($this->corp) || empty($sop_time_id)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			$sopMsg = WorkSopMsgSending::find()->where(['sop_time_id' => $sop_time_id, 'status' => 1]);

			$now_sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
			if (!empty($user_ids)) {
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_ids);
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, $Temp["department"], $Temp["user"], 0, true, 0, [], $now_sub_id);
				$user_ids = empty($user_ids) ? [0] : $user_ids;
			} else {
				[$subUser, $subDepartment, $all, $subDepartmentOld] = WorkDepartment::GiveSubIdReturnDepartmentOrUser($this->corp->id, $now_sub_id);
				if ($all == true) {
					$user_ids = $subUser;
				}
			}
			if (!empty($user_ids)) {
				$sopMsg = $sopMsg->andWhere(['user_id' => $user_ids]);
			}

			$count = $sopMsg->groupBy('user_id')->count();

			$field  = new Expression('sop_time_id,user_id,SUM(CASE WHEN is_over = 1 THEN 1 ELSE 0 END) over_num,SUM(CASE WHEN is_over = 0 THEN 1 ELSE 0 END) not_over_num');
			$offset = ($page - 1) * $pageSize;
			$sopMsg = $sopMsg->limit($pageSize)->offset($offset);
			$sopMsg = $sopMsg->select($field)->groupBy('user_id')->asArray()->all();

			$msgList = [];
			foreach ($sopMsg as $k => $v) {
				$msgData                 = [];
				$msgData['sop_time_id']  = $v['sop_time_id'];
				$msgData['user_id']      = $v['user_id'];
				$msgData['over_num']     = $v['over_num'];
				$msgData['not_over_num'] = $v['not_over_num'];
				$user_name               = '--';
				$workUser                = WorkUser::findOne($v['user_id']);
				if (!empty($workUser)) {
					$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
					$user_name  = $workUser->name . '（' . $departName . '）';
				}
				$msgData['user_name'] = $user_name;

				array_push($msgList, $msgData);
			}

			return [
				'count' => $count,
				'list'  => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP消息提醒操作
		 * @description     SOP消息提醒操作
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-msg-remind
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param msg_ids   必选 array  SOP消息id集合
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/11
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopMsgRemind ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$msg_ids = \Yii::$app->request->post('msg_ids');

			if (empty($msg_ids) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->where(['id' => $msg_ids])->andWhere(['status' => 1,'is_over' => 0])->asArray()->all();
			if (empty($sopMsg)) {
				throw new InvalidParameterException('没有未完成的消息');
			}

			$msgIds = [];
			foreach ($sopMsg as $v) {
				$key            = $v['user_id'] . '_' . $v['sop_time_id'];
				$msgIds[$key][] = $v['id'];
			}

			foreach ($msgIds as $msgId) {
				\Yii::$app->work->push(new WorkSopMsgSendingJob([
					'work_sop_msg_sending_id' => $msgId,
					'is_remind'               => 1,
				]));
			}

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP消息详情页（H5）
		 * @description     SOP消息详情页（H5）
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-msg-index
		 *
		 * @param corp_id        必选 string 企业唯一标志
		 * @param sop_send_id    必选 string SOP消息id
		 * @param userid         必选 string 员工userid
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    listInfo array 客户/客户群数据
		 * @return_param    listInfo.external_userid string 客户external_userid（个人sop）
		 * @return_param    listInfo.nickname string 客户昵称（个人sop）
		 * @return_param    listInfo.avatar string 客户头像（个人sop）
		 * @return_param    listInfo.chatid string 客户群ID（群sop）
		 * @return_param    listInfo.name string 群名称（群sop）
		 * @return_param    listInfo.member_num int 群人数（群sop）
		 * @return_param    listInfo.avatarData array 群头像（群sop）
		 * @return_param    listInfo.is_over int 是否完成1是0否
		 * @return_param    listInfo.msg_id int sop消息id
		 * @return_param    send_day string 消息日期
		 * @return_param    send_time string 消息时间
		 * @return_param    content array 消息内容
		 * @return_param    is_over int 是否全部完成1是0否
		 * @return_param    can_send int 是否可发送及完成1是0否
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/11
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopMsgIndex ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$sop_send_id = \Yii::$app->request->post('sop_send_id');
			$userid      = \Yii::$app->request->post('userid');

			if (empty($this->corp) || empty($sop_send_id) || empty($userid)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$sendIds   = explode('|', $sop_send_id);
			$sopMsgOne = WorkSopMsgSending::findOne($sendIds[0]);

			if (empty($sopMsgOne)) {
				throw new InvalidParameterException('消息参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数错误');
			}

			$canSend = 1;
			if ($sopMsgOne->user_id != $workUser->id){
				$canSend = 0;
			}

			$workSop = WorkSop::findOne($sopMsgOne->sop_id);

			$msgInfo  = [];
			$listInfo = [];
			$is_over  = 1;
			if ($workSop->is_chat == 0) {
				foreach ($sendIds as $sendId) {
					$listD                    = [];
					$sopMsg                   = WorkSopMsgSending::findOne($sendId);
					$externalInfo             = WorkExternalContact::findOne($sopMsg->external_id);
					$listD['external_userid'] = $externalInfo->external_userid;
					$listD['nickname']        = $externalInfo->name_convert;
					$listD['avatar']          = $externalInfo->avatar;
					$listD['is_over']         = $sopMsg->is_over;
					$listD['msg_id']          = $sopMsg->id;
					$listD['corp_name']       = $externalInfo->corp_name;

					if ($externalInfo->gender == 1) {
						$gender = '男性';
					} elseif ($externalInfo->gender == 2) {
						$gender = '女性';
					} else {
						$gender = '未知';
					}
					$fieldSex = CustomFieldValue::findOne(['cid' => $sopMsg->external_id, 'type' => 1, 'fieldid' => 3]);
					if ($fieldSex) {
						if ($fieldSex->value == '男') {
							$gender = '男性';
						} elseif ($fieldSex->value == '女') {
							$gender = '女性';
						} else {
							$gender = '未知';
						}
					}
					$listD['gender'] = $gender;

					$listInfo[]               = $listD;
					$is_over                  = $sopMsg->is_over == 0 ? 0 : $is_over;
				}
			} else {
				foreach ($sendIds as $sendId) {
					$listD               = [];
					$sopMsg              = WorkSopMsgSending::findOne($sendId);
					$workChat            = WorkChat::findOne($sopMsg->external_id);
					$listD['chatid']     = $workChat->chat_id;
					$listD['name']       = WorkChat::getChatName($workChat->id);
					$listD['member_num'] = WorkChatInfo::find()->andWhere(['chat_id' => $workChat->id, 'status' => 1])->count();
					$listD['avatarData'] = WorkChat::getChatAvatar($workChat->id);
					$listD['is_over']    = $sopMsg->is_over;
					$listD['msg_id']     = $sopMsg->id;
					$listInfo[]          = $listD;
					$is_over             = $sopMsg->is_over == 0 ? 0 : $is_over;
				}
			}
			$msgInfo['send_day']  = date('Y-m-d', $sopMsgOne->send_time);
			$msgInfo['send_time'] = date('H:i', $sopMsgOne->send_time);
			$msgInfo['content']   = json_decode($sopMsgOne->content, true);
			$msgInfo['is_over']   = $is_over;
			$msgInfo['can_send']  = $canSend;
			$msgInfo['listInfo']  = $listInfo;

			return [
				'msgInfo' => $msgInfo
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP客户消息（H5）
		 * @description     SOP客户消息（H5）
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-msg-content-one
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param external_userid    必选 string  客户external_userid
		 * @param userid             必选 string  员工userid
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count array 消息数量
		 * @return_param    list array 消息数据
		 * @return_param    list.msg_id int 消息id
		 * @return_param    list.send_day string 消息日期
		 * @return_param    list.send_time string 消息时间
		 * @return_param    list.content array 消息内容
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/11
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopMsgContentOne ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$external_userid = \Yii::$app->request->post('external_userid');
			$userid          = \Yii::$app->request->post('userid');

			if (empty($this->corp) || empty($external_userid) || empty($userid)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$externalInfo = WorkExternalContact::findOne(['corp_id' => $this->corp->id, 'external_userid' => $external_userid]);
			if (empty($externalInfo)) {
				throw new InvalidParameterException('客户参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 0, 'external_id' => $externalInfo->id, 'user_id' => $workUser->id, 'status' => 1, 'is_over' => 0]);
			$count  = $sopMsg->count();
			$sopMsg = $sopMsg->orderBy(['id' => SORT_DESC])->one();

			$msgList = [];
			if (!empty($sopMsg)) {
				$msgD              = [];
				$msgD['msg_id']    = $sopMsg->id;
				$msgD['send_day']  = date('Y-m-d', $sopMsg->send_time);
				$msgD['send_time'] = date('H:i', $sopMsg->send_time);
				$msgD['content']   = json_decode($sopMsg->content, true);
				$msgD['is_over']   = $sopMsg->is_over;

				$msgList[] = $msgD;
			}

			return [
				'count'    => $count,
				'nickname' => $externalInfo->name_convert,
				'list'     => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP客户群消息（H5）
		 * @description     SOP客户群消息（H5）
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-chat-msg-content-one
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param chatid             必选 string  客户群id
		 * @param userid             必选 string  员工userid
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 消息数量
		 * @return_param    chatName string 群名称
		 * @return_param    list array 消息数据
		 * @return_param    list.msg_id int 消息id
		 * @return_param    list.send_day string 消息日期
		 * @return_param    list.send_time string 消息时间
		 * @return_param    list.content array 消息内容
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/21
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopChatMsgContentOne ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$chatid = \Yii::$app->request->post('chatid');
			$userid = \Yii::$app->request->post('userid');

			if (empty($this->corp) || empty($chatid) || empty($userid)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$workChat = WorkChat::findOne(['corp_id' => $this->corp->id, 'chat_id' => $chatid]);
			if (empty($workChat)) {
				throw new InvalidParameterException('客户群参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 1, 'external_id' => $workChat->id, 'user_id' => $workUser->id, 'status' => 1, 'is_over' => 0]);
			$count  = $sopMsg->count();
			$sopMsg = $sopMsg->orderBy(['id' => SORT_DESC])->one();

			$msgList = [];
			if (!empty($sopMsg)) {
				$msgD              = [];
				$msgD['msg_id']    = $sopMsg->id;
				$msgD['send_day']  = date('Y-m-d', $sopMsg->send_time);
				$msgD['send_time'] = date('H:i', $sopMsg->send_time);
				$msgD['content']   = json_decode($sopMsg->content, true);
				$msgD['is_over']   = $sopMsg->is_over;

				$msgList[] = $msgD;
			}

			$chatName  = WorkChat::getChatName($workChat->id);
			$chatName  = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;
			$workUser  = WorkUser::findOne($workChat->owner_id);
			$ownerName = !empty($workUser) ? $workUser->name : '--';

			return [
				'count'     => $count,
				'chatName'  => $chatName,
				'ownerName' => $ownerName,
				'list'      => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP客户消息列表（H5）
		 * @description     SOP客户消息列表（H5）
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-msg-content-list
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param external_userid    必选 string  客户external_userid
		 * @param userid             必选 string  员工userid
		 * @param not_over           必选 int  是否未完成1是
		 * @param page               可选 int 页码
		 * @param page_size          可选 int 页数
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count array 消息数量
		 * @return_param    list array 消息数据
		 * @return_param    list.msg_id int 消息id
		 * @return_param    list.is_over int 是否完成1是
		 * @return_param    list.send_day string 消息日期
		 * @return_param    list.send_time string 消息时间
		 * @return_param    list.content array 消息内容
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/11
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopMsgContentList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$external_userid = \Yii::$app->request->post('external_userid');
			$userid          = \Yii::$app->request->post('userid');
			$not_over        = \Yii::$app->request->post('not_over') ?: 0;
			$page            = \Yii::$app->request->post('page') ?: 1;
			$pageSize        = \Yii::$app->request->post('page_size') ?: 15;

			if (empty($this->corp) || empty($external_userid) || empty($userid)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$externalInfo = WorkExternalContact::findOne(['corp_id' => $this->corp->id, 'external_userid' => $external_userid]);
			if (empty($externalInfo)) {
				throw new InvalidParameterException('客户参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 0, 'external_id' => $externalInfo->id, 'user_id' => $workUser->id, 'status' => 1]);
			if ($not_over){
				$sopMsg = $sopMsg->andWhere(['is_over' => 0]);
			}
			$count  = $sopMsg->count();

			$offset = ($page - 1) * $pageSize;
			$sopMsg = $sopMsg->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();

			$msgList = [];
			foreach ($sopMsg as $v) {
				$msgD              = [];
				$msgD['msg_id']    = $v['id'];
				$msgD['is_over']   = $v['is_over'];
				$msgD['send_day']  = date('Y-m-d', $v['send_time']);
				$msgD['send_time'] = date('H:i', $v['send_time']);
				$msgD['content']   = json_decode($v['content'], true);

				$msgList[] = $msgD;
			}

			return [
				'count'    => $count,
				'nickname' => $externalInfo->name_convert,
				'list'     => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP客户群消息列表（H5）
		 * @description     SOP客户群消息列表（H5）
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-chat-msg-content-list
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param chatid             必选 string  客户群id
		 * @param userid             必选 string  员工userid
		 * @param not_over           必选 int  是否未完成1是
		 * @param page               可选 int 页码
		 * @param page_size          可选 int 页数
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int 消息数量
		 * @return_param    chatName string 群名称
		 * @return_param    list array 消息数据
		 * @return_param    list.msg_id int 消息id
		 * @return_param    list.is_over int 是否完成1是0否
		 * @return_param    list.send_day string 消息日期
		 * @return_param    list.send_time string 消息时间
		 * @return_param    list.content array 消息内容
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/21
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopChatMsgContentList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$chatid   = \Yii::$app->request->post('chatid');
			$userid   = \Yii::$app->request->post('userid');
			$not_over = \Yii::$app->request->post('not_over') ?: 0;
			$page     = \Yii::$app->request->post('page') ?: 0;
			$pageSize = \Yii::$app->request->post('page_size') ?: 15;

			if (empty($this->corp) || empty($chatid) || empty($userid)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$workChat = WorkChat::findOne(['corp_id' => $this->corp->id, 'chat_id' => $chatid]);
			if (empty($workChat)) {
				throw new InvalidParameterException('客户群参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数不正确！');
			}

			$sopMsg = WorkSopMsgSending::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 1, 'external_id' => $workChat->id, 'user_id' => $workUser->id, 'status' => 1]);
			if ($not_over){
				$sopMsg = $sopMsg->andWhere(['is_over' => 0]);
			}
			$count  = $sopMsg->count();

			$offset = ($page - 1) * $pageSize;
			$sopMsg = $sopMsg->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();

			$msgList = [];
			foreach ($sopMsg as $v) {
				$msgD              = [];
				$msgD['msg_id']    = $v['id'];
				$msgD['is_over']   = $v['is_over'];
				$msgD['send_day']  = date('Y-m-d', $v['send_time']);
				$msgD['send_time'] = date('H:i', $v['send_time']);
				$msgD['content']   = json_decode($v['content'], true);

				$msgList[] = $msgD;
			}

			$chatName = WorkChat::getChatName($workChat->id);
			$chatName = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;

			return [
				'count'    => $count,
				'chatName' => $chatName,
				'list'     => $msgList,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP客户消息设置完成
		 * @description     SOP客户消息设置完成
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-msg-set-over
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param msg_id    必选 int  SOP消息id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/11
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopMsgSetOver ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$msg_id = \Yii::$app->request->post('msg_id');

			if (empty($msg_id) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}
			/*$sopMsg = WorkSopMsgSending::findOne($msg_id);
			if (empty($sopMsg)) {
				throw new InvalidParameterException('消息参数错误');
			}

			$sopMsg->is_over   = 1;
			$sopMsg->over_time = time();

			if (!$sopMsg->validate() || !$sopMsg->save()) {
				throw new InvalidParameterException(SUtils::modelError($sopMsg));
			}*/

			WorkSopMsgSending::updateAll(['is_over' => 1, 'over_time' => time()], ['id' => $msg_id, 'is_over' => 0]);

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP员工免打扰状态变更
		 * @description     SOP员工免打扰状态变更
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-user-msg-status-set
		 *
		 * @param corp_id   必选 string 企业唯一标志
		 * @param is_chat   必选 int  是否群SOP规则1是0否
		 * @param userid    必选 string  员工userid
		 * @param status    必选 int  免打扰状态：1开启0关闭
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/12
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopUserMsgStatusSet ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}

			$is_chat = \Yii::$app->request->post('is_chat', 0);
			$userid  = \Yii::$app->request->post('userid');
			$status  = \Yii::$app->request->post('status');

			if (empty($userid) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}
			if (!in_array($status, [0, 1])) {
				throw new InvalidParameterException('状态参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数错误');
			}

			if ($is_chat == 0){
				$workUser->sop_msg_status = $status;
			}else{
				$workUser->sop_chat_msg_status = $status;
			}

			if (!$workUser->validate() || !$workUser->save()) {
				throw new InvalidParameterException(SUtils::modelError($workUser));
			}

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP员工消息状态
		 * @description     SOP员工消息状态
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-user-msg-status
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param userid             必选 string  员工userid
		 * @param external_userid    必选 string  客户external_userid
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    sop_msg_status int SOP消息免打扰是否开启:1开启0关闭
		 * @return_param    has_sop_msg int 是否有未完成消息1是0否
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/12
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopUserMsgStatus ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}
			$userid          = \Yii::$app->request->post('userid');
			$external_userid = \Yii::$app->request->post('external_userid');

			if (empty($userid) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数错误');
			}
			$externalInfo = WorkExternalContact::findOne(['corp_id' => $this->corp->id, 'external_userid' => $external_userid]);
			if (empty($externalInfo)) {
				throw new InvalidParameterException('客户参数不正确！');
			}

			$sopMsg      = WorkSopMsgSending::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 0, 'external_id' => $externalInfo->id, 'user_id' => $workUser->id, 'status' => 1, 'is_over' => 0])->one();
			$has_sop_msg = !empty($sopMsg) ? 1 : 0;

			return [
				'sop_msg_status' => $workUser->sop_msg_status,
				'has_sop_msg'    => $has_sop_msg,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           SOP员工群消息状态
		 * @description     SOP员工群消息状态
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/sop-user-chat-msg-status
		 *
		 * @param corp_id            必选 string 企业唯一标志
		 * @param userid             必选 string  员工userid
		 * @param chatid             必选 string  客户群id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    sop_chat_msg_status int SOP群消息免打扰是否开启:1开启0关闭
		 * @return_param    has_sop_chat_msg int 是否有未完成消息1是0否
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/21
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionSopUserChatMsgStatus ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}
			$userid = \Yii::$app->request->post('userid');
			$chatid = \Yii::$app->request->post('chatid');

			if (empty($userid) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $userid]);
			if (empty($workUser)) {
				throw new InvalidParameterException('员工参数错误');
			}
			$workChat = WorkChat::findOne(['corp_id' => $this->corp->id, 'chat_id' => $chatid]);
			if (empty($workChat)) {
				throw new InvalidParameterException('客户群参数不正确！');
			}

			$sopMsg      = WorkSopMsgSending::find()->where(['corp_id' => $this->corp->id, 'is_chat' => 1, 'external_id' => $workChat->id, 'user_id' => $workUser->id, 'status' => 1, 'is_over' => 0])->one();
			$has_sop_msg = !empty($sopMsg) ? 1 : 0;

			return [
				'sop_msg_status'   => $workUser->sop_chat_msg_status,
				'has_sop_chat_msg' => $has_sop_msg,
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-sop/
		 * @title           标签客户数
		 * @description     标签客户数
		 * @method   post
		 * @url  http://{host_name}/api/work-sop/task-tag-member-num
		 *
		 * @param corp_id             必选 string 企业唯一标志
		 * @param task_id             必选 int  标签id
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    member_num int 标签客户数
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2021/01/14
		 * @number          0
		 *
		 * @throws InvalidParameterException
		 */
		public function actionTaskTagMemberNum ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException('请求方式不允许！');
			}
			$task_id = \Yii::$app->request->post('task_id');

			if (empty($task_id) || empty($this->corp)) {
				throw new InvalidParameterException('参数不正确！');
			}

			$tagFollowUser = WorkTagFollowUser::find()->where(['corp_id' => $this->corp->id, 'tag_id' => $task_id])->count();

			return [
				'member_num' => $tagFollowUser
			];
		}

	}