<?php

use yii\db\Migration;

/**
 * Class m200110_014804_change_external_contact_columns
 */
class m200110_014804_change_external_contact_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->dropForeignKey('KEY_WORK_EXTERNAL_CONTACT_WAYID', '{{%work_external_contact}}');

    	$this->alterColumn('{{%work_external_contact}}', 'way_id', 'char(64) NULL DEFAULT NULL COMMENT \'联系我配置ID\' AFTER `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200110_014804_change_external_contact_columns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200110_014804_change_external_contact_columns cannot be reverted.\n";

        return false;
    }
    */
}
