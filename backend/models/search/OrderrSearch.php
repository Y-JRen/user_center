<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderrSearch represents the model behind the search form about `common\models\Order`.
 */
class OrderrSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform'], 'integer'],
            [['platform_order_id', 'order_id', 'order_subtype', 'desc', 'notice_platform_param', 'remark'], 'safe'],
            [['amount'], 'number'],
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
        $query = Order::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'uid' => $this->uid,
            'order_type' => $this->order_type,
            'amount' => $this->amount,
            'status' => $this->status,
            'notice_status' => $this->notice_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'platform' => $this->platform,
        ]);

        $query->andFilterWhere(['like', 'platform_order_id', $this->platform_order_id])
            ->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'order_subtype', $this->order_subtype])
            ->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'notice_platform_param', $this->notice_platform_param])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
