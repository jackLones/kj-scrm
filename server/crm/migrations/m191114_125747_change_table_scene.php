<?php

use yii\db\Migration;

/**
 * Class m191114_125747_change_table_scene
 */
class m191114_125747_change_table_scene extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%scene}}', 'local_path', 'text COMMENT \'二维码图片本地地址\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191114_125747_change_table_scene cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191114_125747_change_table_scene cannot be reverted.\n";

        return false;
    }
    */
}
