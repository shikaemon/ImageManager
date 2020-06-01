<?php

namespace Shikaemon\ImageManager\Libraries\Repositories\Interfaces;

interface ImageInterface
{
    const RESIZE_TYPE_CONTAIN = 'contain';
    const RESIZE_TYPE_COVER = 'cover';

    public function retrieveById($id);
}
