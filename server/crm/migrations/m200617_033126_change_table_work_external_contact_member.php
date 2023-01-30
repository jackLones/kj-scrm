<?php

use yii\db\Migration;

/**
 * Class m200617_033126_change_table_work_external_contact_member
 */
class m200617_033126_change_table_work_external_contact_member extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%work_external_contact_member}}', 'create_time', 'timestamp NULL COMMENT \'创建时间\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200617_033126_change_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200617_033126_change_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }
    */
}
