<?php

namespace app\services;

class UploadService
{

    public function uploadFile($file, $modelName, $folder = 'uploads')
    {
        $path = $this->getFileName($file, $modelName, $folder);
        if (!$path || !$this->saveFile($file, $path)) {
             return false;
        }

        return $path;
    }

    public static function getFileName($file, $modelName, $folder = 'uploads')
    {
        if (!$file) {
            return null;
        }

        $filename = uniqid() . '.' . $file->extension;
        $path  = $folder . '/' . $modelName . '/' . $filename;
        return $path;
    }

    public static function saveFile($file, $path)
    {
        if (!$file || !$path) {
            return false;
        }
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        return $file->saveAs($path);
    }

    public static function deleteFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
