<?php

namespace app\models;

use app\util\DateUtil;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "pig_dialout_record".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $user_id 员工id
 * @property int $external_userid 客户id
 * @property int $exten 坐席id
 * @property string $call_no 也不知道是啥，看着像座机号
 * @property string $small_phone 小号
 * @property string $called_no 被叫号码
 * @property string $real_called 真实号码
 * @property string $call_sheet_id 通话记录ID
 * @property int $ring 通话振铃时间（话务进入呼叫中心系统的时间）
 * @property int $ringing 被叫振铃开始时间（呼入是按座席振铃的时间,外呼按客户振铃的时间）
 * @property int $begin 通话接通时间（双方开始通话的时间,如果被叫没接听的话为空）
 * @property int $end 通话结束时间
 * @property int $state 接听状态：1（dealing/已接）,2（notDeal/振铃未接听）,3（leak/ivr放弃）,4（queueLeak/排队放弃）,5（blackList/黑名单）,6（voicemail/留言）,7（limit/并发限制）
 * @property string $money 通话消耗费用
 * @property string $record_file 通话录音文件名：用户要访问录音时,在该文件名前面加上服务器路径即可,如：FileServer/RecordFile
 * @property string $file_server 通过FileServer中指定的地址加上RecordFile的值可以获取录音(建议字段长度内容设置为100个字符)
 * @property string $province 目标号码的省,例如北京市。
 * @property string $district 目标号码的市,例如北京市。
 * @property string $hangup_part 挂机方，字段值解释 ：agent 坐席挂机， customer 客户挂机，system 系统挂机
 * @property int $custom_type 0: 企微客户；1：非企客户；2：群客户
 * @property string $create_time
 */
class DialoutRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialout_record}}';
    }

    public static $c = [
        'total'=>['总拨打次数'],
        'connect'=>['接通数'],
        'unconnect'=>['未接通数'],
        'connect_prob'=>['接通率'],
        'duration'=>['计费通话时长'],
        'avg_duration'=>['平均计费通话时长'],
        'duration_turth'=>['实际通话时长'],
        'avg_duration_turth'=>['平均实际通话时长'],
    ];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'external_userid', 'exten', 'call_sheet_id', 'ring', 'ringing', 'begin', 'end', 'state', 'custom_type', 'create_time'], 'required'],
            [['corp_id', 'user_id', 'external_userid', 'exten', 'ring', 'ringing', 'begin', 'end', 'state', 'custom_type'], 'integer'],
            [['money'], 'number'],
            [['create_time'], 'safe'],
            [['call_no', 'small_phone', 'called_no', 'real_called', 'call_sheet_id', 'file_server', 'province', 'district', 'hangup_part'], 'string', 'max' => 255],
            [['record_file'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '授权的企业ID'),
            'user_id' => Yii::t('app', '员工id'),
            'external_userid' => Yii::t('app', '客户id'),
            'exten' => Yii::t('app', '坐席id'),
            'call_no' => Yii::t('app', '也不知道是啥，看着像座机号'),
            'small_phone' => Yii::t('app', '小号'),
            'called_no' => Yii::t('app', '被叫号码'),
            'real_called' => Yii::t('app', '真实号码'),
            'call_sheet_id' => Yii::t('app', '通话记录ID'),
            'ring' => Yii::t('app', '通话振铃时间（话务进入呼叫中心系统的时间）'),
            'ringing' => Yii::t('app', '被叫振铃开始时间（呼入是按座席振铃的时间,外呼按客户振铃的时间）'),
            'begin' => Yii::t('app', '通话接通时间（双方开始通话的时间,如果被叫没接听的话为空）'),
            'end' => Yii::t('app', '通话结束时间'),
            'state' => Yii::t('app', '接听状态：1（dealing/已接）,2（notDeal/振铃未接听）,3（leak/ivr放弃）,4（queueLeak/排队放弃）,5（blackList/黑名单）,6（voicemail/留言）,7（limit/并发限制）'),
            'money' => Yii::t('app', '通话消耗费用'),
            'record_file' => Yii::t('app', '通话录音文件名：用户要访问录音时,在该文件名前面加上服务器路径即可,如：FileServer/RecordFile'),
            'file_server' => Yii::t('app', '通过FileServer中指定的地址加上RecordFile的值可以获取录音(建议字段长度内容设置为100个字符)'),
            'province' => Yii::t('app', '目标号码的省,例如北京市。'),
            'district' => Yii::t('app', '目标号码的市,例如北京市。'),
            'hangup_part' => Yii::t('app', '挂机方，字段值解释 ：agent 坐席挂机， customer 客户挂机，system 系统挂机'),
            'custom_type' => Yii::t('app', '0: 企微客户；1：非企客户；2：群客户'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '授权的企业ID'),
            'user_id' => Yii::t('app', '员工id'),
            'external_userid' => Yii::t('app', '客户id'),
            'exten' => Yii::t('app', '坐席id'),
            'small_phone' => Yii::t('app', '小号'),
            'called_no' => Yii::t('app', '被叫号码'),
            'call_sheet_id' => Yii::t('app', '通话记录ID'),
            'ring' => Yii::t('app', '通话振铃时间（话务进入呼叫中心系统的时间）'),
            'ringing' => Yii::t('app', '被叫振铃开始时间（呼入是按座席振铃的时间,外呼按客户振铃的时间）'),
            'begin' => Yii::t('app', '通话接通时间（双方开始通话的时间,如果被叫没接听的话为空）'),
            'end' => Yii::t('app', '通话结束时间'),
            'state' => Yii::t('app', '接听状态：1（dealing/已接）,2（notDeal/振铃未接听）,3（leak/ivr放弃）,4（queueLeak/排队放弃）,5（blackList/黑名单）,6（voicemail/留言）,7（limit/并发限制）'),
            'money' => Yii::t('app', '通话消耗费用'),
            'record_file' => Yii::t('app', '通话录音文件名：用户要访问录音时,在该文件名前面加上服务器路径即可,如：FileServer/RecordFile'),
            'file_server' => Yii::t('app', '通过FileServer中指定的地址加上RecordFile的值可以获取录音(建议字段长度内容设置为100个字符)'),
            'province' => Yii::t('app', '目标号码的省,例如北京市。'),
            'district' => Yii::t('app', '目标号码的市,例如北京市。'),
            'hangup_part' => Yii::t('app', '挂机方，字段值解释 ：agent 坐席挂机， customer 客户挂机，system 系统挂机'),
            'custom_type' => Yii::t('app', '0: 企微客户；1：非企客户；2：群客户'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }

    //昨日数据统计
    public static function getYesterday($corpId, $userIds)
    {

        $C = 86399;
        $yesTime = strtotime('yesterday');
        $lstTime = $yesTime-86400;

        $select = [
            'IFNULL(sum(IF(begin>0, ceil((end-begin)/60) ,0)),0) duration',
            'IFNULL(sum(IF(begin>0, (end-begin) ,0)),0) duration_turth',
            'count(*) total_num',
            'count(IF(state=1, 1, null)) connect_num',
            'count(IF(state!=1, 1, null)) unconnect_num',
            'IFNULL(ceil(avg(IF(begin>0, ceil((end-begin)/60), null))),0) avg_duration',
            'IFNULL(ceil(avg(IF(begin>0, (end-begin), null))),0) avg_duration_turth',
        ];
        $yesData = static::find()
            ->select($select)
            ->where(['corp_id'=>$corpId])
            ->andFilterWhere(['in', 'user_id', $userIds])
            ->andFilterWhere(['and',['>=','ring',$yesTime],['<=', 'ring', $yesTime+$C]])
            ->asArray()
            ->all();
        $lstData = static::find()
            ->select($select)
            ->where(['corp_id'=>$corpId])
            ->andFilterWhere(['in', 'user_id', $userIds])
            ->andFilterWhere(['and',['>=','ring',$lstTime],['<=', 'ring', $lstTime+$C]])
            ->asArray()
            ->all();

        $totalData = static::find()
            ->select($select)
            ->where(['corp_id'=>$corpId])
            ->andFilterWhere(['in', 'user_id', $userIds])
            ->asArray()
            ->all();

        $result['total'] = static::calculateTrend($yesData[0]['total_num'], $lstData[0]['total_num']);
        $result['connect'] = static::calculateTrend($yesData[0]['connect_num'], $lstData[0]['connect_num']);
        $result['unconnect'] = static::calculateTrend($yesData[0]['unconnect_num'], $lstData[0]['unconnect_num']);
        $result['connect_prob'] = static::calculateTrend($yesData[0]['total_num'] ? round( $yesData[0]['connect_num']/$yesData[0]['total_num'] ,2) : 0, $lstData[0]['total_num'] ? round($lstData[0]['total_num']/$lstData[0]['total_num'],2) : 0);
        $result['duration'] = static::calculateTrend($yesData[0]['duration'], $lstData[0]['duration']);
        $result['avg_duration'] = static::calculateTrend($yesData[0]['avg_duration'], $lstData[0]['avg_duration']);
        $result['duration_turth'] = static::calculateTrend($yesData[0]['duration_turth'], $lstData[0]['duration_turth']);
        $result['avg_duration_turth'] = static::calculateTrend($yesData[0]['avg_duration_turth'], $lstData[0]['avg_duration_turth']);

        //对count重新赋值
        $result['connect_prob']['count'] = $result['connect_prob']['count'] * 100 . '%';
        $result['duration']['count'] = $result['duration']['count'] ? DateUtil::getHumanFormatBySecond($result['duration']['count']*60) : "";
        $result['avg_duration']['count'] = $result['avg_duration']['count'] ? DateUtil::getHumanFormatBySecond($result['avg_duration']['count']*60) : "";
        $result['duration_turth']['count'] = $result['duration_turth']['count'] ? DateUtil::getHumanFormatBySecond($result['duration_turth']['count']) : "";
        $result['avg_duration_turth']['count'] = $result['avg_duration_turth']['count'] ? DateUtil::getHumanFormatBySecond($result['avg_duration_turth']['count']) : "";

        $totalData = $totalData[0];
        //底部总数
        $result['total']['text'] = '累计' . $totalData['total_num'];
        $result['connect']['text'] = '累计' . $totalData['connect_num'];
        $result['unconnect']['text'] = '累计' . $totalData['unconnect_num'];
        $result['connect_prob']['text'] = '总接通率' . ($totalData['total_num'] ? round($totalData['connect_num']/$totalData['total_num'],2) : 0) *100 . '%';
        $result['duration']['text'] = '累计' . DateUtil::getHumanFormatBySecond($totalData['duration'] * 60);
        $result['avg_duration']['text'] = '总平均通话时长' . DateUtil::getHumanFormatBySecond($totalData['avg_duration'] * 60);
        $result['duration_turth']['text'] = '累计' . DateUtil::getHumanFormatBySecond($totalData['duration_turth']);
        $result['avg_duration_turth']['text'] = '总平均通话时长' . DateUtil::getHumanFormatBySecond($totalData['avg_duration_turth']);

        return $result;
    }

    //获取员工排行榜数据
    public static function getUserChartTopData($dataType,$corpId, $userIds, $sdate, $edate, $timeRange,$timeScope, $page = 1,$pageSize = 15,$needCount = false, $order = 'desc')
    {
        $departmentQuery = WorkUser::find()
            ->alias('a')
            ->select(['a.id',"group_concat(b.name separator '/') department"])
            ->leftJoin(WorkDepartment::tableName() . ' b', 'FIND_IN_SET(b.department_id,a.department)')
            ->where(['a.corp_id'=>$corpId,'b.corp_id'=>$corpId])
            ->addGroupBy('a.id');

        $bindQuery = DialoutBindWorkUser::find()->select(['user_id'])->where(['corp_id'=>$corpId])->addGroupBy('user_id');
        $recordQuery = DialoutRecord::getSubQueryGroupByUser($corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);
        $sort = $order=='desc' ? SORT_DESC : SORT_ASC;

        $select = [
            'IFNULL(rq.total_num,0) total',
            'IFNULL(rq.connect_num,0) connect',
            'IFNULL(rq.unconnect_num,0) unconnect',
            'IF(rq.total_num>0, round(rq.connect_num/rq.total_num, 2), 0) connect_prob',
            'IFNULL(rq.duration,0) duration',
            'IFNULL(rq.seconds,0) duration_turth',
            'IF(rq.connect_num>0, round(rq.duration/rq.connect_num, 2), 0) avg_duration',
            'IF(rq.connect_num>0, ceil(rq.seconds/rq.connect_num), 0) avg_duration_turth',
            'c.name',
            'd.department',
            'c.id id'
        ];
        $order = [$dataType=>$sort];
        $query = (new Query())
            ->select($select)
            ->from(['a'=>$bindQuery])
            ->innerJoin(['rq'=>$recordQuery], 'a.user_id=rq.user_id')
            ->innerJoin(['d'=>$departmentQuery], 'a.user_id=d.id')
            ->innerJoin(WorkUser::tableName() . ' c', 'a.user_id=c.id')
            ->addOrderBy($order);


        if ($needCount) {
            $conneQuery = clone $query;
            $count = $conneQuery->count();
        }else{
            $count = 0;
        }

        $list = $query->limit($pageSize)->offset(($page-1)*$pageSize)->all();

        return [
            'count'=>$count,
            'list'=>$list
        ];

    }

    //获取部门排行榜数据
    public static function getDepartmentChartTopData($dataType,$corpId, $userIds,$departmentIds, $sdate, $edate, $timeRange,$timeScope, $page = 1,$pageSize = 15,$needCount = false, $order = 'desc')
    {
        $bindQuery = DialoutBindWorkUser::find()->select(['user_id'])->where(['corp_id'=>$corpId])->addGroupBy('user_id');
        $recordQuery = DialoutRecord::getSubQueryGroupByUser($corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);

        $sort = $order=='desc' ? SORT_DESC : SORT_ASC;

        $select = [
            'IFNULL(sum(rq.total_num),0) total',
            'IFNULL(sum(rq.connect_num),0) connect',
            'IFNULL(sum(rq.unconnect_num),0) unconnect',
            'IFNULL(IF(sum(rq.total_num)>0, round(sum(rq.connect_num)/sum(rq.total_num), 2), 0),0) connect_prob',
            'IFNULL(sum(rq.duration),0) duration',
            'IFNULL(rq.seconds,0) duration_turth',
            'IFNULL(IF(sum(rq.connect_num)>0, round(sum(rq.duration)/sum(rq.connect_num), 2), 0),0) avg_duration',
            'IF(rq.connect_num>0, ceil(rq.seconds/rq.connect_num), 0) avg_duration_turth',
            'wdap.parent_id',
        ];
        $queryMid = (new Query())
            ->select($select)
            ->from(['a'=>$bindQuery])
            ->innerJoin(['rq'=>$recordQuery], 'a.user_id=rq.user_id')
            ->innerJoin(WorkUser::tableName() . ' wu', 'a.user_id=wu.id')
            ->innerJoin('{{%work_department_all_parent}} wdap', 'FIND_IN_SET(wdap.department_id, wu.department)')
            ->where(['wu.corp_id'=>$corpId])
            ->andWhere(['wdap.corp_id'=>$corpId])
            ->andWhere(['<>', 'wdap.parent_id', 1])
            ->andFilterWhere(['in', 'wdap.parent_id', $departmentIds])
            ->andFilterWhere(['in', 'wu.id', $userIds])
            ->addGroupBy('wdap.parent_id');

        $query = WorkDepartment::find()
            ->select(['wd.name','a.*','concat("d-", wd.department_id) id'])
            ->alias('wd')
            ->innerJoin(['a'=>$queryMid], 'a.parent_id=wd.department_id')
            ->where(['wd.corp_id'=>$corpId]);

        if ($needCount) {
            $conneQuery = clone $query;
            $count = $conneQuery->count();
        }else{
            $count = 0;
        }

        $list = $query->addOrderBy(['a.'.$dataType=>$sort,'wd.department_id'=>SORT_ASC])
            ->limit($pageSize)->offset(($page-1)*$pageSize)->asArray()->all();

        return [
            'count'=>$count,
            'list'=>$list
        ];

    }

    //chart 趋势图，员工
    public static function getUserChartTrendData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope,$timeType)
    {
        $recordQuery = DialoutRecord::getSubQueryGroupByDate($corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);

        $data = $recordQuery->all();

        $dataTmp = [];

        foreach ($data as $v) {
            $dataTmp[$v['date']] = $v;
        }

        $data = $dataTmp;

        $result = [];

        $defaultData = [
            'total' =>0,
            'connect'=>0,
            'unconnect'=>0,
            'duration'=>0,
            'duration_turth'=>0,
        ];

        $i = 1;
        switch ($timeType) {
            case 0:
                $dates = DateUtil::getDateFromRange($sdate, $edate);
                foreach ($dates as $v) {
                    $tmp = $defaultData;
                    if (isset($data[$v])) {
                        $tmp['total'] = $data[$v]['total_num'];
                        $tmp['connect'] = $data[$v]['connect_num'];
                        $tmp['unconnect'] = $data[$v]['unconnect_num'];
                        $tmp['duration'] = $data[$v]['duration'];
                        $tmp['duration_turth'] = $data[$v]['seconds'];
                    }
                    $tmp['sdate'] = $v;
                    $tmp['edate'] = $v;
                    $tmp['key'] = $tmp['sdate'];
                    $result[] = $tmp;
                }
                break;
            case 1:
                $dates = static::printWeeks($sdate, $edate);
                foreach ($dates as $date) {
                    $tmp = $defaultData;
                    foreach ($date as $v) {
                        if (isset($data[$v])) {
                            $tmp['total'] += $data[$v]['total_num'];
                            $tmp['connect'] += $data[$v]['connect_num'];
                            $tmp['unconnect'] += $data[$v]['unconnect_num'];
                            $tmp['duration'] += $data[$v]['duration'];
                            $tmp['duration_turth'] = $data[$v]['seconds'];
                        }
                    }
                    $tmp['sdate'] = $date[0];
                    $tmp['edate'] = $date[count($date)-1];
                    $tmp['key'] = $tmp['sdate'] . "~" . $tmp['edate'] . "(第" . $i++ . "周)";
                    $result[] = $tmp;
                }
                break;
            case 2:
                $dates = static::printMonths($sdate, $edate);
                foreach ($dates as $date) {
                    $tmp = $defaultData;
                    foreach ($date as $v) {
                        if (isset($data[$v])) {
                            $tmp['total'] += $data[$v]['total_num'];
                            $tmp['connect'] += $data[$v]['connect_num'];
                            $tmp['unconnect'] += $data[$v]['unconnect_num'];
                            $tmp['duration'] += $data[$v]['duration'];
                            $tmp['duration_turth'] = $data[$v]['seconds'];
                        }
                    }
                    $tmp['sdate'] = $date[0];
                    $tmp['edate'] = $date[count($date)-1];
                    $tmp['key'] = $tmp['sdate'] . "~" . $tmp['edate'] . "(第" . $i++ . "月)";
                    $result[] = $tmp;
                }
                break;
        }

        foreach ($result as $key=>$value){
            $value['connect_prob'] = $value['total'] ? round($value['connect']/$value['total'], 2) : 0;
            $value['avg_duration'] = $value['connect'] ? round($value['duration']/$value['connect'], 2) : 0;
            $value['avg_duration_turth'] = $value['connect'] ? ceil($value['duration_turth']/$value['connect']) : 0;
            $result[$key] = $value;
        }

        return $result;

    }

    //chart 地区分布
    public static function getUserChartRegionData($dataType, $corpId,$userIds,$sdate,$edate,$timeRange,$timeScope)
    {
        $recordQuery = DialoutRecord::getSubQueryGroupByRegion($corpId,$userIds,$sdate,$edate,$timeRange,$timeScope);

        $data = $recordQuery->all();

        return $data;
    }


    /*
         * 计算增长数据
         * */
    private static function calculateTrend($data1 = 0,$data2 = 0)
    {
        $arr = [];

        $arr['count'] = $data1;

        if ($data1 > $data2) {
            $arr['status'] = 1; //上升
        } elseif($data1 == $data2) {
            $arr['status'] = 0; //持平
        }else{
            $arr['status'] = -1; //下降
        }

        if ($data2 > 0) {
            $num = round(abs($data1 - $data2) / $data2, 3);
        } else {
            $num = $data1;
        }
        $num            = sprintf("%.1f", $num * 100);

        $arr['per']   = $num . '%';
        return $arr;
    }

    //得到子查询，根据员工分组
    public static function getSubQueryGroupByUser($corpId, $userIds, $sdate, $edate, $timeRange,$timeScope)
    {
        $sdate = $sdate ? strtotime($sdate) : null;
        $edate = $edate ? strtotime($edate . ' 23:59:59') : null;

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

        $subQuery = DialoutRecord::find()
            ->select([
                '*',
                'from_unixtime(ring,"%k") ring_hour',
                'IF(begin>0, end-begin, 0) seconds',
            ])
            ->where(['corp_id'=>$corpId])
            ->andFilterWhere(['and',
                ['in', 'user_id', $userIds],
                ['>=','ring', $sdate],
                ['<=', 'ring', $edate],
            ]);

        $query = (new Query())
            ->select([
                'a.user_id',
                'IFNULL(sum(ceil(a.seconds/60)),0) duration',
                'IFNULL(sum(a.seconds),0) seconds',
                'count(*) total_num',
                'count(IF(a.state=1, 1, null)) connect_num',
                'count(IF(a.state!=1, 1, null)) unconnect_num',
            ])->from(['a'=>$subQuery])
            ->andFilterWhere(['and',
                ['>=','a.ring_hour', $startHour],
                ['<=', 'a.ring_hour', $endHour],
                ['>=', 'a.seconds', $minSecond],
                ['<=', 'a.seconds', $maxSecond]
            ])->addGroupBy(['a.user_id']);
        return $query;
    }

    //得到子查询，根据日期分组
    public static function getSubQueryGroupByDate($corpId, $userIds, $sdate, $edate, $timeRange,$timeScope)
    {
        $sdate = $sdate ? strtotime($sdate) : null;
        $edate = $edate ? strtotime($edate . ' 23:59:59') : null;

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

        $subQuery = DialoutRecord::find()
            ->select([
                '*',
                'from_unixtime(ring,"%k") ring_hour',
                'from_unixtime(ring,"%Y-%m-%d") ring_date',
                'IF(begin>0, end-begin, 0) seconds',
            ])
            ->where(['corp_id'=>$corpId])
            ->andFilterWhere(['and',
                ['in', 'user_id', $userIds],
                ['>=','ring', $sdate],
                ['<=', 'ring', $edate],
            ]);

        $query = (new Query())
            ->select([
                'a.ring_date date',
                'IFNULL(sum(ceil(a.seconds/60)),0) duration',
                'IFNULL(sum(a.seconds),0) seconds',
                'count(*) total_num',
                'count(IF(a.state=1, 1, null)) connect_num',
                'count(IF(a.state!=1, 1, null)) unconnect_num',
            ])->from(['a'=>$subQuery])
            ->andFilterWhere(['and',
                ['>=','a.ring_hour', $startHour],
                ['<=', 'a.ring_hour', $endHour],
                ['>=', 'a.seconds', $minSecond],
                ['<=', 'a.seconds', $maxSecond]
            ])->addGroupBy(['a.ring_date']);
        return $query;
    }

    //得到子查询，根据地区分组
    public static function getSubQueryGroupByRegion($corpId, $userIds, $sdate, $edate, $timeRange,$timeScope)
    {
        $sdate = $sdate ? strtotime($sdate) : null;
        $edate = $edate ? strtotime($edate . ' 23:59:59') : null;

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

        $subQuery = DialoutRecord::find()
            ->select([
                '*',
                'from_unixtime(ring,"%k") ring_hour',
                'IF(begin>0, end-begin, 0) seconds',
            ])
            ->where(['corp_id'=>$corpId])
            ->andFilterWhere(['and',
                ['in', 'user_id', $userIds],
                ['>=','ring', $sdate],
                ['<=', 'ring', $edate],
            ]);

        $query = (new Query())
            ->select([
                'a.province province',
                'IFNULL(sum(IF(a.begin>0, ceil((a.end-a.begin)/60) ,0)),0) duration',
                'count(*) total_num',
                'count(IF(a.state=1, 1, null)) connect_num',
                'count(IF(a.state!=1, 1, null)) unconnect_num',
            ])->from(['a'=>$subQuery])
            ->andFilterWhere(['and',
                ['>=','a.ring_hour', $startHour],
                ['<=', 'a.ring_hour', $endHour],
                ['>=', 'a.seconds', $minSecond],
                ['<=', 'a.seconds', $maxSecond]
            ])->addGroupBy(['a.province']);
        return $query;
    }

    public static function getNameByDataType($dataType){
        $arr = static::$c;
        return $arr[$dataType][0] ?? '';
    }

    //两个日期之间的所有周
    public static function printWeeks($start, $end)
    {
        $start = strtotime($start);
        $end = strtotime($end);

        $return = [];
        $tmp = [];
        while($start <= $end) {
            $w = date("N", $start);

            if ($w == "1") {
                if (!empty($tmp)) {
                    $key = date("o-W", strtotime($tmp[0]));
                    $return[$key] = $tmp;
                }
                $tmp = [];
            }
            $tmp[] = date("Y-m-d", $start);

            $start += 60*60*24;
        }
        if (!empty($tmp)) {
            $key = date("o-W", strtotime($tmp[0]));
            $return[$key] = $tmp;
        }
        return $return;
    }

    //两个日期之间的所有月
    public static function printMonths($start, $end)
    {
        $start = strtotime($start);
        $end = strtotime($end);

        $return = [];
        $tmp = [];
        while($start <= $end) {
            $d = date("d", $start);

            if ($d == "01") {
                if (!empty($tmp)) {
                    $key = date("Y-m", strtotime($tmp[0]));
                    $return[$key] = $tmp;
                }
                $tmp = [];
            }
            $tmp[] = date("Y-m-d", $start);

            $start += 60*60*24;
        }
        if (!empty($tmp)) {
            $key = date("Y-m", strtotime($tmp[0]));
            $return[$key] = $tmp;
        }
        return $return;

    }

}
