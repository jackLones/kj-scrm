<?php

use yii\db\Migration;

/**
 * Class m200213_011740_change_table_work_contact_way
 */
class m200213_011740_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_contact_way}}', 'attachment_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'内容引擎id\' AFTER `status`');
		$this->addColumn('{{%work_contact_way}}', 'material_sync', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'不同步到内容库1同步\' AFTER `status`');
		$this->addColumn('{{%work_contact_way}}', 'groupId', 'int(11) unsigned DEFAULT \'0\' COMMENT \'分组id\' AFTER `status`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200213_011740_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_011740_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
