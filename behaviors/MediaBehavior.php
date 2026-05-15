<?php

namespace app\behaviors;

use app\models\Media;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class MediaBehavior extends Behavior
{
    public $attribute = 'image';

    public $collection = 'thumbnail';

    public $removed = 'removed_image';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'uploadMedia',
            ActiveRecord::EVENT_AFTER_UPDATE => 'uploadMedia',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteMedia',
        ];
    }

    public function uploadMedia()
    {
        $model = $this->owner;

        $files = $model->{$this->attribute};
        $removed = $model->{$this->removed};

        if (!$files && empty($removed)) {
            return;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $folder = $model->tableName();

            if (!empty($removed)) {
                $removeMedias = Media::find()
                    ->where(['id' => $removed])
                    ->all();

                if (empty($removeMedias)) {
                    throw new NotFoundHttpException("media not found");
                }

                foreach ($removeMedias as $media) {
                    Yii::$app->media->delete($media->filepath);
                    $media->delete();
                }
            }

            if (!empty($files)) {

                foreach ($files as $file) {

                    $uploaded = Yii::$app->media->upload($file, $folder);

                    if (!$uploaded) {
                        throw new \RuntimeException('Upload fail');
                    }

                    $media = new Media();
                    $media->file_id = $model->id;
                    $media->file_type = $model->tableName();
                    $media->collection = $this->collection;
                    $media->filepath = $uploaded['url'];

                    if (!$media->save()) {
                        throw new \RuntimeException('Save media fail');
                    }
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            throw $e;
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
