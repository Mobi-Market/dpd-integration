<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping\Exceptions;

use Throwable;

class UnexpectedResponse extends DpdBaseException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 1000, $previous);
    }
}
