<?php

namespace Yocto\Exception;

use Throwable;

class ForbiddenException extends \Exception
{

    const CODE = 403;

    public function __construct($message = "", $code = self::CODE, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}