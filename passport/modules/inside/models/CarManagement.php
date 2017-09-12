<?php

namespace passport\modules\inside\models;


class CarManagement extends \common\models\CarManagement
{
    public function fields()
    {
        return [
            'plate_no' => function ($model) {
                return $model->plate_number;
            },
            'frame_no' => function ($model) {
                return $model->frame_number;
            },
            'car_brand_son_type_name',
            'car_brand_type_name',
            'brand_name',
            'driving_licenses' => function ($model) {
                return explode(',', $model->driving_license);
            }
        ];
    }
}
