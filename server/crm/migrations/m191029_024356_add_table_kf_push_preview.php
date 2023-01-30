<?php

use yii\db\Migration;

/**
 * Class m191029_024356_add_table_kf_push_preview
 */
class m191029_024356_add_table_kf_push_preview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%kf_push_preview}}', [
		    'id'          => $this->primaryKey(11)->unsigned(),
		    'fans_id'     => $this->integer(11)->unsigned()->comment('粉丝ID'),
		    'random'      => $this->integer(11)->unsigned()->comment('发送随机数'),
		    'expire_time' => $this->integer(11)->unsigned()->comment('过期时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'客服消息预览记录表\'');

	    $this->createIndex('KEY_KF_PUSH_FANSID', '{{%kf_push_preview}}', 'fans_id');

	    $this->addForeignKey('KEY_KF_PUSH_FANSID', '{{%kf_push_preview}}', 'fans_id', '{{%fans}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191029_024356_add_table_kf_push_preview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191029_024356_add_table_kf_push_preview cannot be reverted.\n";

        return false;
    }
    */
}
