<?php

namespace app\controllers;

use app\models\Post;
use app\models\response\PostResponese;
use app\models\search\PostSearch;
use app\services\PostService;
use Override;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    public $enableCsrfValidation = false;
    public $postService;

    #[Override]
    public function init()
    {
        $this->postService = new PostService();
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
     * Lists all Post models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return [
            'data' => $dataProvider->getModels()
        ];
    }

    /**
     * Displays a single Post model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return [
            'model' => $this->findModel($id),
        ];
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new PostResponese();

        if ($this->request->isPost) {
            $result = $this->postService->create($model, $this->request->post());
            if ($result) {
                $model->refresh();
                return [
                    'msg' => 'create sucess',
                    'data' => $model
                ];
            }
        }

        return [
            'msg' => 'create error',
            'error' => $model->errors
        ];
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if ($this->request->isPost) {
            $result = $this->postService->update($model, $this->request->post());
            if ($result) {
                $model->refresh();
                return [
                    'msg' => 'update sucess',
                    'data' => $model
                ];
            }
        }

        return [
            'msg' => 'update error',
            'error' => $model->errors
        ];
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost) {
            $result = $this->postService->updateStatus($model, $this->request->post());
            if ($result) {
                return [
                    'msg' => 'update sucess',
                    'data' => $model
                ];
            }
        }

        return [
            'msg' => 'update error',
            'error' => $model->errors
        ];
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->postService->delete($model)) {
            return [
                'msg' => 'Delete error'
            ];
        }

        return [
            'msg' => 'Delete sucess'
        ];
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PostResponese::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
