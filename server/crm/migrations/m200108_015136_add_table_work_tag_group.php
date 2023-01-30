<?php

use yii\db\Migration;

/**
 * Class m200108_015136_add_table_work_tag_group
 */
class m200108_015136_add_table_work_tag_group extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_tag_group}}', [
			'id'         => $this->primaryKey(11)->unsigned(),
			'corp_id'    => $this->integer(11)->unsigned()->comment('授权的企业ID'),
			'group_name' => $this->char(32)->comment('标签分组名称，长度限制为32个字以内（汉字或英文字母），标签分组名不可与其他标签组重名'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信标签分组表\'');

		$this->addForeignKey('KEY_WORK_TAG_GROUP_CORPID', '{{%work_tag_group}}', 'corp_id', '{{%work_corp}}', 'id');

	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200108_015136_add_table_work_tag_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200108_015136_add_table_work_tag_group cannot be reverted.\n";

        return false;
    }
    */
}
