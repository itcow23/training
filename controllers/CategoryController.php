<?php

namespace app\controllers;

use app\models\response\CategoryResponse;
use app\models\search\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\services\CategoryService;
use Yii;
use yii\web\UploadedFile;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    public  $enableCsrfValidation = false;

    private CategoryService $categoryService;
    public function init()
    {
        parent::init();
        $this->categoryService = new CategoryService();
    }

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


    /**
     * Lists all Category models.
     *
     * @return string
     */

    public function actionIndex()
    {
        $params = $this->request->queryParams;

        $model = new CategorySearch();
        $dataProvider = $model->search($params);
        return [
            'items' => $dataProvider->getModels(),

            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $dataProvider->pagination->page + 1,
                'pageSize' => $dataProvider->pagination->pageSize,
                'pageCount' => $dataProvider->pagination->getPageCount(),
            ]
        ];
    }

    /**
     * Displays a single Category model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionView($id)
    {
        return [
            'model'=>$this->findModel($id)
        ];
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    public function actionCreate()
    {
        $model = new CategoryResponse();

        if ($this->request->isPost) {
            $result = $this->categoryService->create($model, $this->request->post());
            if ($result) {
                $model->refresh();
                return [
                    'msg' => 'Thêm thành công',
                    'model' => $model
                ];
            }
        } else {

            $model->loadDefaultValues();
        }


        return [
            'msg' => 'Không có gì được thêm',
            'error' => $model->errors
        ];
    }



    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($this->categoryService->update($model, $this->request->post())) {
                $model->refresh();
                return [
                    'msg' => 'Update thành công',
                    'model' => $model
                ];
            }
        }

       return [
            'msg' => 'update error',
            'error' => $model->errors
        ];

    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
   
    public function actionApiDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->categoryService->delete($model)) {
           return [
                'msg' => 'Delete error'
           ];
        }

        return [
                'msg' => 'Delete sucess'
           ];
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CategoryResponse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CategoryResponse::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
