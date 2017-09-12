<?php

use yii\db\Migration;

class m170911_073228_freeze_record extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%freeze_record}}', [
            'id' => $this->primaryKey()->notNull(),
            'order_no' => $this->string(32)->notNull()->unique(),
            'uid' => $this->integer()->notNull(),
            'use' => $this->string(32)->notNull()->comment('用途'),// 用途
            'amount' => $this->decimal(10,2)->notNull(),
            'status' => $this->boolean()->notNull()->comment('1:冻结失败;2:冻结成功;3:解冻成功;4:解冻失败'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%freeze_record}}');
    }
}
