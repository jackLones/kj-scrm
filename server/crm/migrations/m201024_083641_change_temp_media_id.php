<?php

use yii\db\Migration;

/**
 * Class m201024_083641_change_temp_media_id
 */
class m201024_083641_change_temp_media_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%temp_media}}', 'media_id', $this->string(80)->comment('回复内容')->after('md5'));
	    $this->createIndex("TEMP_MEDIA_MD5_KEY",'{{%temp_media}}',"md5(6)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201024_083641_change_temp_media_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201024_083641_change_temp_media_id cannot be reverted.\n";

        return false;
    }
    */
}
