<?php

use yii\db\Migration;

/**
 * Class m200928_054351_change_table_public_sea_reclaim_set
 */
class m200928_054351_change_table_public_sea_reclaim_set extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%public_sea_reclaim_set}}', 'is_protect', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'是否客户保护：0否、1是\' AFTER `reclaim_day`');
	    $this->addColumn('{{%public_sea_reclaim_set}}', 'protect_num', 'int(11) unsigned DEFAULT 0 COMMENT \'客户保护数量\' AFTER `is_protect`');
	    $this->addColumn('{{%public_sea_contact_follow_user}}', 'is_protect', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'是否客户保护：0否、1是\' AFTER `is_reclaim`');
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'is_protect', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'是否客户保护：0否、1是\' ');
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'other_way', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'添加客户的其它来源：1公海池\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200928_054351_change_table_public_sea_reclaim_set cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200928_054351_change_table_public_sea_reclaim_set cannot be reverted.\n";

        return false;
    }
    */
}
