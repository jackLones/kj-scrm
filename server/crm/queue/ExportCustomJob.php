<?php

	namespace app\queue;

	use app\models\AwardsActivity;
	use app\models\CustomField;
	use app\models\CustomFieldValue;
	use app\models\Fans;
	use app\models\Fission;
	use app\models\Follow;
	use app\models\RedPack;
	use app\models\User;
	use app\models\WorkChatContactWay;
	use app\models\WorkChatInfo;
	use app\models\WorkContactWay;
	use app\models\WorkContactWayRedpacket;
	use app\models\WorkDepartment;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkPerTagFollowUser;
	use app\models\WorkPublicActivity;
	use app\models\WorkTagContact;
	use app\models\WorkUser;
	use moonland\phpexcel\Excel;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class ExportCustomJob extends BaseObject implements JobInterface
	{
		public $count;
		public $exportData;
		public $uid;
		public $corpId;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			try {
				\Yii::error($this->count, 'count');
				$workExternalUserData = $this->exportData;
				//高级属性搜索
				$fieldList    = CustomField::find()->where('is_define=0')->select('`id`,`key`')->asArray()->all();//默认属性
				$fieldD       = [];
				$contactField = [];
				foreach ($fieldList as $k => $v) {
					$fieldD[$v['key']] = $v['id'];
					array_push($contactField, $v['id']);
				}
				$exportData = [];
				$pageSize   = 500;
				$num        = ceil($this->count / $pageSize);
				WorkExternalContactFollowUser::exportNumWebsocket($this->uid, $this->corpId, $this->count, 0);
				$currentCount  = 0;
				$p             = 0;
				$user          = User::findOne($this->uid);
				//$is_hide_phone = $user->is_hide_phone;
				$is_hide_phone = 0;//导出展示手机号
				$site_url      = \Yii::$app->params['site_url'];
				$fieldData     = [];
				for ($i = 1; $i <= $num; $i++) {
					$j = $i - 1;
					$info = array_slice($workExternalUserData, $j * $pageSize, $pageSize);
					if (!empty($info)) {
						foreach ($info as $key => $val) {
							$perName = WorkPerTagFollowUser::getTagName($val['fid'], 0, $val['user_id']);
							$tagName = WorkTagContact::getTagNameByContactId($val['fid'], 0, 0, $val['user_id'],$this->corpId);
							//跟进状态
							$follow_status = '';
							if (!empty($val['follow_id'])) {
								$follow = Follow::findOne($val['follow_id']);
								if (!empty($follow)) {
									$follow_status = $follow->title;
									if ($follow->status == 0) {
										$follow_status .= '（已删除）';
									}
								}
							}
							$addWayInfo = WorkExternalContactFollowUser::getAddWay($val['add_way']);
							$title                      = '';
							$wayInfo                    = '';
							if ($val['way_id'] > 0) {
								$wayInfo    = '渠道活码';
								$contactWay = WorkContactWay::findOne($val['way_id']);
								if (!empty($contactWay)) {
									$title = $contactWay->title;
								}
							} elseif ($val['chat_way_id'] > 0) {
								$wayInfo = '自动拉群';
								$way     = WorkChatContactWay::findOne($val['chat_way_id']);
								if (!empty($way)) {
									$title = $way->title;
								}
							} elseif ($val['fission_id'] > 0) {
								$wayInfo = '裂变引流';
								$fission = Fission::findOne($val['fission_id']);
								if (!empty($fission)) {
									$title = $fission->title;
								}
							} elseif ($val['award_id'] > 0) {
								$wayInfo = '抽奖引流';
								$award   = AwardsActivity::findOne($val['award_id']);
								if (!empty($award)) {
									$title = $award->title;
								}
							} elseif ($val['red_pack_id'] > 0) {
								$wayInfo = '红包裂变';
								$red     = RedPack::findOne($val['red_pack_id']);
								if (!empty($red)) {
									$title = $red->title;
								}
							} elseif ($val['activity_id'] > 0) {
								$wayInfo = '裂变引流';
								$red     = WorkPublicActivity::findOne($val['activity_id']);
								if (!empty($red)) {
									$title = $red->activity_name;
								}
							} elseif ($val['way_redpack_id'] > 0) {
								$wayInfo   = '红包拉新';
								$redpacket = WorkContactWayRedpacket::findOne($val['way_redpack_id']);
								if (!empty($redpacket)) {
									$title = $redpacket->name;
								}
							}

							$source     = $addWayInfo;
							if (!empty($wayInfo)) {
								$source .= '-' . $wayInfo;
							}
							if (!empty($title)) {
								$source .= '-' . $title;
							}
							$follow     = $follow_status . '（跟进' . $val['follow_num'] . '次）';
							$work_user  = WorkUser::findOne($val['user_id']);
							$departName = WorkDepartment::getDepartNameByUserId($work_user->department, $work_user->corp_id);
							$member     = $work_user->name . '--' . $departName;
							$remark     = !empty($val['nickname']) ? "（备注：" . $val['nickname'] . "）" : ((!empty($val['remark']) && $val['remark'] != $val['name_convert']) ? "（备注：" . $val['remark'] . "）" : "");
							$members    = $member . $remark;
							$tags       = '--';
							if (empty($perName) && !empty($tagName)) {
								$tags = implode(',', $tagName);
							}
							if (!empty($perName) && empty($tagName)) {
								$tags = '私有标签：' . implode(',', $perName);
							}
							if (!empty($perName) && !empty($tagName)) {
								$tags = '公有标签：' . implode(',', $perName);
								$tags .= '私有标签：' . implode(',', $tagName);
							}

							//高级属性
							$fieldValue  = CustomFieldValue::find()->where(['type' => 1, 'cid' => $val['wid']])->andWhere(['in', 'fieldid', $contactField])->asArray()->all();
							$fieldValueD = [];
							foreach ($fieldValue as $field) {
								$fieldValueD[$field['fieldid']] = $field['value'];
							}
							$nickname = isset($fieldValueD[$fieldD['name']]) ? $fieldValueD[$fieldD['name']] : '--';
							$phone    = isset($val['remark_mobiles']) && !empty($val['remark_mobiles']) ? $val['remark_mobiles'] : '--';
							$area     = isset($fieldValueD[$fieldD['area']]) ? $fieldValueD[$fieldD['area']] : '--';
							if (empty($nickname) && empty($phone) && empty($area)) {
								$sPhone = '--';
							} else {
								if ($is_hide_phone){
									$sPhone = $nickname . '/' . $area;
								}else{
									$sPhone = $nickname . '/' . $phone . '/' . $area;
								}
							}
							$fans   = Fans::findOne(['external_userid' => $val['wid'], 'subscribe' => Fans::USER_SUBSCRIBE]);
							$wxName = '';
							if (!empty($fans)) {
								$wxName = $fans->author->wxAuthorizeInfo->nick_name;
							}

							//客户画像
							$fieldData = CustomField::getCustomField($this->uid, $val['wid'], 1);

							//获取所在群名称
							$chatName                  = WorkChatInfo::getChatList(2, $val['wid']);
							$cName                     = !empty($val['name']) ? rawurldecode($val['name']) : '';
							$exportData[$p]['exp_name']    = $cName . '/所在群' . count($chatName) . '个' . '/' . $wxName;
							$exportData[$p]['exp_phone']   = $sPhone;
							$exportData[$p]['exp_source']  = $source;
							$exportData[$p]['exp_tags']    = $tags;
							$exportData[$p]['exp_members'] = $members;
							$exportData[$p]['exp_follow']  = $follow;
							$exportData[$p]['exp_time']    = !empty($val['createtime']) ? date('Y-m-d H:i:s', $val['createtime']) : '';

							foreach ($fieldData as $fkey=>$fval){
								if (!in_array($fval['key'], ['name', 'phone', 'area'])){
									if ($fval['type'] == 8){
										$fieldValD = [];
										foreach ($fval['value'] as $ffkey=>$ffval){
											if (!empty($ffval['url'])){
												array_push($fieldValD, $site_url . $ffval['url']);
											}
										}
										$fieldVal = !empty($fieldValD) ? implode(',', $fieldValD) : '';
									}else{
										$fieldVal = !empty($fval['value']) ? "\t" . $fval['value'] : '--';
									}
									$exportData[$p][$fval['key']] = $fieldVal;
								}
							}

							$p++;
						}
					}
					$currentCount += count($info);
					WorkExternalContactFollowUser::exportNumWebsocket($this->uid, $this->corpId, $this->count, $currentCount);
				}
				if ($currentCount == $this->count) {
					$save_dir = \Yii::getAlias('@upload') . '/exportfile/' . date('Ymd') . '/';
					//创建保存目录
					if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
						return ['error' => 1, 'msg' => '无法创建目录'];
					}
					$columns = ['exp_name', 'exp_phone', 'exp_source', 'exp_tags', 'exp_members', 'exp_follow', 'exp_time'];
					if ($is_hide_phone){
						$exp_phone = '姓名/区域';
					}else{
						$exp_phone = '姓名/手机号/区域';
					}
					$headers = [
						'exp_name'    => '客户信息',
						'exp_phone'   => $exp_phone,
						'exp_source'  => '来源',
						'exp_tags'    => '标签',
						'exp_members' => '归属成员',
						'exp_follow'  => '跟进状态',
						'exp_time'    => '加入时间',
					];

					foreach ($fieldData as $fkey=>$fval){
						if (!in_array($fval['key'], ['name', 'phone', 'area'])){
							array_push($columns, $fval['key']);
							$headers[$fval['key']] = $fval['title'];
						}
					}

					$headerInfo = $headers;
					$columnInfo = $columns;
					$fileName   = '客户数据_' . date("YmdHis", time());
					$pageSize   = 5000;
					if (count($exportData) <= $pageSize) {
						Excel::export([
							'models'       => $exportData,//数库
							'fileName'     => $fileName,//文件名
							'savePath'     => $save_dir,//下载保存的路径
							'asAttachment' => true,//是否下载
							'columns'      => $columns,//要导出的字段
							'headers'      => $headers
						]);
					} else {
						$num     = ceil(count($exportData) / $pageSize);
						$models  = [];
						$columns = [];
						$headers = [];
						for ($i = 1; $i <= $num; $i++) {
							$j                     = $i - 1;
							$info                  = array_slice($exportData, $j * $pageSize, $pageSize);
							$models['sheet' . $i]  = $info;
							$columns['sheet' . $i] = $headerInfo;
							$headers['sheet' . $i] = $columnInfo;
						}
						Excel::export([
							'isMultipleSheet' => true,
							'models'          => $models,
							'fileName'        => $fileName,//文件名
							'savePath'        => $save_dir,//下载保存的路径
							'asAttachment'    => true,//是否下载
							'columns'         => $headers,
							'headers'         => $columns
						]);
					}
					$site_url = \Yii::$app->params['site_url'];
					$url = $site_url . str_replace(\Yii::getAlias('@upload'), '/upload', $save_dir) . $fileName . '.xlsx';
					WorkExternalContactFollowUser::exportNumWebsocket($this->uid, $this->corpId, $this->count, $currentCount, $url);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'ExportCustomJob');
			}


		}

	}