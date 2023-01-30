<?php

use yii\db\Migration;

/**
 * Class m201022_150159_change_table_public_activity_call_template_old
 */
class m201022_150159_change_table_public_activity_call_template_old extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_public_activity_config_call}}', 'template_old', $this->text()->comment("模板原始内容")->after("template_context"));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_150159_change_table_public_activity_call_template_old cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_150159_change_table_public_activity_call_template_old cannot be reverted.\n";

        return false;
    }
    */
}
