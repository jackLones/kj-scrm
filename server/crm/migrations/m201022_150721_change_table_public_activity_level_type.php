<?php

use yii\db\Migration;

/**
 * Class m201022_150721_change_table_public_activity_level_type
 */
class m201022_150721_change_table_public_activity_level_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_public_activity_config_level}}', 'type', $this->integer(1)->comment("1实物，2红包")->after("is_open"));
	    $this->addColumn('{{%work_public_activity_prize_user}}', 'type', $this->integer(1)->comment("1实物，2红包")->after("status"));
	    $this->addColumn('{{%work_public_activity_fans_user}}', 'external_userid', $this->integer(11)->comment("外部联系人id")->after("parent_id"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_150721_change_table_public_activity_level_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_150721_change_table_public_activity_level_type cannot be reverted.\n";

        return false;
    }
    */
}
