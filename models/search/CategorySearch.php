<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Category;

class CategorySearch extends Category
{
    public $key;
    public $pageSize = 10;

    public function rules()
    {
        return [

            [['id', 'status'], 'integer'],

            [['name', 'slug', 'created_at', 'updated_at', 'key'], 'safe'],

            [['pageSize'], 'integer',
                'min' => 1,
                'max' => 100
            ],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = Category::find()
            ->notDeleted()
            ->with('media')
            ->latest();

        $this->load($params, $formName);

        $dataProvider = new ActiveDataProvider([

            'query' => $query,

            'pagination' => [
                'pageSize' => $this->pageSize ?: 10,
                'pageParam' => 'page',
            ],

        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->keyword($this->key);

        return $dataProvider;
    }
}
