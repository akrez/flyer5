<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EntityLog;

/**
 * EntityLogSearch represents the model behind the search form of `app\models\EntityLog`.
 */
class EntityLogSearch extends EntityLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['updatedAt', 'createdAt', 'entityAttribute', 'oldValue', 'newValue', 'des', 'entityBarcode'], 'safe'],
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
    public function search($params, $barcode)
    {
        $query = EntityLog::validQuery()
            ->andWhere(['entityBarcode' => $barcode])
            ->with('entity');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC,]],
            'pagination' => false,
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

        $query->andFilterWhere(['like', 'updatedAt', $this->updatedAt])
            ->andFilterWhere(['like', 'createdAt', $this->createdAt])
            ->andFilterWhere(['like', 'entityAttribute', $this->entityAttribute])
            ->andFilterWhere(['like', 'oldValue', $this->oldValue])
            ->andFilterWhere(['like', 'newValue', $this->newValue])
            ->andFilterWhere(['like', 'des', $this->des])
            ->andFilterWhere(['like', 'entityBarcode', $this->entityBarcode]);

        return $dataProvider;
    }
}
