<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Mailmatics\HttpClientInterface;
use Mailmatics\Response;

/**
 * GuzzleHttpClient, an adapter for the GuzzleHttp\Client class.
 */
class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function request($path, $method = 'GET', array $params = [], array $headers = [])
    {
        $client = $this->getGuzzleClient();
        $path = 'http://localhost:3000/api/' . $path;

        $ua = 'MailmaticsSDK/1.0 (' . $client->getConfig('headers')['User-Agent'] . ')';
        $headers['User-Agent'] = $ua;

        $options = [
            'json'        => $params,
            'headers'     => $headers,
            'debug'       => true,
            'http_errors' => false,
        ];

        $response = $client->request($method, $path, $options);
        $body = $response->getBody()->getContents();

        return new Response($response->getStatusCode(), json_decode($body, true));
    }

    /**
     * @return ClientInterface
     */
    protected function getGuzzleClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }
}
