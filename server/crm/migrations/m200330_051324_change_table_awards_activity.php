<?php

use yii\db\Migration;

/**
 * Class m200330_051324_change_table_awards_activity
 */
class m200330_051324_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%awards_activity}}', 'pic_rule', 'text COMMENT \'图片规则\' AFTER `style`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200330_051324_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200330_051324_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
