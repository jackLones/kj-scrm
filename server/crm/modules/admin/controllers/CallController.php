<?php

namespace app\modules\admin\controllers;

use app\components\InvalidDataException;
use app\components\RouteControllerAction;
use app\models\DialoutConfig;
use app\models\DialoutKey;
use app\modules\admin\components\BaseController;
use app\modules\admin\repositories\DialoutKeyRepository;
use app\modules\admin\service\CallService;
use app\util\ArrUtil;
use app\util\SUtils;
use Prophecy\Call\Call;
use yii\helpers\ArrayHelper;
use yii\web\Request;

class CallController extends BaseController
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return array_merge(
            RouteControllerAction::getActions($this),
            parent::actions()
        );
    }

    /**
     * 设置线路(显示)
     *
     * @method GET
     * @param CallService $service
     */
    public function getActionSetCircuit(CallService $service)
    {
        $callCircuit = $service->getCircuit();
        $callCircuitApiKey = $service->getCircuitApiKey($callCircuit);

        return $this->render('setCircuit', [
            'api_key' => $callCircuitApiKey
        ]);
    }

    /**
     * 设置线路KEY
     *
     * @method POST
     * @param CallService $service
     * @param DialoutKeyRepository $repository
     */
    public function postActionSetCircuit(CallService $service, DialoutKeyRepository $repository)
    {
        $model = new DialoutKey();

        $model->load($data = \Yii::$app->request->post(), '');

        if(! $model->validate()){
            $this->dexit(['error' => 1, 'msg' => SUtils::modelError($model)]);
        }

        $res = $service->getCircuitInfo($data['api_key']);

        if($res['code'] != '200'){
            $this->dexit(['error' => 1, 'msg' => $res['message']]);
        }

        $dialoutKey = $repository->getDialoutKey($data['api_type']);

        if($dialoutKey){
            $result = $dialoutKey->save([
                'api_key' => $data['api_key']
            ]);
        }else{
            $result = $model->save();
        }

        if(! $result){
            $this->dexit(['error' => 1, 'msg' => '保存失败']);
        }

        $this->dexit(['error' => 0, 'msg' => '保存成功']);
    }

    /**
     * 提交开通外呼申请
     *
     * @method POST
     * @param Request $request
     * @param CallService $service
     */
    public function actionDialoutConfig(Request $request, CallService $service)
    {
        $data = $request->post();

        if(empty($data['corp_id'])){
            $this->dexit(['error' => 1, 'msg' => '请选择企业微信']);
        }

        $dialoutConfig = DialoutConfig::find()->where(['corp_id' => $data['corp_id']])->one();

        if(! empty($dialoutConfig)){
            $this->dexit(['error' => 1, 'msg' => '这个企业微信已经提交过开通审核, 您可以在资料审核中查看']);
        }

        $callCircuit = $service->getCircuit();
        $callCircuitApiKey = $service->getCircuitApiKey($callCircuit);

        if(empty($callCircuitApiKey)){
            $this->dexit(['error' => 1, 'msg' => '请您先填写对接KEY']);
        }

        $adminConfigController = new AdminConfigController('admin-config', $this->module);

        $message = [
            'business_license_url' => '请上传营业执照',
            'corporate_identity_card_positive_url' => '请上传法人身份证正反面',
            'corporate_identity_card_reverse_url' => '请上传法人身份证正反面',
        ];

        foreach(array_keys($message) as $key){
            if(empty($_FILES[$key])){
                $this->dexit(['error' => 1, 'msg' => $message[$key]]);
            }
        }

        $files = array_merge(array_keys($message), ['operator_identity_card_positive_url', 'operator_identity_card_reverse_url']);
        foreach($files as $key){
            if(! empty($_FILES[$key])){
                $result = $adminConfigController->localUpload($_FILES[$key]);
                if($result['error'] == 1) $this->dexit($result);
                $data[$key] = $result['data'];
            }
        }

        $model = new DialoutConfig();
        $model->load($data, '');

        if(! $model->validate()){
            $this->dexit(['error' => 1, 'msg' => SUtils::modelError($model)]);
        }

        $transaction = DialoutConfig::getDb()->beginTransaction();

        try {
            //保存数据库
            $result = $model->save();

            if(! $result) throw new InvalidDataException(SUtils::modelError($model));

            //提交给服务商审核
            $res = $service->submitFacilitatorAudit($callCircuitApiKey, $data);
            if($res['code'] != '200') throw new InvalidDataException($res['message']);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->dexit(['error' => 1, 'msg' => $e->getMessage()]);
        }

        $this->dexit(['error' => 0, 'msg' => '提交成功']);
    }

    /**
     * 重新提交开通外呼申请
     *
     * @param Request $request
     * @param CallService $service
     */
    public function actionResubmitDialoutConfig(Request $request, CallService $service)
    {
        $data = $request->post();

        if(empty($data['corp_id'])){
            $this->dexit(['error' => 1, 'msg' => '请选择企业微信']);
        }

        $dialoutConfig = DialoutConfig::find()->where(['corp_id' => $data['corp_id']])->one();

        if(empty($dialoutConfig)){
            $this->dexit(['error' => 1, 'msg' => '这个企业微信还没有提交过开通审核']);
        }

        $callCircuit = $service->getCircuit();
        $callCircuitApiKey = $service->getCircuitApiKey($callCircuit);

        if(empty($callCircuitApiKey)){
            $this->dexit(['error' => 1, 'msg' => '请您先填写对接KEY']);
        }

        $adminConfigController = new AdminConfigController('admin-config', $this->module);

        foreach([
            'business_license_url',
            'corporate_identity_card_positive_url',
            'corporate_identity_card_reverse_url',
            'operator_identity_card_positive_url',
            'operator_identity_card_reverse_url',
            'acknowledgement_url'
        ] as $key){
            if(! empty($_FILES[$key])){
                $result = $adminConfigController->localUpload($_FILES[$key]);
                if($result['error'] == 1) $this->dexit($result);
                $data[$key] = $result['data'];
            }else{
                $data[$key] = $dialoutConfig->{$key};
            }
        }

        $data = ArrUtil::except($data, ['uid']);

        foreach($data as $key => $value){
            if($dialoutConfig->hasProperty($key)) {
                $dialoutConfig->{$key} = $value;
            }
        }

        $dialoutConfig->status = DialoutConfig::STATUS_AUDIT;
        $dialoutConfig->refuse_reason = '';

        $transaction = DialoutConfig::getDb()->beginTransaction();

        try {
            //保存数据库
            $result = $dialoutConfig->save();

            if(! $result) throw new InvalidDataException(SUtils::modelError($dialoutConfig));

            //提交给服务商审核
            $res = $service->submitFacilitatorAudit($callCircuitApiKey, $data);

            if($res['code'] != '200') throw new InvalidDataException($res['message']);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->dexit(['error' => 1, 'msg' => $e->getMessage()]);
        }

        $this->dexit(['error' => 0, 'msg' => '提交成功']);
    }

    /**
     * 修改外呼设置
     *
     * @param Request $request
     */
    public function actionDialoutConfigUpdate(Request $request)
    {
        $data = ArrUtil::only($request->post(), ['id', 'exten_money', 'phone_money']);

        if(! ArrUtil::has($data, 'id')){
            throw new InvalidDataException('ID cannot be blank.');
        }

        $dialoutConfig = DialoutConfig::find()->where(['id' => $data['id']])->one();

        if(empty($dialoutConfig)){
            $this->dexit(['error' => 1, 'msg' => '请先开通外呼']);
        }

        $dialoutConfig->attributes = ArrUtil::except($data, ['id']);

        if(! $dialoutConfig->save()){
            $this->dexit(['error' => 1, 'msg' => SUtils::modelError($dialoutConfig)]);
        }

        $this->dexit(['error' => 0, 'msg' => '修改成功']);
    }
}
