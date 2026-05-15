<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PostCategory;
use app\models\response\PostCategoryResponse;

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
            [['id'], 'integer'],
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

        $query = PostCategoryResponse::find();

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
            'name' => $this->name,
        ]);

       foreach($params as $item){
            $key = $this->normalizeKeyword($item);
            $query->orFilterWhere(['like', "REPLACE(name, ' ', '')", $key]);
       }


        return $dataProvider;
    }

    public static function normalizeKeyword($text)
    {
        $text = strtolower($text);

        $text = str_replace(' ', '', $text);

        return $text;
    }
}
