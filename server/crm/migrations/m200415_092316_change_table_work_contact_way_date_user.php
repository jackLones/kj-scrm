<?php

use yii\db\Migration;

/**
 * Class m200415_092316_change_table_work_contact_way_date_user
 */
class m200415_092316_change_table_work_contact_way_date_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_date_user}}','department','char(255) DEFAULT NULL COMMENT \'部门id\' AFTER `user_key`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200415_092316_change_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_092316_change_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }
    */
}
