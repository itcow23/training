<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\Category;
use app\models\response\CategoryResponse;

/**
 * CategorySearch represents the model behind the search form of `app\models\Category`.
 */
class CategorySearch extends Category
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
            [['name', 'slug', 'img', 'created_at', 'updated_at'], 'safe'],
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

        $query = CategoryResponse::find()->with(['products','media']);



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize ?: 10,
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

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
        ]);

        if ($this->key !== null && $this->key !== '') {
            $key = self::normalizeKeyword((string) $this->key);
            $query->andFilterWhere([
                'like',
                new Expression("REPLACE(LOWER([[name]]), ' ', '')"),
                $key,
            ]);
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
