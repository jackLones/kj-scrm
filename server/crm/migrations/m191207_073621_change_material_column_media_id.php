<?php

use yii\db\Migration;

/**
 * Class m191207_073621_change_material_column_media_id
 */
class m191207_073621_change_material_column_media_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->alterColumn('{{%material}}', 'media_id', 'char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'新增素材的media_id\' AFTER `author_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191207_073621_change_material_column_media_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191207_073621_change_material_column_media_id cannot be reverted.\n";

        return false;
    }
    */
}
