<?php

use yii\db\Migration;

class m170714_092712_create_lakala extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lakala_static}}', [
            'id' => $this->primaryKey()->notNull(),
            'pos_id' => $this->string(32)->notNull(),
            'version' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert('{{%system_conf}}', ['key' => 'lakala_version', 'label' => '拉卡拉版本号', 'value' => 1]);
    }

    public function safeDown()
    {
        $this->delete('{{%system_conf}}', ['key' => 'lakala_version']);
        $this->dropTable('{{%lakala_static}}');
    }
}
