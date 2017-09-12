<?php

use yii\db\Migration;

class m170904_023016_pre_order extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=881130';
        }

        $this->createTable('{{%pre_order}}', [
            'id' => $this->primaryKey()->notNull(),
            'uid' => $this->integer()->notNull(),
            'platform_order_id' => $this->string(32)->defaultValue('')->notNull()->comment('平台订单号'),
            'order_id' => $this->string(32)->notNull()->unique()->comment('用户中心单号'),
            'order_subtype' => $this->string(32)->notNull()->comment('子类型'),
            'desc' => $this->string()->notNull()->comment('订单简述'),
            'amount' => $this->decimal(10, 2)->notNull()->comment('订单金额'),
            'remark' => $this->text()->notNull()->defaultValue('')->comment('备注'),
            'status' => $this->boolean()->notNull()->comment('状态'),
            'platform' => $this->boolean()->notNull()->comment('平台来源'),
            'quick_pay' => $this->boolean()->notNull()->defaultValue(0)->comment('快捷支付，完成后需要消费;0：不需要消费;1：需要消费'),
            'notice_status' => $this->boolean()->notNull()->comment('通知状态；1:需要通知;2:通知失败;3:通知成功;4:不需要通知'),
            'notice_platform_param' => $this->string()->notNull()->defaultValue('')->comment('回调通知参数'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->comment('最后更新时间'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pre_order}}');
    }
}
