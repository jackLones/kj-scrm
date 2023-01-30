<?php

use yii\db\Migration;

/**
 * Class m201022_165415_change_work_user_fans_user
 */
class m201022_165415_change_work_user_fans_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_public_activity_fans_user}}', 'poster_path', $this->string(255)->comment('生成海报素材地址')->after('is_form'));
	    $this->alterColumn('{{%work_public_activity_fans_user}}', 'parent_id', $this->text()->comment('上级id')->after('level'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_165415_change_work_user_fans_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_165415_change_work_user_fans_user cannot be reverted.\n";

        return false;
    }
    */
}
