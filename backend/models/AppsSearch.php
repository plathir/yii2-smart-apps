<?php

namespace plathir\apps\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use plathir\apps\backend\models\Apps;

/**
 * AppsSearch represents the model behind the search form about `app\apps\models\Apps`.
 */
class AppsSearch extends Apps
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'descr', 'type', 'alias', 'app_key', 'vendor', 'vendor_email', 'version', 'active', 'app_icon'], 'safe'],
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
        $query = Apps::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'descr', $this->descr])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'app_key', $this->app_key])
            ->andFilterWhere(['like', 'active', $this->active])
            ->andFilterWhere(['like', 'vendor', $this->vendor])
            ->andFilterWhere(['like', 'vendor_email', $this->vendor_email])
            ->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'app_icon', $this->app_icon]);

        return $dataProvider;
    }
}
