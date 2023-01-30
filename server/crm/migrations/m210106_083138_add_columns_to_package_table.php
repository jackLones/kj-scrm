<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%package}}`.
 */
class m210106_083138_add_columns_to_package_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%package}}', 'market_config_is_open', $this->tinyInteger(1)->unsigned()->notNull()->comment('是否开启营销引流客户添加数量限制的开关')->defaultValue(0));
        $this->addColumn('{{%package}}', 'fission_num', $this->integer(11)->unsigned()->notNull()->comment('裂变引流的客户上限数')->defaultValue('0'));
        $this->addColumn('{{%package}}', 'lottery_draw_num', $this->integer(11)->unsigned()->notNull()->comment('抽奖引流的客户上限数')->defaultValue('0'));
        $this->addColumn('{{%package}}', 'red_envelopes_num', $this->integer(11)->unsigned()->notNull()->comment('红包裂变的客户上限数')->defaultValue('0'));
        $this->addColumn('{{%package}}', 'tech_img_show', $this->tinyInteger(1)->unsigned()->notNull()->comment('底部版权是否展示')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%package}}', 'market_config_is_open');
        $this->dropColumn('{{%package}}', 'fission_num');
        $this->dropColumn('{{%package}}', 'lottery_draw_num');
        $this->dropColumn('{{%package}}', 'red_envelopes_num');
        $this->dropColumn('{{%package}}', 'tech_img_show');
    }
}
