<?php

use yii\db\Migration;

/**
 * Class m200119_062443_change_table_work_welcome
 */
class m200119_062443_change_table_work_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->dropColumn('{{%work_welcome}}', 'user_name');
		$this->addColumn('{{%work_welcome}}', 'status', 'tinyint(1) unsigned DEFAULT 1 COMMENT \'是否启用1启用0不启用\' AFTER `context`');
		$this->addColumn('{{%work_welcome}}', 'time_json', 'varchar(100) DEFAULT NULL COMMENT \'生效时间\' AFTER `context`');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown ()
	{
		echo "m200119_062443_change_table_work_welcome cannot be reverted.\n";

		return false;
	}

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200119_062443_change_table_work_welcome cannot be reverted.\n";

        return false;
    }
    */
}
