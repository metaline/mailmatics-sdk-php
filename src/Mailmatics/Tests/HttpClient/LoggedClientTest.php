<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Tests\HttpClient;

use Mailmatics\HttpClient\LoggedClient;
use Mailmatics\Tests\ApiTestCase;

class LoggedClientTest extends ApiTestCase
{
    public function testDecorator()
    {
        $originalResponse = $this->getResponseMock('{"success":true}', 200, 'OK');

        $httpClient = $this->getMock('Mailmatics\\HttpClientInterface', ['request']);
        $httpClient
            ->method('request')
            ->with('foo/bar', 'POST', ['foo' => 'baz'], ['Accept' => 'text/html'])
            ->willReturn($originalResponse);

        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $logger
            ->method('info')
            ->with(
                'Mailmatics request: POST foo/bar',
                [
                    'request'  => [
                        'method'  => 'POST',
                        'url'     => 'foo/bar',
                        'headers' => ['Accept' => 'text/html'],
                        'body'    => ['foo' => 'baz'],
                    ],
                    'response' => [
                        'status' => 200,
                        'reason' => 'OK',
                        'body'   => '{"success":true}',
                    ],
                ]
            );

        /**
         * @var \Mailmatics\HttpClientInterface $httpClient
         * @var \Psr\Log\LoggerInterface        $logger
         */

        $client = new LoggedClient($httpClient, $logger);

        $response = $client->request('foo/bar', 'POST', ['foo' => 'baz'], ['Accept' => 'text/html']);

        $this->assertSame($originalResponse, $response);
    }
}
