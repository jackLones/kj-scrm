<?php

use yii\db\Migration;

/**
 * Class m200615_093822_change_table_user
 */
class m200615_093822_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'package_id', 'int(11) NOT NULL DEFAULT 0 COMMENT \'套餐id\'');
	    $this->addColumn('{{%user}}', 'package_time', 'int(11) NOT NULL DEFAULT 0 COMMENT \'套餐时长\'');
	    $this->addColumn('{{%user}}', 'time_type', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'套餐时长类型:1日2月3年\'');
	    $this->addColumn('{{%user}}', 'end_time', 'int(11) NOT NULL DEFAULT 0 COMMENT \'套餐失效时间\'');
	    $this->addColumn('{{%user}}', 'login_time', 'int(11) NOT NULL DEFAULT 0 COMMENT \'最后登录时间\'');
	    $this->addColumn('{{%user}}', 'is_merchant', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'是否入驻1是0否\'');
	    $this->addColumn('{{%user}}', 'merchant_time', 'int(11) NOT NULL DEFAULT 0 COMMENT \'入驻时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200615_093822_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_093822_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
