<?php

use yii\db\Migration;

/**
 * Class m191021_061203_change_menu
 */
class m191021_061203_change_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->update('{{%menu}}', ['icon'=>'hdd','key' => 'material', 'link' => 'material'], ['id' => 5]);
	    $this->delete('{{%menu}}', ['id' => 15]);
	    $this->delete('{{%menu}}', ['id' => 16]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_061203_change_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_061203_change_menu cannot be reverted.\n";

        return false;
    }
    */
}
