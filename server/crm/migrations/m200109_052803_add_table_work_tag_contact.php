<?php

use yii\db\Migration;

/**
 * Class m200109_052803_add_table_work_tag_contact
 */
class m200109_052803_add_table_work_tag_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_tag_contact}}', [
			'id'         => $this->primaryKey(11)->unsigned(),
			'tag_id'     => $this->integer(11)->unsigned()->comment('授权的企业的标签ID'),
			'contact_id' => $this->integer(11)->unsigned()->comment('授权的企业的成员ID'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信标签外部联系人表\'');

		$this->addForeignKey('KEY_WORK_TAG_CONTACT_TAG_ID', '{{%work_tag_contact}}', 'tag_id', '{{%work_tag}}', 'id');
		$this->addForeignKey('KEY_WORK_TAG_CONTACT_ID', '{{%work_tag_contact}}', 'contact_id', '{{%work_external_contact}}', 'id');

	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200109_052803_add_table_work_tag_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200109_052803_add_table_work_tag_contact cannot be reverted.\n";

        return false;
    }
    */
}
