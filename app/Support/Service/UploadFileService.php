<?php

namespace App\Support\Service;

use App\Support\Config\Config;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    static function uploadFile($file, $root): string
    {
        $path = Storage::putFile($root, $file);
        return Config::STORAGE_PATH . $path;
    }

    static function uploadImage($file, $root): array
    {
        $path = Storage::putFile($root, $file);
        $info = UploadFileService::fileInfo($file);

        return [
            "path" => Config::STORAGE_PATH . $path,
            "width" => (double)$info["width"],
            "height" => (double)$info["height"],
        ];
    }

    private static function fileInfo($file): array
    {
        $info = getimagesize($file);
        return [
            "width" => (double)$info[0],
            "height" => (double)$info[1],
        ];
    }

    static function fileInfoFormat($file): array
    {
        $file['width'] = (double)$file['width'];
        $file['height'] = (double)$file['height'];

        return $file;
    }
}
