<?php

use yii\db\Migration;

/**
 * Class m210412_122657_change_table_work_tag
 */
class m210412_122657_change_table_work_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    \app\models\AttachmentTagGroup::uptAttachmentTag();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210412_122657_change_table_work_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210412_122657_change_table_work_tag cannot be reverted.\n";

        return false;
    }
    */
}
