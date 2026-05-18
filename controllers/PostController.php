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
    public $postService;

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
        $model = new PostResponse();
        $form = new PostForm(['scenario' => PostForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->postService->create($model, $form)) {
                return $this->successResponse('Created successfully', ['model' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Failed to create');
        }

        return $this->errorResponse($form, 'Invalid request');
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
            if ($result = $this->postService->update($model, $form)) {
                return $this->successResponse('Updated successfully', ['model' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Failed to update');
        }

        return $this->errorResponse($form, 'Invalid request');
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost) {
            $result = $this->postService->updateStatus($model, $this->request->post());
            if ($result) {
                $model = $this->findModel($id);

                return $this->successResponse('Update success', ['data' => $model]);
            }
        }

        return $this->errorResponse($model, 'Update error');
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
            return $this->errorResponse($model, 'Delete error');
        }

        return $this->successResponse('Delete success');
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
        return parent::findModelByClass(PostResponse::class, $id);
    }
}
