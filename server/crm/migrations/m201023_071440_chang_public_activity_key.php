<?php

use yii\db\Migration;

/**
 * Class m201023_071440_chang_public_activity_key
 */
class m201023_071440_chang_public_activity_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_public_activity}}', 'corp_agent', $this->integer(11)->unsigned()->comment('应用id')->after('corp_id'));
	    $this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_CORP_AGENT', '{{%work_public_activity}}', 'corp_agent', '{{%work_corp_agent}}', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201023_071440_chang_public_activity_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_071440_chang_public_activity_key cannot be reverted.\n";

        return false;
    }
    */
}
