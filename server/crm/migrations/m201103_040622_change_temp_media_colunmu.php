<?php

use yii\db\Migration;

/**
 * Class m201103_040622_change_temp_media_colunmu
 */
class m201103_040622_change_temp_media_colunmu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn("{{%temp_media}}","s_local_path",$this->text()->comment("缩略图地址"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201103_040622_change_temp_media_colunmu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201103_040622_change_temp_media_colunmu cannot be reverted.\n";

        return false;
    }
    */
}
