<?php

use yii\db\Migration;

/**
 * Class m200414_091714_add_table_work_contact_way_date_user
 */
class m200414_091714_add_table_work_contact_way_date_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_date_user}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'date_id'     => $this->integer(11)->unsigned()->notNull()->comment('企业微信联系我表ID'),
			'time'        => $this->char(32)->comment('具体时间'),
			'user_key'    => $this->char(255)->comment('用户选择的key值'),
			'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码日期成员表\'');

		$this->addForeignKey('KEY_WORK_CONTACT_WAY_DATE_USER_DATE_ID', '{{%work_contact_way_date_user}}', 'date_id', '{{%work_contact_way_date}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_091714_add_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_091714_add_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }
    */
}
