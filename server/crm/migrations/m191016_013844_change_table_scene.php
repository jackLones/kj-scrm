<?php

use yii\db\Migration;

/**
 * Class m191016_013844_change_table_scene
 */
class m191016_013844_change_table_scene extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%scene}}', 'scene_id', 'int(11) NOT NULL COMMENT \'二维码场景值ID，临时二维码时从100001开始的整型，永久二维码时最大值为100000（目前参数只支持1--100000）\' AFTER `action_name` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191016_013844_change_table_scene cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191016_013844_change_table_scene cannot be reverted.\n";

        return false;
    }
    */
}
