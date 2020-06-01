<?php

namespace Shikaemon\ImageManager\Exceptions;

use Exception;
use Throwable;

class InvalidMimeTypeException extends BaseException
{
    protected $code = 500;
    protected $message = 'system_error';
}
