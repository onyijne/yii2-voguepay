<?php

namespace tecsin\pay2\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tecsin\pay2\models\Pay2CommandHistory;

/**
 * Pay2CommandHistorySearch represents the model behind the search form about `tecsin\pay2\models\Pay2CommandHistory`.
 */
class Pay2CommandHistorySearch extends Pay2CommandHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ref', 'task', 'type', 'status'], 'safe'],
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
        $query = Pay2CommandHistory::find();

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
            ->andFilterWhere(['like', 'task', $this->task])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
