<?php

namespace app\controllers;

use app\models\forms\RatingForm;
use app\models\Rating;
use yii\web\NotFoundHttpException;
use app\services\RatingService;

class RatingController extends BaseController
{
    private RatingService $ratingService;
    public function init()
    {
        parent::init();
        $this->ratingService = new RatingService();
    }

    public function actionIndex()
    {
        return ['message' => 'Index rating'];
    }

    /**
     * Displays a single Rating model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->successResponse(
            ['model' => $this->findModel($id)],
            'Rating retrieved successfully'
        );
    }

    public function actionCreate()
    {
        $form = new RatingForm(['scenario' => RatingForm::SCENARIO_CREATE]);
        $model = new Rating();

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->ratingService->create($model, $form)) {
                return $this->successResponse(
                    ['model' => $this->findModel($model->id, [])],
                    'Rating created successfully',
                    201
                );
            }
            return $this->modelErrorResponse([$form, $model], 'Failed to create rating');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        return parent::findModelByClass(Rating::class, $id);
    }
}
