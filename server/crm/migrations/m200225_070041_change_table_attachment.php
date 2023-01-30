<?php

use yii\db\Migration;

/**
 * Class m200225_070041_change_table_attachment
 */
class m200225_070041_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%attachment}}", "attach_id", "int(11) DEFAULT 0 COMMENT '封面图片id'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200225_070041_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_070041_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
