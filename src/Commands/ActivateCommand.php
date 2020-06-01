<?php

namespace Shikaemon\ImageManager\Commands;

use Exception;
use Shikaemon\ImageManager\Image;
use Shikaemon\ImageManager\Exceptions\InvalidRequestException;
use Shikaemon\ImageManager\Libraries\Repositories\ImageRepository;
use Shikaemon\ImageManager\Libraries\Models\Database\ImageModel;

class ActivateCommand extends AbstractCommand
{
    /**
     * @param Image $image
     * @throws Exception
     */
    public function execute(Image $image)
    {
        try{
            $imageId = $this->argument(0);
            $userId = $this->argument(1);
            if (!isset($imageId)) {
                throw new InvalidRequestException('Invalid parameter.');
            }
            $image = new ImageRepository();
            $record = $image->retrieveByIdAndUserId($imageId, $userId);
            if (empty($record)) {
                throw new Exception(sprintf('There isn\t any record of this id %s.', $imageId));
            }
            // DBにmeta情報を登録。
            $record->status = ImageModel::STATUS_ACTIVATED;
            $record->save();
            // tempにファイルを取りに行く。
            $image->loadFromTemp($record->file_path);
            // fileをPublicにアップロード。
            $image->activate($record->file_path);
        } catch (Exception $e) {
            throw $e;
        }

        $this->setOutput($record);
    }
}