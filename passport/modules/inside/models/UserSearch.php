<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/14
 * Time: 15:47
 */

namespace passport\modules\inside\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public $reg_start_time;
    public $login_start_time;
    public $reg_end_time;
    public $login_end_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'phone', 'status', 'reg_start_time', 'login_start_time', 'reg_end_time', 'login_end_time', 'client_type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'phone' => $this->phone,
            'status' => $this->status,
            'client_type' => strtolower($this->client_type),
        ]);

        // 支持多用户id同时查询
        if (!empty($this->id)) {
            $idArr = explode(',', $this->id);
            $query->andFilterWhere(['id' => $idArr]);
        }

        if (!empty($this->reg_start_time)) {
            $query->andWhere(['>=', 'reg_time', strtotime($this->reg_start_time)]);
        }
        if (!empty($this->reg_end_time)) {
            $query->andWhere(['<', 'reg_time', strtotime($this->reg_end_time)]);
        }
        if (!empty($this->login_start_time)) {
            $query->andWhere(['>=', 'login_time', strtotime($this->login_start_time)]);
        }
        if (!empty($this->login_end_time)) {
            $query->andWhere(['<', 'login_time', strtotime($this->login_end_time)]);
        }

        return $dataProvider;
    }
}