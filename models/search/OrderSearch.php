<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;
use app\models\response\OrderResponse;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    public $key;
    public $pageSize = 10;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_code', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'created_at', 'updated_at'], 'safe'],
            [['account_id', 'membership_level_id', 'pay_method', 'status'], 'integer'],
            [['discount', 'subtotal', 'final_total', 'shipping_fee'], 'number'],
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
        $query = OrderResponse::find()->with([
            'orderItems.product'
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize ?: 5,
                'pageParam' => 'page'
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
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
            'account_id' => $this->account_id,
            'membership_level_id' => $this->membership_level_id,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'final_total' => $this->final_total,
            'pay_method' => $this->pay_method,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'order_code', $this->order_code])
            ->andFilterWhere(['like', 'shipping_name', $this->shipping_name])
            ->andFilterWhere(['like', 'shipping_email', $this->shipping_email])
            ->andFilterWhere(['like', 'shipping_phone', $this->shipping_phone])
            ->andFilterWhere(['like', 'shipping_address', $this->shipping_address]);

        if (!empty($params['key'])) {
            $query->andFilterWhere(['like', 'id', $params['key']]);
        }

        return $dataProvider;
    }
}
