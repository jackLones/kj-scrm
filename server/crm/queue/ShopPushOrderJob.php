<?php
namespace app\queue;

use app\components\InvalidDataException;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderSet;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class  ShopPushOrderJob extends BaseObject implements JobInterface
{
    public $config;
    public $order;

    public function execute($queue)
    {
        ShopThirdOrder::addOrderOne($this->config['corp_id'], $this->config['shop_api_key'], $this->order);
    }
}