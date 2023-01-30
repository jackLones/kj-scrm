<?php

use yii\db\Migration;

/**
 * Class m200115_054436_change_table_work_contact_way
 */
class m200115_054436_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_contact_way}}', 'status', 'tinyint(1) DEFAULT 0 COMMENT \'渠道活码的欢迎语是否开启0关闭1开启\'');
	    $this->alterColumn('{{%work_contact_way}}', 'content', 'text COMMENT \'渠道活码的欢迎语内容\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200115_054436_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200115_054436_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
