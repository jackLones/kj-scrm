<?php

use yii\db\Migration;

/**
 * Class m191216_075231_change_table_kf_push
 */
class m191216_075231_change_table_kf_push extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%kf_push_msg}}', 'error_code', 'int(11)  DEFAULT \'0\' COMMENT \'错误码\'');
	    $this->alterColumn('{{%high_level_push_msg}}', 'error_code', 'int(11)  DEFAULT \'0\' COMMENT \'错误码\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191216_075231_change_table_kf_push cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_075231_change_table_kf_push cannot be reverted.\n";

        return false;
    }
    */
}
