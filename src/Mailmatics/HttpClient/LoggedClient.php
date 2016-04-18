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

use Mailmatics\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * LoggedClient.
 *
 * This is a decorator for add log to a HttpClientInterface implementation.
 */
class LoggedClient implements HttpClientInterface
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface     $logger
     */
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function request($url, $method = 'GET', array $params = [], array $headers = [])
    {
        $request = [
            'method'  => $method,
            'url'     => $url,
            'headers' => $headers,
            'body'    => $params,
        ];

        try {
            $response = $this->httpClient->request($url, $method, $params, $headers);

            $this->logger->info(
                sprintf('Mailmatics request: %s %s', $method, $url),
                [
                    'request'  => $request,
                    'response' => [
                        'status' => $response->getStatusCode(),
                        'reason' => $response->getReasonPhrase(),
                        'body'   => $response->getBody(),
                    ],
                ]
            );

            return $response;
        } catch (\Exception $e) {
            $this->logger->info(
                sprintf('Mailmatics request: %s %s', $method, $url),
                [
                    'request'   => $request,
                    'exception' => $e,
                ]
            );

            throw $e;
        }
    }

    /**
     * @return HttpClientInterface
     */
    public function getOriginalHttpClient()
    {
        return $this->httpClient;
    }
}
