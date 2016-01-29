<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\HttpClient;

use Mailmatics\Exception\ErrorException;
use Mailmatics\HttpClientInterface;
use Mailmatics\Response;

/**
 * StreamClient
 */
class StreamClient implements HttpClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function request($url, $method = 'GET', array $params = [], array $headers = [])
    {
        $headers['Content-type'] = 'application/json';

        $context = stream_context_create([
            'http' => [
                'method'        => $method,
                'header'        => $this->buildRequestHeaders($headers),
                'content'       => json_encode($params),
                'ignore_errors' => true,
                'user_agent'    => 'MailmaticsSDK/1.0 (Stream PHP/' . PHP_VERSION . ')',
            ]
        ]);

        $fp = fopen($url, 'r', false, $context);

        $meta = stream_get_meta_data($fp);
        $body = stream_get_contents($fp);

        $hdrs = $meta['wrapper_data'];

        // HTTP/1.1 200 OK
        $statusLine = array_shift($hdrs);
        preg_match('#^HTTP\/([0-9\.]+) ([0-9]{3}) (.*?)$#', $statusLine, $matches);

        if (!isset($matches[3])) {
            throw new ErrorException('The status line "%s" is not valid', $statusLine);
        }

        $statusCode = $matches[2];
        $reasonPhrase = $matches[3];

        return new Response($body, $statusCode, $reasonPhrase);
    }

    /**
     * @param array $headers
     * @return string
     */
    private function buildRequestHeaders(array $headers)
    {
        $result = [];
        foreach ($headers as $key => $value) {
            $result[] = $key . ': ' . $value;
        }

        return rtrim(implode("\r\n", $result));
    }
}
