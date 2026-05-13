<?php

namespace app\controllers;

use app\models\Order;
use app\models\response\OrderResponse;
use app\models\search\OrderSearch;
use app\services\OrderService;
use Override;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;


/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    public $enableCsrfValidation = false;
    public OrderService $orderService;

    #[Override]
    public function init()
    {
        $this->orderService = new OrderService ();
        return parent::init();
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return[
            'data' => $dataProvider->getModels()
        ];
    }

    /**
     * Displays a single Order model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return [
            'data' => $this->findModel($id),
        ];
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new OrderResponse();

        if ($this->request->isPost) {
            $result =$this->orderService->create($model, $this->request->post());
            if($result){
                return [
                    'msg' => 'create order sucess',
                    'order' => $model
                ];
            }

        }

        return [
            'msg' => 'create error',
            'error' => $model->errors
        ];
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $result = $this->orderService->updateStatusOrder($model, $this->request->post());
            if($result){
                return [
                    'msg' => 'update status success',
                    'order' => $model
                ];
            }
        }

        return [
            'msg' => 'update status error',
            'error' => $model->errors
        ];
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderResponse::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
