<?php

namespace Defro\Google\StreetView\Exception;

use Throwable;

class UnexpectedValueException extends \UnexpectedValueException
{
    public function __construct(string $message, int $code = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
