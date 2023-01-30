<?php

use yii\db\Migration;

/**
 * Class m200309_074907_add_table_group_sort
 */
class m200309_074907_add_table_group_sort extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%group_sort}}', [
			'id'              => $this->primaryKey(11)->unsigned(),
			'source'          => $this->tinyInteger(1)->unsigned()->comment('1企业标签2客户标签3内容引擎'),
			'sort_ids'        => $this->string(255)->comment('排序需要的id'),
			'isMasterAccount' => $this->tinyInteger(1)->comment('1主账户2子账户'),
			'sub_id'          => $this->integer(11)->unsigned()->comment('当前登录账户id'),
			'create_time'     => $this->timestamp()->comment('创建时间')
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'分组排序表\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200309_074907_add_table_group_sort cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200309_074907_add_table_group_sort cannot be reverted.\n";

        return false;
    }
    */
}
