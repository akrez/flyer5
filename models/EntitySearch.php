<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Entity;

class EntitySearch extends Entity
{

    public function rules()
    {
        return [
            [['qc', 'qa', 'providerId', 'sellerId', 'typeId', 'price'], 'integer'],
            [['barcode', 'factor', 'des', 'place', 'submitAt', 'factorAt', 'productAt', 'parentBarcode'], 'safe'],
        ];
    }

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
    public function search($params, $categoryClass = null, $pagesize = 5)
    {
        if ($categoryClass) {
            $instanceModel = new $categoryClass();
            $categoryId = $instanceModel::getCategoryClass();
            $query = $instanceModel::validQuery($categoryId);
        } else {
            $query = Entity::validQuery();
        }
        $query->with('parent')->with('type')->with('provider')->with('seller');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC,]],
            'pagination' => ['pagesize' => $pagesize,]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'barcode' => $this->barcode,
            'price' => $this->price,
            'qc' => $this->qc,
            'qa' => $this->qa,
            'providerId' => $this->providerId,
            'sellerId' => $this->sellerId,
            'typeId' => $this->typeId,
        ]);

        $query->andFilterWhere(['like', 'factor', $this->factor])
            ->andFilterWhere(['like', 'place', $this->place])
            ->andFilterWhere(['like', 'des', $this->des])
            ->andFilterWhere(['like', 'submitAt', $this->submitAt])
            ->andFilterWhere(['like', 'factorAt', $this->factorAt])
            ->andFilterWhere(['like', 'parentBarcode', $this->parentBarcode])
            ->andFilterWhere(['like', 'productAt', $this->productAt]);

        return $dataProvider;
    }
}
