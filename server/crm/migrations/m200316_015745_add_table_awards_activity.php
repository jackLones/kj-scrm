<?php

use yii\db\Migration;

/**
 * Class m200316_015745_add_table_awards_activity
 */
class m200316_015745_add_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%awards_activity}}', [
			'id'            => $this->primaryKey(11)->unsigned(),
			'uid'           => $this->integer(11)->unsigned()->comment('账户id'),
			'sub_id'        => $this->integer(11)->unsigned()->comment('子账户id'),
			'title'         => $this->char(50)->unsigned()->comment('活动名称'),
			'status'        => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('状态：0 未开启 1 进行中 2 已结束'),
			'start_time'    => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")->comment('开始时间'),
			'end_time'      => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")->comment('结束时间'),
			'part_num'      => $this->integer(11)->unsigned()->comment('参数人数'),
			'visitor_num'   => $this->integer(11)->unsigned()->comment('访问量'),
			'description'   => $this->text()->comment('活动说明'),
			'style'         => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('1 梦幻紫 2 喜庆红'),
			'poster_path'   => $this->char(100)->comment('海报地址'),
			'share_title'   => $this->char(100)->comment('分享标题'),
			'apply_setting' => $this->char(100)->comment('参与设置'),
			'award_setting' => $this->char(100)->comment('中奖设置'),
			'share_setting' => $this->char(100)->comment('分享设置'),
			'is_del'        => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('0未删除1已删除'),
			'create_time'   => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'抽奖活动表\'');
		$this->createIndex('KEY_AWARDS_ACTIVITY_UID', '{{%awards_activity}}', 'uid');
		$this->createIndex('KEY_AWARDS_ACTIVITY_SUB_ID', '{{%awards_activity}}', 'sub_id');
		$this->createIndex('KEY_AWARDS_ACTIVITY_STATUS', '{{%awards_activity}}', 'status');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200316_015745_add_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200316_015745_add_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
