<?php

namespace backend\models\search;

use passport\modules\sso\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Order;
use yii\db\Query;

/**
 * OrderSearch represents the model behind the search form about `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $key;
    public $orderStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'notice_platform_param', 'status', 'key'], 'trim'],
            [['order_type','platform', 'order_subtype', 'orderStatus'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        if (isset($params['uid'])) {
            $query = Order::find()->where(['uid' => $params['uid']]);
        } else {
            $query = Order::find();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

//        $orderStatus = $this->status;

        // grid filtering conditions
        $query->andFilterWhere([
            'order_type' => $this->order_type,
            'order_subtype' => $this->order_subtype,
            'platform' => $this->platform,
        ]);

        if (!empty($this->key)) {
            $query->andFilterWhere([
                'OR',
                ['LIKE', 'order_id', "%{$this->key}", false],
                ['IN', 'uid', (new Query())->select('id')->from(User::tableName())->where(['LIKE', 'phone', "{$this->key}%", false])]
            ]);
        }

        if (!empty($this->status)) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if (!empty($this->orderStatus)) {
            $this->status = $this->orderStatus;
        }

        if (!empty($this->created_at)) {
            $startTime = strtotime(substr($this->created_at, 0, 10));
            $endTime = strtotime(substr($this->created_at, -10)) + 86400;
            $query->andFilterWhere(['>=', 'created_at', $startTime])
                ->andFilterWhere(['<', 'created_at', $endTime]);
        }

        if (!empty($this->updated_at)) {
            $startTime = strtotime(substr($this->updated_at, 0, 10));
            $endTime = strtotime(substr($this->updated_at, -10)) + 86400;
            $query->andFilterWhere(['>=', 'updated_at', $startTime])
                ->andFilterWhere(['<', 'updated_at', $endTime]);
        }

        if (!empty($this->order_subtype)) {
            $query->andFilterWhere(['like', 'order_subtype', $this->order_subtype]);
        }

        $query->andFilterWhere(['like', 'platform_order_id', $this->platform_order_id])
            ->andFilterWhere(['like', 'order_id', $this->order_id]);

        return $dataProvider;
    }
}
