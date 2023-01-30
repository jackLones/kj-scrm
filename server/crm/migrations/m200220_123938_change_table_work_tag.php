<?php

use yii\db\Migration;

/**
 * Class m200220_123938_change_table_work_tag
 */
class m200220_123938_change_table_work_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_tag}}', 'tagid', 'varchar(50)  COMMENT \'标签id，非负整型，指定此参数时新增的标签会生成对应的标签id，不指定时则以目前最大的id自增\' AFTER `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200220_123938_change_table_work_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_123938_change_table_work_tag cannot be reverted.\n";

        return false;
    }
    */
}
