<?php

namespace tecsin\pay2\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tecsin\pay2\models\Pay2Ms;

/**
 * Pay2MsSearch represents the model behind the search form about `tecsin\pay2\models\Pay2Ms`.
 */
class Pay2MsSearch extends Pay2Ms
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msID', 'cccRecurrentBillingStatus', 'iiiRecurrenceInterval'], 'integer'],
            [['aaaMerchantId', 'mmmMemo', 'tttTotalCost', 'rrrMerchantRef', 'nnnNotificationUrl', 'sssSuccessUrl', 'fffFailUrl', 'dddDeveloperCode', 'cccCurrencyCode', 'msResponse', 'msExpireAt', 'siteProductId', 'msStatus'], 'safe'],
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
        $query = Pay2Ms::find();

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
            'msID' => $this->msID,
            'cccRecurrentBillingStatus' => $this->cccRecurrentBillingStatus,
            'iiiRecurrenceInterval' => $this->iiiRecurrenceInterval,
        ]);

        $query->andFilterWhere(['like', 'aaaMerchantId', $this->aaaMerchantId])
            ->andFilterWhere(['like', 'mmmMemo', $this->mmmMemo])
            ->andFilterWhere(['like', 'tttTotalCost', $this->tttTotalCost])
            ->andFilterWhere(['like', 'rrrMerchantRef', $this->rrrMerchantRef])
            ->andFilterWhere(['like', 'nnnNotificationUrl', $this->nnnNotificationUrl])
            ->andFilterWhere(['like', 'sssSuccessUrl', $this->sssSuccessUrl])
            ->andFilterWhere(['like', 'fffFailUrl', $this->fffFailUrl])
            ->andFilterWhere(['like', 'dddDeveloperCode', $this->dddDeveloperCode])
            ->andFilterWhere(['like', 'cccCurrencyCode', $this->cccCurrencyCode])
            ->andFilterWhere(['like', 'msResponse', $this->msResponse])
            ->andFilterWhere(['like', 'msExpireAt', $this->msExpireAt])
            ->andFilterWhere(['like', 'siteProductId', $this->siteProductId])
            ->andFilterWhere(['like', 'msStatus', $this->msStatus]);

        return $dataProvider;
    }
}
