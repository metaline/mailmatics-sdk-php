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

use Mailmatics\Response;
use Mailmatics\Tests\Fixtures\Stringable;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $response = new Response('{"foo":"bar"}', 123, 'Foo');

        $this->assertEquals('{"foo":"bar"}', $response->getBody());
        $this->assertEquals(123, $response->getStatusCode());
        $this->assertEquals('Foo', $response->getReasonPhrase());
    }

    public function testNotHaveToPassStatusCodeAndReasonPhraseToConstructor()
    {
        $response = new Response('{"foo":"bar"}');

        $this->assertEquals('{"foo":"bar"}', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testUnknownStatusCode()
    {
        $response = new Response('{"foo":"bar"}', 123);

        $this->assertEquals('{"foo":"bar"}', $response->getBody());
        $this->assertEquals(123, $response->getStatusCode());
        $this->assertEquals('unknown status', $response->getReasonPhrase());
    }

    /**
     * @dataProvider bodyProvider
     * @param $body
     */
    public function testBodyMustBeAString($body, $realBody)
    {
        $response = new Response($body);
        $this->assertEquals($realBody, $response->getBody());
    }

    public function bodyProvider()
    {
        return [
            [null, ''],
            [true, '1'],
            [false, ''],
            ['', ''],
            ['foo', 'foo'],
            [0, '0'],
            [123, '123'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The body could not be an array.
     */
    public function testBodyCouldNotBeAnArray()
    {
        new Response(['foo' => 'bar']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The body must be a string or an object with "__toString" magic method.
     */
    public function testBodyCouldNotBeAnObject()
    {
        new Response(new \stdClass());
    }

    public function testBodyCanBeAnObjectWith__toString()
    {
        new Response(new Stringable());
    }
}
