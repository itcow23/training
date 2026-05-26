<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;


/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
    public $key;
    public $pageSize = 5;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status', 'discount'], 'integer'],
            [['name', 'description', 'slug', 'created_at', 'updated_at', 'key'], 'safe'],
            [['price'], 'number'],
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
        $query = Product::find()
            ->withRelations()
            ->withActiveCategory();

        if (!empty($this->category_id)) {
            $query->byCategory($this->category_id);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize ?: 5,
                'pageParam' => 'page',
            ],

            'sort' => [
                'attributes' => [
                    'id' => [
                        'asc' => ['product.id' => SORT_ASC],
                        'desc' => ['product.id' => SORT_DESC],
                    ],
                    'name',
                    'price',
                    'category_id',
                    'status',
                    'discount',
                ],
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
            'product.id' => $this->id,
            'product.price' => $this->price,
            'product.status' => $this->status,
            'product.discount' => $this->discount,
            'product.created_at' => $this->created_at,
            'product.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'product.name', $this->name])
            ->andFilterWhere(['like', 'product.description', $this->description])
            ->andFilterWhere(['like', 'product.slug', $this->slug]);

        $query->keyword($this->key);

        return $dataProvider;
    }
}
