<?php

namespace Shikaemon\ImageManager\Exceptions;

use Exception;
use Throwable;

class InvalidRequestException extends BaseException
{
    protected $code = 500;
    protected $message = 'invalid_request';
}
