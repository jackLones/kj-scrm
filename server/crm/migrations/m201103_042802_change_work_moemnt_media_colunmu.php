<?php

use yii\db\Migration;

/**
 * Class m201103_042802_change_work_moemnt_media_colunmu
 */
class m201103_042802_change_work_moemnt_media_colunmu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn("{{%work_moment_media}}","local_path",$this->text()->comment("媒体本地位置")->after("sort"));
	    $this->addColumn("{{%work_moment_media}}","s_local_path",$this->text()->comment("缩略图地址")->after("local_path"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201103_042802_change_work_moemnt_media_colunmu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201103_042802_change_work_moemnt_media_colunmu cannot be reverted.\n";

        return false;
    }
    */
}
