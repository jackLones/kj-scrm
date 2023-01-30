<?php

use yii\db\Migration;

/**
 * Class m200107_083401_change_table_fans_statistic
 */
class m200107_083401_change_table_fans_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%fans_statistic}}', 'male', 'int(11) unsigned COMMENT \'男性\'');
		$this->addColumn('{{%fans_statistic}}', 'female', 'int(11) unsigned COMMENT \'女性\'');
		$this->addColumn('{{%fans_statistic}}', 'unknown', 'int(11) unsigned COMMENT \'未知性别\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_search', 'int(11) unsigned COMMENT \'公众号搜索\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_account_migration', 'int(11) unsigned COMMENT \'公众号迁移\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_profile_card', 'int(11) unsigned COMMENT \'名片分享\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_qr_code', 'int(11) unsigned COMMENT \'扫描二维码\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_profile_link', 'int(11) unsigned COMMENT \'图文页内名称点击\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_profile_item', 'int(11) unsigned COMMENT \'图文页右上角菜单\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_paid', 'int(11) unsigned COMMENT \'支付后关注\'');
		$this->addColumn('{{%fans_statistic}}', 'add_scene_others', 'int(11) unsigned COMMENT \'其他\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200107_083401_change_table_fans_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200107_083401_change_table_fans_statistic cannot be reverted.\n";

        return false;
    }
    */
}
