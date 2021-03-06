<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RawEntity;

/**
 * RawEntitySearch represents the model behind the search form of `app\models\RawEntity`.
 */
class RawEntitySearch extends RawEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'qty', 'rawId'], 'integer'],
            [['entityBarcode', 'des'], 'safe'],
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
    public function search($params = [], $barcode = null)
    {
        $query = RawEntity::validQuery($barcode)->with('raw');

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
            'qty' => $this->qty,
            'rawId' => $this->rawId,
        ]);

        $query->andFilterWhere(['like', 'entityBarcode', $this->entityBarcode]);
        $query->andFilterWhere(['like', 'des', $this->des]);

        return $dataProvider;
    }
}
