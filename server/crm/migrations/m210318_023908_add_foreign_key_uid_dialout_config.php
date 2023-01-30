<?php

use yii\db\Migration;

/**
 * Class m210318_023908_add_foreign_key_uid_dialout_config
 */
class m210318_023908_add_foreign_key_uid_dialout_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
        alter table {{%dialout_config}} modify uid int(11) UNSIGNED NOT NULL COMMENT '用户ID';
SQL;

        $this->execute($sql);

        $this->addForeignKey('{{%foreign-key-uid}}', '{{%dialout_config}}', 'uid', '{{%user}}', 'uid', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%foreign-key-uid}}', '{{%dialout_config}}');

    }
}
