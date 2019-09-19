<?php

namespace Ginger\HttpClient {
    function curl_init() {
        return new \stdClass;
    }

    function curl_setopt_array($curl, array $options) {
        $curl->options = $options;
    }

    function curl_exec($curl) {
        return json_encode($curl->options);
    }

    function curl_errno($curl) {
        if ($curl->options[CURLOPT_URL] == 'https://www.example.com/error') {
            return CURLE_OUT_OF_MEMORY;
        }

        return CURLE_OK;
    }

    function curl_error($curl) {
        return 'A memory allocation request failed.';
    }

    function curl_close($curl) {
    }
}

namespace Ginger\Tests\HttpClient {

    use Ginger\HttpClient\CurlHttpClient;
    use Ginger\HttpClient\HttpException;

    final class CurlHttpClientTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var CurlHttpClient
         */
        private $client;

        public function setUp()
        {
            $this->client = new CurlHttpClient(
                'https://www.example.com',
                '1a1b2e63c55e'
            );
        }

        public function test_it_sends_a_request()
        {
            $response = $this->client->request(
                'POST',
                '/foo/bar',
                ['Content-Type' => 'text/plain'],
                'request data'
            );

            $this->assertEquals(
                [
                    CURLOPT_URL => 'https://www.example.com/foo/bar',
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => 'request data',
                    CURLOPT_HTTPHEADER => [
                        'Content-Length: 12',
                        'Content-Type: text/plain'
                    ],
                    CURLOPT_USERPWD => '1a1b2e63c55e:'
                ],
                json_decode($response, true)
            );
        }

        public function test_it_omits_the_content_length_header_when_no_request_data()
        {
            $response = $this->client->request(
                'GET',
                '/foo/bar'
            );

            $this->assertEquals(
                [
                    CURLOPT_URL => 'https://www.example.com/foo/bar',
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => '1a1b2e63c55e:'
                ],
                json_decode($response, true)
            );
        }

        public function test_it_throws_an_exception_on_curl_error()
        {
            $this->setExpectedException(
                HttpException::class,
                'cURL error: 27: A memory allocation request failed. (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for /error'
            );

            $this->client->request('GET', '/error');
        }
    }
}