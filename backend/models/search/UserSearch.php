<?php

namespace backend\models\search;

use common\models\UserBalance;
use common\models\UserFreeze;
use common\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    public $key;
    public $balance;
    public $freeze;

    public static $balanceArray = [
        2 => '0＜X≤3000',
        3 => '3000＜X≤7000',
        4 => '7000＜X≤15000',
        5 => '15000＜X≤30000',
        6 => '30000＜X≤50000',
        7 => '50000以上'
    ];

    public static $freezeArray = [
        2 => '0＜X≤50000',
        3 => '5000＜X≤10000',
        4 => '100000＜X≤200000',
        5 => '200000以上'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reg_time', 'login_time', 'key'], 'trim'],
            [['from_platform', 'reg_ip', 'status', 'id', 'balance', 'freeze'], 'safe'],
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

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
            'from_platform' => $this->from_platform,
        ]);

        if (!empty($this->key)) {
            if (preg_match("/[\x7f-\xff]/", $this->key)) {
                $uid = UserInfo::find()->select('uid')->where(['real_name' => $this->key])->asArray()->all();
                if (!empty($uid)) {
                    $query->andFilterWhere(['id' => ArrayHelper::getColumn($uid, 'uid')]);
                }
            } else {
                $query->andFilterWhere([
                    'OR',
                    ['reg_ip' => $this->key],
                    ['LIKE', 'phone', "{$this->key}%", false]
                ]);
            }
        }

        if (!empty($this->reg_time)) {
            $startTime = strtotime(substr($this->reg_time, 0, 10));
            $endTime = strtotime(substr($this->reg_time, -10)) + 86400;
            $query->andFilterWhere(['>=', 'reg_time', $startTime])
                ->andFilterWhere(['<', 'reg_time', $endTime]);
        }

        if (!empty($this->login_time)) {
            $startTime = strtotime(substr($this->login_time, 0, 10));
            $endTime = strtotime(substr($this->login_time, -10)) + 86400;
            $query->andFilterWhere(['>=', 'login_time', $startTime])
                ->andFilterWhere(['<', 'login_time', $endTime]);
        }

        /**
         * 余额查询
         */
        if (!empty($this->balance)) {
            $balanceQuery = UserBalance::find();
            foreach ($this->balance as $value) {
                switch ($value) {
                    case 2:
                        $balanceQuery->orWhere('amount > 0 and amount <= 3000');
                        break;
                    case 3:
                        $balanceQuery->orWhere('amount > 3000 and amount <= 7000');
                        break;
                    case 4:
                        $balanceQuery->orWhere('amount > 7000 and amount <= 15000');
                        break;
                    case 5:
                        $balanceQuery->orWhere('amount > 15000 and amount <= 30000');
                        break;
                    case 6:
                        $balanceQuery->orWhere('amount > 30000 and amount <= 50000');
                        break;
                    case 7:
                        $balanceQuery->orWhere('amount > 50000');
                        break;
                }
            }
            if (!empty($balanceQuery->where)) {
                $uid = $balanceQuery->select('uid')->asArray()->all();
                $query->andFilterWhere(['id' => ArrayHelper::getColumn($uid, 'uid')]);
            }
        }

        /**
         * 冻结余额查询
         */
        if (!empty($this->freeze)) {
            $freezeQuery = UserFreeze::find();
            foreach ($this->freeze as $value) {
                switch ($value) {
                    case 2:
                        $freezeQuery->orWhere('amount > 0 and amount <= 50000');
                        break;
                    case 3:
                        $freezeQuery->orWhere('amount > 50000 and amount <= 100000');
                        break;
                    case 4:
                        $freezeQuery->orWhere('amount > 100000 and amount <= 200000');
                        break;
                    case 5:
                        $freezeQuery->orWhere('amount > 200000');
                        break;
                }
            }
            if (!empty($freezeQuery->where)) {
                $uid = $freezeQuery->select('uid')->asArray()->all();
                $query->andFilterWhere(['id' => ArrayHelper::getColumn($uid, 'uid')]);
            }
        }

        return $dataProvider;
    }
}
