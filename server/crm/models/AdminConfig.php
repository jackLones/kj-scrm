<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_admin_config".
 *
 * @property string $group 配置类型
 * @property string $key 字段名称
 * @property string $value 字段内容
 * @property string $create_time 创建时间
 * @property string $update_time 修改时间
 */
class AdminConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['value'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['group'], 'string', 'max' => 255],
            [['key'], 'string', 'max' => 180],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group'         => Yii::t('app','配置类型'),
            'key'           => Yii::t('app','字段名称'),
            'value'         => Yii::t('app','字段内容'),
            'create_time'   => Yii::t('app','创建时间'),
            'update_time'   => Yii::t('app','修改时间'),
        ];
    }

		/*
		 * 更新版权图片
		 * */
        public function updateTechImg($url) {
            if (!$url) return false;
            $this->updateAll(['value'=>$url],['group'=>'web','key'=>'web_tech_img']);
        }

        /*
         * 根据key获取值
         * */
        public static function getValueByKey($key)
        {
            if (!$key) return '';
            $data = self::find()->where(['key' => $key])->asArray()->one();

            if (!$data) {
                return '';
            }else{
                return $data['value'];
            }

        }


	}
