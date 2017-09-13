<?php

namespace passport\modules\inside\models;

class FreezeRecord extends \common\models\FreezeRecord
{
    public function fields()
    {
        return [
            'order_no',
            'uid',
            'amount',
            'use'
        ];
    }
}
