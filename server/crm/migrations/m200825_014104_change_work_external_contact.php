<?php

use yii\db\Migration;

/**
 * Class m200825_014104_change_work_external_contact
 */
class m200825_014104_change_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_external_contact}}', 'is_fans', 'tinyint(1) NULL DEFAULT 0 COMMENT \'0：不是粉丝；1：是粉丝\' AFTER `unionid`');
		$this->createIndex('KEY_WORK_EXTERNAL_CONTACT_IS_FANS', '{{%work_external_contact}}', 'is_fans');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200825_014104_change_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200825_014104_change_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
