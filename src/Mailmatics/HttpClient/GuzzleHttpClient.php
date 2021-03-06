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
    public function request($url, $method = 'GET', array $params = [], array $headers = [])
    {
        $client = $this->getGuzzleClient();

        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = 'MailmaticsSDK/1.0 (' . $client->getConfig('headers')['User-Agent'] . ')';

        $options = [
            'json'        => $params,
            'headers'     => $headers,
            'http_errors' => false,
        ];

        $response = $client->request($method, $url, $options);
        $body = $response->getBody()->getContents();

        return new Response($body, $response->getStatusCode(), $response->getReasonPhrase());
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
