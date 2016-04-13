<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Tests\Api;

use Mailmatics\Api\Transactional;
use Mailmatics\Tests\ApiTestCase;

/**
 * TransactionalTest
 */
class TransactionalTest extends ApiTestCase
{
    public function testAll()
    {
        $client = $this->getClientMockForRequest('transactional', 'GET', [], false, [['id' => 1], ['id' => 2]]);

        $transactional = new Transactional($client);
        $resources = $transactional->all();

        $this->assertTrue(is_array($resources));
        $this->assertEquals(['id' => 1], $resources[0]);
        $this->assertEquals(['id' => 2], $resources[1]);
    }

    public function testGet()
    {
        $client = $this->getClientMockForRequest('transactional/1', 'GET', [], false, ['id' => 1]);

        $transactional = new Transactional($client);
        $resource = $transactional->get(1);

        $this->assertEquals(['id' => 1], $resource);
    }

    public function testHtmlPreview()
    {
        $preview = '<html><body>Transaction email body</body></html>';

        $client = $this->getClientMockForRequest('transactional/1/preview', 'GET', [], true, $preview);

        $transactional = new Transactional($client);

        $this->assertEquals($preview, $transactional->htmlPreview(1));
    }

    public function testTextPreview()
    {
        $text = <<<TXT
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
TXT;

        $client = $this->getClientMockForRequest('transactional/1/text', 'GET', [], false, $text);

        $transactional = new Transactional($client);

        $this->assertEquals($text, $transactional->textPreview(1));
    }

    public function testSend()
    {
        $email = 'foo@example.com';
        $data = [];

        $params = [
            'recipient' => $email,
            'data'      => $data,
        ];

        $response = [
            'id' => 123
        ];

        $client = $this->getClientMockForRequest('transactional/123abc/send', 'POST', $params, false, $response);

        $transactional = new Transactional($client);
        $this->assertEquals($response, $transactional->send('123abc', $email, $data));
    }

    public function testSendWithSchedule()
    {
        $email = 'foo@example.com';
        $data = [];
        $schedule = new \DateTime('2016-01-15 12:30:00', new \DateTimeZone('+05:00'));

        $params = [
            'recipient' => $email,
            'data'      => $data,
            'schedule'  => 1452843000, // timestamp: Fri, 15 Jan 2016 07:30:00 GMT
        ];

        $response = [
            'id' => 123
        ];

        $client = $this->getClientMockForRequest('transactional/123abc/send', 'POST', $params, false, $response);

        $transactional = new Transactional($client);
        $this->assertEquals($response, $transactional->send('123abc', $email, $data, $schedule));
    }

    public function testTransacted()
    {
        /** @var \Mailmatics\Client $client */
        $client = $this->getMockBuilder('Mailmatics\\Client')->disableOriginalConstructor()->getMock();

        $transactional = new Transactional($client);
        $transacted = $transactional->getTransacted(123);

        $this->assertInstanceOf('Mailmatics\\Api\\Transacted', $transacted);
    }

    public function testReports()
    {
        $response = [
            [
                ['v' => '2015-12-30T17:34:28.120Z'],
                ['v' => 0],
                ['v' => 2],
                ['v' => 3],
                ['v' => 0],
            ],
            [
                ['v' => '2015-12-31T17:34:28.120Z'],
                ['v' => 1],
                ['v' => 0],
                ['v' => 0],
                ['v' => 2],
            ],
            [
                ['v' => '2016-01-01T17:34:28.120Z'],
                ['v' => 1],
                ['v' => 2],
                ['v' => 3],
                ['v' => 4],
            ],
        ];

        $client = $this->getClientMockForRequest('transactional/1/reports', 'GET', [], false, $response);

        $transactional = new Transactional($client);
        $reports = $transactional->reports(1);

        $this->assertEquals($response, $reports);
    }
}
