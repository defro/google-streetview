<?php

namespace Defro\Google\StreetView\Exception;

use GuzzleHttp\Exception\GuzzleException;

class RequestException extends \RuntimeException
{
    /**
     * RequestException constructor.
     *
     * @param string                          $message
     * @param \Throwable|GuzzleException|null $previous
     */
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
