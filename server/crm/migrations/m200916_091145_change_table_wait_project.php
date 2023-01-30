<?php

use yii\db\Migration;

/**
 * Class m200916_091145_change_table_wait_project
 */
class m200916_091145_change_table_wait_project extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%wait_project}}', 'level_id', 'int(11) unsigned DEFAULT NULL  COMMENT \'项目优先级\' AFTER `user_id`');
		$this->addForeignKey('KEY_WAIT_PROJECT_LEVEL', '{{%wait_project}}', 'level_id', '{{%wait_level}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200916_091145_change_table_wait_project cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200916_091145_change_table_wait_project cannot be reverted.\n";

        return false;
    }
    */
}
