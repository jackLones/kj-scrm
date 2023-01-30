<?php

use yii\db\Migration;

/**
 * Class m201030_061025_change_table_wokr_public_activity_poster_two_columns
 */
class m201030_061025_change_table_wokr_public_activity_poster_two_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'heard_width', $this->decimal(11, 2)->unsigned()->comment('头像宽')->after('is_heard'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'heard_height', $this->decimal(11, 2)->unsigned()->comment('头像高')->after('heard_width'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'heard_top', $this->decimal(11, 2)->unsigned()->comment('头像距离顶部')->after('heard_type'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'heard_left', $this->decimal(11, 2)->unsigned()->comment('头像距离左边')->after('heard_top'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'code_width', $this->decimal(11, 2)->unsigned()->comment('二维码宽')->after('heard_ratio'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'code_height', $this->decimal(11, 2)->unsigned()->comment('二维码高')->after('code_width'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'code_top', $this->decimal(11, 2)->unsigned()->comment('二维码距离顶部')->after('code_height'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'code_left', $this->decimal(11, 2)->unsigned()->comment('二维码距离左边')->after('code_top'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'font_top', $this->decimal(11, 2)->unsigned()->comment('字体距离顶部')->after('is_font'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'font_left', $this->decimal(11, 2)->unsigned()->comment('字体距离左边')->after('font_top'));
	    $this->alterColumn('{{%work_public_activity_poster_config}}', 'font_size', $this->decimal(11, 2)->unsigned()->comment('字体大小')->after('font_left'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201030_061025_change_table_wokr_public_activity_poster_two_columns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201030_061025_change_table_wokr_public_activity_poster_two_columns cannot be reverted.\n";

        return false;
    }
    */
}
