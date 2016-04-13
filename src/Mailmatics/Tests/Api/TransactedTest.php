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

use Mailmatics\Api\Transacted;
use Mailmatics\Tests\ApiTestCase;

/**
 * TransactedTest
 */
class TransactedTest extends ApiTestCase
{
    /**
     * @dataProvider badTransactionalIdProvider
     *
     * @param mixed  $id
     * @param string $message
     */
    public function testTransactionalIdMustBeAnInteger($id, $message)
    {
        /** @var \Mailmatics\Client $client */
        $client = $this->getMockBuilder('Mailmatics\\Client')->disableOriginalConstructor()->getMock();

        $this->setExpectedException(
            \InvalidArgumentException::CLASS,
            $message
        );

        new Transacted($client, $id);
    }

    public function badTransactionalIdProvider()
    {
        return [
            [0, 'Expected an integer greater than zero.'],
            [-1, 'Expected an integer greater than zero.'],
            [null, 'Expected an integer greater than zero. "NULL" given instead.'],
            [true, 'Expected an integer greater than zero. "boolean" given instead.'],
            [false, 'Expected an integer greater than zero. "boolean" given instead.'],
            ['abc', 'Expected an integer greater than zero. "string" given instead.'],
            ['123abc', 'Expected an integer greater than zero. "string" given instead.'],
        ];
    }

    public function testAll()
    {
        $client = $this->getClientMockForRequest(
            'transactional/123/transacted',
            'GET',
            [],
            false,
            [['id' => 456], ['id' => 789]]
        );

        $transacted = new Transacted($client, 123);
        $resources = $transacted->all();

        $this->assertTrue(is_array($resources));
        $this->assertEquals(['id' => 456], $resources[0]);
        $this->assertEquals(['id' => 789], $resources[1]);
    }

    public function testGet()
    {
        $client = $this->getClientMockForRequest('transactional/123/transacted/456', 'GET', [], false, ['id' => 456]);

        $transacted = new Transacted($client, 123);
        $resource = $transacted->get(456);

        $this->assertEquals(['id' => 456], $resource);
    }
}
