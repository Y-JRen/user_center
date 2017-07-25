<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'from_channel', 'reg_time'], 'safe'],
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
        $query = User::find();

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
            'from_platform' => $this->from_platform,
        ]);

        if (!empty($this->reg_time)) {
            $startTime = strtotime(substr($this->reg_time, 0, 10));
            $endTime = strtotime(substr($this->reg_time, -10)) + 86400;
            $query->andFilterWhere(['>=', 'reg_time', $startTime])
                ->andFilterWhere(['<', 'reg_time', $endTime]);
        }

        $query->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
