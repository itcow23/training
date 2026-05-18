<?php

namespace app\controllers;

use app\services\CommentService;
use app\models\Comment;
use app\models\forms\CommentForm;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CommentController extends BaseController
{
    private CommentService $commentService;
    public function init()
    {
        parent::init();
        $this->commentService = new CommentService();
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
        return [
            'msg' => 'index'
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
        $model = new Comment();
        $form = new CommentForm(['scenario' => CommentForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->commentService->create($model, $form)) {
                return $this->successResponse('Create success', ['model' => $model]);
            }
            return $this->errorResponse($form, 'Create error');
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
        $form = new CommentForm(['scenario' => CommentForm::SCENARIO_UPDATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->commentService->update($model, $form)) {
                return $this->successResponse('Update success', ['model' => $result]);
            }
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
        $form = new CommentForm(['scenario' => CommentForm::SCENARIO_DELETE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->commentService->delete($model, $form)) {
                return $this->successResponse('Delete success');
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Delete error');
        }

        return $this->errorResponse($form, 'Invalid request');
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        return parent::findModelByClass(Comment::class, $id);
    }
}
