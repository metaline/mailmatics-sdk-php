<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Tests;

use Mailmatics\HttpClientInterface;
use Mailmatics\Response;

/**
 * ApiTestCase
 */
class ApiTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param HttpClientInterface $httpClient
     * @return \Mailmatics\Client
     */
    protected function getClientMock(HttpClientInterface $httpClient)
    {
        $client = $this->getMockBuilder('Mailmatics\\Client')
            ->setConstructorArgs([['username' => 'foo', 'password' => '123'], [], $httpClient])
            ->setMethods(['getToken'])
            ->getMock();

        $client->method('getToken')->willReturn('abc');

        return $client;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array  $params
     * @param bool   $asText
     * @param mixed  $response
     * @return \Mailmatics\Client
     */
    protected function getClientMockForRequest($path, $method, array $params, $asText, $response)
    {
        $client = $this->getMockBuilder('Mailmatics\\Client')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->method('request')
            ->with($path, $method, $params, $asText)
            ->willReturn($response);

        return $client;
    }

    /**
     * @param string   $path
     * @param string   $method
     * @param array    $params
     * @param array    $headers
     * @param Response $response
     * @return HttpClientInterface
     */
    protected function getHttpClientMock($path, $method, array $params, array $headers, Response $response)
    {
        $httpClient = $this->getMock('Mailmatics\\HttpClientInterface', ['request']);

        if ($path !== 'auth/login') {
            $headers['Authorization'] = 'Bearer abc';
        }

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with('http://www.mailmatics.com/api/' . $path, $method, $params, $headers)
            ->willReturn($response);

        return $httpClient;
    }

    /**
     * @param string $data
     * @param int    $statusCode
     * @param string $reasonPhrase
     * @return Response
     */
    protected function getResponseMock($data, $statusCode = 200, $reasonPhrase = '')
    {
        $response = $this->getMockBuilder('Mailmatics\\Response')
            ->setMethods(['getBody', 'getStatusCode', 'getReasonPhrase'])
            ->disableOriginalConstructor()
            ->getMock();

        $response->method('getBody')->willReturn($data);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getReasonPhrase')->willReturn($reasonPhrase);

        return $response;
    }
}
