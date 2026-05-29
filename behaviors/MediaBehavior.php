<?php

namespace app\behaviors;

use app\models\Media;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class MediaBehavior extends Behavior
{
    public $attribute = 'image';

    public $removedAttribute = 'removed_image';

    public $collection = 'thumbnail';

    public $folder;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'uploadMedia',
            ActiveRecord::EVENT_AFTER_UPDATE => 'uploadMedia',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteMedia',
        ];
    }

    public function uploadMedia($event): void
    {
        $model = $this->owner;

        $files = $model->{$this->attribute};
        $removed = $model->{$this->removedAttribute};

        if (empty($files) && empty($removed)) {
            return;
        }

        if (!empty($removed)) {
            $this->removeMedia($model, $removed);
        }

        if (!empty($files)) {
            $this->storeMedia($model, $files);
        }
    }

    protected function removeMedia($model, array $removed): void
    {
        $medias = Media::find()
            ->where([
                'id' => $removed,
                'file_id' => $model->id,
                'file_type' => $model->tableName(),
            ])
            ->all();

        $foundIds = array_column($medias, 'id');
        $notFoundIds = array_diff($removed, $foundIds);

        if (!empty($notFoundIds)) {
            Yii::$app->media->logError("Media records not found for deletion for IDs: " . implode(', ', $notFoundIds));
        }

        if (empty($medias)) {
            return;
        }

        $filepaths = array_column($medias, 'filepath');
        Yii::$app->media->delete($filepaths);

        try {
            Media::deleteAll([
                'id' => $removed,
                'file_id' => $model->id,
                'file_type' => $model->tableName(),
            ]);
        } catch (\Throwable $e) {
            Yii::$app->media->logError("Failed to delete media records from database: " . $e->getMessage());
        }
    }

    protected function storeMedia($model, array $files): void
    {
        foreach ($files as $file) {
            $folder = $this->folder;
            if (empty($folder)) {
                $classParts = explode('\\', get_class($model));
                $folder = strtolower(end($classParts));
            }

            try {
                $uploaded = Yii::$app->media->upload($file, $folder);

                if (!$uploaded) {
                    $msg = "Failed to upload image '{$file->name}'.";
                    Yii::$app->media->logError($msg);
                    continue;
                }

                $media = new Media();
                $media->file_id = $model->id;
                $media->file_type = $model->tableName();
                $media->collection = $this->collection;
                $media->filepath = $uploaded['url'];

                if (!$media->save()) {
                    $msg = "Failed to save media record for '{$file->name}'. Errors: " . json_encode($media->errors);
                    Yii::$app->media->logError($msg);
                    continue;
                }
            } catch (\Throwable $e) {
                $msg = "Error processing upload for '{$file->name}': " . $e->getMessage();
                Yii::$app->media->logError($msg);
                continue;
            }
        }
    }

    public function deleteMedia()
    {
        $model = $this->owner;

        $medias = Media::find()
            ->where([
                'file_id' => $model->id,
                'file_type' => $model->tableName(),
            ])
            ->all();

        $filepaths = array_column($medias, 'filepath');
        Yii::$app->media->delete($filepaths);

        try {
            Media::deleteAll([
                'file_id' => $model->id,
                'file_type' => $model->tableName(),
            ]);
        } catch (\Throwable $e) {
            Yii::$app->media->logError("Failed to delete media records from database for model {$model->id}: " . $e->getMessage());
        }
    }
}
