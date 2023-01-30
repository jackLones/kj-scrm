<?php

namespace app\modules\admin\controllers;

use app\models\DialoutConfig;
use app\models\DissipateLog;
use app\models\WorkCorp;
use app\modules\admin\service\CallService;
use app\util\ArrUtil;
use yii\data\Pagination;
use yii\web\Request;

class CallBalanceDetailController extends CallController
{
    public $pageSize = 20;

    /**
     * 平台明细
     *
     * @method GET
     * @param Request $request
     * @param CallService $service
     */
    public function actionIndex(Request $request, CallService $service)
    {
        $circuit = $service->getCircuit();

        $circuitApiKey = $service->getCircuitApiKey($circuit);

        if(empty($circuitApiKey)){
            return $this->redirect('/admin/call/set-circuit');
        }

        $map = [];
        $action = $request->get('action', '0');
        $keywords = $request->get('keywords', '');

        if($action != '0') $map['action'] = $action;

        if(!empty($keywords)){
            $dialoutConfigs = DialoutConfig::find()
                ->alias('dc')
                ->rightJoin('{{%work_corp}} wc', 'wc.id = dc.corp_id')
                ->rightJoin('{{%user}} u', 'u.uid = dc.uid')
                ->where(['u.account' => $keywords])
                ->orWhere(['wc.corp_name' => $keywords])
                ->select(['corp_id'])
                ->asArray()
                ->all();

            $map['corp_id'] = array_column($dialoutConfigs, 'corp_id');
        }

        if(isset($map['corp_id']) && empty($map['corp_id'])){
            $count = 0;
            $dissipateLogs = [];
        }else{
            $res = $service->getCircuitDissipateInfo($circuitApiKey, $map);

            if($res['code'] != '200'){
                $this->dexit(['error' => 1, 'msg' => $res['message']]);
            }

            $data = $res['message'];
            $count = $data['count'];
            $dissipateLogs = $data['data'];
        }

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);

        $res = $service->getCircuitInfo($circuitApiKey);

        if($res['code'] != '200'){
            $this->dexit(['error' => 1, 'msg' => $res['message']]);
        }

        $data = $res['message'];

        return $this->render('index', [
            'balance' => ArrUtil::get($data, 'balance', 0),
            'recharge_amount_sum' => ArrUtil::get($data, 'recharge_amount_sum', 0),
            'dissipate_amount_sum' => ArrUtil::get($data, 'dissipate_amount_sum', 0),
            'action' => $action,
            'keywords' => $keywords,
            'pages' => $pages,
            'dissipateLogs' => array_map(function($dissipateLog){
                return new DissipateLog($dissipateLog);
            }, $dissipateLogs)
        ]);
    }
}
