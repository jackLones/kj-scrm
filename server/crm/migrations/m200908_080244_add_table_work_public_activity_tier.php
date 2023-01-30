<?php

use yii\db\Migration;

/**
 * Class m200908_080244_add_table_work_public_activity_tier
 */
class m200908_080244_add_table_work_public_activity_tier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%work_public_activity_tier}}",[
		    "id"=>$this->primaryKey(11)->unsigned(),
		    "activity_id"=>$this->integer(11)->unsigned()->comment("活动id"),
		    "parent_id"=>$this->integer(11)->unsigned()->comment("上级id"),
		    "parent_fans"=>$this->text()->unsigned()->comment("任务宝参与上级id"),
		    "fans_id"=>$this->integer(11)->unsigned()->comment("任务宝参与id"),
		    "level"=>$this->text()->unsigned()->comment("级别"),
	    ],"ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝参与者级别'");
	    $this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_TIER', '{{%work_public_activity_tier}}', 'activity_id', '{{%work_public_activity}}', 'id');
	    $this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_TIER_FANS', '{{%work_public_activity_tier}}', 'fans_id', '{{%work_public_activity_fans_user}}', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200908_080244_add_table_work_public_activity_tier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200908_080244_add_table_work_public_activity_tier cannot be reverted.\n";

        return false;
    }
    */
}
