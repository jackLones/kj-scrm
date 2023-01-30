<?php

use yii\db\Migration;

/**
 * Class m210205_032121_change_custom_field
 */
class m210205_032121_change_custom_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //线下客户来源
        $customField = \app\models\CustomField::findOne(['uid' => 0, 'type' => 2, 'key' => 'offline_source']);
        if (!empty($customField)) {
            $customFieldOption =  \app\models\CustomFieldOption::find()
                ->select('')
                ->where(['fieldid' =>$customField['id']])
                ->orderBy('value desc')
                ->asArray()
                ->one();
            if (!empty($customFieldOption)) {
                $this->insert('{{%custom_field_option}}', [
                    'fieldid' => $customField->id,
                    'value'   => $customFieldOption['value']+1,
                    'match'   => '第三方订单导入',
                ]);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210205_032121_change_custom_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210205_032121_change_custom_field cannot be reverted.\n";

        return false;
    }
    */
}
