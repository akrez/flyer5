<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RawType;

/**
 * RawTypeSearch represents the model behind the search form of `app\models\RawType`.
 */
class RawTypeSearch extends RawType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'rawId', 'typeId'], 'integer'],
            [['qty'], 'number'],
            [['des'], 'safe'],
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
    public function search($params, $newModel, $parentModel)
    {
        $query = RawType::find()->where(['typeId' => $parentModel->id])->with('raw');

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
            'typeId' => $this->typeId,
        ]);

        $query->andFilterWhere(['like', 'des', $this->des]);

        return $dataProvider;
    }

}
