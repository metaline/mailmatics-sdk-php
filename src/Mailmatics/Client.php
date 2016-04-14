<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics;

use Mailmatics\HttpClient\StreamClient;

/**
 * Client
 */
class Client
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    private $token;

    /**
     * @var Api\Lists
     */
    private $lists;

    /**
     * @var Api\Campaigns
     */
    private $campaigns;

    /**
     * @var Api\Transactional
     */
    private $transactional;

    /**
     * Client constructor.
     *
     * @param array               $credentials
     * @param array               $options
     * @param HttpClientInterface $httpClient
     */
    public function __construct(array $credentials, array $options = [], HttpClientInterface $httpClient = null)
    {
        $this->credentials = $credentials;
        $this->httpClient = $httpClient;

        $defaultOptions = ['base_url' => 'http://api.mailmatics.com/'];
        $this->options = array_merge($defaultOptions, $options);
    }

    /**
     * @return Api\Lists
     */
    public function getLists()
    {
        if ($this->lists === null) {
            $this->lists = new Api\Lists($this);
        }

        return $this->lists;
    }

    /**
     * @return Api\Lists
     */
    public function getCampaigns()
    {
        if ($this->campaigns === null) {
            $this->campaigns = new Api\Campaigns($this);
        }

        return $this->campaigns;
    }

    /**
     * @return Api\Transactional
     */
    public function getTransactional()
    {
        if ($this->transactional === null) {
            $this->transactional = new Api\Transactional($this);
        }

        return $this->transactional;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = new StreamClient();
        }

        return $this->httpClient;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array  $params
     * @param bool   $asText
     * @return array|string
     * @throws Exception\BadResponseException
     */
    public function request($path, $method = 'GET', array $params = [], $asText = false)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ];

        $url = $this->options['base_url'] . $path;
        $response = $this->getHttpClient()->request($url, $method, $params, $headers);

        $this->validateResponseStatusCode($response);

        if ($asText) {
            return $response->getBody();
        }

        $body = $this->validateBodyResponse($response->getBody());

        return $body['data'];
    }

    /**
     * @return string
     * @throws Exception\ExceptionInterface
     */
    public function getToken()
    {
        if ($this->token === null) {
            if (isset($this->credentials['apiKey'])) {
                $this->token = $this->credentials['apiKey'];
            } else {
                $response = $this->getHttpClient()->request($this->options['base_url'] . 'auth/login', 'POST', [
                    'username' => isset($this->credentials['username']) ? $this->credentials['username'] : '',
                    'password' => isset($this->credentials['password']) ? $this->credentials['password'] : '',
                ]);

                $this->validateResponseStatusCode($response);
                $body = $this->validateBodyResponse($response->getBody());

                if (!isset($body['token'])) {
                    throw new Exception\BadResponseException('The parameter "token" is missing');
                }

                $this->token = $body['token'];
            }
        }

        return $this->token;
    }

    /**
     * @param Response $response
     * @throws Exception\ExceptionInterface
     */
    private function validateResponseStatusCode(Response $response)
    {
        $body = json_decode($response->getBody(), true);
        $statusCode = $response->getStatusCode();

        switch ($statusCode) {
            case 404:
                $message = isset($body['error']) ? $body['error'] : 'Resource does not exist';

                throw new Exception\ResourceNotFoundException($message);

            default:
                if ($statusCode < 200 || $statusCode >= 300) {
                    throw new Exception\ErrorException(
                        isset($body['error']) ? $body['error'] : $response->getReasonPhrase()
                    );
                }
        }
    }

    /**
     * @param string $body
     * @return array
     * @throws Exception\ExceptionInterface
     */
    private function validateBodyResponse($body)
    {
        $body = json_decode($body, true);

        if (!is_array($body)) {
            throw new Exception\BadResponseException('The response body is not valid');
        }

        if (!isset($body['success'])) {
            throw new Exception\BadResponseException('The parameter "success" is missing');
        }

        if (!isset($body['data'])) {
            $body['data'] = null;
        }

        if ($body['success'] === false) {
            if (!isset($body['error'])) {
                throw new Exception\BadResponseException('An error occurred, and the parameter "error" is missing');
            }

            throw new Exception\ErrorException($body['error'], $body['data']);
        }

        return $body;
    }
}
