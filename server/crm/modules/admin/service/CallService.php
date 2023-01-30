<?php

namespace app\modules\admin\service;

use app\modules\admin\repositories\DialoutKeyRepository;
use app\util\HasHttpRequest;

class CallService
{
    use HasHttpRequest;

    public $baseUri;

    public function __construct()
    {
        $this->baseUri = \Yii::$app->params['dialout_url'];
    }

    /**
     * 获取当前选择的线路
     *
     * @return mixed
     */
    public function getCircuit()
    {
        return \Yii::$app->params['call']['circuit'];
    }

    /**
     * 获取驱动对应的KEY
     *
     * @param string $driver
     * @return string
     */
    public function getCircuitApiKey(string $circuit)
    {
        $repository = \Yii::createObject(DialoutKeyRepository::class);

        $dialoutKey = $repository->getDialoutKey($circuit);

        return $dialoutKey ? $dialoutKey->api_key : '';
    }

    /**
     * 获取线路信息
     *
     * @param string $apiKey
     */
    public function getCircuitInfo(string $apiKey)
    {
        return $this->get('index.php', ['r' => 'webcall/api/circuit-info', 'api_key' => $apiKey]);
    }

    /**
     * 提交服务商审核
     *
     * @param array $data
     * @return array
     */
    public function submitFacilitatorAudit(string $apiKey, array $data)
    {
        $data['api_key'] = $apiKey;
        $data['custom_key'] = $data['corp_id']; unset($data['corp_id']);

        foreach(
            [
                'business_license_url',
                'corporate_identity_card_positive_url',
                'corporate_identity_card_reverse_url',
                'operator_identity_card_positive_url',
                'operator_identity_card_reverse_url',
                'acknowledgement_url'
            ] as $key
        ){
            if(! empty($data[$key])){
                $data[$key] = \Yii::$app->request->hostInfo . '/' . ltrim($data[$key], '/');
            }
        }

        return $this->post('index.php', $data, ['query' => ['r' => 'webcall/api/material-audit']]);
    }

    /**
     * 获取线路消费信息
     *
     * @param string $apiKey
     * @param array $map
     * @return array|string
     */
    public function getCircuitDissipateInfo(string $apiKey, array $map = [])
    {
        if(isset($map['corp_id'])){
            $map['custom_key'] = $map['corp_id']; unset($map['corp_id']);
        }

        return $this->post('index.php', $map, ['query' => ['r' => 'webcall/api/circuit-dissipate-info', 'api_key' => $apiKey]]);
    }

    /**
     * 话费充值
     *
     * @param string $apiKey
     * @param int $custom_key
     * @param int $amount
     */
    public function rechargeTelephone(string $apiKey, int $custom_key, int $minute)
    {
        return $this->post('index.php', compact('custom_key', 'minute'), ['query' => ['r' => 'webcall/api/circuit-dissipate-info', 'api_key' => $apiKey]]);
    }
}