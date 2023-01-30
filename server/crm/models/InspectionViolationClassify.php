<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_inspection_violation_classify".
 *
 * @property int $id
 * @property int $corp_id 企业id
 * @property string $content 内容
 * @property string $create_time 创建时间
 * @property int $is_delete 是否删除 0否 1是
 */
class InspectionViolationClassify extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%inspection_violation_classify}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'is_delete'], 'integer'],
            [['content'], 'required'],
            [['create_time'], 'safe'],
            [['content'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '企业id'),
            'content' => Yii::t('app', '内容'),
            'create_time' => Yii::t('app', '创建时间'),
            'is_delete' => Yii::t('app', '是否删除 0否 1是'),
        ];
    }

    /**
     * 添加默认数据
     */
    public static function getClassifyAdd($corp_id)
    {
        $arr = [
            [
                'corp_id' => $corp_id,
                'content' => '言语不适当，需要注意'
            ],
            [
                'corp_id' => $corp_id,
                'content' => '辱骂客户'
            ],
            [
                'corp_id' => $corp_id,
                'content' => '没有正面回应客户问题'
            ],
            [
                'corp_id' => $corp_id,
                'content' => '未及时回复客户问题'
            ],
            [
                'corp_id' => $corp_id,
                'content' => '未准确解答客户问题'
            ],
        ];
        \Yii::$app->db->createCommand()
            ->batchInsert(InspectionViolationClassify::tableName(), [
                'corp_id',
                'content',
            ], $arr)
            ->execute();
    }
}
