<?php

use yii\db\Migration;

/**
 * Class m210208_092628_update_way_redpacket_add_num
 */
class m210208_092628_update_way_redpacket_add_num extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    \app\models\WorkContactWayRedpacket::updateAddNum();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210208_092628_update_way_redpacket_add_num cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_092628_update_way_redpacket_add_num cannot be reverted.\n";

        return false;
    }
    */
}
