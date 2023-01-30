<?php

use yii\db\Migration;

/**
 * Class m201019_034848_change_table_work_department
 */
class m201019_034848_change_table_work_department extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%work_department}}', 'name', 'varchar(255)  DEFAULT NULL COMMENT \'部门名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示部门名称\'');
		$this->alterColumn('{{%work_department}}', 'name_en', 'varchar(255)  DEFAULT NULL COMMENT \'英文名称\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_034848_change_table_work_department cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_034848_change_table_work_department cannot be reverted.\n";

        return false;
    }
    */
}
