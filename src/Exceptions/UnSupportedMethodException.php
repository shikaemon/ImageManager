<?php

namespace Shikaemon\ImageManager\Exceptions;

use Exception;
use Throwable;

class UnSupportedMethodException extends BaseException
{
    protected $code = 500;
    protected $message = 'un_supported_method';
}
