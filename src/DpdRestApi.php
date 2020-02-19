<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping;

use MobiMarket\DpdShipping\Entities\ApiAuth;
use MobiMarket\DpdShipping\Traits\RestApiClient;

class DpdRestApi
{
    use RestApiClient;

    public function __construct(
        string $base_uri,
        float $timeout,
        bool $should_log,
        ?int $cache_ttl,
        ApiAuth $auth
    ) {
        $this->buildClient(
            $base_uri,
            $timeout,
            $should_log,
            $cache_ttl,
            $auth
        );
    }

    /*
     * GET /shipping/network/{code}
     */
    public function getService(string $code): object
    {
        return $this->sendAPIRequestNotEmpty('get', "shipping/network/{$code}");
    }

    /*
     * GET /shipping/network
     */
    public function listServices(array $body): object
    {
        return $this->sendAPIRequestNotEmpty('get', 'shipping/network', $body);
    }

    /*
     * GET /shipping/country/{code}
     */
    public function getCountry(string $code): object
    {
        return $this->sendAPIRequestNotEmpty('get', "shipping/country/{$code}");
    }

    /*
     * GET /shipping/country
     */
    public function listCountries(): object
    {
        return $this->sendAPIRequestNotEmpty('get', 'shipping/country');
    }

    /*
     * POST /shipping/shipment
     */
    public function shipment(array $body): object
    {
        return $this->sendAPIRequestNotEmpty('post', 'shipping/shipment', $body);
    }

    /*
     * GET /shipping/shipment/{shipmentId}/label
     */
    public function label(string $shipmentId, string $format): object
    {
        return $this->sendAPIRequestNotEmpty('get', "shipping/shipment/{$shipmentId}/label", null, ['Content-Type' => $format]);
    }
}
