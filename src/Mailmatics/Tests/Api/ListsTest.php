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

use Mailmatics\Api\Lists;
use Mailmatics\Tests\ApiTestCase;

/**
 * ListsTest
 */
class ListsTest extends ApiTestCase
{
    public function testAll()
    {
        $client = $this->getClientMockForRequest('lists', 'GET', [], false, [['id' => 1], ['id' => 2]]);

        $lists = new Lists($client);
        $resources = $lists->all();

        $this->assertTrue(is_array($resources));
        $this->assertEquals(['id' => 1], $resources[0]);
        $this->assertEquals(['id' => 2], $resources[1]);
    }

    public function testGet()
    {
        $client = $this->getClientMockForRequest('lists/1', 'GET', [], false, ['id' => 1]);

        $lists = new Lists($client);
        $resource = $lists->get(1);

        $this->assertEquals(['id' => 1], $resource);
    }

    public function testAddSubscriber()
    {
        $client = $this->getClientMockForRequest(
            'lists/abc123uuid456xyz/subscribe',
            'POST',
            [
                'email' => 'mail@example.com',
                'name' => 'mail@example.com',
            ],
            false,
            ['id' => 123]
        );

        $lists = new Lists($client);
        $response = $lists->addSubscriber('abc123uuid456xyz', 'mail@example.com');

        $this->assertEquals(['id' => 123], $response);
    }

    public function testAddSubscriberWithName()
    {
        $client = $this->getClientMockForRequest(
            'lists/abc123uuid456xyz/subscribe',
            'POST',
            [
                'email' => 'mail@example.com',
                'name' => 'John Doe',
            ],
            false,
            ['id' => 456]
        );

        $lists = new Lists($client);
        $response = $lists->addSubscriber('abc123uuid456xyz', 'mail@example.com', ['name' => 'John Doe']);

        $this->assertEquals(['id' => 456], $response);
    }
}
