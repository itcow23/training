<?php

namespace app\controllers;

use app\models\forms\TagForm;
use app\models\response\TagResponse;
use app\models\Tag;
use app\models\search\TagSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\services\TagService;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends BaseController
{
    private TagService $tagService;
    public function init()
    {
        parent::init();
        $this->tagService = new TagService();
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
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
     * Lists all Tag models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);


         return $this->listResponse(
            $dataProvider->getModels(),
            $dataProvider->getTotalCount(),
            $dataProvider->pagination->getPage() + 1,
            $dataProvider->pagination->getPageSize(),
            $dataProvider->pagination->getPageCount(),
            'Tags retrieved successfully'
        );
    }

    /**
     * Displays a single Tag model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->successResponse(
            ['model' => $this->findModel($id)],
            'Tag retrieved successfully'
        );
    }

    /**
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
       $form = new TagForm(['scenario' => TagForm::SCENARIO_CREATE]);
        $model = new TagResponse();

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->tagService->create($model, $form)) {
                 return $this->successResponse(
                    ['model' => $result],
                    'Tag created successfully',
                    201
                );
            }
            return $this->errorResponse(
                $form->hasErrors() ? $form : $model,
                'Failed to create tag',
                422
            );
        }

         return $this->errorResponse(
            ['message' => 'POST request required'],
            'Invalid request',
            400
        );
    }

    /**
     * Updates an existing Tag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
         $model = $this->findModel($id);
        $form = new TagForm(['scenario' => TagForm::SCENARIO_UPDATE, 'id' => $model->id]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->tagService->update($model, $form)) {
                 return $this->successResponse(
                    ['model' => $result],
                    'Tag updated successfully'
                );
            }
            return $this->errorResponse(
                $form->hasErrors() ? $form : $model,
                'Failed to update tag',
                422
            );
        }

        return $this->errorResponse(
            ['message' => 'POST request required'],
            'Invalid request',
            400
        );
    }

    /**
     * Deletes an existing Tag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model = $this->findModel($id);

        if (!$this->tagService->delete($model)) {
             return $this->errorResponse(
                $model->hasErrors() ? $model->errors : ['message' => 'Failed to delete'],
                'Delete failed',
                400
            );
        }

         return $this->successResponse(
            [],
            'Tag deleted successfully'
        );
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
         return parent::findModelByClass(TagResponse::class, $id);
    }
}
