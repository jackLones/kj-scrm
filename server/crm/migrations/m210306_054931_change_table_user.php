<?php

use yii\db\Migration;

/**
 * Class m210306_054931_change_table_user
 */
class m210306_054931_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%public_sea_customer}}', 'is_del', 'tinyint(1) DEFAULT 0 COMMENT \'是否已删除\'');
	    $this->addColumn('{{%user}}', 'is_sync_image', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'是否同步图片\'');
	    $this->addColumn('{{%user}}', 'is_sync_voice', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'是否同步音频\'');
	    $this->addColumn('{{%user}}', 'is_sync_video', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'是否同步视频\'');
	    $this->addColumn('{{%user}}', 'is_sync_news', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'是否同步图文\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210306_054931_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210306_054931_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
