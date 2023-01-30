<?php

use yii\db\Migration;

/**
 * Class m200327_095622_add_table_awards_records
 */
class m200327_095622_add_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->dropColumn('{{%awards_records}}', 'openid');
		$this->dropColumn('{{%awards_records}}', 'external_id');
		$this->dropColumn('{{%awards_records}}', 'config_id');
		$this->dropColumn('{{%awards_records}}', 'qr_code');
		$this->dropColumn('{{%awards_records}}', 'state');
		$this->dropColumn('{{%awards_records}}', 'phone');
		$this->dropColumn('{{%awards_records}}', 'num');
		$this->dropColumn('{{%awards_records}}', 'receive_time');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_095622_add_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_095622_add_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
