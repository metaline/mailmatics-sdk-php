<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Exception;

/**
 * ErrorException
 */
class ErrorException extends \RuntimeException implements ExceptionInterface
{
    protected $errorMessage;
    protected $data;

    /**
     * ErrorException constructor.
     *
     * @param string $errorMessage
     * @param mixed  $data
     */
    public function __construct($errorMessage, $data = null)
    {
        $message = 'An error occurred: ' . $errorMessage;

        if ($data !== null) {
            $data = json_encode($data, JSON_PRETTY_PRINT);

            $message .= '<br>' . $data;
        }

        parent::__construct($message);
    }
}
