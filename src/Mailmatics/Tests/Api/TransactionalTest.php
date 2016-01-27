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

    public function testPreview()
    {
        $preview = '<html><body>Transaction email body</body></html>';

        $client = $this->getClientMockForRequest('transactional/1/preview', 'GET', [], true, $preview);

        $transactional = new Transactional($client);

        $this->assertEquals($preview, $transactional->preview(1));
    }
}
