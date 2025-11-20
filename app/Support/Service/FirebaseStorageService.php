<?php

namespace App\Support\Service;

use Kreait\Firebase\Factory;
use DateTimeImmutable;
use Exception;

class FirebaseStorageService
{
    protected $storage;
    protected $bucket;
    protected $bucketName;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));

        $this->storage = $factory->createStorage();
        $this->bucket = $this->storage->getBucket(config('firebase.storage_bucket'));
        $this->bucketName = config('firebase.storage_bucket');
    }

    public function uploadFile($file, string $path): array
    {
        if (is_string($file)) {
            $filePath = $file;
        } else {
            $filePath = $file->getRealPath();
        }

        $imageSize = getimagesize($filePath);
        $width = $imageSize[0] ?? null;
        $height = $imageSize[1] ?? null;

        $fileContents = file_get_contents($filePath);

        $filename = uniqid() . '_' . ($file->getClientOriginalName() ?? basename($filePath));

        $firebasePath = rtrim($path, '/') . '/' . $filename;

        $this->bucket->upload($fileContents, [
            'name' => $firebasePath,
            'predefinedAcl' => 'publicRead',
        ]);

        $publicUrl = "https://storage.googleapis.com/{$this->bucketName}/{$firebasePath}";

        return [
            'path' => $publicUrl,
            'width' => $width,
            'height' => $height,
        ];
    }

    public function getSignedUrl(string $filePath, int $expiresInMinutes = 2900): string
    {
        $object = $this->bucket->object($filePath);

        if (!$object->exists()) {
            throw new Exception("File does not exist: $filePath");
        }

        $expiresAt = new DateTimeImmutable("+{$expiresInMinutes} minutes");

        return $object->signedUrl($expiresAt, [
            'version' => 'v4',
        ]);
    }


    public function deleteFile(string $publicUrl)
    {
        $bucketUrl = "https://storage.googleapis.com/{$this->bucketName}/";

        if (strpos($publicUrl, $bucketUrl) !== 0) {
            throw new Exception("Invalid file URL for deletion");
        }

        $filePath = substr($publicUrl, strlen($bucketUrl));

        $object = $this->bucket->object($filePath);

        if (!$object->exists()) {
            throw new Exception("File does not exist: $filePath");
        }
        $object->delete();
    }

}
