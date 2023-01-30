<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%youzan_shop}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m200527_080102_drop_uid_columns_from_youzan_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%youzan_shop}}', 'uid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%youzan_shop}}', 'uid', $this->integer(11)->unsigned()->comment('用户ID'));

        // creates index for column `uid`
        $this->createIndex(
            '{{%idx-youzan_shop-uid}}',
            '{{%youzan_shop}}',
            'uid'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-youzan_shop-uid}}',
            '{{%youzan_shop}}',
            'uid',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }
}
