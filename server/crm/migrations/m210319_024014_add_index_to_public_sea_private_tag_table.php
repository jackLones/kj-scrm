<?php

use yii\db\Migration;

/**
 * Class m210319_024014_change_columns_to_public_sea_private_tag_table
 */
class m210319_024014_add_index_to_public_sea_private_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex("tag_id_idx", "{{%public_sea_private_tag}}", 'tag_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex("tag_id_idx", "{{%public_sea_private_tag}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_024014_change_columns_to_public_sea_private_tag_table cannot be reverted.\n";

        return false;
    }
    */
}
