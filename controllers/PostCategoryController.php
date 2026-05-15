<?php

namespace app\controllers;

use app\models\response\PostCategoryResponse;
use app\models\search\PostCategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\services\PostCategoryService;
use Yii;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class PostCategoryController extends Controller
{
    public  $enableCsrfValidation = false;

    private PostCategoryService $postCategoryService;
    public function init()
    {
        parent::init();
        $this->postCategoryService = new PostCategoryService();
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

        $model = new PostCategorySearch();
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
        $model = new PostCategoryResponse();

        if ($this->request->isPost) {
            $result = $this->postCategoryService->create($model, $this->request->post());
            if ($result) {
                return [
                    'msg' => 'create success',
                    'model' => $model
                ];
            }
        }

        return [
            'msg' => 'create error'
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
            if ($this->postCategoryService->update($model, $this->request->post())) {
                return [
                    'msg' => 'Update sucess',
                    'model' => $model
                ];
            }
        }

       return [
            'msg' => 'update error'
        ];

    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->postCategoryService->delete($model)) {
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
     * @return PostCategoryResponse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PostCategoryResponse::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
