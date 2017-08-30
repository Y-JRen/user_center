<?php

use common\models\Order;
use yii\db\Migration;

class m170830_033538_update_order_remark extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(Order::tableName(), 'remark', $this->text());
    }

    public function safeDown()
    {
        $this->alterColumn(Order::tableName(), 'remark', $this->string(255));
    }
}
