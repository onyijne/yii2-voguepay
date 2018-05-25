<?php

namespace tecsin\pay2\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tecsin\pay2\models\Pay2Sales;

/**
 * Pay2SalesSearch represents the model behind the search form about `tecsin\pay2\models\Pay2Sales`.
 */
class Pay2SalesSearch extends Pay2Sales
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ref', 'remark', 'received_amount', 'mature_date', 'transaction_date', 'memo', 'total', 'total_paid', 'extra_charges', 'gateway', 'user_id', 'referrer'], 'safe'],
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
        $query = Pay2Sales::find();

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
        ]);

        $query->andFilterWhere(['like', 'ref', $this->ref])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'received_amount', $this->received_amount])
            ->andFilterWhere(['like', 'mature_date', $this->mature_date])
            ->andFilterWhere(['like', 'transaction_date', $this->transaction_date])
            ->andFilterWhere(['like', 'memo', $this->memo])
            ->andFilterWhere(['like', 'total', $this->total])
            ->andFilterWhere(['like', 'total_paid', $this->total_paid])
            ->andFilterWhere(['like', 'extra_charges', $this->extra_charges])
            ->andFilterWhere(['like', 'gateway', $this->gateway])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'referrer', $this->referrer]);

        return $dataProvider;
    }
}
