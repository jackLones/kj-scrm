<?php

use yii\db\Migration;

/**
 * Class m200110_085334_change_external_user_follow_user
 */
class m200110_085334_change_external_user_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->alterColumn('{{%work_external_contact_follow_user}}', 'description', 'varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'该成员对此外部联系人的描述\' AFTER `remark`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200110_085334_change_external_user_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200110_085334_change_external_user_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
