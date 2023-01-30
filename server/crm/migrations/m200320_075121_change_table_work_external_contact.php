<?php

use yii\db\Migration;

/**
 * Class m200320_075121_change_table_work_external_contact
 */
class m200320_075121_change_table_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_external_contact}}', 'name', 'varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT \'外部联系人的姓名或别名\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200320_075121_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200320_075121_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
