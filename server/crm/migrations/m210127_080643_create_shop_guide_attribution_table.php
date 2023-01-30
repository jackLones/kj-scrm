<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_guide_attribution}}`.
 */
class m210127_080643_create_shop_guide_attribution_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_guide_attribution}}', [
            'id'                     =>$this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'                =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'role'                   =>$this->string(200)->notNUll()->defaultValue('')->comment('导购所属角色类型'),
            'mode_type'              =>$this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('导购管理模式:1顾客对应多导购 2导购锁定+有效期'),
            'priority'               =>$this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('扫码添加时:扫码添加条件优先级:1每个⻔店⾸个添加的企微好友则以⻔店第⼀个添加的⼈为这个顾客的导购;2⾸个添加的企微好友成为导购;3所有的添加的企微好友都成为导购;针对的是模式 mode_type = 1'),
            'is_consumption'         =>$this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('顾客消费时（订单所属⻔店的企微好友）0不处理 1 订单尝试关联员⼯且⾃动添加该员⼯为导购;针对的是模式 mode_type = 1'),
            'in_page_lock'           =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('进⼊⻚⾯锁定的天数 针对的是模式 mode_type = 2'),
            'add_friend_lock'        =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('添加好友锁定天数 针对的是模式 mode_type = 2'),
            'consumption_amount_lock'=>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('消费锁定天数 针对的是模式 mode_type=2'),
            'performance_related'    =>$this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('业绩关联设置 0 默认 不关联 1 关联员⼯，没有则不处理 2 优先给关联员⼯，没有则归属⻔店⾸个员⼯'),
            'add_time'               =>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'            =>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_GUIDE_ATTRIBUTION_CORP_ID', '{{%shop_guide_attribution}}', 'corp_id');
        $this->addCommentOnTable('{{%shop_guide_attribution}}', '导购归属设置');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_guide_attribution}}');
        return false;
    }
}
