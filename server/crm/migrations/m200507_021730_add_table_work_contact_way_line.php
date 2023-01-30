<?php

use yii\db\Migration;

/**
 * Class m200507_021730_add_table_work_contact_way_line
 */
class m200507_021730_add_table_work_contact_way_line extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_line}}', [
			'id'              => $this->primaryKey(11)->unsigned(),
			'way_id'          => $this->integer(11)->unsigned()->comment('渠道二维码ID'),
			'type'            => $this->tinyInteger(1)->unsigned()->comment('1新增2客户删除员工3员工删除客户'),
			'external_userid' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
			'user_id'         => $this->integer(11)->unsigned()->comment('成员ID'),
			'create_time'     => $this->timestamp()->comment('操作时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码行为轨迹表\'');
		$this->addForeignKey('KEY_WORK_CONTACT_WAY_LINE_WAY_ID', '{{%work_contact_way_line}}', 'way_id', '{{%work_contact_way}}', 'id');
		$this->addForeignKey('KEY_WORK_CONTACT_WAY_LINE_EXTERNAL_USERID', '{{%work_contact_way_line}}', 'external_userid', '{{%work_external_contact}}', 'id');
		$this->addForeignKey('KEY_WORK_CONTACT_WAY_LINE_USER_ID', '{{%work_contact_way_line}}', 'user_id', '{{%work_user}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200507_021730_add_table_work_contact_way_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200507_021730_add_table_work_contact_way_line cannot be reverted.\n";

        return false;
    }
    */
}
