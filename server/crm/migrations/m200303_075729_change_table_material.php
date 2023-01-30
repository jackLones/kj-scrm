<?php

use yii\db\Migration;

/**
 * Class m200303_075729_change_table_material
 */
class m200303_075729_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%material}}', 'update_time', 'int(11) DEFAULT 0 COMMENT \'修改时间\' AFTER `create_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200303_075729_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200303_075729_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
