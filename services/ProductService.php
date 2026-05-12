<?php

namespace app\services;

use app\models\Product;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;
use app\services\MediaService;

class ProductService
{
     private MediaService $mediaService;
    private UploadService $uploadService;
    public function __construct()
    {
        $this->mediaService = new MediaService();
        $this->uploadService = new UploadService();
    }
    public function create(Product $model, array $postData)
    {

        if (!$model->load($postData)) {
            dd('load fail');
            return false;
        }
        $uploadedFile = UploadedFile::getInstances($model, 'image');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new RuntimeException('Failed to save Product.');
            }
            if ($uploadedFile) {
                foreach ($uploadedFile as $file) {
                    $filePath = $this->uploadService->uploadFile($file, $model->tableName());
                    if (!$filePath) {
                        throw new RuntimeException('Failed to save uploaded file.');
                    }
                    $data = [
                        'file_id' => $model->id,
                        'file_type' => $model->tableName(),
                        'filepath' => $filePath,
                    ];
                    if (!$this->mediaService->saveFile($data)) {
                        throw new RuntimeException('Failed to save media record.');
                    }
                }
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('image', $e->getMessage());
            return false;
        }
    }

    public function update(Product $model, array $postData): bool
    {
        if (!$model->load($postData)) {
            return false;
        }

        $img_deleted = $postData['img_deleted'] ?? [];
        $uploadedFile = UploadedFile::getInstances($model, 'image');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new RuntimeException('Failed to save Product.');
            }

            if (!empty($img_deleted)) {
                foreach ($img_deleted as $mediaId) {
                    $media = $this->mediaService->getMediaById($mediaId);
                    if ($media) {
                        if (file_exists($media->filepath)) {
                            unlink($media->filepath);
                        }
                        $media->delete();
                    }
                }
            }
            if ($uploadedFile) {
                foreach ($uploadedFile as $file) {
                    $filePath = $this->uploadService->uploadFile($file, $model->tableName());
                    if (!$filePath) {
                        throw new RuntimeException('Failed to save uploaded file.');
                    }
                    $data = [
                        'file_id' => $model->id,
                        'file_type' => $model->tableName(),
                        'filepath' => $filePath,
                    ];
                    $media = $model->getMedia()->one();
                    if (!$this->mediaService->saveFile($data)) {
                        throw new RuntimeException('Failed to save media record.');
                    }
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

    public function delete(Product $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $mediaItems = $model->getMedia()->all();
            foreach ($mediaItems as $media) {
                if (file_exists($media->filepath)) {
                    unlink($media->filepath);
                }
                $this->mediaService->deleteFile($media);
            }

            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete Product.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }
}
