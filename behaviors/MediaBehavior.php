<?php

namespace app\behaviors;

use app\models\Media;
use Exception;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use RuntimeException;

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

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!empty($removed)) {
                $this->removeMedia($model, $removed);
            }

            if (!empty($files)) {
                $this->storeMedia($model, $files);
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            throw $e;
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

        foreach ($medias as $media) {
            $path = $media->filepath;

            if (!$media->delete()) {
                throw new Exception('Failed to delete media record.');
            }

            Yii::$app->media->delete($path);
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

            $uploaded = Yii::$app->media->upload($file, $folder);

            if (!$uploaded) {
                throw new RuntimeException('Failed to upload image.');
            }

            $media = new Media();
            $media->file_id = $model->id;
            $media->file_type = $model->tableName();
            $media->collection = $this->collection;
            $media->filepath = $uploaded['url'];

            if (!$media->save()) {
                throw new RuntimeException('Failed to save media information.');
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

        foreach ($medias as $media) {

            Yii::$app->media->delete($media->filepath);

            $media->delete();
        }
    }
}
