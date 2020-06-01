<?php

namespace Shikaemon\ImageManager\Commands;

use Exception;
use Shikaemon\ImageManager\Image;
use Shikaemon\ImageManager\Exceptions\InvalidRequestException;
use Shikaemon\ImageManager\Libraries\Repositories\ImageRepository;
use Shikaemon\ImageManager\Libraries\Models\Database\ImageModel;

class DeleteCommand extends AbstractCommand
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
            $record = $image->retrieveById($imageId);
            if (empty($record)) {
                throw new Exception(sprintf('There isn\'t any record of this id %s.', $imageId));
            }
            // DBにmeta情報を削除。
            $record->delete();
            // fileを削除。
            $path = sprintf('%s/%s', $record->posted_on->format('Y/m/d'), $record->image_key);
            $record = $image->retrieveByIdAndUserId($imageId, $userId);
            $isOriginalDelete = config('image_manager.original_file_delete');
            if ($isOriginalDelete) {
                $image->deleteOriginal($path);
            }
        } catch (Exception $e) {
            throw $e;
        }

        $this->setOutput(true);
    }
}