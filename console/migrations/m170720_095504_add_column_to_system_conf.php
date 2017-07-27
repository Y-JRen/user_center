<?php

use common\models\SystemConf;
use yii\db\Migration;

class m170720_095504_add_column_to_system_conf extends Migration
{
    public function safeUp()
    {
        $columns = [
            'key' => 'recharge_order_valid_time',
            'label' => '充值订单有效时间(分钟)',
            'value' => 30,
            'type' => 'text',
            'remark' => '单位：分钟',
            'is_show' => 0
        ];
        $this->insert(SystemConf::tableName(), $columns);
    }

    public function safeDown()
    {
        $this->delete(SystemConf::tableName(), ['key' => 'recharge_order_valid_time']);
    }
}
