<?php

namespace app\controllers;

use app\models\forms\CategoryForm;
use app\models\response\CategoryResponse;
use app\models\search\CategorySearch;
use yii\web\NotFoundHttpException;
use app\services\CategoryService;
use yii\web\UploadedFile;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{

    private CategoryService $categoryService;
    
    public function __construct($id, $module, CategoryService $categoryService, $config = [])
    {
        $this->categoryService = $categoryService;
        parent::__construct($id, $module, $config);
    }

    protected function defaultRelations(): array
    {
        return ['products', 'media'];
    }

    /**
     * Lists all Category models.
     *
     * @return array
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams, '');

        return $this->dataProviderResponse($dataProvider, 'Categories retrieved successfully');
    }

    /**
     * Displays a single Category model.
     * @param int $id ID
     * @return array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->successResponse(
            ['model' => $this->findModel($id)],
            'Category retrieved successfully'
        );
    }

    /**
     * Creates a new Category model.
     * @return array
     */
    public function actionCreate()
    {
        $form = new CategoryForm(['scenario' => CategoryForm::SCENARIO_CREATE]);
        $model = new CategoryResponse();

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            $form->image = UploadedFile::getInstancesByName('image');

            if ($this->categoryService->create($model, $form)) {
                return $this->successResponse(
                    ['model' => $this->findModel($model->id, [])],
                    'Category created successfully',
                    201
                );
            }

            return $this->modelErrorResponse([$form, $model], 'Failed to create category');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
    }

    /**
     * Updates an existing Category model.
     * @param int $id ID
     * @return array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, []);
        $form = new CategoryForm(['scenario' => CategoryForm::SCENARIO_UPDATE, 'id' => $model->id]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            $form->image = UploadedFile::getInstancesByName('image');

            if ($this->categoryService->update($model, $form)) {
                return $this->successResponse(
                    ['model' => $this->findModel($model->id, [])],
                    'Category updated successfully'
                );
            }

            return $this->modelErrorResponse([$form, $model], 'Failed to update category');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
    }

    /**
     * Deletes an existing Category model.
     * @param int $id ID
     * @return array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id, []);

        if (!$this->categoryService->delete($model)) {
            return $this->modelErrorResponse($model, 'Delete failed', 400);
        }

        return $this->successResponse([], 'Category deleted successfully');
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @param array|null $with
     * @return CategoryResponse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, ?array $with = null)
    {
        return parent::findModelByClass(CategoryResponse::class, $id, $with);
    }
}
