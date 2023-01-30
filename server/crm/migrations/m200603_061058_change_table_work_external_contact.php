<?php

	use yii\db\Migration;

	/**
 * Class m200603_061058_change_table_work_external_contact
 */
class m200603_061058_change_table_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_external_contact}}', 'chat_way_id', 'char(64) DEFAULT NULL COMMENT \'群活码配置ID\' AFTER `way_id`');
		$this->createIndex('KEY_CHAT_WAY_ID', '{{%work_external_contact}}', 'chat_way_id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_061058_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_061058_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
