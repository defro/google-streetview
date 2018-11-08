<?php

namespace Defro\Google\StreetView;

use Throwable;

class GoogleStreetViewException extends \Exception
{

    public function __construct(string $message, int $code = null, Throwable $previous = null)
    {
        parent::__construct($message, (int) $code, $previous);
    }

}
