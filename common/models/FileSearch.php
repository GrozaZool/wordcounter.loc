<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FileSearch represents the model behind the search form of `common\models\File`.
 */
class FileSearch extends File
{

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'wordCount', 'fileSize'], 'integer'],
            [['fileGroup', 'fileType'], 'string'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
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
    public function search(array $params): ActiveDataProvider
    {
        $query = File::find();

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
            'wordCount' => $this->wordCount,
            'fileSize' => $this->fileSize,
        ]);

        if (!empty($this->fileType)) {
            $query->joinWith(['type t' => function ($q) {
                $q->where("t.extension LIKE '%{$this->fileType}%'");
            }]);
        }

        if (!empty($this->fileGroup)) {
            $query->joinWith(['groups gr' => function ($q) {
                $q->where("gr.name LIKE '%{$this->fileGroup}%'");
            }]);
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
