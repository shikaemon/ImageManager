<?php

namespace Shikaemon\ImageManager\Libraries\Services;

use Shikaemon\ImageManager\Libraries\Repositories\Interfaces\ImageInterface;
use Shikaemon\ImageManager\Libraries\Models\Database\ImageModel;

class ImageService
{
    private $imageRepository;

    public function __construct(ImageInterface $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function register()
    {
        $image = new ImageModel();
    }
}