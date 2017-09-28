<?php

use yii\db\Migration;

class m170830_075644_platform_order extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%platform_order}}', [
            'id' => $this->primaryKey()->notNull(),
            'uid' => $this->integer()->notNull(),
            'platform_order_id' => $this->integer()->notNull(),
            'platform_order_no' => $this->string()->notNull(),
            'pro_type' => $this->string()->notNull(),
            'pro_name' => $this->string()->notNull(),
            'status' => $this->boolean()->notNull(),
            'create_time' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%platform_order}}');
    }
}