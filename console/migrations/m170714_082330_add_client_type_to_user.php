<?php

use common\models\User;
use yii\db\Migration;

class m170714_082330_add_client_type_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn(User::tableName(), 'client_type', $this->string(50)->notNull()->defaultValue('')->comment('注册时的客户端类型'));
    }

    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'client_type');
    }
}
