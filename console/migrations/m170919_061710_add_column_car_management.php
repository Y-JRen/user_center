<?php

use common\models\CarManagement;
use yii\db\Migration;

class m170919_061710_add_column_car_management extends Migration
{
    public function safeUp()
    {
        $this->addColumn(CarManagement::tableName(), 'factory_id', $this->integer()->notNull()->after('status')->comment('厂商id'));
        $this->addColumn(CarManagement::tableName(), 'factory_name', $this->string()->notNull()->defaultValue('')->after('status')->comment('厂商名称'));

        $this->renameColumn(CarManagement::tableName(), 'car_brand_son_type_name', 'model_name');
        $this->renameColumn(CarManagement::tableName(), 'car_brand_son_type_id', 'model_id');

        $this->renameColumn(CarManagement::tableName(), 'car_brand_type_name', 'series_name');
        $this->renameColumn(CarManagement::tableName(), 'car_brand_type_id', 'series_id');
    }

    public function safeDown()
    {

        $this->renameColumn(CarManagement::tableName(), 'series_id', 'car_brand_type_id');
        $this->renameColumn(CarManagement::tableName(), 'series_name', 'car_brand_type_name');

        $this->renameColumn(CarManagement::tableName(), 'model_id', 'car_brand_son_type_id');
        $this->renameColumn(CarManagement::tableName(), 'model_name', 'car_brand_son_type_name');

        $this->dropColumn(CarManagement::tableName(), 'factory_name');
        $this->dropColumn(CarManagement::tableName(), 'factory_id');
    }
}
