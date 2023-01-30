<?php

use yii\db\Migration;

/**
 * Class m200817_074020_change_table_fans
 */
class m200817_074020_change_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans}}', 'external_userid', 'int(11) UNSIGNED NULL COMMENT \'外部联系人ID\'');
	    $this->addForeignKey("KEY_FANS_EXTERNAL_USERID", "{{%fans}}", "external_userid", "{{%work_external_contact}}", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200817_074020_change_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200817_074020_change_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
