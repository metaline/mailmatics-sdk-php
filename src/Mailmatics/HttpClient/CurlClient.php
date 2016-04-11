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
 * CurlClient
 */
class CurlClient implements HttpClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function request($url, $method = 'GET', array $params = [], array $headers = [])
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $headers['Accept'] = 'application/json';
        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = 'MailmaticsSDK/1.0 (Curl/' . curl_version()['version'] . ' PHP/' . PHP_VERSION . ')';

        $headers = $this->buildHeaders($headers);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

        if ($method !== 'GET') {
            if ($method === 'POST') {
                curl_setopt($handle, CURLOPT_POST, true);
            } else {
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
            }

            if (!empty($params)) {
                curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($params));
            }
        }

        $curlResponse = curl_exec($handle);

        $headerSize = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        $errNo = curl_errno($handle);
        $errDesc = curl_error($handle);

        curl_close($handle);

        if ($errNo) {
            throw new ErrorException($errDesc);
        }

        $header = substr($curlResponse, 0, $headerSize);
        $body = substr($curlResponse, $headerSize);

        $headers = [];
        $reasonPhrase = '';
        foreach (explode("\r\n", $header) as $line) {
            if ($line === '') {
                continue;
            }

            if (substr($line, 0, 5) === 'HTTP/') {
                if (preg_match('#^HTTP\/([0-9\.]+) ' . $statusCode . ' (.*?)$#', $line, $matches)) {
                    $reasonPhrase = $matches[2];
                }

                continue;
            }

            list($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }

        return new Response($body, $statusCode, $reasonPhrase);
    }

    /**
     * @param array $headers
     * @return array
     */
    private function buildHeaders($headers)
    {
        $hdrs = [];

        foreach ($headers as $key => $value) {
            $hdrs[] = $key . ': ' . $value;
        }

        return $hdrs;
    }
}
