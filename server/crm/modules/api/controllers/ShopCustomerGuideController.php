<?php

namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\models\AuthStoreGroup;
use app\models\AuthStoreUser;
use app\models\ShopCustomer;
use app\models\ShopCustomerGuideRelation;
use app\models\ShopCustomerOrder;
use app\models\ShopCustomerRfmSetting;
use app\models\ShopGuideAttribution;
use app\models\WorkUser;
use app\modules\api\components\WorkBaseController;
use moonland\phpexcel\Excel;
use yii\db\Expression;
use yii\debug\actions\db\ExplainAction;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


class ShopCustomerGuideController extends WorkBaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'               => ['POST'],
                    'achievement'        => ['POST'],
                    'get-config'         => ['POST'],
                    'update-attribution' => ['POST'],
                    'download-code'      => ['POST'],
                ],
            ],
        ]);
    }

    /*
    * 导购列表
    * @url  http://{host_name}/api/shop-customer-guide/list
    * */
    public function actionList()
    {
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;
        $where    = ['u.corp_id' => $this->corp->id, 'u.status' => 1, 'u.is_del' => 0, 'u.is_external' => 1, 'u.dimission_time' => 0];

        $guideKeyword = \Yii::$app->request->post('guide_keyword', ''); //导购昵称或导购号码

        $workUserModel = WorkUser::find()
            ->alias('u')
            ->leftJoin("{{%auth_store_user}} as a", "u.id=a.user_id and a.status=1")
            ->leftJoin("{{%auth_store}} as s", "s.id=a.store_id")
            ->leftJoin("{{%auth_store_group}} as g", "g.id=s.group_id")
            ->select('u.id as uid,u.name,a.store_id,s.shop_name,s.group_id,a.qc_url,g.parent_ids,a.id as code_id')
            ->where($where);
        if (!empty($guideKeyword)) {
            $workUserModel->andWhere(['or', ['like', 'u.name', trim($guideKeyword)], ['like', 'u.mobile', trim($guideKeyword)]]);
        }

        //小组或者门店id
        $storeId = \Yii::$app->request->post('store_id', '');
        if (!empty($storeId)) {
            $storeIdArr = strstr($storeId, ',') ? explode(',', $storeId) : [$storeId];
            $storeIds   = [];
            foreach ($storeIdArr as $v) {
                if (strstr($v, '-') !== false) {
                    $tmpId = explode('-', $v);
                    if ($tmpId[1] == 's') {
                        $storeIds[] = $tmpId[0];
                    }
                } else {
                    $storeIds[] = $v;
                }
            }
            $workUserModel = $workUserModel->andWhere(['in', 'a.store_id', $storeIds]);
        }

        $count     = $workUserModel->count();
        $guideList = $workUserModel
            ->limit($pageSize)
            ->offset($offset)
            ->asArray()
            ->all();

        foreach ($guideList as $k => $v) {
            $guideList[$k]['store_id'] = $v['store_id'] ? $v['store_id'] : 0;
            $customerId[]              = $v['uid'];
        }
        //统计顾客数量
        $customerNum = [];
        if (!empty($customerId)) {
            $customerList = ShopCustomerGuideRelation::find()
                ->select(new Expression("guide_id,store_id,count(distinct cus_id) as num,count(distinct case when DATE_FORMAT(add_time,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m') then cus_id else null end) as month_num"))
                ->where(['guide_id' => $customerId, 'status' => 1])
                ->groupBy('guide_id,store_id')
                ->asArray()
                ->all();

            foreach ($customerList as $v) {
                $customerNum[$v['guide_id']][$v['store_id']]['num']       = $v['num'];
                $customerNum[$v['guide_id']][$v['store_id']]['month_num'] = $v['month_num'];
            }
        }

        $result = [];
        foreach ($guideList as $k => $v) {
            $result[$k]['key']      = $k;
            $result[$k]['id']       = $v['uid'] ? intval($v['uid']) : 0;
            $result[$k]['name']     = $v['name'] ? $v['name'] : 0;
            $result[$k]['type']     = '企业微信';
            $result[$k]['code_id']  = $v['code_id'] ? intval($v['code_id']) : 0;
            $result[$k]['qc_url']   = $v['qc_url'] ? $v['qc_url'] : '';
            $result[$k]['store_id'] = $v['store_id'] ? $v['store_id'] : 0;

            //门店分组信息
            $result[$k]['group_name'] = ShopCustomerOrder::getStoreName($v['store_id']);
            $result[$k]['name']       = $result[$k]['name'];

            //顾客数量
            if (!empty($customerNum[$v['uid']][$v['store_id']]['num'])) {
                $result[$k]['num'] = $customerNum[$v['uid']][$v['store_id']]['num'];
            } else {
                $result[$k]['num'] = 0;
            }
            if (!empty($customerNum[$v['uid']][$v['store_id']]['month_num'])) {
                $result[$k]['month_num'] = $customerNum[$v['uid']][$v['store_id']]['month_num'];
            } else {
                $result[$k]['month_num'] = 0;
            }

        }

        return ['result' => $result, 'count' => $count];
    }

    /*
    * 导购业绩
    * @url  http://{host_name}/api/shop-customer-guide/achievement
    * */
    public function actionAchievement()
    {
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;
        $where    = ['u.corp_id' => $this->corp->id, 'o.corp_id' => $this->corp->id,'u.status' => 1, 'u.is_del' => 0, 'u.is_external' => 1, 'u.dimission_time' => 0];

        $guideKeyword = \Yii::$app->request->post('guide_keyword', ''); //导购昵称或导购号码
        $startTimes   = \Yii::$app->request->post('start_time', ''); //开始时间
        $startTime    = date("Y-m-d", strtotime($startTimes)) . ' 00:00:00';

        $endTimes = \Yii::$app->request->post('end_time', '');   //结束时间
        $endTime  = date("Y-m-d", strtotime("+1 day", strtotime($endTimes))) . ' 00:00:00';;

        $isExport = \Yii::$app->request->post('is_export', 0);   //是否导出

        /*导购业绩*/
        $orderModel = ShopCustomerOrder::find()->alias('o')
            ->select(new Expression("o.guide_id,u.name,o.store_id,s.shop_name,g.parent_ids,count(o.id) as num,sum(o.payment_amount) as amount,count(case when o.status=2 then o.id else null end) as refund_num,sum(o.refund_amount) as refund"))
            ->joinWith('user u', true, 'left join')
            ->joinWith(['store s' => function ($query) {
                return $query->select('s.id,s.shop_name,s.group_id,g.parent_ids')->joinWith('group g', true, 'left join');
            }], true, 'left join')
            ->where($where);
        //门店筛选
        $storeId = \Yii::$app->request->post('store_id', '');
        if (!empty($storeId)) {
            //TODO::门店筛选
            $storeIdArr = strstr($storeId, ',') ? explode(',', $storeId) : [$storeId];
            $storeIds   = [];
            foreach ($storeIdArr as $v) {
                if (strstr($v, '-') !== false) {
                    $tmpId = explode('-', $v);
                    if ($tmpId[1] == 's') {
                        $storeIds[] = $tmpId[0];
                    }
                } else {
                    $storeIds[] = $v;
                }
            }
            if (!empty($storeIds)) {
                $orderModel = $orderModel->andWhere(['in', 'o.store_id', $storeIds]);
            }

        }
        //关键字筛选
        if (!empty($guideKeyword)) {
            $orderModel = $orderModel->andWhere(['or', ['like', 'u.name', trim($guideKeyword)], ['like', 'u.mobile', trim($guideKeyword)]]);
        }
        if (!empty($startTimes) && !empty($endTimes)) {
            $orderModel = $orderModel->andFilterWhere(['between', 'o.pay_time', $startTime, $endTime]);
        }
        if (!empty($storeId)) {
            $orderModel = $orderModel->groupBy('o.guide_id,o.store_id');
        } else {
            $orderModel = $orderModel->groupBy('o.guide_id');
        }
        $guideListCount = $orderModel->count();
        if ($isExport == 1) {
            $guideList = $orderModel->asArray()->all();
        } else {
            $guideList = $orderModel->limit($pageSize)->offset($offset)->asArray()->all();
        }
        /*导购业绩*/

        $result = [];
        foreach ($guideList as $k => $v) {
            $result[$k]['key']      = $k;
            $result[$k]['id']       = $v['guide_id'] ? $v['guide_id'] : 0;
            $result[$k]['store_id'] = $v['store_id'] ? $v['store_id'] : 0;
            $result[$k]['name']     = $v['name'] ? $v['name'] : '';
            $result[$k]['name']     = $result[$k]['name'] . '(' . $v['guide_id'] . ')';
            //门店分组信息
            if (!empty($storeId)) {
                $groupName = [];
                if (!empty($v['parent_ids'])) {
                    $group     = AuthStoreGroup::find()
                        ->where(['id' => explode(',', $v['parent_ids'])])
                        ->select('name')
                        ->asArray()
                        ->all();
                    $groupName = array_column($group, 'name');
                    $groupName = implode('-', $groupName) . '-' . $v['shop_name'];
                }
                $result[$k]['group_name'] = $groupName ? $groupName : '';
            } else {
                $result[$k]['group_name'] = '所有门店';
                $result[$k]['store_id']   = 0;
            }

            $result[$k]['amount']     = $v['amount'];
            $result[$k]['num']        = $v['num'];
            $result[$k]['refund_num'] = $v['refund_num'];
            $result[$k]['refund']     = $v['refund'];
        }

        if ($isExport == 1) {
            if (empty($result)) {
                throw new InvalidParameterException('暂无数据，无法导出！');
            }
            $saveDir = \Yii::getAlias('@upload') . '/exportfile/' . date('Ymd') . '/';
            //创建保存目录
            if (!file_exists($saveDir) && !mkdir($saveDir, 0777, true)) {
                return ['error' => 1, 'msg' => '无法创建目录'];
            }

            $columns  = ['name', 'group_name', 'num', 'amount', 'refund_num', 'refund'];
            $headers  = [
                'name'       => '姓名',
                'group_name' => '所属门店',
                'num'        => '关联订单数',
                'amount'     => '关联销售额',
                'refund_num' => '退款数',
                'refund'     => '退款金额',
            ];
            $fileName = '导购业绩表_' . date("YmdHis", time());
            Excel::export([
                'models'       => $result,//数库
                'fileName'     => $fileName,//文件名
                'savePath'     => $saveDir,//下载保存的路径
                'asAttachment' => true,//是否下载
                'columns'      => $columns,//要导出的字段
                'headers'      => $headers
            ]);
            $url = \Yii::$app->params['site_url'] . str_replace(\Yii::getAlias('@upload'), '/upload', $saveDir) . $fileName . '.xlsx';
            return [
                'url' => $url,
            ];
        }
        return ['result' => $result, 'count' => $guideListCount];
    }


    /*
    * 读取导购配置信息
    * @url  http://{host_name}/api/shop-customer-guide/get-config
    * */
    public function actionGetConfig()
    {
        $data['role']   = ShopCustomerGuideRelation::getRole($this->corp->id);
        $data['config'] = ShopGuideAttribution::getData($this->corp->id);
        if (!empty($data['role']) && !empty($data['config']) && !empty($data['config']['role'])) {
            $roles = strstr($data['config']['role'], ',') !== false ? explode(',', $data['config']['role']) : [(int)$data['config']['role']];
            foreach ($roles as $k => $v) {
                $roles[$k] = intval($v);
            }
            $data['config']['role'] = $roles;
            foreach ($data['role'] as &$role) {
                $role['checked'] = in_array($role['id'], $data['config']['role']) ? 1 : 0;
            }
        }
        return $data;
    }

    /*
    * 更新配置信息
    * @url  http://{host_name}/api/shop-customer-guide/update-attribution
    * */
    public function actionUpdateAttribution()
    {
        $post = \Yii::$app->request->post();
        $data = [];
        if (isset($post['role'])) {
            $data['role'] = $post['role'];
        }
        if (isset($post['mode_type'])) {
            $data['mode_type'] = $post['mode_type'];
        }
        if (isset($post['priority'])) {
            $data['priority'] = $post['priority'];
        }
        if (isset($post['is_consumption'])) {
            $data['is_consumption'] = $post['is_consumption'];
        }
        if (isset($post['in_page_lock'])) {
            $data['in_page_lock'] = $post['in_page_lock'];
        }
        if (isset($post['add_friend_lock'])) {
            $data['add_friend_lock'] = $post['add_friend_lock'];
        }
        if (isset($post['consumption_amount_lock'])) {
            $data['consumption_amount_lock'] = $post['consumption_amount_lock'];
        }
        if (isset($post['performance_related'])) {
            $data['performance_related'] = $post['performance_related'];
        }
        if (empty($data)) {
            throw new InvalidDataException('参数为空!');
        }
        return ShopGuideAttribution::updateConfig($this->corp->id, $this->user->uid, $data);
    }

    /*
    * 下载二维码
    * @url  http://{host_name}/api/shop-customer-guide/download-code
    * */
    public function actionDownloadCode()
    {
        $codeId = \Yii::$app->request->post('code_id');
        if (empty($codeId)) {
            throw new InvalidDataException('参数为空!');
        }
        $codeIds = explode(',', $codeId);
        $file    = AuthStoreUser::find()->select('qc_url,user_id,store_id')->where(['id' => $codeIds])->all();
        $saveDir = \Yii::getAlias('@upload') . '/download_file/' . date('Ymd') . '/';
        //创建保存目录
        if (!file_exists($saveDir) && !mkdir($saveDir, 0777, true)) {
            return ['error' => 1, 'msg' => '无法创建目录'];
        }
        $fileName = $saveDir . '二维码.zip';
        $zip      = new \ZipArchive;
        $res      = $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($res === TRUE) {
            foreach ($file as $qr_code) {
                if (!empty($qr_code['qc_url'])) {
                    $zip->addFromString($qr_code['user_id'] . '_' . $qr_code['store_id'] . '.png', file_get_contents(\Yii::$app->params['site_url'] . str_replace(\Yii::getAlias('@upload'), '/upload', $qr_code['qc_url'])));
                }
            }
            $zip->addFromString('ReadMe.txt', '欢迎日思夜想scrm！');
            $zip->close();
            $url = \Yii::$app->params['site_url'] . str_replace(\Yii::getAlias('@upload'), '/upload', $fileName);
            return ['url' => $url];
        } else {
            return ['url' => '', 'msg' => '下载失败，请重试'];
        }
    }


}
