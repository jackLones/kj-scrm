<?php

use yii\db\Migration;

/**
 * Class m200323_033355_change_table_awards_activity
 */
class m200323_033355_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_activity}}', 'corp_id', 'int(11) unsigned DEFAULT NULL COMMENT \'授权的企业ID\' AFTER `uid`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200323_033355_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200323_033355_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
