<?php

namespace app\models;

use yii\base\Component;

class DissipateLog extends Component
{
    public $action;

    public $action_str;

    public $amount;

    public $detailed;

    public $create_time;

    public $custom_key;

    public function getUser()
    {
        return $this->dialoutConfig->user;
    }

    public function getWorkCorp()
    {
        return $this->dialoutConfig->workCorp;
    }

    public function getDialoutConfig()
    {
        return DialoutConfig::find()->where(['corp_id' => $this->custom_key])->one();
    }
}
