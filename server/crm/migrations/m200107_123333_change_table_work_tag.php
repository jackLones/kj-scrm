<?php

use yii\db\Migration;

/**
 * Class m200107_123333_change_table_work_tag
 */
class m200107_123333_change_table_work_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag}}', 'type', 'tinyint(1) DEFAULT "0" COMMENT \'类型0 外部联系人 1 员工\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200107_123333_change_table_work_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200107_123333_change_table_work_tag cannot be reverted.\n";

        return false;
    }
    */
}
