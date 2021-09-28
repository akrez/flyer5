<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Type;

/**
 * TypeSearch represents the model behind the search form of `app\models\Type`.
 */
class TypeSearch extends Type
{

    public function rules()
    {
        return [
            [['id', 'parentId'], 'integer'],
            [['name', 'shortname', 'unit', 'des'], 'safe'],
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
    public function search($params, $instanceModel = null)
    {
        $query = $instanceModel::find()
            ->andWhere(['categoryId' => $instanceModel::getCategoryClass()])
            ->with('parent');

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

        $query->andFilterWhere([
            'id' => $this->id,
            'parentId' => $this->parentId,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'shortname', $this->shortname])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'des', $this->des]);

        return $dataProvider;
    }
}
