<?php

use yii\db\Migration;

/**
 * Class m200416_062258_change_work_contact_way
 */
class m200416_062258_change_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way}}', 'way_group_id', 'int(11) unsigned COMMENT \'渠道活码分组id\'  AFTER `corp_id`');
	    $this->addForeignKey('KEY_WORK_CONTACT_WAY_WAYGROUPID', '{{%work_contact_way}}', 'way_group_id', '{{%work_contact_way_group}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200416_062258_change_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200416_062258_change_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
