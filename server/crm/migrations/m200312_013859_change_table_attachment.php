<?php

use yii\db\Migration;

/**
 * Class m200312_013859_change_table_attachment
 */
class m200312_013859_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%attachment}}', 's_local_path', 'text COMMENT \'附件本地缩略图地址\' AFTER `local_path`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200312_013859_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200312_013859_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
