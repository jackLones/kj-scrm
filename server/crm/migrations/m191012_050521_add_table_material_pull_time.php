<?php

use yii\db\Migration;

/**
 * Class m191012_050521_add_table_material_pull_time
 */
class m191012_050521_add_table_material_pull_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%material_pull_time}}', [
		    'id'            => $this->primaryKey(11)->unsigned(),
		    'author_id'     => $this->integer(11)->unsigned()->comment('公众号ID'),
		    'material_type' => $this->tinyInteger(1)->comment('素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、缩略图（thumb)、6：参数二维码（scene）'),
		    'pull_time'     => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('最后拉取日期'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'素材拉取记录表\'');

	    $this->createIndex('KEY_MATERIAL_PULL_TIME_AUTHORID', '{{%material_pull_time}}', 'author_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191012_050521_add_table_material_pull_time cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191012_050521_add_table_material_pull_time cannot be reverted.\n";

        return false;
    }
    */
}
