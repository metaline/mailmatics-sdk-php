<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Tests\Exception;

use Mailmatics\Exception\ErrorException;

class ErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageComposition()
    {
        $e = new ErrorException('Lorem ipsum dolor sit amet');
        $this->assertEquals('An error occurred: Lorem ipsum dolor sit amet', $e->getMessage());

        $e = new ErrorException('Lorem ipsum dolor sit amet', ['foo' => 'bar']);
        $this->assertEquals("An error occurred: Lorem ipsum dolor sit amet<br>{\n    \"foo\": \"bar\"\n}", $e->getMessage());
    }
}
