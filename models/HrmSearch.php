<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Hrm;

/**
 * HrmSearch represents the model behind the search form of `app\models\Hrm`.
 */
class HrmSearch extends Hrm
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gender', 'role'], 'integer'],
            [['updatedAt', 'createdAt', 'fullname', 'fatherName', 'code', 'mobile', 'nationalCode', 'birthdate', 'des'], 'safe'],
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
        $query = Hrm::find();

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
            'gender' => $this->gender,
            'role' => $this->role,
        ]);

        $query
                ->andFilterWhere(['like', 'fullname', $this->fullname])
                ->andFilterWhere(['like', 'fatherName', $this->fatherName])
                ->andFilterWhere(['like', 'code', $this->code])
                ->andFilterWhere(['like', 'mobile', $this->mobile])
                ->andFilterWhere(['like', 'nationalCode', $this->nationalCode])
                ->andFilterWhere(['like', 'birthdate', $this->birthdate])
                ->andFilterWhere(['like', 'des', $this->des]);

        return $dataProvider;
    }

}
