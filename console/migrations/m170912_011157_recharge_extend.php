<?php

use yii\db\Migration;

class m170912_011157_recharge_extend extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%recharge_extend}}', [
            'order_id' => $this->primaryKey()->notNull(),
            'order_no' => $this->string(32)->notNull()->unique(),
            'uid' => $this->integer()->notNull(),
            'use' => $this->string(32)->notNull()->comment('用途'),// 用途
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%recharge_extend}}');
    }
}
