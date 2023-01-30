<?php

use yii\db\Migration;

/**
 * Class m200310_032610_change_table_attachment
 */
class m200310_032610_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%attachment}}', 'isMasterAccount', 'int(11) UNSIGNED NULL DEFAULT 1 COMMENT \'1主账户2子账户\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_032610_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_032610_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
