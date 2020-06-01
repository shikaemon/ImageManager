<?php

namespace Shikaemon\ImageManager\Exceptions;

use Exception;
use Throwable;

class ImageNotFoundException extends BaseException
{
    protected $code = 500;
    protected $message = 'image_not_found';
}
