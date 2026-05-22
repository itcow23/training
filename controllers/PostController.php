<?php

namespace app\controllers;

use app\models\forms\PostForm;
use app\models\response\PostResponse;
use app\models\search\PostSearch;
use yii\web\NotFoundHttpException;
use app\services\PostService;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends BaseController
{
    private PostService $postService;

    public function init()
    {
        $this->postService = new PostService();
        return parent::init();
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

       return $this->dataProviderResponse($dataProvider, 'Posts retrieved successfully');
    }

    /**
     * Displays a single Post model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->successResponse(
            ['model' => $this->findModel($id)],
            'Post retrieved successfully'
        );
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new PostResponse();
        $form = new PostForm(['scenario' => PostForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->postService->create($model, $form)) {
                return $this->successResponse(
                    ['model' => $this->findModel($model->id, [])],
                    'Post created successfully',
                    201
                );
            }
            return $this->modelErrorResponse([$form, $model], 'Failed to create post');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
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
        $form = new PostForm(['scenario' => PostForm::SCENARIO_UPDATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->postService->update($model, $form)) {
                return $this->successResponse(
                    ['model' => $this->findModel($model->id, [])],
                    'Post updated successfully'
                );
            }
            return $this->modelErrorResponse([$form, $model], 'Failed to update post');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $form = new PostForm(['scenario' => PostForm::SCENARIO_UPDATE_STATUS]);
        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            $result = $this->postService->updateStatus($model, $form);
            if ($result) {
                return $this->successResponse(
                    ['model' => $this->findModel($model->id, [])],
                    'Post status updated successfully'
                );
            }
        }

            return $this->modelErrorResponse([$form, $model], 'Failed to update post status');
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
            return $this->modelErrorResponse([$model], 'Failed to delete post');
        }

        return $this->successResponse([], 'Post deleted successfully');
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PostResponse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        return parent::findModelByClass(PostResponse::class, $id);
    }
}
