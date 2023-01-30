<?php

namespace app\queue;

use app\components\InvalidDataException;
use app\models\ShopMaterialCoupon;
use app\models\ShopMaterialPage;
use app\models\ShopMaterialProduct;
use app\models\ShopMaterialProductGroup;
use app\models\ShopMaterialSourceRelationship;
use app\models\ShopThirdOrder;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class  ShopPushJob extends BaseObject implements JobInterface
{
    public $config;
    public $data;
    public $method;

    public function execute($queue)
    {
        switch ($this->method) {
            case 'order_list':
                $this->dealOrder();
                break;
            case 'product_list':
                $this->dealProduct();
                break;
            case 'page':
                $this->dealPage();
                break;
            case 'coupon':
                $this->dealCoupon();
                break;
            case 'review_count':
                $this->dealReviewCount();
                break;
            default:
                break;
        }
    }

    private function dealOrder()
    {
        $order = $this->data;
        foreach ($order as $v) {
            ShopThirdOrder::addOrderOne($this->config['corp_id'], $this->config['shop_api_key'], $v);
        }
    }

    private function dealProduct()
    {
        $products = $this->data;
        foreach ($products as $v) {
            $where             = [
                'corp_id'    => $this->config['corp_id'],
                'source'     => ShopThirdOrder::SOURCE_PIG,
                'product_id' => $v['product_id']
            ];
            $v['corp_id']      = $this->config['corp_id'];
            $v['shop_api_key'] = $this->config['shop_api_key'];
            if (!empty($v['recommend_remark'])) {
                $v['recommend_remark'] = rawurlencode($v['recommend_remark']);
            }
            //如果存在分组则插入分组表
            $v['group_id']             = $v['group_id'] > 0 && !empty($v['group_name']) ? $v['group_id'] : 0;
            $whereGroup                = [
                'corp_id'  => $this->config['corp_id'],
                'group_id' => $v['group_id'],
                'source'   => ShopThirdOrder::SOURCE_PIG
            ];
            $groupSave                 = $whereGroup;
            $groupSave['name']         = $v['group_id'] > 0 ? $v['group_name'] : '暂无分组';
            $groupSave['shop_api_key'] = $this->config['shop_api_key'];
            $v['group_id']             = ShopMaterialProductGroup::addProductGroup($whereGroup, $groupSave);
            unset($v['group_name']);
            unset($groupSave);
            unset($whereGroup);
            //TODO::暂时只有小猪电商 后期需要调整
            $v['source'] = ShopThirdOrder::SOURCE_PIG;
            try {
                ShopMaterialProduct::addProduct($where, $v);
            } catch (InvalidDataException $e) {
                \Yii::error(['product' => $v, 'error' => $e], 'addProductError');
            }
            unset($v);
        }
    }

    private function dealPage()
    {
        $pages = $this->data;
        foreach ($pages as $v) {
            $where             = [
                'corp_id' => $this->config['corp_id'],
                'page_id' => $v['page_id'],
                'source'  => ShopThirdOrder::SOURCE_PIG
            ];
            $v['corp_id']      = $this->config['corp_id'];
            $v['shop_api_key'] = $this->config['shop_api_key'];
            //TODO::暂时只有小猪电商 后期需要调整
            $v['source'] = ShopThirdOrder::SOURCE_PIG;
            try {
                ShopMaterialPage::addPage($where, $v);
            } catch (InvalidDataException $e) {
                \Yii::error(['product' => $v, 'error' => $e], 'addPageError');
            }
            unset($v);
        }
    }

    private function dealCoupon()
    {
        $coupon = $this->data;
        foreach ($coupon as $v) {
            $where             = [
                'corp_id'   => $this->config['corp_id'],
                'coupon_id' => $v['coupon_id'],
                'source'    => ShopThirdOrder::SOURCE_PIG
            ];
            $v['corp_id']      = $this->config['corp_id'];
            $v['shop_api_key'] = $this->config['shop_api_key'];
            try {
                //TODO::暂时只有小猪电商 后期需要调整
                $v['source'] = ShopThirdOrder::SOURCE_PIG;
                ShopMaterialCoupon::addCoupon($where, $v);
            } catch (InvalidDataException $e) {
                \Yii::error(['coupon' => $v, 'error' => $e], 'addCouponError');
            }
            unset($v);
        }
    }

    private function dealReviewCount()
    {
        $scrmsid = $this->data;
        ShopMaterialSourceRelationship::updateAllCounters(['review_count' => 1], ['id' => $scrmsid]);
    }


}