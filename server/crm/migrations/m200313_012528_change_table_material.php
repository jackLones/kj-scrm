<?php

use yii\db\Migration;

/**
 * Class m200313_012528_change_table_material
 */
class m200313_012528_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%material}}', 's_local_path', 'text COMMENT \'素材本地缩略图地址\' AFTER `local_path`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200313_012528_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200313_012528_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
