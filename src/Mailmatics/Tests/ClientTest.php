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

use Mailmatics\Client;
use Mailmatics\Exception\BadResponseException;
use Mailmatics\Exception\ErrorException;

/**
 * ClientTest
 */
class ClientTest extends ApiTestCase
{
    public function testNotHaveToPassHttpClientToConstructor()
    {
        $client = new Client(['api' => 'abc']);

        $this->assertInstanceOf('Mailmatics\\HttpClient\\StreamClient', $client->getHttpClient());
    }

    public function testPassHttpClientInterfaceToConstructor()
    {
        $httpClient = $this->getMock('Mailmatics\\HttpClientInterface');
        $client = new Client(['api' => 'abc'], [], $httpClient);

        $this->assertSame($httpClient, $client->getHttpClient());
    }

    /**
     * @expectedException \Mailmatics\Exception\BadResponseException
     * @expectedExceptionMessage The parameter "token" is missing
     */
    public function testBadLoginResponse()
    {
        $credentials = ['username' => 'foo', 'password' => '123'];

        $response = $this->getResponseMock(json_encode(['success' => true]));
        $httpClient = $this->getHttpClientMock('auth/login', 'POST', $credentials, [], $response);

        $client = new Client($credentials, [], $httpClient);

        $client->getToken();
    }

    public function testLoginOnlyOnce()
    {
        $credentials = ['username' => 'foo', 'password' => '123'];

        $response = $this->getResponseMock(json_encode(['success' => true, 'token' => 'foo-321-baz']));
        $httpClient = $this->getHttpClientMock('auth/login', 'POST', $credentials, [], $response);

        $client = new Client($credentials, [], $httpClient);

        $client->getToken();
        $client->getToken();
    }

    public function testJsonResponse()
    {
        $data = ['foo' => 'bar'];

        $response = $this->getResponseMock(json_encode(['success' => true, 'data' => $data]));
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        $this->assertEquals($data, $client->request('foo/bar', 'GET', [], false));
    }

    public function testTextResponse()
    {
        $data = ['foo' => 'bar'];

        $response = $this->getResponseMock(json_encode(['success' => true, 'data' => $data]));
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        $this->assertEquals('{"success":true,"data":{"foo":"bar"}}', $client->request('foo/bar', 'GET', [], true));
    }

    /**
     * @dataProvider             badResponseBodyProvider
     *
     * @param mixed  $body
     * @param string $message
     */
    public function testBadResponse($body, $message)
    {
        $response = $this->getResponseMock(json_encode($body));
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        try {
            $client->request('foo/bar');
        } catch (BadResponseException $e) {
            $this->assertEquals($message, $e->getMessage());

            return;
        }

        // You shall not pass!
        $this->assertFalse(true);
    }

    public function badResponseBodyProvider()
    {
        return [
            'not-array'           => [
                'body'    => 'foo',
                'message' => 'The response body is not valid'
            ],
            'void'                => [
                'body'    => [],
                'message' => 'The parameter "success" is missing'
            ],
            'noSuccessParam'      => [
                'body'    => ['data' => []],
                'message' => 'The parameter "success" is missing'
            ],
            'errorWithoutMessage' => [
                'body'    => ['success' => false],
                'message' => 'An error occurred, and the parameter "error" is missing'
            ],
        ];
    }

    /**
     * @expectedException \Mailmatics\Exception\ErrorException
     * @expectedExceptionMessage The error message
     */
    public function testErrorResponse()
    {
        $body = ['success' => false, 'error' => 'The error message'];

        $response = $this->getResponseMock(json_encode($body));
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        $client->request('foo/bar');
    }

    public function testErrorAndDataResponse()
    {
        $body = [
            'success' => false,
            'error' => 'The error message',
            'data' => [
                'foo' => 'bar'
            ]
        ];

        $response = $this->getResponseMock(json_encode($body));
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        try {
            $client->request('foo/bar');
        } catch (ErrorException $e) {
            $this->assertEquals(
                "An error occurred: The error message<br>{\n    \"foo\": \"bar\"\n}",
                $e->getMessage()
            );

            return;
        }

        // You shall not pass!
        $this->assertFalse(true);
    }

    /**
     * @expectedException \Mailmatics\Exception\ResourceNotFoundException
     * @expectedExceptionMessage Resource does not exist
     */
    public function testResourceNotFoundException()
    {
        $response = $this->getResponseMock(json_encode(['success' => false, 'error' => 'Resource does not exist']), 404);
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        $client->request('foo/bar');
    }

    /**
     * @expectedException \Mailmatics\Exception\ErrorException
     * @dataProvider errorCodeProvider
     *
     * @param int $errorCode
     */
    public function testResourceErrorException($errorCode)
    {
        $response = $this->getResponseMock(json_encode(['success' => false, 'error' => 'Whooops!']), $errorCode);
        $httpClient = $this->getHttpClientMock('foo/bar', 'GET', [], [], $response);
        $client = $this->getClientMock($httpClient);

        $client->request('foo/bar');
    }

    public function errorCodeProvider()
    {
        return [
            [400],
            [401],
            [402],
            [403],
            [405],
            [406],
            [407],
            [408],
            [409],
            [410],
            [411],
            [412],
            [413],
            [414],
            [415],
            [416],
            [417],
            [500],
            [501],
            [502],
            [503],
            [504],
            [505],
            [999], // does not exist!
        ];
    }

    /**
     * @dataProvider apiProvider
     * @param $method
     * @param $class
     */
    public function testApi($method, $class)
    {
        $client = new Client(['api' => 'abc']);

        $this->assertInstanceOf($class, $client->$method());
    }

    public function apiProvider()
    {
        return [
            ['getLists', 'Mailmatics\\Api\\Lists'],
            ['getCampaigns', 'Mailmatics\\Api\\Campaigns'],
            ['getTransactional', 'Mailmatics\\Api\\Transactional'],
        ];
    }

    public function testToken()
    {
        $client = new Client(['api' => 'foo-123-abc']);

        $this->assertEquals('foo-123-abc', $client->getToken());
    }

    public function testLogin()
    {
        $credentials = ['username' => 'foo', 'password' => '123'];

        $response = $this->getResponseMock(json_encode(['success' => true, 'token' => 'cba-123-foo']));
        $httpClient = $this->getHttpClientMock('auth/login', 'POST', $credentials, [], $response);

        $client = new Client($credentials, [], $httpClient);

        $this->assertEquals('cba-123-foo', $client->getToken());
    }

    /**
     * @expectedException \Mailmatics\Exception\ErrorException
     * @expectedExceptionMessageRegExp #Wrong username or password#
     */
    public function testFailedLogin()
    {
        $credentials = ['username' => 'foo', 'password' => '123'];

        $response = $this->getResponseMock(json_encode(['success' => false, 'error' => 'Wrong username or password']));
        $httpClient = $this->getHttpClientMock('auth/login', 'POST', $credentials, [], $response);

        $client = new Client($credentials, [], $httpClient);

        $client->getToken();
    }
}
