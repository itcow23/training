<?php

namespace app\components;

use yii\base\Component;
use yii\web\UploadedFile;

class MediaComponent extends Component
{
    public $basePath = '@webroot/uploads/';
    public $baseUrl = '/uploads';

    public function upload(UploadedFile $file, string $folder)
    {
        $fileName = date('Ymd_His') . '_' . uniqid() . '.' . $file->extension;

        $relativePath = $folder . '/' . $fileName;

        $fullPath = \Yii::getAlias($this->basePath) . $relativePath;

        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!$file->saveAs($fullPath)) {
            return false;
        }

        return [
            'url' => $relativePath,
            //'disk' => 'local',
            // 'mime_type' => $file->type,
            // 'size' => $file->size,
        ];
    }

    public function delete(string $path): bool
    {
        $fullPath = \Yii::getAlias($this->basePath). $path;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return true;
    }
}
