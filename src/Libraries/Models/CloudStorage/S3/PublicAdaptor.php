<?php

namespace Shikaemon\ImageManager\Libraries\Models\CloudStorage\S3;

class PublicAdaptor extends AbstractS3Adaptor
{
    protected $fileSystem;

    public function __construct()
    {
        $this->fileSystem = config('image_manager.filesystems.s3.public');
        parent::__construct();
    }
}