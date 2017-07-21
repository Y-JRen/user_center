<?php

namespace backend\models\search;

use passport\modules\sso\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Order;

/**
 * OrderrSearch represents the model behind the search form about `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'order_type', 'notice_status', 'updated_at', 'platform'], 'integer'],
            [['platform_order_id', 'order_id', 'order_subtype', 'desc', 'created_at', 'notice_platform_param', 'remark', 'status', 'phone'], 'safe'],
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
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_type' => $this->order_type,
            'amount' => $this->amount,
            'uid' => $this->uid
        ]);

        if (!empty($this->phone)) {
            $query->andFilterWhere(['in', 'uid', User::find()->select('id')->where(['like', 'phone', $this->phone])]);
        }

        if (!empty($this->status)) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if (!empty($this->created_at)) {
            $startTime = strtotime(substr($this->created_at, 0, 10));
            $endTime = strtotime(substr($this->created_at, -10));
            $query->andFilterWhere(['>=', 'created_at', $startTime])
                ->andFilterWhere(['<', 'created_at', $endTime]);
        }

        if (!empty($this->order_subtype)) {
            $query->andFilterWhere(['like', 'order_subtype', $this->order_subtype]);
        }

        $query->andFilterWhere(['like', 'platform_order_id', $this->platform_order_id])
            ->andFilterWhere(['like', 'order_id', $this->order_id]);

        return $dataProvider;
    }
}
