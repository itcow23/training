<?php

namespace app\controllers;

use app\models\forms\PostCategoryForm;
use app\models\response\PostCategoryResponse;
use app\models\search\PostCategorySearch;
use yii\web\NotFoundHttpException;
use app\services\PostCategoryService;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class PostCategoryController extends BaseController
{
    private PostCategoryService $postCategoryService;
    public function init()
    {
        parent::init();
        $this->postCategoryService = new PostCategoryService();
    }

    /**
     * @inheritDoc
     */
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
            'model' => $this->findModel($id)
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
        $form = new PostCategoryForm(['scenario' => PostCategoryForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->postCategoryService->create($model, $form)) {
                return $this->successResponse('Created successfully', ['model' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Failed to create');
        }

        return $this->errorResponse($form, 'Invalid request');
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
        $form = new PostCategoryForm(['scenario' => PostCategoryForm::SCENARIO_UPDATE, 'id' => $model->id]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->postCategoryService->update($model, $form)) {
                return $this->successResponse('Updated successfully', ['model' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Failed to update');
        }

        return $this->errorResponse($form, 'Invalid request');
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
            return $this->errorResponse($model, 'Delete error');
        }

        return $this->successResponse('Delete success');
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
        return parent::findModelByClass(PostCategoryResponse::class, $id);
    }
}
