<?php

use yii\db\Migration;

/**
 * Class m191028_031317_change_table_kf_push_msg
 */
class m191028_031317_change_table_kf_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%kf_push_msg}}', 'target_num', 'int(11) unsigned DEFAULT \'0\' COMMENT \'预计发送粉丝数 \' ');
	    $this->addColumn('{{%kf_push_msg}}', 'fans_num', 'int(11) unsigned DEFAULT \'0\' COMMENT \'发送成功粉丝数 \' ');
	    $this->addColumn('{{%kf_push_msg}}', 'queue_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'队列id \' ');
	    $this->addColumn('{{%kf_push_msg}}', 'status', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'状态 0未发送 1已发送 2发送失败 \' ');
	    $this->addColumn('{{%kf_push_msg}}', 'is_del', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'状态 0未删除 1已删除 \' ');
	    $this->alterColumn('{{%kf_push_msg}}', 'content', 'text COMMENT \'对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID\' AFTER `msg_type`');
	    $this->alterColumn('{{%kf_push_msg}}', 'push_type', 'tinyint(1) unsigned DEFAULT \'0\'  COMMENT \'发送类别：1：标签、2：全部\' AFTER `content_url`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191028_031317_change_table_kf_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191028_031317_change_table_kf_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
