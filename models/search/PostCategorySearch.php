<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PostCategory;

/**
 * PostCategorySearch represents the model behind the search form of `app\models\PostCategory`.
 */
class PostCategorySearch extends PostCategory
{
    public $key;
    public $pageSize = 10;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'slug', 'created_at', 'updated_at'], 'safe'],
            ['key', 'safe'],
            [['pageSize'], 'integer']
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {

        $query = PostCategory::find()->notDeleted()->withRelations();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize ?: 5,
                'pageParam' => 'page',
            ],

            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'name' => $this->name,
        ]);

        $query->keyword($this->key);

        return $dataProvider;
    }
}

