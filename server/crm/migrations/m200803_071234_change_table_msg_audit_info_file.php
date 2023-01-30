<?php

use yii\db\Migration;

/**
 * Class m200803_071234_change_table_msg_audit_info_file
 */
class m200803_071234_change_table_msg_audit_info_file extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_msg_audit_info_emotion}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束'));
		$this->addColumn('{{%work_msg_audit_info_file}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束'));
		$this->addColumn('{{%work_msg_audit_info_image}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束'));
		$this->addColumn('{{%work_msg_audit_info_video}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束'));
		$this->addColumn('{{%work_msg_audit_info_voice}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束'));
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200803_071234_change_table_msg_audit_info_file cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200803_071234_change_table_msg_audit_info_file cannot be reverted.\n";

        return false;
    }
    */
}
