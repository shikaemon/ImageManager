<?php

namespace Shikaemon\ImageManager\Libraries\Models\CloudStorage\S3;

class OriginalAdaptor extends AbstractS3Adaptor
{
    public $fileSystem;

    public function __construct()
    {
        $this->fileSystem = config('image_manager.filesystems.s3.original');
        parent::__construct();
    }
}