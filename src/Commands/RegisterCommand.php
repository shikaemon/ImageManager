<?php

namespace Shikaemon\ImageManager\Commands;

use Exception;
use Shikaemon\ImageManager\Image;
use Shikaemon\ImageManager\Libraries\Repositories\ImageRepository;
use Shikaemon\ImageManager\Libraries\Models\Database\ImageModel;

class RegisterCommand extends AbstractCommand
{
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
    }

    /**
     * @param Image $image
     * @throws Exception
     */
    public function execute(Image $image)
    {
        // DBにmeta情報を登録。
        // 画像の名前を作りDBに保存。
        // fileをTempにアップロード。
        try {
            $userId = $this->arguments[1] ?? null;
            $image = new ImageRepository();
            $image->setUploadedFile($this->arguments[0]);
            $image->setUserId($userId);
            $image->generateFileName();
            $image->getFileName();
            $record = new ImageModel();
            $record->user_id = $userId;
            $record->image_key = $image->getImageKey();
            $record->extension = $image->getExtension();
            $record->file_path = $image->getFileName();
            $record->width = $image->getWidth();
            $record->height = $image->getHeight();
            $record->longitude = $image->getLongitude();
            $record->latitude = $image->getLatitude();
            $record->posted_on = today();

            $record->save();
            $image->register();
        } catch (Exception $e) {
            throw $e;
        }

        $this->setOutput($record);
    }
}