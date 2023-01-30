<?php

use yii\db\Migration;

/**
 * Class m200205_070808_change_table_kf_push_msg
 */
class m200205_070808_change_table_kf_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%kf_push_msg}}', 'attachment_id', 'int(11) unsigned DEFAULT NULL COMMENT \'附件id\' AFTER `material_id`');
	    $this->addColumn('{{%high_level_push_msg}}', 'attachment_id', 'int(11) unsigned DEFAULT NULL COMMENT \'附件id\' AFTER `material_id`');
	    $this->addColumn('{{%reply_info}}', 'attachment_id', 'int(11) unsigned DEFAULT NULL COMMENT \'附件id\' AFTER `material_id`');
	    $this->addColumn('{{%reply_info}}', 'is_sync', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否同步文件柜\' AFTER `status`');
	    $this->addColumn('{{%reply_info}}', 'is_use', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否是自定义\' AFTER `status`');
	    $this->addColumn('{{%reply_info}}', 'attach_id', 'int(11) unsigned DEFAULT NULL COMMENT \'同步文件柜的id\' AFTER `is_sync`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_070808_change_table_kf_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_070808_change_table_kf_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
