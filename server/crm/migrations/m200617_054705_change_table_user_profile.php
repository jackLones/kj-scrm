<?php

use yii\db\Migration;

/**
 * Class m200617_054705_change_table_user_profile
 */
class m200617_054705_change_table_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user_profile}}', 'province', 'int(11) NOT NULL DEFAULT 0 COMMENT \'省份\'');
	    $this->addColumn('{{%user_profile}}', 'city', 'int(11) NOT NULL DEFAULT 0 COMMENT \'城市\'');
	    $this->addColumn('{{%user_profile}}', 'email', 'varchar(100) NOT NULL DEFAULT \'\' COMMENT \'邮箱\'');
	    $this->addColumn('{{%user_profile}}', 'qq', 'varchar(100) NOT NULL DEFAULT \'\' COMMENT \'qq\'');
	    $this->addColumn('{{%user_profile}}', 'weixin', 'varchar(100) NOT NULL DEFAULT \'\' COMMENT \'微信\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200617_054705_change_table_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200617_054705_change_table_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
