<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping\Traits;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use MobiMarket\DpdShipping\Entities\ApiAuth;
use MobiMarket\DpdShipping\Exceptions\RequestFailed;
use MobiMarket\DpdShipping\Exceptions\UnexpectedResponse;
use Psr\Http\Message\ResponseInterface as HttpResponse;
use stdClass;

trait RestApiClient
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var ApiAuth
     */
    protected $auth;

    /**
     * @var int|null
     */
    protected $cache_ttl = null;

    /**
     * Sets up require parameters for the api.
     */
    public function buildClient(
        string $base_uri,
        float $timeout,
        bool $should_log,
        ?int $cache_ttl,
        ApiAuth $auth
    ): void {
        $stack = HandlerStack::create();

        if (true === $should_log) {
            $stack->push(
                Middleware::log(
                    Log::getMonolog(),
                    new MessageFormatter('{req_body} - {res_body}')
                )
            );

            $stack->push(
                Middleware::log(
                    Log::getMonolog(),
                    new MessageFormatter('{uri} - {method} - {code}')
                )
            );
        }

        $this->client = new HttpClient([
            // Base URI is used with relative requests
            'base_uri'    => $base_uri,
            // You can set any number of default request options.
            'timeout'     => $timeout,
            // Handler stack for logging purposes.
            'handler'     => $stack,
            // Disable internal errors to let us catch all status codes.
            'http_errors' => false,
        ]);

        $this->auth      = $auth;
        $this->cache_ttl = $cache_ttl;

        // If caching is enabled, attempt to load it.
        if ($cache_ttl && $token = Cache::get('dpd-integration-token')) {
            $this->auth->token = $token;
        }
    }

    /**
     * Send the request to the API.
     */
    protected function sendAPIRequest(
        string $method,
        string $endpoint,
        ?array $data = null,
        ?array $headers = null,
        ?array $query = null,
        bool $dont_refresh = false
    ) {
        // First request which isn't to get a token should get a
        // token first.
        if (false === $dont_refresh && null === $this->auth->token) {
            $this->getFreshToken();
        }

        $body           = null;
        $contentHeaders = [
            'Accept'     => 'application/json',
            'GeoClient'  => "account/{$this->auth->account_id}",
            'GeoSession' => $this->auth->token,
        ];

        if ($data) {
            $body                           = json_encode($data);
            $contentHeaders['Content-Type'] = 'application/json';
        }

        if ($query) {
            $query = array_dot($query);
        }

        $headers = $headers ?? [];

        /**
         * @var HttpResponse
         */
        $response = $this->client->{$method}($endpoint, [
            'body'      => $body,
            'query'     => $query,
            'headers'   => $headers + $contentHeaders,
        ]);

        $code = $response->getStatusCode();

        // Codes from 400 to 5XX are errors
        if ($code >= 400 && $code <= 599) {
            // Refresh + Unauthed
            if (false === $dont_refresh && 401 === $code) {
                $this->getFreshToken();

                // We should have a fresh token, give it another shot.
                // (however we tell it not to continue trying if it fails)
                return $this->sendAPIRequest(
                    $method,
                    $endpoint,
                    $data,
                    $headers,
                    $query,
                    $dont_refresh = true
                );
            }

            throw new RequestFailed($response);
        }

        $body = (string) $response->getBody();

        return json_decode($body) ?? $body;
    }

    protected function sendAPIRequestNotEmpty(
        string $method,
        string $endpoint,
        ?array $data = null,
        ?array $headers = null,
        ?array $query = null,
        bool $dont_refresh = false
    ): stdClass {
        if ($response = $this->sendAPIRequest($method, $endpoint, $data, $headers, $query, $dont_refresh)) {
        } else {
            throw new UnexpectedResponse('Response is empty');
        }

        return $response;
    }

    /**
     * Send a request to /user/?action=login to refresh the token for other methods.
     * POST /user/?action=login.
     */
    protected function getFreshToken(): void
    {
        $login_auth = base64_encode("{$this->auth->username}:{$this->auth->password}");

        $headers = [
            'Authorization' => "Basic {$login_auth}",
        ];

        $query = [
            'action' => 'login',
        ];

        // Do not refresh, since this IS the refresh request.
        $response = $this->sendAPIRequestNotEmpty('post', 'user', null, $headers, $query, true);

        // Update token then cache.
        $this->auth->token = $response->data->geoSession;

        if ($this->cache_ttl) {
            Cache::put('dpd-integration-token', $this->auth->token, $this->cache_ttl);
        }
    }
}
