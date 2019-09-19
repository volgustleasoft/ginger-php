<?php

namespace Ginger;

use Ginger\HttpClient\CurlHttpClient;

final class Ginger
{
    /**
     * The library version.
     */
    const CLIENT_VERSION = '2.0.0';

    /**
     * The API version.
     */
    const API_VERSION = 'v1';

    /**
     * Create a new API client.
     *
     * @param string $endpoint
     * @param string $apiKey
     * @return ApiClient
     */
    public static function createClient($endpoint, $apiKey)
    {
        return new ApiClient(
            new CurlHttpClient(
                $endpoint . '/' . self::API_VERSION,
                $apiKey,
                [
                    'User-Agent' => sprintf(
                        'Ginger-PHP/%s (%s; PHP %s)',
                        self::CLIENT_VERSION,
                        PHP_OS,
                        phpversion()
                    )
                ]
            )
        );
    }
}
