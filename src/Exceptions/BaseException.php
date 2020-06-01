<?php

namespace Shikaemon\ImageManager\Exceptions;

use Exception;
use Throwable;

class BaseException extends Exception
{
    protected $code = 500;
    protected $message = 'system_error';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = __($this->message);
        }
        if (empty($code)) {
            $code = $this->code;
        }
        parent::__construct($message, $code, $previous);
    }
}
