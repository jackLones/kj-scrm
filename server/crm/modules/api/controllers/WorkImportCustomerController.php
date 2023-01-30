<?php

	namespace app\modules\api\controllers;

	use app\components\InvalidDataException;
    use app\models\AuthoritySubUserDetail;
    use app\models\WorkDepartment;
    use app\models\WorkImportCustomer;
    use app\models\WorkImportCustomerDetail;
    use app\models\WorkImportCustomerMsgSend;
    use app\models\WorkTag;
    use app\models\WorkUser;
    use app\modules\api\components\WorkBaseController;
    use app\queue\SyncWorkImportCustomerJob;
    use app\queue\WorkImportCustomerSendingJob;
    use app\util\SUtils;
    use moonland\phpexcel\Excel;
    use yii\base\DynamicModel;
    use yii\db\Expression;
    use yii\web\MethodNotAllowedHttpException;

    class WorkImportCustomerController extends WorkBaseController
	{
		/**
		 * showdoc
		 * @catalog         数据接口/api/work-import-customer/
		 * @title           客户导入列表
		 * @description     客户导入列表
		 * @method   post
		 * @url  http://{host_name}/api/work-import-customer/import-customer-list
		 *
		 * @param corp_id      必选 string 企业的唯一ID
		 * @param title        可选 string 导入名称
		 * @param page         可选 int 页码
		 * @param page_size    可选 int 每页数据量，默认15
		 *
		 * @return          {"error":0,"data":{"list":[]}}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int  数据条数
		 * @return_param    list array 数据列表
		 * @return_param    list.import_id int 导入id
		 * @return_param    list.userName array 分配员工
		 * @return_param    list.title string 导入名称
		 * @return_param    list.add_time string 创建时间
		 * @return_param    list.snum int 导入人数
		 * @return_param    list.addNum int 已添加人数
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-07-28
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionImportCustomerList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$title    = \Yii::$app->request->post('title', '');
			$page     = \Yii::$app->request->post('page', 1);
			$pageSize = \Yii::$app->request->post('page_size', 15);
            $tag_ids  = \Yii::$app->request->post('tag_ids', '');
            $tag_type = \Yii::$app->request->post('tag_type', 1);

			$title    = trim($title);

			if (empty($this->corp)) {
				throw new InvalidDataException('参数不正确！');
			}

			$importList = WorkImportCustomer::find()->andWhere(['corp_id' => $this->corp['id'], 'is_del' => 0]);

			//规则名称查询
			if (!empty($title)) {
				$importList = $importList->andWhere(['like', 'title', $title]);
			}
            //标签筛选
            $tagIds = $tag_ids ? (is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids)) : [];
            if (!empty($tagIds) && in_array($tag_type, [1, 2, 3])) {
                $userTag = WorkImportCustomer::find()
                    ->alias('wic')
                    ->innerJoin('{{%work_tag}} wtg', 'find_in_set(wtg.id,wic.tag_ids) != 0 AND wtg.`is_del` = 0')
                    ->where(['wic.corp_id' => $this->corp['id'],'wtg.corp_id' => $this->corp['id'],'wic.is_del' => 0])
                    ->groupBy('wic.id')
                    ->select('wic.id,GROUP_CONCAT(wtg.id) tag_ids');

                $importList = $importList->leftJoin(['wt' => $userTag], "`wt`.`id` = {{%work_import_customer}}.`id`");
                $tagsFilter = [];
                if ($tag_type == 1) {//标签或
                    $tagsFilter[] = 'OR';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                    });
                }elseif ($tag_type == 2) {//标签且
                    $tagsFilter[] = 'AND';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                    });
                }elseif ($tag_type == 3) {//标签不包含
                    $tagsFilter[] = 'AND';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['is not','wt.tag_ids',NULL] : (new Expression("NOT FIND_IN_SET($value,IFNULL(wt.tag_ids,''))"));
                    });
                }
                $importList->andWhere($tagsFilter);
            }

			$count = $importList->count();
			$offset     = ($page - 1) * $pageSize;
			$importList = $importList->limit($pageSize)->offset($offset);
			$importList = $importList->orderBy(['id' => SORT_DESC])->asArray()->all();
			//子账户范围限定
//			$user = [];
//			if (isset($this->subUser->sub_id)) {
//				$sub_id = isset($this->subUser->sub_id) ? $this->subUser->sub_id : 0;
//				$user = WorkDepartment::GiveDepartmentReturnUserData($this->corp->id, [], [], 0, true,0, [],$sub_id,0,true);
//				$user = empty($user) ? [0] : $user;
//			}
			$result = [];
			foreach ($importList as $k=>$v) {
				$importD              = [];
				$importD['import_id'] = $v['id'];
				$importD['title']     = $v['title'];
				$importD['add_time']  = $v['add_time'] ? date('Y-m-d H:i', $v['add_time']) : '--';
				$importD['all_num']   = $v['all_num'];
				$importD['snum']      = $v['snum'];
				$importD['fail_num']  = $v['all_num'] - $v['snum'];
//				$ids = json_decode($v["user_ids"],1);
//				if(!empty($ids)){
//					$Temp = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($ids);
//					if(!array_intersect($Temp["user"],$user) && isset($this->subUser->sub_id)){
//						unset($importList[$k]);
//						$count = count($importList);
//						continue;
//					}
//				}
				$userNameArr = [];
				$userAllIds = json_decode($v['distribution_records'], true);
				if(empty($userAllIds)){//兼容老数据
                    $userAllIds = [];
                    $userIds = json_decode($v['user_ids'], true);
                    array_push($userAllIds,[
                        'distribution_time' => $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : date('Y-m-d H:i:s'),
                        'user_ids'          => $userIds
                    ]);

                    $import = WorkImportCustomer::findOne($v['id']);
                    $import->distribution_records = json_encode($userAllIds);
                    $import->save();
                }

                foreach ($userAllIds as $vv) {
                    $userIds = $vv['user_ids'];

                    $userNames = [];
                    foreach ($userIds as $user_id) {
                        if (strpos($user_id, 'd') !== false) {
                            $T = explode("-", $user_id);
                            if (isset($T[1])) {
                                $department = WorkDepartment::findOne(["corp_id"=>$this->corp->id,"department_id"=>$T[1]]);
                                array_push($userNames, ["name"=>$department->name,"title"=>"part"]);
                            }
                        }else{
                            $workUser = WorkUser::findOne($user_id);
                            array_push($userNames, ["name"=>$workUser->name ?? '未知',"title"=>"name"]);
                        }
                    }
                    array_push($userNameArr, [
                        'distribution_time' => $vv['distribution_time'],
                        'user_name' => $userNames
                    ]);
                }

				$importD['distribution_records'] = array_reverse($userNameArr);//前端页面反向显示

				//已添加人数
				$addNum            = WorkImportCustomerDetail::find()->andWhere(['import_id' => $v['id'], 'is_add' => 1])->count();
				$importD['addNum'] = $addNum;
                $tagNames = !empty($v['tag_ids']) ? WorkTag::find()->where(['in','id',explode(",",$v['tag_ids'])])->andWhere(['is_del' => 0])->select('tagname as tag_name')->asArray()->all() : '';
                $importD['tag_names'] = $tagNames ? array_column($tagNames,'tag_name') : '';

                $result[] = $importD;
			}

			return [
				'count' => $count,
				'list'  => $result
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-import-customer/
		 * @title           导入数据删除
		 * @description     导入数据删除
		 * @method   post
		 * @url  http://{host_name}/api/work-import-customer/import-customer-delete
		 *
		 * @param import_id   必选 int 导入ID
		 *
		 * @return          {"error":0,"data":{}}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-07-28
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionImportCustomerDelete ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$import_id = \Yii::$app->request->post('import_id', 0);

			if (empty($import_id)) {
				throw new InvalidDataException('缺少必要参数！');
			}

			$import = WorkImportCustomer::findOne($import_id);

			if (empty($import)) {
				throw new InvalidDataException('导入参数错误！');
			} else {
				$import->is_del   = 1;
				$import->upt_time = time();

				if (!$import->validate() || !$import->save()) {
					throw new InvalidDataException(SUtils::modelError($import));
				}
			}

			return true;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-import-customer/
		 * @title           客户导入详情列表
		 * @description     客户导入详情列表
		 * @method   post
		 * @url  http://{host_name}/api/work-import-customer/import-customer-detail-list
		 *
		 * @param corp_id        必选 string 企业的唯一ID
		 * @param import_id      可选 int 导入ID
		 * @param status         可选 int 状态1未添加2已添加
		 * @param phone          可选 int 手机号
		 * @param page           可选 int 页码
		 * @param page_size      可选 int 每页数据量，默认15
		 *
		 * @return          {"error":0,"data":{"list":[]}}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int  数据条数
		 * @return_param    list array 数据列表
		 * @return_param    list.detail_id int 详情id
		 * @return_param    list.phone string 手机号
		 * @return_param    list.nickname string 微信昵称
		 * @return_param    list.name string 姓名
		 * @return_param    list.sex string 性别
		 * @return_param    list.area string 区域
		 * @return_param    list.des string 备注
		 * @return_param    list.is_add int 添加状态1已添加0未添加
		 * @return_param    list.userName string 员工名称
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-07-28
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionImportCustomerDetailList ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$import_id = \Yii::$app->request->post('import_id', '');
			$status    = \Yii::$app->request->post('status', 0);
			$phone     = \Yii::$app->request->post('phone');
			$page      = \Yii::$app->request->post('page', 1);
			$pageSize  = \Yii::$app->request->post('page_size', 15);
            $tag_ids   = \Yii::$app->request->post('tag_ids', '');
            $tag_type  = \Yii::$app->request->post('tag_type', 1);

			$phone     = trim($phone);

			if (empty($this->corp)) {
				throw new InvalidDataException('参数不正确！');
			}

            if($page == 1 && $this->corp['important_customer_recycle_switch']){//超时的未添加客户重新归为待分配
                $customerUser = WorkImportCustomerDetail::find()
                    ->where(['corp_id' => $this->corp['id'], 'is_add' => 0, 'external_follow_id' => 0])
                    ->andWhere(['<','add_time',time()-$this->corp['important_customer_recycle_time']*24*3600])
                    ->andWhere(['<','time',time()-$this->corp['important_customer_recycle_time']*24*3600])
                    ->limit(1)
                    ->one();

                $customerUser && WorkImportCustomerDetail::updateAll(['is_add' => 2],[
                    'AND',
                    ['corp_id' => $this->corp['id'],'is_add' => 0,'external_follow_id' => 0],
                    ['<','add_time',time()-$this->corp['important_customer_recycle_time']*24*3600],
                    ['<','time',time()-$this->corp['important_customer_recycle_time']*24*3600],
                ]);
            }
			$importDetailList = WorkImportCustomerDetail::find()
                ->alias("wc")
                ->andWhere(['wc.corp_id' => $this->corp['id']]);
			if (isset($this->subUser->sub_id)) {
				$sub_detail = AuthoritySubUserDetail::getDepartmentUserLists($this->subUser->sub_id, $this->corp->id);
				if($sub_detail === false){
					return ["count"=>0,"list"=>[]];
				}
				if(is_array($sub_detail)){
					$importDetailList = $importDetailList->andWhere(["in","wc.user_id",$sub_detail]);
				}
			}
            $userTag = WorkImportCustomer::find()
                ->alias('wic')
                ->innerJoin('{{%work_tag}} wtg', 'find_in_set(wtg.id,wic.tag_ids) != 0 AND wtg.`is_del` = 0')
                ->where(['wic.corp_id' => $this->corp['id'],'wtg.corp_id' => $this->corp['id'],'wic.is_del' => 0])
                ->groupBy('wic.id')
                ->select('wic.id,GROUP_CONCAT(wtg.id) tag_ids');
            $importDetailList = $importDetailList->leftJoin(['wt' => $userTag], "`wt`.`id` = wc.`import_id`");
            //标签筛选
            $tagIds = $tag_ids ? (is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids)) : [];
            if (!empty($tagIds) && in_array($tag_type, [1, 2, 3])) {
                $tagsFilter = [];
                if ($tag_type == 1) {//标签或
                    $tagsFilter[] = 'OR';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                    });
                }elseif ($tag_type == 2) {//标签且
                    $tagsFilter[] = 'AND';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                    });
                }elseif ($tag_type == 3) {//标签不包含
                    $tagsFilter[] = 'AND';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['is not','wt.tag_ids',NULL] : (new Expression("NOT FIND_IN_SET($value,IFNULL(wt.tag_ids,''))"));
                    });
                }
                $importDetailList->andWhere($tagsFilter);
            }

			$importTitle = '';
			if ($import_id) {
				$importDetailList = $importDetailList->andWhere(['wc.import_id' => $import_id]);
				$import           = WorkImportCustomer::findOne($import_id);
				$importTitle      = isset($import->title) ? $import->title : '';
			}
			if ($status && in_array($status, [1, 2, 3])) {
				$status           -= 1;
				$importDetailList = $importDetailList->andWhere(['wc.is_add' => $status]);
			}
			if ($phone !== '') {
				$importDetailList = $importDetailList->andWhere(['like', 'wc.phone', $phone]);
			}
			//返回所有未添加客户的客户id
            $importAllIdsList = clone $importDetailList;
            $customerIds = $importAllIdsList->andWhere(['=','wc.external_follow_id', 0])->select('wc.id')->asArray()->all();

			$count = $importDetailList->count();

			$offset           = ($page - 1) * $pageSize;
			$importDetailList = $importDetailList->limit($pageSize)->offset($offset);
			$importDetailList = $importDetailList->orderBy(['wc.id' => SORT_DESC])->select('wc.*,wt.tag_ids')->asArray()->all();

			$result = [];
			foreach ($importDetailList as $v) {
				$importD              = [];
				$importD['detail_id'] = $v['id'];
				$importD['phone']     = $v['phone'];
				$importD['nickname']  = $v['nickname'];
				$importD['name']      = $v['name'];
				$sex                  = '未知';
				if ($v['sex'] == 1) {
					$sex = '男';
				} elseif ($v['sex'] == 2) {
					$sex = '女';
				}
				$importD['sex']      = $sex;
				$importD['area']     = $v['area'];
				$importD['des']      = $v['des'];
				$importD['is_add']   = $v['is_add'];
				$workUser            = WorkUser::findOne($v['user_id']);
				$importD['userName'] = $workUser->name;
				if(empty($v['distribution_records'])){
                    $customerDetail = WorkImportCustomerDetail::findOne($v['id']);
                    $customerDetail->distribution_records = json_encode([['add_time'=>date('Y-m-d H:i:s',$v['time']),'user_id'=>$v['user_id']]]);
                    $customerDetail->save();//兼容之前老数据
                }
				$distributionRecords = empty($v['distribution_records']) ? [['add_time'=>date('Y-m-d H:i:s',$v['time']),'user_id'=>$v['user_id']]] : json_decode($v['distribution_records'],true);
				$userIds = array_column($distributionRecords,'user_id');
                $workUser = WorkUser::find()->where(['in','id',$userIds])->select('id,name')->all();
				$workUser = array_column($workUser,'name','id');
				array_walk($distributionRecords,function (&$record)use($workUser){
                    $record['user_name'] = $workUser[$record['user_id']] ?? "";
                    unset($record['user_id']);
                });
                $importD['distribution_records'] = array_reverse($distributionRecords);
                $importD['tag_names'] = !empty($v['tag_ids']) ? WorkTag::find()->where(['in','id',explode(",",$v['tag_ids'])])->andWhere(['is_del' => 0])->select('tagname as tag_name')->asArray()->all() : '';
                !empty($importD['tag_names'])  && $importD['tag_names'] = array_column($importD['tag_names'],'tag_name');
				$result[] = $importD;
			}

			return [
				'count'       => $count,
				'importTitle' => $importTitle,
				'list'        => $result,
                'all_customer_ids'  => array_column($customerIds,'id')
			];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-import-customer/
		 * @title           客户导入提交
		 * @description     客户导入提交
		 * @method   post
		 * @url  http://{host_name}/api/work-import-customer/import-customer
		 *
		 * @param corp_id        必选 string 企业的唯一ID
		 * @param sub_id         必选 int 子账户ID
		 * @param agentid        必选 string 应用ID
		 * @param user_ids       必选 string 成员id,逗号隔开
		 *
		 * @return          {"error":0,"data":{"insertNum":1,"skipNum":1}}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    insertNum int 成功个数
		 * @return_param    skipNum int 失败个数
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-07-28
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionImportCustomer ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$sub_id   = \Yii::$app->request->post('sub_id', '');
			$agentid  = \Yii::$app->request->post('agentid', '');
			$user_ids = \Yii::$app->request->post('user_ids', '');
			$tag_ids  = \Yii::$app->request->post('tag_ids', '');

			if (empty($this->corp) || empty($agentid) || empty($user_ids)) {
				throw new InvalidDataException('参数不正确！');
			}

			if (!empty($_FILES['importFile']['name'])) {
				$fileTypes = explode(".", $_FILES['importFile']['name']);
				$fileType  = $fileTypes[count($fileTypes) - 1];
				/*判别是不是.xls .xlsx文件，判别是不是excel文件*/
				if (strtolower($fileType) != "xls" && strtolower($fileType) != "xlsx") {
					throw new InvalidDataException('文件类型不对！');
				}
				$fileTmpPath = $_FILES['importFile']['tmp_name'];
				$excelData   = Excel::import($fileTmpPath, [
					'setFirstRecordAsKeys' => false
				]);
				$importData = $excelData[0];

				if (!empty($importData[1])) {
					$header = $importData[1];
					if ($header['A'] != '手机号' || $header['B'] != '微信昵称' || $header['C'] != '姓名' || $header['D'] != '性别' || $header['E'] != '地区' || $header['F'] != '备注') {
						throw new InvalidDataException('导入数据格式不对，请检查标题是否与模版一致！');
					}
				} else {
					throw new InvalidDataException('导入数据格式不对，请检查标题是否与模版一致！');
				}
				$count = count($importData);
				if ($count < 2) {
					throw new InvalidDataException('请在文件内添加要导入的数据！');
				} else if ($count > 30001) {
					throw new InvalidDataException('单次导入客户最多3万条，请重新上传！');
				}

				try {
					$import               = [];
					$import['corp_id']    = $this->corp['id'];
					$import['corpid']     = $this->corp['corpid'];
					$import['agentid']    = $agentid;
					$import['sub_id']     = $sub_id;
					$import['title']      = $_FILES['importFile']['name'];
					$import['user_ids']   = $user_ids;
					$import['importData'] = $importData;
                    $import['tag_ids']    = $tag_ids;

					$jobId = \Yii::$app->work->push(new SyncWorkImportCustomerJob([
						'import' => $import,
					]));

					/*$res = WorkImportCustomer::create($import);

					$textHtml = '本次';
					if (isset($res['insertNum'])) {
						$textHtml .= '导入成功' . $res['insertNum'] . '条，';
					}
					if (!empty($res['skipNum'])) {
						$textHtml .= '忽略' . $res['skipNum'] . '条（已有的），';
					}
					if (!empty($res['skipPhoneNum'])) {
						$textHtml .= $res['skipPhoneNum'] . '条手机号格式不正确，';
					}
					$textHtml = trim($textHtml, '，');

					return ['textHtml' => $textHtml];*/

					return ['error' => 0];
				} catch (InvalidDataException $e) {
					throw new InvalidDataException($e->getMessage());
				}
			} else {
				throw new InvalidDataException('请上传文件！');
			}
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/work-import-customer/
		 * @title           员工分配客户列表
		 * @description     员工分配客户列表
		 * @method   post
		 * @url  http://{host_name}/api/work-import-customer/import-user-customer
		 *
		 * @param corp_id        必选 string 企业的唯一ID
		 * @param userid         可选 int 员工userid
		 * @param status         可选 int 状态1未添加2已添加
		 * @param page           可选 int 页码
		 * @param page_size      可选 int 每页数据量，默认15
		 *
		 * @return          {"error":0,"data":{"list":[]}}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 * @return_param    count int  数据条数
		 * @return_param    list array 数据列表
		 * @return_param    list.detail_id int 详情id
		 * @return_param    list.phone string 手机号
		 * @return_param    list.is_add int 添加状态1已添加0未添加
		 *
		 * @remark          Create by PhpStorm. User: fulu. Date: 2020-07-28
		 * @number          0
		 *
		 * @throws InvalidDataException
		 * @throws MethodNotAllowedHttpException
		 */
		public function actionImportUserCustomer ()
		{
			if (\Yii::$app->request->isGet) {
				throw new MethodNotAllowedHttpException('请求方式不允许！');
			}
			$userid   = \Yii::$app->request->post('userid', '');
			$status   = \Yii::$app->request->post('status', 0);
			$page     = \Yii::$app->request->post('page', 1);
			$pageSize = \Yii::$app->request->post('page_size', 15);

			if (empty($this->corp) || empty($userid)) {
				throw new InvalidDataException('参数不正确！');
			}
			$workUser = WorkUser::findOne(['corp_id' => $this->corp['id'], 'userid' => $userid]);
			if (empty($workUser)){
				throw new InvalidDataException('员工数据不正确！');
			}

			$importDetailList = WorkImportCustomerDetail::find()->andWhere(['corp_id' => $this->corp['id'], 'user_id' => $workUser->id]);

			if ($status && in_array($status, [1, 2, 3])) {
				$status           -= 1;
				$importDetailList = $importDetailList->andWhere(['is_add' => $status]);
			}

			$count = $importDetailList->count();

			$offset           = ($page - 1) * $pageSize;
			$importDetailList = $importDetailList->limit($pageSize)->offset($offset);
			$importDetailList = $importDetailList->orderBy(['id' => SORT_DESC])->asArray()->all();

			$result = [];
			foreach ($importDetailList as $v) {
				$importD              = [];
				$importD['detail_id'] = $v['id'];
				$importD['phone']     = $v['phone'];
				$importD['is_add']    = $v['is_add'];
				$importD['remark']    = $v['des'];

				$result[] = $importD;
			}

			return [
				'count' => $count,
				'list'  => $result
			];
		}

        /**
         * 导入的客户二次分配
         * @return int[]
         * @throws InvalidDataException
         */
        public function actionSecondaryDistributionCustomers()
        {
            $userIds      =  \Yii::$app->request->post('user_ids');//企业成员
            $importId     =  \Yii::$app->request->post('import_id');//Excel导入ID
            $customerIds  =  \Yii::$app->request->post('customer_ids');//Excel客户的导入ID
            $isBatch      =  \Yii::$app->request->post('is_batch');//批量全部分配

            $phone     = \Yii::$app->request->post('phone');
            $tag_ids   = \Yii::$app->request->post('tag_ids', '');
            $tag_type  = \Yii::$app->request->post('tag_type', 1);

            if (empty($this->corp) || (empty($importId) && empty($customerIds) && empty($isBatch)) || (empty($userIds) || !is_array($userIds))) {
                throw new InvalidDataException('参数不正确！');
            }

            $importDetailList = WorkImportCustomerDetail::find()
                ->alias("wc")
                ->andWhere(['wc.corp_id' => $this->corp['id']]);
            if (isset($this->subUser->sub_id)) {
                $sub_detail = AuthoritySubUserDetail::getDepartmentUserLists($this->subUser->sub_id, $this->corp->id);
                $sub_detail === false && SUtils::throwException(InvalidDataException::class,'无权限操作！');
                if (is_array($sub_detail)) {
                    $importDetailList = $importDetailList->andWhere(["in", "wc.user_id", $sub_detail]);
                }
            }
            $userTag = WorkImportCustomer::find()
                ->alias('wic')
                ->innerJoin('{{%work_tag}} wtg', 'find_in_set(wtg.id,wic.tag_ids) != 0 AND wtg.`is_del` = 0')
                ->where(['wic.corp_id' => $this->corp['id'],'wtg.corp_id' => $this->corp['id'],'wic.is_del' => 0])
                ->groupBy('wic.id')
                ->select('wic.id,GROUP_CONCAT(wtg.id) tag_ids');
            $importDetailList = $importDetailList->leftJoin(['wt' => $userTag], "`wt`.`id` = wc.`import_id`");

            if($isBatch) {
                //标签筛选
                $tagIds = $tag_ids ? (is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids)) : [];
                if (!empty($tagIds) && in_array($tag_type, [1, 2, 3])) {
                    $tagsFilter = [];
                    if ($tag_type == 1) {//标签或
                        $tagsFilter[] = 'OR';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                        });
                    }elseif ($tag_type == 2) {//标签且
                        $tagsFilter[] = 'AND';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                        });
                    }elseif ($tag_type == 3) {//标签不包含
                        $tagsFilter[] = 'AND';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['is not','wt.tag_ids',NULL] : (new Expression("NOT FIND_IN_SET($value,IFNULL(wt.tag_ids,''))"));
                        });
                    }
                    $importDetailList->andWhere($tagsFilter);
                }

                !empty($importId) && $importDetailList = $importDetailList->andWhere(['wc.import_id' => $importId]);
                !empty($phone) && $importDetailList = $importDetailList->andWhere(['like', 'wc.phone', $phone]);
            }else{
                !empty($customerIds) && !is_array($customerIds) && $customerIds = explode(',', $customerIds);
                $importDetailList = $importDetailList->andFilterWhere(['wc.import_id' => $importId, 'wc.id' => $customerIds]);
            }
            //返回所有未添加客户的客户id
            $customerIds = $importDetailList
                ->andWhere(['=', 'wc.external_follow_id', 0])
                ->andWhere(['wc.is_add' => [0,2]])
                ->select('wc.id,wc.import_id')
                ->asArray()
                ->all();
            empty($customerIds) && SUtils::throwException(InvalidDataException::class,'当前无可二次分配客户！');

            try{
                return \Yii::$app->db->transaction(function($db)use($importId,$customerIds,$userIds) {
                    if($importId){
                        $workCustomer = WorkImportCustomer::findOne(['id'=>$importId]);
                        $staffUserIds = json_decode($workCustomer->distribution_records,true);
                        array_push($staffUserIds,['distribution_time' => date('Y-m-d H:i:s'),'user_ids' => $userIds]);
                        $workCustomer->user_ids = json_encode($userIds);
                        $workCustomer->distribution_records = json_encode($staffUserIds);
                        $workCustomer->save();//记录分配记录
                    }
                    $userNum  = $successNum = $exist = 0;
                    $allotNum = [];
                    $userCount  = count($userIds);
                    foreach ($customerIds as $v) {
                        //分配员工
                        $detailData = [
                            'corp_id'       =>  $this->corp->id,
                            'user_id'       =>  $userIds[$userNum],
                            'customer_id'   =>  $v['id']
                        ];
                        $res = WorkImportCustomerDetail::distributionCustomers($detailData);
                        if($res === true){
                            $successNum++;
                            if (!isset($allotNum[$userIds[$userNum]])) {
                                $allotNum[$userIds[$userNum]] = 1;
                            } else {
                                $allotNum[$userIds[$userNum]] += 1;
                            }
                        }else if('exist' === $res){
                            $exist++;
                        }
                        $userNum++;
                        if ($userNum >= $userCount) {
                            $userNum = 0;
                        }
                        empty($importId) && $importId = $v['import_id'];
                    }
                    //发提醒消息
                    foreach ($userIds as $user_id) {
                        if (isset($allotNum[$user_id]) && $allotNum[$user_id] > 0) {
                            $msgSend            = new WorkImportCustomerMsgSend();
                            $msgSend->corp_id   =  $this->corp->id;
                            $msgSend->user_id   = $user_id;
                            $msgSend->import_id = $importId;
                            $msgSend->add_num   = $allotNum[$user_id];
                            $msgSend->status    = 0;

                            if ($msgSend->save()) {
                                \Yii::$app->work->push(new WorkImportCustomerSendingJob([
                                    'work_import_customer_send_id' => $msgSend->id
                                ]));
                            }
                        }
                    }
                    return [
                        'success_num' => $successNum,
                        'exist_num'   => $exist
                    ];
                });
            }catch (\Exception $e){
                \Yii::error($e->getMessage(), 'SecondaryDistributionCustomers');
                throw new InvalidDataException($e->getMessage());
            }
        }

        /**
         * 客户导入二次分配超时回收信息
         * @return array
         * @throws \Exception
         */
        public function actionImportantCustomerRecycleInfo()
        {
            empty($this->corp) && SUtils::throwException(InvalidDataException::class, '参数不正确！');
            return [
                'switch' => $this->corp->important_customer_recycle_switch,
                'time'   => $this->corp->important_customer_recycle_time
            ];
        }

        /**
         * 客户导入二次分配超时回收信息设置
         * @return bool
         * @throws \Exception
         */
        public function actionImportantCustomerRecycleSetting()
        {
            $param   = \Yii::$app->request->post();
            $model = DynamicModel::validateData($param, [
                [['switch'], 'in', 'range' => [0, 1]],
                ['time','integer','min' => 1],
            ]);
            ($model->hasErrors() || empty($this->corp)) && SUtils::throwException(InvalidDataException::class, '参数不正确！');

            $this->corp->important_customer_recycle_switch = $param['switch'];
            $this->corp->important_customer_recycle_time = $param['time'];

            return $this->corp->save();
        }
	}