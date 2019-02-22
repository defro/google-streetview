<?php

namespace Defro\Google\StreetView\Exception;

use Throwable;

class UnexpectedStatusException extends \RuntimeException
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
