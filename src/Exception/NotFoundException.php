<?php

namespace Yocto\Exception;

use Throwable;

class NotFoundException extends \Exception
{

    const CODE = 404;

    public function __construct($message = "", $code = self::CODE, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}