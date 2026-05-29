<?php

namespace app\components;

use yii\base\Component;
use yii\web\UploadedFile;

class MediaComponent extends Component
{
    public $basePath = '@webroot/uploads/';
    public $baseUrl = '/uploads';
    public $errors = [];

    public function logError(string $message)
    {
        $this->errors[] = $message;
        \Yii::error($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function upload(UploadedFile $file, string $folder)
    {
        //test upload fall
        if (strpos(strtolower($file->name), 'fail_me') !== false) {
            $this->logError("Simulated upload failure for file '{$file->name}'.");
            return false;
        }
        ///

        $fileName = date('Ymd_His') . '_' . uniqid() . '.' . $file->extension;

        $relativePath = $folder . '/' . $fileName;

        $fullPath = \Yii::getAlias($this->basePath) . $relativePath;

        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!$file->saveAs($fullPath)) {
            $this->logError("Failed to save uploaded file '{$file->name}' to '{$fullPath}'.");
            return false;
        }

        return [
            'url' => $relativePath,
            //'disk' => 'local',
            // 'mime_type' => $file->type,
            // 'size' => $file->size,
        ];
    }

    public function delete($paths): bool
    {
        $paths = (array)$paths;
        foreach ($paths as $path) {
            $fullPath = \Yii::getAlias($this->basePath) . $path;

            if (file_exists($fullPath)) {
                if (!@unlink($fullPath)) {
                    $this->logError("Failed to physically delete file '{$path}' at '{$fullPath}'.");
                }
            } else {
                $this->logError("Physical file not found for deletion: '{$path}'.");
            }
        }

        return true;
    }
}
