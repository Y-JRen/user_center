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
            'model_name',
            'series_name',
            'brand_name',
            'driving_licenses' => function ($model) {
                return explode(',', $model->driving_license);
            }
        ];
    }
}
