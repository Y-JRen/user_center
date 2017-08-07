<?php

namespace backend\models\search;

use common\models\UserInfo;
use passport\modules\sso\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Order;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * OrderLineSearch represents the model behind the search form about `common\models\Order`.
 */
class OrderLineSearch extends Order
{
    public $key;
    public $history;
    public $orderStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'status', 'key'], 'trim'],
            [['order_type', 'platform', 'order_subtype', 'history', 'orderStatus'], 'safe'],
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

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_type' => $this->order_type,
            'order_subtype' => $this->order_subtype,
            'platform' => $this->platform,
        ]);

        if (!empty($this->key)) {
            if (preg_match("/[\x7f-\xff]/", $this->key)) {
                $uid = UserInfo::find()->select('uid')->where(['real_name' => $this->key])->asArray()->all();
                if (!empty($uid)) {
                    $query->andFilterWhere(['uid' => ArrayHelper::getColumn($uid, 'uid')]);
                }
            } else {
                $query->andFilterWhere(
                    ['IN', 'uid', (new Query())->select('id')->from(User::tableName())->where(['LIKE', 'phone', "{$this->key}%", false])]
                );
            }
        }

        if (!empty($this->orderStatus)) {
            $this->status = $this->orderStatus;
        }

        if (!empty($this->status)) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if (!empty($this->created_at)) {
            $startTime = strtotime(substr($this->created_at, 0, 10));
            $endTime = strtotime(substr($this->created_at, -10)) + 86400;
            $query->andFilterWhere(['>=', 'created_at', $startTime])
                ->andFilterWhere(['<', 'created_at', $endTime]);
        }

        return $dataProvider;
    }
}
