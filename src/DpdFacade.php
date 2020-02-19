<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping;

use Illuminate\Support\Facades\Facade;

/**
 */
class DpdFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DpdRestApi::class;
    }
}
