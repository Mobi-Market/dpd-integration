<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping\Exceptions;

use Exception;
use Throwable;

class DpdBaseException extends Exception
{
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
