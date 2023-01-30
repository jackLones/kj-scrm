<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dialout_record}}`.
 */
class m210223_090448_create_dialout_record_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE {{%dialout_record}} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) NOT NULL COMMENT '授权的企业ID',
  `user_id` int(11) NOT NULL COMMENT '员工id',
  `external_userid` int(11) NOT NULL COMMENT '客户id',
  `exten` int(11) NOT NULL COMMENT '坐席id',
  `call_no` varchar(255) COMMENT '也不知道是啥，看着像座机号',
  `small_phone` varchar(255) COMMENT '小号',
  `called_no` varchar(255) COMMENT '被叫号码',
  `real_called` varchar(255) COMMENT '真实号码',
  `call_sheet_id` varchar(255) NOT NULL COMMENT '通话记录ID',
  `ring` int(11) NOT NULL COMMENT '通话振铃时间（话务进入呼叫中心系统的时间）',
  `ringing` int(11) NOT NULL COMMENT '被叫振铃开始时间（呼入是按座席振铃的时间,外呼按客户振铃的时间）',
  `begin` int(11) NOT NULL COMMENT '通话接通时间（双方开始通话的时间,如果被叫没接听的话为空）',
  `end` int(11) NOT NULL COMMENT '通话结束时间',
  `state` tinyint(2) NOT NULL COMMENT '接听状态：1（dealing/已接）,2（notDeal/振铃未接听）,3（leak/ivr放弃）,4（queueLeak/排队放弃）,5（blackList/黑名单）,6（voicemail/留言）,7（limit/并发限制）',
  `money` decimal(12,2) DEFAULT NULL COMMENT '通话消耗费用',
  `record_file` varchar(1024) COMMENT '通话录音文件名：用户要访问录音时,在该文件名前面加上服务器路径即可,如：FileServer/RecordFile',
  `file_server` varchar(255) COMMENT '通过FileServer中指定的地址加上RecordFile的值可以获取录音(建议字段长度内容设置为100个字符)',
  `province` varchar(255) COMMENT '目标号码的省,例如北京市。',
  `district` varchar(255) COMMENT '目标号码的市,例如北京市。',
  `hangup_part` varchar(255) DEFAULT NULL COMMENT '挂机方，字段值解释 ：agent 坐席挂机， customer 客户挂机，system 系统挂机',
  `custom_type` tinyint(1) NOT NULL COMMENT '0: 企微客户；1：非企客户；2：群客户',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='外呼坐席通话记录表';
SQL;

        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dialout_record}}');
    }
}
