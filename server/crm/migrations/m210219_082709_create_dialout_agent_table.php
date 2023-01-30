<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dialout_agent}}`.
 */
class m210219_082709_create_dialout_agent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%dialout_agent}}', [
            'id' => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id' => $this->integer(11)->unsigned()->comment('授权的企业ID'),
            'exten' => $this->integer(11)->unsigned()->comment('坐席id'),
            'small_phone'=>$this->char(180)->comment('小号')->defaultValue('')->notNull(),
            'start_time' => $this->timestamp()->defaultValue('2000-01-01 00:00:00')->comment('坐席可用开始时间'),
            'expire' => $this->timestamp()->defaultValue('2000-01-01 00:00:00')->comment('坐席到期时间'),
            'enable' => $this->integer(11)->unsigned()->comment('是否开通，1已开通；0未开通'),
            'status' => $this->integer(11)->unsigned()->comment('是否可用，1：可用，2：不可用'),
            'state' => $this->integer(11)->unsigned()->comment('状态，1登出/置闲；2登录/置忙'),
            'state_change_time' => $this->integer(11)->unsigned()->comment('状态改变时间'),
            'last_use_user' => $this->integer(11)->unsigned()->comment('最后一个使用该坐席的员工'),
            'create_time' => $this->timestamp()->defaultValue('2000-01-01 00:00:00')->comment('创建日期'),
        ]);
        $this->addCommentOnTable('{{%dialout_agent}}', '外呼坐席开通表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dialout_agent}}');
    }
}
