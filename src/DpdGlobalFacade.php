<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping;

use Illuminate\Support\Facades\Facade;

/**
 */
class DpdGlobalFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DpdGlobalApi::class;
    }
}
