<?php

	namespace app\modules\api\controllers;


    use app\components\InvalidDataException;
    use app\components\InvalidParameterException;
    use app\models\AuthoritySubUserDetail;
    use app\models\CustomField;
    use app\models\DialoutAgent;
    use app\models\DialoutBindWorkUser;
    use app\models\DialoutConfig;
    use app\models\DialoutKey;
    use app\models\DialoutOrder;
    use app\models\DialoutRecord;
    use app\models\PublicSeaContactFollowUser;
    use app\models\PublicSeaCustomer;
    use app\models\WorkDepartment;
    use app\models\WorkExternalContact;
    use app\models\WorkExternalContactFollowUser;
    use app\models\WorkUser;
    use app\modules\api\components\WorkBaseController;
    use app\queue\DialoutExportJob;
    use app\util\DateUtil;
    use app\util\SUtils;
    use yii\db\Query;
    use yii\web\MethodNotAllowedHttpException;


    class DialoutController extends WorkBaseController
	{
	    //坐席数量和费用小结
        public function actionExtenSummary()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $cur_time = date("Y-m-d H:i:s");
                $corpId     = $this->corp['id'];
                $exten_total = DialoutAgent::find()->where(['corp_id'=>$corpId,'enable'=>1])->count();
                $exten_used = DialoutAgent::find()
                    ->where(['corp_id'=>$corpId,'enable'=>1])
                    ->andWhere(['>=', 'expire',$cur_time])
                    ->andWhere(['is not','last_use_user',null])->count();
                $exten_usable = DialoutAgent::find()
                    ->where(['corp_id'=>$corpId,'enable'=>1])
                    ->andWhere(['>=', 'expire',$cur_time])
                    ->andWhere(['is','last_use_user',null])->count();
                $exten_expired = DialoutAgent::find()->where(['corp_id'=>$corpId,'enable'=>1])->andWhere(['<', 'expire',$cur_time])->count();

                $balance = DialoutConfig::find()->select(['balance'])->where(['corp_id'=>$corpId])->asArray()->all();
                $balance = $balance[0]['balance'] ?? 0;
                return [
                    'balance'     => $balance,
                    'exten_total'      => $exten_total,
                    'exten_used'      => $exten_used,
                    'exten_usable'      => $exten_usable,
                    'exten_expired'     => $exten_expired,
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //账户明细
        public function actionAccountDetail()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }

                $corpId     = $this->corp['id'];
                $type     = \Yii::$app->request->post('type') ?: null;

                $page        = \Yii::$app->request->post('page', 1);
                $pageSize    = \Yii::$app->request->post('page_size',15);
                $offset      = ($page - 1) * $pageSize;

                $edate     = \Yii::$app->request->post('edate') ?: null;
                $sdate     = \Yii::$app->request->post('sdate') ?: null;

                $edate = $edate ? $edate . ' 23:59:59' : null;

                $balance = DialoutConfig::find()->select(['balance'])->where(['corp_id'=>$corpId])->asArray()->all();
                $balance = $balance[0]['balance'] ?? 0;

                $statis = DialoutOrder::find()
                    ->select([
                        'IFNULL(sum(IF(money>0, money,0)),0) recharge',
                        'IFNULL(sum(IF(money<0, -money,0)),0) consume',
                    ])->where(['corp_id'=>$corpId,'status'=>1])
                    ->asArray()
                    ->all();
                $statis = $statis[0];
                $statis['balance'] = $balance;
                //$statis['consume'] = abs($statis['consume']);

                $query = DialoutOrder::find()
                    ->where(['corp_id'=>$corpId,'status'=>1])
                    ->andFilterWhere(['and',['>=', 'create_time', $sdate], ['<=', 'create_time', $edate],['=','type',$type]]);

                $count = $query->count();

                $typeText = [
                    1=>'花费充值',
                    2=>'话费消耗',
                    3=>'坐席充值',
                    4=>'开通坐席',
                    5=>'续费坐席',
                ];

                $list = $query
                    ->select([
                        'type',
                        'money',
                        'create_time',
                    ])->limit($pageSize)->offset($offset)
                    ->orderBy(['create_time'=>SORT_DESC])
                    ->asArray()
                    ->all();
                foreach ($list as &$value) {
                    $value['type_text'] = $typeText[$value['type']] ?? '';
                    $value['date'] = date("Y-m-d", strtotime($value['create_time']));
                    $value['time'] = date("H:i", strtotime($value['create_time']));
                    $value['create_time'] = $value['date'] . " " . $value['time'];
                }

                $result = [
                    'statis'=>$statis,
                    'count'=>$count,
                    'list'=>$list,
                ];

                return $result;

            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

		//坐席管理列表
        public function actionExtenList()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];
                $status     = \Yii::$app->request->post('status',0);
                $keyword     = \Yii::$app->request->post('keyword');
                $stime     = \Yii::$app->request->post('stime');
                $etime     = \Yii::$app->request->post('etime');
                $page        = \Yii::$app->request->post('page', 1);
                $pageSize    = \Yii::$app->request->post('page_size',15);
                $offset      = ($page - 1) * $pageSize;

                $keyword = $keyword?: null;

                $stime = $stime?: null;
                $etime = $etime?: null;

                //员工部门信息
                $departmentQuery = WorkUser::find()
                    ->alias('a')
                    ->select(['a.id',"group_concat(b.name separator '/') department"])
                    ->leftJoin(WorkDepartment::tableName() . ' b', 'FIND_IN_SET(b.department_id,a.department)')
                    ->where(['a.corp_id'=>$corpId,'b.corp_id'=>$corpId])
                    ->addGroupBy('a.id');

                //坐席绑定员工信息
                $bindInfoQuery = DialoutBindWorkUser::find()
                    ->select(['exten','count(*) num','min(status) status'])
                    ->where(['corp_id'=>$corpId])
                    ->addGroupBy('exten');

                //通话信息
                $callQuery = DialoutRecord::find()
                    ->select([
                        'exten',
                        'sum(money) money',
                        'sum(ceil((end-begin)/60)) duration',
                        'sum((end-begin)) seconds'
                    ])->where(['corp_id'=>$corpId])
                    ->andWhere(['>', 'begin', 0])
                    ->addGroupBy(['exten']);

                //坐席费用
                $extenFeeQuery = DialoutOrder::find()
                    ->select([
                        'exten',
                        'sum(money) money',
                    ])
                    ->where(['corp_id'=>$corpId,'type'=>3,'status'=>1])
                    ->addGroupBy(['exten']);

                $query = DialoutAgent::find()
                    ->alias('a')
                    ->leftJoin(WorkUser::tableName() . ' b', 'a.last_use_user=b.id')
                    ->leftJoin(['c'=>$departmentQuery], 'a.last_use_user=c.id')
                    ->leftJoin(['d'=>$bindInfoQuery], 'a.exten=d.exten')
                    ->leftJoin(['e'=>$callQuery], 'a.exten=e.exten')
                    ->leftJoin(['f'=>$extenFeeQuery], 'a.exten=f.exten')
                    ->where(['a.corp_id'=>$corpId,'a.enable'=>1])
                    ->andFilterWhere(['or',['like', 'b.name', $keyword],['like', 'a.small_phone', $keyword]])
                    ->andFilterWhere(['or',['and',['<=','a.start_time',$stime],['>=', 'a.expire',$stime]], ['and',['<=','a.start_time',$etime],['>=', 'a.expire',$etime]]]);

                $select = [
                    'a.id',
                    'a.exten',
                    'a.small_phone',
                    'a.status',
                    'a.start_time',
                    'a.expire',
                    'IFNULL(b.name, "") user_name',
                    'IFNULL(c.department, "") department',
                    'IFNULL(d.num, 0) use_num',
                    'IFNULL(d.status, 0) use_status',
                    'IFNULL(e.money, 0) phone_money',
                    'IFNULL(e.duration, 0) duration',
                    'IFNULL(e.seconds, 0) seconds',
                    'IFNULL(f.money, 0) exten_money',
                ];
                $dataAll = $query->select($select)->orderBy(['a.create_time'=>SORT_DESC])->asArray()->all();
                $list = [];
                $cur_time = date("Y-m-d H:i:s");
                foreach ($dataAll as $value) {
                    if ($value['expire'] < $cur_time) {
                        $value['status_text'] = '已过期';
                        $value['status'] = 3;
                    }elseif($value['use_status'] == 0) {
                        $value['status_text'] = '未分配';
                        $value['status'] = 4;
                    }elseif($value['use_status']== 1){
                        $value['status_text'] = '使用中';
                        $value['status'] = 2;
                    }else{
                        $value['status_text'] = '停用';
                        $value['status'] = 1;
                    }
                    if ($status != 0 && $status != $value['status']) {
                        continue;
                    }
                    $value['duration'] = $value['duration'] ? DateUtil::getHumanFormatBySecond($value['duration']*60) : "";
                    $list[] = $value;
                }

                $count = count($list);
                $list = array_slice($list,$offset,$pageSize);

                foreach ($list as &$value) {
                    $value['expire'] = date("Y-m-d", strtotime($value['expire']));
                    $value['start_time'] = date("Y-m-d", strtotime($value['start_time']));
                    $value['duration_turth'] = DateUtil::getHumanFormatBySecond($value['seconds']);
                }
                return [
                    'count'     => $count,
                    'list'      => $list,
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //获取可用的坐席列表
        public function actionGetUsableExten()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];
                $cre_time = date("Y-m-d H:i:s");
                $list = DialoutAgent::find()
                    ->alias('a')
                    ->select('a.exten,a.small_phone')
                    ->leftJoin(DialoutBindWorkUser::tableName() . ' b', 'a.exten=b.exten')
                    ->where(['a.corp_id'=>$corpId,'a.status'=>1])
                    ->andWhere(['>=', 'a.expire', $cre_time])
                    ->andWhere(['is', 'b.id', null])
                    ->orderBy(['a.id'=>SORT_ASC])
                    ->asArray()
                    ->all();
                return [
                    'list'      => $list,
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //外呼明细
        public function actionDialoutDetail(){
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];
                $id     = \Yii::$app->request->post('id',0);
                $sdate     = \Yii::$app->request->post('sdate') ?: null;
                $edate     = \Yii::$app->request->post('edate') ?: null;

                $page        = \Yii::$app->request->post('page') ?: 1;
                $pageSize    = \Yii::$app->request->post('page_size') ?: 15;
                $offset      = ($page - 1) * $pageSize;

                $sdate = $sdate ? strtotime($sdate) : null;
                $edate = $edate ? strtotime($edate . ' 23:59:59') : null;

                if (!$id) {
                    throw new InvalidParameterException('缺少必要参数！');
                }
                $bindData = DialoutBindWorkUser::findOne($id);
                if (!$bindData) {
                    throw new InvalidParameterException('数据不存在！');
                }

                $user_id = $bindData->user_id;
                $exten = $bindData->exten;

                $where = ['a.corp_id'=>$corpId,'a.user_id'=>$user_id,'a.exten'=>$exten,'a.state'=>1];
                $andWhere = ['>', 'a.begin', 0];
                $andFilterWhere = ['and',['>=', 'a.ring', $sdate], ['<=', 'a.ring', $edate]];

                //员工部门信息
                $departmentQuery = WorkUser::find()
                    ->alias('a')
                    ->select(['a.id',"group_concat(b.name separator '/') department"])
                    ->leftJoin(WorkDepartment::tableName() . ' b', 'FIND_IN_SET(b.department_id,a.department)')
                    ->where(['a.corp_id'=>$corpId,'b.corp_id'=>$corpId])
                    ->addGroupBy('a.id');

                $userInfo = WorkUser::find()->select([
                    'a.name',
                    'IFNULL(c.department, "") department',
                ])->alias('a')->leftJoin(['c'=>$departmentQuery], 'a.id=c.id')->where(['a.id'=>$user_id])->asArray()->all();

                $statis =  DialoutRecord::find()
                    ->alias('a')
                    ->select([
                        'IFNULL(sum(a.money),0) money',
                        'IFNULL(sum(ceil((a.end-a.begin)/60)),0) duration',
                        'IFNULL(sum(a.end-a.begin),0) seconds'
                    ])->where($where)->andWhere($andWhere)->andWhere($andWhere)->andFilterWhere($andFilterWhere)
                    ->asArray()
                    ->all();

                $statis = $statis[0];
                $statis['duration'] = !$statis['duration'] ? "--小时--分钟" : DateUtil::getHumanFormatBySecond($statis['duration']*60);
                $statis['duration_turth'] = !$statis['seconds'] ? "--小时--分钟" : DateUtil::getHumanFormatBySecond($statis['seconds']);
                $statis['user_name'] = $userInfo[0]['name'] ?? '';
                $statis['department'] = $userInfo[0]['department'] ?? '';

                $query = DialoutRecord::find()
                    ->alias('a')
                    ->leftJoin(WorkExternalContact::tableName() . ' d', 'a.external_userid=d.id')
                    ->leftJoin(PublicSeaCustomer::tableName() . ' e', 'a.external_userid=e.id')
                    ->where($where)
                    ->andWhere($andWhere)
                    ->andFilterWhere($andFilterWhere);

                $count = $query->count();

                $list = $query->select([
                    'a.money',
                    'IF(a.begin>0, a.end-a.begin, 0) seconds',
                    'IFNULL(IF(a.custom_type=1,e.name,d.name_convert), "") custom_name',
                    'a.ring',
                    'a.begin',
                    'a.end',
                    'a.real_called',
                    'concat(a.file_server, "/", a.record_file) file'
                ])->limit($pageSize)->offset($offset)->orderBy(['a.create_time'=>SORT_DESC])->asArray()->all();

                foreach ($list as &$value) {
                    $value['duration'] = DateUtil::getHumanFormatBySecond(ceil($value['seconds']/60)*60);
                    $value['duration_turth'] = DateUtil::getHumanFormatBySecond($value['seconds']);
                    $value['ring'] = date("Y-m-d H:i:s", $value['ring']);
                    $value['begin'] = date("Y-m-d H:i:s", $value['begin']);
                    $value['end'] = date("Y-m-d H:i:s", $value['end']);
                }
                $data = [
                    'statis'=>$statis,
                    'count'=>$count,
                    'list'=>$list,
                ];
                return $data;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //坐席明细
        public function actionExtenDetail(){
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];
                $id     = \Yii::$app->request->post('id',0);
                $sdate     = \Yii::$app->request->post('sdate') ?: null;
                $edate     = \Yii::$app->request->post('edate') ?: null;
                $keyword     = \Yii::$app->request->post('keyword') ?: null;

                $page        = \Yii::$app->request->post('page') ?: 1;
                $pageSize    = \Yii::$app->request->post('page_size') ?: 15;
                $offset      = ($page - 1) * $pageSize;

                $sdate = $sdate ? strtotime($sdate) : null;
                $edate = $edate ? strtotime($edate . ' 23:59:59') : null;

                if (!$id) {
                    throw new InvalidParameterException('缺少必要参数！');
                }
                $ExtenData = DialoutAgent::findOne($id);
                if (!$ExtenData) {
                    throw new InvalidParameterException('数据不存在！');
                }

                $departmentQuery = WorkUser::find()
                    ->alias('a')
                    ->select(['a.id',"group_concat(b.name separator '/') department"])
                    ->leftJoin(WorkDepartment::tableName() . ' b', 'FIND_IN_SET(b.department_id,a.department)')
                    ->where(['a.corp_id'=>$corpId,'b.corp_id'=>$corpId])
                    ->addGroupBy('a.id');

                $exten = $ExtenData->exten;
                $small_phone = $ExtenData->small_phone;

                $where = ['a.corp_id'=>$corpId,'a.exten'=>$exten,'a.state'=>1];
                $andWhere = ['>', 'a.begin', 0];
                $andFilterWhere = ['and',['>=', 'a.ring', $sdate], ['<=', 'a.ring', $edate]];
                $andKeywordFilterWhere = ['or',['like', 'b.name', $keyword],['=', 'a.small_phone', $keyword]];

                $statis =  DialoutRecord::find()
                    ->alias('a')
                    ->select([
                        'IFNULL(sum(a.money),0) money',
                        'IFNULL(sum(ceil((end-begin)/60)),0) duration',
                        'IFNULL(sum(end-begin),0) seconds',
                    ])->leftJoin(WorkUser::tableName() . ' b', 'a.user_id=b.id')
                    ->where($where)->andWhere($andWhere)->andFilterWhere($andFilterWhere)->andFilterWhere($andKeywordFilterWhere)
                    ->asArray()
                    ->all();

                $statis = $statis[0];

                $statis['duration'] = !$statis['duration'] ? '--小时--分钟' : DateUtil::getHumanFormatBySecond($statis['duration']*60);
                $statis['duration_turth'] = !$statis['seconds'] ? '--小时--分钟' : DateUtil::getHumanFormatBySecond($statis['seconds']);
                $statis['exten'] = $exten;

                $query = DialoutRecord::find()
                    ->alias('a')
                    ->leftJoin(WorkUser::tableName() . ' b', 'a.user_id=b.id')
                    ->leftJoin(['c'=>$departmentQuery],'a.user_id=c.id')
                    ->leftJoin(WorkExternalContact::tableName() . ' d', 'a.external_userid=d.id')
                    ->leftJoin(PublicSeaCustomer::tableName() . ' e', 'a.external_userid=e.id')
                    ->where($where)->andWhere($andWhere)->andFilterWhere($andFilterWhere)->andFilterWhere($andKeywordFilterWhere);

                $count = $query->count();

                $list = $query->select([
                    'a.money',
                    'IF(a.begin>0, a.end-a.begin, 0) seconds',
                    'a.ring',
                    'a.small_phone',
                    'IFNULL(IF(a.custom_type=1,e.name,d.name_convert), "") custom_name',
                    'IFNULL(b.name, "") user_name',
                    'IFNULL(c.department, "") department'
                ])->limit($pageSize)->offset($offset)->orderBy(['a.ring'=>SORT_DESC])->asArray()->all();

                foreach ($list as &$value) {
                    $value['ring'] = date("Y-m-d H:i", $value['ring']);
                    $value['create_time'] = $value['ring'];
                    $value['duration_turth'] = DateUtil::getHumanFormatBySecond($value['seconds']);
                    $value['duration'] = DateUtil::getHumanFormatBySecond(ceil($value['seconds']/60) * 60);
                    $value['small_phone'] = $value['small_phone']==$small_phone ? "" : $value['small_phone'];
                }
                $data = [
                    'statis'=>$statis,
                    'count'=>$count,
                    'list'=>$list,
                ];
                return $data;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

		//坐席员工列表
		public function actionExtenUserList()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];
                $status     = \Yii::$app->request->post('status', null);
                $keyword     = \Yii::$app->request->post('keyword',null);
                $page        = \Yii::$app->request->post('page') ?: 1;
                $pageSize    = \Yii::$app->request->post('page_size') ?: 15;
                $offset      = ($page - 1) * $pageSize;
                $status = $status ?: null;
                $keyword = $keyword?: null;
                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,[]);

                $departmentQuery = WorkUser::find()
                    ->alias('a')
                    ->select(['a.id',"group_concat(b.name separator '/') department"])
                    ->leftJoin(WorkDepartment::tableName() . ' b', 'FIND_IN_SET(b.department_id,a.department)')
                    ->where(['a.corp_id'=>$corpId,'b.corp_id'=>$corpId])
                    ->addGroupBy('a.id');

                $callQuery = DialoutRecord::find()
                    ->select([
                        'exten',
                        'user_id',
                        'sum(money) money',
                        'sum(ceil((end-begin)/60)) duration',
                        'sum((end-begin)) seconds'
                    ])->where(['corp_id'=>$corpId])
                    ->andFilterWhere(['in', 'user_id', $userIds])
                    ->andWhere(['>', 'begin', 0])
                    ->addGroupBy(['user_id','exten']);

                $query = DialoutBindWorkUser::find()
                    ->alias('a')
                    ->leftJoin(WorkUser::tableName() . ' b', 'a.user_id=b.id')
                    ->leftJoin(['c'=>$departmentQuery], 'a.user_id=c.id')
                    ->innerJoin(DialoutAgent::tableName() . ' d', 'a.exten=d.exten')
                    ->leftJoin(['f'=>$callQuery], 'a.user_id=f.user_id and a.exten=f.exten')
                    ->where(['a.corp_id'=>$corpId, 'd.corp_id'=>$corpId])
                    ->andFilterWhere(['a.status'=>$status])
                    ->andFilterWhere(['or',['like', 'b.name', $keyword],['like', 'd.small_phone', $keyword]]);

                $count = $query->count();
                $select = [
                    'a.id',
                    'a.exten',
                    'd.small_phone',
                    'IFNULL(b.name, "") user_name',
                    'IFNULL(b.gender, 1) gender',
                    'IFNULL(b.thumb_avatar, "") thumb_avatar',
                    'IFNULL(b.avatar, "") avatar',
                    'IFNULL(c.department, "") department',
                    'IFNULL(f.money, 0) phone_money',
                    'IFNULL(f.duration, 0) duration',
                    'IFNULL(f.seconds, 0) seconds',
                    'a.status'
                ];
                $data = $query->select($select)->limit($pageSize)->offset($offset)->orderBy(['a.create_time'=>SORT_DESC])->asArray()->all();
                $list = [];
                foreach ($data as $value) {
                    $value['duration'] = $value['duration'] ? DateUtil::getHumanFormatBySecond($value['duration'] * 60) : "";
                    $value['duration_turth'] = $value['seconds'] ? DateUtil::getHumanFormatBySecond($value['seconds']) : "";
                    $list[] = $value;
                }
                return [
                    'count'     => $count,
                    'list'      => $list,
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }

        }

        //添加员工坐席
		public function actionBindExten()
        {
            if (empty($this->corp)) {
                throw new InvalidParameterException('参数不正确！');
            }
            $cre_time = date("Y-m-d H:i:s");
            $corpId     = $this->corp['id'];
            $userId   = \Yii::$app->request->post('user_id');
            $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,[$userId]);
            if (count($userIds) != 1) {
                throw new InvalidParameterException('请选择正确的员工！');
            }

            $exten   = \Yii::$app->request->post('exten', '');
            if (!$exten) {
                throw new InvalidParameterException('坐席工号不能为空！');
            }

            //检查正在使用的员工
            if (DialoutBindWorkUser::checkUsing($corpId, $userIds)){
                throw new InvalidParameterException('不允许对同一个正在启用状态的员工绑定多个工号！');
            }

            //检查坐席是否可用
            $extenIsUsable = DialoutAgent::checkUsableByExten($exten,$corpId);
            if (!$extenIsUsable) {
                throw new InvalidParameterException('该坐席不可用！');
            }

            //检查坐席是否已经分配
            $extenIsDistribute = DialoutAgent::chackIsdistribute($exten, $corpId);
            if ($extenIsDistribute) {
                throw new InvalidParameterException('该坐席已经被分配！');
            }

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $data = new DialoutBindWorkUser;
                $data->corp_id = $corpId;
                $data->user_id = $userId;
                $data->exten = $exten;
                $data->status = 1;
                $data->create_time = $cre_time;
                $data->save();
                DialoutAgent::updateAll(['last_use_user'=>$userId],['corp_id'=>$corpId,'exten'=>$exten]);
                $transaction->commit();
            }catch (InvalidDataException $e) {
                $transaction->rollBack();
                throw new InvalidDataException($e->getMessage());
            }

            return true;
        }

        //检查该坐席是否处于登录状态
        public function actionCheakState()
        {
            if (empty($this->corp)) {
                throw new InvalidParameterException('参数不正确！');
            }
            $corpId     = $this->corp['id'];
            $type = \Yii::$app->request->post('option_type','');
            $id = \Yii::$app->request->post('id','');
            if (!$id) {
                throw new InvalidParameterException('缺少必要参数！');
            }

            if ($type == 3) {
                $data = DialoutAgent::findOne($id);
            }else{
                $data = DialoutBindWorkUser::findOne($id);
            }

            if (!$data) {
                throw new InvalidParameterException('数据不存在！');
            }

            $stateInfo = DialoutAgent::cheakState($data->exten,$data->corp_id);

            $result = $stateInfo;
            $result['msg'] = '';
            if ($result['state'] == 1) {
                switch ($type) {
                    case 1:
                        break;
                    case 2:
                        $userInfo = WorkUser::findOne($data->user_id);
                        $userName = $userInfo ? $userInfo->name : '';
                        $result['msg'] = "启用后，【" . $userName . "】具有外呼客户的权限。确定启用吗？";
                        $usingUser = DialoutBindWorkUser::getUsingUser($data->exten,$data->corp_id);
                        if (!empty($usingUser['user_id']) && $usingUser['user_id'] != $data->user_id) {
                            $usingName = $usingUser['name'];
                            $result['msg'] = "当前工号" . $data->exten . " 已被【" . $usingName . "】 使用，需要更换成【" . $userName .  "】使用吗？一旦更换后，【" . $usingName . "】将不可使用，确定更换吗？";
                        }
                        break;
                    case 3:
                        $usingUser = DialoutBindWorkUser::getUsingUser($data->exten,$data->corp_id);
                        $usingName = $usingUser['name'] ?? '';
                        $result['msg'] = "";
                        $result['using_name'] = $usingName;
                        $result['exten'] = $data->exten;
                        break;
                }
            }

            return $result;
        }

        //转移坐席
        public function actionShiftExten(){
            if (empty($this->corp)) {
                throw new InvalidParameterException('参数不正确！');
            }
            $corpId     = $this->corp['id'];
            $id = \Yii::$app->request->post('id','');
            $userId   = \Yii::$app->request->post('user_id',0);

            if (!$userId) {
                throw new InvalidParameterException('目标成员不能为空！');
            }

            $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,[$userId]);
            if (count($userIds) != 1) {
                throw new InvalidParameterException('请选择正确的员工！');
            }

            if (!$id) {
                throw new InvalidParameterException('缺少必要参数！');
            }

            //检查员工是否正在使用
            if (DialoutBindWorkUser::checkUsing($corpId, $userIds)){
                throw new InvalidParameterException('不允许对同一个正在启用状态的员工绑定多个工号！');
            }

            $data = DialoutAgent::findOne($id);

            if (!$data) {
                throw new InvalidParameterException('数据不存在！');
            }

            $exten = $data->exten;
            $corpId = $data->corp_id;

            //检查坐席是否可用
            $extenIsUsable = DialoutAgent::checkUsableByExten($exten,$corpId);
            if (!$extenIsUsable) {
                throw new InvalidParameterException('该坐席不可用！');
            }

            //是否在通话中
            $stateInfo = DialoutAgent::cheakState($exten,$corpId);
            if ($stateInfo['state'] == 2) {
                $msg = "【" . $stateInfo['user_name'] . "】正在通话中，不可禁用";
                throw new InvalidParameterException($msg);
            }

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $cre_time = date("Y-m-d H:i:s");
                //禁用该坐席
                DialoutBindWorkUser::updateAll(['status'=>2],['corp_id'=>$corpId,'exten'=>$exten,'status'=>1]);

                //将坐席工号分配给新员工
                $userInfo = DialoutBindWorkUser::find()->where(['corp_id'=>$corpId,'user_id'=>$userId,'exten'=>$exten])->all();

                if ($userInfo) {
                    $userInfo = $userInfo[0];
                    $userInfo->status = 1;
                }else{
                    $userInfo = new DialoutBindWorkUser;
                    $userInfo->corp_id = $corpId;
                    $userInfo->user_id = $userId;
                    $userInfo->exten = $exten;
                    $userInfo->status = 1;
                    $userInfo->create_time = $cre_time;
                }
                $userInfo->save();
                DialoutAgent::updateAll(['last_use_user'=>$userId],['corp_id'=>$corpId,'exten'=>$exten]);
                $transaction->commit();
            }catch (InvalidDataException $e) {
                $transaction->rollBack();
                throw new InvalidDataException($e->getMessage());
            }
            return true;
        }

        //禁用员工坐席
        public function actionForbidden()
        {
            if (empty($this->corp)) {
                throw new InvalidParameterException('参数不正确！');
            }
            $corpId     = $this->corp['id'];
            $id = \Yii::$app->request->post('id','');
            if (!$id) {
                throw new InvalidParameterException('缺少必要参数！');
            }
            $data = DialoutBindWorkUser::findOne($id);

            if (!$data) {
                throw new InvalidParameterException('参数有误！');
            }
            $stateInfo = DialoutAgent::cheakState($data->exten,$data->corp_id);
            if ($stateInfo['state'] == 2) {
                $msg = "【" . $stateInfo['user_name'] . "】正在通话中，不可禁用";
                throw new InvalidParameterException($msg);
            }

            $data->status = 2;
            $data->save();

            return true;
        }

        //开启员工坐席
        public function actionOpen()
        {
            if (empty($this->corp)) {
                throw new InvalidParameterException('参数不正确！');
            }
            $corpId     = $this->corp['id'];
            $id = \Yii::$app->request->post('id','');
            if (!$id) {
                throw new InvalidParameterException('缺少必要参数！');
            }

            $data = DialoutBindWorkUser::findOne($id);

            if (!$data) {
                throw new InvalidParameterException('参数有误！');
            }

            //检查员工是否正在使用
            if (DialoutBindWorkUser::checkUsing($corpId, [$data->user_id])){
                throw new InvalidParameterException('不允许对同一个正在启用状态的员工绑定多个工号！');
            }

            //是否在通话中
            $stateInfo = DialoutAgent::cheakState($data->exten,$data->corp_id);
            if ($stateInfo['state'] == 2) {
                $msg = "【" . $stateInfo['user_name'] . "】正在通话中，不可禁用";
                throw new InvalidParameterException($msg);
            }

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                DialoutAgent::updateAll(['last_use_user'=>$data->user_id],['corp_id'=>$corpId,'exten'=>$data->exten]);
                //正在使用的
                $usingUser = DialoutBindWorkUser::getUsingUser($data->exten,$data->corp_id);
                if (!empty($usingUser)) {
                    DialoutBindWorkUser::updateAll(['status'=>2],['id'=>$usingUser['id']]);
                }
                $data->status = 1;
                $data->save();

                $transaction->commit();
            }catch (InvalidDataException $e) {
                $transaction->rollBack();
                throw new InvalidDataException($e->getMessage());
            }

            return true;
        }

        //获取拨打电话所需要的参数
        public function actionDialoutData()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];
                $type     = \Yii::$app->request->post('type',0);
                $follow_id     = \Yii::$app->request->post('follow_id',0);

                $userId = \Yii::$app->request->post('userid', '');
                $externalUserid     = \Yii::$app->request->post('external_userid', '');

                //判断余额
                $balance = DialoutConfig::find()->select(['balance'])->where(['corp_id'=>$corpId])->asArray()->all();
                $balance = $balance[0]['balance'] ?? 0;
                if ($balance <= 0) {
                    throw new InvalidParameterException('余额不足，请先充值！');
                }

                $bindExen = DialoutBindWorkUser::isBindExten($corpId, $this->user->uid??0, $this->subUser->sub_id??0);
                if (!$bindExen) {
                    throw new InvalidParameterException('无可用坐席！');
                }

                $keyInfo = DialoutKey::findOne(['api_type'=>'7moor']);
                if (!$keyInfo) {
                    throw new InvalidParameterException('未分配线路！');
                }

                $bindInfo = explode('_',$bindExen);

                $dialout_phone = '';
                $external_userid = 0;

                if ($type == 0) {
                    $follow           = WorkExternalContactFollowUser::findOne($follow_id);
                    if (!$follow) {
                        throw new InvalidParameterException('参数不正确 ！');
                    }
                    $external_userid = $follow->external_userid;
                    $dialout_phone = CustomField::getDialoutPhone($external_userid, $follow->user_id);

                }elseif($type == 1) {
                    $follow = PublicSeaContactFollowUser::findOne($follow_id);
                    if (!$follow) {
                        throw new InvalidParameterException('参数不正确  ！');
                    }
                    $external_userid = $follow->sea_id;
                    $dialout_phone = PublicSeaCustomer::getDialoutPhone($external_userid);
                }elseif($type == 2) {
                    $workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $userId]);
                    if (empty($workUser)) {
                        throw new InvalidParameterException('员工数据错误！');
                    }
                    $externalUserData = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $externalUserid]);
                    if (empty($externalUserData)) {
                        throw new InvalidParameterException('客户数据错误！');
                    }

                    $external_userid = $externalUserData->id;
                    $dialout_phone = CustomField::getDialoutPhone($external_userid, $workUser->id);

                }

                if (!$dialout_phone) {
                    throw new InvalidParameterException('手机号不存在！');
                }

                return [
                    'ActionID'=>$bindInfo[0] . "_" . $external_userid . "_" . $type . '_' . time(),
                    'bindExen'=>$bindInfo[1],
                    'dialout_phone'=>$dialout_phone,
                    'api_key'=>$keyInfo->api_key,
                    'custom_key'=>$corpId,
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }


        //通话记录列表
        public function actionDialoutRecord()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $corpId     = $this->corp['id'];

                $keyword     = \Yii::$app->request->post('keyword') ?: null;
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $sdate     = \Yii::$app->request->post('sdate');
                $edate     = \Yii::$app->request->post('edate');
                $state     = \Yii::$app->request->post('state') ?: null;
                $province     = \Yii::$app->request->post('province') ?: null;
                $district     = \Yii::$app->request->post('district') ?: null;
                $is_export     = \Yii::$app->request->post('is_export',0);
                $export_all     = \Yii::$app->request->post('export_all',0);
                $ids     = \Yii::$app->request->post('ids') ?: [];

                $page        = \Yii::$app->request->post('page') ?: 1;
                $pageSize    = \Yii::$app->request->post('page_size') ?: 15;
                $offset      = ($page - 1) * $pageSize;
                $userId   = \Yii::$app->request->post('staffList');

                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userId);
                $sdate = $sdate ? strtotime($sdate) : null;
                $edate = $edate ? strtotime($edate . ' 23:59:59') : null;
                $pageSize = ($is_export&&$export_all) ? 99999 : $pageSize;

                $stateOption = '=';
                if ($state && $state != 1) {
                    $stateOption = '!=';
                    $state = 1;
                }

                $departmentQuery = WorkUser::find()
                    ->alias('a')
                    ->select(['a.id',"group_concat(b.name separator '/') department"])
                    ->leftJoin(WorkDepartment::tableName() . ' b', 'FIND_IN_SET(b.department_id,a.department)')
                    ->where(['a.corp_id'=>$corpId,'b.corp_id'=>$corpId])
                    ->addGroupBy('a.id');

                $subQuery = DialoutRecord::find()
                    ->alias('a')
                    ->select([
                            'a.*',
                            'from_unixtime(a.ring,"%k") ring_hour',
                            'IF(a.begin>0, a.end-a.begin, 0) seconds',
                            'IFNULL(IF(a.custom_type=1,e.name,d.name_convert), "") custom_name',
                            'IFNULL(b.name, "") user_name',
                        ])
                    ->leftJoin(WorkUser::tableName() . ' b', 'a.user_id=b.id')
                    ->leftJoin(WorkExternalContact::tableName() . ' d', 'a.external_userid=d.id')
                    ->leftJoin(PublicSeaCustomer::tableName() . ' e', 'a.external_userid=e.id')
                    ->where(['a.corp_id'=>$corpId])
                    ->andFilterWhere(['and',
                        ['in', 'a.user_id', $userIds],
                        ['>=','a.ring', $sdate],
                        ['<=', 'a.ring', $edate],
                        ['=','a.province',$province],
                        ['=','a.district',$district],
                        [$stateOption,'a.state',$state],
                    ]);

                $select = [
                    'a.id',
                    'a.exten',
                    'a.custom_name',
                    'a.user_name',
                    'IFNULL(c.department, "") department',
                    'a.ring_hour',
                    'a.seconds',
                    'a.money',
                    'a.ring',
                    'a.ringing',
                    'a.end',
                    'a.state',
                    'a.file_server',
                    'a.record_file',
                    'a.province',
                    'a.district',
                    'a.create_time',
                    'a.real_called',
                    'a.begin',
                ];

                $startHour = null;
                $endHour = null;
                if ($timeRange ) {
                    $startHour = ($timeRange-1) * 6;
                    $endHour = $timeRange * 6 -1;
                }

                $minSecond = null;
                $maxSecond = null;
                switch ($timeScope) {
                    case 1:
                        $minSecond = 0;
                        $maxSecond = 300;
                        break;
                    case 2:
                        $minSecond = 300+1;
                        $maxSecond = 600;
                        break;
                    case 3:
                        $minSecond = 600+1;
                        $maxSecond = 1800;
                        break;
                    case 4:
                        $minSecond = 1800+1;
                        $maxSecond = 3600;
                        break;
                    case 5:
                        $minSecond = 3600+1;
                        break;
                }

                //统计数据
                $statis = (new Query())
                    ->select([
                        'IFNULL(sum(IF(a.begin>0, ceil((a.end-a.begin)/60) ,0)),0) duration',
                        'IFNULL(sum(a.seconds),0) seconds',
                        'count(*) total_num',
                        'count(IF(a.state=1, 1, null)) connect_num',
                        'count(IF(a.state!=1, 1, null)) unconnect_num',
                    ])->from(['a'=>$subQuery])
                    ->andFilterWhere(['or',['like', 'a.user_name', $keyword],['like', 'a.custom_name', $keyword]])
                    ->andFilterWhere(['and',
                        ['>=','a.ring_hour', $startHour],
                        ['<=', 'a.ring_hour', $endHour],
                        ['>=', 'a.seconds', $minSecond],
                        ['<=', 'a.seconds', $maxSecond]
                    ])->all();

                $statis = $statis[0];
                $statis['duration'] = !$statis['duration'] ? "--小时--分钟" : DateUtil::getHumanFormatBySecond($statis['duration']*60);
                $statis['duration_turth'] = !$statis['seconds'] ? "--小时--分钟" : DateUtil::getHumanFormatBySecond($statis['seconds']);
                $statis['total_num'] = $statis['total_num'] ?: '--';
                $statis['connect_num'] = $statis['connect_num'] ?: '--';
                $statis['unconnect_num'] = $statis['unconnect_num'] ?: '--';

                $query = (new Query())->select($select)
                    ->from(['a'=>$subQuery])
                    ->leftJoin(['c'=>$departmentQuery], 'a.user_id=c.id')
                    ->andFilterWhere(['in', 'a.id', $ids])
                    ->andFilterWhere(['or',['like', 'a.user_name', $keyword],['like', 'a.custom_name', $keyword]])
                    ->andFilterWhere(['and',
                            ['>=','a.ring_hour', $startHour],
                            ['<=', 'a.ring_hour', $endHour],
                            ['>=', 'a.seconds', $minSecond],
                            ['<=', 'a.seconds', $maxSecond]
                        ]);

                $count = $query->count();

                $data = $query->select($select)->limit($pageSize)->offset($offset)->orderBy(['a.create_time'=>SORT_DESC])->all();
                $list = [];
                foreach ($data as $value) {
                    $value['file_name'] = $value['file_server'] . '/' . $value['record_file'];
                    $value['duration_turth'] = $value['state']!= 1 ? '--' : DateUtil::getHumanFormatBySecond($value['seconds']);
                    $value['duration'] = $value['state']!= 1 ? '--' : DateUtil::getHumanFormatBySecond(ceil($value['seconds']/60)*60);

                    if ($value['state'] == 1) {
                        $value['state_text'] = "接通";
                    }else{
                        $dd = $value['ringing'] ? "响铃时长" . ($value['end'] - $value['ringing']) . "s" : '-';
                        $value['state_text'] = "未接通(" . $dd . ")";
                    }
                    $value['ring'] = date("Y-m-d H:i:s", $value['ring']);
                    $value['end'] = $value['end'] ? date("Y-m-d H:i:s", $value['end']) : '';
                    $value['begin'] = $value['begin'] ? date("Y-m-d H:i:s", $value['begin']) : '';
                    $list[] = $value;
                }

                if ($is_export) {
                    if (empty($list)) {
                        throw new InvalidParameterException('暂无数据，无法导出！');
                    }
                    foreach ($list as &$value) {

                        $value['user_name'] = $value['user_name'] . '(' . $value['department'] . ')';
                        $value['region'] = $value['province'] . ' ' . $value['district'] ;
                    }

                    $headers = [
                        'ring'=>'拨打时间',
                        'user_name'=>'成员拨打',
                        'custom_name'=>'客户接听',
                        'region'=>'手机号归属地',
                        'duration_turth'=>'实际通话时长',
                        'duration'=>'计费通话时长',
                        'state_text'=>'状态',
                    ];
                    \Yii::$app->work->push(new DialoutExportJob([
                        'exportData'  => $list,
                        'headers' => $headers,
                        'uid'     => empty($this->user->uid) ? $this->subUser->sub_id : $this->user->uid,
                        'corpId'  => $corpId,
                        'fileName'  => '通话记录' . '_' . date("YmdHis"),
                    ]));
                    return ['error' => 0];
                }

                $list = static::addRankingFiled($list, ($page-1)*$pageSize+1);

                return [
                    'count'     => $count,
                    'list'      => $list,
                    'statis'   => $statis,
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //得到通话状态
        public function actionGetCallState()
        {
            if (\Yii::$app->request->isPost) {
                $ActionID     = \Yii::$app->request->post('ActionID','');

                if (!$ActionID) {
                    throw new InvalidParameterException('参数不正确 ！');
                }
                $state = \Yii::$app->cache->get($ActionID);

                // 0 啥也没有 1：接通 2：挂断 3: 被叫呼铃

                $state = $state ?: 0;
                return [
                    'state'=>$state
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //忽略提示
        public function actionIgnoreTip()
        {
            if (\Yii::$app->request->isPost) {
                $ActionID     = \Yii::$app->request->post('ActionID','');

                if (!$ActionID) {
                    throw new InvalidParameterException('参数不正确 ！');
                }

                $actionIDs = explode('_', $ActionID);

                $external_userid = $actionIDs[1] ?? 0;
                $custom_type = $actionIDs[2] ?? -1;

                if ($custom_type != 1) {
                    throw new InvalidParameterException('actionId 有误 ！');
                }

                $custom = PublicSeaCustomer::findOne($external_userid);
                if (!$custom) {
                    throw new InvalidParameterException('客户不存在！');
                }

                $custom->ignore_add_wechat_tip = 1;
                $custom->save();
                return [
                    'error'=>0
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //获取忽略提示状态
        public function actionGetIgnoreTip()
        {
            if (\Yii::$app->request->isPost) {
                $ActionID     = \Yii::$app->request->post('ActionID','');

                if (!$ActionID) {
                    throw new InvalidParameterException('参数不正确 ！');
                }

                $actionIDs = explode('_', $ActionID);

                $external_userid = $actionIDs[1] ?? 0;
                $custom_type = $actionIDs[2] ?? -1;

                if ($custom_type != 1) {
                    throw new InvalidParameterException('actionId 有误 ！');
                }

                $custom = PublicSeaCustomer::findOne($external_userid);
                if (!$custom) {
                    throw new InvalidParameterException('客户不存在！');
                }

                return [
                    'state'=>$custom->ignore_add_wechat_tip
                ];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //======================================统计接口=====================================
        //顶部的昨日数据
        public function actionYesterday()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }

                $defaultValue = [
                    'title'         =>'',
                    'introduce'     =>'',
                    'status'        => 0,
                    'count'         => 0,
                    'per'           => '0%',
                    'text'         => '',
                ];
                $resultData = [
                    'total'       => ['title'=>'昨日总拨打次数','introduce'=>'拨打电话的总次数（包含未接通）'],
                    'connect' => ['title'=>'昨日接通数','introduce'=>'电话接通的次数'],
                    'unconnect'    => ['title'=>'昨日未接通数','introduce'=>'电话未接通的次数'],
                    'connect_prob'          => ['title'=>'昨日接通率','introduce'=>'电话接通的百分比'],
                    'duration'        => ['title'=>'昨日计费通话时长','introduce'=>'通话时长不满1分钟，则按1分钟计时'],
                    'avg_duration'         => ['title'=>'昨日平均计费通话时长','introduce'=>'计费通话时长/接通数。通话时长不满1分钟，则按1分钟计时'],
                    'duration_turth'        => ['title'=>'昨日实际通话时长','introduce'=>'实际通话时长'],
                    'avg_duration_turth'         => ['title'=>'昨日平均实际通话时长','introduce'=>'实际通话时长/接通数'],
                ];

                foreach ($resultData as $key=>$value)
                {
                    $resultData[$key] = array_merge($defaultValue, $value);
                }

                $corpid = $this->corp['id'];
                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,[]);

                $data = DialoutRecord::getYesterday($corpid, $userIds);

                foreach ($resultData as $key=>$value)
                {
                    if (isset($data[$key])) {
                        $resultData[$key] = array_merge($value,$data[$key]);
                    }
                }

                return $resultData;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //chart图
        public function actionChart()
        {
            $chartType = \Yii::$app->request->post('chartType') ?: null;
            switch ($chartType) {
                case 0:
                    return $this->actionChartTop();
                    break;
                case 1:
                    return $this->actionChartTrend();
                    break;
                case 2:
                    return $this->actionChartRegion();
                    break;
                case 3:
                    return $this->actionChartRegion();
                    break;
            }
        }

        //chart图，排行榜
        public function actionChartTop()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');
                $isDepartment   = \Yii::$app->request->post('staffType', 0);
                $dataType   = \Yii::$app->request->post('dataType', '');
                $order = \Yii::$app->request->post('order','desc');
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');

                $corpId = $this->corp['id'];
                if (!$dataType) {
                    throw new InvalidParameterException('缺少字段类型！');
                }

                static::checkDate($sdate, $edate);

                if ($isDepartment) {
                    [$userIds,$departmentIds] = static::getVisibleUsersAndDepartments($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    $result = DialoutRecord::getDepartmentChartTopData($dataType, $corpId, $userIds,$departmentIds,$sdate,$edate,$timeRange,$timeScope, 1,15, false, $order);
                }else{
                    $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    //根据类型获取数据
                    $result = DialoutRecord::getUserChartTopData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope, 1,15, false, $order);

                }

                $xData = [];
                $seriesData = [];

                if (!empty($result['list'])) {
                    foreach ($result['list'] as $v) {
                        array_push($xData, $v['name']);
                        array_push($seriesData, $v[$dataType]??0);
                    }
                }

                $info = [
                    'xData'      => $xData,
                    'seriesData' => $seriesData,
                    'name'=>DialoutRecord::getNameByDataType($dataType),
                ];

                return $info;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //chart图，趋势图
        public function actionChartTrend()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');
                $dataType   = \Yii::$app->request->post('dataType', '');
                $timeType   = \Yii::$app->request->post('timeType', 0);
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');

                $corpId = $this->corp['id'];
                if (!$dataType) {
                    throw new InvalidParameterException('缺少字段类型！');
                }

                static::checkDate($sdate, $edate);

                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);

                $result = DialoutRecord::getUserChartTrendData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope,$timeType);

                $xData = [];
                $yData = [];

                $tipName = DialoutRecord::getNameByDataType($dataType);

                foreach ($result as $v) {
                    array_push($xData, $v['key']);
                    array_push($yData, $v[$dataType]??0);
                }
                $info = [
                    'xData'      => $xData,
                    'seriesData' => [
                        'data'=>$yData,
                        'name'=>$tipName,
                        'smooth'=>true,
                        'type'=>'line',
                    ],
                    'name'=>DialoutRecord::getNameByDataType($dataType),
                ];

                return $info;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //chart图，地区分布
        public function actionChartRegion()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');
                $dataType   = \Yii::$app->request->post('dataType', '');
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');
                $chartType = \Yii::$app->request->post('chartType') ?: null;
                $order     = \Yii::$app->request->post('order', 'desc');

                $corpId = $this->corp['id'];
                if (!$dataType) {
                    throw new InvalidParameterException('缺少字段类型！');
                }

                static::checkDate($sdate, $edate);

                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);

                $result = DialoutRecord::getUserChartRegionData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);
                $data = [];
                foreach ($result as $value) {
                    $tmp['name'] = $value['province'];
                    $tmp['num'] = 0;
                    if($dataType == 'total') {
                        $tmp['num'] = $value['total_num'];
                    }elseif($dataType == 'connect') {
                        $tmp['num'] = $value['connect_num'];
                    }elseif($dataType == 'unconnect') {
                        $tmp['num'] = $value['unconnect_num'];
                    }
                    $data[] = $tmp;
                }

                $last_names = array_column($data,'num');
                array_multisort($last_names,$order=='desc'?SORT_DESC:SORT_ASC,$data);

                $xData = [];
                $seriesData = [];

                if ($chartType == 2) {
                    foreach ($data as $v) {
                        $tmp = [
                            'value'=>$v['num'],
                            'name'=>$v['name']
                        ];
                        array_push($seriesData, $tmp);
                    }
                    $info['seriesData'] = $seriesData;
                }else{
                    foreach ($data as $v) {
                        array_push($xData, $v['name']);
                        array_push($seriesData, $v['num']);
                    }

                    $info = [
                        'xData'      => $xData,
                        'seriesData' => $seriesData,
                        'name'=>DialoutRecord::getNameByDataType($dataType),
                    ];
                }

                return $info;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //表格
        public function actionTable(){
            $tableType = \Yii::$app->request->post('chartType') ?: null;
            switch ($tableType) {
                case 0:
                    return $this->actionTableTop();
                    break;
                case 1:
                    return $this->actionTableTrend();
                    break;
                case 2:
                case 3:
                    return $this->actionTableRegion();
                    break;
            }
        }
        //表格，排行榜
        public function actionTableTop()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');
                $isDepartment   = \Yii::$app->request->post('staffType', 0);
                $dataType   = \Yii::$app->request->post('dataType', '');
                $order = \Yii::$app->request->post('order','desc');
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');
                $page     = \Yii::$app->request->post('page', 1);
                $pageSize     = \Yii::$app->request->post('page_size', 15);
                $is_export     = \Yii::$app->request->post('is_export', 0);

                static::checkDate($sdate, $edate);

                $pageSize = $is_export ? 99999: $pageSize;

                $corpId = $this->corp['id'];
                if (!$dataType) {
                    throw new InvalidParameterException('缺少字段类型！');
                }

                if ($isDepartment) {
                    [$userIds,$departmentIds] = static::getVisibleUsersAndDepartments($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    $result = DialoutRecord::getDepartmentChartTopData($dataType, $corpId, $userIds,$departmentIds,$sdate,$edate,$timeRange,$timeScope, $page,$pageSize, true, $order);
                }else{
                    $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    $result = DialoutRecord::getUserChartTopData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope, $page,$pageSize, true, $order);
                }

                $list = $result['list'] ? : [];

                foreach ($list as &$value) {
                    $value['duration'] = DateUtil::getHumanFormatBySecond($value['duration']*60);
                    $value['duration_turth'] = DateUtil::getHumanFormatBySecond($value['duration_turth']);
                    $value['avg_duration_turth'] = DateUtil::getHumanFormatBySecond($value['avg_duration_turth']);
                    $value['duration_date'] = $sdate==$edate ? $sdate : $sdate . '~' . $edate;
                    $value['avg_duration'] = $value['avg_duration'] ? $value['avg_duration'] . '分钟' : '';
                }

                $list = static::addRankingFiled($list, ($page-1)*$pageSize+1);

                $tipName = DialoutRecord::getNameByDataType($dataType);

                if ($is_export) {
                    $data = [];
                    foreach ($list as $value) {
                        $tmp['ranking'] = $value['ranking'];
                        $tmp['duration_date'] = $value['duration_date'];
                        $tmp['name'] = isset($value['department']) ? $value['name'] . '(' . $value['department'] . ')' : $value['name'];
                        $tmp['num'] = $value[$dataType] ?? 0;
                        $data[] = $tmp;
                    }


                    if (empty($data)) {
                        throw new InvalidParameterException('暂无数据，无法导出！');
                    }

                    $headers = [
                        'ranking' => '排行',
                        'duration_date'=>'时间',
                        'num'=>$tipName,
                        'name'=>'成员',
                    ];
                    \Yii::$app->work->push(new DialoutExportJob([
                        'exportData'  => $data,
                        'headers' => $headers,
                        'uid'     => empty($this->user->uid) ? $this->subUser->sub_id : $this->user->uid,
                        'corpId'  => $corpId,
                    ]));
                    return ['error' => 0];
                }

                $result['list'] = $list;

                return $result;

            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //表格，趋势图
        public function actionTableTrend()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');
                $dataType   = \Yii::$app->request->post('dataType', '');
                $timeType   = \Yii::$app->request->post('timeType', 0);
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');
                $page     = \Yii::$app->request->post('page', 1);
                $pageSize     = \Yii::$app->request->post('page_size', 15);
                $order     = \Yii::$app->request->post('order', 'desc');
                $sortType     = \Yii::$app->request->post('sortType', 0);
                $is_export     = \Yii::$app->request->post('is_export', 0);

                static::checkDate($sdate, $edate);

                $pageSize = $is_export ? 99999: $pageSize;

                $corpId = $this->corp['id'];

                $headers['export_column_name'] = '成员';
                if ($sortType == 0) {
                    $headers['export_column_name'] = '日期';
                    $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    $result = DialoutRecord::getUserChartTrendData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope,$timeType);
                    //对数据进行处理
                    $count = count($result);
                    //根据字段last_name对数组$data进行排序
                    $last_names = array_column($result,$dataType);
                    array_multisort($last_names,$order=='desc'?SORT_DESC:SORT_ASC,$result);

                    $list = array_slice($result, ($page - 1) * $pageSize, $pageSize);
                    $result = [
                        'count'=>$count,
                        'list'=>$list,
                    ];
                }elseif($sortType == 1) {
                    $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    $result = DialoutRecord::getUserChartTopData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope, $page,$pageSize, true, $order);
                }else{
                    [$userIds,$departmentIds] = static::getVisibleUsersAndDepartments($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                    $result = DialoutRecord::getDepartmentChartTopData($dataType, $corpId, $userIds,$departmentIds,$sdate,$edate,$timeRange,$timeScope, $page,$pageSize, true, $order);
                }

                $list = $result['list'] ? : [];
                foreach ($list as &$value) {
                    $value['duration'] = DateUtil::getHumanFormatBySecond($value['duration']*60);
                    $value['duration_turth'] = DateUtil::getHumanFormatBySecond($value['duration_turth']);
                    $value['avg_duration_turth'] = DateUtil::getHumanFormatBySecond($value['avg_duration_turth']);
                    $value['avg_duration'] = $value['avg_duration'] ? $value['avg_duration'] . '分钟' : '';
                    $value['connect_prob'] = $value['connect_prob'] * 100 . '%';
                }

                if ($is_export) {
                    if (empty($list)) {
                        throw new InvalidParameterException('暂无数据，无法导出！');
                    }

                    foreach ($list as &$value) {
                        if ($sortType == 0) {
                            $value['export_column_name'] = $value['key'];
                        }elseif ($sortType == 1) {
                            $value['export_column_name'] = $value['name'] . '(' . $value['department'] . ')';
                        }else{
                            $value['export_column_name'] = $value['name'];
                        }
                    }

                    $headers = array_merge($headers,[
                        'total'=>'总拨打次数',
                        'connect'=>'接通数',
                        'unconnect'=>'未接通数',
                        'connect_prob'=>'接通率',
                        'duration_turth'=>'实际总通话时长',
                        'avg_duration_turth'=>'平均实际通话时长',
                        'duration'=>'总计费通话时长',
                        'avg_duration'=>'平均计费通话时长',
                    ]);

                    \Yii::$app->work->push(new DialoutExportJob([
                        'exportData'  => $list,
                        'headers' => $headers,
                        'uid'     => empty($this->user->uid) ? $this->subUser->sub_id : $this->user->uid,
                        'corpId'  => $corpId,
                    ]));
                    return ['error' => 0];
                }

                $result['list'] = $list;

                return $result;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //表格，地区分布
        public function actionTableRegion()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');
                $dataType   = \Yii::$app->request->post('dataType', '');
                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');
                $page     = \Yii::$app->request->post('page', 1);
                $pageSize     = \Yii::$app->request->post('page_size', 15);
                $order     = \Yii::$app->request->post('order', 'desc');
                $is_export     = \Yii::$app->request->post('is_export', 0);
                $sortFiled     = \Yii::$app->request->post('sortFiled', 'num');

                $corpId = $this->corp['id'];
                if (!$dataType) {
                    throw new InvalidParameterException('缺少字段类型！');
                }

                static::checkDate($sdate, $edate);

                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);

                $data = DialoutRecord::getUserChartRegionData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);

                $total = 0;
                $connect = 0;
                $unconnect = 0;

                foreach ($data as $v) {
                    $total += $v['total_num'];
                    $connect += $v['connect_num'];
                    $unconnect += $v['unconnect_num'];

                }

                foreach ($data as &$value) {
                    $value['total_pre'] = $total ? round($value['total_num']/$total, 4) * 100 : 0;
                    $value['connect_pre'] = $connect ? round($value['connect_num']/$connect, 4) * 100 : 0;
                    $value['unconnect_pre'] = $unconnect ? round($value['unconnect_num']/$unconnect, 4) * 100 : 0;
                    $value['total'] = $value['total_num'];
                    $value['connect'] = $value['connect_num'];
                    $value['unconnect'] = $value['unconnect_num'];
                    unset($value['total_num']);
                    unset($value['connect_num']);
                    unset($value['unconnect_num']);
                }

                //对数据进行处理
                $count = count($data);

                $result = [];
                foreach ($data as $valueData) {
                    $preKey = $dataType . '_pre';
                    $tmp = [
                        'province'=>$valueData['province'],
                        'num'=>$valueData[$dataType] ?? 0,
                        'pre'=>$valueData[$preKey] ?? 0,
                    ];
                    $result[] = $tmp;
                }

                //根据字段last_name对数组$result进行排序
                $last_names = array_column($result,$sortFiled);
                array_multisort($last_names,$order=='desc'?SORT_DESC:SORT_ASC,$result);

                $data = $result;

                $data = static::addRankingFiled($data);

                foreach ($data as &$v) {
                    $v['pre'] = round($v['pre'], 2) . '%';
                }

                $tipName = DialoutRecord::getNameByDataType($dataType);

                //判断是否有导出
                if ($is_export == 1) {
                    if (empty($data)) {
                        throw new InvalidParameterException('暂无数据，无法导出！');
                    }

                    $headers = [
                        'ranking' => '排行',
                        'province'=>'地区',
                        'num'=>$tipName,
                        'pre'=>'占比',
                    ];
                    \Yii::$app->work->push(new DialoutExportJob([
                        'exportData'  => $data,
                        'headers' => $headers,
                        'uid'     => empty($this->user->uid) ? $this->subUser->sub_id : $this->user->uid,
                        'corpId'  => $corpId,
                    ]));
                    return ['error' => 0];
                }
                $list = array_slice($data, ($page - 1) * $pageSize, $pageSize);
                $result = [
                    'count'=>$count,
                    'list'=>$list,
                ];
                return $result;
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }

        //当前结果
        public function actionCurrentResult()
        {
            if (\Yii::$app->request->isPost) {
                if (empty($this->corp)) {
                    throw new InvalidParameterException('参数不正确！');
                }
                $userIds   = \Yii::$app->request->post('staffList');

                $timeRange     = \Yii::$app->request->post('timeRange');
                $timeScope     = \Yii::$app->request->post('timeScope') ?: null;
                $edate     = \Yii::$app->request->post('edate');
                $sdate     = \Yii::$app->request->post('sdate');

                static::checkDate($sdate, $edate);

                $corpId = $this->corp['id'];

                $userIds = static::getVisibleUser($this->subUser->sub_id ?? 0, $this->corp->id ,$userIds);
                $data = DialoutRecord::getUserChartRegionData('', $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);

                $regionNum = count($data);
                $total = 0;
                $connect = 0;
                $unconnect = 0;
                $duration = 0;

                foreach ($data as $v) {
                    $total += $v['total_num'];
                    $connect += $v['connect_num'];
                    $unconnect += $v['unconnect_num'];
                    $duration += $v['duration'];
                }

                $avg_duration = $total ? round($duration/$total, 1) : 0;

                $duration = DateUtil::getHumanFormatBySecond($duration*60);

                $result = "$sdate~$edate 总呼出次数 $total 次；接通 $connect 次; 未接通 $unconnect 次; 总通话时长 $duration; 平均通话时长 $avg_duration 分钟; 呼叫地区 $regionNum 个";

                return ['data'=>$result];
            }else{
                throw new MethodNotAllowedHttpException('请求方式不允许！');
            }
        }


        //返回账号可见的员工id
        private static function getVisibleUser($subId, $corpId, $userIds)
        {
            if (empty($userIds)) {
                $userIds = [];
            }else{
                $Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($userIds);
                $userIds = WorkDepartment::GiveDepartmentReturnUserData($corpId, $Temp["department"], $Temp["user"], 0, true,0,[],$subId);
                $userIds = empty($userIds) ? [] : $userIds;
            }

            if ($subId) {
                $sub_detail = AuthoritySubUserDetail::getDepartmentUserLists($subId, $corpId);

                if ($sub_detail === false) {
                    return [-1];
                }

                if (is_array($sub_detail)) {
                    $userIds = empty($userIds) ? $sub_detail : array_merge(array_intersect($userIds, $sub_detail));
                }
            }

            return $userIds;
        }

        /*
         * 返回可见的部门和员工
         * */
        private static function getVisibleUsersAndDepartments($subId, $corpId, $departmentIds)
        {
            $userIds = [];
            if (empty($departmentIds)) {
                $departmentIds = [];
            }else{
                $Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($departmentIds);
                $departmentIds = $Temp['department'];
                $departmentIds = WorkDepartment::GiveDepartmentReturnChildren($departmentIds, $corpId);
                WorkDepartment::GiveDepartmentReturnUserArray($departmentIds, $corpId, $userIds,true, 0);
            }

            $visibleUserIds = [];
            $visibleDepartments = [];

            if ($subId) {
                [$subUser, $subDepartment, $all, $subDepartmentOld] = WorkDepartment::GiveSubIdReturnDepartmentOrUser($corpId, $subId);

                if ($all) {
                    $visibleUserIds = $subUser;
                    $visibleDepartments = $subDepartment;
                }
            }

            if (!empty($departmentIds)) {
                if (!empty($visibleDepartments)) {
                    $visibleDepartments = array_intersect($departmentIds, $visibleDepartments);
                }else{
                    $visibleDepartments = $departmentIds;
                }
            }

            if (!empty($userIds)) {
                if (!empty($visibleUserIds)) {
                    $visibleUserIds = array_intersect($visibleUserIds, $userIds);
                }else{
                    $visibleUserIds = $userIds;
                }
            }
            $visibleDepartments = array_merge($visibleDepartments);
            $visibleUserIds = array_merge($visibleUserIds);
            return [$visibleUserIds,$visibleDepartments];
        }


        //增加排行字段
        public static function addRankingFiled($arr, $start = 1)
        {
            $result = [];
            foreach ($arr as $v) {
                $v['ranking'] = $start++;
                $result[] = $v;
            }
            return $result;
        }

        /*
         * 判断开始日期和结束日期；
         * 0.开始日期和结束日期不能为空
         * 1.开始日期小于等于结束日期
         * 2.结束日期小于等于昨日日期
         * */
        private static function checkDate($sDate, $eDate)
        {
            if (empty($sDate) || empty($eDate)){
                throw new InvalidParameterException("查询日期不能为空");
            }
            if ($sDate > $eDate) {
                throw new InvalidParameterException("开始日期不能大于结束日期");
            }
        }
	}
