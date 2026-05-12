<?php

namespace app\services;
use app\models\Category;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;
use app\services\MediaService;

class CategoryService
{
    private MediaService $mediaService;
    private UploadService $uploadService;
    public function __construct()
    {
            $this->mediaService = new MediaService();
            $this->uploadService = new UploadService();
    }
    public function create(Category $model, array $postData)
    {
        if (!$model->load($postData)) {
            return false;
        }

        $uploadedFile = UploadedFile::getInstance($model, 'img');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                //throw new RuntimeException('Failed to save category.');
                return false;
            }

            if ($uploadedFile) {
                $filePath = $this->uploadService->getFileName($uploadedFile, $model->tableName());
                if (!$this->uploadService->saveFile($uploadedFile, $filePath)) {
                    //throw new RuntimeException('Failed to save uploaded file.');
                    return false;
                }

                $data = [
                    'file_id' => $model->id,
                    'file_type' => $model->tableName(),
                    'filepath' => $filePath,
                ];
                if (!$this->mediaService->saveFile($data)) {
                    //throw new RuntimeException('Failed to save media record.');
                    return false;
                }

            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('img', $e->getMessage());
            return false;
        }
    }

    public function update(Category $model, array $postData): bool
    {
        if (!$model->load($postData)) {
            return false;
        }
        $uploadedFile = UploadedFile::getInstance($model, 'img');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new RuntimeException('Failed to save category.');
            }

            if ($uploadedFile) {

                $oldMedia = $model->getMedia()->one();
                if ($oldMedia) {
                    if (file_exists($oldMedia->filepath)) {
                        unlink($oldMedia->filepath);
                    }
                    $this->mediaService->deleteFile($oldMedia);
                }


                $filePath = $this->uploadService->getFileName($uploadedFile, $model->tableName());
                if (!$this->uploadService->saveFile($uploadedFile, $filePath)) {
                    throw new RuntimeException('Failed to save uploaded file.');
                }


                $data = [
                    'file_id' => $model->id,
                    'file_type' => $model->tableName(),
                    'filepath' => $filePath,
                ];
                $media = $model->getMedia()->one();
                if (!$this->mediaService->updateFile($media, $data)) {
                    throw new RuntimeException('Failed to save media record.');
                }
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('img', $e->getMessage());
            return false;
        }
    }

    public function delete(Category $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $media = $model->getMedia()->one();
            if ($media) {
                if (file_exists($media->filepath)) {
                    unlink($media->filepath);
                }
                $media->delete();
            }

            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete category.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }
}
