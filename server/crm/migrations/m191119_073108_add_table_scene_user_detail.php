<?php

use yii\db\Migration;

/**
 * Class m191119_073108_add_table_scene_user_detail
 */
class m191119_073108_add_table_scene_user_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%scene_user_detail}}', [
		    'id'        => $this->primaryKey(11)->unsigned(),
		    'scene_id'  => $this->integer(11)->unsigned()->comment('参数二维码ID'),
		    'fans_id'   => $this->integer(11)->unsigned()->comment('粉丝ID'),
		    'is_new'    => $this->tinyInteger(11)->unsigned()->comment('是否是新粉丝'),
		    'scan_time' => $this->timestamp()->comment('扫码时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道二维码用户记录表\'');
	    $this->createIndex('KEY_SCENE_USER_DETAIL_SCENEID', '{{%scene_user_detail}}', 'scene_id');
	    $this->addForeignKey('KEY_SCENE_USER_DETAIL_SCENEID', '{{%scene_user_detail}}', 'scene_id', '{{%scene}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191119_073108_add_table_scene_user_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191119_073108_add_table_scene_user_detail cannot be reverted.\n";

        return false;
    }
    */
}
