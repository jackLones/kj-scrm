<?php

namespace app\modules\admin\repositories;

use app\models\DialoutKey;

class DialoutKeyRepository
{
    /**
     * @var DialoutKey
     */
    protected $dialotKey;

    /**
     * DialoutKeyRepository constructor.
     * @param DialoutKey $dialotKey
     */
    public function __construct(DialoutKey $dialotKey)
    {
        $this->dialotKey = $dialotKey;
    }

    /**
     * @param string $apiType
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getDialoutKey(string $apiType)
    {
        return DialoutKey::find()->where(['api_type' => $apiType])->one();
    }
}