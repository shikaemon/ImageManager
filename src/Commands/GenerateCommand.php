<?php

namespace Shikaemon\ImageManager\Commands;

use Exception;
use Shikaemon\ImageManager\Exceptions\InvalidRequestException;
use Shikaemon\ImageManager\Image;
use Shikaemon\ImageManager\Libraries\Repositories\ImageRepository;

class GenerateCommand extends AbstractCommand
{
    /**
     * @param Image $image
     * @throws Exception
     */
    public function execute(Image $image)
    {
        try {
            $imageId = $this->argument(0);
            $imageType = $this->argument(1);
            if (!isset($imageId)) {
                throw new InvalidRequestException('Invalid parameter.');
            }
            $image = new ImageRepository();
            $record = $image->retrieveById($imageId);
            if (empty($record)) {
                throw new Exception(sprintf('There isn\'t any record of this id %s.', $imageId));
            }

            $imageTypeDetail = config(sprintf('image_manager.image_types.%s', $imageType));
            if (empty($imageTypeDetail) || empty($imageTypeDetail['resize_type']) || empty($imageTypeDetail['width']) || empty($imageTypeDetail['height'])) {
                throw new Exception(sprintf('This image type is invalid or isn\'t supported %s.', $imageType));
            }
            $resizeType = $imageTypeDetail['resize_type'];
            $record = $image->retrieveById($imageId);
            $image->loadFromOriginal($record->file_path);
            $binary = null;
            switch ($resizeType) {
                case 'cover':
                    $image->fit($imageTypeDetail['width'], $imageTypeDetail['height']);
                    break;
                case 'fit':
                    $image->fit($imageTypeDetail['width'], $imageTypeDetail['height']);
                    break;
                default:
                    break;
            }
            $binary = $image->getInterventionImage();
            $publishPath = sprintf('%s/%s/%s.%s', $record->posted_on->format('Y/m/d'), $record->id, $imageType, $record->extension);
            // original画像を取得。
            if (!$image->loadFromPublic($publishPath)) {
                $image->publish($publishPath);
            }
        } catch (Exception $e) {
            throw $e;
        }

        $this->setOutput($binary);
    }
}