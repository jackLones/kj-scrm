<?php


namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\models\ShopCustLevelSet;
use app\models\ShopCustomer;
use app\models\ShopCustomerLevelLog;
use app\models\ShopCustomerRfmAlias;
use app\models\ShopCustomerRfmDefault;
use app\models\ShopCustomerRfmSetting;
use app\models\WorkMsgAudit;
use app\modules\api\components\WorkBaseController;
use app\util\SUtils;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ShopCustomerRfmController extends WorkBaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'           => ['POST'],
                    'add-alias'      => ['POST'],
                    'change-setting' => ['POST'],
                ],
            ],
        ]);
    }

    /*
     * 等级列表接口
     * @url  http://{host_name}/api/shop-customer-rfm/list
     * */
    public function actionList()
    {
        $result = [];
        //会话存档
        $workAudit            = WorkMsgAudit::findOne(['corp_id' => $this->corp->id, 'status' => 1]);
        $result['work_audit'] = !empty($workAudit) ? 1 : 0;
        //rfm相关设置
        $rfmSetting            = ShopCustomerRfmSetting::getData($this->corp->id);
        $result['rfm_setting'] = $rfmSetting;
        //当前参考值
        $consumptionDataOpen = isset($rfmSetting['consumption_data_open']) ? $rfmSetting['consumption_data_open'] : 0;
        $msgAuditOpen        = isset($rfmSetting['msg_audit_open']) ? $rfmSetting['msg_audit_open'] : 0;
        $result['rfm_data']  = ShopCustomer::getRfmData($this->corp->id, $consumptionDataOpen, $msgAuditOpen);

        //rfm默认等级列表
        $rfmAll  = ShopCustomerRfmAlias::getAllData($this->corp->id);
        $rfmList = [];
        foreach ($rfmAll as $v) {
            $rfmList[$v['type']][] = [
                'id'           => $v['id'],
                'frequency'    => $v['frequency'] == 1 ? '高' : '低',
                'recency'      => $v['recency'] == 1 ? '高' : '低',
                'monetary'     => $v['type'] == 0 ? '--' : ($v['monetary'] == 1 ? '高' : '低'),
                'default_name' => $v['default_name'],
                'alis_name'    => !empty($v['alias']) ? $v['alias']['rfm_name'] : '',
            ];
        }
        $result['open_rfm_list']  = $rfmList[1];
        $result['close_rfm_list'] = $rfmList[0];
        return $result;
    }

    /*
     * rfm别名自定义
     * @url  http://{host_name}/api/shop-customer-rfm/add-alias
     * */
    public function actionAddAlias()
    {
        $data['rfm_id']   = \Yii::$app->request->post('rfm_id', 0);
        $data['rfm_name'] = \Yii::$app->request->post('rfm_name', 0);
        $id               = ShopCustomerRfmAlias::AddAlias($this->corp->id, $this->user->uid, $data);
        return ['id' => $id];
    }

    /*
     * rfm配置修改
     * @url  http://{host_name}/api/shop-customer-rfm/change-setting
     * */
    public function actionChangeSetting()
    {
        $post = \Yii::$app->request->post();
        $data = [];
        if (isset($post['consumption_data_open'])) {
            $data['consumption_data_open'] = $post['consumption_data_open'];
        }
        if (isset($post['msg_audit_open'])) {
            $data['msg_audit_open'] = $post['msg_audit_open'] ?: 0;
        }
        if (isset($post['msg_allow_time'])) {
            $data['msg_allow_time'] = $post['msg_allow_time'] ?: 0;
        }
        if (isset($post['frequency_type'])) {
            $data['frequency_type'] = $post['frequency_type'] ?: 0;
        }
        if (isset($post['frequency_value'])) {
            $data['frequency_value'] = $post['frequency_value'] ?: 0;
        }
        if (isset($post['recency_type'])) {
            $data['recency_type'] = $post['recency_type'] ?: 0;
        }
        if (isset($post['recency_value'])) {
            $data['recency_value'] = $post['recency_value'] ?: 0;
        }
        if (isset($post['monetary_value'])) {
            $data['monetary_value'] = $post['monetary_value'] ?: 0;
        }
        if (empty($data)) {
            throw new InvalidDataException('参数为空!');
        }

        return ShopCustomerRfmSetting::updateSetting($this->corp->id, $this->user->uid, $data);

    }

}