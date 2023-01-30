<?php

use yii\db\Migration;

/**
 * Class m190917_013807_add_table_fans_tags
 */
class m190917_013807_add_table_fans_tags extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->createTable('{{%fans_tags}}', [
    		'id' => $this->primaryKey(11)->unsigned(),
		    'fans_id' => $this->integer(11)->unsigned()->comment('粉丝ID'),
		    'tags_id' => $this->integer(11)->unsigned()->comment('标签ID'),
		    'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间')
	    ],'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'粉丝标签表\'');

    	$this->createIndex('KEY_FANS_TAGS_FANSID', '{{%fans_tags}}', 'fans_id');
    	$this->createIndex('KEY_FANS_TAGS_TAGSID', '{{%fans_tags}}', 'tags_id');

    	$this->addForeignKey('KEY_FANS_TAGS_FANSID', '{{%fans_tags}}', 'fans_id', '{{%fans}}', 'id');
    	$this->addForeignKey('KEY_FANS_TAGS_TAGSID', '{{%fans_tags}}', 'tags_id', '{{%tags}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_013807_add_table_fans_tags cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_013807_add_table_fans_tags cannot be reverted.\n";

        return false;
    }
    */
}
