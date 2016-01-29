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

use Mailmatics\Exception\ExceptionInterface;

/**
 * HandlerInterface
 */
interface HttpClientInterface
{
    /**
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param array  $headers
     * @return Response
     * @throws ExceptionInterface
     */
    public function request($url, $method = 'GET', array $params = [], array $headers = []);
}
