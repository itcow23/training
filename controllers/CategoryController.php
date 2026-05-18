<?php

namespace app\controllers;

use app\models\forms\CategoryForm;
use app\models\response\CategoryResponse;
use app\models\search\CategorySearch;
use yii\web\NotFoundHttpException;
use app\services\CategoryService;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{
    private CategoryService $categoryService;
    public function init()
    {
        parent::init();
        $this->categoryService = new CategoryService();
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
        $form = new CategoryForm(['scenario' => CategoryForm::SCENARIO_CREATE]);
        $model = new CategoryResponse();

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->categoryService->create($model, $form)) {
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
        $form = new CategoryForm(['scenario' => CategoryForm::SCENARIO_UPDATE, 'id' => $model->id]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->categoryService->update($model, $form)) {
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

        if (!$this->categoryService->delete($model)) {
            return $this->errorResponse($model, 'Failed to delete');
        }

        return $this->successResponse('Delete success');
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
        return parent::findModelByClass(CategoryResponse::class, $id);
    }
}
