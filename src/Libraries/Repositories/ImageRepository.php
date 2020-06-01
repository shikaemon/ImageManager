<?php

namespace Shikaemon\ImageManager\Libraries\Repositories;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as Binary;
use Shikaemon\ImageManager\Exceptions\InvalidMimeTypeException;
use Shikaemon\ImageManager\Libraries\Models\CloudStorage\S3\OriginalAdaptor;
use Shikaemon\ImageManager\Libraries\Models\CloudStorage\S3\PublicAdaptor;
use Shikaemon\ImageManager\Libraries\Models\CloudStorage\S3\TempAdaptor;
use Shikaemon\ImageManager\Libraries\Models\Enum\Geo;
use Shikaemon\ImageManager\Libraries\Repositories\Interfaces\ImageInterface;
use Shikaemon\ImageManager\Libraries\Storages\Databases\ImageStorage;

/**
 * Class ImageRepository
 * @package Shikaemon\ImageManager\Libraries\Repositories
 * @property $binary
 * @property $extension
 * @property $fileName
 * @property Geo $geo
 * @property int $imageId
 * @property Binery interventionImage
 * @property string $imageKey
 * @property TempAdaptor $tmpStorage
 * @property PublicAdaptor $publicStorage
 * @property OriginalAdaptor $originalStorage
 * @property UploadedFile $originalFile
 * @property int $userId
 */
class ImageRepository extends ImageStorage implements ImageInterface
{
    const ORIENTATION_REGULAR = 1;  // 補正しない
    const ORIENTATION_UP_DOWN = 2;  // 上下反転
    const ORIENTATION_ROTATE_180 = 3;   // 180度回転
    const ORIENTATION_RIGHT_LEFT = 4;   // 左右反転
    const ORIENTATION_UP_DOWN_AND_RIGHT_270 = 5;    // 上下反転+時計周りに270度回転
    const ORIENTAION_ROTATE_90 = 6; // 時計周りに90度回転
    const ORIENTATION_UP_DOWN_AND_ROTATE_90 = 7;    // 上下反転+時計周りに90度回転
    const ORIENTATION_ROTATE_270 = 8; // 時計周りに270度回転

    const CENTER = 'center';

    private $binary;
    private $extension;
    private $fileName;
    private $geo;
    private $imageId;
    private $imageKey;
    private $interventionImage;
    private $originalFile;
    private $publicStorage;
    private $tmpStorage;
    private $originalStorage;
    private $userId;

    /**
     * ImageRepository constructor.
     * @param null $originalFile
     * @param TempAdaptor|null $tempAdaptor
     * @param PublicAdaptor|null $publicAdaptor
     * @param OriginalAdaptor|null $originalAdaptor
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct($originalFile = null, TempAdaptor $tempAdaptor = null, PublicAdaptor $publicAdaptor = null, OriginalAdaptor $originalAdaptor = null)
    {
        $this->tmpStorage = isset($tempAdaptor) ? $tempAdaptor : new TempAdaptor();
        $this->publicStorage = isset($publicAdaptor) ? $publicAdaptor : new PublicAdaptor();
        $this->originalStorage = isset($originalAdaptor) ? $originalAdaptor : new OriginalAdaptor();
        if ($originalFile instanceof UploadedFile) {
            $this->setUploadedFile($originalFile);
        }
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function activate($filePath)
    {
        return $this->originalStorage->put($filePath, $this->binary);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function deleteOriginal($filePath)
    {
        return $this->originalStorage->deleteDirectory($filePath);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function deletePublic($filePath)
    {
        return $this->publicStorage->deleteDirectory($filePath);
    }

    /**
     * @param $filePath
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadFromOriginal($filePath)
    {
        return $this->binary = $this->originalStorage->get($filePath);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function loadFromPublic($filePath)
    {
        try {
            $this->binary = $this->publicStorage->get($filePath);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $filePath
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadFromTemp($filePath)
    {
        return $this->binary = $this->tmpStorage->get($filePath);
    }

    /**
     * @param float $width
     * @param float $height
     * @throws Exception
     */
    public function cover($width, $height)
    {
        if (!isset($this->binary)) {
            throw new Exception();
        }
        $image = Image::make($this->binary);
        $this->binary = $image->cover($width, $height, null, self::CENTER)->encode();
    }

    /**
     * @param $width
     * @param $height
     * @return Binary
     * @throws Exception
     */
    public function fit($width, $height)
    {
        if (!isset($this->binary)) {
            throw new Exception();
        }
        $this->setInterventionImage(Image::make($this->binary));
        return $this->binary = $this->interventionImage->fit($width, $height, null, self::CENTER)->encode();
    }

    public function publish($filePath)
    {
        return $this->publicStorage->put($filePath, $this->binary, TempAdaptor::PUBLISHING_SETTING_PUBLIC);
    }

    /**
     * @return string
     */
    public function generateFileName()
    {
        $this->generateImageKey($this->originalFile->getFileName());
        return $this->fileName = sprintf('%s/%s/img.%s', now()->format(('Y/m/d')), $this->imageKey, $this->extension);
    }

    /**
     * ファイルの名を取得する。
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function getImageKey()
    {
        return $this->imageKey;
    }

    /**
     * @return InterventionImage
     */
    public function getInterventionImage()
    {
        return $this->interventionImage;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->binary->getWidth();
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->binary->getHeight();
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->geo->getLongitude();
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->geo->getLatitude();
    }

    /**
     * @param Carbon $time
     * @param $mstImageId
     * @param $filePath
     * @return string
     */
    public function generateStoragePath(Carbon $time, $mstImageId, $filePath)
    {
        return sprintf('/%s/%s/%s/%s/%s', $time->year, $time->month, $time->day, $mstImageId, $filePath);
    }

    /**
     * @param $extension
     * @return bool
     */
    public function checkMimeType($extension)
    {
        try {
            $this->tmpStorage->getExtension($extension);
        } catch (InvalidMimeTypeException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param Binary $binary
     */
    public function setInterventionImage(Binary $binary)
    {
        $this->interventionImage = $binary;
    }

    /**
     * @param UploadedFile $originalFile
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws Exception
     */
    public function setUploadedFile(UploadedFile $originalFile)
    {
        try {
            $this->binary = Image::make($originalFile->getPathname());
            $this->rotateOriginImage();
            $this->originalFile = $originalFile;
            $this->setExtension($this->tmpStorage->getExtension($originalFile->getMimeType()));
        } catch (Exception $e) {
            throw $e;
        }
        // 位置情報を取得してセット
        $this->setGeoCode();
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function register()
    {
        if (empty($this->originalFile)) {
            return false;
        }

        try {
            $this->tmpStorage->put($this->fileName, $this->originalFile->get(), TempAdaptor::PUBLIC);
        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * 画像が横向きになるのを防止
     */
    private function rotateOriginImage()
    {
        if (!empty($this->binary->exif()['Orientation'])) {
            switch ($this->binary->exif()['Orientation']) {
                case self::ORIENTATION_REGULAR:
                    // 何もしない
                    break;
                case self::ORIENTATION_UP_DOWN:
                    // 上下反転
                    $this->binary->flip();
                    break;
                case self::ORIENTATION_ROTATE_180:
                    // 180度回転
                    $this->binary->rotate(180);
                    break;
                case self::ORIENTATION_RIGHT_LEFT:
                    // 180度回転して上下反転
                    $this->binary->rotate(180)->flip();
                    break;
                case self::ORIENTATION_UP_DOWN_AND_RIGHT_270:
                    // 90度回転
                    $this->binary->rotate(270)->flip();
                    break;
                case self::ORIENTAION_ROTATE_90:
                    // 270度回転
                    $this->binary->rotate(270);
                    break;
                case self::ORIENTATION_UP_DOWN_AND_ROTATE_90:
                    // 90度回転
                    $this->binary->rotate(90)->flip();
                    break;
                case self::ORIENTATION_ROTATE_270:
                    // 90度回転
                    $this->binary->rotate(90);
                    break;
                default:
                    // 何もしない
                    break;
            }
        }
    }

    /**
     * @param $seed
     * @param $extension
     * @return string
     */
    private function generateImageKey($seed)
    {
        return $this->imageKey = sha1(sprintf('%s_%s_%08d_%s', $this->userId, microtime(), rand(0, 99999999), $seed));
    }

    private function setGeoCode()
    {
        if (!empty($this->binary) && !empty($this->binary->exif())) {
            $this->geo = self::makeGeoByExifArray($this->binary->exif());
        } else {
            $this->geo = self::makeGeoByExifArray();
        }
    }

    /**
     * @param $extension
     */
    private function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @param array|null $exifArray
     * @return Geo
     */
    private static function makeGeoByExifArray(array $exifArray = null)
    {
        return new Geo($exifArray);
    }
}