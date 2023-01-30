<?php

use yii\db\Migration;

/**
 * Class m200413_060038_change_table_attachment
 */
class m200413_060038_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%attachment}}', 'channel_type', 'tinyint(1) DEFAULT 0 COMMENT \'二维码类型：1公众号渠道二维码，2企业微信渠道活码\' ');
	    $this->addColumn('{{%attachment}}', 'channel_id', 'int(11) unsigned DEFAULT 0 COMMENT \'二维码id\' ');
	    $this->addColumn('{{%work_contact_way}}', 'local_path', 'text COMMENT \'二维码图片本地地址\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200413_060038_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200413_060038_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
