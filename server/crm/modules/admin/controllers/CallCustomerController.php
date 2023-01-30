<?php

namespace app\modules\admin\controllers;

use app\components\InvalidDataException;
use app\models\DialoutAgent;
use app\models\DialoutConfig;
use app\models\DialoutOrder;
use app\models\DialoutRecord;
use app\modules\admin\service\CallService;
use app\util\ArrUtil;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\Request;

class CallCustomerController extends CallController
{
    public $pageSize =  20;

    public function getRawSql(ActiveQuery $query)
    {
        return $query->createCommand()->getRawSql();
    }
    /**
     * 客户列表
     *
     * @method GET
     * @param  Request $request
     */
    public function actionIndex(Request $request)
    {
        $keywords = $request->get('keywords', '');

        $agentQuery = DialoutOrder::find()->alias('do')
            ->where(new Expression('do.corp_id=dc.corp_id'));

        //账户累计消耗
        $query = clone $agentQuery;
        $expendMoneyQuery = $query
            ->andWhere(['status' => 1, 'type' => [2, 4, 5, 7]])
            ->select(['sum(do.money)']);

        //坐席query
        $dialoutAgentQuery = DialoutAgent::find()->alias('da')
            ->where(new Expression('da.corp_id=dc.corp_id'));

        //已用坐席
        $query = clone $dialoutAgentQuery;
        $usedAgentQuery = $query
            ->andWhere(['da.enable' => 1, 'da.status' => 1])
            ->andWhere(['not', ['da.last_use_user' => null]])
            ->select(['count(*) as agent_num']);

        //开通坐席
        $query = clone $dialoutAgentQuery;
        $openAgentQuery = $query
            ->andWhere(['da.enable' => 1, 'da.status' => 1])
            ->select(['count(*) as agent_num']);

        //坐席消耗
        $query = clone $agentQuery;
        $agentExpendMoneyQuery = $query
            ->andWhere(['status' => 1, 'type' => [4, 5, 7]])
            ->select(['sum(do.money)']);

        //通话时长
        $callDurationQuery = DialoutRecord::find()->alias('dr')
            ->where(new Expression('dr.corp_id=dc.corp_id'))
            ->andWhere(['dr.state' => 1])
            ->andWhere(['>', 'dr.begin', 0 ])
            ->select(['sum(dr.end - dr.begin)']);

        //话费消耗
        $query = clone $agentQuery;
        $callExpendMoneyQuery = $query
            ->andWhere(['status' => 1, 'type' => 2])
            ->select(['sum(do.money)']);

        $query = DialoutConfig::find()->alias('dc')
            ->select([
                'dc.*',
                sprintf('(%s) as expend_money', $this->getRawSql($expendMoneyQuery)),
                sprintf('(%s) as open_agent_num', $this->getRawSql($openAgentQuery)),
                sprintf('(%s) as used_agent_num', $this->getRawSql($usedAgentQuery)),
                sprintf('(%s) as agent_expend_money', $this->getRawSql($agentExpendMoneyQuery)),
                sprintf('(%s) as call_duration_num', $this->getRawSql($callDurationQuery)),
                sprintf('(%s) as call_expend_money', $this->getRawSql($callExpendMoneyQuery)),
                'u.account',
                'wc.corp_name',
                'wc.corp_full_name',
            ])
            ->leftJoin('{{%work_corp}} wc', 'wc.id = dc.corp_id')
            ->leftJoin('{{%user}} u', 'u.uid = dc.uid')
            ->where(['dc.status' => DialoutConfig::STATUS_SUCCESS])
            ->orderBy('dc.id DESC');

        if(!empty($keywords)){
            $query->where(['u.account' => $keywords])
                ->orWhere(['wc.corp_name' => $keywords]);
        }

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
        $customers = $query->asArray()->all();

        return $this->render('index', [
            'keywords' => $keywords,
            'pages' => $pages,
            'customers' => $customers
        ]);
    }

    /**
     * 话费充值
     *
     * @param Request $request
     * @param CallService $service
     */
    public function actionRechargeTelephone(Request $request, CallService $service)
    {
        $keys = ['id', 'minute'];

        $data = ArrUtil::only($request->post(), $keys);

        foreach($keys as $key){
            if(! ArrUtil::has($data, $key)){
                throw new InvalidDataException($key . ' cannot be blank.');
            }
        }

        $dialoutConfig = DialoutConfig::find()->where(['id' => $data['id']])->one();

        if(empty($dialoutConfig)){
            $this->dexit(['error' => 1, 'msg' => '请先开通外呼']);
        }

        $callCircuit = $service->getCircuit();
        $callCircuitApiKey = $service->getCircuitApiKey($callCircuit);

        if(empty($callCircuitApiKey)){
            $this->dexit(['error' => 1, 'msg' => '请您先填写对接KEY']);
        }

        $res = $service->rechargeTelephone($callCircuitApiKey, $dialoutConfig->corp_id, $data['minute']);

        if($res['code'] != '200'){
            $this->dexit(['error' => 1, 'msg' => $res['message']]);
        }


    }
}
