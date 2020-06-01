<?php

namespace Shikaemon\ImageManager\Libraries\Models\CloudStorage\S3;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Shikaemon\ImageManager\Exceptions\InvalidMimeTypeException;
use Shikaemon\ImageManager\Libraries\Models\CloudStorage\Interfaces\CloudStorageAdapterInterface;

/**
 * Class S3Adaptor
 * @package Shikaemon\ImageManager\Libraries\Models\CloudStorage
 * @property string $fileSystem
 * @property Filesystem $storage
 */
class AbstractS3Adaptor implements CloudStorageAdapterInterface
{
    const PUBLIC = 'public';

    protected $fileSystem;
    private $storage;

    /**
     * BaseImageAdapterModel constructor.
     */
    public function __construct()
    {
        $this->storage = Storage::disk($this->fileSystem);
    }

    /**
     * @param $mimeType
     * @return mixed
     * @throws InvalidMimeTypeException
     */
    public static function getExtension($mimeType)
    {
        if (!isset(self::MIME_TYPES[$mimeType])) {
            throw new InvalidMimeTypeException();
        }

        return self::MIME_TYPES[$mimeType];
    }

    /**
     * @param $fileName
     * @param string $key
     * @return string
     */
    public static function generateImageKey($fileName, $key = null)
    {
        if (empty($key)) {
            $key = config('app.key');
        }
        return hash_hmac('sha256', sprintf('%s-%s-%s-%s', rand(0, 9999999), $fileName, microtime(), config('app.key')), $key, false);
    }

    /**
     * @param $filePath
     * @param $contents
     * @param array $options
     * @return bool
     */
    public function put($filePath, $contents, $options = [])
    {
        return $this->storage->put($filePath, $contents, $options);
    }

    /**
     * @param $filePath
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($filePath)
    {
        return $this->storage->get($filePath);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function exists($filePath)
    {
        return $this->storage->exists($filePath);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function delete($filePath)
    {
        return $this->storage->delete($filePath);
    }

    /**
     * @param $dirPath
     * @return bool
     */
    public function deleteDirectory($dirPath)
    {
        return $this->storage->deleteDirectory($dirPath);
    }
}