<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RawImported;

/**
 * RawImportedSearch represents the model behind the search form of `app\models\RawImported`.
 */
class RawImportedSearch extends RawImported
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'price', 'qty', 'sellerId', 'providerId', 'rawId'], 'integer'],
            [['factor', 'submitAt', 'factorAt', 'des'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = RawImported::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC,]],
            'pagination' => ['pagesize' => 5,]
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
            'price' => $this->price,
            'qty' => $this->qty,
            'sellerId' => $this->sellerId,
            'providerId' => $this->providerId,
            'rawId' => $this->rawId,
        ]);

        $query->andFilterWhere(['like', 'factor', $this->factor])
                ->andFilterWhere(['like', 'submitAt', $this->submitAt])
                ->andFilterWhere(['like', 'factorAt', $this->factorAt])
                ->andFilterWhere(['like', 'des', $this->des]);

        return $dataProvider;
    }
}
