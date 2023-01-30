<?php

use yii\db\Migration;

/**
 * Class m200813_030110_add_table_work_public_activity_poster_config
 */
class m200813_030110_add_table_work_public_activity_poster_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%work_public_activity_poster_config}}",[
		    "id"=>$this->primaryKey(11)->unsigned(),
		    "activity_id"=>$this->integer(11)->unsigned()->comment("活动id"),
		    "heard_type"=>$this->integer(1)->notNull()->defaultValue(1)->unsigned()->comment("1正方形2圆形"),
		    "is_heard"=>$this->integer(1)->notNull()->unsigned()->comment("是否使用头像"),
		    "heard_width"=>$this->integer(4)->notNull()->unsigned()->comment("头像宽"),
		    "heard_height"=>$this->integer(4)->notNull()->unsigned()->comment("头像高"),
		    "heard_top"=>$this->integer(4)->notNull()->unsigned()->comment("头像距离顶部"),
		    "heard_left"=>$this->integer(4)->notNull()->unsigned()->comment("头像距离左边"),
		    "heard_ratio"=>$this->decimal(1,1)->notNull()->unsigned()->comment("头像比例"),
		    "code_width"=>$this->integer(4)->notNull()->unsigned()->comment("二维码宽"),
		    "code_height"=>$this->integer(4)->notNull()->unsigned()->comment("二维码高"),
		    "code_top"=>$this->integer(4)->notNull()->unsigned()->comment("二维码距离顶部"),
		    "code_left"=>$this->integer(4)->notNull()->unsigned()->comment("二维码距离左边"),
		    "code_ratio"=>$this->decimal(1,1)->notNull()->unsigned()->comment("二维码比列"),
		    "is_font"=>$this->integer(1)->notNull()->unsigned()->comment("是否使用名称"),
		    "font_top"=>$this->integer(4)->notNull()->unsigned()->comment("字体距离顶部"),
		    "font_left"=>$this->integer(4)->notNull()->unsigned()->comment("字体距离左边"),
		    "font_size"=>$this->integer(2)->notNull()->unsigned()->comment("字体大小"),
		    "font_color"=>$this->char(30)->notNull()->unsigned()->comment("字体颜色"),
		    "background_url"=>$this->string(255)->notNull()->unsigned()->comment("背景地址"),
		    "create_time"=>$this->integer(11)->notNull()->unsigned()->comment("修改时间"),
		    "update_time"=>$this->integer(11)->notNull()->unsigned()->comment("修改时间"),
	    ],"ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动海报配置表'");

	    $this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_POSTER_CONFIG_ID', '{{%work_public_activity_poster_config}}', 'activity_id', '{{%work_public_activity}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200813_030110_add_table_work_public_activity_poster_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200813_030110_add_table_work_public_activity_poster_config cannot be reverted.\n";

        return false;
    }
    */
}
