<?php

namespace tecsin\pay2\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tecsin\pay2\models\Pay2Setup;

/**
 * Pay2SetupSearch represents the model behind the search form about `tecsin\pay2\models\Pay2Setup`.
 */
class Pay2SetupSearch extends Pay2Setup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['merchant_id', 'success_url', 'failure_url', 'api_key', 'voguepay_email'], 'safe'],
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
        $query = Pay2Setup::find();

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

        $query->andFilterWhere(['like', 'merchant_id', $this->merchant_id])
            ->andFilterWhere(['like', 'success_url', $this->success_url])
            ->andFilterWhere(['like', 'failure_url', $this->failure_url])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'voguepay_email', $this->voguepay_email]);

        return $dataProvider;
    }
}
