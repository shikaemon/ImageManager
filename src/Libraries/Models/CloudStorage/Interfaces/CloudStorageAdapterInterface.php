<?php

namespace Shikaemon\ImageManager\Libraries\Models\CloudStorage\Interfaces;

use Shikaemon\ImageManager\Exceptions\InvalidMimeTypeException;

interface CloudStorageAdapterInterface
{
    const PUBLISHING_SETTING_PRIVATE = 'private';
    const PUBLISHING_SETTING_PUBLIC = 'public';
    const PUBLISHING_SETTING_TEMP = 'temp';

    const MIME_TYPE_GIF = 'image/gif';
    const MIME_TYPE_JPEG = 'image/jpeg';
    const MIME_TYPE_JPG = 'image/jpg';
    const MIME_TYPE_PNG = 'image/png';
    const MIME_TYPE_HEIC = 'application/octet-stream';

    const EXTENSION_GIF = 'gif';
    const EXTENSION_JPG = 'jpg';
    const EXTENSION_PNG = 'png';
    const EXTENSION_HEIC = 'heic';

    const MIME_TYPES = [
        self::MIME_TYPE_GIF => self::EXTENSION_GIF,
        self::MIME_TYPE_JPEG => self::EXTENSION_JPG,
        self::MIME_TYPE_JPG => self::EXTENSION_JPG,
        self::MIME_TYPE_PNG => self::EXTENSION_PNG,
    ];

    /**
     * @param $mimeType
     * @return mixed
     * @throws InvalidMimeTypeException
     */
    public static function getExtension($mimeType);

    /**
     * @param $fileName
     * @param string $key
     * @return string
     */
    public static function generateImageKey($fileName, $key = null);

    /**
     * @param $filePath
     * @param $contents
     * @param array $options
     * @return bool
     */
    public function put($filePath, $contents, $options = []);

    /**
     * @param $filePath
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($filePath);

    /**
     * @param $filePath
     * @return bool
     */
    public function exists($filePath);

    /**
     * @param $filePath
     * @return bool
     */
    public function delete($filePath);
}
