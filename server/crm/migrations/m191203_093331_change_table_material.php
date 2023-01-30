<?php

use yii\db\Migration;

/**
 * Class m191203_093331_change_table_material
 */
class m191203_093331_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%material}}', 'article_sort', 'varchar(125) DEFAULT \'\' COMMENT \'图文素材的排序，多图文时用逗号分割\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191203_093331_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191203_093331_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
