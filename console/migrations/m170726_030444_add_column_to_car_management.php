<?php

use common\models\CarManagement;
use yii\db\Migration;

class m170726_030444_add_column_to_car_management extends Migration
{
    public function safeUp()
    {
        $this->addColumn(CarManagement::tableName(), 'platform', $this->boolean()->notNull()->defaultValue(1)->after('status')->comment('车辆添加来源'));
        $this->createIndex('car_management_plate_number', CarManagement::tableName(), 'plate_number', true);
    }

    public function safeDown()
    {
        $this->dropIndex('car_management_plate_number', CarManagement::tableName());
        $this->dropColumn(CarManagement::tableName(), 'platform');
    }
}
