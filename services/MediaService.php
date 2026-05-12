<?php

namespace app\services;

use app\models\Media;
use Yii;

class MediaService
{
    public function saveFile( array $data): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $media = new Media();
            $media->file_id = $data['file_id'];
            $media->file_type = $data['file_type'];
            $media->filepath = $data['filepath'];
            $media->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function updateFile(Media $media, array $data): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $media->file_id = $data['file_id'];
            $media->file_type = $data['file_type'];
            $media->filepath = $data['filepath'];
            $media->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function deleteFile(Media $media): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $media->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }
}
