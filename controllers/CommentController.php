<?php

namespace app\controllers;

use app\services\CommentService;
use app\models\Comment;
use app\models\forms\CommentForm;
use yii\web\NotFoundHttpException;

/**
 * CommentController implements the CRUD actions for Comment model.
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
     * Lists all Comment models.
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
     * Creates a new Comment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    public function actionCreate()
    {
        $model = new Comment();
        $form = new CommentForm(['scenario' => CommentForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->commentService->create($model, $form)) {
                return $this->successResponse(
                    ['model' => $result],
                    'Comment created successfully',
                    201
                );
            }

            return $this->errorResponse(
                $form->hasErrors() ? $form : $model,
                'Failed to create comment',
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
     * Updates an existing Comment model.
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
                return $this->successResponse(
                    ['model' => $result],
                    'Comment updated successfully'
                );
            }

            return $this->errorResponse(
                $form->hasErrors() ? $form : $model,
                'Failed to update comment',
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
     * Deletes an existing Comment model.
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
                return $this->successResponse(
                    [],
                    'Comment deleted successfully',
                );
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Delete error', 400);
        }

        return $this->errorResponse(
            ['message' => 'POST request required'],
            'Invalid request',
            400
        );
    }

    /**
     * Finds the Comment model based on its primary key value.
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
