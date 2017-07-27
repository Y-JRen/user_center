<?php

use yii\db\Migration;

class m170718_031244_create_table_user_oauth extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_oauth}}', [
            'id' => $this->primaryKey()->unsigned(),
            'open_id' => $this->string(32)->notNull(),
            'type' => $this->boolean()->notNull()->comment('1:微信;2:QQ;3:Sina'),
            'uid' => $this->integer()->notNull(),
            'info' => $this->string()->notNull()->defaultValue(''),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 添加联合唯一索引
        $this->createIndex('open_id_type', '{{%user_oauth}}', ['open_id', 'type'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_oauth}}');
    }
}
