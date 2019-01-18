<?php

namespace Defro\Google\StreetView\Exception;

class RequestException extends \RuntimeException
{
    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
