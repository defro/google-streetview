<?php

namespace Defro\Google\StreetView\Exception;

use http\Exception\RuntimeException;
use Throwable;

class BadStatusCodeException extends RuntimeException
{
    public function __construct(
        string $message, int $statusCode, Throwable $previous = null
    )
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
