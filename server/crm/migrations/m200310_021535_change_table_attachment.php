<?php

use yii\db\Migration;

/**
 * Class m200310_021535_change_table_attachment
 */
class m200310_021535_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%attachment}}', 'qy_local_path', 'text COMMENT \'企业封面地址\' ');
	    $this->addColumn('{{%attachment}}', 'qy_attach_id', 'int(11) DEFAULT \'0\' COMMENT \'企业封面id\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_021535_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_021535_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
