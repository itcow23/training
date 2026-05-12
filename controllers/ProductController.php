<?php

namespace app\controllers;

use app\models\Product;
use app\models\response\ProductResponse;
use app\models\search\ProdcutSearch;
use app\services\ProductService;
use Override;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
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

    private ProductService $productService;
    #[Override]
    public function init()
    {
        $this->productService = new ProductService();
        return parent::init();
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProdcutSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $model = new ProductResponse();

        // return $this->render('index', [
        //     'searchModel' => $searchModel,
        //     'dataProvider' => $dataProvider,
        // ]);

        return [
            'items' => $dataProvider->getModels(),
            'product active' => $model->find()->active()->all(),
            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $dataProvider->pagination->page + 1,
                'pageSize' => $dataProvider->pagination->pageSize,
                'pageCount' => $dataProvider->pagination->getPageCount(),
            ]
        ];
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // return $this->render('view', [
        //     'model' => $this->findModel($id),
        // ]);

        return [
            'model' => $this->findModel($id),
        ];
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProductResponse();

        if ($this->request->isPost) {
            $result = $this->productService->create($model, $this->request->post());
            if ($result) {
                return [
                    'msg' => 'create sucess',
                    'model' => $model
                ];
            }
        } else {

            $model->loadDefaultValues();
        }


        return [
            'msg' => 'create error'
        ];
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($this->productService->update($model, $this->request->post())) {
                return [
                    'msg' => 'Update thành công',
                    'model' => $model
                ];
            }
        }

       return [
            'msg' => 'update error'
        ];
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->productService->delete($model)) {
           return [
                'msg' => 'Delete error'
           ];
        }

        return [
                'msg' => 'Delete sucess'
           ];
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductResponse::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
