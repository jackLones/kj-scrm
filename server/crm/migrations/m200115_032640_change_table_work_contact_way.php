<?php

use yii\db\Migration;

/**
 * Class m200115_032640_change_table_work_contact_way
 */
class m200115_032640_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_contact_way}}', 'status', 'tinyint(1) DEFAULT 0 COMMENT \'渠道活码是否开启0关闭1开启\' AFTER `add_num` ');
		$this->addColumn('{{%work_contact_way}}', 'content', 'text COMMENT \'渠道活码的内容\' AFTER `add_num` ');
		$this->addColumn('{{%work_contact_way}}', 'tag_ids', 'text COMMENT \'给客户打的标签\' AFTER `add_num` ');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200115_032640_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200115_032640_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
