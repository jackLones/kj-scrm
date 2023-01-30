<?php


namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\models\ShopCustLevelSet;
use app\models\ShopCustomer;
use app\models\ShopCustomerLevelLog;
use app\models\ShopCustomerRfmAlias;
use app\models\ShopCustomerRfmDefault;
use app\modules\api\components\WorkBaseController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ShopCustomerLevelController extends WorkBaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'         => ['POST'],
                    'add-level'    => ['POST'],
                    'update-level' => ['POST'],
                    'delete-level' => ['POST'],
                ],
            ],
        ]);
    }


    /**
     * @inheritDoc
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     *
     * @throws \app\components\InvalidParameterException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }


    /*
     * 等级列表接口
     * @url  http://{host_name}/api/shop-customer-level/list
     * */
    public function actionList()
    {

        $level  = ShopCustLevelSet::getData($this->corp->id);
        $result = [];
        //每个等级的顾客数以及占比
        $customer = ShopCustomer::getLevelDetail($this->corp->id);

        $levelCustomerNum = 0;
        if (!empty($level)) {
            foreach ($level as $k => $v) {
                $v['customer_num']  = isset($customer['level_num'][$v['id']]) ? $customer['level_num'][$v['id']] : 0;
                $v['customer_rate'] = isset($customer['level_num'][$v['id']]) ? ceil($customer['level_num'][$v['id']] * 100 / $customer['customer_num']) : 0;
                $result[$k]         = $v;
                $levelCustomerNum   += $v['customer_num'];
            }
        }

        //无等级
        $noLevel['title']         = '无等级';
        $noLevel['desc']          = '未划分任何等级的用户';
        $noLevel['customer_num']  = $customer['customer_num'] - $levelCustomerNum;
        $noLevel['customer_rate'] = $customer['customer_num'] > 0 ? ceil($noLevel['customer_num'] * 100 / $customer['customer_num']) : 0;
        return [
            'result'   => $result,
            'no_level' => $noLevel
        ];
    }

    /*
     * 等级添加接口
     * @url  http://{host_name}/api/shop-customer-level/add-level
     * */
    public function actionAddLevel()
    {
        $data['corp_id'] = $this->corp->id;
        $data['title']   = \Yii::$app->request->post('title', 0);
        $data['weight']  = \Yii::$app->request->post('weight', 0);
        $data['color']   = \Yii::$app->request->post('color', '#000000');
        $data['desc']    = \Yii::$app->request->post('desc', '');
        $data['sort']    = \Yii::$app->request->post('sort', 100);
        $id              = ShopCustLevelSet::addLevel($data, $this->user->uid);
        return ['id' => $id];
    }

    /*
    * 等级更新接口
    * @url  http://{host_name}/api/shop-customer-level/update-level
    * */
    public function actionUpdateLevel()
    {
        $post = \Yii::$app->request->post();
        if (!isset($post['id']) || empty($post['id'])) {
            throw new InvalidDataException('该等级记录不存在！');
        }
        $post['corp_id'] = $this->corp->id;
        return ShopCustLevelSet::UpdateLevel($post, $this->user->uid);
    }


    /*
    * 等级删除/移动接口
    * @url  http://{host_name}/api/shop-customer-level/delete-level
    * */
    public function actionDeleteLevel()
    {
        $id      = \Yii::$app->request->post('id');
        $levelId = \Yii::$app->request->post('level_id');
        $type    = \Yii::$app->request->post('type', 0); //操作类型 1删除 0移动
        $check   = \Yii::$app->request->post('check', 0); //确认知道操作危险性 1确认 0未确认

        if (empty($this->corp->id)) {
            throw new InvalidDataException('缺少企业id参数！');
        }

        $result   = ['result' => 0];
        $customer = ShopCustomer::getLevelDetail($this->corp->id);

        if (0 == $check) {
            throw new InvalidDataException('未勾选确认操作影响框！');
        }
        if (!isset($id) || empty($id)) {
            throw new InvalidDataException('缺少等级id参数！');
        }


        if ((!isset($levelId)) && (isset($customer['level_num'][$id]) && $customer['level_num'][$id] > 0)) {
            throw new InvalidDataException('缺少新等级参数！');
        } else if (isset($customer['level_num'][$id]) && $customer['level_num'][$id] > 0) {
            $newLevel = $levelId > 0 ? ShopCustLevelSet::findOne($levelId) : 0;
            if (empty($newLevel) && $levelId !== 0) {
                throw new InvalidDataException('新等级不存在！');
            }
            if ($levelId == $id) {
                throw new InvalidDataException('新老等级不能相同！');
            }
        }

        if (isset($customer['level_num'][$id]) && $customer['level_num'][$id] > 0) {
            $customerWhere = ['corp_id' => $this->corp->id, 'level_id' => $id];
            $re            = ShopCustomer::updateCustomerLevel($this->corp->id, $this->user->uid, $customerWhere, $levelId);
            if (!$re) {
                throw new InvalidDataException('用户移动失败请重新移动！');
            }
            $result = ['result' => $re];
        }
        if ($type == 1) {
            $result = ShopCustLevelSet::deleteLevel($id, $this->user->uid);
        }
        return $result;
    }


}